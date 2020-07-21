<?php

return [

    'user_model' => App\User::class,

    'message_model' => App\Models\Messenger\Message::class,

    'participant_model' => App\Models\Messenger\Participant::class,

    'thread_model' => App\Models\Messenger\Thread::class,

    'messages_table' => 'messenger_messages',

    'participants_table' => 'messenger_participant',

    'threads_table' => 'messenger_threads',
];
