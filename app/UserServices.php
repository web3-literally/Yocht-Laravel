<?php

namespace App;

use App\Models\Services\Service;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserServices
 * @package App\Models\Classifieds
 *
 * @property File file
 */
class UserServices extends Model
{
    public $timestamps = false;

    public $table = 'user_services';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'service_id' => 'int'
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
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
