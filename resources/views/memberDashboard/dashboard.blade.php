<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @if(Auth::check())
        <h1>Welcome, {{ Auth::user()->first_name }}!</h1>
    @else
        <h1>Welcome, Guest!</h1>
    @endif
    <p>You are now logged in as a member.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger">
            Logout
        </button>
    </form>

</body>
</html>