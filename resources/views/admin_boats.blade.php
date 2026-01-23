<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Boat Registry</title>

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
        .topbar h1 { font-size: 20px; font-weight: 600; color: var(--neon-cyan); text-transform: uppercase; letter-spacing: 1px; }
        .time-box { display:flex; gap:10px; align-items:center; font-family: monospace; color: var(--text-muted); }

        /* --- 5. PANEL & TABLE (Glass Effect) --- */
        .panel {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 25px; border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .panel h2 {
            margin-bottom:20px; color: white; font-size: 20px;
            text-transform: uppercase; letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;
        }

        /* TABLE STYLE */
        table { width:100%; border-collapse:collapse; margin-top: 10px; }
        thead { background: rgba(255,255,255,0.05); }
        th {
            padding: 15px; text-align: left;
            color: var(--neon-cyan); font-size: 13px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        td {
            padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 15px; color: var(--text-main); vertical-align: middle;
        }
        tbody tr:hover { background: rgba(255,255,255,0.05); }

        /* OWNER BADGE */
        .owner-badge {
            background: rgba(241, 196, 15, 0.15);
            color: var(--neon-yellow);
            padding: 5px 10px; border-radius: 6px;
            font-size: 12px; font-weight: 700; border: 1px solid var(--neon-yellow);
            display: inline-flex; align-items: center; gap: 6px;
        }

        /* REMOVE BUTTON */
        .btn-ban {
            background: rgba(255, 0, 60, 0.15);
            border: 1px solid var(--neon-red);
            color: var(--neon-red);
            padding: 6px 12px; border-radius: 6px;
            cursor: pointer; font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;
        }
        .btn-ban:hover {
            background: var(--neon-red); color: white;
            box-shadow: 0 0 10px var(--neon-red);
        }

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
            <h1>Boat Registry</h1>
            <div class="time-box">
                <i class="fa-regular fa-clock"></i>
                <span id="liveTime"></span>
            </div>
        </header>

        @if(session('success'))
            <div style="background: rgba(0, 255, 128, 0.2); color: var(--neon-green); border: 1px solid var(--neon-green); padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <section class="panel">
            <h2>🚤 All Registered Boats</h2>
            <table>
                <thead>
                <tr>
                    <th>Boat Name</th>
                    <th>Reg. No</th>
                    <th>Owner Name</th>
                    <th>Type / Capacity</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($boats as $boat)
                    <tr>
                        <td style="font-weight: bold; color: white;">{{ $boat->boat_name }}</td>
                        <td style="font-family: monospace; color: var(--text-muted);">{{ $boat->registration_number }}</td>
                        <td>
                            <span class="owner-badge">
                                <i class="fa-solid fa-user"></i> {{ $boat->user->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: bold;">{{ $boat->boat_type }}</div>
                            <span style="font-size:12px; opacity:0.6;">Cap: {{ $boat->capacity }} ppl</span>
                        </td>
                        <td>
                            <form action="{{ route('admin.deleteBoat', $boat->id) }}" method="POST" onsubmit="return confirm('Delete this boat permanently?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn-ban"><i class="fa-solid fa-trash"></i> Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: gray;">
                            <i class="fas fa-ship" style="font-size: 30px; margin-bottom: 10px;"></i>
                            <p>No boats registered yet.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </section>

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
