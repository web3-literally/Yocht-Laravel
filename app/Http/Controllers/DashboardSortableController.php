<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rutorika\Sortable\SortableController;

/**
 * Class DashboardSortableController
 * @package App\Http\Controllers
 */
class DashboardSortableController extends SortableController
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function sort(Request $request)
    {
        $sortableEntities = app('config')->get('sortable.entities', []);
        $validator = $this->getValidator($sortableEntities, $request);

        if (!$validator->passes()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'failed' => $validator->failed(),
            ];
        }

        list($entityClass) = $this->getEntityInfo($sortableEntities, (string)$request->input('entityName'));

        $entity = $entityClass::my()->find($request->input('id'));

        if (empty($entity)) {
            return abort(404);
        }

        return parent::sort($request);
    }
}