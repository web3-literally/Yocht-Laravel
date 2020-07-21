<!-- Shortcut -->
<div class="nav_icons">
    <ul class="sidebar_threeicons">
        @if(Sentinel::getUser()->hasAccess(['admin.pages']))
            <li>
                <a href="{{ route('admin.pages.create') }}" title="@lang('pages/title.addpage')">
                    <i class="livicon" data-name="doc-portrait" data-c="#418BCA" data-hc="#418BCA" data-s="25"></i>
                </a>
            </li>
        @endif
        @if(Sentinel::getUser()->hasAccess(['admin.blog']))
            <li>
                <a href="{{ route('admin.blog.create') }}" title="@lang('blog/title.add-blog')">
                    <i class="livicon" data-name="doc-landscape" data-c="#F89A14" data-hc="#F89A14" data-s="25"></i>
                </a>
            </li>
        @endif
        {{--<li>
            <a href="{{ route('admin.shop.products.create') }}" title="Add New Product">
                <i class="livicon" data-name="thumbnails-big" data-c="#EF6F6C" data-hc="#EF6F6C" data-s="25"></i>
            </a>
        </li>--}}
        @if(Sentinel::getUser()->hasAccess(['admin.users']))
            <li>
                <a href="{{ route('admin.users.create') }}" title="@lang('users/modal.adduser')">
                    <i class="livicon" data-name="user" data-c="#6CC66C" data-hc="#6CC66C" data-s="25"></i>
                </a>
            </li>
        @endif
    </ul>
</div>