<?php

namespace App;

use Cartalyst\Sentinel\Activations\EloquentActivation;

class EmailConfirmations extends EloquentActivation
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'email_confirmations';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('completed', 0);
    }
}