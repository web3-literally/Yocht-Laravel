<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Pion\Support\Eloquent\Position\Traits\PositionEventsTrait;
use Pion\Support\Eloquent\Position\Traits\PositionTrait;
use Route;

/**
 * Class MenuItem
 *
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $menu_id
 * @property string $html_class
 * @property string $visible_for
 * @property string $link
 * @property string $label
 * @property int $order
 * @property int $parent
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class MenuItem extends Model
{
    use \Rutorika\Sortable\SortableTrait;

    /**
     * @var string
     */
    public $table = 'menu_items';

    /**
     * @var string
     */
    protected static $sortableField = 'order';

    /**
     * @var string
     */
    protected static $sortableGroupField = ['menu_id', 'parent'];

    /**
     * @var array
     */
    public $positionGroup = ['parent'];

    /**
     * The fillable attributes.
     *
     * @var array
     */
    public $fillable = [
        'title',
        'content',
        'menu_id',
        'html_class',
        'visible_for',
        'link',
        'label',
        'order',
        'parent',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'content' => 'string',
        'menu_id' => 'integer',
        'html_class' => 'string',
        'visible_for' => 'array',
        'link' => 'string',
        'label' => 'string',
        'order' => 'integer',
        'parent' => 'integer',
    ];

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title ? $this->title : $this->label;
    }

    public function getItemType()
    {
        $type = 'link';
        if ($this->link == 'home') {
            $type = 'home';
        }
        return $type;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        $instance = $this->hasMany(MenuItem::class, 'parent', 'id');
        $instance->getQuery()->sorted();
        return $instance;
    }
}
