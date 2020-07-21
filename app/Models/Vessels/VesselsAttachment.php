<?php

namespace App\Models\Vessels;

use Exception;
use App\File;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Rutorika\Sortable\SortableTrait;
use Sentinel;

/**
 * Class VesselsAttachment
 * @package App\Models\Vessels
 */
class VesselsAttachment extends Model
{
    use ElasticquentTrait;
    use SortableTrait;

    public $timestamps = false;

    public $table = 'vessels_attachments';

    public $fillable = [
        'user_id',
        'vessel_id',
        'file_id',
        'global_folder',
        'type',
        'access_mode',
    ];

    protected $appends = ['data'];

    /**
     * @var string
     */
    protected static $sortableField = 'order';

    /**
     * @var string
     */
    protected static $sortableGroupField = 'vessel_id';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'vessel_id' => 'int',
        'file_id' => 'int',
        'order' => 'int'
    ];

    /**
     * Elasticsearch index id
     *
     * @return string
     */
    function getIndexName()
    {
        return 'vessel_attachments';
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array(
        'user_id' => [
            'type' => 'integer'
        ],
        'vessel_id' => [
            'type' => 'integer'
        ],
        'file_id' => [
            'type' => 'integer'
        ],
        'data' => [
            'type' => 'text',
            'store' => false
        ],
        'type' => [
            'type' => 'text'
        ],
        'access_mode' => [
            'type' => 'text'
        ],
        'order' => [
            'type' => 'integer'
        ]
    );

    /**
     * @return string
     */
    public function getDataAttribute()
    {
        return base64_encode(file_get_contents($this->file->getFilePath()));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'vessel_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->access_mode == 'full') {
            return $this->file->cleanup() && parent::delete() && $this->file->delete(false);
        }

        return parent::delete();
    }

    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        $classifiedsTable = (new Vessel())->getTable();
        $classifiedsImagesTable = (new Vessel())->getTable();
        return $query->join($classifiedsTable, $classifiedsTable . '.id', '=', $classifiedsImagesTable . '.classified_id')->where($classifiedsTable . '.user_id', $user->getUserId())->select($classifiedsImagesTable . '.*');
    }

    /**
     * @param string $size
     * @return string
     */
    public function getThumb($size)
    {
        return $this->file->getThumb($size);
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

        $params = $this->getBasicEsParams();
        $params['pipeline'] = 'attachment';

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
}
