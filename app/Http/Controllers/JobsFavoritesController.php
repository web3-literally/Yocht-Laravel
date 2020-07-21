<?php

namespace App\Http\Controllers;

use App\Helpers\PageOffset;
use App\Models\Jobs\FavoriteJob;
use App\Models\Jobs\Job;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceGroup;
use Cache;
use Illuminate\Http\Request;
use Sentinel;

/**
 * Class JobsFavoritesController
 * @package App\Http\Controllers
 */
class JobsFavoritesController extends Controller
{
    public function my($related_member_id, Request $request)
    {
        $groups = Cache::rememberForever('JobsFindGroups', function () {
            $groups = ServiceGroup::pluck('label', 'slug')->all();

            return $groups;
        });

        $selectedCategories = request('categories', []);
        $titledCategories = [];
        if ($selectedCategories) {
            $titledCategories = ServiceCategory::whereIn('id', $selectedCategories)->orderBy('position', 'asc')->pluck('label')->all();
        }
        $titledCategories = (string)implode(',', $titledCategories);

        $selectedServices = request('services', []);
        $titledServices = [];
        if ($selectedServices) {
            $titledServices = Service::whereIn('id', $selectedServices)->orderBy('position', 'asc')->pluck('title')->all();
        }
        $titledServices = (string)implode(',', $titledServices);

        $must = [
            0 => [
                "match" => [
                    'user_id' => Sentinel::getUser()->getUserId()
                ]
            ],
        ];
        $must_not = [
            0 => [
                "match" => [
                    'job.status' => 'draft'
                ]
            ],
        ];
        $filter = [];

        if ($keywords = $request->get('keywords')) {
            $must[] = [
                'multi_match' => [
                    'query' => $keywords,
                    'fields' => ['title^3', 'content'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        if (!($request->get('group') == '')) {
            if ($categories = $request->get('categories', [])) {
                foreach($categories as $category) {
                    $must[] = [
                        "match" => [
                            'categories_index' => $category
                        ]
                    ];
                }
            }
        }

        $order = [];

        $limit = 5;

        $query = null;
        if ($must || $must_not || $filter) {
            $query = ["bool" => []];
            if ($must) {
                $query['bool']['must'] = $must;
            }

            if ($must_not) {
                $query['bool']['must_not'] = $must_not;
            }
            if ($filter) {
                $query['bool']['filter'] = $filter;
            }
        }

        $rows = FavoriteJob::searchByQuery($query, null, ['id', 'job_id'], $limit, PageOffset::offset($limit), $order)->paginate($limit);

        return view('favorites.jobs', compact('rows', 'groups', 'selectedCategories', 'selectedServices', 'titledCategories', 'titledServices'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store($related_member_id, $id)
    {
        $job = Job::published()->find($id);

        if (empty($job)) {
            return abort(404);
        }

        $favorite = FavoriteJob::my()->where('job_id', $id)->get();

        if ($favorite->isNotEmpty()) {
            return abort(404);
        }

        $item = new FavoriteJob();
        $item->job_id = $id;
        $item->user_id = Sentinel::getUser()->getUserId();

        $item->saveOrFail();
        $item->addToIndex();

        return response()->json(true);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($related_member_id, $id)
    {
        $item = FavoriteJob::my()->where('job_id', $id);

        if (empty($item)) {
            return abort(404);
        }

        if ($item->delete()) {
            $item->removeFromIndex();
        }

        return response()->json(true);
    }
}
