<?php

namespace App\Repositories;

use App\Models\Events\Event;
use App\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use InfyOm\Generator\Common\BaseRepository;
use Sentinel;

class EventsRepository extends BaseRepository
{
    const GEO_DISTANCE = '300mi';

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Event::class;
    }

    /**
     * Name of page parameter
     * @var string
     */
    protected $pageName = 'page';

    /**
     * @param string $name
     * @return $this
     */
    public function setPageName(string $name = 'page')
    {
        $this->pageName = $name;

        return $this;
    }

    /**
     * @param string $query
     * @return LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator
    {
        return Event::search($query)->paginate(config('search.max_results'), ['*'], $this->pageName);
    }

    /**
     * @param string $keywords
     * @param null $limit
     * @param null $store
     * @param null $offset
     * @return \Elasticquent\ElasticquentResultCollection
     */
    public function findByKeywords(string $keywords, $limit = null, $store = null, $offset = null)
    {
        $limit = $limit ?? config('search.max_results');

        $query = [
            'bool' => [
                'must' => [
                    'multi_match' => [
                        'query' => $keywords,
                        'fields' => ['title', 'description'],
                        'fuzziness' => 'AUTO'
                    ]
                ],
            ]
        ];
        if (Sentinel::check()) {
            $query['bool']['must_not'] = [
                0 => [
                    'match' => [
                        'user_id' => Sentinel::getUser()->getUserId()
                    ]
                ]
            ];
        }

        return Event::searchByQuery($query, null, $store, $limit, $offset, ['_score']);
    }

    /**
     * @param string|null $query
     * @param string|null $from
     * @param string|null $to
     * @param int|null $category_id
     * @param int|null $location_id
     * @param mixed $callback
     * @return LengthAwarePaginator
     * @deprecated
     */
    public function listing(string $query = null, string $from = null, string $to = null, int $category_id = null, $location_id = null, $callback = null): LengthAwarePaginator
    {
        if ($from || $to) {
            $builder = Event::query();
        } else {
            $builder = Event::upcoming();
        }
        if ($query) {
            $builder->search($query);
        }
        if ($from) {
            $builder->where('starts_at', '>=', date('Y-m-d 00:00:00', strtotime($from)));
        }
        if ($to) {
            $builder->where('starts_at', '<=', date('Y-m-d 23:59:59', strtotime($to)));
        }
        if ($category_id) {
            $builder->where('category_id', $category_id);
        }
        if ($location_id) {
            $builder->where('location_id', $location_id);
        }
        if (!is_null($callback)) {
            $callback($builder);
        }
        return $builder->orderBy('starts_at', 'asc')->paginate(5);
    }

    /**
     * @param array $query
     * @return \Elasticquent\ElasticquentResultCollection
     */
    protected function searchByQuery($query) {
        return Event::searchByQuery($query, null, ['id', 'user_id', 'title', 'slug', 'image',
            'category_id', 'type', 'description', 'price', 'starts_at', 'ends_at', 'address', 'map_lat', 'map_lng',
            'created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * @param User $related
     * @param Carbon $start
     * @param Carbon|null $end
     * @param callable $callback
     * @return \Illuminate\Support\Collection
     */
    public function getEventsByDate($related, Carbon $start, Carbon $end = null, callable $callback = null)
    {
        if (is_null($end)) {
            $end = $start->copy();
        }

        $start = $start->startOfDay();
        $end = $end->endOfDay();

        $query = [
            'bool' => [
                'must' => [
                    0 => [
                        'range' => [
                            'starts' => [
                                'gte' => $start->format('c'),
                                'lte' => $end->format('c'),
                            ]
                        ]
                    ],
                    1 => [
                        'match' => [
                            'related_member_id' => $related->id
                        ]
                    ]
                ]
            ]
        ];

        if ($callback) {
            $query = call_user_func($callback, $query);
        }

        $events = $this->searchByQuery($query);

        return $events;
    }

    /**
     * @param User $related
     * @param Carbon $start
     * @param Carbon|null $end
     * @param callable $callback
     * @return \Illuminate\Support\Collection
     */
    public function getNearestEventsByDate($related, Carbon $start, Carbon $end = null, callable $callback = null)
    {
        if (is_null($end)) {
            $end = $start->copy();
        }

        $start = $start->startOfDay();
        $end = $end->endOfDay();

        $query = [
            'bool' => [
                'must' => [
                    0 => [
                        'range' => [
                            'starts' => [
                                'gte' => $start->format('c'),
                                'lte' => $end->format('c'),
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($related->isBusinessAccount()) {
            $location = [
                'lat' => request()->cookie('current_location_lat'),
                'lon' => request()->cookie('current_location_lng')
            ];
        }
        if ($related->isVesselAccount()) {
            $location = $related->profile->location_map;
            if (!$location) {
                $location = [
                    'lat' => null,
                    'lon' => null
                ];
            }
        }

        $query['bool']['filter'][] = [
            "geo_distance" => [
                "distance" => EventsRepository::GEO_DISTANCE,
                "map" => $location
            ]
        ];

        if ($callback) {
            $query = call_user_func($callback, $query);
        }

        $events = $this->searchByQuery($query);

        return $events;
    }
}
