<?php

namespace App\Mail\Messenger;

use App\Models\Messenger\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Thread
     */
    public $thread;

    /**
     * @var null|string
     */
    protected $message = '';

    /**
     * Create a new message instance.
     *
     * @param Thread $thread
     * @param null|string $message
     * @return void
     */
    public function __construct(Thread $thread, $message = null)
    {
        $this->thread = $thread;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->thread->toUser()->email, $this->thread->toUser()->full_name)
            ->subject('You have a new message from ' . $this->thread->fromUser()->full_name)
            ->markdown('emails.emailTemplates.messenger.new-message', [
                'thread' => $this->thread,
                'message' => $this->message,
            ]);

        return $this;
    }
}
