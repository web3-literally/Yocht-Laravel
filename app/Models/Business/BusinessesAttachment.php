<?php

namespace App\Models\Business;

use Exception;
use App\File;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Rutorika\Sortable\SortableTrait;

/**
 * Class BusinessesAttachment
 * @package App\Models\Business
 */
class BusinessesAttachment extends Model
{
    //use ElasticquentTrait;
    use SortableTrait;

    public $timestamps = false;

    public $table = 'businesses_attachments';

    public $fillable = [
        'business_id',
        'file_id',
        'type',
    ];

    protected $appends = ['data'];

    /**
     * @var string
     */
    protected static $sortableField = 'order';

    /**
     * @var string
     */
    protected static $sortableGroupField = 'business_id';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'business_id' => 'int',
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
        return 'business_attachments';
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array(
        'business_id' => [
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
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
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
        return $this->file->cleanup() && parent::delete() && $this->file->delete(false);
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
