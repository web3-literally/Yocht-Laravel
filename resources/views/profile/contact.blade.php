@extends('layouts.dashboard-profile')

@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/minimal/blue.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
@stop

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('dashboard._messages')
            </div>
        </div>
    </div>
    <div class="container">
        <h3>@lang('general.account_contact')</h3>
        @parent
        <div class="row">
            <div class="col-lg-12 col-12">
                <div class="position-center">
                    @php
                        $parts = explode('.', Request::route()->getName());
                        $parts[] = 'update';
                    @endphp
                    {!! Form::model($user, ['url' => route(implode('.', $parts), Request::route()->parameters), 'method' => 'put', 'class' => 'form-horizontal','enctype'=>"multipart/form-data"]) !!}
                    <div class="form-group {{ $errors->first('first_name', 'has-error') }}">
                        <div class="row">
                            <div class="col-lg-2 col-12">
                                <label class="control-label">
                                    First Name
                                    <span class='require'>*</span>
                                </label>
                            </div>
                            <div class="col-lg-10 col-12">
                                <div class="input-group input-group-append">
                                    <input type="text" placeholder="" name="first_name" id="u-name" class="form-control" value="{!! old('first_name',$user->first_name) !!}">
                                </div>
                                <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->first('last_name', 'has-error') }}">
                        <div class="row">
                            <div class="col-lg-2 col-12">
                                <label class="control-label">
                                    Last Name
                                    <span class='require'>*</span>
                                </label>
                            </div>
                            <div class="col-lg-10 col-12">
                                <div class="input-group input-group-append">
                                    <input type="text" placeholder="" name="last_name" id="u-name" class="form-control" value="{!! old('last_name',$user->last_name) !!}"></div>
                                <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->first('email', 'has-error') }}">
                        <div class="row">
                            <div class="col-lg-2 col-12">
                                <label class="control-label">
                                    Email
                                    <span class='require'>*</span>
                                </label>
                            </div>
                            <div class="col-lg-10 col-12">
                                @if($confirmation = $user->emailConfirmations()->latest()->first())
                                    @if(!$confirmation->completed)
                                        <div class="alert alert-warning">Please, check you mail inbox and confirm your <b>{{ $confirmation->email }}</b> email. Your email will be changed if you confirm new one.</div>
                                    @endif
                                @endif
                                <div class="input-group input-group-append">
                                    <input type="text" placeholder="" id="email" name="email" class="form-control" value="{!! old('email',$user->email) !!}"></div>
                                <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->first('phone', 'has-error') }}">
                        <div class="row">
                            <div class="col-lg-2 col-12">
                                <label class="control-label">
                                    Phone *
                                </label>
                            </div>
                            <div class="col-lg-10 col-12">
                                <div class="input-group input-group-append">
                                    <input type="text" placeholder="" name="phone" id="phone" class="form-control" value="{!! old('phone',$user->phone) !!}"></div>
                                <span class="help-block">{{ $errors->first('phone', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid p-0 m-0">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group {{ $errors->first('country', 'has-error') }}">
                                    <div class="row">
                                        <div class="col-lg-3 col-12">
                                            <label class="control-label">Select Country </label>
                                        </div>
                                        <div class="col-lg-9 col-12">
                                            {!! Form::select('country', $countries, $user->country,['class' => 'form-control select2', 'id' => 'countries']) !!}
                                            <span class="help-block">{{ $errors->first('country', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->first('user_state', 'has-error') }}">
                                    <div class="row">
                                        <div class="col-lg-3 col-12">
                                            <label class="control-label" for="state">State</label>
                                        </div>
                                        <div class="col-lg-9 col-12 col-md-12 col-sm-12">
                                            <div class="input-group input-group-append">
                                                <input type="text" placeholder="" id="state" class="form-control" name="user_state" value="{!! old('user_state',$user->user_state) !!}"/>
                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('user_state', ':message') }}</span>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->first('city', 'has-error') }}">
                                    <div class="row">
                                        <div class="col-lg-3 col-12">
                                            <label class=control-label" for="city">City</label>
                                        </div>
                                        <div class="col-lg-9 col-12">
                                            <div class="input-group input-group-append">
                                                <input type="text" placeholder="" id="city" class="form-control" name="city" value="{!! old('city',$user->city) !!}"/>
                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('city', ':message') }}</span>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->first('address', 'has-error') }}">
                                    <div class="row">
                                        <div class="col-lg-3 col-12">
                                            <label class="control-label">
                                                Address
                                            </label>
                                        </div>
                                        <div class="col-lg-9 col-12">
                                            <input class="form-control" id="add1" name="address" value="{{ old('address',$user->address) }}">
                                        </div>
                                        <span class="help-block">{{ $errors->first('address', ':message') }}</span>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->first('postal', 'has-error') }}">
                                    <div class="row">
                                        <div class="col-lg-3 col-12">
                                            <label class=" control-label" for="postal">Postal</label>
                                        </div>
                                        <div class="col-lg-9 col-12">
                                            <div class="input-group input-group-append">
                                                <input type="text" placeholder="" id="postal" class="form-control" name="postal" value="{!! old('postal',$user->postal) !!}"/>
                                            </div>
                                            <span class="help-block">{{ $errors->first('postal', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @widget('Map', ['id' => 'user-map', 'class' => '', 'address' => $user->full_address, 'height' =>'253px', 'zoom' => 12])
                            </div>
                        </div>
                        @if($user->isCaptainAccount() || $user->isCrewAccount())
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-2 col-12">
                                        <label class=" control-label" for="experience">Experience</label>
                                    </div>
                                    <div class="col-lg-1 col-12">
                                        <div class="input-group input-group-append">
                                            <input type="number" placeholder="" id="experience" class="form-control" name="profile[experience]" min="1" max="50" value="{!! old('experience',$user->profile->experience) !!}"/>
                                        </div>
                                        <span class="help-block">{{ $errors->first('profile.experience', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if(Sentinel::getUser()->isMemberMarineAccount())
                        <h3>Company Info</h3>
                        <div class="form-group {{ $errors->first('profile.company_name', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Company Name
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[company_name]" class="form-control" value="{!! old('profile.company_name', $user->profile->company_name) !!}"></div>
                                    <span class="help-block">{{ $errors->first('profile.company_name', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.company_email', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Company Email
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[company_email]" class="form-control" value="{!! old('profile.company_email', $user->profile->company_email) !!}"></div>
                                    <span class="help-block">{{ $errors->first('profile.company_email', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.company_state', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        State
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        @php
                                            $states = \Igaster\LaravelCities\Geo::getCountry($user->profile->company_country ?? 'US')
                                                ->children()
                                                ->orderBy('name')
                                                ->pluck('name', 'name')
                                                ->all();
                                        @endphp
                                        {{ Form::select('profile[company_state]', $states, null, ['class' => 'form-control', 'placeholder' => '']) }}</div>
                                    <span class="help-block">{{ $errors->first('profile.company_state', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.company_city', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        City
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[company_city]" class="form-control" value="{!! old('profile.company_city', $user->profile->company_city) !!}"></div>
                                    <span class="help-block">{{ $errors->first('profile.company_city', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.company_address', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Company Address
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[company_address]" class="form-control" value="{!! old('profile.company_address', $user->profile->company_address) !!}"></div>
                                    <span class="help-block">{{ $errors->first('profile.company_address', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{--@if(Sentinel::getUser()->isMemberAccount())
                        <h3>Website Links</h3>
                        <div class="form-group {{ $errors->first('profile.link_website', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Website
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_website]" autocomplete="off" class="form-control" value="{!! old('profile.link_website', $user->profile->link_website) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_website', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.link_blog', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Blog
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_blog]" autocomplete="off" class="form-control" value="{!! old('profile.link_blog', $user->profile->link_blog) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_blog', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.link_youtube', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Youtube
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_youtube]" autocomplete="off" class="form-control" value="{!! old('profile.link_youtube', $user->profile->link_youtube) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_youtube', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.link_pinterest', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Pinterest
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_pinterest]" autocomplete="off" class="form-control" value="{!! old('profile.link_pinterest', $user->profile->link_pinterest) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_pinterest', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.link_twitter', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Twitter
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_twitter]" autocomplete="off" class="form-control" value="{!! old('profile.link_twitter', $user->profile->link_twitter) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_twitter', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.link_facebook', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Facebook
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_facebook]" autocomplete="off" class="form-control" value="{!! old('profile.link_facebook', $user->profile->link_facebook) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_facebook', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.link_linkedin', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Linkedin
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_linkedin]" autocomplete="off" class="form-control" value="{!! old('profile.link_linkedin', $user->profile->link_linkedin) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_linkedin', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.link_google_plus', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Google+
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_google_plus]" autocomplete="off" class="form-control" value="{!! old('profile.link_google_plus', $user->profile->link_google_plus) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_google_plus', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->first('profile.link_instagram', 'has-error') }}">
                            <div class="row">
                                <div class="col-lg-2 col-12">
                                    <label class="control-label">
                                        Instagram
                                    </label>
                                </div>
                                <div class="col-lg-10 col-12">
                                    <div class="input-group input-group-append">
                                        <input type="text" placeholder="" name="profile[link_instagram]" autocomplete="off" class="form-control" value="{!! old('profile.link_instagram', $user->profile->link_instagram) !!}">
                                    </div>
                                    <span class="help-block">{{ $errors->first('profile.link_instagram', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif--}}
                    <div class="form-group">
                        <div class="row">
                            <div class="offset-2 col-10">
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </div>
                    {!!  Form::close()  !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/moment/js/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/user-account.js') }}"></script>
@stop
