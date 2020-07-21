<?php

namespace App\Http\Controllers;

use App\CrewMember;
use App\Employee;
use App\Helpers\RelatedProfile;
use App\Http\Requests\Tasks\DashboardTasksRequest;
use App\Models\Eav\Entity;
use App\Models\Tasks\Task;
use App\Role;
use App\Rules\AssignedTo;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Sentinel;
use DB;

/**
 * Class TasksController
 * @package App\Http\Controllers
 */
class TasksController extends Controller
{
    /**
     * @var array
     */
    //protected $orders = [];

    /**
     * @return string
     */
    /*protected function getOrder()
    {
        return in_array(request('order'), array_keys($this->orders)) ? request('order') : 'new';
    }*/

    /**
     * @param Request $related_member_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function membersData($related_member_id, Request $request)
    {
        $relatedMember = RelatedProfile::currentRelatedMember();

        $results = collect([]);

        $members = collect([]);

        if ($q = $request->get('search')) {
            if ($relatedMember->isBusinessAccount()) {
                $members = Employee::join('businesses_employees', 'users.id', '=', 'businesses_employees.user_id')
                    ->where('businesses_employees.business_id', $relatedMember->profile->id)
                    ->where(function ($query) use ($q) {
                        $query->where('users.first_name', 'like', "%{$q}%")->orWhere('users.last_name', 'like', "%{$q}%");
                    })
                    ->groupBy('users.id')
                    ->select('users.*')
                    ->get();
            }
            if ($relatedMember->isVesselAccount()) {
                $members = CrewMember::join('vessels_crew', 'users.id', '=', 'vessels_crew.user_id')
                    ->where('vessels_crew.vessel_id', $relatedMember->profile->id)
                    ->where(function ($query) use ($q) {
                        $query->where('users.first_name', 'like', "%{$q}%")->orWhere('users.last_name', 'like', "%{$q}%");
                    })->groupBy('users.id')
                    ->select('users.*')
                    ->get();
            }

            $results = $members->map(function ($item) {
                /** @var User $item */
                return [
                    'id' => $item->id,
                    'thumb' => $item->getThumb('24x24'),
                    'text' => "{$item->member_title} ({$item->account_type_title})",
                ];
            });
        } elseif ($request->get('with') == 'position') {
            $roles = [];
            if ($relatedMember->isBusinessAccount()) {
                $roles = Employee::EMPLOYEE_ROLES;
            }
            if ($relatedMember->isVesselAccount()) {
                $roles = CrewMember::CREW_ROLES;
            }
            if ($roles) {
                $results = Role::whereIn('slug', $roles)->get()->map(function ($item) {
                    /** @var User $item */
                    return [
                        'id' => $item->slug,
                        'text' => "{$item->name}",
                    ];
                });
            }
        }

        $results->prepend([
            'id' => $relatedMember->parent->id,
            'thumb' => $relatedMember->parent->getThumb('24x24'),
            'text' => "{$relatedMember->parent->member_title} ({$relatedMember->parent->account_type_title})",
        ]);

        return response()->json(['results' => $results]);
    }

    /**
     * @parem int $related_member_id
     * @parem Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tasks($related_member_id, Request $request)
    {
        $request->validate([
            'set_as' => ['nullable', Rule::in(array_keys(Task::getSetAsList()))],
            'priority' => ['nullable', Rule::in(array_keys(Task::getPriorityList()))],
            'assigned_to_id' => ['nullable', resolve(AssignedTo::class)],
            'status' => ['nullable', Rule::in(['shared', 'acknowledge', 'upcoming', 'snoozed', 'overdue', 'completed'])],
        ]);

        $setAsList = Task::getSetAsList();
        $priorityList = Task::getPriorityList();

        $table = Task::getModel()->getTable();

        //\DB::enableQueryLog();

        $builder = Task::orderBy($table . '.due_date_at', 'asc')->where($table . '.status', '!=', 'cancelled');
        // Filter
        if ($status = $request->get('status')) {
            switch ($status) {
                case 'shared':
                    $builder->sharedToMe($related_member_id);
                    break;
                case 'acknowledge':
                    $builder->acknowledge($related_member_id);
                    break;
                case 'upcoming':
                    $builder->upcoming($related_member_id);
                    break;
                case 'snoozed':
                    $builder->snoozed($related_member_id);
                    break;
                case 'overdue':
                    $builder->overdue($related_member_id);
                    break;
                case 'completed':
                    $builder->completed($related_member_id);
                    break;
                default:
                    $builder->owned($related_member_id);
            }
        } else {
            $builder->owned($related_member_id);
        }
        if ($setAs = $request->get('set_as')) {
            $builder->where($table . '.set_as', $setAs);
        }
        if ($priority = $request->get('priority')) {
            $builder->where($table . '.priority', $priority);
        }
        if ($assignedTo = $request->get('assigned_to')) {
            if (is_numeric($assignedTo)) {
                $builder->where($table . '.assigned_to_id', $assignedTo);
            } else {
                $builder->leftJoin('role_users', $table . '.assigned_to_id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->where('roles.slug', $assignedTo);
            }
        }
        if ($search = $request->get('search')) {
            $columns = implode(',', Task::getModel()->getSearchableColumns());
            $searchableTerm = Task::getModel()->getFullTextWildcards($search);
            $builder->orders = [];
            $builder->selectRaw($table . ".*, attr.*, MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE) AS relevance_score", [$searchableTerm])
                ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $searchableTerm)
                ->orderByDesc('relevance_score');
        }
        // End Filter
        $tasks = $builder->select([$table . '.*', 'attr.*'])->paginate(20);

        //dd(DB::getQueryLog());

        $entity = Entity::findByCode('task');
        $attributes = $entity->attributesFor($related_member_id)->get();

        $additionalColumns = (array)json_decode(request()->cookie('tasks_additional_columns'));
        $columns = [];
        $columns[] = 'set_as';
        $columns[] = 'priority';
        $columns[] = 'created_at';
        $columns[] = 'due_date_at';
        $columns[] = 'description';
        foreach ($additionalColumns as $additionalColumn) {
            if ($attributes->first(function ($item) use ($additionalColumn) {
                return $item->attribute_code == $additionalColumn;
            })) {
                $columns[] = $additionalColumn;
            }
        }
        $columns[] = 'assigned_to_id';

        $additional_attributes = $attributes->filter(function ($item) use ($additionalColumns) {
            return in_array($item->attribute_code, $additionalColumns);
        });

        return view('tasks.index', compact('related_member_id', 'tasks', 'columns', 'attributes', 'additional_attributes', 'setAsList', 'priorityList'));
    }

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($related_member_id, Request $request)
    {
        $setAsList = Task::getSetAsList();
        $priorityList = Task::getPriorityList();

        $entity = Entity::findByCode('task');
        $attributes = $entity->attributesFor($related_member_id)->get();

        return view('tasks.create', compact('setAsList', 'priorityList', 'attributes'));
    }

    /**
     * @param int $related_member_id
     * @param DashboardTasksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store($related_member_id, DashboardTasksRequest $request)
    {
        DB::beginTransaction();
        try {
            $model = new Task();
            $model->fill($request->all());
            $model->created_by_id = Sentinel::getUser()->getUserId();
            $model->related_member_id = $related_member_id;
            $model->saveOrFail();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return redirect()->back()->with('error', 'Failed to save the task.');
        }

        return redirect()->route('account.tasks.index')->with('success', 'Task successfully saved.');
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($related_member_id, $id, Request $request)
    {
        $model = Task::hasAccess($related_member_id)->select(['*', 'attr.*'])->find($id);

        if (empty($model)) {
            return abort(404);
        }

        $setAsList = Task::getSetAsList();
        $priorityList = Task::getPriorityList();

        $entity = Entity::findByCode('task');
        $attributes = $entity->attributesFor($related_member_id)->get();

        return view('tasks.edit', compact('setAsList', 'priorityList', 'attributes'))->with('model', $model);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param DashboardTasksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update($related_member_id, $id, DashboardTasksRequest $request)
    {
        $model = Task::hasAccess($related_member_id)->find($id);

        if (empty($model)) {
            return abort(404);
        }

        DB::beginTransaction();
        try {
            $model->fill($request->all());
            $model->saveOrFail();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return redirect()->back()->with('error', 'Failed to save the task.');
        }

        return redirect()->route('account.tasks.index')->with('success', 'Task successfully saved.');
    }

    /**
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function share($related_member_id, Request $request)
    {
        $request->validate([
            'share_to_id' => ['nullable', resolve(AssignedTo::class)],
        ]);

        $model = Task::hasAccess($related_member_id)->find($request->get('id'));

        if (empty($model)) {
            return abort(404);
        }

        DB::beginTransaction();
        try {
            $model->shared_to()->syncWithoutDetaching([$request->get('share_to_id')], false);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            if ($request->ajax()) {
                abort(500);
            }

            return redirect()->back()->with('error', 'Failed to share the task.');
        }

        if ($request->ajax()) {
            return response()->json('Task successfully shared.');
        }

        return redirect()->route('account.tasks.index')->with('success', 'Task successfully shared.');
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function repeat($related_member_id, $id, Request $request)
    {
        $model = Task::my($related_member_id)->find($id);

        if (empty($model)) {
            return abort(404);
        }

        $setAsList = Task::getSetAsList();
        $priorityList = Task::getPriorityList();

        $model->due_date_at = null;

        return view('tasks.edit', compact('setAsList', 'priorityList'))->with('model', $model)->with('route', ['account.tasks.reopen', 'id' => $model->id]);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param DashboardTasksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function reopen($related_member_id, $id, DashboardTasksRequest $request)
    {
        $model = Task::my($related_member_id)->find($id);

        if (empty($model)) {
            return abort(404);
        }

        DB::beginTransaction();
        try {
            $model->fill($request->all());
            $model->status = '';
            $model->saveOrFail();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            return redirect()->back()->with('error', 'Failed to reopen the task.');
        }

        return redirect()->route('account.tasks.index')->with('success', 'Task successfully reopened.');
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param $hours
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function snoozed($related_member_id, $id, $hours, Request $request)
    {
        if (!in_array($hours, [24, 48, 72])) {
            return abort(404);
        }

        $model = Task::hasAccess($related_member_id)->find($id);

        if (empty($model)) {
            return abort(404);
        }

        DB::beginTransaction();
        try {
            $model->status = 'snoozed';
            $model->due_date_at = Carbon::parse($model->due_date_at ?? date('Y-m-d'))->addHours($hours);
            $model->saveOrFail();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            if ($request->ajax()) {
                abort(500);
            }

            return redirect()->back()->with('error', 'Failed to snooze the task.');
        }

        $message = 'Task was snoozed for ' . $hours . ' hours.';

        if ($request->ajax()) {
            return response()->json($message);
        }

        return redirect()->route('account.tasks.index')->with('success', $message);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param $status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function status($related_member_id, $id, $status, Request $request)
    {
        if (!in_array($status, ['acknowledge', 'completed', 'cancelled'])) {
            return abort(404);
        }

        $model = Task::hasAccess($related_member_id)->find($id);

        if (empty($model)) {
            return abort(404);
        }

        DB::beginTransaction();
        try {
            $model->status = $status;
            $model->saveOrFail();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            if ($request->ajax()) {
                abort(500);
            }

            return redirect()->back()->with('error', 'Failed to change status.');
        }

        $message = 'Task status changed to ' . (Task::getStatuses()[$status] ?? '') . '.';

        if ($request->ajax()) {
            return response()->json($message);
        }

        return redirect()->route('account.tasks.index')->with('success', $message);
    }
}
