<?php

namespace App;

use App\Models\Business\Business;
use App\Models\ServiceArea;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Models\Vessels\Vessel;
use Braintree\Customer as BraintreeCustomer;
use Cartalyst\Sentinel\Users\EloquentUser;
use Exception;
use Igaster\LaravelCities\Geo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Laravel\Cashier\Billable;
use Spatie\Activitylog\Models\Activity;

class ExtendedUser extends EloquentUser
{
    use Billable;
    use SoftDeletes;

    protected $fillable = [];

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token', 'subscriptions'];

    protected $dates = ['deleted_at'];

    /**
     * @var null|string
     */
    protected $account_type = null;

    /**
     * @var null|Role
     */
    protected $account_role = null;

    /**
     * @var null|bool
     */
    protected $has_membership = null;

    /**
     * @return string
     */
    public function getAccountType()
    {
        if (is_null($this->account_type)) {
            $role = $this->getAccountRole();
            $this->account_type = $role ? $this->getAccountRole()->getRoleSlug() : 'none';
        }

        return $this->account_type;
    }

    /**
     * @return Role|null
     */
    public function getAccountRole()
    {
        if (is_null($this->account_role)) {
            $roles = $this->getRoles();
            if (is_array($roles)) {
                $account_role = current($roles);
                $this->account_role = Role::find($account_role['id']);
            } else {
                $this->account_role = $roles->getIterator()->current();
            }
        }

        return $this->account_role;
    }

    /**
     * @return bool
     */
    public function isFreeAccount()
    {
        return in_array($this->getAccountType(), ['user', 'land_services', 'marinas_shipyards', 'crew']);
    }

    /**
     * @return bool
     */
    public function isMemberAccount()
    {
        return in_array($this->getAccountType(), ['owner', 'marine']);
    }

    /**
     * @return bool
     */
    public function isMemberOwnerAccount()
    {
        return $this->getAccountType() == 'owner';
    }

    /**
     * @return bool
     */
    public function isMemberMarineAccount()
    {
        return $this->getAccountType() == 'marine';
    }

    /**
     * @return bool
     */
    public function isMemberMarinasShipyards()
    {
        return $this->getAccountType() == 'marinas_shipyards';
    }

    /**
     * @return bool
     */
    public function isLandServicesAccount()
    {
        return $this->getAccountType() == 'land_services';
    }

    /**
     * @return bool
     */
    public function isManagerAccount()
    {
        return $this->getAccountType() == 'manager';
    }

    /**
     * @return bool
     */
    public function isVesselAccount()
    {
        return $this->getAccountType() == 'vessel';
    }

    /**
     * @return bool
     */
    public function isTenderAccount()
    {
        return $this->getAccountType() == 'tender';
    }

    /**
     * @return bool
     */
    public function isBoatAccount()
    {
        $role = $this->getAccountType();
        return $role == 'vessel' || $role == 'tender';
    }

    /**
     * @return bool
     */
    public function isBusinessAccount()
    {
        return $this->getAccountType() == 'business';
    }

    /**
     * @return bool
     */
    public function isCaptainAccount()
    {
        return $this->getAccountType() == 'captain';
    }

    /**
     * @return bool
     */
    public function isCrewAccount()
    {
        return $this->getAccountType() == 'crew';
    }

    /**
     * @return CrewMember
     */
    public function asCrewMember()
    {
        $member = new CrewMember();
        $member->setRawAttributes($this->getAttributes());

        return $member;
    }

    /**
     * If has active subscription
     *
     * @return bool
     */
    public function hasMembership()
    {
        if (is_null($this->has_membership)) {
            $this->has_membership = $this->subscribed('Membership');
        }

        return $this->has_membership;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        if ($this->isBoatAccount()) {
            return $this->hasOne(Vessel::class, 'user_id', 'id')->withTrashed();
        }
        if ($this->isBusinessAccount()) {
            return $this->hasOne(Business::class, 'user_id', 'id')->withTrashed();
        }

        return $this->hasOne(Profile::class, 'user_id', 'id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, UserCategories::getModel()->getTable(), 'user_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, UserServices::getModel()->getTable(), 'user_id', 'service_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service_areas()
    {
        return $this->hasMany(ServiceArea::class);
    }

    /**
     * Email's confirmations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailConfirmations()
    {
        return $this->hasMany(EmailConfirmations::class, 'user_id', 'id');
    }

    /**
     * Latest email's confirmations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function latestEmailConfirmation()
    {
        return $this->hasMany(EmailConfirmations::class, 'user_id', 'id')->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestLogin()
    {
        return $this->hasOne(Activity::class, 'subject_id', 'id')->where('description', 'LoggedIn')->where('subject_type', 'App\User')->orderByDesc('created_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestLogout()
    {
        return $this->hasOne(Activity::class, 'subject_id', 'id')->where('description', 'LoggedOut')->where('subject_type', 'App\User')->orderByDesc('created_at');
    }

    /**
     * Create a Braintree customer for the given model.
     *
     * @param array $options
     * @return \Braintree\Customer
     * @throws \Exception
     */
    public function createAsBraintreeCustomer(array $options = [])
    {
        $response = BraintreeCustomer::create(
            array_replace_recursive([
                'firstName' => Arr::get(explode(' ', $this->name), 0),
                'lastName' => Arr::get(explode(' ', $this->name), 1),
                'email' => $this->email
            ], $options)
        );

        if (!$response->success) {
            throw new Exception('Unable to create Braintree customer: ' . $response->message);
        }

        $this->braintree_id = $response->customer->id;

        return $response->customer;
    }

    /**
     * Delete a Braintree customer of the given model.
     *
     * @return \Braintree\Result\Successful
     * @throws \Exception
     */
    public function deleteBraintreeCustomer()
    {
        $response = BraintreeCustomer::delete($this->asBraintreeCustomer()->id);

        if (!$response->success) {
            throw new Exception('Unable to delete Braintree customer: ' . $response->message);
        }

        $this->braintree_id = null;

        return $response;
    }
}
