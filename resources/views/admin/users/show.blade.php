@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    View User Details
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/x-editable/css/bootstrap-editable.css') }}" rel="stylesheet"/>

    <link href="{{ asset('assets/css/pages/user_profile.css') }}" rel="stylesheet"/>
@stop


{{-- Page content --}}
@section('content')
    <section class="content-header">
        <!--section starts-->
        <h1>User Profile</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="#">Users</a>
            </li>
            <li class="active">User Profile</li>
        </ol>
    </section>
    <!--section ends-->
    <section class="content user_profile">
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav  nav-tabs first_svg">
                    <li class="nav-item">
                        <a href="#tab1" data-toggle="tab" class="nav-link active">
                            <i class="livicon" data-name="user" data-size="16" data-c="#777"  data-hc="#000" data-loop="true"></i>
                            User Profile</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab2" data-toggle="tab" class="nav-link">
                            <i class="livicon" data-name="money" data-size="16" data-loop="true" data-c="#000" data-hc="#000"></i>
                            @lang('billing.subscriptions')
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab3" data-toggle="tab" class="nav-link">
                            <i class="livicon" data-name="key" data-size="16" data-loop="true" data-c="#000" data-hc="#000"></i>
                            Change Password</a>
                    </li>
                </ul>
                <div  class="tab-content mar-top" id="clothing-nav-content">
                    <div id="tab1" class="tab-pane fade show active">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-heading">
                                        <h3 class="card-title">

                                            User Profile
                                        </h3>

                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="img-file">
                                                <img src="{{ $user->getThumb('200x200') }}" alt="img" class="img-fluid"/>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                                <div class="table-responsive-lg table-responsive-sm table-responsive-md table-responsive">
                                                    <table class="table table-bordered table-striped" id="users">

                                                        <tr>
                                                            <td>@lang('users/title.first_name')</td>
                                                            <td>
                                                                <p class="user_name_max">{{ $user->first_name }}</p>
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.last_name')</td>
                                                            <td>
                                                                <p class="user_name_max">{{ $user->last_name }}</p>
                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.email')</td>
                                                            <td>
                                                                {{ $user->email }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                @lang('users/title.gender')
                                                            </td>
                                                            <td>
                                                                {{ $user->gender }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.dob')</td>

                                                            @if($user->dob=='0000-00-00')
                                                                <td>
                                                                </td>
                                                            @else
                                                                <td>
                                                                    {{ $user->dob }}
                                                                </td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.country')</td>
                                                            <td>
                                                                {{ $user->country }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.state')</td>
                                                            <td>
                                                                {{ $user->state }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.city')</td>
                                                            <td>
                                                                {{ $user->city }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.address')</td>
                                                            <td>
                                                                {{ $user->address }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.postal')</td>
                                                            <td>
                                                                {{ $user->postal }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.status')</td>
                                                            <td>

                                                                @if($user->deleted_at)
                                                                    Deleted
                                                                @elseif($activation = Activation::completed($user))
                                                                    Activated
                                                                @else
                                                                    Pending
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.created_at')</td>
                                                            <td>
                                                                {!! $user->created_at->toFormattedDateString() !!}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('users/title.updated_at')</td>
                                                            <td>
                                                                {!! $user->updated_at->diffForHumans() !!}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab2" class="tab-pane fade">
                        @include('admin.users._subscriptions')
                    </div>
                    <div id="tab3" class="tab-pane fade">
                        <div class="row">
                            <div class="col-md-12 pd-top ml-auto">
                                <form class="form-horizontal">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="row">
                                            {{ csrf_field() }}
                                            <label for="inputpassword" class="col-md-3 control-label">
                                                Password
                                                <span class='require'>*</span>
                                            </label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                            <span class="input-group-append">
                                                                <span class="input-group-text"><i class="livicon" data-name="key" data-size="16" data-loop="true" data-c="#000" data-hc="#000"></i></span>
                                                            </span>
                                                    <input type="password" id="password" placeholder="Password" name="password"
                                                           class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                            <label for="inputnumber" class="col-md-3 control-label">
                                                Confirm Password
                                                <span class='require'>*</span>
                                            </label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                            <span class="input-group-append">
                                                                <span class="input-group-text"><i class="livicon" data-name="key" data-size="16" data-loop="true" data-c="#000" data-hc="#000"></i></span>
                                                            </span>
                                                    <input type="password" id="password-confirm" placeholder="Confirm Password" name="confirm_password"
                                                           class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="col-md-offset-3 col-md-9 ml-auto">
                                            <button type="submit" class="btn btn-primary" id="change-password">Submit
                                            </button>
                                            &nbsp;
                                            <input type="reset" class="btn btn-default" value="Reset"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
    <!-- Bootstrap WYSIHTML5 -->
    <script  src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#change-password').click(function (e) {
                e.preventDefault();
                var check = false;
                if ($('#password').val() ===""){
                    alert('Please Enter password');
                }
                else if  ($('#password').val() !== $('#password-confirm').val()) {
                    alert("confirm password should match with password");
                }
                else if  ($('#password').val() === $('#password-confirm').val()) {
                    check = true;
                }

                if(check == true){
                var sendData =  '_token=' + $("input[name='_token']").val() +'&password=' + $('#password').val() +'&id=' + {{ $user->id }};
                    var path = "passwordreset";
                    $.ajax({
                        url: path,
                        type: "post",
                        data: sendData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                        },
                        success: function (data) {
                            $('#password, #password-confirm').val('');
                            alert('password reset successful');
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert('error in password reset');
                        }
                    });
                }
            });
        });



        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
            e.target // newly activated tab
            e.relatedTarget // previous active tab
            $("#clothing-nav-content .tab-pane").removeClass("show active");
        });

    </script>

@stop
