<?php

namespace App\Models\Reviews;

use App\Models\Jobs\Job;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReviewFor
 * @package App\Models\Reviews
 */
class ReviewFor extends Model
{
    public $timestamps = false;

    public $table = 'reviews_for';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'review_id' => 'integer',
        'instance_id' => 'integer',
        'for' => 'string'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @throws \Exception
     */
    public function instance()
    {
        if ($this->for == 'member') {
            return $this->hasOne(User::class, 'id', 'instance_id')->withTrashed();
        }

        if ($this->for == 'job') {
            return $this->hasOne(Job::class, 'id', 'instance_id')->withTrashed();
        }

        throw new \Exception('Broken review instance');
    }
}
