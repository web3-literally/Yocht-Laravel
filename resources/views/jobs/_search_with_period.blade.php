<div id="jobs-search" class="form-style">
    {!! Form::open(['url' => route('account.jobs.index'), 'method' => 'GET']) !!}
    {!! Form::hidden('tab', request('tab')) !!}
    {!! Form::text('jobs-search', request('jobs-search'), ['class' => 'form-control', 'placeholder' => 'Search', 'autocomplete' => 'off']) !!}
    <div class="results d-none">
        <ul class="results-list dropdown-menu" role="menu" aria-labelledby="dropdownMenu"></ul>
    </div>
    {!! Form::close() !!}
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $('#jobs-search').each(function (i, el) {
                var block = $(el);
                var formBlock = $('form', block);
                var resultsBlock = $('.results', block);
                var timer = null;
                var ajax = null;

                block.find('input[name=jobs-search]').on('keyup', function (e) {
                    if (ajax) {
                        ajax.abort();
                        ajax = null;
                    }
                    if (timer) {
                        clearTimeout(timer);
                    }
                    timer = setTimeout(function () {
                        ajax = $.ajax({
                            url: "{{ route('account.jobs.search') }}",
                            cache: false,
                            data: formBlock.serialize(),
                            success: function (results) {
                                resultsBlock.find('ul li').remove();
                                if (results) {
                                    resultsBlock.removeClass('d-none');
                                    var resultsListBlock = resultsBlock.find('ul');
                                    for (var title in results) {
                                        if (results[title] instanceof Object) {
                                            var liEl = $('<li class="dropdown-submenu">');
                                            liEl.append($('<a class="dropdown-item dropdown-toggle">').text(title));
                                            var subUlEl = $('<ul class="dropdown-menu">');
                                            for (var subtitle in results[title]) {
                                                var subLiEl = $('<li>');
                                                subLiEl.append($('<a class="dropdown-item">').text(subtitle));
                                                subUlEl.append(subLiEl);
                                            }
                                            liEl.append(subUlEl);
                                            resultsListBlock.append(liEl);
                                        } else {
                                            var liEl = $('<li>');
                                            liEl.append($('<a class="dropdown-item">').text(title));
                                            resultsListBlock.append(liEl);
                                        }
                                    }
                                } else {
                                    resultsBlock.addClass('d-none');
                                }
                            },
                            error: function () {
                                resultsBlock.find('ul li').remove();
                                resultsBlock.addClass('d-none');
                            }
                        });
                    }, 500);
                });

                resultsBlock.on('click', '.dropdown-item:not(.dropdown-toggle)', function () {
                    $('input[name=jobs-search]', block).val($(this).text());
                    resultsBlock.addClass('d-none');
                    formBlock.submit();
                    return false;
                });

                $(block).click(function (e) {
                    e.stopPropagation();
                });
                block.find('input[name=jobs-search]').focus(function (e) {
                    if ($('li', resultsBlock).length) {
                        resultsBlock.removeClass('d-none');
                    }
                });
                $(document).click(function () {
                    resultsBlock.addClass('d-none');
                });

                block.on('click', '.dropdown-menu a.dropdown-toggle', function (e) {
                    if (!$(this).next().hasClass('show')) {
                        $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                    }
                    var $subMenu = $(this).next(".dropdown-menu");
                    $subMenu.toggleClass('show');
                    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
                        $('.dropdown-submenu .show').removeClass("show");
                    });
                    return false;
                });
            });
        });
    </script>
@endsection