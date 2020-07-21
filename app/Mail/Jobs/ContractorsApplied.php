<?php

namespace App\Mail\Jobs;

use App\Models\Jobs\Job;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ContractorsApplied
 * @package App\Mail\Jobs
 *
 * @deprecated
 */
class ContractorsApplied extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @var User
     */
    protected $member;

    /**
     * Create a new message instance.
     *
     * @param Job $job
     * @param User $member
     * @return void
     */
    public function __construct(Job $job, User $member)
    {
        $this->job = $job;
        $this->member = $member;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->member->email, $this->member->full_name)
            ->subject('Applied to ' . $this->job->title . ' job')
            ->markdown('emails.emailTemplates.jobs.contractors-applied', [
                'job' => $this->job
            ]);

        return $this;
    }
}
