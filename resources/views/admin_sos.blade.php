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
        .sos-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }

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

        .info-row { margin-bottom: 8px; display: flex; align-items: center; gap: 10px; font-size: 15px; color: #eaf6ff; }
        .info-row i { width: 20px; color: #ff5d4f; text-align: center; }

        /* New Styles for Boat Info */
        .boat-info { background: rgba(59, 188, 255, 0.1); border: 1px solid #3bbcff; padding: 10px; border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .boat-icon { font-size: 24px; color: #3bbcff; }

        .btn-map {
            display: block; width: 100%; text-align: center;
            background: #3bbcff; color: #061726;
            padding: 10px; border-radius: 8px; margin-top: 15px;
            text-decoration: none; font-weight: bold;
        }
        .btn-map:hover { background: #29a0e0; }

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
            <a href="{{ route('admin.dashboard') }}"
               class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>

            <a href="{{ route('admin.users') }}"
               class="{{ Route::is('admin.users') || Route::is('admin.approve') || Route::is('admin.reject') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Users
            </a>

            <a href="{{ route('admin.boats') }}"
               class="{{ Route::is('admin.boats') ? 'active' : '' }}">
                <i class="fa-solid fa-ship"></i> Boats
            </a>

            <a href="{{ route('admin.sos') }}"
               class="{{ Route::is('admin.sos') ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation"></i> SOS Monitor
            </a>

            <a href="{{ route('admin.map') }}"
               class="{{ Route::is('admin.map') ? 'active' : '' }}">
                <i class="fa-solid fa-map"></i> Map
            </a>

            <a href="{{ route('admin.analytics') }}"
               class="{{ Route::is('admin.analytics') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-simple"></i> Analytics
            </a>

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

                    @if($alert->boat)
                        <div class="boat-info">
                            <i class="fa-solid fa-ship boat-icon"></i>
                            <div>
                                <div style="font-weight: bold; color: #3bbcff;">{{ $alert->boat->boat_name }}</div>
                                <div style="font-size: 12px; opacity: 0.8;">{{ $alert->boat->registration_number }}</div>
                            </div>
                        </div>
                    @else
                        <div class="boat-info" style="border-color: gray;">
                            <i class="fa-solid fa-question boat-icon" style="color: gray;"></i>
                            <div style="color: gray;">Unknown Boat</div>
                        </div>
                    @endif

                    <div class="info-row">
                        <i class="fa-solid fa-user"></i>
                        <strong>Owner: {{ $alert->user->name }}</strong>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-phone"></i>
                        {{ $alert->user->mobile ?? $alert->user->email }}
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>
                            Lat: {{ number_format($alert->latitude, 4) }},
                            Lng: {{ number_format($alert->longitude, 4) }}
                        </span>
                    </div>
                    <div class="info-row">
                        <i class="fa-regular fa-clock"></i>
                        {{ $alert->created_at->diffForHumans() }}
                    </div>

                    <a href="{{ route('admin.map') }}" class="btn-map">
                        <i class="fa-solid fa-map-location-dot"></i> View on Map
                    </a>
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
