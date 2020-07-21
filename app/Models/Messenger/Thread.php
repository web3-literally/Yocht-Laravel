<?php

namespace App\Models\Messenger;

use App\Models\Classifieds\ClassifiedsMessenger;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Sentinel;

/**
 * Class Thread
 * @package App\Models\Messenger
 */
class Thread extends \Cmgmyr\Messenger\Models\Thread
{
    const DIRECT_SUBJECT = 'Direct Connection';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messenger_threads';

    /**
     * Generates a string of participant information.
     *
     * @param null|int $userId
     * @param array $columns
     *
     * @return string
     */
    public function participantsString($userId = null, $columns = ['first_name', 'last_name'])
    {
        return parent::participantsString($userId, $columns);
    }

    /**
     * Returns the user object that created the thread.
     *
     * @return Models::user()
     */
    public function creator()
    {
        if (is_null($this->creatorCache)) {
            $firstMessage = $this->conversations()->withTrashed()->oldest()->first();
            $this->creatorCache = $firstMessage ? $firstMessage->user : Models::user();
        }

        return $this->creatorCache;
    }

    /**
     * @return Models::user()
     */
    public function directUser()
    {
        $userId = Sentinel::getUser()->getUserId();
        if ($this->creator()->id == $userId) {
            return $this->participants()->orderBy('id', 'desc')->first()->user;
        }

        return $this->creator();
    }

    /**
     * @return Models::user()
     */
    public function fromUser()
    {
        return $this->creator();
    }

    /**
     * @return User
     */
    public function toUser()
    {
        return $this->participants()->orderBy('id', 'desc')->first()->user;
    }

    /**
     * Messages relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @codeCoverageIgnore
     */
    public function messages()
    {
        return $this->hasMany(Models::classname(Message::class), 'thread_id', 'id')
            ->join('messenger_threads', 'messenger_threads.id', '=', 'messenger_messages.thread_id')
            ->where('subject', 'not like', 'Ticket%')
            ->groupBy('messenger_messages.id')
            ->select('messenger_messages.*');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classified()
    {
        return $this->belongsTo(ClassifiedsMessenger::class, 'id', 'thread_id');
    }

    /**
     * All Messages relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @codeCoverageIgnore
     */
    public function conversations()
    {
        return $this->hasMany(Models::classname(Message::class), 'thread_id', 'id')
            ->join('messenger_threads', 'messenger_threads.id', '=', 'messenger_messages.thread_id')
            ->groupBy('messenger_messages.id')
            ->select('messenger_messages.*');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeMy(Builder $query)
    {
        $userId = Sentinel::getUser()->getUserId();

        $threadsTable = Models::table('threads');
        $messagesTable = Models::table('messages');
        $participantsTable = Models::table('participants');

        return $query->join($participantsTable, $this->getQualifiedKeyName(), '=', $participantsTable . '.thread_id')
            ->join($messagesTable, $this->getQualifiedKeyName(), '=', $messagesTable . '.thread_id')
            ->where($participantsTable . '.user_id', $userId)
            ->where($threadsTable . '.subject', 'not like', 'Ticket%')
            ->groupBy($threadsTable . '.id')
            ->select([$threadsTable . '.*', 'messenger_messages.updated_at AS latest_order']);
    }

    public function scopeMyTo(Builder $query, $toId)
    {
        $fromId = Sentinel::getUser()->getUserId();

        $threadsTable = Models::table('threads');
        $messagesTable = Models::table('messages');
        $participantsTable = Models::table('participants');

        return $query->join($participantsTable, $this->getQualifiedKeyName(), '=', $participantsTable . '.thread_id')
            ->join($messagesTable, $this->getQualifiedKeyName(), '=', $messagesTable . '.thread_id')
            ->where(function ($query) use ($participantsTable, $messagesTable, $fromId, $toId) {
                $query->where(function ($query) use ($participantsTable, $messagesTable, $fromId, $toId) {
                    $query->where($participantsTable . '.user_id', $fromId)
                        ->where($messagesTable . '.user_id', $toId);
                })->orWhere(function ($query) use ($participantsTable, $messagesTable, $fromId, $toId) {
                    $query->where($participantsTable . '.user_id', $toId)
                        ->where($messagesTable . '.user_id', $fromId);
                });
            })
            ->where($threadsTable . '.subject', self::DIRECT_SUBJECT)
            ->groupBy($threadsTable . '.id')
            ->select([$threadsTable . '.*', 'messenger_messages.updated_at AS latest_order']);
    }
}