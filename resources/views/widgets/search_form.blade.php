<div class="search-form-widget">
    {{ Form::open(['route' => 'search', 'id' => $config['id'] ?? 'search-form', 'class' => 'search-form', 'method' => 'GET']) }}
    <div class="input-group mb-3 search-field {{ $errors->first('q', 'has-error') }}">
        {{ Form::text('q', request('q', null), ['class' => 'form-control p-dark', 'autocomplete' => 'off', 'placeholder' => trans('general.search_placeholder'), 'data-min-placeholder' => 'Please enter 3 or more characters']) }}
        <div class="input-group-append">
            <div class="loader"></div>
            <button class="btn btn-outline-secondary icon-search" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
    {!! $errors->first('q', '<span class="help-block">:message</span>') !!}
    <div class="results-container">
        <div class="results d-flex d-flex justify-content-start"></div>
    </div>
    {{ Form::close() }}
</div>

@section('footer_scripts')
    @parent
    <script>
        $('#{{ $config['id'] ?? 'search-form' }}').each(function () {
            var form = $(this);
            var input = form.find('input[name=q]');

            form.find('[type=submit]').on('click', function() {
                var q = String(form.find('input[name=q]').val()).trim();
                if (!(q && q.length >= 3)) {
                    form.find('input[name=q]').focus();
                    return false;
                }
            });

            input.on('focus', function () {
                $(this).data('placeholder', $(this).attr('placeholder'));
                $(this).attr('placeholder', $(this).data('min-placeholder'));
            }).on('blur', function () {
                $(this).attr('placeholder', $(this).data('placeholder'));
            });
        });
        $(function () {
            $('#{{ $config['id'] ?? 'search-form' }}').each(function () {
                var timerId = null;
                var xhr = null;

                var form = $(this);
                var input = form.find('input[name=q]');
                var loader = form.find('.loader');
                var results = form.find('.results-container');

                input.on('blur', function () {
                    if (xhr) {
                        xhr.abort();
                    }
                    results.hide();
                }).on('keyup', function () {
                    if (timerId !== null) {
                        clearTimeout(timerId);
                        timerId = null;
                    }
                    timerId = setTimeout(function () {
                        if (xhr) {
                            xhr.abort();
                        }
                        timerId = null;
                        results.hide();
                        var q = input.val().trim();
                        if (q && q.length >= 3) {
                            loader.show();
                            xhr = $.ajax({
                                dataType: "json",
                                url: "{{ route('quick.search') }}",
                                data: {
                                    'q': q
                                },
                                success: function (data, textStatus, jqXHR) {
                                    var inner = results.find('.results');
                                    inner.html('');

                                    if (data.total) {
                                        var renderBlock = function (block, title) {
                                            if (block.length) {
                                                var ul = $('<ul class="dropdown-menu">');
                                                inner.append(ul);
                                                ul.append($('<li class="nav-item"><label>' + title + '</label></li>'));
                                                for (var i = 0; i < block.length; i++) {
                                                    var li = $('<li class="nav-item">');
                                                    var a = $('<a class="nav-link">');
                                                    a.attr('href', block[i].url);
                                                    a.text(block[i].title);
                                                    li.append(a);
                                                    ul.append(li);
                                                }
                                            }
                                        };

                                        if (data.blog_posts && data.blog_posts.length) {
                                            renderBlock(data.blog_posts, 'Blog Posts');
                                        }
                                        if (data.events && data.events.length) {
                                            renderBlock(data.events, 'Events');
                                        }
                                        if (data.classifieds && data.classifieds.length) {
                                            renderBlock(data.classifieds, 'Classifieds');
                                        }
                                    } else {
                                        inner.append($('<div class="empty">No results</div>'));
                                    }

                                    results.css({
                                        position: 'absolute',
                                        top: input.offset().top + input.outerHeight(),
                                        left: input.offset().left,
                                        width: input.outerWidth()
                                    }).show();
                                },
                                complete: function (jqXHR, textStatus) {
                                    loader.hide();
                                }
                            });
                        }
                    }, 450);
                });
            });
        });
    </script>
@stop
