<?php

namespace App\Models\Jobs;

use App\Helpers\Geocoding;
use App\Helpers\Owner;
use App\Jobs\Index\JobsUpdate;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Models\Traits\FullTextSearchTrait;
use App\Models\Traits\ThumbTrait;
use App\Models\Vessels\Vessel;
use App\User;
use Elasticquent\ElasticquentTrait;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use MadWeb\Seoable\Contracts\Seoable;
use MadWeb\Seoable\Traits\SeoableTrait;
use CyrildeWit\EloquentViewable\Viewable;
use Sentinel;
use File;

/**
 * Class Job
 * @package App\Models\Jobs
 */
class Job extends Model implements Seoable
{
    use SoftDeletes;
    use Sluggable;
    use SluggableScopeHelpers;
    use ThumbTrait;
    use SeoableTrait;
    use FullTextSearchTrait;
    use Viewable;
    use ElasticquentTrait;

    const STATUS_PUBLISHED = 'published';
    const STATUS_IN_PROCESS = 'in_process';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DRAFT = 'draft';

    const VISIBILITY = ['private', 'public'];

    /**
     * @var string
     */
    public $table = 'jobs';

    protected $appends = ['created', 'location_address', 'location_map', 'categories_index', 'services_index', 'categories_services_titles', 'period'];

    /**
     * @var array
     */
    public $fillable = [
        'title',
        'content',
        'address',
        'starts_at',
        'p_o_number',
        'warranty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'map_lat' => 'string',
        'map_lng' => 'string',
    ];

    /**
     * The attributes that should be casted to date type.
     *
     * @var array
     */
    protected $dates = ['starts_at', 'deleted_at'];

    /**
     * The columns of the full text index
     */
    protected $searchable = [
        'title',
        'content',
        'address'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['user_id', 'title']
            ]
        ];
    }

    public function seoable()
    {
        $this->seo()->setTitleRaw($this->title)->setKeywordsRaw('')->setDescriptionRaw('');
    }

    /**
     * Elasticsearch index id
     *
     * @return string
     */
    function getIndexName()
    {
        return 'jobs';
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array(
        'job_for' => [
            'type' => 'text'
        ],
        'visibility' => [
            'type' => 'text'
        ],
        'status' => [
            'type' => 'text'
        ],
        'slug' => [
            'type' => 'text'
        ],
        'title' => [
            'type' => 'text'
        ],
        'content' => [
            'type' => 'text'
        ],
        'created' => [
            'type' => 'date'
        ],
        'location_address' => [
            'type' => 'text'
        ],
        'location_map' => [
            'type' => 'geo_point'
        ],
        'categories' => [
            'type' => 'nested',
            'properties' => [
                'id' => [
                    'type' => 'long'
                ]
            ]
        ],
        'services' => [
            'type' => 'nested',
            'properties' => [
                'id' => [
                    'type' => 'long'
                ]
            ]
        ],
        'created_at' => [
            'type' => 'text',
            'index' => false
        ],
        'updated_at' => [
            'type' => 'text',
            'index' => false
        ],
        'deleted_at' => [
            'type' => 'text',
            'index' => false
        ],
    );

    /**
     * @return string
     */
    public function getLocationAddressAttribute()
    {
        if ($this->vessel_id) {
            return $this->vessel->location_address;
        }

        return $this->address;
    }

    /**
     * @return array|null
     */
    public function getLocationMapAttribute()
    {
        if ($this->vessel_id) {
            return $this->vessel->location_map;
        }

        return [
            'lat' => $this->map_lat,
            'lon' => $this->map_lng
        ];
    }

    /**
     * @return int
     */
    public function getCreatedAttribute()
    {
        return $this->created_at->format('c');
    }

    /**
     * @return mixed
     */
    public function getMetaAttribute()
    {
        return $this->getSeoData();
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            'draft' => trans('jobs.status_draft'),
            'published' => trans('jobs.status_published'),
            'in_process' => trans('jobs.status_in_process'),
            'completed' => trans('jobs.status_completed')
        ];
    }

    /**
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $statuses = $this->getStatuses();

        return $statuses[$this->status];
    }

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
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function members()
    {
        return $this->hasMany(JobMembers::class, 'job_id');
    }

    /**
     * @return bool
     */
    public function isPersonalJob()
    {
        return ($this->members->count() <= 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ticket()
    {
        return $this->hasOne(JobTickets::class, 'job_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, JobCategories::getModel()->getTable(), 'job_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, JobServices::getModel()->getTable(), 'job_id', 'service_id');
    }

    /**
     * @return array
     */
    public function getCategoriesIndexAttribute()
    {
        $all = [];
        foreach ($this->categories as $category) {
            $ids = array_merge([$category->id], $category->getParentIds());
            $all = array_merge($all, $ids);
        }

        return array_values(array_unique($all));
    }

    /**
     * @return array
     */
    public function getServicesIndexAttribute()
    {
        $all = [];
        foreach ($this->services as $service) {
            $ids = array_merge([$service->id], $service->getParentIds());
            $all = array_merge($all, $ids);
        }

        return array_values(array_unique($all));
    }

    /**
     * @return array
     */
    public function getCategoriesServicesFlatAttribute()
    {
        $all = [];

        foreach ($this->categories as $category) {
            $items = array_merge([
                $category->id => ['id' => $category->id,'title' => $category->label, 'entity_type' => 'category']
            ], $category->getParents());
            $all = array_merge($all, $items);
        }

        foreach ($this->services as $services) {
            $items = array_merge([
                $services->id => ['id' => $services->id,'title' => $services->title, 'entity_type' => 'service']
            ], $services->getParents());
            $all = array_merge($all, $items);
        }

        return $all;
    }

    /**
     * @return string
     */
    public function getPeriodAttribute()
    {
        return $this->periodLink ? $this->periodLink->period->name : '';
    }

    /**
     * @return array
     */
    public function getCategoriesServicesTitlesAttribute()
    {
        $all = collect($this->categories_services_flat);

        return $all->pluck('title')->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applications()
    {
        $instance = $this->hasMany(JobApplications::class);
        return $instance;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function periodLink()
    {
        return $this->hasOne(JobPeriod::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function charges()
    {
        return $this->hasManyThrough(User::class, JobCharges::class, 'job_id', 'id', 'id', 'user_id');
    }

    /**
     * @return bool
     */
    public function isPublicIndex()
    {
        return ($this->visibility == 'public' && $this->status != 'draft');
    }

    public function scopePublished($query)
    {
        return $query->where('status', '!=', self::STATUS_DRAFT);
    }

    public function scopePublishedOnly($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeOnlyPublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeOnlyPublicIndex($query)
    {
        return $query->where($this->getTable() . '.visibility', 'public')->where($this->getTable() . '.status', '!=', 'draft');
    }

    public function scopeIsPrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    public function scopeMy($query, $related_id = null)
    {
        $jobsTable = $this->getTable();
        $slaveTable = (new User())->getTable();

        $user = Sentinel::getUser();

        return $query
            ->join($slaveTable, $jobsTable . '.user_id', '=', $slaveTable . '.id')
            ->where(function ($query) use ($jobsTable, $user, $related_id) {
                $query->where($jobsTable . '.user_id', $user->getUserId());
                if (is_null($related_id)) {
                    $query->whereNull($jobsTable . '.vessel_id');
                } else {
                    $query->where($jobsTable . '.related_member_id', $related_id);
                }
            })
            ->orWhere(function ($query) use ($jobsTable, $slaveTable, $user) {
                $ids = $user->vessels()->pluck('id');
                $query->where($slaveTable . '.parent_id', $user->getUserId())->whereIn($jobsTable . '.vessel_id', $ids);
            })
            ->orderBy($jobsTable . '.created_at', 'desc')
            ->groupBy($jobsTable . '.id')
            ->select($jobsTable . '.*');
    }

    public function scopeRelated($query, $to)
    {
        $jobsTable = $this->getTable();
        $ticketsTable = (new JobTickets())->getTable();

        return $query
            ->join($ticketsTable, $ticketsTable . '.job_id', '=', $jobsTable . '.id')
            ->orderBy($jobsTable . '.created_at', 'desc')
            ->where($jobsTable . '.user_id', Owner::currentOwner()->id)
            ->where($ticketsTable . '.applicant_id', $to)
            ->groupBy($jobsTable . '.id')
            ->select($jobsTable . '.*');
    }

    /**
     * @return $this
     */
    protected function prepareIndex()
    {
        $this->services;

        return $this;
    }

    /**
     * Add to Search Index
     *
     * @return array
     * @throws Exception
     */
    public function addToIndex()
    {
        if (!$this->exists) {
            throw new Exception('Document does not exist.');
        }

        $this->prepareIndex();

        $params = $this->getBasicEsParams();

        // Get our document body data.
        $params['body'] = $this->getIndexDocumentData();

        // The id for the document must always mirror the
        // key for this model, even if it is set to something
        // other than an auto-incrementing value. That way we
        // can do things like remove the document from
        // the index, or get the document from the index.
        $params['id'] = $this->getKey();

        return $this->getElasticSearchClient()->index($params);
    }

    /**
     * Partial Update to Indexed Document
     *
     * @return array
     */
    public function updateIndex()
    {
        $this->prepareIndex();

        $params = $this->getBasicEsParams();

        // Get our document body data.
        $params['body']['doc'] = $this->getIndexDocumentData();

        return $this->getElasticSearchClient()->update($params);
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->content = strip_tags($this->content, config('app.allowable_tags'));

        $job = $this;
        if (empty($job->vessel_id)) {
            $response = Geocoding::latlngLookup($job->address);
            if ($response && $response->status === 'OK') {
                if ($response->results) {
                    $place = current($response->results);
                    $job->map_lat = $place->geometry->location->lat;
                    $job->map_lng = $place->geometry->location->lng;
                }
            }
        } else {
            $job->address = $job->map_lat = $job->map_lng = null;
        }

        $result = parent::save($options);

        if ($result) {
            JobsUpdate::dispatch($this->id)
                ->onQueue('high');
        }

        return $result;
    }

    /**
     * @param bool $save
     * @return $this
     * @throws \Throwable
     */
    public function deleteImage($save = true)
    {
        if ($this->hasImage()) {
            $job = $this;

            $destinationPath = public_path() . '/uploads/jobs/';

            $folders = File::glob($destinationPath . '*', GLOB_ONLYDIR);
            if ($folders) {
                foreach ($folders as $folder) {
                    $filePath = $destinationPath . basename($folder) . '/' . $job->image;
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
            }
            $filePath = $destinationPath . $job->image;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $job->image = null;
            if ($save) {
                $job->saveOrFail();
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasImage()
    {
        return (bool)$this->image;
    }

    /**
     * @param $size
     * @return bool|string
     */
    public function getThumb($size)
    {
        $placeholderUrl = str_replace('{size}', $size, config('app.placeholder_url'));
        if (!$this->hasImage()) {
            return $placeholderUrl;
        }

        $sourceImage = '/uploads/jobs/' . $this->image;
        $thumbUrl = $this->genThumb($sourceImage, $size);
        if (!$thumbUrl) {
            return $placeholderUrl;
        }
        return $thumbUrl;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return route('jobs.show', ['slug' => $this->slug]);
    }

    /**
     * @param int|array $ids
     * @return JobMembers
     * @throws \Throwable
     */
    public function attachMember($ids)
    {
        if (!is_array($ids)) {
            $ids = [(int)$ids];
        }

        foreach ($ids as $id) {
            $member = new JobMembers();
            $member->job_id = $this->id;
            $member->member_id = $id;
            $member->saveOrFail();
        }

        return $member;
    }

    /**
     * @param int $id
     * @return JobPeriod
     * @throws \Throwable
     */
    public function attachPeriod($id)
    {
        $link = new JobPeriod();
        $link->job_id = $this->id;
        $link->period_id = $id;
        $link->saveOrFail();

        return $link;
    }
}
