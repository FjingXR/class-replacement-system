<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body style="font-family: sans-serif; padding: 2rem;">
    @php
        $user = Auth::user();
        $name = $user->name;
        $id = $user->loginId();
    @endphp

    @if ($user->isStudent())
        <p>Welcome {{ $name }}, Student ({{ $id }})</p>
    @elseif ($user->isLecturer() && $user->lecturer?->is_pl)
        <p>Welcome {{ $name }}, Programme Leader ({{ $id }})</p>
    @else
        <p>Welcome {{ $name }}, Lecturer ({{ $id }})</p>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="margin-top: 1rem; padding: 0.5rem 1rem;">Log out</button>
    </form>
</body>
</html>
