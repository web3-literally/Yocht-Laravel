<?php

namespace App\Http\Controllers;

use App\Jobs\Index\ReviewsUpdate;
use App\Models\Reviews\Review;
use App\Repositories\ReviewRepository;
use Illuminate\Notifications\DatabaseNotification;
use Sentinel;

/**
 * Class ReviewsController
 * @package App\Http\Controllers
 */
class ReviewsController extends Controller
{
    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $review = Review::approved()->find($id);
        if (!$review) {
            return abort(404);
        }

        resolve('seotools')->setTitle($review->title . config('seotools.meta.defaults.separator') . trans('reviews.reviews'));

        $notification = DatabaseNotification::whereNull('read_at')->where('type', 'like', '%Reviewed')->where('notifiable_id', $review->member->id)->where('instance_id', $id)->get()->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return view('reviews.show', compact('review'));
    }

    /**
     * @param int $id
     * @param string $status
     * @param string $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setStatus($id, $status, $key)
    {
        if ($key === sha1(implode('-', [config('reviews.secret'), $id, $status])) && in_array($status, [Review::STATUS_APPROVED, Review::STATUS_DECLINED])) {
            try {
                $review = Review::findOrFail($id);

                if ($review->status == Review::STATUS_PENDING) {
                    $repository = resolve(ReviewRepository::class);
                    $repository->setStatus($review, $status);

                    if ($status == Review::STATUS_APPROVED) {
                        $review->loadMissing('for');
                        // Exception('Broken review instance') occurred if job executed under the queue
                        ReviewsUpdate::dispatchNow($review);
                    }

                    return view('reviews.status-changed')->with('review', $review);
                }
            } catch (\Throwable $e) {
                report($e);

                return abort(500);
            }

            return abort(404);
        }

        return abort(404);
    }
}