<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;


class NewsSource extends Model
{
    use SoftDeletes;

    public $table = 'news_sources';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'url'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'url' => 'string'
    ];
}
