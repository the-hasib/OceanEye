<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>OceanEye – Coast Guard Unit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        /* --- 1. OPTIMIZED THEME SETTINGS --- */
        :root {
            /* High Opacity Background (Faster than Blur) */
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
            overflow-x: hidden;
            display: flex; flex-direction: column;
        }

        /* Simple Dark Overlay (Faster Rendering) */
        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        /* --- 2. TOPBAR --- */
        .topbar {
            background: rgba(5, 15, 30, 0.95); /* Solid transparent look */
            border-bottom: 1px solid var(--glass-border);
            padding: 16px 22px;
            display:flex; justify-content:space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }

        .brand {
            font-size: 24px; font-weight: 700; color: white;
            text-transform: uppercase; letter-spacing: 1px;
            display: flex; align-items: center; gap: 10px;
            text-shadow: 0 0 10px var(--neon-cyan);
        }

        .user-info { display: flex; align-items: center; gap: 10px; font-weight: 600; color: var(--neon-cyan); font-size: 14px; }

        .logout-btn {
            background: rgba(255, 0, 60, 0.1);
            border: 1px solid var(--neon-red);
            color: var(--neon-red); padding: 8px 15px; border-radius: 5px;
            font-weight: 700; cursor: pointer; transition: 0.2s;
            text-transform: uppercase; font-size: 12px; margin-left: 15px;
        }
        .logout-btn:hover { background: var(--neon-red); color: white; }

        /* --- 3. LAYOUT --- */
        .layout {
            display:grid;
            grid-template-columns: 260px 1fr;
            gap: 20px;
            padding: 20px;
            max-width: 100%; width: 100%;
        }
        .side-cards { display:flex; flex-direction:column; gap:15px; }

        /* --- 4. PANELS (Removed Blur for Speed) --- */
        .info-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            padding: 16px;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: 0.2s;
        }
        /* Top Highlight for 3D Effect */
        .info-card::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }
        .info-card:hover {
            transform: translateY(-3px);
            border-color: rgba(0, 243, 255, 0.4);
        }

        .info-card h3 {
            margin-bottom: 8px;
            color: var(--neon-cyan); font-size: 18px;
            letter-spacing: 0.5px; text-transform: uppercase;
        }
        .info-card p { font-size: 14px; color: var(--text-main); margin-bottom: 4px; }

        /* Accents */
        .info-card.alert { border-left: 4px solid var(--neon-red); }
        .info-card.success { border-left: 4px solid var(--neon-green); }

        /* --- 5. FORMS --- */
        select {
            width: 100%; padding: 8px;
            background: rgba(255, 255, 255, 0.1); /* Lighter bg */
            border: 1px solid rgba(255,255,255,0.2); border-radius: 5px;
            color: white; outline: none; font-size: 13px;
            transition: 0.3s;
        }
        select:focus { border-color: var(--neon-cyan); background: rgba(0,0,0,0.5); }

        .btn-broadcast {
            background: rgba(255, 0, 60, 0.15); border: 1px solid var(--neon-red); color: var(--neon-red);
            padding: 8px 12px; border-radius: 5px; cursor: pointer; transition: 0.3s;
        }
        .btn-broadcast:hover { background: var(--neon-red); color: white; }

        /* --- 6. MAP (Fast Render) --- */
        .map-section {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px; overflow:hidden;
            height: 565px;
            position: relative;
        }
        #bdMap { width:100%; height:100%; filter: invert(90%) hue-rotate(180deg) contrast(85%); }

        /* --- 7. TABLE --- */
        .table-section { grid-column: 1 / -1; padding-top: 0; }
        .table-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border); border-radius: 12px; padding: 25px;
        }
        .table-card h2 { margin-bottom: 12px; color: white; font-size: 20px; text-transform: uppercase; letter-spacing: 1px; }

        table { width:100%; border-collapse:collapse; margin-top: 5px; }
        thead { border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { text-align: left; padding: 12px; color: var(--neon-cyan); font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; color: white; }
        tbody tr:hover { background: rgba(255,255,255,0.05); }

        /* Buttons */
        .btn-resolve {
            background: transparent; border: 1px solid var(--neon-green); color: var(--neon-green);
            padding: 6px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; font-weight: 700;
            transition: 0.3s; display: inline-block; text-transform: uppercase; letter-spacing: 1px;
        }
        .btn-resolve:hover { background: var(--neon-green); color: black; }

        /* Animation */
        @keyframes blink { 50% { opacity: 0.5; } }
        .blink-red { animation: blink 1s infinite; color: var(--neon-red); text-shadow: 0 0 10px var(--neon-red); }

        footer { text-align:center; padding:16px; font-size:13px; opacity:.7; color: white; grid-column: 1 / -1; }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand">🌊 <strong>OceanEye</strong> – Coast Guard Unit</div>
    <div class="user-info">
        <span id="time" style="font-family: monospace;">Loading...</span>
        <span>| {{ Auth::user()->name ?? 'Officer' }}</span>
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
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-size: 16px; margin-bottom: 2px;">Temp: <span id="temp" style="font-weight: bold; color: white;">28</span> °C</p>
                    <p style="font-size: 14px; opacity: 0.8;">Wind: <span id="wind">12</span> km/h</p>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 0;">

            <form action="{{ route('coastguard.warning') }}" method="POST">
                @csrf
                <label style="font-size: 12px; color: var(--neon-cyan); margin-bottom: 5px; display: block; letter-spacing: 0.5px;">SELECT CONDITION:</label>
                <div style="display: flex; gap: 5px;">
                    <select name="signal" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="0" style="color: #00ff80;">🟢 Sea is Safe (Normal)</option>
                        <option value="1">Signal 1</option>
                        <option value="2">Signal 2</option>
                        <option value="3">Signal 3</option>
                        <option value="4">Signal 4 (Caution)</option>
                        <option value="5">Signal 5 (Danger)</option>
                        <option value="6">Signal 6 (Danger)</option>
                        <option value="7">Signal 7 (Danger)</option>
                        <option value="8">Signal 8 (Great Danger)</option>
                        <option value="9">Signal 9 (Great Danger)</option>
                        <option value="10">Signal 10 (Great Danger)</option>
                    </select>

                    <button type="submit" onclick="return confirm('Are you sure you want to broadcast this signal?')" class="btn-broadcast">
                        <i class="fa-solid fa-tower-broadcast"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="info-card">
            <h3>🌊 Sea Status</h3>

            @if(isset($current_signal) && $current_signal >= 5)
                <div class="blink-red" style="font-weight: bold;">
                    <p style="font-size: 20px; margin-top: 5px;"><i class="fa-solid fa-triangle-exclamation"></i> DANGER</p>
                    <p style="font-size: 14px; opacity: 0.9;">Signal No: {{ $current_signal }}</p>
                </div>
            @elseif(isset($current_signal) && $current_signal >= 1)
                <div style="color: var(--neon-yellow); font-weight: bold;">
                    <p style="font-size: 20px; margin-top: 5px;"><i class="fa-solid fa-bell"></i> WARNING</p>
                    <p style="font-size: 14px; opacity: 0.9;">Signal No: {{ $current_signal }}</p>
                </div>
            @else
                <div style="color: var(--neon-green); font-weight: bold;">
                    <p style="font-size: 20px; margin-top: 5px;"><i class="fa-solid fa-shield-halved"></i> SAFE</p>
                    <p style="font-size: 14px; opacity: 0.9;">No Active Warnings</p>
                </div>
            @endif
        </div>

        <div class="info-card alert">
            <h3>🚨 Active SOS</h3>
            <p id="sosCount" style="font-size: 24px; font-weight: bold; color: var(--neon-red); text-shadow: 0 0 10px var(--neon-red);">
                {{ $active_alerts->count() }} <span style="font-size: 14px; font-weight: normal; color: white; text-shadow: none;">Signals</span>
            </p>
        </div>

        <div class="info-card success">
            <h3>🎖 Missions Done</h3>
            <p style="font-size: 24px; font-weight: bold; color: var(--neon-green); text-shadow: 0 0 10px var(--neon-green);">
                {{ $mission_count }} <span style="font-size: 14px; font-weight: normal; color: white; text-shadow: none;">Saved</span>
            </p>
        </div>
    </aside>

    <section class="map-section">
        <div id="bdMap"></div>
        <div style="position: absolute; bottom: 10px; right: 10px; background: black; color: var(--neon-cyan); padding: 4px 10px; border-radius: 20px; font-size: 11px; border: 1px solid var(--neon-cyan); z-index: 999; box-shadow: 0 0 10px var(--neon-cyan);">
            <span style="animation: blink 1s infinite;">●</span> LIVE
        </div>
    </section>
</main>

@if(session('success'))
    <div style="margin: 0 20px 15px; padding: 15px; background: rgba(0,255,128,0.2); border: 1px solid var(--neon-green); color: var(--neon-green); border-radius: 8px; text-align: center;">
        {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div style="margin: 0 20px 15px; padding: 15px; background: rgba(255,0,60,0.2); border: 1px solid var(--neon-red); color: var(--neon-red); border-radius: 8px; text-align: center; animation: blink 1s infinite;">
        <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
    </div>
@endif

<section class="table-section" style="padding: 0 25px;">
    <div class="table-card">
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
                    <td style="color: var(--neon-red); font-weight: bold;">#{{ $alert->id }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $alert->user->name }}</div>
                        <small style="opacity: 0.7;">{{ $alert->user->mobile }}</small>
                    </td>
                    <td style="font-family: monospace;">{{ $alert->location }}</td>
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
    </div>
</section>

<footer>© 2026 Team The Error Squad. All rights reserved.</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    function updateTime(){ document.getElementById("time").innerText = new Date().toLocaleString("en-GB"); }
    updateTime(); setInterval(updateTime, 1000);

    const map = L.map("bdMap", {zoomControl: false}).setView([21.8, 89.5], 9);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

    fetch("https://raw.githubusercontent.com/johan/world.geo.json/master/countries/BGD.geo.json")
        .then(res=>res.json())
        .then(data=>{ L.geoJSON(data,{ style:{ color:"#626769ff", weight:2, fillOpacity:0.05 } }).addTo(map); });

    const allBoats = @json($boats);
    const boatIcon = L.divIcon({
        className: 'custom-boat-marker',
        html: '<i class="fas fa-ship"></i>',
        iconSize: [24, 24], iconAnchor: [12, 12]
    });

    const style = document.createElement('style');
    style.innerHTML = `
        .custom-boat-marker {
            color: #000; font-size: 10px;
            background: #00f3ff; border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            box-shadow: 0 0 10px #00f3ff; border: 2px solid white;
        }
        .sos-marker {
            color: #ff003c; font-size: 24px;
            display: flex; justify-content: center; align-items: center;
            animation: pulse-sos 1s infinite;
        }
        @keyframes pulse-sos { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.2); opacity: 0.7; } 100% { transform: scale(1); opacity: 1; } }
    `;
    document.head.appendChild(style);

    if (allBoats && allBoats.length > 0) {
        allBoats.forEach(boat => {
            let lat = 21.50 + (Math.random() * 0.5);
            let lng = 89.00 + (Math.random() * 1.0);
            L.marker([lat, lng], {icon: boatIcon}).addTo(map)
                .bindPopup(`<div style="color: black;"><strong>${boat.boat_name}</strong><br><span style="font-size: 11px;">Reg: ${boat.registration_number}</span></div>`);
        });
    }

    const alerts = @json($active_alerts);
    const sosIcon = L.divIcon({
        className: 'sos-marker',
        html: '<i class="fas fa-radiation"></i>',
        iconSize: [30, 30], iconAnchor: [15, 15]
    });

    alerts.forEach(alert => {
        try {
            if(alert.location) {
                let parts = alert.location.split(',');
                let lat = parseFloat(parts[0]);
                let lng = parseFloat(parts[1]);
                if(!isNaN(lat) && !isNaN(lng)) {
                    L.marker([lat, lng], {icon: sosIcon}).addTo(map)
                        .bindPopup(`<b style="color:red;">🆘 SOS #${alert.id}</b><br>User: ${alert.user.name}`);
                }
            }
        } catch (error) { console.error("Skipping bad SOS data:", error); }
    });

    const stationIcon = L.divIcon({
        className: 'station-icon',
        html: `<div style="background: #ff003c; color: white; width: 30px; height: 30px; border-radius: 5px; display: flex; justify-content: center; align-items: center; box-shadow: 0 0 10px #ff003c; border: 1px solid white;"><i class="fa-solid fa-building-shield"></i></div>`,
        iconSize: [30, 30], iconAnchor: [15, 15]
    });
    L.marker([22.492, 89.260], {icon: stationIcon}).addTo(map).bindPopup("<b>Coast Guard Base</b><br>Paikgacha Unit");
</script>

</body>
</html>
