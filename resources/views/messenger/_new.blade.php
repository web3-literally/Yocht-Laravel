@if(Sentinel::getUser()->hasAccess(['messages.new']))
    @section('header_styles')
        @parent
        <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
        <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
    @stop

    <div id="conversation-search" class="ml-4">
        <select class="select"></select>
    </div>

    @section('footer_scripts')
        @parent
        <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
        <script>
            var formatMemeber = function (item) {
                if (item.id === '' || item.loading) {
                    return $('<span>' + item.text + '</span>');
                }
                if (item.group) {
                    return $('<span class="select-members-group-item">' + item.text + '</span>');
                }

                var el = $(
                    '<span class="select-member-item"><img src="'+item.thumb+'"><span class="name">' + item.text + '</span></span>'
                );

                return el;
            };

            $("#conversation-search select").select2({
                ajax: {
                    url: "{{ route('account.messages.search-member') }}",
                    dataType: 'json',
                    cache: true
                },
                placeholder: "Find a member",
                minimumInputLength: 1,
                delay: 650,
                theme: "bootstrap",
                templateResult: formatMemeber,
                templateSelection: formatMemeber,
                width: '400px',
            }).on('select2:select', function (e) {
                var item = e.params.data;
                $("#conversation-search .select2-container").loading();
                window.location.href = item.url;
            });
        </script>
    @stop
@endif
