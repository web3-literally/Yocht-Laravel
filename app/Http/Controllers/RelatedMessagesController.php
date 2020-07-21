<?php

namespace App\Http\Controllers;

use App\Models\Messenger\Participant;
use App\Models\Messenger\Message;
use App\Models\Messenger\Thread;
use View;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class RelatedMessagesController extends Controller
{
    /**
     * @param int $related_member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($related_member_id)
    {
        $threads = Thread::forUser($related_member_id)->latest('updated_at')->paginate(20);

        View::share('related_member_id', $related_member_id);

        return view('related-messenger.index', compact('threads'));
    }

    /**
     * @param int $related_member_id
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($related_member_id, $id)
    {
        $thread = Thread::forUser($related_member_id)->findOrFail($id);

        $userId = $related_member_id;
        $messages = $thread->messages()->latest()->paginate(10);
        $thread->markAsRead($userId);

        return view('related-messenger.show', compact('thread', 'messages'));
    }

    /**
     * @param $related_member_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($related_member_id, $id)
    {
        try {
            $thread = Thread::forUser($related_member_id)->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');

            return redirect()->route('account.related.messages.index');
        }

        $thread->activateAllParticipants();

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => $related_member_id,
            'body' => Input::get('message'),
        ]);

        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => $related_member_id,
        ]);
        $participant->last_read = new Carbon;
        $participant->save();

        // Recipients
        if (Input::has('recipients')) {
            $thread->addParticipant(Input::get('recipients'));
        }

        return redirect()->route('account.related.messages.show', $id);
    }
}
