<?php

namespace App\Models\Members;

use App\User;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Sentinel;

/**
 * Class FavoriteMember
 * @package App\Models\Members
 */
class FavoriteMember extends Model
{
    use ElasticquentTrait;

    /**
     * @var string
     */
    public $table = 'members_favorites';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
        'member_id'
    ];

    protected $casts = [];

    /**
     * Elasticsearch index id
     *
     * @return string
     */
    function getIndexName()
    {
        return 'favorite_members';
    }

    /**
     * @var array
     */
    protected $appends = [
        'roles',
        'services',
        'map',
        'published',
    ];

    protected $mappingProperties = array(
        'roles' => [
            'type' => 'nested',
            'properties' => [
                'slug' => [
                    'type' => 'text'
                ],
            ]
        ],
        'services' => [
            'type' => 'nested',
            'properties' => [
                'service_id' => [
                    'type' => 'long'
                ]
            ]
        ],
    );

    /**
     * @return mixed
     */
    public function getRolesAttribute()
    {
        return $this->member->roles;
    }

    /**
     * @return mixed
     */
    public function getServicesAttribute()
    {
        return $this->member->services;
    }

    /**
     * Prepare location for Elasticsearch
     *
     * @return array|null
     */
    public function getMapAttribute()
    {
        $map_lat = $this->member->map_lat;
        $map_lng = $this->member->map_lng;
        if (!is_null($map_lat) && !is_null($map_lng)) {
            return [
                'lat' => $map_lat,
                'lon' => $map_lng
            ];
        }

        return null;
    }

    /**
     * Is profile published (can be founded via search)
     *
     * @return bool
     */
    public function getPublishedAttribute()
    {
        return (bool)$this->member->isSearchableMember();
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
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('id', 'desc')->where('user_id', $user->getUserId());
    }
}