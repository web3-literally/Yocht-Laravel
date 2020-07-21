<?php

namespace App\Models\Classifieds;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Sentinel;

/**
 * Class FavoriteClassified
 * @package App\Models\Classifieds
 */
class FavoriteClassified extends Model
{
    /**
     * @var string
     */
    public $table = 'classifieds_favorites';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
        'classified_id'
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
    public function classified()
    {
        return $this->belongsTo(Classifieds::class, 'classified_id');
    }

    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('id', 'desc')->where('user_id', $user->getUserId());
    }
}