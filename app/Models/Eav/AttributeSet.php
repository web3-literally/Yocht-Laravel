<?php

namespace App\Models\Eav;

class AttributeSet extends \Eav\AttributeSet
{
    /**
     * @{inheriteDoc}
     */
    protected $fillable = [
        'attribute_set_name', 'entity_id', 'related_member_id'
    ];
}
