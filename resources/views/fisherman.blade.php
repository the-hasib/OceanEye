<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fisherman Dashboard - OceanEye</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* --- 1. GLOBAL STYLES --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", sans-serif; }

        body {
            /* Dark Blue Gradient Background to mimic the ocean depth */
            background: radial-gradient(circle at top, #0b2740, #061726);
            color: #eaf6ff;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensures footer stays at the bottom */
        }

        /* --- 2. NAVBAR STYLING --- */
        .navbar {
            background: #0c3558;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo { font-size: 24px; font-weight: bold; color: white; }

        .nav-right { display: flex; align-items: center; gap: 20px; }

        /* Live Clock Style */
        .date-time {
            font-size: 14px;
            color: #eaf6ff;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Logout Button: Red background, no hover effect (Coast Guard style) */
        .logout-btn {
            background: #ff4757;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
        }

        /* --- 3. LAYOUT CONTAINER --- */
        .container {
            padding: 30px;
            max-width: 1200px;
            margin: auto;
            width: 100%;
            flex: 1;
        }
        h2, h3 { color: #cfe9ff; margin-bottom: 15px; }

        /* --- 4. DASHBOARD GRID SYSTEM --- */
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr 2fr; /* Columns: SOS, Weather, Map */
            gap: 20px;
            margin-bottom: 40px;
        }

        /* Card Base Style */
        .card {
            background: #0f2f4a;
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: transform 0.3s;
        }
        .card:hover { transform: translateY(-5px); background: #134a73; }
        .card i { font-size: 40px; margin-bottom: 15px; }

        /* SOS Card Specifics */
        .card.sos { border: 1px solid #ff5d4f; cursor: pointer; }
        .card.sos i { color: #ff5d4f; }
        .card.sos:hover { background: #ff5d4f; color: white; }

        /* Map Card Container */
        .card.map-card {
            padding: 0;
            overflow: hidden;
            text-align: left;
            display: block;
            height: 250px;
        }
        #map { width: 100%; height: 100%; }

        /* --- 5. BOAT MANAGEMENT SECTION --- */
        .boat-section { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }

        .panel {
            background: #0f2f4a;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Form Inputs */
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; opacity: 0.8; }

        input, select {
            width: 100%;
            padding: 12px;
            background: #0b2740;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: white;
            outline: none;
        }
        input:focus { border-color: #3bbcff; }

        .btn-submit {
            width: 100%;
            background: #3bbcff;
            color: #061726;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Data Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead { background: #124366; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.08); }
        th { color: #cfe9ff; }
        td { color: rgba(255,255,255,0.8); }

        /* --- 6. MODAL STYLES (POPUP) --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); z-index: 1000;
            display: none;
            justify-content: center; align-items: center;
        }
        .modal-content {
            background: #0f2f4a; border: 1px solid #3bbcff;
            padding: 30px; border-radius: 16px;
            width: 90%; max-width: 400px; text-align: center;
        }
        .modal-btns { display: flex; gap: 10px; margin-top: 20px; }
        .btn-cancel { background: #444; color: white; flex: 1; border: none; padding: 10px; border-radius: 8px; cursor: pointer; }
        .btn-confirm { background: #ff5d4f; color: white; flex: 1; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: bold; }

        /* --- 7. FOOTER --- */
        .footer {
            text-align: center;
            padding: 20px;
            background: #081b2e;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            margin-top: auto;
            border-top: 1px solid #1a3c5a;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .grid-container, .boat-section { grid-template-columns: 1fr; }
            .navbar { flex-direction: column; gap: 10px; align-items: flex-start; }
            .nav-right { flex-direction: column; gap: 10px; width: 100%; align-items: flex-start; }
            .logout-btn { width: 100%; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">🌊 OceanEye <span style="font-size: 14px; opacity: 0.7; font-weight: normal;">| Fisherman Panel</span></div>

    <div class="nav-right">
        <div class="date-time" id="liveClock">Loading Date...</div>

        <div style="font-weight: bold;">{{ Auth::user()->name }}</div>

        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success" style="background:#2ecc71; padding:12px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="background:#e74c3c; padding:12px; border-radius:8px; margin-bottom:20px;">{{ session('error') }}</div>
    @endif

    <h2>📊 Dashboard Overview</h2>

    <div class="grid-container">
        <div class="card sos" onclick="openSosModal()">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>SOS Alert</h3>
            <p style="font-size: 13px; opacity: 0.9;">CLICK FOR EMERGENCY</p>
        </div>

        <div class="card weather" style="padding: 0; overflow: hidden; justify-content: space-between;">
            <div style="padding: 20px; width: 100%;">
                <i class="fas fa-cloud-sun" style="color: #ffd24c; font-size: 35px; margin-bottom: 10px;"></i>
                <h3 style="margin: 0; font-size: 28px; color: white;">{{ $weather->temperature }}</h3>
                <p style="font-size: 13px; opacity: 0.8;">{{ $weather->condition }}</p>
            </div>
            <div style="width: 100%; padding: 12px;
                        background: {{ $weather->signal_number > 0 ? '#ff4757' : '#2ecc71' }};
                        color: white; font-weight: bold;">
                @if($weather->signal_number > 0)
                    <i class="fas fa-flag" style="font-size: 14px;"></i> Danger Signal: {{ $weather->signal_number }}
                @else
                    <i class="fas fa-shield-alt" style="font-size: 14px;"></i> Sea is Safe
                @endif
            </div>
        </div>

        <div class="card map-card">
            <div id="map"></div> <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.6); padding: 5px 10px; border-radius: 5px; font-size: 12px; z-index: 999;">
                <i class="fa-solid fa-satellite-dish" style="color: #2ecc71;"></i> Live Navigation
            </div>
        </div>
    </div>

    <div id="sosModal" class="modal-overlay">
        <div class="modal-content">
            <h2 style="color: #ff5d4f; margin-bottom: 10px;">Emergency</h2>
            <p style="color: #cfe9ff; margin-bottom: 20px;">Select which boat is in danger:</p>

            <form action="{{ route('sos.send') }}" method="POST" id="sosForm">
                @csrf
                <input type="hidden" name="latitude" id="lat_val">
                <input type="hidden" name="longitude" id="lng_val">

                <div class="form-group">
                    <select name="boat_id" required>
                        <option value="" disabled selected>-- Select Your Boat --</option>
                        @foreach($boats as $boat)
                            <option value="{{ $boat->id }}">{{ $boat->boat_name }} ({{ $boat->registration_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-btns">
                    <button type="button" class="btn-cancel" onclick="closeSosModal()">Cancel</button>
                    <button type="button" class="btn-confirm" onclick="confirmSOS()">SEND SOS</button>
                </div>
            </form>
        </div>
    </div>

    <h2>🚤 Boat Management</h2>

    <div class="boat-section">
        <div class="panel">
            <h3>Register New Boat</h3>
            <form action="{{ route('boats.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Boat Name</label>
                    <input type="text" name="boat_name" placeholder="Ex: Mayer Doa" required>
                </div>
                <div class="form-group">
                    <label>Registration Number</label>
                    <input type="text" name="registration_number" placeholder="Ex: REG-011233" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="boat_type">
                        <option value="Trawler">Fishing Trawler</option>
                        <option value="Small Boat">Small Fishing Boat</option>
                        <option value="Speedboat">Speedboat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" name="capacity" placeholder="5" required>
                </div>
                <button type="submit" class="btn-submit">Register Boat</button>
            </form>
        </div>

        <div class="panel">
            <h3>My Registered Boats</h3>
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Reg. No</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($boats as $boat)
                    <tr>
                        <td style="color: #3bbcff; font-weight: bold;">{{ $boat->boat_name }}</td>
                        <td>{{ $boat->registration_number }}</td>
                        <td>{{ $boat->boat_type }}</td>
                        <td>
                            <form action="{{ route('boats.delete', $boat->id) }}" method="POST" onsubmit="return confirm('Delete this boat?')">
                                @csrf
                                @method('DELETE')
                                <button style="color: #ff5d4f; background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: rgba(255,255,255,0.5); padding: 30px;">
                            No boats registered yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="footer">
    &copy; 2026 Team The Error Squad. All rights reserved.
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. Live Clock Logic
    function updateClock() {
        const now = new Date();
        document.getElementById('liveClock').innerText = now.toLocaleString('en-GB', {
            day: 'numeric', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        }) + " | Sundarbans";
    }
    setInterval(updateClock, 1000);
    updateClock(); // Initial call

    // 2. Modal Toggle Functions
    function openSosModal() { document.getElementById('sosModal').style.display = 'flex'; }
    function closeSosModal() { document.getElementById('sosModal').style.display = 'none'; }

    // 3. SOS Submission (No Geo-Prompt, handles logic on server)
    function confirmSOS() {
        var boatSelect = document.querySelector('select[name="boat_id"]');
        if(boatSelect.value === "") { alert("⚠️ Please select a boat first!"); return; }

        if(!confirm("🚨 ARE YOU SURE? Sending SOS for " + boatSelect.options[boatSelect.selectedIndex].text)) { return; }

        var btn = document.querySelector('.btn-confirm');
        btn.innerHTML = "Sending...";
        btn.disabled = true;
        document.getElementById('sosForm').submit();
    }

    // 4. Advanced Map Initialization
    // Center map on the Bay of Bengal area
    const map = L.map('map').setView([21.8, 89.5], 9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OceanEye' }).addTo(map);

    // Pass PHP variable (allBoats) to JavaScript
    const allBoats = @json($allBoats);

    // Custom Icon Definition for Boats
    const boatIcon = L.divIcon({
        className: 'custom-boat',
        html: `<div style="
            background: #3bbcff;
            color: #061726;
            width: 30px; height: 30px;
            border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            border: 2px solid white;
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
            font-size: 14px;">
            <i class="fa-solid fa-ship"></i>
        </div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    // Loop through all boats and add them to the map with random offsets
    allBoats.forEach(boat => {
        // Generate a random position near the Bay of Bengal to simulate activity
        // Latitude range: 21.50 - 22.00, Longitude range: 89.00 - 90.00
        let lat = 21.50 + (Math.random() * 0.5);
        let lng = 89.00 + (Math.random() * 1.0);

        L.marker([lat, lng], {icon: boatIcon})
            .addTo(map)
            .bindPopup(`
                <div style="text-align: center;">
                    <b style="color: #0c3558;">${boat.boat_name}</b><br>
                    <span style="font-size: 11px; color: gray;">${boat.registration_number}</span><br>
                    <span style="color: #2ecc71; font-size: 10px;">● Active in Sea</span>
                </div>
            `);
    });

</script>

</body>
</html>
