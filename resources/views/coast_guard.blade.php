<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>OceanEye – Coast Guard Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        [cite_start]/* --- CSS FROM YOUR FILE [cite: 5] --- */
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
        tbody tr { border-top:1px solid #1d4f78; cursor:pointer; }
        tbody tr:hover { background:#174a74; }

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
            <p id="sosCount">0 boats</p>
        </div>

        <div class="info-card">
            <h3>🚤 Patrol Units</h3>
            <p>Active: 3</p>
            <p>Available: 2</p>
        </div>
    </aside>

    <section class="map-section">
        <div id="bdMap"></div>
    </section>
</main>

<section class="table-section">
    <h2>🚨 SOS Alert List</h2>
    <table>
        <thead>
        <tr>
            <th>Boat ID</th>
            <th>Fisherman</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody id="sosTable">
        <tr>
            <td colspan="6" style="text-align: center; color: gray;">No Active SOS Alerts</td>
        </tr>
        </tbody>
    </table>
</section>

<footer>
    © 2026 Team The Error Squad. All rights reserved.
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // ⏰ Live Time
    function updateTime(){
        document.getElementById("time").innerText = new Date().toLocaleString("en-GB");
    }
    updateTime();
    setInterval(updateTime,1000);

    // 🌍 MAP INIT
    const map = L.map("bdMap").setView([23.685, 90.3563], 7);

    // Tiles
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{
        attribution:"© OpenStreetMap"
    }).addTo(map);

    // 🇧🇩 Bangladesh Boundary
    fetch("https://raw.githubusercontent.com/johan/world.geo.json/master/countries/BGD.geo.json")
        .then(res=>res.json())
        .then(data=>{
            L.geoJSON(data,{
                style:{
                    color:"#626769ff",
                    weight:2,
                    fillOpacity:0.05
                }
            }).addTo(map);
        });

    // 📌 Marker Logic (Backend Ready)
    let markers = {};

    document.getElementById("sosTable").addEventListener("click", e=>{
        const row = e.target.closest("tr");
        if(!row) return;

        const lat = row.dataset.lat;
        const lng = row.dataset.lng;

        if(!lat || !lng) return;

        map.setView([lat,lng], 11);

        if(markers[row.dataset.boat]){
            markers[row.dataset.boat].openPopup();
        }
    });
</script>

</body>
</html>
