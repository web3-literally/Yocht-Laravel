<?php

namespace App\Helpers;

use App\Models\Jobs\JobApplications;
use App\Models\Messenger\Message;
use App\Models\Messenger\Participant;
use App\Models\Messenger\Thread;
use Carbon\Carbon;
use Sentinel;

class ApplicantConversation
{
    /**
     * @param JobApplications $applicant
     * @param string $message
     * @return mixed
     * @throws \Throwable
     */
    public static function create(JobApplications $applicant, $message)
    {
        $thread = new Thread();
        $thread->subject = 'Ticket #' . $applicant->ticket_id;
        $thread->saveOrFail();

        $thread->activateAllParticipants();

        $message = strip_tags($message);

        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Sentinel::getUser()->getUserId(),
            'body' => $message,
        ]);

        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => Sentinel::getUser()->getUserId(),
        ]);
        //$participant->last_read = new Carbon;
        $participant->saveOrFail();

        // Recipient
        $thread->addParticipant($applicant->job->user_id);

        return $thread->id;
    }
}
