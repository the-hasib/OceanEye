<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>OceanEye – Coast Guard Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        /* --- CSS FROM YOUR FILE --- */
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Segoe UI', sans-serif; }

        body { background:#071c33; color:#e8f6ff; }

        /* TOP BAR */
        .topbar {
            display:flex; justify-content:space-between; align-items: center;
            padding:16px 22px; background:#0b3558; border-bottom:1px solid #134e7a;
        }

        /* Logout Button Style */
        .logout-btn {
            background: #ff5b5b; color: white; border: none; padding: 8px 15px;
            border-radius: 5px; cursor: pointer; font-weight: bold; margin-left: 15px;
        }
        .user-info { display: flex; align-items: center; gap: 10px; }

        /* LAYOUT */
        .layout {
            display:grid; grid-template-columns:260px 1fr; gap:20px; padding:20px;
        }

        /* SIDE CARDS */
        .side-cards { display:flex; flex-direction:column; gap:15px; }

        .info-card {
            background:#0f2a44; padding:16px; border-radius:14px;
            box-shadow:0 10px 30px rgba(0,0,0,.35);
        }
        .info-card.alert { border-left:5px solid #ff5b5b; }
        .info-card.success { border-left:5px solid #2ecc71; } /* New Success Style */
        .info-card h3 { margin-bottom:8px; color:#6fd3ff; }

        /* MAP */
        .map-section { background:#061a2e; border-radius:18px; overflow:hidden; }
        #bdMap { height:460px; width:100%; }

        /* TABLE */
        .table-section { padding:20px; }
        .table-section h2 { margin-bottom:12px; }

        table {
            width:100%; border-collapse:collapse; background:#0f2a44;
            border-radius:12px; overflow:hidden;
        }
        thead { background:#123e63; }
        th,td { padding:12px; text-align:left; }
        tbody tr { border-top:1px solid #1d4f78; cursor:pointer; transition: 0.2s; }
        tbody tr:hover { background:#174a74; }

        /* Action Button Style */
        .btn-resolve {
            background: #2ecc71; color: white; border: none; padding: 6px 12px;
            border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 13px; display: inline-block;
        }
        .btn-resolve:hover { background: #27ae60; }

        /* FOOTER */
        footer { text-align:center; padding:16px; font-size:13px; opacity:.7; }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand">
        🌊 <strong>OceanEye</strong> – Coast Guard Unit
    </div>

    <div class="user-info">
        <span id="time"></span>
        <span style="color: #6fd3ff;">| {{ Auth::user()->name ?? 'Officer' }}</span>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</header>

<main class="layout">
    <aside class="side-cards">
        <div class="info-card">
            <h3>🌦 Weather</h3>
            <p>Temp: <span id="temp">28</span> °C</p>
            <p>Wind: <span id="wind">12</span> km/h</p>
        </div>

        <div class="info-card">
            <h3>🌊 Waves</h3>
            <p>Height: <span id="wave">Normal</span></p>
            <p>Status: Safe</p>
        </div>

        <div class="info-card alert">
            <h3>🚨 Active SOS</h3>
            <p id="sosCount" style="font-size: 24px; font-weight: bold;">
                {{ $active_alerts->count() }} <span style="font-size: 14px; font-weight: normal;">Signals</span>
            </p>
        </div>

        <div class="info-card success">
            <h3>🎖 Missions Done</h3>
            <p style="font-size: 24px; font-weight: bold;">
                {{ $mission_count }} <span style="font-size: 14px; font-weight: normal;">Saved</span>
            </p>
        </div>
    </aside>

    <section class="map-section">
        <div id="bdMap"></div>
    </section>
</main>

@if(session('success'))
    <div style="margin: 0 20px; padding: 15px; background: #2ecc71; color: white; border-radius: 8px; text-align: center;">
        {{ session('success') }}
    </div>
@endif

<section class="table-section">
    <h2>🚨 SOS Alert List</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Fisherman</th>
            <th>Location</th> <th>Time</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="sosTable">
        @forelse($active_alerts as $alert)
            {{-- Parsing Location String --}}
            @php
                $parts = explode(',', $alert->location);
                $lat = trim($parts[0] ?? '0');
                $lng = trim($parts[1] ?? '0');
            @endphp

            <tr data-lat="{{ $lat }}" data-lng="{{ $lng }}" data-boat="{{ $alert->id }}">
                <td>#{{ $alert->id }}</td>
                <td>{{ $alert->user->name }} <br><small style="opacity: 0.7;">{{ $alert->user->mobile }}</small></td>
                <td>{{ $alert->location }}</td>
                <td>{{ $alert->created_at->diffForHumans() }}</td>
                <td>
                    <a href="{{ route('sos.resolve', $alert->id) }}"
                       class="btn-resolve"
                       onclick="return confirm('Confirm rescue mission complete?')">
                        <i class="fa-solid fa-check"></i> Mark Rescued
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; color: gray; padding: 20px;">
                    No Active SOS Alerts. The seas are safe.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</section>

<footer>
    © 2026 Team The Error Squad. All rights reserved.
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // ⏰ Live Time Logic
    function updateTime(){
        document.getElementById("time").innerText = new Date().toLocaleString("en-GB");
    }
    updateTime();
    setInterval(updateTime,1000);

    // 🌍 Initialize Map (Centered on Bay of Bengal)
    const map = L.map("bdMap").setView([21.5, 90.0], 8);

    // Add OpenStreetMap Tiles
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{
        attribution:"© OpenStreetMap"
    }).addTo(map);

    // 🇧🇩 Load Bangladesh Boundary (GeoJSON)
    fetch("https://raw.githubusercontent.com/johan/world.geo.json/master/countries/BGD.geo.json")
        .then(res=>res.json())
        .then(data=>{
            L.geoJSON(data,{
                style:{ color:"#626769ff", weight:2, fillOpacity:0.05 }
            }).addTo(map);
        });

    // 📌 Dynamic Marker Logic
    // Convert Laravel Data to JS Array
    const alerts = @json($active_alerts);
    let markers = {};

    // Custom Red Icon for SOS
    var sosIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
    });

    // Loop through alerts and add markers
    alerts.forEach(alert => {
        // Parse "Lat, Lng" string to Float
        let parts = alert.location.split(',');
        let lat = parseFloat(parts[0]);
        let lng = parseFloat(parts[1]);

        if(!isNaN(lat) && !isNaN(lng)) {
            let marker = L.marker([lat, lng], {icon: sosIcon})
                .addTo(map)
                .bindPopup(`
                    <b>🆘 SOS ALERT #${alert.id}</b><br>
                    User: ${alert.user.name}<br>
                    Loc: ${alert.location}<br>
                    <a href="/coastguard/resolve/${alert.id}" style="color: green; font-weight: bold;">Resolve Now</a>
                `);

            // Store marker reference for Table Click event
            markers[alert.id] = marker;
        }
    });

    // 🖱 Table Click Event (Pan to Marker)
    document.getElementById("sosTable").addEventListener("click", e => {
        const row = e.target.closest("tr");
        if(!row) return;

        // Ignore if clicked on the Button directly
        if(e.target.closest('a')) return;

        const lat = row.dataset.lat;
        const lng = row.dataset.lng;
        const id = row.dataset.boat;

        if(lat && lng && lat != 0) {
            map.setView([lat, lng], 10);
            if(markers[id]){
                markers[id].openPopup();
            }
        }
    });
</script>

</body>
</html>
