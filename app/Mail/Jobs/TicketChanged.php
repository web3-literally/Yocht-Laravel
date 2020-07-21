<?php

namespace App\Mail\Jobs;

use App\Models\Jobs\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketChanged extends Mailable implements ShouldQueue
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
        $target = $this->job->applicant->parent;

        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($target->email, $target->full_name)
            ->subject('Ticket #' . $this->job->ticket->id . ' changed')
            ->markdown('emails.emailTemplates.jobs.ticket-changed', [
                'job' => $this->job,
                'applicant' => $this->job->applicant,
            ]);

        return $this;
    }
}
