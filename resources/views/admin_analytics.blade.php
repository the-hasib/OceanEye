<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - OceanEye</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* --- COPYING YOUR EXACT DASHBOARD STYLES --- */
        * { margin:0; padding:0; box-sizing:border-box; font-family: "Segoe UI", sans-serif; }

        body { background: radial-gradient(circle at top, #0b2740, #061726); color:#eaf6ff; display: flex; flex-direction: column; min-height: 100vh; }

        /* LAYOUT */
        .admin-layout { display:flex; flex: 1; }

        /* SIDEBAR */
        .sidebar { width:240px; background:#0c3558; padding:20px; flex-shrink:0; display: flex; flex-direction: column; }
        .brand { font-size:22px; font-weight:700; margin-bottom:30px; color: white; }

        .sidebar nav { display: flex; flex-direction: column; flex-grow: 1; }
        .sidebar nav a, .sidebar nav button {
            display:flex; align-items:center; gap:12px; padding:12px 14px;
            margin-bottom:8px; color:#cfe9ff; text-decoration:none;
            border-radius:10px; transition:.3s; background: none; border: none;
            width: 100%; font-size: 16px; cursor: pointer; text-align: left;
        }
        .sidebar nav a i, .sidebar nav button i { width:20px; text-align:center; }

        .sidebar nav a:hover, .sidebar nav a.active, .sidebar nav button:hover { background:#134a73; }
        .logout-form { margin-top: auto; }
        .sidebar nav button.logout { background:#133b55; color: #ff5d4f; }
        .sidebar nav button.logout:hover { background:#e74c3c; color: white; }

        /* MAIN */
        .main { flex:1; padding:28px; display: flex; flex-direction: column; }

        /* TOP BAR */
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .time-box { display:flex; gap:8px; align-items:center; opacity:.85; }

        /* --- ANALYTICS SPECIFIC STYLES (Matching Theme) --- */

        /* Grid Layout for Charts */
        .analytics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }

        /* Reusing your Panel Style for Cards */
        .panel-card { background:#0f2f4a; padding:20px; border-radius:18px; height: 100%; }
        .panel-card h3 { margin-bottom:15px; font-size: 16px; color: #cfe9ff; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }

        /* List Items (Leaderboard) */
        .list-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,.08); }
        .list-item:last-child { border-bottom: none; }

        .badge { background: #134a73; color: #3bbcff; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .rank-icon { margin-right: 8px; font-size: 16px; }

        /* Footer */
        .footer { text-align: center; padding: 20px; color: rgba(255, 255, 255, 0.4); font-size: 13px; margin-top: auto; }

    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand">🌊 OceanEye</div>
        <nav>
            <nav>
                <a href="{{ route('admin.dashboard') }}" class="active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a href="{{ route('admin.users') }}"><i class="fa-solid fa-users"></i> Users</a>
                <a href="{{ route('admin.boats') }}"><i class="fa-solid fa-ship"></i> Boats</a>
                <a href="{{ route('admin.sos') }}"><i class="fa-solid fa-triangle-exclamation"></i> SOS Monitor</a>
                <a href="{{ route('admin.map') }}"><i class="fa-solid fa-map"></i> Map</a>

                <a href="{{ route('admin.analytics') }}"><i class="fa-solid fa-chart-simple"></i> Analytics</a>
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
                <span id="liveTime"></span>
            </div>
        </header>

        <section class="analytics-grid">
            <div class="panel-card">
                <h3><i class="fa-solid fa-anchor" style="color:#3bbcff;"></i> Boat Fleet Distribution</h3>
                <div style="height: 250px; display: flex; justify-content: center;">
                    <canvas id="boatChart"></canvas>
                </div>
            </div>

            <div class="panel-card">
                <h3><i class="fa-solid fa-heart-pulse" style="color:#ff5d4f;"></i> Monthly SOS Trends</h3>
                <div style="height: 250px;">
                    <canvas id="sosChart"></canvas>
                </div>
            </div>
        </section>

        <section class="analytics-grid">
            <div class="panel-card">
                <h3><i class="fa-solid fa-medal" style="color:#ffd24c;"></i> Top Rescue Units</h3>

                @forelse($top_rescuers as $index => $rescuer)
                    <div class="list-item">
                        <div>
                            <span class="rank-icon">
                                @if($index == 0) 👑
                                @elseif($index == 1) 🥈
                                @elseif($index == 2) 🥉
                                @else #{{ $index + 1 }}
                                @endif
                            </span>
                            <strong>{{ $rescuer->name }}</strong>
                        </div>
                        <span class="badge">{{ $rescuer->total_rescues }} Missions</span>
                    </div>
                @empty
                    <p style="color:rgba(255,255,255,0.5); font-size:13px; text-align:center; margin-top:20px;">No rescue missions recorded yet.</p>
                @endforelse
            </div>

            <div class="panel-card">
                <h3><i class="fa-solid fa-fish" style="color:#3bbcff;"></i> Top Fishermen</h3>

                @foreach($top_fishermen as $fisherman)
                    <div class="list-item">
                        <div>
                            <i class="fa-solid fa-user" style="color:rgba(255,255,255,0.5); margin-right:8px;"></i>
                            {{ $fisherman->name }}
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
    // 1. Clock Logic
    function updateTime(){
        document.getElementById("liveTime").innerText = new Date().toLocaleString("en-GB");
    }
    updateTime();
    setInterval(updateTime,1000);

    // 2. Boat Chart
    const boatCtx = document.getElementById('boatChart');
    new Chart(boatCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($boat_stats->pluck('boat_type')) !!},
            datasets: [{
                data: {!! json_encode($boat_stats->pluck('total')) !!},
                backgroundColor: ['#3bbcff', '#2ecc71', '#ffd24c', '#ff5d4f'], // Dashboard colors
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'right', labels: { color: '#eaf6ff', font: { size: 12 } } }
            }
        }
    });

    // 3. SOS Chart ON PAGE
    const sosCtx = document.getElementById('sosChart');
    new Chart(sosCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthly_sos->pluck('month')) !!},
            datasets: [{
                label: 'SOS Alerts',
                data: {!! json_encode($monthly_sos->pluck('total')) !!},
                backgroundColor: '#ff5d4f',
                borderRadius: 4,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: '#eaf6ff' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#eaf6ff' }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>

</body>
</html>
