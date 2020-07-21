<?php

namespace App\Repositories\Classifieds;

use App\Models\Classifieds\Classifieds;
use Illuminate\Pagination\LengthAwarePaginator;
use InfyOm\Generator\Common\BaseRepository;
use Sentinel;

class ClassifiedsRepository extends BaseRepository
{
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
        return Classifieds::class;
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
        return Classifieds::search($query)->paginate(config('search.max_results'), ['*'], $this->pageName);
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

        return Classifieds::searchByQuery($query, null, $store, $limit, $offset, ['_score']);
    }

    /**
     * @param string|null $query
     * @param array $params
     * @param string $order
     * @param mixed $callback
     * @return LengthAwarePaginator
     * @deprecated
     */
    public function listing(string $query = null, array $params = null, string $order = null, $callback = null): LengthAwarePaginator
    {
        $builder = Classifieds::published();

        if ($query) {
            $builder->search($query);
        }

        $params['category_id'] = $params['category_id'] ?? null;
        if ($params['category_id']) {
            $builder->where('category_id', $params['category_id']);
        }

        $params['location_id'] = $params['location_id'] ?? null;
        if ($params['location_id']) {
            $builder->where('location_id', $params['location_id']);
        }

        $params['manufacturer'] = $params['manufacturer'] ?? null;
        if ($params['manufacturer']) {
            $builder->where('manufacturer', $params['manufacturer']);
        }

        $params['state'] = $params['state'] ?? null;
        if (in_array($params['state'], array_keys(Classifieds::getStates()))) {
            $builder->where('state', $params['state']);
        }

        $params['type'] = $params['type'] ?? null;
        if (in_array($params['type'], array_keys(Classifieds::getTypes()))) {
            $builder->where('type', $params['type']);
        }

        $params['from_length'] = $params['from_length'] ?? null;
        if ($params['from_length']) {
            $builder->where('length', '>=', $params['from_length']);
        }
        $params['to_length'] = $params['to_length'] ?? null;
        if ($params['to_length']) {
            $builder->where('length', '<=', $params['to_length']);
        }

        $params['from_year'] = $params['from_year'] ?? null;
        if ($params['from_year']) {
            $builder->where('year', '>=', $params['from_year']);
        }
        $params['to_year'] = $params['to_year'] ?? null;
        if ($params['to_year']) {
            $builder->where('year', '<=', $params['to_year']);
        }

        $params['from_price'] = $params['from_price'] ?? null;
        if ($params['from_price']) {
            $builder->where('price', '>=', $params['from_price']);
        }
        $params['to_price'] = $params['to_price'] ?? null;
        if ($params['to_price']) {
            $builder->where('price', '<=', $params['to_price']);
        }

        switch ($order) {
            case 'name':
                $builder->orderBy('title', 'asc');
                break;
            case 'price_asc':
                $builder->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $builder->orderBy('price', 'desc');
                break;
            case 'new':
            default:
                $builder->orderBy('created_at', 'desc');
                break;
        }

        if (!is_null($callback)) {
            $callback($builder);
        }
        return $builder->paginate(9);
    }

    /**
     * @return array
     */
    public static function withinDropdown()
    {
        $within = [];
        for ($i = 1; $i <= 3; $i++) {
            $value = 100 * $i;
            $within[$value] = ($value) . ' miles';
        }

        $within['unlimited'] = 'Unlimited';

        return $within;
    }
}
