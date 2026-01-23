<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - OceanEye</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* --- 5. ANALYTICS GRIDS --- */
        .analytics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px; }

        .panel-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 25px; border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            height: 100%;
            transition: 0.3s;
        }
        .panel-card:hover { transform: translateY(-5px); border-color: var(--neon-cyan); box-shadow: 0 15px 40px rgba(0,0,0,0.4); }

        .panel-card h3 {
            margin-bottom:20px; font-size: 18px; color: white;
            text-transform: uppercase; letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;
            display: flex; align-items: center; gap: 10px;
        }

        /* --- 6. LIST ITEMS --- */
        .list-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,.05);
            transition: 0.2s;
        }
        .list-item:last-child { border-bottom: none; }
        .list-item:hover { background: rgba(255,255,255,0.05); padding-left: 10px; padding-right: 10px; border-radius: 8px; }

        .badge {
            background: rgba(0, 243, 255, 0.15); color: var(--neon-cyan);
            padding: 5px 12px; border-radius: 6px; font-size: 13px; font-weight: 700;
            border: 1px solid var(--neon-cyan);
        }

        .rank-icon { margin-right: 10px; font-size: 18px; }

        /* Footer */
        .footer { text-align: center; padding: 20px; color: var(--text-muted); font-size: 12px; margin-top: auto; opacity: 0.6; }
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
        <header class="topbar">
            <h1>Analytics Overview</h1>
            <div class="time-box">
                <i class="fa-regular fa-clock"></i>
                <span id="liveTime">Loading...</span>
            </div>
        </header>

        <section class="analytics-grid">
            <div class="panel-card">
                <h3><i class="fa-solid fa-anchor" style="color:var(--neon-cyan);"></i> Boat Fleet Distribution</h3>
                <div style="height: 280px; display: flex; justify-content: center;">
                    <canvas id="boatChart"></canvas>
                </div>
            </div>

            <div class="panel-card">
                <h3><i class="fa-solid fa-heart-pulse" style="color:var(--neon-red);"></i> Monthly SOS Trends</h3>
                <div style="height: 280px;">
                    <canvas id="sosChart"></canvas>
                </div>
            </div>
        </section>

        <section class="analytics-grid">
            <div class="panel-card">
                <h3><i class="fa-solid fa-medal" style="color:var(--neon-yellow);"></i> Top Rescue Units</h3>

                @forelse($top_rescuers as $index => $rescuer)
                    <div class="list-item">
                        <div>
                            <span class="rank-icon">
                                @if($index == 0) 🥇
                                @elseif($index == 1) 🥈
                                @elseif($index == 2) 🥉
                                @else #{{ $index + 1 }}
                                @endif
                            </span>
                            <strong style="font-size: 16px;">{{ $rescuer->name }}</strong>
                        </div>
                        <span class="badge" style="border-color: var(--neon-green); color: var(--neon-green); background: rgba(0, 255, 128, 0.1);">
                            {{ $rescuer->total_rescues }} Missions
                        </span>
                    </div>
                @empty
                    <p style="color:var(--text-muted); font-size:14px; text-align:center; margin-top:40px;">No rescue missions recorded yet.</p>
                @endforelse
            </div>

            <div class="panel-card">
                <h3><i class="fa-solid fa-fish" style="color:var(--neon-cyan);"></i> Top Fishermen</h3>

                @foreach($top_fishermen as $fisherman)
                    <div class="list-item">
                        <div>
                            <i class="fa-solid fa-user-circle" style="color:var(--text-muted); margin-right:10px; font-size: 18px;"></i>
                            <span style="font-size: 16px; font-weight: 600;">{{ $fisherman->name }}</span>
                        </div>
                        <span class="badge">{{ $fisherman->boats_count }} Boats</span>
                    </div>
                @endforeach
            </div>
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

    // Chart Data Handling
    const boatCtx = document.getElementById('boatChart');
    new Chart(boatCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($boat_stats->pluck('boat_type')) !!},
            datasets: [{
                data: {!! json_encode($boat_stats->pluck('total')) !!},
                backgroundColor: ['#00f3ff', '#00ff80', '#f1c40f', '#ff003c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { position: 'right', labels: { color: '#ffffff', font: { family: 'Rajdhani', size: 14 } } }
            }
        }
    });

    const sosCtx = document.getElementById('sosChart');
    new Chart(sosCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthly_sos->pluck('month')) !!},
            datasets: [{
                label: 'SOS Alerts',
                data: {!! json_encode($monthly_sos->pluck('total')) !!},
                backgroundColor: 'rgba(255, 0, 60, 0.6)',
                borderColor: '#ff003c',
                borderWidth: 1,
                borderRadius: 4,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: '#ffffff', font: { family: 'Rajdhani' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#ffffff', font: { family: 'Rajdhani' } }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>

</body>
</html>
