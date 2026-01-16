<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Boats</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Dark Theme Styles */
        * { margin:0; padding:0; box-sizing:border-box; font-family: "Segoe UI", sans-serif; }
        body { background: radial-gradient(circle at top, #0b2740, #061726); color:#eaf6ff; display: flex; flex-direction: column; min-height: 100vh; }

        /* Layout & Sidebar */
        .admin-layout { display:flex; flex: 1; }
        .sidebar { width:240px; background:#0c3558; padding:20px; flex-shrink:0; display: flex; flex-direction: column; }
        .brand { font-size:22px; font-weight:700; margin-bottom:30px; color: white; }
        .sidebar nav { display: flex; flex-direction: column; flex-grow: 1; }
        .sidebar nav a, .sidebar nav button { display:flex; align-items:center; gap:12px; padding:12px 14px; margin-bottom:8px; color:#cfe9ff; text-decoration:none; border-radius:10px; transition:.3s; background: none; border: none; width: 100%; font-size: 16px; cursor: pointer; text-align: left; }
        .sidebar nav a:hover, .sidebar nav a.active, .sidebar nav button:hover { background:#134a73; color: white; }
        .logout-form { margin-top: auto; }
        .sidebar nav button.logout { background:#133b55; color: #ff5d4f; }

        /* Main Content */
        .main { flex:1; padding:28px; display: flex; flex-direction: column; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .panel { background:#0f2f4a; padding:20px; border-radius:18px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .panel h2 { margin-bottom:14px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }

        /* Table */
        table { width:100%; border-collapse:collapse; margin-top: 10px; }
        thead { background:#124366; }
        th,td { padding:12px; text-align:left; vertical-align: middle; }
        tbody tr { border-bottom:1px solid rgba(255,255,255,.08); }
        tbody tr:hover { background: rgba(255,255,255,0.02); }

        .owner-badge { background: rgba(255, 210, 76, 0.15); color: #ffd24c; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .btn-ban { background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; }

        .footer { text-align: center; padding: 20px; color: rgba(255, 255, 255, 0.4); font-size: 13px; margin-top: auto; }
    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand">🌊 OceanEye</div>
        <nav>
            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}"><i class="fa-solid fa-users"></i> Users</a>

            <a href="{{ route('admin.boats') }}" class="active"><i class="fa-solid fa-ship"></i> Boats</a>

            <a href="#"><i class="fa-solid fa-triangle-exclamation"></i> SOS Monitor</a>
            <a href="#"><i class="fa-solid fa-map"></i> Map</a>
            <a href="{{ route('admin.boats') }}"><i class="fa-solid fa-ship"></i> Boats</a>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </form>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <h1>Boat Registry</h1>
            <div style="color: #cfe9ff;"><i class="fa-regular fa-clock"></i> {{ date('d M Y') }}</div>
        </header>

        @if(session('success'))
            <div style="background: rgba(46, 204, 113, 0.2); border: 1px solid #2ecc71; color: #2ecc71; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
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
                        <td style="font-weight: bold;">{{ $boat->boat_name }}</td>
                        <td>{{ $boat->registration_number }}</td>
                        <td>
                            <span class="owner-badge">
                                <i class="fa-solid fa-user"></i> {{ $boat->user->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td>
                            {{ $boat->boat_type }} <br>
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
                        <td colspan="5" style="text-align: center; padding: 30px; color: gray;">No boats registered yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </section>

        <div class="footer">Team The Error Squad. All rights reserved.</div>
    </main>

</div>

</body>
</html>
