<?php

namespace App\Models\Jobs;

use App\User;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Sentinel;

/**
 * Class FavoriteJob
 * @package App\Models\Jobs
 */
class FavoriteJob extends Model
{
    use ElasticquentTrait;

    /**
     * @var string
     */
    public $table = 'jobs_favorites';

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $appends = ['title', 'content', 'categories_index', 'services_index'];

    /**
     * @var array
     */
    public $fillable = [
        'job_id'
    ];

    /**
     * Elasticsearch index id
     *
     * @return string
     */
    function getIndexName()
    {
        return 'favorite_jobs';
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array(
        'title' => [
            'type' => 'text'
        ],
        'content' => [
            'type' => 'text'
        ],
        'job' => [
            'type' => 'nested',
            'properties' => [
                'id' => [
                    'type' => 'long'
                ],
            ]
        ],
    );

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * @return array
     */
    public function getTitleAttribute()
    {
        return $this->job->title;
    }

    /**
     * @return array
     */
    public function getContentAttribute()
    {
        return $this->job->content;
    }

    /**
     * @return array
     */
    public function getCategoriesIndexAttribute()
    {
        return $this->job->categories_index;
    }

    /**
     * @return array
     */
    public function getServicesIndexAttribute()
    {
        return $this->job->services_index;
    }

    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('id', 'desc')->where('user_id', $user->getUserId());
    }
}