<?php

namespace App\Mail\Jobs;

use App\Models\Jobs\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobCompleted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Job
     */
    protected $job;

    /**
     * Create a new message instance.
     *
     * @param Job $job
     * @return void
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->job->applicant->isBusinessAccount()) {
            $applicant = $this->job->applicant->parent;
        } else {
            $applicant = $this->job->applicant;
        }

        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($applicant->email, $applicant->full_name)
            ->subject($this->job->title . ' job completed')
            ->markdown('emails.emailTemplates.jobs.completed', [
                'job' => $this->job,
                'applicant' => $applicant
            ]);

        return $this;
    }
}
