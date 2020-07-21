<?php

namespace App\Models\Jobs;

use App\File;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Sentinel;

/**
 * Class JobApplicationAttachments
 * @package App\Models\Jobs
 */
class JobApplicationAttachments extends Model
{
    public $timestamps = false;

    /**
     * @var string
     */
    public $table = 'job_applications_attachments';

    /**
     * @var array
     */
    public $fillable = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be casted to date type.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * @return bool
     */
    public function isMine()
    {
        return (Sentinel::getUser()->getUserId() == $this->owner_id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function application()
    {
        return $this->belongsTo(JobApplications::class, 'application_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        return $this->file->cleanup() && parent::delete() && $this->file->delete(false);
    }
}