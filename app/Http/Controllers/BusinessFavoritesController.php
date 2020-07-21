<?php

namespace App\Http\Controllers;

use App\Helpers\Geocoding;
use App\Helpers\PageOffset;
use App\Models\Members\FavoriteMember;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\User;
use Sentinel;

/**
 * Class MembersFavoritesController
 * @package App\Http\Controllers
 */
class BusinessFavoritesController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function my(Request $request)
    {
        $must = [
            0 => [
                "match" => [ // Display only published/searchable members by default
                    'published' => true
                ]
            ],
            1 => [
                "match" => [ // Display only my favorite members
                    'user_id' => Sentinel::getUser()->getUserId()
                ]
            ],
            2 => [
                'nested' => [
                    'path' => 'roles',
                    'query' => [
                        'match' => [
                            'roles.slug' => 'business'
                        ]
                    ]
                ],
            ],
        ];

        $filter = [];

        $order = [
            'id' => [
                'order' => 'desc'
            ]
        ];

        if ($location = $request->get('location')) {
            $response = Geocoding::latlngLookup($location);
            if ($response && $response->status === 'OK' && $response->results) {
                $place = current($response->results);

                $filter = [
                    "geo_distance" => [
                        "distance" => "100km",
                        "map" => [
                            "lat" => $place->geometry->location->lat,
                            "lon" => $place->geometry->location->lng
                        ]
                    ]
                ];

                $order = [ // Sort by closest to specified location
                    "_geo_distance" => [
                        "map" => [
                            "lat" => $place->geometry->location->lat,
                            "lon" => $place->geometry->location->lng
                        ],
                        "order" => "asc",
                        "unit" => "km",
                        "distance_type" => "plane"
                    ]
                ];
            } else {
                $favorites = new Collection();
                return view('favorites.business', compact('favorites'));
            }
        }

        if ($role = $request->get('account-type')) {
            $must[] = [
                'nested' => [
                    'path' => 'roles',
                    'query' => [
                        'match' => [
                            'roles.slug' => $role
                        ]
                    ]
                ],
            ];
        }

        if ($services = $request->get('services')) {
            $services = explode(',', $services);

            $shouldMatchIds = [];
            foreach ($services as $serviceId) {
                $shouldMatchIds[] = [
                    'match' => [
                        'services.service_id' => $serviceId
                    ]
                ];
            }

            $must[] = [
                'nested' => [
                    'path' => 'services',
                    'query' => [
                        'bool' => [
                            'should' => $shouldMatchIds
                        ],
                    ]
                ],
            ];
        }

        $limit = 10;

        $query = null;
        if ($must || $filter) {
            $query = [
                "bool" => []
            ];
            if ($must) {
                $query['bool']['must'] = $must;
            }
            if ($filter) {
                $query['bool']['filter'] = $filter;
            }
        }

        $favorites = FavoriteMember::searchByQuery($query, null, null, $limit, PageOffset::offset($limit), $order)->paginate($limit);

        return view('favorites.business', compact('favorites'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store($related_member_id, $id)
    {
        $member = User::searchableAccounts()->find($id);

        if (empty($member)) {
            return abort(404);
        }

        $favorite = FavoriteMember::my()->where('member_id', $id)->get();

        if ($favorite->isNotEmpty()) {
            return abort(404);
        }

        $item = new FavoriteMember();
        $item->member_id = $id;
        $item->user_id = Sentinel::getUser()->getUserId();

        $item->saveOrFail();
        $item->addToIndex();

        return response()->json(true);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($related_member_id, $id)
    {
        $item = FavoriteMember::my()->where('member_id', $id)->first();

        if (empty($item)) {
            return abort(404);
        }

        if ($item->delete()) {
            $item->removeFromIndex();
        }

        return response()->json(true);
    }
}
