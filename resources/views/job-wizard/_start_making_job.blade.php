@inject('serviceRepository', 'App\Repositories\ServiceRepository')

<div id="start-making-job" class="form-style">
    {!! Form::open(['url' => route('account.jobs.wizard.members'), 'method' => 'GET']) !!}
        <div class="d-flex justify-content-center">
            <label for="group">What do you need</label>
            <div id="group-input">
                {{ Form::select('group', $serviceRepository->getGroupsDropdownList(), request('group'), ['id' => 'group', 'class' => 'form-control', 'placeholder' => 'Select an option']) }}
            </div>
            <div id="business-categories" class="services-input d-none">
                {{--<div class="tree-input">
                    {{ Form::text(null, null, ['id' => 'services-title', 'class' => 'form-control services-title', 'readonly' => 'readonly', 'data-loading' => 'Loading...', 'data-load' => 'Select options', 'placeholder' => '']) }}
                    {{ Form::hidden('services', null, ['id' => 'services']) }}
                    <div class="dropdown-container">
                        <div class="dropdown-tree">
                            <div id="services-tree" data-url="{{ route('services.tree.by') }}"></div>
                        </div>
                    </div>
                </div>--}}
            </div>
            {!! Form::button(trans('pagination.next'), ['type' => 'submit', 'class'=> 'btn btn--orange ml-3 pl-4 pr-4']); !!}
        </div>
    {!! Form::close() !!}
</div>

@section('footer_scripts')
    @parent
@endsection