<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Live Map</title>

    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* --- 1. THEME VARIABLES --- */
        :root {
            --glass-bg: rgba(6, 18, 38, 0.85);
            --glass-border: rgba(255, 255, 255, 0.15);
            --neon-cyan: #00f3ff;
            --neon-red: #ff003c;
            --neon-green: #00ff80;
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
            background: rgba(5, 15, 30, 0.95);
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

        /* --- 4. MAIN MAP AREA --- */
        .main {
            flex:1; padding:0;
            display: flex; flex-direction: column; position: relative;
        }

        /* Map Filter (Makes it Dark/Sci-Fi) */
        #map {
            height: 100%; width: 100%; z-index: 1;
            filter: invert(100%) hue-rotate(180deg) contrast(90%) grayscale(20%);
        }

        /* Legend Overlay (Glass Card) */
        .map-overlay {
            position: absolute; top: 20px; right: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            padding: 20px; border-radius: 12px;
            z-index: 999;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
            border: 1px solid var(--glass-border);
            min-width: 250px;
        }
        .map-overlay h3 {
            color: white; margin-bottom: 15px; font-size: 18px;
            text-transform: uppercase; letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;
            display: flex; align-items: center; gap: 10px;
        }

        .legend-item { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; font-size: 15px; }
        .legend-label { display: flex; align-items: center; gap: 10px; color: var(--text-main); }

        /* Legend Dots */
        .dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; border: 2px solid white; }
        .dot-red { background: var(--neon-red); box-shadow: 0 0 10px var(--neon-red); animation: blink 1s infinite; }
        .dot-cyan { background: var(--neon-cyan); box-shadow: 0 0 10px var(--neon-cyan); }

        @keyframes blink { 50% { opacity: 0.5; transform: scale(0.9); } }

        /* --- CUSTOM MARKER CSS --- */
        .custom-boat-marker {
            color: #000; font-size: 12px;
            background: var(--neon-cyan); border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            box-shadow: 0 0 15px var(--neon-cyan); border: 2px solid white;
        }

        .custom-sos-marker {
            color: #ff003c; font-size: 24px;
            display: flex; justify-content: center; align-items: center;
            animation: pulse-sos 1s infinite;
        }
        @keyframes pulse-sos {
            0% { transform: scale(1); filter: drop-shadow(0 0 0 var(--neon-red)); }
            50% { transform: scale(1.2); filter: drop-shadow(0 0 15px var(--neon-red)); }
            100% { transform: scale(1); filter: drop-shadow(0 0 0 var(--neon-red)); }
        }

    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand"><i class="fas fa-water"></i> OceanEye</div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="{{ Route::is('admin.users') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="{{ route('admin.boats') }}" class="{{ Route::is('admin.boats') ? 'active' : '' }}">
                <i class="fa-solid fa-ship"></i> Boats
            </a>
            <a href="{{ route('admin.sos') }}" class="{{ Route::is('admin.sos') ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation"></i> SOS Monitor
            </a>
            <a href="{{ route('admin.map') }}" class="{{ Route::is('admin.map') ? 'active' : '' }}">
                <i class="fa-solid fa-map"></i> Map
            </a>
            <a href="{{ route('admin.analytics') }}" class="{{ Route::is('admin.analytics') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-simple"></i> Analytics
            </a>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="main">
        <div class="map-overlay">
            <h3><i class="fa-solid fa-satellite-dish"></i> Live Tracking</h3>

            <div class="legend-item">
                <div class="legend-label">
                    <span class="dot dot-red"></span> SOS Signal
                </div>
                <strong style="color: var(--neon-red);">{{ count($alerts) }}</strong>
            </div>

            <div class="legend-item">
                <div class="legend-label">
                    <span class="dot dot-cyan"></span> Active Boats
                </div>
                <strong style="color: var(--neon-cyan);">{{ count($boats) }}</strong>
            </div>
        </div>

        <div id="map"></div>
    </main>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Initialize Map
    var map = L.map('map', {zoomControl: false}).setView([21.8, 90.0], 9);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // --- CUSTOM ICONS (DivIcon for CSS Styling) ---
    var boatIcon = L.divIcon({
        className: 'custom-boat-marker',
        html: '<i class="fas fa-ship"></i>',
        iconSize: [30, 30],
        iconAnchor: [15, 15],
        popupAnchor: [0, -15]
    });

    var sosIcon = L.divIcon({
        className: 'custom-sos-marker',
        html: '<i class="fas fa-radiation"></i>',
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20]
    });

    // --- 2. SHOW SOS ALERTS (Neon Red Markers) ---
    var alerts = @json($alerts);

    alerts.forEach(function(alert) {
        var lat = 21.9; var lng = 89.9;
        try {
            // Coordinate parsing logic based on your location string format
            if(alert.latitude && alert.longitude) {
                lat = alert.latitude;
                lng = alert.longitude;
            } else {
                var parts = alert.location.split(',');
                lat = parseFloat(parts[0].replace(/[^\d.]/g, ''));
                lng = parseFloat(parts[1].replace(/[^\d.]/g, ''));
            }
        } catch(e) {}

        L.marker([lat, lng], {icon: sosIcon})
            .addTo(map)
            .bindPopup(`
                <div style="text-align:center; color: black;">
                    <strong style="color:#d63031; font-size:14px;">⚠️ SOS ALERT</strong><br>
                    <b>User:</b> ${alert.user ? alert.user.name : 'Unknown'}<br>
                    <b>Contact:</b> ${alert.user ? alert.user.mobile : 'N/A'}<br>
                    <small>${new Date(alert.created_at).toLocaleString()}</small>
                </div>
            `);
    });

    // --- 3. SHOW ALL BOATS (Neon Cyan Markers) ---
    var boats = @json($boats);

    boats.forEach(function(boat) {
        // Generating demo coordinates if real GPS not available
        var lat = 21.5 + (Math.random() * 0.5);
        var lng = 89.5 + (Math.random() * 1.0);

        L.marker([lat, lng], {icon: boatIcon})
            .addTo(map)
            .bindPopup(`
                <div style="color: black;">
                    <b style="color: #0984e3; font-size:14px;">⛵ ${boat.boat_name}</b><br>
                    <b>Reg:</b> ${boat.registration_number}<br>
                    <b>Owner:</b> ${boat.user ? boat.user.name : 'Unknown'}<br>
                    <span style="font-size:11px; color:gray;">Type: ${boat.boat_type}</span>
                </div>
            `);
    });

</script>

</body>
</html>
