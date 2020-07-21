<?php

namespace App\Models\Events;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Sentinel;

/**
 * Class FavoriteEvent
 * @package App\Models\Events
 */
class FavoriteEvent extends Model
{
    /**
     * @var string
     */
    public $table = 'events_favorites';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
        'event_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Job::class, 'event_id');
    }

    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('id', 'desc')->where('user_id', $user->getUserId());
    }
}