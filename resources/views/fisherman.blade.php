<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fisherman Dashboard</title>
    @include('styles')
</head>
<body>
<div class="navbar">
    <div class="logo">OceanEye</div>
    <div class="user-info">
        {{ Auth::user()->name }}
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<div class="container">
    <h2>Fisherman Dashboard</h2>
    <div class="grid-container">
        <div class="card sos">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>SOS Alert</h3>
            <p>Send emergency signal.</p>
        </div>
        <div class="card">
            <i class="fas fa-cloud-sun-rain"></i>
            <h3>Weather</h3>
            <p>Check storm warnings.</p>
        </div>
        <div class="card">
            <i class="fas fa-fish"></i>
            <h3>Log Catch</h3>
            <p>Daily fishing report.</p>
        </div>
    </div>
</div>
</body>
</html>
