<?php

namespace App;

use Exception;
use App\Jobs\Index\MembersRemove;
use App\Jobs\Index\MembersUpdate;
use App\Repositories\ServiceRepository;
use Elasticquent\ElasticquentTrait;

class SearchableUser extends ExtendedUser
{
    use ElasticquentTrait;

    /**
     * @var array
     */
    const SEARCHABLE_ROLES = ['vessel', 'business'];

    /**
     * @var array
     */
    protected $appends = ['full_name', 'member_title', 'map', 'since', 'subscribed', 'published', 'rate', 'related_to', 'categories_index', 'services_index', 'service_areas_index'];

    /**
     * Elasticsearch index id
     *
     * @return string
     */
    function getIndexName()
    {
        return 'members';
    }

    /**
     * Profiles which can be indexed for member search
     *
     * @return bool
     */
    public function isSearchableMember()
    {
        return in_array($this->getAccountType(), User::SEARCHABLE_ROLES);
    }

    /**
     * Elasticsearch fields mapping
     *
     * @var array
     */
    protected $mappingProperties = array(
        'member_title' => [
            'type' => 'text',
            'fielddata' => true
        ],
        'created_at' => [
            'type' => 'text',
            'index' => false
        ],
        'updated_at' => [
            'type' => 'text',
            'index' => false
        ],
        'since' => [
            'type' => 'date'
        ],
        'map' => [
            'type' => 'geo_point'
        ],
        'published' => [
            'type' => 'boolean'
        ],
        'subscribed' => [
            'type' => 'boolean'
        ],
        'rate' => [
            'type' => 'integer'
        ],
        'roles' => [
            'type' => 'nested',
            'properties' => [
                'slug' => [
                    'type' => 'text'
                ]
            ]
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
        'service_areas' => [
            'type' => 'nested',
            'properties' => [
                'id' => [
                    'type' => 'long'
                ]
            ]
        ],
        'related_to' => [
            'properties' => [
                'marinas' => [
                    'type' => 'boolean'
                ],
                'shipyards' => [
                    'type' => 'boolean'
                ]
            ]
        ],
        'profile.name' => [
            'type' => 'text'
        ],
        'profile.description' => [
            'type' => 'text'
        ],
    );

    /**
     * Prepare location for Elasticsearch
     *
     * @return array|null
     */
    public function getMapAttribute()
    {
        $map_lat = $this->map_lat;
        $map_lng = $this->map_lng;
        if (!is_null($map_lat) && !is_null($map_lng)) {
            return [
                'lat' => $map_lat,
                'lon' => $map_lng
            ];
        }

        return null;
    }

    /**
     * @return null|int
     */
    public function getMapLatAttribute()
    {
        if ($this->isBoatAccount() || $this->isBusinessAccount()) {
            return $this->profile->map_lat;
        }

        return $this->getAttributeFromArray('map_lat');
    }

    /**
     * @return null|int
     */
    public function getMapLngAttribute()
    {
        if ($this->isBoatAccount() || $this->isBusinessAccount()) {
            return $this->profile->map_lng;
        }

        return $this->getAttributeFromArray('map_lng');
    }

    /**
     * @return int
     */
    public function getSinceAttribute()
    {
        return $this->created_at->format('c');
    }

    /**
     * Is profile published (can be founded via search)
     *
     * @return bool
     */
    public function getPublishedAttribute()
    {
        $published = (bool)$this->isSearchableMember();
        if (empty($this->member_title) && !$this->isVesselAccount()) {
            $published = false;
        }
        return $published;
    }

    /**
     * Is profile subscribed
     *
     * @return bool
     */
    public function getSubscribedAttribute()
    {
        return (bool)$this->hasMembership();
    }

    /**
     * @return array
     */
    public function getRelatedToAttribute()
    {
        return [
            'shipyards' => ($this->isBusinessAccount() && $this->profile->business_type == 'marinas_shipyards')
        ];
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
    public function getServiceAreasIndexAttribute()
    {
        $areas = [];
        foreach ($this->service_areas as $area) {
            $areas[] = $area->hierarchy;
        }

        return $areas;
    }

    /**
     * @return mixed
     */
    public function scopeSearchableAccounts($query)
    {
        $userTable = (new User())->getTable();
        $roleUserTable = 'role_users';
        $roleTable = (new Role())->getTable();
        return $query->join($roleUserTable, $roleUserTable . '.user_id', '=', $userTable . '.id')->join($roleTable, $roleTable . '.id', '=', $roleUserTable . '.role_id')
            ->whereIn($roleTable . '.slug', User::SEARCHABLE_ROLES)
            ->select($userTable . '.*');
    }

    /**
     * @return $this
     */
    protected function prepareIndex()
    {
        $this->roles;
        $this->profile;
        if ($this->isBusinessAccount()) {
            $this->categories;
            $this->services;
            $this->service_areas;
        }

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
        $isNew = !boolval($this->id);

        $result = parent::save($options);

        // Update index data
        if (!$isNew && $this->isSearchableMember()) {
            MembersUpdate::dispatch($this)
                ->onQueue('high');
        }

        return $result;
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $this->getAccountType(); // Cache role before deletion (for isSearchableMember)
        $result = parent::delete();
        if ($result && $this->isSearchableMember()) {
            MembersRemove::dispatchNow($this);
        }

        return $result;
    }
}
