@section('header_styles')
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}"  rel="stylesheet" type="text/css" />
@stop
<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            <strong>Permissions</strong>
        </div>
        <div class="col-sm-3">
            @foreach(PermissionsHelper::getGroupPermissionsExcept(['backend', 'other']) as $group => $perms)
                @if (!$loop->first)
                    <hr>
                @endif
                @php ($land = 'permissions.groups.' . $group)
                <h4>@lang($land)</h4>
                @foreach($perms as $id)
                    @php ($land = 'permissions.labels.' . $id)
                    <div class="paddingtopbottom_5px">
                        {{ Form::checkbox('permissions[' . $id . ']', 1, $permissions[$id] ?? false, ['class' => 'custom-checkbox', 'id' => 'permissions-' . $id]) }} <label for="permissions-{{ $id }}">@lang($land)</label>
                    </div>
                @endforeach
            @endforeach
            @php ($other = PermissionsHelper::getGroupPermissions('other'))
            @if($other)
                <hr>
                <h4>@lang('permissions.groups.other')</h4>
                @foreach($other as $id)
                    @php ($land = 'permissions.labels.' . $id)
                    <div class="paddingtopbottom_5px">
                        {{ Form::checkbox('permissions[' . $id . ']', 1, $permissions[$id] ?? false, ['class' => 'custom-checkbox', 'id' => 'permissions-' . $id]) }} <label for="permissions-{{ $id }}">@lang($land)</label>
                    </div>
                @endforeach
             @endif
        </div>
        <div class="col-sm-3">
            <h4>@lang('permissions.groups.backend')</h4>
            @foreach(PermissionsHelper::getGroupPermissions('backend') as $id)
                @if($id == 'admin')
                    @php ($land = 'permissions.labels.backend')
                @else
                    @php ($land = 'permissions.labels.' . $id)
                @endif
                @php($level = count(explode('.', $id)))
                <div class="paddingtopbottom_5px" style="padding-left: {{ 20*($level-1) }}px">
                    {{ Form::checkbox('permissions[' . $id . ']', 1, $permissions[$id] ?? false, ['class' => 'custom-checkbox', 'id' => 'permissions-' . $id]) }} <label for="permissions-{{ $id }}">@lang(Lang::has($land.'.0') ? $land.'.0' : $land)</label>
                </div>
            @endforeach
        </div>
    </div>
</div>
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script>
        $(function() {
            $('input[type="checkbox"].custom-checkbox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue margin_right5',
                increaseArea: '20%'
            });
        });
    </script>
@stop