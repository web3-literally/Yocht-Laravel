<?php

namespace App\Repositories\Jobs;

use App\Models\Jobs\Job;
use Illuminate\Pagination\LengthAwarePaginator;
use InfyOm\Generator\Common\BaseRepository;
use Sentinel;

class JobsRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Job::class;
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
        return Job::published()->search($query)->paginate(config('search.max_results'), ['*'], $this->pageName);
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
                    0 => [
                        "match" => [
                            'visibility' => 'public'
                        ]
                    ],
                    1 => [
                        "match" => [
                            'status' => Job::STATUS_PUBLISHED
                        ]
                    ],
                    2 => [
                        'multi_match' => [
                            'query' => $keywords,
                            'fields' => ['title', 'content'],
                            'fuzziness' => 'AUTO'
                        ]
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

        return Job::searchByQuery($query, null, $store, $limit, $offset, ['_score']);
    }

    /**
     * @param string|null $status
     * @param string|null $query
     * @param int|null $category_id
     * @param int|null $service_id
     * @param int|null $location_id
     * @param mixed $callback
     * @return LengthAwarePaginator
     * @deprecated
     */
    public function listing(string $status = null, string $query = null, int $category_id = null, int $service_id = null, $location_id = null, $callback = null): LengthAwarePaginator
    {
        $builder = Job::published();
        if (!is_null($status)) {
            $builder->where('status', $status);
        }
        if ($query) {
            $builder->search($query);
        }
        if ($category_id) {
            $builder->where('category_id', $category_id);
        }
        if ($service_id) {
            $builder->where('service_id', $service_id);
        }
        if ($location_id) {
            $builder->where('location_id', $location_id);
        }
        $builder->where('visibility', 'public');
        if (!is_null($callback)) {
            $callback($builder);
        }
        return $builder->orderBy('created_at', 'desc')->paginate(5);
    }

    /**
     * @param $related_id
     * @param null|string $status
     * @return int
     */
    public function unreadJobs($related_id, $status = null)
    {
        // TODO: Optimize, mysql view can improve performance
        $user_id = Sentinel::getUser()->getUserId();
        $unread = 0;

        $query = Job::my($related_id);

        if (is_null($status)) {
            $query->where('jobs.status', '!=', Job::STATUS_DRAFT);
        } else {
            $query->where('jobs.status', $status);
        }

        $query->get()->each(function ($item, $key) use ($user_id, &$unread) {
            $item->applications->each(function ($item, $key) use ($user_id, &$unread) {
                if ($item->thread->thread->isUnread($user_id)) {
                    $unread++;
                }
            });
        });

        return $unread;
    }
}
