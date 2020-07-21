<?php

namespace App;

use App\Models\Business\Business;
use App\Models\Classifieds\FavoriteClassified;
use App\Models\Events\FavoriteEvent;
use App\Models\Jobs\FavoriteJob;
use App\Models\Jobs\Job;
use App\Models\Jobs\JobApplications;
use App\Models\Members\FavoriteMember;
use App\Models\Messenger\Thread;
use App\Models\Reviews\Review;
use App\Models\ServiceArea;
use App\Models\Specialization;
use App\Models\Traits\ThumbTrait;
use App\Models\Vessels\Vessel;
use Carbon\Carbon;
use App\Models\Messenger\Message;
use HighIdeas\UsersOnline\Traits\UsersOnlineTrait;
use Cviebrock\EloquentTaggable\Taggable;
use Igaster\LaravelCities\Geo;
use Intervention\Image\Facades\Image;
use Cmgmyr\Messenger\Traits\Messagable;
use Amsgames\LaravelShop\Traits\ShopUserTrait;
use Illuminate\Notifications\Notifiable;
use Sentinel;
use Cache;
use File;
use DB;

class User extends SearchableUser
{
    use UsersOnlineTrait;
    use Notifiable;
    use Messagable;
    use ShopUserTrait;
    use Taggable;
    use ThumbTrait;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        if ($this->isBoatAccount()) {
            return $this->profile->name ?? '-';
        }
        if ($this->isBusinessAccount()) {
            return $this->profile->company_name ?? '-';
        }

        return str_limit(trim($this->first_name . ' ' . $this->last_name), 30);
    }

    /**
     * @return string
     */
    public function getMemberTitleAttribute()
    {
        if ($this->isBoatAccount()) {
            return $this->profile->title ?? '-';
        }

        return $this->full_name;
    }

    /**
     * @return string
     */
    public function getAccountTypeTitleAttribute()
    {
        return $this->getAccountRole() ? $this->getAccountRole()->name : 'None';
    }

    /**
     * @return string
     */
    public function getFullAddressAttribute()
    {
        if ($this->isBoatAccount()) {
            return $this->profile->address;
        }
        if ($this->isBusinessAccount()) {
            return $this->profile->company_address;
        }

        $parts = [];
        if ($this->address) {
            $parts[] = $this->address;
        }
        if ($this->city) {
            $parts[] = $this->city;
        }
        if ($this->user_state) {
            $parts[] = $this->user_state;
        }
        if ($this->country) {
            $parts[] = $this->country;
        }
        return implode(', ', $parts);
    }

    /**
     * @return string
     */
    public function getCountryLabelAttribute()
    {
        if ($this->country) {
            $country = Geo::getCountry($this->country);
            return $country ? $country->name : '';
        }

        return '';
    }

    /**
     * @return string
     */
    public function getFullPhoneAttribute()
    {
        if ($this->isMemberMarineAccount()) {
            return '';
        }

        return $this->phone;
    }

    /**
     * @return string
     */
    public function getListingColorAttribute()
    {
        return $this->profile->color;
    }

    public function specialization()
    {
        return $this->hasOne(Specialization::class, 'id', 'specialization_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @param bool $save
     * @return $this
     * @throws \Throwable
     */
    public function deleteImage($save = true)
    {
        if ($this->hasPic()) {
            $model = $this;

            $destinationPath = public_path() . '/uploads/users/';

            $folders = File::glob($destinationPath . '*', GLOB_ONLYDIR);
            if ($folders) {
                foreach ($folders as $folder) {
                    $filePath = $destinationPath . basename($folder) . '/' . $model->pic;
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
            }
            $filePath = $destinationPath . $model->pic;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $model->pic = null;
            if ($save) {
                $model->saveOrFail();
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPic()
    {
        return (bool)$this->pic;
    }

    /**
     * @return bool
     */
    public function hasVesselPic()
    {
        return (bool)$this->profile->images->count();
    }

    /**
     * @return bool
     */
    public function hasBusinessPic()
    {
        return (bool)$this->profile->images->count();
    }

    /**
     * @param $size
     * @return bool|string
     */
    public function getThumb($size)
    {
        if ($this->isBoatAccount() && $this->hasVesselPic()) {
            return $this->profile->getThumb($size);
        } elseif ($this->isBusinessAccount() && $this->hasBusinessPic()) {
            return $this->profile->getThumb($size);
        }

        return $this->getProfileThumb($size);
    }

    /**
     * @param $size
     * @return string
     */
    public function getProfileThumb($size)
    {
        /*$sourceImage = '/assets/images/authors/no_avatar.jpg';*/
        if ($this->hasPic()) {
            $sourceImage = '/uploads/users/' . $this->pic;
        } else {
            /*if ($this->gender === 'male') {
                $sourceImage = '/assets/images/authors/avatar3.png';
            }
            if ($this->gender === 'female') {
                $sourceImage = '/assets/images/authors/avatar5.png';
            }*/
            return str_replace('{size}', $size, config('app.placeholder_url'));
        }

        $sourceFolder = dirname($sourceImage) . '/';
        $sourceFileName = basename($sourceImage);

        $size = strtolower($size);
        try {
            if ($size) {
                $sourcepath = public_path() . $sourceImage;
                $path = public_path() . $sourceFolder . $size . '/';
                $filepath = $path . $sourceFileName;
                if (!file_exists($filepath)) {
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    list($width, $height) = explode('x', $size);
                    Image::make($sourcepath)->fit($width, $height)->save($filepath);
                }
                return asset($sourceFolder . $size . '/' . $sourceFileName);
            } else {
                return asset($sourceFolder . $sourceFileName);
            }
        } catch (\Throwable $e) {
            /*$sourceImage = '/assets/images/authors/no_avatar.jpg';
            $sourceFolder = dirname($sourceImage) . '/';
            $sourceFileName = basename($sourceImage);*/
        }

        /*return asset($sourceFolder . $sourceFileName);*/
        return str_replace('{size}', $size, config('app.placeholder_url'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favoriteClassifieds()
    {
        return $this->hasMany(FavoriteClassified::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favoriteJobs()
    {
        return $this->hasMany(FavoriteJob::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favoriteEvents()
    {
        return $this->hasMany(FavoriteEvent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favoriteMembers()
    {
        return $this->hasMany(FavoriteMember::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favoriteVessels()
    {
        $favoriteMember = (new FavoriteMember())->getTable();
        $roleUserTable = 'role_users';
        $roleTable = (new Role())->getTable();
        return $this->favoriteMembers()->join($roleUserTable, $roleUserTable . '.user_id', '=', $favoriteMember . '.member_id')->join($roleTable, $roleTable . '.id', '=', $roleUserTable . '.role_id')
            ->where($roleTable . '.slug', 'vessel')->select($favoriteMember . '.*');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favoriteBusinesses()
    {
        $favoriteMember = (new FavoriteMember())->getTable();
        $roleUserTable = 'role_users';
        $roleTable = (new Role())->getTable();
        return $this->favoriteMembers()->join($roleUserTable, $roleUserTable . '.user_id', '=', $favoriteMember . '.member_id')->join($roleTable, $roleTable . '.id', '=', $roleUserTable . '.role_id')
            ->where($roleTable . '.slug', 'business')->select($favoriteMember . '.*');
    }

    /**
     * @return mixed
     */
    public function getUnreadJobApplicationsAttribute()
    {
        return $this->unreadJobApplications();
    }

    /**
     * @return mixed
     */
    public function unreadJobApplications()
    {
        $jobTable = (new Job())->getTable();
        $jobApplicationsTable = (new JobApplications())->getTable();
        return JobApplications::whereNull('read_at')
            ->join($jobTable, $jobApplicationsTable . '.job_id', '=', $jobTable . '.id')
            ->where($jobTable . '.user_id', $this->id)
            ->where('status', Job::STATUS_PUBLISHED)
            ->get();
    }

    /**
     * @return mixed
     */
    public function inProgressJobs()
    {
        return Job::my()->whereIn('status', [Job::STATUS_PUBLISHED, Job::STATUS_IN_PROCESS])->get();
    }

    public function scopeSubscribedMembers($query)
    {
        $userTable = (new User())->getTable();
        $roleUserTable = 'role_users';
        $roleTable = (new Role())->getTable();
        return $query->join($roleUserTable, $roleUserTable . '.user_id', '=', $userTable . '.id')->join($roleTable, $roleTable . '.id', '=', $roleUserTable . '.role_id')
            ->where(function ($query) use ($roleTable) {
                $query->whereIn($roleTable . '.slug', ['owner', 'marine'])
                    ->whereHas('subscriptions', function ($query) {
                        $query->whereNested(function ($query) {
                            $query->where('cancelled', 0)
                                ->where('name', 'Membership')// name of subscription
                                ->whereNull('ends_at')
                                ->orWhere('ends_at', '>', Carbon::now())
                                ->orWhereNotNull('trial_ends_at')
                                ->where('trial_ends_at', '>', Carbon::today());
                        });
                    });
            })
            ->orWhere($roleTable . '.slug', 'user')
            ->select($userTable . '.*');
    }

    /**
     * @param $query
     * @param array $members
     * @return mixed
     */
    public function scopeMembers($query, $members = ['user', 'land_services', 'owner', 'marine', 'captain'])
    {
        $userTable = (new User())->getTable();
        $roleUserTable = 'role_users';
        $roleTable = (new Role())->getTable();
        return $query->join($roleUserTable, $roleUserTable . '.user_id', '=', $userTable . '.id')->join($roleTable, $roleTable . '.id', '=', $roleUserTable . '.role_id')
            ->whereIn($roleTable . '.slug', $members)
            ->select($userTable . '.*');
    }

    /**
     * @return mixed
     */
    public function scopeChildAccounts($query)
    {
        return $query->where('parent_id', Sentinel::getUser()->getUserId());
    }

    /**
     * @return mixed
     */
    public function scopeCrewAccounts($query)
    {
        $userTable = (new User())->getTable();
        $roleUserTable = 'role_users';
        $roleTable = (new Role())->getTable();
        return $query->join($roleUserTable, $roleUserTable . '.user_id', '=', $userTable . '.id')->join($roleTable, $roleTable . '.id', '=', $roleUserTable . '.role_id')
            ->whereIn($roleTable . '.slug', CrewMember::CREW_ROLES)
            ->select($userTable . '.*');
    }

    /**
     * @return bool
     */
    public function hasVessel()
    {
        return boolval($this->vessels->count());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vessels()
    {
        return $this->hasMany(Vessel::class, 'owner_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function primaryVessel()
    {
        return $this->hasOne(Vessel::class, 'owner_id', 'id')->where('is_primary', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function crew()
    {
        $userTable = (new CrewMember())->getTable();
        $roleUserTable = 'role_users';
        $roleTable = (new Role())->getTable();

        return $this->hasMany(CrewMember::class, 'parent_id', 'id')
            ->join($roleUserTable, $roleUserTable . '.user_id', '=', $userTable . '.id')
            ->join($roleTable, $roleTable . '.id', '=', $roleUserTable . '.role_id')
            ->whereIn($roleTable . '.slug', CrewMember::CREW_ROLES)
            ->groupBy($userTable . '.id')
            ->select($userTable . '.*');
    }

    /**
     * @return bool
     */
    public function hasBusiness()
    {
        return boolval($this->businesses->count());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function businesses()
    {
        return $this->hasMany(Business::class, 'owner_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function primaryBusiness()
    {
        return $this->hasOne(Business::class, 'owner_id', 'id')->where('is_primary', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    /**
     * Returns the new messages for user.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function unreadThreads($limit = 10)
    {
        $ids = Message::unreadForUser($this->id)
            ->join('messenger_threads', 'messenger_threads.id', '=', 'messenger_messages.thread_id')
            ->where('messenger_threads.subject', 'not like', 'Ticket%')
            ->groupBy('messenger_threads.id')
            ->select('messenger_threads.id')
            ->pluck('messenger_threads.id')
            ->all();

        return Thread::whereIn('id', $ids)->limit($limit)->get();
    }

    /**
     * Returns the new messages count for user.
     *
     * @return int
     */
    public function unreadMessagesCount()
    {
        return Message::unreadForUser($this->id)
            ->join('messenger_threads', 'messenger_threads.id', '=', 'messenger_messages.thread_id')
            ->where('messenger_threads.subject', 'not like', 'Ticket%')
            ->groupBy('messenger_threads.id')
            ->select('messenger_threads.id')
            ->get()
            ->count();
    }

    /**
     * @return int
     */
    public function age()
    {
        return $this->created_at->diff(Carbon::now())->format('%y');
    }

    /**
     * @return int
     */
    public function level()
    {
        if ($this->isBusinessAccount() && $this->profile->established_year) {
            $age = Carbon::now()->year - $this->profile->established_year;
        } else {
            $age = $this->age();
        }
        if ($age < 4) {
            return 1;
        } elseif ($age >= 4 && $age < 8) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * @return float|null
     */
    public function rating()
    {
        return Cache::remember('MemberAVGRating' . $this->id, 60, function () {
            $row = Review::leftJoin('reviews_for', 'reviews.id', '=', 'reviews_for.review_id')
                ->where('reviews.status', Review::STATUS_APPROVED)
                ->where('reviews_for.for', 'member')
                ->where('reviews_for.instance_id', $this->id)
                ->groupBy('reviews_for.instance_id')
                ->select(DB::raw('AVG(reviews.rating) AS avg_rating'))
                ->get()->first();
            if (!$row) {
                return null;
            }

            return round($row->avg_rating, 1);
        });
    }

    /**
     * Rate used for member sorting
     * @return float|null
     */
    public function getRateAttribute()
    {
        return (int)$this->rating();
    }

    /**
     * @param array $params
     * @return string|null
     */
    public function getPublicProfileLink($params = [])
    {
        $params += ['id' => $this->id];

        if ($this->isBusinessAccount()) {
            return route('members.business.show', $params);
        }

        if ($this->isVesselAccount()) {
            return route('members.vessel.show', $params);
        }

        return null;
    }
}
