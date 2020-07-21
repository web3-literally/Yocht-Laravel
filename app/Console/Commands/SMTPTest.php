<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Mail;

class SMTPTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smtp:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SMTP server testing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $to = $this->ask('Enter a destination email address');

        if ($to) {
            Mail::raw($this->description, function (Message $message) use ($to) {
                $message->to($to, null, true);
            });
            $this->alert("Email was sent. Please, check your {$to} inbox.");
        }
    }
}
