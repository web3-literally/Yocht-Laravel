<?php

namespace App\Models\Business;

use App\Employee;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Employees
 * @package App\Models\Classifieds
 */
class BusinessesEmployees extends Model
{
    public $timestamps = false;

    public $table = 'businesses_employees';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'owner_id' => 'int',
        'business_id' => 'int',
        'user_id' => 'int'
    ];

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
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }
}
