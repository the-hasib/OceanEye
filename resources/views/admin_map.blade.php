<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Live Map</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* Dark Theme Styles */
        * { margin:0; padding:0; box-sizing:border-box; font-family: "Segoe UI", sans-serif; }
        body { background: radial-gradient(circle at top, #0b2740, #061726); color:#eaf6ff; display: flex; flex-direction: column; min-height: 100vh; }

        .admin-layout { display:flex; flex: 1; }
        .sidebar { width:240px; background:#0c3558; padding:20px; flex-shrink:0; display: flex; flex-direction: column; }
        .brand { font-size:22px; font-weight:700; margin-bottom:30px; color: white; }
        .sidebar nav { display: flex; flex-direction: column; flex-grow: 1; }
        .sidebar nav a, .sidebar nav button { display:flex; align-items:center; gap:12px; padding:12px 14px; margin-bottom:8px; color:#cfe9ff; text-decoration:none; border-radius:10px; transition:.3s; background: none; border: none; width: 100%; font-size: 16px; cursor: pointer; text-align: left; }
        .sidebar nav a:hover, .sidebar nav a.active, .sidebar nav button:hover { background:#134a73; color: white; }
        .logout-form { margin-top: auto; }
        .sidebar nav button.logout { background:#133b55; color: #ff5d4f; }

        .main { flex:1; padding:0; display: flex; flex-direction: column; position: relative; } /* Padding 0 for full map */

        /* Map Container */
        #map { height: 100%; width: 100%; z-index: 1; }

        /* Map Legend Overlay */
        .map-overlay {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(12, 53, 88, 0.9);
            padding: 15px;
            border-radius: 10px;
            z-index: 999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
            max-width: 300px;
        }
        .map-overlay h3 { color: #fff; margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 5px; }
        .legend-item { display: flex; align-items: center; gap: 10px; margin-bottom: 5px; font-size: 14px; }
        .dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
        .dot-red { background: #ff4757; box-shadow: 0 0 5px #ff4757; }
        .dot-blue { background: #3bbcff; }

    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand">🌊 OceanEye</div>
        <nav>
            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}"><i class="fa-solid fa-users"></i> Users</a>
            <a href="{{ route('admin.boats') }}"><i class="fa-solid fa-ship"></i> Boats</a>
            <a href="{{ route('admin.sos') }}"><i class="fa-solid fa-triangle-exclamation"></i> SOS Monitor</a>

            <a href="{{ route('admin.map') }}" class="active"><i class="fa-solid fa-map"></i> Map</a>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="main">
        <div class="map-overlay">
            <h3><i class="fa-solid fa-radar"></i> Live Tracking</h3>
            <div class="legend-item">
                <span class="dot dot-red"></span> SOS Signal (Distress)
            </div>
            <div class="legend-item">
                <span class="dot dot-blue"></span> Active Boats (Safe)
            </div>
            <p style="margin-top:10px; font-size:12px; opacity:0.7;">
                Map updates automatically based on incoming signals.
            </p>
        </div>

        <div id="map"></div>
    </main>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Initialize Map (Center: Bay of Bengal)
    var map = L.map('map').setView([21.5, 90.0], 8);

    // 2. Add Map Tiles (OpenStreetMap - Dark Modeish look available but standard is fine)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // 3. Custom Icons
    var sosIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
    });

    // 4. Add SOS Markers from Database
    var alerts = @json($alerts);

    alerts.forEach(function(alert) {
        // Parse Location String "21.9N, 89.9E" -> [21.9, 89.9]
        // This is a simple parser assuming the format is consistent
        var lat = 21.9; // Default fallback
        var lng = 89.9;

        // Try to parse if format is like "21.9, 89.9" or similar
        try {
            var parts = alert.location.split(',');
            lat = parseFloat(parts[0].replace(/[^\d.]/g, ''));
            lng = parseFloat(parts[1].replace(/[^\d.]/g, ''));
        } catch(e) {
            console.log("Error parsing location");
        }

        // Add Marker
        L.marker([lat, lng], {icon: sosIcon})
            .addTo(map)
            .bindPopup(`
                <b>SOS ALERT!</b><br>
                User: ${alert.user.name}<br>
                Mobile: ${alert.user.mobile}<br>
                Time: ${new Date(alert.created_at).toLocaleTimeString()}
            `)
            .openPopup();
    });

</script>

</body>
</html>
