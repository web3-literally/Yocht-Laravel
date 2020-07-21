<?php

namespace App\Models\Jobs;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Sentinel;

/**
 * Class JobCharges
 * @package App\Models\Jobs
 */
class JobCharges extends Model
{
    /**
     * @var string
     */
    public $table = 'job_charges';

    /**
     * @var array
     */
    public $fillable = [
        'job_id',
        'user_id',
        'transaction_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be casted to date type.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}