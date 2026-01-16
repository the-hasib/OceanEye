<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* --- COPYING YOUR DASHBOARD STYLES FOR EXACT MATCH --- */
        * { margin:0; padding:0; box-sizing:border-box; font-family: "Segoe UI", sans-serif; }

        /* Same Gradient Background */
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
        .sidebar nav a:hover, .sidebar nav a.active, .sidebar nav button:hover { background:#134a73; color: white; }

        /* LOGOUT BUTTON */
        .logout-form { margin-top: auto; }
        .sidebar nav button.logout { background:#133b55; color: #ff5d4f; }
        .sidebar nav button.logout:hover { background:#e74c3c; color: white; }

        /* MAIN CONTENT */
        .main { flex:1; padding:28px; display: flex; flex-direction: column; }

        /* TOP BAR */
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .time-box { display:flex; gap:8px; align-items:center; opacity:.85; }

        /* PANEL (Table Container) */
        .panel { background:#0f2f4a; padding:20px; border-radius:18px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .panel h2 { margin-bottom:14px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }

        /* TABLE STYLE (Exact Match) */
        table { width:100%; border-collapse:collapse; margin-top: 10px; }
        thead { background:#124366; }
        th,td { padding:12px; text-align:left; vertical-align: middle; }
        tbody tr { border-bottom:1px solid rgba(255,255,255,.08); }
        tbody tr:hover { background: rgba(255,255,255,0.02); }

        /* BADGES & ICONS */
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-fisherman { background: rgba(59, 188, 255, 0.15); color: #3bbcff; }
        .badge-coast { background: rgba(46, 204, 113, 0.15); color: #2ecc71; }

        .info-text { font-size: 13px; opacity: 0.7; margin-top: 2px; }

        /* DELETE BUTTON */
        .btn-ban { background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; transition: 0.3s; }
        .btn-ban:hover { background: #c0392b; }

        /* FOOTER */
        .footer { text-align: center; padding: 20px; color: rgba(255, 255, 255, 0.4); font-size: 13px; margin-top: auto; }
    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand">🌊 OceanEye</div>
        <nav>
            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>

            <a href="{{ route('admin.users') }}" class="active"><i class="fa-solid fa-users"></i> Users</a>

            <a href="#"><i class="fa-solid fa-triangle-exclamation"></i> SOS Monitor</a>
            <a href="#"><i class="fa-solid fa-map"></i> Map</a>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <h1>User Management</h1>
            <div class="time-box">
                <i class="fa-regular fa-clock"></i>
                <span id="liveTime"></span>
            </div>
        </header>

        @if(session('success'))
            <div style="background: rgba(46, 204, 113, 0.2); border: 1px solid #2ecc71; color: #2ecc71; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <section class="panel">
            <h2>👥 All Registered Users</h2>

            <table>
                <thead>
                <tr>
                    <th>Name / Role</th>
                    <th>Contact Info</th>
                    <th>NID / License</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="font-weight: bold; font-size: 15px;">{{ $user->name }}</div>
                            <span class="badge {{ $user->role == 'fisherman' ? 'badge-fisherman' : 'badge-coast' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->role == 'fisherman')
                                <div><i class="fa-solid fa-phone fa-xs"></i> {{ $user->mobile }}</div>
                            @else
                                <div><i class="fa-solid fa-envelope fa-xs"></i> {{ $user->email }}</div>
                            @endif
                        </td>
                        <td>
                            @if($user->nid)
                                <div class="info-text">NID: {{ $user->nid }}</div>
                                <div class="info-text">Lic: {{ $user->license_no }}</div>
                            @else
                                <span style="opacity: 0.5;">-</span>
                            @endif
                        </td>
                        <td>
                            {{ $user->address ?? '-' }}
                        </td>
                        <td>
                            <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to BAN this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn-ban">
                                    <i class="fa-solid fa-ban"></i> Ban
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: gray;">
                            No active users found in the system.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </section>

        <div class="footer">
            Team The Error Squad. All rights reserved.
        </div>
    </main>

</div>

<script>
    // Live Clock Function
    function updateTime(){
        document.getElementById("liveTime").innerText = new Date().toLocaleString("en-GB");
    }
    updateTime();
    setInterval(updateTime,1000);
</script>

</body>
</html>
