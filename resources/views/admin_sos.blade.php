<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SOS Monitor</title>

    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* --- 1. THEME VARIABLES (Consistent Across App) --- */
        :root {
            --glass-bg: rgba(6, 18, 38, 0.85);
            --glass-border: rgba(255, 255, 255, 0.15);
            --neon-cyan: #00f3ff;
            --neon-red: #ff003c;
            --neon-green: #00ff80;
            --neon-yellow: #f1c40f;
            --text-main: #ffffff;
            --text-muted: #cbd5e1;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Rajdhani', sans-serif; }

        body {
            background: url("{{ asset('login.jpg') }}") no-repeat center center/cover fixed;
            min-height: 100vh;
            color: var(--text-main);
            display: flex; flex-direction: column;
        }

        /* Dark Overlay */
        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        /* --- 2. LAYOUT --- */
        .admin-layout { display:flex; flex: 1; height: 100vh; overflow: hidden; }

        /* --- 3. SIDEBAR (Glass Style) --- */
        .sidebar {
            width: 260px;
            background: rgba(5, 15, 30, 0.95); /* Solid transparent look */
            border-right: 1px solid var(--glass-border);
            padding: 25px;
            display: flex; flex-direction: column;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
        }

        .brand {
            font-size: 28px; font-weight: 700; margin-bottom: 40px;
            color: white; text-transform: uppercase; letter-spacing: 2px;
            text-shadow: 0 0 10px var(--neon-cyan);
            display: flex; align-items: center; gap: 10px;
        }

        .sidebar nav { display: flex; flex-direction: column; gap: 10px; flex-grow: 1; }

        .sidebar nav a, .sidebar nav button {
            display:flex; align-items:center; gap:15px; padding:12px 15px;
            color: var(--text-muted); text-decoration:none;
            border-radius: 8px; transition: 0.3s; background: transparent; border: 1px solid transparent;
            width: 100%; font-size: 16px; cursor: pointer; text-align: left;
            font-weight: 600; letter-spacing: 0.5px;
        }

        /* Hover & Active States */
        .sidebar nav a:hover, .sidebar nav a.active {
            background: rgba(0, 243, 255, 0.1);
            border-color: var(--neon-cyan);
            color: var(--neon-cyan);
            box-shadow: 0 0 15px rgba(0, 243, 255, 0.1);
        }

        /* Logout Button */
        .logout-form { margin-top: auto; }
        .sidebar nav button.logout {
            color: var(--neon-red);
            border: 1px solid rgba(255, 0, 60, 0.3);
        }
        .sidebar nav button.logout:hover {
            background: var(--neon-red); color: white;
            box-shadow: 0 0 15px var(--neon-red); border-color: var(--neon-red);
        }

        /* --- 4. MAIN CONTENT --- */
        .main {
            flex:1; padding: 30px;
            display: flex; flex-direction: column;
            overflow-y: auto; /* Scrollable content */
        }

        /* TOP BAR */
        .topbar {
            display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;
            background: var(--glass-bg); padding: 15px 25px; border-radius: 12px;
            border: 1px solid var(--glass-border);
        }
        .topbar h1 { font-size: 20px; font-weight: 600; color: var(--neon-red); text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 10px; text-shadow: 0 0 10px rgba(255,0,60,0.3); }
        .time-box { display:flex; gap:10px; align-items:center; font-family: monospace; color: var(--text-muted); }

        /* --- 5. SOS CARDS (Glass Alert Theme) --- */
        .sos-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 25px; }

        .sos-card {
            background: linear-gradient(135deg, rgba(255, 0, 60, 0.15), rgba(6, 18, 38, 0.9));
            border: 1px solid var(--neon-red);
            border-radius: 16px;
            padding: 25px;
            position: relative;
            box-shadow: 0 0 20px rgba(255, 0, 60, 0.2);
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(255, 0, 60, 0.4); border-color: var(--neon-red); }
            70% { box-shadow: 0 0 0 15px rgba(255, 0, 60, 0); border-color: #ff5d4f; }
            100% { box-shadow: 0 0 0 0 rgba(255, 0, 60, 0); border-color: var(--neon-red); }
        }

        .sos-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 0, 60, 0.3); padding-bottom: 15px;
        }
        .sos-title { color: var(--neon-red); font-weight: 700; font-size: 18px; text-transform: uppercase; letter-spacing: 2px; }

        /* Rows */
        .info-row { margin-bottom: 10px; display: flex; align-items: center; gap: 12px; font-size: 15px; color: var(--text-main); }
        .info-row i { width: 20px; color: var(--neon-red); text-align: center; }

        /* Boat Info Box */
        .boat-info {
            background: rgba(0, 243, 255, 0.1);
            border: 1px solid var(--neon-cyan);
            padding: 12px; border-radius: 8px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 15px;
        }
        .boat-icon { font-size: 24px; color: var(--neon-cyan); text-shadow: 0 0 10px var(--neon-cyan); }

        /* Map Button */
        .btn-map {
            display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%;
            background: transparent; color: var(--neon-cyan); border: 1px solid var(--neon-cyan);
            padding: 10px; border-radius: 8px; margin-top: 20px;
            text-decoration: none; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-map:hover { background: var(--neon-cyan); color: black; box-shadow: 0 0 15px var(--neon-cyan); }

        /* Empty State */
        .empty-state { text-align: center; margin-top: 80px; opacity: 0.7; }
        .empty-state i { font-size: 60px; color: var(--neon-green); margin-bottom: 20px; text-shadow: 0 0 20px var(--neon-green); }
        .empty-state h2 { color: var(--neon-green); margin-bottom: 10px; text-transform: uppercase; letter-spacing: 2px; }

        /* Footer */
        .footer { text-align: center; padding: 20px; color: var(--text-muted); font-size: 12px; margin-top: auto; opacity: 0.6; }
    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand"><i class="fas fa-water"></i> OceanEye</div>
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
            <h1><i class="fa-solid fa-tower-broadcast"></i> Live SOS Monitor</h1>
            <div class="time-box">
                <i class="fa-regular fa-clock"></i>
                <span id="liveTime">Loading...</span>
            </div>
        </header>

        <div class="sos-container">
            @forelse($alerts as $alert)
                <div class="sos-card">
                    <div class="sos-header">
                        <span class="sos-title">Emergency Signal</span>
                        <i class="fa-solid fa-rss fa-beat-fade" style="color: var(--neon-red); font-size: 20px;"></i>
                    </div>

                    @if($alert->boat)
                        <div class="boat-info">
                            <i class="fa-solid fa-ship boat-icon"></i>
                            <div>
                                <div style="font-weight: 700; color: white;">{{ $alert->boat->boat_name }}</div>
                                <div style="font-size: 13px; color: var(--text-muted); font-family: monospace;">{{ $alert->boat->registration_number }}</div>
                            </div>
                        </div>
                    @else
                        <div class="boat-info" style="border-color: var(--text-muted); background: rgba(255,255,255,0.05);">
                            <i class="fa-solid fa-question boat-icon" style="color: var(--text-muted); text-shadow: none;"></i>
                            <div style="color: var(--text-muted);">Unknown Boat</div>
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
                        <span style="font-family: monospace; color: var(--neon-yellow);">
                            LAT: {{ number_format($alert->latitude, 4) }},
                            LNG: {{ number_format($alert->longitude, 4) }}
                        </span>
                    </div>
                    <div class="info-row">
                        <i class="fa-regular fa-clock"></i>
                        {{ $alert->created_at->diffForHumans() }}
                    </div>

                    <a href="{{ route('admin.map') }}" class="btn-map">
                        <i class="fa-solid fa-map-location-dot"></i> Locate on Map
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

<script>
    function updateTime(){
        document.getElementById("liveTime").innerText = new Date().toLocaleString("en-GB");
    }
    updateTime();
    setInterval(updateTime,1000);
</script>

</body>
</html>
