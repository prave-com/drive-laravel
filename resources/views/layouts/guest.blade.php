<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <script src="{{ asset('assets/js/pass.js') }}"></script>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/bootstrap.js', 'resources/sass/app.scss'])
</head>

<body class="font-sans bg-[#10453A] text-gray-900 antialiased m-0 h-screen flex flex-col">
    <header class="w-full px-4 py-2">
        <a href="/"
            class="no-underline flex flex-row gap-2 font-bold text-base md:text-xl text-[#E8E8E8] after:bg-[#E8E8E8] after:h-0.5 after:w-32 after:md:w-64 after:bg-[#E8E8E8] items-center">
            Drive Laravel
        </a>
    </header>

    <main class="container flex justify-center items-center flex-grow-1"
        style="min-height: calc(100vh - 80px); padding: 0 20px;">
        <div class="row"
            style="width: 900px; height: 515px; border-radius: 10px; background: #fff; padding: 0; box-shadow: 5px 5px 10px 1px rgba(0, 0, 0, 0.2);">
            <div class="col-md-6 side-image"
                style="background-image: url('/assets/img/login.png'); background-position: center; background-size: cover; background-repeat: no-repeat; position: relative;">
                <!-- Side Image -->
            </div>

            <div class="col-md-6 d-flex justify-content-center align-items-center position-relative">
                {{ $slot }}
            </div>
        </div>
    </main>

    <!-- Footer adjusted to remain at the bottom of the page -->
    <footer class="flex justify-end text-[#E8E8E8] text-xs md:text-base p-1 pr-4 mt-auto">
        &copy; {{ date('Y') }} Drive Laravel. All rights reserved.
    </footer>

</body>

</html>
