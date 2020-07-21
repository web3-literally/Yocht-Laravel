<?php

namespace App\Http\Controllers;

use App\File;
use App\Http\Requests\MessageRequest;
use App\Mail\Messenger\NewMessage;
use App\Models\Jobs\Job;
use App\Models\Jobs\JobTickets;
use App\Models\Messenger\Message;
use App\Models\Messenger\Participant;
use App\User;
use Illuminate\Http\Request;
use Sentinel;
use Carbon\Carbon;
use Validator;
use Mail;

/**
 * Class TicketsController
 * @package App\Http\Controllers
 */
class TicketsController extends Controller
{
    /**
     * @param int $related_member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($related_member_id)
    {
        $tab = request('tab');

        $perPage = 10;

        if ($tab == 'pending') {
            $tickets = JobTickets::toMe($related_member_id)
                ->published()
                ->paginate($perPage);

            return view('tickets.pending', compact('tickets'));
        }

        $builder = JobTickets::forMe($related_member_id);
        if ($tab == Job::STATUS_COMPLETED) {
            $builder->completed();
        } else {
            $builder->active();
        }

        $tickets = $builder->paginate($perPage);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * @param int $related_member_id
     * @param int $id Ticket
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details($related_member_id, int $id)
    {
        $ticket = $this->loadTicket($id);

        return view('tickets.details', compact('ticket'));
    }

    /**
     * @param int $related_member_id
     * @param int $id Ticket
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function messages($related_member_id, int $id)
    {
        $ticket = $this->loadTicket($id);
        if (empty($ticket->application->thread)) {
            abort(404);
        }
        $thread = $ticket->application->thread->thread;
        $messages = $thread->conversations()->latest()->paginate(10);

        $userId = Sentinel::getUser()->getUserId();
        $thread->markAsRead($userId);

        return view('tickets.messages', compact('ticket', 'thread', 'messages'));
    }

    /**
     * @param int $related_member_id
     * @param int $id Ticket
     * @param MessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function send($related_member_id, int $id, MessageRequest $request)
    {
        $ticket = $this->loadTicket($id);
        if (empty($ticket->application->thread)) {
            abort(404);
        }
        $thread = $ticket->application->thread->thread;

        $thread->activateAllParticipants();

        $message = strip_tags($request->get('message'));

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
        $participant->last_read = new Carbon;
        $participant->saveOrFail();

        // Recipient
        $thread->addParticipant($thread->directUser()->id);

        Mail::send(new NewMessage($thread, $message));

        return redirect()->route('account.tickets.messages', ['id' => $id]);
    }

    /**
     * @param int $related_member_id
     * @param int $id Ticket
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function upload($related_member_id, int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,odt',
        ]);

        if ($validator->fails()) {
            $files = $request->file('files');
            $result = [];
            $bag = $validator->getMessageBag();
            foreach ($bag->messages() as $key => $messages) {
                list($field, $index) = explode('.', $key);
                $file = $files[$index] ?? null;
                if ($file) {
                    $message = current($messages);
                    $message = str_replace($key, $file->getClientOriginalName(), $message);
                    $result[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'error' => $message
                    ];
                }
            }

            return response()->json(['files' => $result]);
        }

        $ticket = $this->loadTicket($id);

        $model = $ticket->application;

        $result = [];

        if ($request->hasfile('files')) {
            $storePath = 'ticket/' . $ticket->id . '/' . $ticket->application->id;
            foreach ($request->file('files') as $i => $file) {
                try {
                    $fl = new File();

                    $fl->mime = $file->getMimeType();
                    $fl->size = $file->getSize();
                    $fl->filename = $file->getClientOriginalName();
                    $fl->disk = 'local';
                    $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                    $fl->saveOrFail();

                    $link = $model->attachFile($fl);

                    // Attachment notification message
                    // TODO: Avoid duplicate code VesselsJobsController
                    $thread = $model->thread->thread;
                    $thread->activateAllParticipants();
                    $message = $fl->filename . ' was attached.';
                    Message::create([
                        'thread_id' => $thread->id,
                        'user_id' => Sentinel::getUser()->getUserId(),
                        'body' => $message,
                    ]);
                    $participant = Participant::firstOrCreate([
                        'thread_id' => $thread->id,
                        'user_id' => Sentinel::getUser()->getUserId(),
                    ]);
                    $participant->last_read = new Carbon;
                    $participant->saveOrFail();
                    $thread->addParticipant($thread->directUser()->id);
                    Mail::send(new NewMessage($thread, $message));

                    unset($fl);

                    $result[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'url' => route('account.tickets.attachments.download', ['id' => $ticket->id, 'file' => $link->file_id])
                    ];
                } catch (\Throwable $e) {
                    $result[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'error' => $e->getMessage()
                    ];
                } finally {
                    if (isset($fl->id) && $fl->id) {
                        // Delete file in case if failed to update database
                        $fl->delete();
                    }
                }
            }
        }

        return response()->json(['files' => $result]);
    }

    /**
     * @param int $related_member_id
     * @param int $id Ticket
     * @param int $file File
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($related_member_id, int $id, int $file)
    {
        $ticket = $this->loadTicket($id);

        $model = $ticket->application;

        $link = $model->attachments()->where('file_id', $file)->first();
        if (!$link) {
            abort(404);
        }

        return response()->download($link->file->getFilePath(), $link->file->filename);
    }

    /**
     * @param int $related_member_id
     * @param int $id Ticket
     * @param int $file File
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function remove($related_member_id, int $id, int $file)
    {
        $ticket = $this->loadTicket($id);

        $model = $ticket->application;

        $link = $model->attachments(Sentinel::getUser()->getUserId())->where('file_id', $file)->first();
        if (!$link) {
            abort(404);
        }

        if (!$link->delete()) {
            abort('500', 'An unknown error has occurred');
        }

        return response()->json(true);
    }

    /**
     * @param int $id Ticket
     * @return mixed
     */
    protected function loadTicket(int $id)
    {
        $ticket = JobTickets::forMe(request()->route('related_member_id'))->find($id);
        if (empty($ticket)) {
            abort(404);
        }

        return $ticket;
    }

    /**
     * @param int $memberId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function related(int $memberId)
    {
        $member = User::find($memberId);
        if (!$member) {
            abort(404);
        }

        $tickets = JobTickets::related($memberId)->paginate(10);

        return view('tickets.related', compact('member', 'tickets'));
    }
}
