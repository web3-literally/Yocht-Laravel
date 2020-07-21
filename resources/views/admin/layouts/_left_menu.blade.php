<ul id="menu" class="page-sidebar-menu">
    <li {!! (Request::is('admin') ? 'class="active"' : '') !!}>
        <a href="{{ route('admin.dashboard') }}">
            <i class="livicon" data-name="dashboard" data-size="18" data-c="#ffffff" data-hc="#ffffff" data-loop="true"></i>
            <span class="title">@lang('general.dashboard')</span>
        </a>
    </li>

    @if(Sentinel::getUser()->hasAccess(['admin.menus']))
        <li {!! (Request::is('admin/menus*') ? 'class="active"' : '') !!}>
            <a href="{{ URL::route('admin.menus.index') }}">
                <i class="livicon" data-name="tree" data-size="18" data-c="#6CC66C" data-hc="#6CC66C" data-loop="true"></i>
                <span class="title">Menus</span>
            </a>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.pages']))
        <li {!! (Request::is('admin/pages*') ? 'class="active"' : '') !!}>
            <a href="{{ URL::to('admin/pages/create') }}" class="pull-right" title="@lang('pages/title.create')">
                <i class="fa fa-plus-circle"></i>
            </a>
            <a href="{{ URL::to('admin/pages') }}">
                <i class="livicon" data-name="doc-portrait" data-size="18" data-c="#418BCA" data-hc="#418BCA" data-loop="true"></i>
                <span class="title">@lang('pages/title.pages')</span>
            </a>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.news']))
        <li class="{{ Request::is('admin/news*') ? 'active' : '' }}">
            <a href="#">
                <i class="livicon" data-name="notebook" data-size="18" data-c="#F89A14" data-hc="#F89A14" data-loop="true"></i>
                <span class="title">@lang('news.news')</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="sub-menu">
                <li {!! ((Request::is('admin/news/*') || !Request::is('admin/news/source/*')) ? 'class="active"' : '') !!}>
                    <a href="{{ route('admin.news.create') }}" class="action pull-right" title="@lang('news.create')">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{{ route('admin.news.index') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('news.news')
                    </a>
                </li>
                @if(Sentinel::getUser()->hasAccess(['admin.news.sources']))
                    <li {!! (Request::is('admin/news/sources/*') ? 'class="active"' : '') !!}>
                        <a href="{!! route('admin.news.sources.index') !!}">
                            <i class="fa fa-angle-double-right"></i>
                            @lang('news.sources')
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.blog']))
        <li {!! (Request::is('admin/blog*') ? 'class="active"' : '') !!}>
            <a href="#">
                <i class="livicon" data-name="doc-landscape" data-size="18" data-c="#1DA1F2" data-hc="#1DA1F2" data-loop="true"></i>
                <span class="title">@lang('blog/title.blog')</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="sub-menu">
                <li {!! ((Request::is('admin/blog') || Request::is('admin/blog/*')) ? 'class="active"' : '') !!}>
                    <a href="{{ URL::to('admin/blog/create') }}" class="action pull-right" title="@lang('blog/title.create')">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{{ URL::to('admin/blog') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('blog/title.blogs')
                    </a>
                </li>
                <li {!! (Request::is('admin/blogcategory') || Request::is('admin/blogcategory/*') ? 'class="active"' : '') !!}>
                    <a href="{{ URL::to('admin/blogcategory/create') }}" class="action pull-right" title="@lang('blogcategory/title.create')">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{{ URL::to('admin/blogcategory') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('blog/title.blogcategories')
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.events']))
        <li {!! (Request::is('admin/events*') ? 'class="active"' : '') !!}>
            <a href="#">
                <i class="livicon" data-c="#EF6F6C" data-hc="#EF6F6C" data-name="calendar" data-size="18" data-loop="true"></i>
                <span class="title">@lang('events.events')</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="sub-menu">
                <li {!! ((Request::is('admin/events*') && !Request::is('admin/events/categories*')) ? 'class="active"' : '') !!}>
                    {{--<a href="{{ URL::to('admin/events/create') }}" class="action pull-right" title="@lang('events.create')">
                        <i class="fa fa-plus-circle"></i>
                    </a>--}}
                    <a href="{{ URL::to('admin/events') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('events.events')
                    </a>
                </li>
                <li {!! (Request::is('admin/events/categories*') ? 'class="active"' : '') !!}>
                    <a href="{{ URL::to('admin/events/categories/create') }}" class="action pull-right" title="@lang('events.create_category')">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{{ URL::to('admin/events/categories') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('events.categories')
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.users']))
        <li {!! (Request::is('admin/users*') || Request::is('admin/user*') || Request::is('admin/deleted_users') || Request::is('admin/groups*') || Request::is('admin/positions*') || Request::is('admin/specializations*') ? 'class="active"' : '') !!}>
            <a href="#">
                <i class="livicon" data-name="user" data-size="18" data-c="#6CC66C" data-hc="#6CC66C" data-loop="true"></i>
                <span class="title">@lang('users/title.users')</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="sub-menu">
                <li {!! (Request::is('admin/users*') ? 'class="active" id="active"' : '') !!}>
                    <a href="{{ URL::to('admin/users/create') }}" class="action pull-right" title="Add New User">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{{ URL::to('admin/users') }}">
                        <i class="fa fa-angle-double-right"></i>
                        Users
                    </a>
                </li>
                <li {!! (Request::is('admin/deleted_users') ? 'class="active" id="active"' : '') !!}>
                    <a href="{{ URL::to('admin/deleted_users') }}">
                        <i class="fa fa-angle-double-right"></i>
                        Deleted Users
                    </a>
                </li>
                @if(Sentinel::getUser()->hasAccess(['admin.users.groups']))
                    <li {!! (Request::is('admin/groups*') ? 'class="active" id="active"' : '') !!}>
                        <a href="{{ URL::to('admin/groups/create') }}" class="action pull-right" title="Add New Group">
                            <i class="fa fa-plus-circle"></i>
                        </a>
                        <a href="{{ URL::to('admin/groups') }}">
                            <i class="fa fa-angle-double-right"></i>
                            Groups
                        </a>
                    </li>
                @endif
                <li class="{{ Request::is('admin/positions*') ? 'active' : '' }}">
                    <a href="{{ URL::route('admin.positions.index') }}">
                        <i class="fa fa-angle-double-right"></i>
                        Positions
                    </a>
                </li>
                <li class="{{ Request::is('admin/specializations*') ? 'active' : '' }}">
                    <a href="{!! route('admin.specializations.index') !!}">
                        <i class="fa fa-angle-double-right"></i>
                        Specializations
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.jobs']))
        <li {!! (Request::is('admin/jobs*') ? 'class="active"' : '') !!}>
            <a href="{{ route('admin.jobs.index') }}">
                <i class="livicon" data-name="hammer" data-size="18" data-c="#418BCA" data-hc="#418BCA" data-loop="true"></i>
                <span class="title">@lang('jobs.jobs')</span>
                @php $publishedJobs = \App\Models\Jobs\Job::published()->count() @endphp
                <span class="badge badge-danger" title="Published {{ $publishedJobs }} jobs">{{ $publishedJobs }}</span>
            </a>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.classifieds']))
        <li class="{{ Request::is('admin/classifieds/*') ? 'active' : '' }}">
            <a href="{!! route('admin.classifieds.index') !!}">
                <i class="livicon" data-c="#EF6F6C" data-hc="#EF6F6C" data-name="tag" data-size="18" data-loop="true"></i>
                <span class="title">@lang('classifieds.classifieds')</span>
                @php $publishedClassifieds = \App\Models\Classifieds\Classifieds::published()->count() @endphp
                <span class="badge badge-danger" title="Published {{ $publishedClassifieds }} classifieds">{{ $publishedClassifieds }}</span>
            </a>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.vessels']))
        <li {!! (Request::is('admin/vessels*') ? 'class="active"' : '') !!}>
            <a href="#">
                <i class="livicon" data-c="#6CC66C" data-hc="#6CC66C" data-name="flag" data-size="18" data-loop="true"></i>
                <span class="title">@lang('vessels.vessels')</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="{{ Request::is('admin/vessel/manufacturers*') ? 'active' : '' }}">
                    <a href="{{ route('admin.vessels.manufacturers.create') }}" class="action pull-right" title="Add New Manufacturer">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{!! route('admin.vessels.manufacturers.index') !!}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('vessels.manufacturers')
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.services']))
        <li {!! (Request::is('admin/services*') ? 'class="active"' : '') !!}>
            <a href="#">
                <i class="livicon" data-c="#1DA1F2" data-hc="#1DA1F2" data-name="gear" data-size="18" data-loop="true"></i>
                <span class="title">@lang('services.services')</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="{{ Request::is('admin/services*') && !Request::is('admin/services/categories*') ? 'active' : '' }}">
                    <a href="{{ route('admin.services.create') }}" class="action pull-right" title="Add New Service">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{!! route('admin.services.index') !!}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('services.services')
                    </a>
                </li>
                <li class="{{ Request::is('admin/services/categories*') ? 'active' : '' }}">
                    <a href="{{ route('admin.services.categories.create') }}" class="action pull-right" title="Add New Category">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{!! route('admin.services.categories.index') !!}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('services.categories')
                    </a>
                </li>
            </ul>
        </li>
    @endif

    {{--@if(Sentinel::getUser()->hasAccess(['shop']))
        <li {!! (Request::is('admin/shop*') ? 'class="active"' : '') !!}>
            <a href="#">
                <i class="livicon" data-name="shopping-cart" data-size="18" data-c="#EF6F6C" data-hc="#EF6F6C" data-loop="true"></i>
                <span class="title">@lang('shop.shop')</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="sub-menu">
                <li {!! (Request::is('admin/shop/products*') ? 'class="active" id="active"' : '') !!}>
                    <a href="{{ URL::route('admin.shop.products.create') }}" class="action pull-right" title="Add New Product">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a href="{{ URL::route('admin.shop.products.index') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('shop.products')
                    </a>
                </li>
                <li {!! (Request::is('admin/shop/orders*') ? 'class="active" id="active"' : '') !!}>
                    <a href="{{ URL::route('admin.shop.orders.index') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('shop.orders')
                    </a>
                </li>
            </ul>
        </li>
    @endif--}}

    @if(Sentinel::getUser()->hasAccess(['admin.reviews']))
        <li class="{{ Request::is('admin/reviews*') ? 'active' : '' }}">
            <a href="{!! route('admin.reviews.index') !!}">
                <i class="livicon" data-c="#EF6F6C" data-hc="#EF6F6C" data-name="comment" data-size="18" data-loop="true"></i>
                @lang('reviews.reviews')
            </a>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['admin.billing']))
        <li {!! (Request::is('admin/billing*') ? 'class="active"' : '') !!}>
            <a href="#">
                <i class="livicon" data-name="money" data-size="18" data-c="#6CC66C" data-hc="#6CC66C" data-loop="true"></i>
                <span class="title">@lang('billing.billing')</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="sub-menu">
                <li {!! (Request::is('admin/billing/plans') ? 'class="active" id="active"' : '') !!}>
                    <a href="{{ URL::to('admin/billing/plans') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('billing.plans')
                    </a>
                </li>
                <li {!! (Request::is('admin/billing/subscriptions') ? 'class="active" id="active"' : '') !!}>
                    <a href="{{ URL::to('admin/billing/subscriptions') }}">
                        <i class="fa fa-angle-double-right"></i>
                        @lang('billing.subscriptions')
                    </a>
                </li>
            </ul>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['dev']))
        <li {!! (Request::is('admin/generator_builder') ? 'class="active"' : '') !!}>
            <a href="{{ URL::to('admin/generator_builder') }}">
                <i class="livicon" data-name="shield" data-size="18" data-c="#F89A14" data-hc="#F89A14"
                   data-loop="true"></i>
                Generator Builder
            </a>
        </li>

        <li {!! (Request::is('admin/log_viewers') || Request::is('admin/log_viewers/logs')  ? 'class="active"' : '') !!}>
            <a href="{{  URL::to('admin/log_viewers') }}">
                <i class="livicon" data-name="help" data-size="18" data-c="#1DA1F2" data-hc="#1DA1F2" data-loop="true"></i>
                Log Viewer
            </a>
        </li>
    @endif

    @if(Sentinel::getUser()->hasAccess(['audit']))
        <li {!! (Request::is('admin/activity_log') ? 'class="active"' : '') !!}>
            <a href="{{  URL::to('admin/activity_log') }}">
                <i class="livicon" data-name="eye-open" data-size="18" data-c="#F89A14" data-hc="#F89A14" data-loop="true"></i>
                Activity Log
            </a>
        </li>
    @endif
    {{--<li {!! (Request::is('admin/googlemaps') || Request::is('admin/vectormaps') || Request::is('admin/advancedmaps') ? 'class="active"' : '') !!}>
        <a href="#">
            <i class="livicon" data-name="map" data-c="#67C5DF" data-hc="#67C5DF" data-size="18"
               data-loop="true"></i>
            <span class="title">Maps</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="sub-menu">
            <li {!! (Request::is('admin/googlemaps') ? 'class="active"' : '') !!}>
                <a href="{{ URL::to('admin/googlemaps') }}">
                    <i class="fa fa-angle-double-right"></i>
                    Google Maps
                </a>
            </li>
            <li {!! (Request::is('admin/vectormaps') ? 'class="active"' : '') !!}>
                <a href="{{ URL::to('admin/vectormaps') }}">
                    <i class="fa fa-angle-double-right"></i>
                    Vector Maps
                </a>
            </li>
            <li {!! (Request::is('admin/advancedmaps') ? 'class="active"' : '') !!}>
                <a href="{{ URL::to('admin/advancedmaps') }}">
                    <i class="fa fa-angle-double-right"></i>
                    Advanced Maps
                </a>
            </li>
        </ul>
    </li>--}}
    <!-- Menus generated by CRUD generator -->
    @include('admin/layouts/menu')
</ul>