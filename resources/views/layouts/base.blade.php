<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/img/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/img/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/img/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/img/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/img/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/img/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    {!! SEO::generate() !!}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/style.css') }}" media="screen">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/print.css') }}" media="print">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/all.css') }}"/>
    <script id="app" type="application/json">@json(['base_url' => url('/')])</script>
    <script>
        App = JSON.parse(document.getElementById('app').innerHTML);
    </script>
    @yield('header_styles')
    @section('header_scripts')
    @show
</head>
<body class="{{trim(preg_replace(["/[\n\r]/", '/\s{2,}/'], ' ', View::yieldContent('page_class')))}}">
    @section('body')
        @yield('header')

        @section('main')
            <main role="main" class="{{trim(preg_replace(["/[\n\r]/", '/\s{2,}/'], ' ', View::yieldContent('content_class')))}}">
                @yield('content_block')

                @section('footer-block')
                    <div class="footer-block-outer container">
                        <div class="footer-text footer-block">
                            <div class="d-flex row">
                                <div class="flex-column footer-brand col-md-2 col-sm-12">
                                    <a class="navbar-brand" href="{{ route('home') }}">
                                        <img src="{{  asset('assets/img/frontend/logo-blue.png')  }}" alt="{{ Setting::get('copyright') }}">
                                    </a>
                                </div>
                                <div class="flex-column footer-about-excerpt col-md-2 col-sm-12">
                                    @widget('AboutExcerpt')
                                    @widget('FollowUs')
                                </div>
                                <div class="flex-column footer- col-md-5 col-sm-12">
                                    @widget('FooterMenu')
                                </div>
                                <div class="flex-column footer-contact col-md-3 col-sm-12">
                                    @widget('Contact')
                                    @widget('JoinTodayBanner')
                                </div>
                            </div>
                        </div>
                    </div>
                @show
            </main>
        @show
    @show

    <footer class="footer">
        <div class="d-flex justify-content-center align-items-center">
            <div class="centered-block">
                @widget('Copyright')
            </div>
        </div>
    </footer>

    @yield('google_map_script')
    <script type="text/javascript" src="{{ asset('assets/js/frontend/scripts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/bootbox.min.js') }}"></script>
    @section('footer_scripts')
    @show
    @yield('detect_location')
</body>
</html>
