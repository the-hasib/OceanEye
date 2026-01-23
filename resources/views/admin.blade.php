<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OceanEye</title>

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

        /* --- 5. SUMMARY CARDS (Glass Box Effect) --- */
        .summary { display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; margin-bottom:30px; }

        .card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            padding: 20px;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: 0.3s;
        }
        /* Top Highlight for 3D Effect */
        .card::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }
        .card:hover { transform: translateY(-5px); border-color: var(--neon-cyan); }

        /* Card Borders */
        .card:nth-child(1) { border-left: 4px solid var(--neon-cyan); }
        .card:nth-child(2) { border-left: 4px solid var(--neon-yellow); } /* Pending */
        .card:nth-child(3) { border-left: 4px solid var(--neon-red); }    /* Alert */
        .card:nth-child(4) { border-left: 4px solid var(--neon-green); }  /* Coast Guard */

        .summary h4 { margin-bottom: 5px; opacity: 0.8; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .summary p { font-size: 32px; font-weight: 700; color: white; text-shadow: 0 0 10px rgba(255,255,255,0.3); }

        /* --- 6. PANEL & TABLE --- */
        .panel {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 25px; border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .panel h2 { margin-bottom:20px; color: var(--neon-yellow); font-size: 20px; text-transform: uppercase; letter-spacing: 1px; }

        table { width:100%; border-collapse:collapse; }
        thead { border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { padding: 15px; text-align: left; color: var(--neon-cyan); font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 15px; color: var(--text-main); }
        tbody tr:hover { background: rgba(255,255,255,0.05); }

        /* Note */
        .note { margin-top:15px; font-size:13px; opacity:.6; text-align: center; }

        /* --- 7. BUTTONS --- */
        .btn { padding: 6px 15px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; text-decoration: none; transition: 0.3s; display: inline-block; }

        .btn.approve {
            background: transparent; border: 1px solid var(--neon-green); color: var(--neon-green);
        }
        .btn.approve:hover { background: var(--neon-green); color: black; box-shadow: 0 0 10px var(--neon-green); }

        .btn.reject {
            background: transparent; border: 1px solid var(--neon-red); color: var(--neon-red); margin-left: 5px;
        }
        .btn.reject:hover { background: var(--neon-red); color: white; box-shadow: 0 0 10px var(--neon-red); }

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
            <h1>Welcome Admin, {{ Auth::user()->name }}</h1>
            <div class="time-box">
                <i class="fa-regular fa-clock"></i>
                <span id="liveTime">Loading...</span>
            </div>
        </header>

        <section class="summary">
            <div class="card">
                <h4>Total Users</h4>
                <p>{{ $total_users }}</p>
            </div>
            <div class="card pending">
                <h4 style="color: var(--neon-yellow);">Pending Approvals</h4>
                <p style="color: var(--neon-yellow);">{{ $pending_count }}</p>
            </div>
            <div class="card alert">
                <h4 style="color: var(--neon-red);">Active SOS</h4>
                <p style="color: var(--neon-red);">{{ $active_sos }}</p>
            </div>
            <div class="card">
                <h4 style="color: var(--neon-green);">Coast Guard Units</h4>
                <p style="color: var(--neon-green);">{{ $coast_guard_count }}</p>
            </div>
        </section>

        <section class="panel">
            <h2>⏳ Pending User Approvals</h2>

            @if(session('success'))
                <div style="background: rgba(0, 255, 128, 0.2); color: var(--neon-green); border: 1px solid var(--neon-green); padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Mobile / ID</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pending_users as $user)
                    <tr>
                        <td style="font-weight: bold;">{{ $user->name }}</td>
                        <td>
                            @if($user->role == 'fisherman')
                                <span style="color: var(--neon-cyan);">Fisherman</span>
                            @else
                                <span style="color: orange;">Coast Guard</span>
                            @endif
                        </td>
                        <td style="font-family: monospace; opacity: 0.8;">{{ $user->mobile ?? $user->email }}</td>
                        <td style="color: var(--neon-yellow); font-weight: bold; text-transform: uppercase; font-size: 13px;">{{ ucfirst($user->status) }}</td>
                        <td>
                            <a href="{{ route('admin.approve', $user->id) }}" class="btn approve" onclick="return confirm('Approve this user?')"><i class="fas fa-check"></i> Approve</a>
                            <a href="{{ route('admin.reject', $user->id) }}" class="btn reject" onclick="return confirm('Reject this user?')"><i class="fas fa-times"></i> Reject</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: gray; padding: 30px;">
                            <i class="fas fa-check-circle" style="font-size: 24px; margin-bottom: 10px;"></i><br>
                            No pending approvals at the moment.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <p class="note">Users remain inactive until approved by Admin.</p>
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
