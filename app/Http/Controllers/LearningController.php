<?php

namespace App\Http\Controllers;

use App\Mail\CourseCompletedMail;
use App\Models\LessonProgress;
use App\Models\Product;
use App\Models\ProductLesson;
use App\Models\UserProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LearningController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $userId = auth()->id();
        $product->load(['sections.lessons']);

        $allLessonIds = $this->lessonIds($product);
        $completedIds = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $allLessonIds)
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->all();

        $progress = $this->progressFromCounts(count($allLessonIds), count($completedIds));
        $isCompleted = count($allLessonIds) > 0 && count($completedIds) === count($allLessonIds);

        return view('learning.show', [
            'product' => $product,
            'completedIds' => $completedIds,
            'progress' => $progress,
            'isCompleted' => $isCompleted,
            'currentLesson' => null,
        ]);
    }

    public function lesson(string $slug, int $lessonId): View
    {
        $product = Product::query()->where('slug', $slug)->firstOrFail();
        $product->load(['sections.lessons']);

        $lesson = ProductLesson::query()
            ->whereIn('section_id', $product->sections->pluck('id'))
            ->where('id', $lessonId)
            ->firstOrFail();

        $userId = auth()->id();
        $allLessonIds = $this->lessonIds($product);
        $completedIds = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $allLessonIds)
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->all();

        $progress = $this->progressFromCounts(count($allLessonIds), count($completedIds));
        $isCompleted = count($allLessonIds) > 0 && count($completedIds) === count($allLessonIds);

        $isLessonComplete = in_array($lesson->id, $completedIds, true);

        $orderedIds = $allLessonIds;
        $idx = array_search($lesson->id, $orderedIds, true);
        $prevId = $idx > 0 ? $orderedIds[$idx - 1] : null;
        $nextId = ($idx !== false && $idx < count($orderedIds) - 1) ? $orderedIds[$idx + 1] : null;

        return view('learning.show', [
            'product' => $product,
            'completedIds' => $completedIds,
            'progress' => $progress,
            'isCompleted' => $isCompleted,
            'currentLesson' => $lesson,
            'isLessonComplete' => $isLessonComplete,
            'prevLessonId' => $prevId,
            'nextLessonId' => $nextId,
        ]);
    }

    public function markComplete(int $lessonId): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $lesson = ProductLesson::with('section.product')->findOrFail($lessonId);
        $product = $lesson->section->product;

        $owns = UserProduct::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
        abort_unless($owns, 403);

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()],
        );

        $allLessonIds = $this->lessonIds($product->loadMissing('sections.lessons'));
        $completedCount = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $allLessonIds)
            ->whereNotNull('completed_at')
            ->count();

        if (count($allLessonIds) > 0 && $completedCount === count($allLessonIds)) {
            Log::info('Course completed', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product' => $product->title,
            ]);

            $alreadyNotified = LessonProgress::where('user_id', $user->id)
                ->whereIn('lesson_id', $allLessonIds)
                ->whereNotNull('completed_at')
                ->where('completed_at', '<', now()->subSeconds(2))
                ->count();

            if ($alreadyNotified < count($allLessonIds) && $user->email) {
                Mail::to($user->email)->queue(new CourseCompletedMail($user, $product));
            }
        }

        return redirect()
            ->route('learning.lesson', ['slug' => $product->slug, 'lesson_id' => $lesson->id])
            ->with('status', 'Lesson ditandai selesai!');
    }

    /**
     * @param  Product  $product  loaded with sections.lessons
     * @return array<int, int>
     */
    private function lessonIds(Product $product): array
    {
        return $product->sections
            ->flatMap(fn ($s) => $s->lessons->pluck('id'))
            ->all();
    }

    /**
     * @return array{total:int,completed:int,percent:int}
     */
    private function progressFromCounts(int $total, int $completed): array
    {
        if ($total === 0) {
            return ['total' => 0, 'completed' => 0, 'percent' => 0];
        }

        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => (int) floor(($completed / $total) * 100),
        ];
    }
}
