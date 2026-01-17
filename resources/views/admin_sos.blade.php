<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SOS Monitor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Dark Theme Styles */
        * { margin:0; padding:0; box-sizing:border-box; font-family: "Segoe UI", sans-serif; }
        body { background: radial-gradient(circle at top, #0b2740, #061726); color:#eaf6ff; display: flex; flex-direction: column; min-height: 100vh; }

        /* Layout */
        .admin-layout { display:flex; flex: 1; }
        .sidebar { width:240px; background:#0c3558; padding:20px; flex-shrink:0; display: flex; flex-direction: column; }
        .brand { font-size:22px; font-weight:700; margin-bottom:30px; color: white; }

        .sidebar nav { display: flex; flex-direction: column; flex-grow: 1; }
        .sidebar nav a, .sidebar nav button { display:flex; align-items:center; gap:12px; padding:12px 14px; margin-bottom:8px; color:#cfe9ff; text-decoration:none; border-radius:10px; transition:.3s; background: none; border: none; width: 100%; font-size: 16px; cursor: pointer; text-align: left; }
        .sidebar nav a:hover, .sidebar nav a.active, .sidebar nav button:hover { background:#134a73; color: white; }
        .logout-form { margin-top: auto; }
        .sidebar nav button.logout { background:#133b55; color: #ff5d4f; }

        /* Main Content */
        .main { flex:1; padding:28px; display: flex; flex-direction: column; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }

        /* SOS CARDS */
        .sos-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }

        .sos-card {
            background: rgba(231, 76, 60, 0.1); /* Red tint */
            border: 2px solid #e74c3c;
            border-radius: 15px;
            padding: 20px;
            position: relative;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(231, 76, 60, 0); }
            100% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
        }

        .sos-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid rgba(231, 76, 60, 0.3); padding-bottom: 10px; }
        .sos-title { color: #ff5d4f; font-weight: bold; font-size: 18px; text-transform: uppercase; letter-spacing: 1px; }

        .info-row { margin-bottom: 8px; display: flex; align-items: center; gap: 10px; font-size: 15px; }
        .info-row i { width: 20px; color: #ff5d4f; text-align: center; }

        .empty-state { text-align: center; margin-top: 50px; opacity: 0.6; }
        .empty-state i { font-size: 60px; color: #2ecc71; margin-bottom: 20px; }

        .footer { text-align: center; padding: 20px; color: rgba(255, 255, 255, 0.4); font-size: 13px; margin-top: auto; }
    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand">🌊 OceanEye</div>
        <nav>
            <nav>
                <a href="{{ route('admin.dashboard') }}" class="active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a href="{{ route('admin.users') }}"><i class="fa-solid fa-users"></i> Users</a>
                <a href="{{ route('admin.boats') }}"><i class="fa-solid fa-ship"></i> Boats</a>
                <a href="{{ route('admin.sos') }}"><i class="fa-solid fa-triangle-exclamation"></i> SOS Monitor</a>
                <a href="{{ route('admin.map') }}"><i class="fa-solid fa-map"></i> Map</a>

                <a href="{{ route('admin.analytics') }}"><i class="fa-solid fa-chart-simple"></i> Analytics</a>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <h1 style="color: #ff5d4f;"><i class="fa-solid fa-tower-broadcast"></i> Live SOS Monitor</h1>
            <div style="color: #cfe9ff;"><i class="fa-regular fa-clock"></i> Live Feed</div>
        </header>

        <div class="sos-container">
            @forelse($alerts as $alert)
                <div class="sos-card">
                    <div class="sos-header">
                        <span class="sos-title">Emergency Signal</span>
                        <i class="fa-solid fa-rss fa-fade" style="color: red;"></i>
                    </div>

                    <div class="info-row">
                        <i class="fa-solid fa-user"></i>
                        <strong>{{ $alert->user->name }}</strong>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-phone"></i>
                        {{ $alert->user->mobile }}
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $alert->location }}
                    </div>
                    <div class="info-row">
                        <i class="fa-regular fa-clock"></i>
                        {{ $alert->created_at->diffForHumans() }}
                    </div>

                    <div style="margin-top: 15px; font-size: 13px; color: #ffa500; background: rgba(0,0,0,0.2); padding: 8px; border-radius: 5px;">
                        Status: Waiting for Coast Guard
                    </div>
                </div>
            @empty
            @endforelse
        </div>

        @if($alerts->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-shield-heart"></i>
                <h2>All Clear</h2>
                <p>No distress signals detected in the network.</p>
            </div>
        @endif

        <div class="footer">Team The Error Squad. All rights reserved.</div>
    </main>

</div>

</body>
</html>
