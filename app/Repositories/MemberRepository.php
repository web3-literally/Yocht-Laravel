<?php

namespace App\Repositories;

use App\Models\Reviews\Review;
use App\User;
use InfyOm\Generator\Common\BaseRepository;

class MemberRepository extends BaseRepository
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
        return User::class;
    }

    /**
     * @param int $for
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getReviews(int $for)
    {
        $rows = Review::leftJoin('reviews_for', 'reviews.id', '=', 'reviews_for.review_id')
            ->where('reviews.status', Review::STATUS_APPROVED)
            ->where('reviews_for.for', 'member')
            ->where('reviews_for.instance_id', $for)
            ->groupBy('reviews_for.id')
            ->select('reviews.*')
            ->paginate(10);

        return $rows;
    }
}
