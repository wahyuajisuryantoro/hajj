<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>HAJJ Member</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Premium Bootstrap v5.1.3 Landing Page Template" />
    <meta name="keywords" content="bootstrap v5.1.3, premium, marketing, multipurpose" />
    <meta content="Themesdesign" name="author" />

    <!-- fevicon -->
    <link rel="shortcut icon" href="{{ asset('assets_landing/images/favicon.ico') }}">

    <!-- Bootstrap css -->
    <link rel="stylesheet" href="{{ asset('assets_landing/css/bootstrap.min.css') }}" type="text/css" />

    <!-- animation -->
    <link rel="stylesheet" href="{{ asset('assets_landing/css/aos.css') }}" />

    <!-- swiper -->
    <link rel="stylesheet" href="{{ asset('assets_landing/css/swiper-bundle.min.css') }}">

    <!-- Icon -->
    <link rel="stylesheet" href="{{ asset('assets_landing/css/materialdesignicons.min.css') }}" type="text/css" />

    <!-- css -->
    <link rel="stylesheet" href="{{ asset('assets_landing/css/style.min.css') }}" type="text/css" />

    <!-- colors -->
    <link href="{{ asset('assets_landing/css/colors/default.css') }}" rel="stylesheet" type="text/css" id="color-opt" />


</head>

<body data-bs-spy="scroll" data-bs-target="#navbar" data-bs-offset="71">

    @include('landing.partials.navbar')

    @yield('content')
    {{-- @include('landing.partials.footer') --}}

    <!-- Style switcher -->
    <div id="style-switcher" onclick="toggleSwitcher()" style="left: -189px;">
        <div>
            <h6>Select your color</h6>
            <ul class="pattern list-unstyled mb-0">
                <li>
                    <a class="color1" href="javascript: void(0);" onclick="setColor('default')"></a>
                </li>
                <li>
                    <a class="color2" href="javascript: void(0);" onclick="setColor('success')"></a>
                </li>
                <li>
                    <a class="color3" href="javascript: void(0);" onclick="setColor('warning')"></a>
                </li>
                <li>
                    <a class="color4" href="javascript: void(0);" onclick="setColor('blue')"></a>
                </li>
                <li>
                    <a class="color5" href="javascript: void(0);" onclick="setColor('info')"></a>
                </li>
                <li>
                    <a class="color6" href="javascript: void(0);" onclick="setColor('purple')"></a>
                </li>
            </ul>
        </div>
        <div class="bottom">
            <a href="javascript: void(0);" class="settings rounded-end"><i class="mdi mdi-cog mdi-spin"></i></a>
        </div>
    </div>
    <!-- end switcher-->

    <!--start back-to-top-->
    <button onclick="topFunction()" id="back-to-top">
        <i class="mdi mdi-arrow-up"></i>
    </button>
    <!--end back-to-top-->

    <!--Custom js-->
    <script src="{{ asset('assets_landing/js/counter.js') }}"></script>

    <!--Bootstrap Js-->
    <script src="{{ asset('assets_landing/js/bootstrap.bundle.min.js') }}"></script>

    <!-- animation -->
    <script src="{{ asset('assets_landing/js/aos.js') }}"></script>

    <!-- swiper js -->
    <script src="{{ asset('assets_landing/js/swiper-bundle.min.js') }}"></script>

    <!-- contact -->
    <script src="{{ asset('assets_landing/js/contact.js') }}"></script>

    <!-- App Js -->
    <script src="{{ asset('assets_landing/js/app.js') }}"></script>
</body>

</html>
