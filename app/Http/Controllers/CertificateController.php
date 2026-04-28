<?php

namespace App\Http\Controllers;

use App\Models\LessonProgress;
use App\Models\Product;
use App\Models\UserProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CertificateController extends Controller
{
    public function generate(int $product_id): BinaryFileResponse|RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $product = Product::findOrFail($product_id);

        $owns = UserProduct::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
        abort_unless($owns, 403);

        $lessonIds = $product->sections()->with('lessons:id,section_id')->get()
            ->flatMap(fn ($s) => $s->lessons->pluck('id'))
            ->all();

        $totalLessons = count($lessonIds);
        $completedLessons = $totalLessons === 0
            ? 0
            : LessonProgress::where('user_id', $user->id)
                ->whereIn('lesson_id', $lessonIds)
                ->whereNotNull('completed_at')
                ->count();

        if ($totalLessons === 0 || $completedLessons < $totalLessons) {
            return redirect()
                ->route('learning.show', $product->slug)
                ->with('error', 'Selesaikan semua materi dulu untuk mengunduh sertifikat.');
        }

        $relativePath = 'certificates/'.$user->id.'_'.$product->id.'.pdf';
        $absolutePath = Storage::path($relativePath);
        $downloadName = 'Sertifikat-'.$product->slug.'.pdf';

        if (! Storage::exists($relativePath)) {
            $latestCompletion = LessonProgress::where('user_id', $user->id)
                ->whereIn('lesson_id', $lessonIds)
                ->whereNotNull('completed_at')
                ->max('completed_at');

            Carbon::setLocale('id');
            $completedAt = $latestCompletion ? Carbon::parse($latestCompletion) : now();
            $completedDate = $completedAt->translatedFormat('d F Y');

            $pdf = Pdf::loadView('certificates.template', [
                'user' => $user,
                'product' => $product,
                'completedDate' => $completedDate,
            ])->setPaper('a4', 'landscape');

            Storage::put($relativePath, $pdf->output());
        }

        return response()->download($absolutePath, $downloadName);
    }
}
