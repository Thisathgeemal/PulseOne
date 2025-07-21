<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <script src="https://kit.fontawesome.com/bc9b460555.js" crossorigin="anonymous"></script>

</head>
<body class="bg-gray-100 text-gray-900">

    <div class="flex">
        <!-- Sidebar -->
        @include('memberDashboard.components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-h-screen">
            <!-- Header -->
            @include('memberDashboard.components.header')

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
