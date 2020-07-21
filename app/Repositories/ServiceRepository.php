<?php

namespace App\Repositories;

use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceGroup;
use Cache;
use Illuminate\Support\Collection;
use InfyOm\Generator\Common\BaseRepository;
use App\Role;
use App\UserServices;
use DB;

class ServiceRepository extends BaseRepository
{
    /**
     * Needed to split docks, based on selected services (specialized in)
     *
     */
    const SHIPYARDS_ID = 8;

    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Service::class;
    }

    /**
     * @return array
     */
    public function getGroupsDropdownList()
    {
        $groups = Cache::rememberForever('ServiceGroup', function () {
            $groups = ServiceGroup::pluck('label', 'slug')->all();

            return $groups;
        });

        return $groups;
    }

    /**
     * @param array $except
     * @return array
     */
    public function getDropdownList($except = [])
    {
        $servicesGroup = [];
        $services = Service::whereNotIn('id', $except)->orderBy('title', 'asc')->get();
        foreach ($services as $service) {
            $servicesGroup[$service->category->label][$service->id] = $service->title;
        }

        return $servicesGroup;
    }

    public function getDropdownListFor($role, $except = [])
    {
        $role = Role::where('slug', $role)->firstOrFail();
        $categories = ServiceCategory::where('provided_by', $role->id)->pluck('id')->all();

        $servicesGroup = [];
        $services = Service::whereNotIn('id', $except)->whereIn('category_id', $categories)->orderBy('title', 'asc')->get();
        foreach ($services as $service) {
            $servicesGroup[$service->category->label][$service->id] = $service->title;
        }

        return $servicesGroup;
    }

    /**
     * @param Collection $flat
     * @param int|null $parent
     * @return array
     */
    public function createCategoriesTree(Collection &$flat, $parent = null)
    {
        $list = [];
        /** @var ServiceCategory $row */
        foreach ($flat as $row) {
            if ($row->parent_id == $parent) {
                $item = [
                    'id' => $row->id,
                    'text' => $row->label,
                    'type' => 'category'
                ];

                $children = $this->createCategoriesTree($flat, $row->id);
                if ($children) {
                    $item['children'] = $children;
                }

                if ($row->hasServices()) {
                    $item['services'] = $this->createServicesTree($row->services);
                }

                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * @param Collection $flat
     * @param int|null $parent
     * @return array
     */
    public function createServicesTree(Collection &$flat, $parent = null)
    {
        $list = [];
        /** @var Service $row */
        foreach ($flat as $row) {
            if ($row->parent_id == $parent) {
                $item = [
                    'id' => $row->id,
                    'text' => $row->title,
                    'type' => $row->type,
                ];

                $children = $this->createServicesTree($flat, $row->id);
                if ($children) {
                    $item['children'] = $children;
                }

                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * @param Collection $flat
     * @param int|null $parent
     * @return array
     */
    public function createTree(Collection &$flat, $parent = null)
    {
        $list = [];
        foreach ($flat as $row) {
            if ($row->parent_id == $parent) {
                $item = [
                    'id' => $row->id,
                    'text' => $row->title,
                    'type' => $row->type,
                    'category_id' => $row->category_id,
                    'parent_id' => $row->parent_id,
                    'data' => $row->data,
                    'selectable' => true
                ];

                $children = $this->createTree($flat, $row->id);
                if ($children) {
                    $item['children'] = $children;
                    // Check if no child specialization
                    foreach($children as $child) {
                        if ($child['type'] == 'service') {
                            $item['selectable'] = false;
                            break;
                        }
                    }
                }

                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * @deprecated
     *
     * @param array $providedBy
     * @return array
     */
    public function servicesAvailable(array $providedBy)
    {
        $categoriesTable = (new ServiceCategory())->getTable();
        $rolesTable = (new Role())->getTable();
        $servicesTable = (new Service())->getTable();
        return ServiceCategory::join($rolesTable, $rolesTable . '.id', '=', $categoriesTable . '.provided_by')
            ->join($servicesTable, $categoriesTable . '.id', '=', $servicesTable . '.category_id')
            ->whereIn($rolesTable . '.slug', $providedBy)
            ->groupBy($categoriesTable . '.provided_by')
            ->select([DB::raw($rolesTable . '.slug' . ' AS `provided_by`'), DB::raw('COUNT(' . $servicesTable . '.id' . ') AS `services`')])
            ->pluck('services', 'provided_by')
            ->all();
    }

    /**
     * @deprecated
     *
     * @param int $userId
     * @return mixed
     */
    public function memberIsShipyard(int $userId)
    {
        $userServicesTable = (new UserServices())->getTable();
        $servicesTable = (new Service())->getTable();
        return DB::table($userServicesTable)->join($servicesTable, $servicesTable . '.id', '=', $userServicesTable . '.service_id')
            ->where($userServicesTable . '.user_id', $userId)
            ->where($servicesTable . '.category_id', self::SHIPYARDS_ID)
            ->groupBy($userServicesTable . '.user_id')
            ->select([DB::raw('COUNT(' . $servicesTable . '.id' . ') AS `val`')])
            ->value('val');
    }
}
