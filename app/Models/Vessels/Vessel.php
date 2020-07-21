<?php

namespace App\Models\Vessels;

use App\CrewMember;
use App\Helpers\Country;
use App\Jobs\Index\MembersUpdate;
use App\Models\Classifieds\ClassifiedsCategory;
use App\Models\Classifieds\ClassifiedsManufacturer;
use App\Models\Position;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Igaster\LaravelCities\Geo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MadWeb\Seoable\Traits\SeoableTrait;
use App\File;
use App\User;
use Sentinel;
use Cache;

/**
 * Class Vessel
 * @package App\Models\Vessels
 */
class Vessel extends Model
{
    use SoftDeletes;
    use Sluggable;
    use SluggableScopeHelpers;
    use SeoableTrait;

    public $table = 'vessels';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name_prefix',
        'name',
        'manufacturer_id',
        'propulsion',
        'fuel_type',
        'color',
        'charter',
        'private',
        'flag',
        'category_id',
        'description',
        'address',
        'year',
        'length',
        'guest_capacity',
        'crew_capacity',
        'hull_type',
        'max_speed',
        'cruise_speed',
        'vessel_type',
        'width',
        'draft',
        'fuel',
        'fresh_water',
        'black_water',
        'grey_water',
        'clean_oil',
        'dirty_oil',
        'gear_oil',
        'registered_port_id',
        'registered_location_id',
        'imo',
        'official',
        'mmsi',
        'call_sign',
        'on',
        'gross_tonnage',
        'net_tonnage',
        'hull',
        'number_of_engines',
        'make_main_engines',
        'hp_of_engine',
        'engine_model',
        'number_of_generators',
        'make_main_generators',
        'generator_model',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'user_id' => 'integer',
        'owner_id' => 'integer',
        'manufacturer_id' => 'integer',
        'registered_location_id' => 'integer',
        'slug' => 'string',
        'fuel_type' => 'string',
        'description' => 'string',
        'address' => 'string',
        'year' => 'integer',
        'hp_of_engine' => 'integer',
        'length' => 'integer',
        'map_lat' => 'string',
        'map_lng' => 'string',
        'owners' => 'array',
        'staff' => 'array',
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
                'source' => ['user_id', 'name']
            ]
        ];
    }

    public function seoable()
    {
        $this->seo()->setTitleRaw($this->title)->setKeywordsRaw('')->setDescriptionRaw('');
    }

    /**
     * @return int
     */
    public function getMemberId()
    {
        return $this->user->id;
    }

    /**
     * @return string|null
     */
    public function getLocationAddressAttribute()
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getLocationAttribute()
    {
        if (!($this->location_city || $this->location_country)) {
            return null;
        }
        return implode(', ', [$this->location_city, $this->location_country]);
    }

    /**
     * @return array|null
     */
    public function getLocationMapAttribute()
    {
        if (!is_null($this->map_lat) && !is_null($this->map_lng)) {
            return [
                'lat' => $this->map_lat,
                'lon' => $this->map_lng
            ];
        }

        return null;
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
    public static function getHullTypes()
    {
        return Cache::get('vessels_hull_types', function () {
            return [
                'aluminum' => 'Aluminum',
                'carbon' => 'Carbon',
                'composite' => 'Composite',
                'fiberglass' => 'Fiberglass',
                'grp' => 'GRP',
                'iroko' => 'Iroko',
                'mahogany' => 'Mahogany',
                'steel' => 'Steel',
                'wood' => 'Wood',
            ];
        });
    }

    /**
     * @return array
     */
    static public function getFuelType()
    {
        return [
            'diesel' => 'Diesel',
            'electric' => 'Electric',
            'gasoline' => 'Gas/petroleum',
            'other' => 'Other'
        ];
    }

    /**
     * @return array
     */
    static public function getNamePrefixes()
    {
        return [
            'M/Y' => 'Motor yacht',
            'S/Y' => 'Sail yacht',
        ];
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        $name = $this->getAttributeFromArray('name');
        if (empty($name)) {
            /** @deprecated Name is required and can't be empty */
            $name = "{$this->manufacturer->title}";
        }
        return $name;
    }

    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        $title = $this->name;

        if ($this->type == 'vessel') {
            $title = ($this->name_prefix ? "{$this->name_prefix} " : null) . $title;
        }

        return $title;
    }

    /**
     * @return string
     */
    public function getFuelTypeTitleAttribute()
    {
        $types = self::getFuelType();
        return $types[$this->getAttributeFromArray('fuel_type')] ?? '';
    }

    /**
     * @return string
     */
    public function getHullTypeTitleAttribute()
    {
        $types = self::getHullTypes();
        return $types[$this->getAttributeFromArray('hull_type')] ?? '';
    }

    /**
     * @return string
     */
    public function getFlagTitleAttribute()
    {
        $flags = Country::getAll();

        return $flags[strtoupper($this->flag)] ?? '';
    }

    /**
     * @return string
     */
    public function getRegisteredPortCityAttribute()
    {
        $city = '';
        if ($this->registered_port) {
            list($city) = explode(',', $this->registered_port);
        }

        return $city;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getFlagUrlAttribute()
    {
        if ($this->flag) {
            return asset('/assets/img/countries_flags/' . strtolower($this->flag) . '.png');
        }

        return '';
    }

    public function setOwnersAttribute($value)
    {
        $this->attributes['owners'] = is_array($value) ? json_encode(array_values($value)) : $value;
    }

    public function setStaffAttribute($value)
    {
        $this->attributes['staff'] = is_array($value) ? json_encode(array_values($value)) : $value;
    }

    /**
     * @return bool
     */
    public function isVessel()
    {
        return ($this->type == 'vessel');
    }

    /**
     * @return bool
     */
    public function isTender()
    {
        return ($this->type == 'tender');
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
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo(ClassifiedsManufacturer::class, 'manufacturer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function registeredLocation()
    {
        return $this->belongsTo(Geo::class, 'registered_location_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function locationHistory()
    {
        return $this->hasMany(LocationHistory::class, 'vessel_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function images()
    {
        return $this->hasMany(VesselsImage::class, 'vessel_id')->sorted();
    }

    /**
     * @param string $size
     * @return string
     */
    public function getThumb($size)
    {
        if ($this->images->count()) {
            return $this->images->first()->getThumb($size);
        }

        return str_replace('{size}', $size, config('app.placeholder_url'));
    }

    /**
     * @param File $file
     * @return VesselsImage
     * @throws \Throwable
     */
    public function attachImageFile(File $file)
    {
        $image = new VesselsImage();
        $image->vessel_id = $this->id;
        $image->file_id = $file->id;
        $image->saveOrFail();

        return $image;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function records()
    {
        return $this->hasMany(VesselsAttachment::class, 'vessel_id')->where('vessels_attachments.type', 'record')->sorted();
    }

    /**
     * @param array $access_mode
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function documents($access_mode = ['full', 'read'])
    {
        return $this->hasMany(VesselsAttachment::class, 'vessel_id')->where('vessels_attachments.type', 'document')->whereIn('vessels_attachments.access_mode', $access_mode);
    }

    /**
     * @param array $access_mode
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function myDocuments($access_mode = ['full', 'read'])
    {
        return $this->documents($access_mode)->where((new VesselsAttachment())->getTable() . '.user_id', Sentinel::getUser()->getUserId());
    }

    /**
     * @param array $types
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function publicAttachments()
    {
        return $this->hasMany(VesselsAttachment::class, 'vessel_id')->where('vessels_attachments.global_folder', 'public');
    }

    /**
     * @param array $types
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function attachments($types = ['document', 'record', 'video'])
    {
        return $this->hasMany(VesselsAttachment::class, 'vessel_id')->whereIn('vessels_attachments.type', $types);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function video()
    {
        return $this->hasOne(VesselsAttachment::class, 'vessel_id')->where('type', 'video');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function crew()
    {
        $memberTable = (new User())->getTable();
        $positionTable = (new Position())->getTable();
        $vesselsCrewTable = (new VesselsCrew())->getTable();

        return $this->hasMany(VesselsCrew::class, 'vessel_id')
            ->join($memberTable, $vesselsCrewTable . '.user_id', '=', $memberTable . '.id')
            ->leftJoin($positionTable, $memberTable . '.id', '=', $positionTable . '.id')
            ->orderBy($positionTable . '.order', 'desc')
            ->groupBy($vesselsCrewTable . '.id')
            ->select($vesselsCrewTable . '.*');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function crewMembers()
    {
        return $this->hasManyThrough(CrewMember::class, VesselsCrew::class, 'vessel_id', 'id', 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Vessel::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tenders()
    {
        return $this->hasMany(Vessel::class, 'parent_id', 'id');
    }

    public function scopePublished($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        return $query->where('owner_id', $user->getUserId());
    }

    /**
     * @param File $file
     * @param string $type
     * @param string $global_folder
     * @param int $user_id
     * @param string $access_mode
     * @return VesselsAttachment
     * @throws \Throwable
     */
    public function attachFile(File $file, $type, $global_folder = null, $user_id = null, $access_mode = 'full')
    {
        $user_id = $user_id ?? Sentinel::getUser()->getUserId();

        $attachment = new VesselsAttachment();
        $attachment->user_id = $user_id;
        $attachment->vessel_id = $this->id;
        $attachment->file_id = $file->id;
        $attachment->global_folder = $global_folder;
        $attachment->type = $type;
        $attachment->access_mode = $access_mode;
        $attachment->saveOrFail();

        return $attachment;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getPublicProfileLink($route = null, $params = [])
    {
        $params += ['boat_id' => $this->id];
        return route($route ? $route : 'account.' . $this->type . 's.profile', $params);
    }

    /**
     * @param int|array $ids
     * @return VesselsCrew
     * @throws \Throwable
     */
    public function attachMember($ids)
    {
        if (!is_array($ids)) {
            $ids = [(int)$ids];
        }

        foreach ($ids as $id) {
            $link = new VesselsCrew();
            $link->owner_id = $this->owner->id;
            $link->vessel_id = $this->id;
            $link->user_id = $id;
            $link->saveOrFail();
        }

        return $link;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $isNew = !boolval($this->id);

        $this->description = strip_tags($this->description, config('app.allowable_tags'));

        if (!$isNew) {
            if ($this->type == 'vessel') {
                MembersUpdate::dispatch($this->user)
                    ->onQueue('high');
            }
        }

        if ($this->getOriginal('address') && $this->getOriginal('address') != $this->getAttribute('address')) {
            // Boat location changed/specified
            $model = $this->locationHistory()->create([
                'address' => $this->getOriginal('address'),
                'map_lat' => $this->getOriginal('map_lat'),
                'map_lng' => $this->getOriginal('map_lng'),
            ]);
        }

        return parent::save($options);
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $result = parent::delete();
        if ($result) {
            if ($this->type == 'vessel') {
                $this->user->delete();
            }
        }

        return $result;
    }
}
