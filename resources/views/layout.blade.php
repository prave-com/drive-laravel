<!-- resources/views/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Drive Laravel')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/bootstrap.js', 'resources/sass/app.scss'])
</head>

<body>
    <header>
        <!-- Tambahkan navbar atau header lainnya di sini -->
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <!-- Tambahkan footer di sini -->
    </footer>
</body>

</html>
