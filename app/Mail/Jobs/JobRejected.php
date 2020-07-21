<?php

namespace App\Mail\Jobs;

use App\Models\Jobs\JobApplications;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var JobApplications
     */
    protected $application;

    /**
     * @var User
     */
    protected $contractor;

    /**
     * Create a new message instance.
     *
     * @param JobApplications $application
     * @param User $contractor
     * @return void
     */
    public function __construct(JobApplications $application, User $contractor)
    {
        $this->application = $application;
        $this->contractor = $contractor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->contractor->email, $this->contractor->full_name)
            ->subject('Rejected from ' . $this->application->job->title . ' job')
            ->markdown('emails.emailTemplates.jobs.rejected', [
                'application' => $this->application,
                'contractor' => $this->contractor
            ]);

        return $this;
    }
}
