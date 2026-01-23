<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fisherman Dashboard - OceanEye</title>

    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* --- 1. THEME VARIABLES --- */
        :root {
            /* [CHANGE] Transparency Increased (0.75 -> 0.35) */
            --glass-bg: rgba(6, 18, 38, 0.35);
            --glass-border: rgba(255, 255, 255, 0.2);
            --neon-cyan: #00f3ff;
            --neon-red: #ff003c;
            --text-main: #ffffff;
            --text-muted: #d1d5db; /* Lighter text for better contrast on glass */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Rajdhani', sans-serif; }

        body {
            background: url("{{ asset('login.jpg') }}") no-repeat center center/cover fixed;
            min-height: 100vh;
            color: var(--text-main);
            overflow-x: hidden;
            display: flex; flex-direction: column;
        }

        /* Lighter Overlay so background image is visible */
        body::before {
            content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.35);
            z-index: -1;
        }

        /* --- 2. NAVBAR --- */
        .navbar {
            background: rgba(5, 11, 20, 0.5); /* More transparent navbar */
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 40px;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }

        .brand-logo {
            font-size: 28px; font-weight: 700; color: white;
            text-transform: uppercase; letter-spacing: 2px;
            display: flex; align-items: center; gap: 10px;
            text-shadow: 0 0 15px var(--neon-cyan);
        }

        .user-info {
            display: flex; align-items: center; gap: 20px;
            font-size: 15px; font-weight: 600; color: var(--neon-cyan);
        }

        .btn-logout {
            background: rgba(255, 0, 60, 0.1);
            border: 1px solid var(--neon-red);
            color: var(--neon-red); padding: 6px 15px; border-radius: 4px;
            font-weight: 700; cursor: pointer; transition: 0.3s;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .btn-logout:hover { background: var(--neon-red); color: white; box-shadow: 0 0 15px var(--neon-red); }

        /* --- 3. DASHBOARD LAYOUT --- */
        .dashboard-container {
            max-width: 1500px; margin: 30px auto; width: 100%; padding: 0 20px;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* --- 4. PANELS (Transparent Glass Box) --- */
        .panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px); /* Frosted glass effect */
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 25px;
            position: relative; overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25); /* Depth shadow */
            transition: 0.4s;
        }
        /* Top sheen for 3D effect */
        .panel::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }
        .panel:hover {
            transform: translateY(-5px);
            background: rgba(6, 18, 38, 0.45); /* Slightly darker on hover */
            border-color: rgba(0, 243, 255, 0.4);
            box-shadow: 0 15px 50px rgba(0, 243, 255, 0.1);
        }

        /* --- 5. GRID SYSTEM --- */
        .top-grid { display: grid; grid-template-columns: 280px 1fr 1.5fr; gap: 25px; margin-bottom: 30px; }

        /* SOS Module */
        .sos-card {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; cursor: pointer;
            background: linear-gradient(160deg, rgba(255, 0, 60, 0.1), rgba(10, 20, 40, 0.4));
            border-color: rgba(255, 0, 60, 0.3);
        }
        .sos-btn {
            width: 90px; height: 90px;
            background: rgba(255, 0, 60, 0.15); border: 2px solid var(--neon-red);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 40px; color: var(--neon-red); margin-bottom: 15px;
            box-shadow: 0 0 20px rgba(255, 0, 60, 0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(255, 0, 60, 0.7); } 70% { box-shadow: 0 0 0 20px rgba(255, 0, 60, 0); } }

        /* Weather Module */
        .weather-panel { display: flex; flex-direction: column; justify-content: space-between; }
        .temp-val { font-size: 64px; font-weight: 700; line-height: 1; color: white; text-shadow: 0 0 20px rgba(255,255,255,0.3); }
        .weather-status { font-size: 18px; color: var(--neon-cyan); text-transform: uppercase; letter-spacing: 2px; }

        /* Signal Badges */
        .signal-box {
            padding: 12px; margin-top: 15px; border-radius: 6px;
            text-align: center; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .sig-safe { background: rgba(0, 255, 128, 0.15); color: #00ff80; border: 1px solid #00ff80; box-shadow: 0 0 15px rgba(0, 255, 128, 0.2); }
        .sig-danger { background: rgba(255, 0, 60, 0.15); color: #ff003c; border: 1px solid #ff003c; box-shadow: 0 0 15px rgba(255, 0, 60, 0.3); animation: flash 1s infinite; }
        @keyframes flash { 50% { opacity: 0.5; } }

        /* Map */
        .map-wrapper { padding: 0; height: 100%; border-radius: 12px; overflow: hidden; position: relative; }
        #map { width: 100%; height: 100%; filter: invert(90%) hue-rotate(180deg) contrast(90%); }

        .live-indicator {
            position: absolute; top: 15px; right: 15px;
            background: black; color: var(--neon-cyan); border: 1px solid var(--neon-cyan);
            padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
            z-index: 999; text-transform: uppercase; letter-spacing: 1px;
            box-shadow: 0 0 10px var(--neon-cyan);
        }

        /* --- 6. MANAGEMENT GRID --- */
        .bottom-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 25px; }

        .form-label { font-size: 13px; color: var(--neon-cyan); margin-bottom: 8px; display: block; letter-spacing: 1px; }
        input, select {
            width: 100%; padding: 14px;
            background: rgba(0, 0, 0, 0.25); /* Transparent input fields */
            border: 1px solid rgba(255,255,255,0.15); border-radius: 6px;
            color: white; font-size: 16px; outline: none; margin-bottom: 15px;
            transition: 0.3s;
        }
        input:focus { border-color: var(--neon-cyan); box-shadow: 0 0 10px rgba(0, 243, 255, 0.2); background: rgba(0,0,0,0.4); }

        .btn-neon {
            width: 100%; padding: 14px; background: transparent;
            border: 1px solid var(--neon-cyan); color: var(--neon-cyan);
            font-weight: 700; text-transform: uppercase; letter-spacing: 2px;
            cursor: pointer; transition: 0.3s; margin-top: 10px;
        }
        .btn-neon:hover { background: var(--neon-cyan); color: black; box-shadow: 0 0 20px var(--neon-cyan); }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 12px; color: var(--text-muted); font-size: 12px; letter-spacing: 1px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        td { padding: 15px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 15px; }
        tr:hover td { background: rgba(255,255,255,0.1); color: white; }

        /* Boat Icon on Map */
        .boat-marker-icon {
            color: #000; font-size: 14px;
            background: var(--neon-cyan); border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            box-shadow: 0 0 15px var(--neon-cyan);
            border: 2px solid white;
        }

        /* Modal */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9); z-index: 2000; display: none;
            justify-content: center; align-items: center;
        }
        .modal-content {
            background: #0a111a; border: 1px solid var(--neon-red);
            padding: 40px; width: 450px; text-align: center; border-radius: 10px;
            box-shadow: 0 0 50px rgba(255, 0, 60, 0.3);
        }

        @media (max-width: 1024px) { .top-grid, .bottom-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="brand-logo"><i class="fas fa-water"></i> OceanEye</div>
    <div class="user-info">
        <span>{{ Auth::user()->name }}</span>
        <span style="opacity: 0.5;">|</span>
        <span id="clock" style="font-family: monospace;">00:00</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn-logout">Logout</button>
        </form>
    </div>
</nav>

<div class="dashboard-container">

    @if(session('success'))
        <div style="background: rgba(0,255,128,0.1); border: 1px solid #00ff80; color: #00ff80; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="top-grid">

        <div class="panel sos-card" onclick="openSosModal()">
            <div class="sos-btn"><i class="fas fa-satellite-dish"></i></div>
            <h2 style="color: var(--neon-red); letter-spacing: 2px;">SOS</h2>
            <p style="font-size: 13px; opacity: 0.7;">BROADCAST DISTRESS SIGNAL</p>
        </div>

        <div class="panel weather-panel">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 5px;">LIVE CONDITIONS</div>
                    <div class="temp-val">{{ $weather->temperature ?? '28°C' }}</div>
                    <div class="weather-status"><i class="fas fa-wind"></i> {{ $weather->condition ?? 'Sunny' }}</div>
                </div>
                <i class="fas fa-sun" style="font-size: 60px; color: #ffbb00; filter: drop-shadow(0 0 20px #ffbb00);"></i>
            </div>

            @if(isset($warning_signal) && $warning_signal >= 5)
                <div class="signal-box sig-danger">
                    <i class="fas fa-radiation"></i> DANGER: SIGNAL {{ $warning_signal }}
                </div>
            @elseif(isset($warning_signal) && $warning_signal >= 1)
                <div class="signal-box" style="border: 1px solid #f1c40f; color: #f1c40f;">
                    <i class="fas fa-exclamation-triangle"></i> WARNING: SIGNAL {{ $warning_signal }}
                </div>
            @else
                <div class="signal-box sig-safe">
                    <i class="fas fa-shield-alt"></i> SEA IS SAFE
                </div>
            @endif
        </div>

        <div class="panel map-wrapper">
            <div id="map"></div>
            <div class="live-indicator"><span style="animation: blink 1s infinite;">●</span> LIVE</div>
        </div>
    </div>

    <div class="bottom-grid">

        <div class="panel">
            <div style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--neon-cyan); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                <i class="fas fa-plus-square"></i> REGISTER VESSEL
            </div>
            <form action="{{ route('boats.store') }}" method="POST">
                @csrf
                <label class="form-label">VESSEL NAME</label>
                <input type="text" name="boat_name" placeholder="Ex:Mayer Doa" required>

                <label class="form-label">REGISTRATION NO</label>
                <input type="text" name="registration_number" placeholder="Ex: REG-8821" required>

                <label class="form-label">TYPE</label>
                <select name="boat_type">
                    <option value="Trawler">Deep Sea Trawler</option>
                    <option value="Small Boat">Coastal Boat</option>
                    <option value="Speedboat">Speedboat</option>
                </select>

                <label class="form-label">CAPACITY</label>
                <input type="number" name="capacity" placeholder="Crew Size" required>

                <button type="submit" class="btn-neon">ADD TO FLEET</button>
            </form>
        </div>

        <div class="panel">
            <div style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: white; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                <i class="fas fa-list"></i> FLEET STATUS
            </div>
            @if($boats->count() > 0)
                <table>
                    <thead>
                    <tr>
                        <th>NAME</th> <th>REG. NO</th> <th>TYPE</th> <th style="text-align: right;">ACTION</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($boats as $boat)
                        <tr>
                            <td style="font-weight: 700; color: white;">{{ $boat->boat_name }}</td>
                            <td style="color: var(--text-muted);">{{ $boat->registration_number }}</td>
                            <td><span style="background: rgba(255,255,255,0.1); padding: 3px 8px; font-size: 12px; border-radius: 4px;">{{ $boat->boat_type }}</span></td>
                            <td style="text-align: right;">
                                <form action="{{ route('boats.delete', $boat->id) }}" method="POST" onsubmit="return confirm('Remove?');">
                                    @csrf @method('DELETE')
                                    <button style="background: none; border: none; cursor: pointer; color: var(--neon-red);"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 60px; opacity: 0.5;">
                    <i class="fas fa-anchor" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>NO VESSELS REGISTERED</p>
                </div>
            @endif
        </div>
    </div>

    <div style="text-align: center; margin-top: 40px; color: var(--text-muted); font-size: 12px;">
         © 2026.Team The Error Squad. All rights reserved.
    </div>

</div>

<div id="sosModal" class="modal-overlay">
    <div class="modal-content">
        <i class="fas fa-biohazard" style="font-size: 60px; color: var(--neon-red); margin-bottom: 20px; animation: pulse 1s infinite;"></i>
        <h2 style="color: white; margin-bottom: 10px; letter-spacing: 2px;">CONFIRM SOS</h2>
        <p style="color: #999; margin-bottom: 30px;">This will alert Coast Guard HQ immediately.</p>

        <form action="{{ route('sos.send') }}" method="POST" id="sosForm">
            @csrf
            <select name="boat_id" required style="background: #000; border: 1px solid var(--neon-red); margin-bottom: 20px; color: white; padding: 10px;">
                <option value="" disabled selected>Select Vessel</option>
                @foreach($boats as $boat)
                    <option value="{{ $boat->id }}">{{ $boat->boat_name }}</option>
                @endforeach
            </select>
            <div style="display: flex; gap: 15px;">
                <button type="button" onclick="closeSosModal()" style="flex: 1; padding: 12px; background: #222; border: none; color: white; cursor: pointer;">CANCEL</button>
                <button type="button" onclick="confirmSOS()" style="flex: 1; padding: 12px; background: var(--neon-red); border: none; color: white; font-weight: 700; cursor: pointer;">BROADCAST</button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    setInterval(() => { document.getElementById('clock').innerText = new Date().toLocaleTimeString(); }, 1000);
    function openSosModal() { document.getElementById('sosModal').style.display = 'flex'; }
    function closeSosModal() { document.getElementById('sosModal').style.display = 'none'; }
    function confirmSOS() {
        if(document.querySelector('select[name="boat_id"]').value) {
            document.getElementById('sosForm').submit();
        } else {
            alert("Select a vessel");
        }
    }

    const map = L.map('map', {zoomControl: false}).setView([21.8, 89.5], 9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const allBoats = @json($allBoats);
    const boatIcon = L.divIcon({
        className: 'boat-marker-icon',
        html: '<i class="fas fa-ship"></i>',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    allBoats.forEach(b => {
        L.marker([21.50 + Math.random(), 89.00 + Math.random()], {icon: boatIcon}).addTo(map)
            .bindPopup(`<b style="color:black;">${b.boat_name}</b>`);
    });
</script>

</body>
</html>
