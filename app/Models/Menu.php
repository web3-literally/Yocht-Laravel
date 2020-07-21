<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
 *
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property string $html_class
 * @property string $type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Menu extends Model
{
    const MENU_TYPE_HORIZONTAL = 'horizontal';
    const MENU_TYPE_VERTICAL = 'vertical';
    const MENU_TYPES = [self::MENU_TYPE_HORIZONTAL, self::MENU_TYPE_VERTICAL];

    /**
     * @var string
     */
    public $table = 'menus';

    /**
     * The fillable attributes.
     *
     * @var array
     */
    public $fillable = [
        'label',
        'html_class',
        'type',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'label' => 'string',
        'html_class' => 'string',
        'type' => 'string'
    ];

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::MENU_TYPE_HORIZONTAL => mb_convert_case(self::MENU_TYPE_HORIZONTAL, MB_CASE_TITLE),
            self::MENU_TYPE_VERTICAL => mb_convert_case(self::MENU_TYPE_VERTICAL, MB_CASE_TITLE)
        ];
    }

    /**
     * @return string
     */
    public function getTypeLabel()
    {
        $types = self::getTypes();
        return $types[$this->type];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        $instance = $this->hasMany(MenuItem::class);
        $instance->getQuery()->sorted()->whereNull('parent');
        return $instance;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }
}
