@if(!$task->isOverdue() && $task->status != 'acknowledge' && $task->status != 'completed')
    <button type="button" class="btn-acknowledge btn link--orange" data-url="{{ route('account.tasks.status', ['id' => $task->id, 'status' => 'acknowledge']) }}">
        Acknlge <i class="fas fa-tasks"></i>
    </button>
@endif

@if($task->status != 'completed')
    <div class="btn-group">
        <button type="button" class="btn-snoozed btn btn--orange dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-hourglass-half"></i> Snoozed
        </button>
        <div class="dropdown-menu">
            <button class="dropdown-item btn-snoozed-for" data-url="{{ route('account.tasks.snoozed', ['id' => $task->id, 'hours' => 24]) }}">24 hrs</button>
            <button class="dropdown-item btn-snoozed-for" data-url="{{ route('account.tasks.snoozed', ['id' => $task->id, 'hours' => 48]) }}">48 hrs</button>
            <button class="dropdown-item btn-snoozed-for" data-url="{{ route('account.tasks.snoozed', ['id' => $task->id, 'hours' => 72]) }}">72 hrs</button>
        </div>
    </div>
@endif

@if($task->status != 'completed')
    <button type="button" class="btn-completed btn link--orange" data-url="{{ route('account.tasks.status', ['id' => $task->id, 'status' => 'completed']) }}">
        <i class="fas fa-check"></i> Completed
    </button>
@endif

@if($task->status == 'completed')
    <a href="{{ route('account.tasks.repeat', ['id' => $task->id]) }}" class="btn">
        <i class="fas fa-redo"></i> Repeat
    </a>
@endif

@if($task->status != 'completed')
    <button type="button" class="btn-cancelled btn link--orange" data-url="{{ route('account.tasks.status', ['id' => $task->id, 'status' => 'cancelled']) }}">
        <i class="fas fa-times"></i> Cancelled
    </button>
@endif

@if($task->status != 'completed')
    <button type="button" class="btn-share-to btn link--orange" data-toggle="modal" data-target="#modal-share-to" data-id="{{ $task->id }}">
        <i class="fas fa-share-square"></i> Share
    </button>
@endif

<a href="{{ route('account.tasks.edit', ['id' => $task->id]) }}" class="btn"><i class="fas fa-edit"></i> Edit</a>