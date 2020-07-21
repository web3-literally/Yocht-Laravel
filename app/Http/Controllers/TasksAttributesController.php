<?php

namespace App\Http\Controllers;

use App\Helpers\RelatedProfile;
use App\Http\Requests\Tasks\DashboardTasksAttributeRequest;
use App\Models\Eav\AttributeSet;
use App\Models\Eav\Entity;
use Eav\AttributeOption;
use Illuminate\Support\Str;
use DB;
use Eav\Attribute;
use Eav\EntityAttribute;

/**
 * Class TasksAttributesController
 * @package App\Http\Controllers
 */
class TasksAttributesController extends Controller
{
    /**
     * @param int $related_member_id
     * @param DashboardTasksAttributeRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store($related_member_id, DashboardTasksAttributeRequest $request)
    {
        $relatedMember = RelatedProfile::currentRelatedMember();

        $title = $request->get('title');
        $code = Str::slug($title, '_');

        DB::beginTransaction();
        try {
            /** @var Entity $entity */
            $entity = Entity::findByCode('task');

            if (!($attributeSet = $entity->attributeSetFor($related_member_id)->first())) {
                $attributeSet = AttributeSet::create([
                    'attribute_set_name' => 'Related #' . $related_member_id,
                    'entity_id' => $entity->entity_id,
                    'related_member_id' => $related_member_id,
                ]);
            }

            $attribute = Attribute::add([
                'attribute_code' => $code,
                'entity_code' => 'task',
                'backend_class' => null,
                'backend_type' => 'string',
                'backend_table' => null,
                'frontend_class' => null,
                'frontend_type' => $request->get('type') == 'select' ? 'select' : 'text',
                'frontend_label' => $title,
                'default_value' => '',
                'is_required' => 0,
                'is_filterable' => 0,
                'is_searchable' => 0,
                'required_validate_class' => null
            ]);

            EntityAttribute::map([
                'attribute_code' => $code,
                'entity_code' => 'task',
                'attribute_set' => $attributeSet->attribute_set_name,
                'attribute_group' => 'General'
            ]);

            if ($request->get('type') == 'select') {
                $options = [];
                foreach ($request->get('options', []) as $option) {
                    $options[$option] = $option;
                }
                AttributeOption::add($attribute, $options);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            abort(500, 'Failed to create a new attribute');
        }

        return response()->json(['message' => 'Attribute was saved', 'attribute' => $attribute]);
    }

    /**
     * @param int $related_member_id
     * @param string $attribute
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function remove($related_member_id, $attribute)
    {
        $relatedMember = RelatedProfile::currentRelatedMember();

        DB::beginTransaction();
        try {
            /** @var Entity $entity */
            $entity = Entity::findByCode('task');
            $attribute = $entity->attributesFor($related_member_id)->where('attribute_code', $attribute)->first();
            if (!$attribute) {
                throw new \Exception('Not found', 404);
            }
            $attribute->delete();

            DB::table($attribute->backendTable())->where('entity_type_id', $entity->entity_id)->where('attribute_id', $attribute->attribute_id)->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            abort(500, 'Failed to delete attribute');
        }

        return response()->json(['message' => 'Attribute was deleted', 'attribute' => $attribute]);
    }
}
