<?php

namespace App\Models\Eav;

use Eav\Attribute;
use Eav\AttributeSet;
use Eav\EntityAttribute;

class Entity extends \Eav\Entity
{
    /**
     * Find the entity by code.
     *
     * @param  string $code
     * @return \Eav\Entity
     */
    public static function findByCode(string $code)
    {
        return self::where('entity_code', '=', $code)->firstOrFail();
    }

    /**
     * Define a one-to-many relationship for attribute set.
     *
     * @param int $related_member_id
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributeSetFor($related_member_id)
    {
        return $this->attributeSet()->where('related_member_id', $related_member_id);
    }

    /**
     * Define a one-to-many relationship for attribute.
     *
     * @param int $related_member_id
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributesFor($related_member_id)
    {
        $attributeTable = Attribute::getModel()->getTable();
        $entityAttributeTable = EntityAttribute::getModel()->getTable();
        $attributeSetTable = AttributeSet::getModel()->getTable();
        return $this->attributes()
            ->join($entityAttributeTable, $attributeTable . '.attribute_id', '=', $entityAttributeTable . '.attribute_id')
            ->join($attributeSetTable, $entityAttributeTable . '.attribute_set_id', '=', $attributeSetTable . '.attribute_set_id')
            ->where($attributeSetTable . '.related_member_id', $related_member_id)
            ->select($attributeTable . '.*');
    }
}
