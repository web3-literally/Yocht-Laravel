<?php

namespace App\Models\Classifieds;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ClassifiedsCategoriesManufacturers
 * @package App\Models\Classifieds
 */
class ClassifiedsCategoriesManufacturers extends Model
{
    public $table = 'classifieds_categories_manufacturers';

    public $timestamps = false;

    public $fillable = [
        'category_id',
        'manufacturer_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'category_id' => 'integer',
        'manufacturer_id' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ClassifiedsCategory::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo(ClassifiedsCategory::class, 'manufacturer_id');
    }
}
