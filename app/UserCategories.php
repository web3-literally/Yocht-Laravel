<?php

namespace App;

use App\Models\Services\ServiceCategory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserServices
 * @package App\Models\Classifieds
 *
 * @property File file
 */
class UserCategories extends Model
{
    public $timestamps = false;

    public $table = 'user_categories';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'category_id' => 'int'
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
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
