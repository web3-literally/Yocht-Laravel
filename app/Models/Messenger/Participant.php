<?php

namespace App\Models\Messenger;

/**
 * Class Participant
 * @package App\Models\Messenger
 */
class Participant extends \Cmgmyr\Messenger\Models\Participant
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messenger_participants';
}