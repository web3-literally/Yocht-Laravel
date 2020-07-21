<?php

namespace App;

use App\Helpers\Crew;
use App\Helpers\Geocoding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Profile
 * @package App
 */
class Profile extends Model
{
    use SoftDeletes;

    protected $table = 'user_profile';

    protected $guarded = ['id'];

    protected $fillable = [
        'alt_phone',
        //'personal_quote',
        //'established_year',
        'experience',
        //'hours_of_operation',
        //'accepted_forms_of_payments',
        //'credentials',
        //'honors_and_awards',
        //'about',
        'link_website',
        'link_blog',
        'link_youtube',
        'link_pinterest',
        'link_twitter',
        'link_facebook',
        'link_linkedin',
        'link_google_plus',
        'link_instagram',
        'captain_first_name',
        'captain_last_name',
        'captain_email',
        'captain_phone',
        'company_name',
        'company_email',
        'company_country',
        'company_state', /** @deprecated  */
        'company_city',
        'company_address',
        'vhf_channel',
        'number_of_ships',
        'min_depth',
        'max_depth',
        /*'owners',
        'staff',*/
    ];

    protected $casts = [
        'owners' => 'array',
        'staff' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    /*public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }*/

    /*public function map_file()
    {
        return $this->hasOne(File::class, 'id', 'map_file_id');
    }*/

    /**
     * @param string $value
     * @return string
     */
    static protected function prepareLink($value) {
        if ($value) {
            return preg_replace('/^(?!https?:\/\/)/', 'http://', $value);
        }

        return '';
    }

    public function setLinkWebsiteAttribute($value) {
        $this->attributes['link_website'] = self::prepareLink($value);
    }

    public function setLinkBlogAttribute($value) {
        $this->attributes['link_blog'] = self::prepareLink($value);
    }

    public function setLinkYoutubeAttribute($value) {
        $this->attributes['link_youtube'] = self::prepareLink($value);
    }

    public function setLinkPinterestAttribute($value) {
        $this->attributes['link_pinterest'] = self::prepareLink($value);
    }

    public function setLinkTwitterAttribute($value) {
        $this->attributes['link_twitter'] = self::prepareLink($value);
    }

    public function setLinkFacebookAttribute($value) {
        $this->attributes['link_facebook'] = self::prepareLink($value);
    }

    public function setLinkLinkedinAttribute($value) {
        $this->attributes['link_linkedin'] = self::prepareLink($value);
    }

    public function setLinkGooglePlusAttribute($value) {
        $this->attributes['link_google_plus'] = self::prepareLink($value);
    }

    public function setLinkInstagramAttribute($value) {
        $this->attributes['link_instagram'] = self::prepareLink($value);
    }

    /*public function setHoursOfOperationAttribute($value) {
        $this->attributes['hours_of_operation'] = strip_tags($value);
    }*/

    /*public function setAcceptedFormsOfPaymentsAttribute($value) {
        $this->attributes['accepted_forms_of_payments'] = strip_tags($value);
    }*/

    /*public function setCredentialsAttribute($value) {
        $this->attributes['credentials'] = strip_tags($value);
    }*/

    /*public function setHonorsAndAwardsAttribute($value) {
        $this->attributes['honors_and_awards'] = strip_tags($value);
    }*/

    public function setOwnersAttribute($value)
    {
        $this->attributes['owners'] = is_array($value) ? json_encode(array_values($value)) : $value;
    }

    public function setStaffAttribute($value)
    {
        $this->attributes['staff'] = is_array($value) ? json_encode(array_values($value)) : $value;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function attachments()
    {
        return $this->hasMany(ProfileAttachment::class, 'profile_id')->sorted();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function video()
    {
        return $this->hasOne(ProfileAttachment::class, 'profile_id')->where('type', 'video');
    }

    /**
     * @param File $file
     * @param string $type
     * @return ProfileAttachment
     * @throws \Throwable
     */
    /*public function attachFile(File $file, $type)
    {
        $attachment = new ProfileAttachment();
        $attachment->profile_id = $this->id;
        $attachment->file_id = $file->id;
        $attachment->type = $type;
        $attachment->saveOrFail();

        return $attachment;
    }*/

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (!in_array(strtolower($this->color), array_keys(Crew::colors()))) {
            $this->color = 'transparent';
        }

        if ($this->getOriginal('company_address') != $this->getAttribute('company_address')) {
            if ($this->getAttribute('company_address')) {
                $address = trim("{$this->getAttribute('company_address')} {$this->getAttribute('company_city')} {$this->getAttribute('company_state')} {$this->getAttribute('company_country')}");
                $response = Geocoding::latlngLookup($address);
                if ($response && $response->status === 'OK') {
                    if ($response->results) {
                        $place = current($response->results);
                        $this->company_map_lat = $place->geometry->location->lat;
                        $this->company_map_lng = $place->geometry->location->lng;
                    }
                }
            } else {
                $this->company_map_lat = null;
                $this->company_map_lng = null;
            }
        }

        $companyNameChanged = $this->getOriginal('company_name') != $this->getAttribute('company_name');

        $result = parent::save($options);

        if ($companyNameChanged) {
            User::where('id', $this->user_id)->update(['company_name' => $this->company_name]);
        }

        return $result;
    }
}
