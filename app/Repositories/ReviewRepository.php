<?php

namespace App\Repositories;

use App\Models\Reviews\Review;
use InfyOm\Generator\Common\BaseRepository;
use App\Notifications\JobReviewed;
use App\Notifications\MemberReviewed;
use App\User;
use Cache;

class ReviewRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Review::class;
    }

    public function setStatus(Review $review, $status)
    {
        $oldStatus = $review->status;

        $review->status = $status;
        $review->saveOrFail();

        if ($oldStatus != Review::STATUS_APPROVED && $review->status == Review::STATUS_APPROVED) {
            if ($review->for->for == 'member') {
                $member = $review->for->instance;
                $member->notify(new MemberReviewed($review, $member, $review->by));
            } elseif ($review->for->for == 'job') {
                $member = $review->for->instance->user;
                $member->notify(new JobReviewed($review, $review->for->instance, $review->by));
            }

            Cache::forget('MemberAVGRating' . $member->id);
        }
    }
}
