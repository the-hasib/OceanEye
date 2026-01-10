<!DOCTYPE html>
<html lang="en">
<head>
    <title>Coast Guard Dashboard</title>
    @include('styles')
</head>
<body>
<div class="navbar" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="logo">OceanEye <span style="font-size:12px;">(GUARD)</span></div>
    <div class="user-info">
        Officer {{ Auth::user()->name }}
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<div class="container">
    <h2>Patrol Dashboard</h2>
    <div class="grid-container">
        <div class="card">
            <i class="fas fa-binoculars"></i>
            <h3>Live Patrol</h3>
            <p>Monitor active vessels.</p>
        </div>
        <div class="card">
            <i class="fas fa-search-location"></i>
            <h3>Verify Vessel</h3>
            <p>Check license & registration.</p>
        </div>
        <div class="card sos">
            <i class="fas fa-exclamation-circle"></i>
            <h3>Active Alerts</h3>
            <p>Respond to SOS signals.</p>
        </div>
    </div>
</div>
</body>
</html>
