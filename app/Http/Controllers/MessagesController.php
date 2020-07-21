<?php

namespace App\Http\Controllers;

use App\Helpers\PageOffset;
use App\Http\Requests\MessageRequest;
use App\Mail\Messenger\NewMessage;
use App\MessengerUser;
use App\User;
use Illuminate\Http\Request;
use Sentinel;
use Carbon\Carbon;
use App\Models\Messenger\Message;
use App\Models\Messenger\Participant;
use App\Models\Messenger\Thread;
use Mail;
use Cache;

/**
 * Class MessagesController
 * @package App\Http\Controllers
 */
class MessagesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $threads = Thread::my()->latest('latest_order')->paginate(20);

        return view('messenger.index', compact('threads'));
    }

    /**
     * @param int $member_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function create(int $member_id)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $member = $this->loadMemberTo($member_id);

        if ($member) {
            $thread = Thread::myTo($member->id)->first();
            if ($thread) {
                return redirect()->route('account.messages.show', $thread->id);
            }

            return view('messenger.create', compact('member'));
        }

        abort(404);
    }

    /**
     * @param int $member_id
     * @param MessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(int $member_id, MessageRequest $request)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $member = $this->loadMemberTo($member_id);

        if ($member) {
            $thread = Thread::create([
                'subject' => Thread::DIRECT_SUBJECT,
            ]);

            $message = strip_tags($request->get('message'));

            Message::create([
                'thread_id' => $thread->id,
                'user_id' => $user->getUserId(),
                'body' => $message,
            ]);

            // Sender
            Participant::create([
                'thread_id' => $thread->id,
                'user_id' => Sentinel::getUser()->getUserId(),
                'last_read' => new Carbon,
            ]);

            // Recipient
            $thread->addParticipant($member->id);

            Mail::send(new NewMessage($thread, $message));

            return redirect()->route('account.messages.show', $thread->id)->with('success', 'You have sent the message.');
        }

        abort(404);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(int $id)
    {
        $thread = Thread::my()->findOrFail($id);

        $userId = Sentinel::getUser()->getUserId();
        $messages = $thread->messages()->latest()->paginate(10);
        $thread->markAsRead($userId);

        return view('messenger.show', compact('thread', 'messages'));
    }

    /**
     * @param $id
     * @param MessageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update($id, MessageRequest $request)
    {
        $thread = Thread::my()->findOrFail($id);

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

        return redirect()->route('account.messages.show', $id);
    }

    /**
     * @param int|string $q
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    protected function crewQuery($q)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $query = null;

        if ($user->isCaptainAccount() || $user->isCrewAccount()) {
            $user = $user->asCrewMember();
            if ($user->vessel) {
                // If assigned to boat
                $query = $user->vessel->crewMembers();
            }
        } else {
            $query = $user->accounts();
        }

        if ($query) {
            if (is_numeric($q)) {
                return $query->where(MessengerUser::getModel()->getTable() . '.id', intval($q));
            } else {
                $columns = implode(',', MessengerUser::getModel()->getSearchableColumns());
                $searchableTerm = MessengerUser::getModel()->getFullTextWildcards($q);
                return $query->selectRaw(MessengerUser::getModel()->getTable() . ".*, MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE) AS relevance_score", [$searchableTerm])
                    ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $searchableTerm)
                    ->orderByDesc('relevance_score')->where('users.id', '!=', $user->id);
            }
        }

        return null;
    }

    /**
     * @param $member_id
     * @return User|null
     */
    protected function loadMemberTo($member_id)
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $member = null;

        if ($query = $this->crewQuery($member_id)) {
            $member = $query->first();
            if (!$member && !$user->isCrewAccount()) {
                $member = User::searchableAccounts()->where('users.id', $member_id)->where('users.id', '!=', $user->id)->first();
            }
        }

        return $member;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchMember(Request $request)
    {
        $term = mb_strtolower($request->get('term'));

        /** @var User $user */
        $user = Sentinel::getUser();

        $results = Cache::remember('MessagesMembersSearch-' . $user->getAccountType() . '-' . md5($term), 3, function () use ($user, $term) {
            $results = [];

            if ($crewQuery = $this->crewQuery($term . '')) {
                $crew = $crewQuery->get();

                $list = $crew->map(function ($item) {
                    /** @var User $item */
                    return [
                        'id' => $item->id,
                        'thumb' => $item->getThumb('24x24'),
                        'text' => $item->member_title,
                        'url' => route('account.messages.create', $item->id)
                    ];
                });

                if ($list->count()) {
                    $results[] = [
                        'text' => trans('crew.crew'),
                        'group' => true,
                        'children' => $list->toArray()
                    ];
                }
            }

            if ($user->isCrewAccount()) {
                return $results;
            }

            $must = [];
            if (is_numeric($term)) {
                $must[] = [
                    'match' => [
                        'id' => $term
                    ]
                ];
            } else {
                $must[] = [
                    'multi_match' => [
                        'query' => $term,
                        'fields' => ['member_title^3', /*'profile.description'*/],
                        'fuzziness' => 'AUTO'
                    ]
                ];
            }

            $must_not = [
                0 => [
                    'match' => [
                        'id' => Sentinel::getUser()->getUserId()
                    ]
                ],
            ];

            $query = null;
            if ($must || $must_not) {
                $query = [
                    "bool" => []
                ];
                if ($must) {
                    $query['bool']['must'] = $must;
                }
                if ($must_not) {
                    $query['bool']['must_not'] = $must_not;
                }
            }

            $members = User::searchByQuery($query, null, ['id', 'member_title'], 30, null, [
                'member_title' => [
                    'order' => 'asc'
                ]
            ]);

            $list = $members->map(function ($item) {
                /** @var User $item */
                return [
                    'id' => $item->id,
                    'thumb' => $item->getThumb('24x24'),
                    'text' => $item->member_title,
                    'url' => route('account.messages.create', $item->id)
                ];
            });

            if ($list->count()) {
                $results[] = [
                    'text' => trans('general.members'),
                    'group' => true,
                    'children' => $list->toArray()
                ];
            }

            return $results;
        });

        return response()->json(['results' => $results]);
    }

    /**
     * @param User $member
     * @return $this
     * @throws \Exception
     */
    protected function denyYourself(User $member)
    {
        if ($member->id == Sentinel::getUser()->getUserId()) {
            throw new \Exception('You can\'t make a post for yourself');
        }

        return $this;
    }
}
