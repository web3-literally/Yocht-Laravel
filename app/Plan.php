<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sentinel;

/**
 * Class Plan
 * @package App
 */
class Plan extends Model
{
    /**
     * @var string
     */
    protected $table = 'plans';

    /**
     * @var array
     */
    protected $fillable = ['slug'];

    /**
     * Update the creation and update timestamps.
     *
     * @return void
     */
    protected function updateTimestamps()
    {
        // Used created_at and updated_at from the source
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeForCurrentMember($query)
    {
        $accountType = Sentinel::getUser()->getAccountType();

        return $query->where('slug', 'like', "%{$accountType}%");
    }
}
