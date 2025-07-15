<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PULSEONE</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    <h2>Select a Role</h2>
    @foreach ($roles as $role)
        <form method="POST" action="{{ route('select.role') }}" style="display:inline-block; margin:10px;">
            @csrf
            <input type="hidden" name="role_id" value="{{ $role->id }}">
            <button type="submit">{{ $role->name }}</button>
        </form>
    @endforeach
</body>
</html>
