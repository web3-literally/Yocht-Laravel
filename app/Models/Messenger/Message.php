<?php

namespace App\Models\Messenger;

/**
 * Class Message
 * @package App\Models\Messenger
 */
class Message extends \Cmgmyr\Messenger\Models\Message
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messenger_messages';
}