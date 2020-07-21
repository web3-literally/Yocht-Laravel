<?php

namespace App\Mail\Jobs;

use App\Models\Jobs\Job;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PrivateInvitation
 * @package App\Mail\Jobs
 */
class PrivateInvitation extends Mailable implements ShouldQueue
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
        $target = $this->member->parent;

        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($target->email, $target->full_name)
            ->subject('Job ' . $this->job->title . ' private invitation on ' . config('app.name'))
            ->markdown('emails.emailTemplates.jobs.private-invitation', [
                'job' => $this->job,
                'member' => $this->member
            ]);

        return $this;
    }
}
