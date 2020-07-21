<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaskShare
 * @package App\Models\Tasks
 */
class TaskShare extends Model
{
    public $timestamps = false;

    public $table = 'task_share';

    public $fillable = [];

    protected $casts = [];
}