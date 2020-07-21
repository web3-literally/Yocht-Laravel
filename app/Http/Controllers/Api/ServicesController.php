<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceGroup;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Cache;

/**
 * Class ServicesController
 * @package App\Http\Controllers
 */
class ServicesController extends Controller
{
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * ServicesController constructor.
     * @param MessageBag $messageBag
     * @param ServiceRepository $serviceRepository
     */
    public function __construct(MessageBag $messageBag, ServiceRepository $serviceRepository)
    {
        parent::__construct($messageBag);

        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function group(Request $request)
    {
        $request->validate([
            'id' => ['nullable', 'numeric', 'exists:services_groups,id'],
            'slug' => ['nullable', 'exists:services_groups,slug']
        ]);

        // TODO: Optimize search by $groupId or $groupSlug
        $groupId = $request->get('id');
        $groupSlug = $request->get('slug');

        $categoriesTree = Cache::remember('ServicesGroup' . md5($groupId . $groupSlug), 15, function () use ($groupId, $groupSlug) {
            $categoriesTree = [];

            $builder = ServiceCategory::orderBy('position', 'asc');
            if ($groupId) {
                $builder->where('services_categories.group_id', $groupId);
            } elseif ($groupSlug) {
                $builder->join('services_groups', 'services_categories.group_id', '=', 'services_groups.id');
                $builder->where('services_groups.slug', $groupSlug);
            }

            /** @var Collection $categories */
            $categories = $builder->select('services_categories.*')
                ->orderBy('services_categories.position', 'asc')
                ->orderBy('services_categories.id', 'asc')
                ->get();

            if ($categories->count()) {
                $categoriesTree = $this->serviceRepository->createCategoriesTree($categories);
            }

            return $categoriesTree;
        });

        return response()->json($categoriesTree);
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function category(Request $request)
    {
        $request->validate([
            'id' => ['nullable', 'numeric', 'exists:services_categories,id'],
        ]);

        $categoryId = $request->get('id');

        $servicesTree = Cache::remember('ServicesCategory' . $categoryId, 15, function () use ($categoryId) {
            $servicesTree = [];

            $builder = Service::orderBy('position', 'asc');
            if ($categoryId) {
                $builder->where('category_id', $categoryId);
            }

            /** @var Collection $services */
            $services = $builder->get();

            if ($services->count()) {
                $servicesTree = $this->serviceRepository->createTree($services);
            }

            return $servicesTree;
        });

        return response()->json($servicesTree);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function treeAll()
    {
        $tree = Cache::remember('ServicesFullTree', 1440, function () {
            $tree = [];

            $groups = ServiceGroup::orderBy('id', 'asc')->get()->all();
            foreach ($groups as $group) {
                $item = [
                    'id' => $group->id,
                    'text' => $group->label,
                    'type' => 'group'
                ];
                $item['children'] = resolve(\App\Repositories\ServiceRepository::class)->createCategoriesTree($group->categories);

                $tree[] = $item;
            }

            return $tree;
        });

        return response()->json($tree);
    }
}
