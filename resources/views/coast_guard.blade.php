<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>OceanEye – Coast Guard Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        /* --- 1. GLOBAL STYLES --- */
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Segoe UI', sans-serif; }
        body { background:#071c33; color:#e8f6ff; }

        /* --- 2. TOPBAR --- */
        .topbar {
            display:flex; justify-content:space-between; align-items: center;
            padding:16px 22px; background:#0b3558; border-bottom:1px solid #134e7a;
        }
        .logout-btn {
            background: #ff5b5b; color: white; border: none; padding: 8px 15px;
            border-radius: 5px; cursor: pointer; font-weight: bold; margin-left: 15px;
        }
        .user-info { display: flex; align-items: center; gap: 10px; }

        /* --- 3. LAYOUT --- */
        .layout { display:grid; grid-template-columns:260px 1fr; gap:20px; padding:20px; }
        .side-cards { display:flex; flex-direction:column; gap:15px; }

        /* Info Cards */
        .info-card {
            background:#0f2a44; padding:16px; border-radius:14px;
            box-shadow:0 10px 30px rgba(0,0,0,.35);
        }
        .info-card.alert { border-left:5px solid #ff5b5b; }
        .info-card.success { border-left:5px solid #2ecc71; }
        .info-card h3 { margin-bottom:8px; color:#6fd3ff; }

        /* Map */
        .map-section { background:#061a2e; border-radius:18px; overflow:hidden; }
        #bdMap { height:500px; width:100%; }

        /* --- 4. TABLE SECTION --- */
        .table-section { padding:20px; }
        .table-section h2 { margin-bottom:12px; }

        table { width:100%; border-collapse:collapse; background:#0f2a44; border-radius:12px; overflow:hidden; }
        thead { background:#123e63; }
        th,td { padding:12px; text-align:left; }
        tbody tr { border-top:1px solid #1d4f78; cursor:pointer; transition: 0.2s; }
        tbody tr:hover { background:#174a74; }

        /* Buttons */
        .btn-resolve {
            background: #2ecc71; color: white; border: none; padding: 6px 12px;
            border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 13px; display: inline-block;
        }
        .btn-resolve:hover { background: #27ae60; }

        footer { text-align:center; padding:16px; font-size:13px; opacity:.7; }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand">🌊 <strong>OceanEye</strong> – Coast Guard Unit</div>
    <div class="user-info">
        <span id="time">Loading...</span>
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

<footer>© 2026 Team The Error Squad. All rights reserved.</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Live Time
    function updateTime(){ document.getElementById("time").innerText = new Date().toLocaleString("en-GB"); }
    updateTime(); setInterval(updateTime, 1000);

    // 2. Map Init
    const map = L.map("bdMap").setView([21.8, 89.5], 9);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { attribution:"© OceanEye" }).addTo(map);

    // 3. Load Bangladesh Borders
    fetch("https://raw.githubusercontent.com/johan/world.geo.json/master/countries/BGD.geo.json")
        .then(res=>res.json())
        .then(data=>{ L.geoJSON(data,{ style:{ color:"#626769ff", weight:2, fillOpacity:0.05 } }).addTo(map); });


    // ===============================================
    // 🚤 PART 1: SHOW BOATS (Priority Logic)
    // ===============================================
    const allBoats = @json($boats);
    console.log("Boats Loaded:", allBoats); // Check console for data

    const boatIcon = L.divIcon({
        className: 'custom-boat',
        html: `<div style="background: #3bbcff; color: #061726; width: 25px; height: 25px; border-radius: 50%; display: flex; justify-content: center; align-items: center; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5); font-size: 12px;"><i class="fa-solid fa-ship"></i></div>`,
        iconSize: [25, 25], iconAnchor: [12, 12]
    });

    if (allBoats && allBoats.length > 0) {
        allBoats.forEach(boat => {
            // Simulation: Generate random location near Sundarbans
            let lat = 21.50 + (Math.random() * 0.5);
            let lng = 89.00 + (Math.random() * 1.0);

            L.marker([lat, lng], {icon: boatIcon}).addTo(map)
                .bindPopup(`
                    <div style="text-align: left;">
                        <strong style="color: #0c3558;">${boat.boat_name}</strong><br>
                        <span style="font-size: 11px; color: gray;">Reg: ${boat.registration_number}</span><br>
                        <span style="color: #2ecc71; font-size: 10px;">● Normal Status</span>
                    </div>
                `);
        });
    } else {
        console.log("⚠️ No boats found to display.");
    }


    // ===============================================
    // 🚨 PART 2: SOS ALERTS (Safe Mode)
    // ===============================================
    const alerts = @json($active_alerts);
    const sosIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
    });

    alerts.forEach(alert => {
        try {
            // Check if location exists before parsing
            if(alert.location) {
                let parts = alert.location.split(',');
                let lat = parseFloat(parts[0]);
                let lng = parseFloat(parts[1]);

                if(!isNaN(lat) && !isNaN(lng)) {
                    L.marker([lat, lng], {icon: sosIcon}).addTo(map)
                        .bindPopup(`<b>🆘 SOS #${alert.id}</b><br>User: ${alert.user.name}<br>Loc: ${alert.location}`);
                }
            }
        } catch (error) {
            console.error("Skipping bad SOS data:", error);
        }
    });


    // ===============================================
    // 🏢 PART 3: STATION MARKER
    // ===============================================
    const stationIcon = L.divIcon({
        className: 'station-icon',
        html: `<div style="background: #ff4757; color: white; width: 30px; height: 30px; border-radius: 5px; display: flex; justify-content: center; align-items: center; box-shadow: 0 0 10px rgba(0,0,0,0.5);"><i class="fa-solid fa-building-shield"></i></div>`,
        iconSize: [30, 30], iconAnchor: [15, 15]
    });
    L.marker([22.492, 89.260], {icon: stationIcon}).addTo(map).bindPopup("<b>Coast Guard Base</b><br>Paikgacha Unit");

</script>

</body>
</html>
