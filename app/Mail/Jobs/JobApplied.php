<?php

namespace App\Mail\Jobs;

use App\Models\Jobs\JobApplications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplied extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var JobApplications
     */
    protected $application;

    /**
     * Create a new message instance.
     *
     * @param JobApplications $application
     * @return void
     */
    public function __construct(JobApplications $application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->application->job->applicant->email, $this->application->job->applicant->full_name)
            ->subject('Applied to ' . $this->application->job->title . ' job')
            ->markdown('emails.emailTemplates.jobs.applied', [
                'application' => $this->application
            ]);

        return $this;
    }
}
