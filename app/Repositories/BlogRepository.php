<?php

namespace App\Repositories;

use App\Blog;
use Illuminate\Pagination\LengthAwarePaginator;
use InfyOm\Generator\Common\BaseRepository;

class BlogRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Blog::class;
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
        return Blog::published()->search($query)->paginate(config('search.max_results'), ['*'], $this->pageName);
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
        return Blog::searchByQuery([
            'multi_match' => [
                'query' => $keywords,
                'fields' => ['title', 'content'],
                'fuzziness' => 'AUTO'
            ]
        ], null, $store, $limit, $offset, ['_score']);
    }

    /**
     * @param string|null $query
     * @param mixed $callback
     * @return LengthAwarePaginator
     */
    public function listing(string $query = null, $callback = null): LengthAwarePaginator
    {
        $builder = Blog::published();
        if ($query) {
            $builder->search($query);
        }

        if (!is_null($callback)) {
            $callback($builder);
        }

        return $builder->orderBy('publish_on', 'desc')->paginate(14);
    }
}
