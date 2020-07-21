<?php

namespace App\Models\Classifieds;

use App\Models\Messenger\Thread;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClassifiedsMessenger
 * @package App\Models\Classifieds
 */
class ClassifiedsMessenger extends Model
{
    public $timestamps = false;

    public $table = 'classifieds_messenger';

    public $fillable = [
        'classified_id',
        'thread_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'classified_id' => 'integer',
        'thread_id' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classified()
    {
        return $this->belongsTo(Classifieds::class, 'classified_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }
}
