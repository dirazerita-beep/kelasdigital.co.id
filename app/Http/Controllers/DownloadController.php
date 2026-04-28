<?php

namespace App\Http\Controllers;

use App\Models\ProductLesson;
use App\Models\UserProduct;
use Illuminate\Http\RedirectResponse;

class DownloadController extends Controller
{
    public function download(int $lessonId): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $lesson = ProductLesson::with('section.product')->findOrFail($lessonId);
        $product = $lesson->section->product;

        $owns = UserProduct::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
        abort_unless($owns, 403);

        abort_unless((bool) $lesson->gdrive_file_id, 404);

        return redirect()->away(
            'https://drive.google.com/uc?export=download&id='.$lesson->gdrive_file_id
        );
    }
}
