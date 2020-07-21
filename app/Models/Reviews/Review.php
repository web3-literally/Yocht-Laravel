<?php

namespace App\Models\Reviews;

use App\User;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Review
 * @package App\Models\Reviews
 */
class Review extends Model
{
    use ElasticquentTrait;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';

    public $table = 'reviews';

    public $fillable = [
        'title',
        'rating',
        'message',
        'recommendation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'rating' => 'integer',
        'recommendation' => 'boolean'
    ];

    /**
     * The attributes that should be casted to date type.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'title',
        'message',
        'rating',
        'recommendation',
        'status',
        'for_instance_type',
        'member_id',
        'member_title',
        'by_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $appends = ['member_id', 'member_title', 'for_instance_type'];

    /**
     * Elasticsearch index id
     *
     * @return string
     */
    function getIndexName()
    {
        return 'reviews';
    }

    /**
     * Reviews which can be indexed for Member Reviews search
     *
     * @return bool
     */
    public function isSearchable()
    {
        return $this->status == self::STATUS_APPROVED;
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array();

    /**
     * @return User
     */
    public function getMemberAttribute()
    {
        if ($this->for->for == 'member') {
            return $this->for->instance;
        }

        return $this->for->instance->user;
    }

    /**
     * @return int
     */
    public function getMemberIdAttribute()
    {
        return $this->member->id;
    }

    /**
     * @return string
     */
    public function getMemberTitleAttribute()
    {
        return $this->member->full_name;
    }

    /**
     * @return string
     */
    public function getForInstanceTypeAttribute()
    {
        return $this->for->for;
    }

    /**
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return mb_convert_case($this->status, MB_CASE_TITLE);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeMembers($query)
    {
        $reviewForTable = (new ReviewFor())->getTable();

        return $query->join($reviewForTable, $reviewForTable . '.review_id', '=', $this->getTable() . '.id')
            ->where($reviewForTable . '.for', 'member')
            ->groupBy($this->getTable() . '.id')
            ->select($this->getTable() . '.*');
    }

    /**
     * @return mixed
     */
    public function scopeSearchable($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function for()
    {
        return $this->hasOne(ReviewFor::class, 'review_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function by()
    {
        return $this->belongsTo(User::class, 'by_id');
    }

    /**
     * @param int $id
     * @return $this
     * @throws \Throwable
     */
    public function attachForMember($id)
    {
        $for = new ReviewFor();
        $for->review_id = $this->id;
        $for->instance_id = $id;
        $for->for = 'member';
        $for->saveOrFail();

        return $this;
    }

    /**
     * @param int $id
     * @return $this
     * @throws \Throwable
     */
    public function attachForJob($id)
    {
        $for = new ReviewFor();
        $for->review_id = $this->id;
        $for->instance_id = $id;
        $for->for = 'job';
        $for->saveOrFail();

        return $this;
    }
}
