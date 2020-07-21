<?php

namespace App\Models\Business;

use App\Employee;
use App\File;
use App\Jobs\Index\MembersUpdate;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MadWeb\Seoable\Traits\SeoableTrait;
use App\User;
use Sentinel;

/**
 * Class Business
 * @package App\Models\Business
 */
class Business extends Model
{
    use SoftDeletes;
    use Sluggable;
    use SluggableScopeHelpers;
    use SeoableTrait;

    public $table = 'businesses';

    protected $dates = ['deleted_at'];

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        if ($this->business_type == 'marinas_shipyards') {
            return array_merge($this->fillable, [
                'established_year',
                'company_country',
                'vhf_channel',
                'hours_of_operation',
                'number_of_ships',
                'min_depth',
                'max_depth',
            ]);
        } elseif ($this->business_type == 'marine' || $this->business_type == 'land_services') {
            return array_merge($this->fillable, [
                'established_year',
                'company_country',
                'hours_of_operation',
            ]);
        }
        return $this->fillable;
    }

    /**
     * The attributes that are assignable.
     *
     * @var array
     */
    public $fillable = [
        'company_name',
        'company_email',
        'company_city',
        'company_address',
        'description',
        'company_phone',
        'company_phone_alt',
        'owners',
        'staff',

        'accepted_forms_of_payments',
        'credentials',
        'insurance',
        'honors_and_awards',
        'licenses_and_certificates',
        'restrictions',

        'company_website',
        'company_blog',
        'company_youtube',
        'company_pinterest',
        'company_twitter',
        'company_facebook',
        'company_linkedin',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'slug' => 'string',
        'user_id' => 'integer',
        'owner_id' => 'integer',
        'company_name' => 'string',
        'company_email' => 'string',
        'company_city' => 'string',
        'company_address' => 'string',
        'description' => 'string',
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
                'source' => ['user_id', 'company_name']
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
     * @return string
     */
    public function getNameAttribute()
    {
        $name = $this->getAttributeFromArray('company_name');

        return $name;
    }

    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        $title = $this->name;

        return $title;
    }

    /**
     * @param array|null $value
     */
    public function setOwnersAttribute($value)
    {
        $this->attributes['owners'] = is_array($value) ? json_encode(array_values($value)) : $value;
    }

    /**
     * @param array|null $value
     */
    public function setStaffAttribute($value)
    {
        $this->attributes['staff'] = is_array($value) ? json_encode(array_values($value)) : $value;
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function map_file()
    {
        return $this->hasOne(File::class, 'id', 'map_file_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function brochure_file()
    {
        return $this->hasOne(File::class, 'id', 'brochure_file_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, BusinessesEmployees::getModel()->getTable(), 'business_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function images()
    {
        return $this->hasMany(BusinessesImage::class, 'business_id')->sorted();
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
     * @return BusinessesImage
     * @throws \Throwable
     */
    public function attachImageFile(File $file)
    {
        $image = new BusinessesImage();
        $image->business_id = $this->id;
        $image->file_id = $file->id;
        $image->saveOrFail();

        return $image;
    }

    /**
     * @param File $file
     * @param string $type
     * @return BusinessesAttachment
     * @throws \Throwable
     */
    public function attachFile(File $file, $type)
    {
        $user_id = $user_id ?? Sentinel::getUser()->getUserId();

        $attachment = new BusinessesAttachment();
        $attachment->business_id = $this->id;
        $attachment->file_id = $file->id;
        $attachment->type = $type;
        $attachment->saveOrFail();

        return $attachment;
    }

    public function scopePublished($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeMy(Builder $query)
    {
        $user = Sentinel::getUser();
        return $query->where('owner_id', $user->getUserId());
    }

    /**
     * @param array $types
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function attachments($types = ['document', 'video'])
    {
        return $this->hasMany(BusinessesAttachment::class, 'business_id')->whereIn('businesses_attachments.type', $types);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function video()
    {
        return $this->hasOne(BusinessesAttachment::class, 'business_id')->where('businesses_attachments.type', 'video');
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
            MembersUpdate::dispatch($this->user)
                ->onQueue('high');
        }

        return parent::save($options);
    }
}
