<?php

namespace App\Models\Tasks;

use App\Models\Traits\FullTextSearchTrait;
use App\User;
use Carbon\Carbon;
use Elasticquent\ElasticquentTrait;
use Eav\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Sentinel;

/**
 * Class Task
 * @package App\Models\Tasks
 */
class Task extends Model
{
    use SoftDeletes;
    use ElasticquentTrait;
    use FullTextSearchTrait;

    const ENTITY  = 'task';

    public $table = 'tasks';

    /**
     * The columns of the full text index
     */
    protected $searchable = [
        'title',
        'description'
    ];

    /**
     * @return array
     */
    public function getSearchableColumns()
    {
        return $this->searchable;
    }

    protected $appends = [];

    public $fillable = [
        'set_as',
        'priority',
        'title',
        'description',
        'assigned_to_id',
        'due_date_at'
    ];

    protected $casts = [
        'title' => 'string',
        'created_by_id' => 'int',
        'assigned_to_id' => 'int',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Elasticsearch index id
     *
     * @return string
     */
    function getIndexName()
    {
        return 'tasks';
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = [];

    /**
     * @return array
     */
    public static function getSetAsList()
    {
        return [
            'alert' => 'Alert',
            'reminder' => 'Reminder',
            'maintenance_service' => 'Maintenance/service',
            'yard_period_refit' => 'Yard period/refit',
            'work_schedules' => 'Work schedules',
        ];
    }

    /**
     * @return string|null
     */
    public function getSetAsLabelAttribute()
    {
        $list = $this->getSetAsList();

        return $list[$this->set_as] ?? null;
    }

    /**
     * @return array
     */
    public static function getPriorityList()
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
        ];
    }

    /**
     * @return string|null
     */
    public function getPriorityLabelAttribute()
    {
        $list = $this->getPriorityList();

        return $list[$this->priority] ?? null;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            'snoozed' => 'Snoozed',
            'acknowledge' => 'Acknowledge',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $statuses = $this->getStatuses();

        return $statuses[$this->status] ?? '';
    }

    /**
     * @return bool
     */
    public function isOverdue()
    {
        return (Carbon::parse($this->due_date_at) < Carbon::now());
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
    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shared_to()
    {
        return $this->belongsToMany(User::class, TaskShare::getModel()->getTable(), 'task_id', 'user_id');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeHasAccess($query, $related_id = 0)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $table = $this->getTable();

        return $query->leftJoin('task_share', $table . '.id', '=', 'task_share.task_id')
            ->where($table . '.related_member_id', $related_id)
            ->where(function ($query) use ($user, $table) {
                $query->orWhere($table . '.created_by_id', $user->id)
                    ->orWhere($table . '.assigned_to_id', $user->id)
                    ->orWhere('task_share.user_id', $user->id);
            })
            ->groupBy($table . '.id')
            ->select($table . '.*');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeMy($query, $related_id = 0)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $table = $this->getTable();

        return $query->where($table . '.related_member_id', $related_id)
            ->where($table . '.created_by_id', $user->id)
            ->groupBy($table . '.id')
            ->select($table . '.*');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeOwned($query, $related_id = 0)
    {
        $table = $this->getTable();

        return $query->my($related_id)->where($table . '.status', '!=', 'completed');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeAssignedToMe($query, $related_id = 0)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $table = $this->getTable();

        return $query->where($table . '.related_member_id', $related_id)
            ->where($table . '.assigned_to_id', $user->id)
            ->select($table . '.*');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeUpcoming($query, $related_id = 0)
    {
        $table = $this->getTable();

        return $query->assignedToMe($related_id)->where($table . '.status', '')->whereDate($table . '.due_date_at', '>=', Carbon::now()->format('Y-m-d'));
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeSnoozed($query, $related_id = 0)
    {
        $table = $this->getTable();

        return $query->assignedToMe($related_id)->where($table . '.status', 'snoozed')->whereDate($table . '.due_date_at', '>=', Carbon::now()->format('Y-m-d'));
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeOverdue($query, $related_id = 0)
    {
        $table = $this->getTable();

        return $query->assignedToMe($related_id)->whereDate($table . '.due_date_at', '<', Carbon::now()->format('Y-m-d'));
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeAcknowledge($query, $related_id = 0)
    {
        $table = $this->getTable();

        return $query->assignedToMe($related_id)->where($table . '.status', 'acknowledge');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeCompleted($query, $related_id = 0)
    {
        $table = $this->getTable();

        return $query->my($related_id)->where($table . '.status', 'completed');
    }

    /**
     * @param Builder $query
     * @param int $related_id
     * @return mixed
     */
    public function scopeSharedToMe($query, $related_id = 0)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $table = $this->getTable();

        return $query->join('task_share', $table . '.id', '=', 'task_share.task_id')
            ->where($table . '.related_member_id', $related_id)
            ->where('task_share.user_id', $user->id)
            ->groupBy($table . '.id')
            ->select($table . '.*');
    }

    public function insertMainTable(\Illuminate\Database\Eloquent\Builder $query, array $options, $attributes, $loadedAttributes)
    {
        if ($this->fireModelEvent('creating.main') === false) {
            return false;
        }

        $mainTableAttribute = $this->getMainTableAttribute($loadedAttributes);
        $mainTableAttribute = array_merge($mainTableAttribute, [
            'set_as',
            'priority',
            'title',
            'description',
            'status',
            'created_by_id',
            'assigned_to_id',
            'due_date_at',
            'related_member_id',
            'entity_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $mainData = array_intersect_key($attributes, array_flip($mainTableAttribute));

        $this->insertAndSetId($query, $mainData);

        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->exists = true;

        $this->wasRecentlyCreated = true;

        $this->fireModelEvent('created.main', false);

        return true;
    }

    public function updateMainTable(\Illuminate\Database\Eloquent\Builder $query, array $options, $attributes, $loadedAttributes)
    {
        if ($this->fireModelEvent('updating.main') === false) {
            return false;
        }

        $mainTableAttribute = $this->getMainTableAttribute($loadedAttributes);
        $mainTableAttribute = array_merge($mainTableAttribute, [
            'set_as',
            'priority',
            'title',
            'description',
            'status',
            'created_by_id',
            'assigned_to_id',
            'due_date_at',
            'related_member_id',
            'entity_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $mainData = array_intersect_key($attributes, array_flip($mainTableAttribute));

        $numRows = $this->setKeysForSaveQuery($query)->update($mainData);

        $this->fireModelEvent('updated.main', false);

        return true;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->description = strip_tags($this->description, config('app.allowable_tags'));

        return parent::save($options);
    }
}
