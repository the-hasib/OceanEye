<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OceanEye</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* --- CSS FROM YOUR FILE --- */
        * { margin:0; padding:0; box-sizing:border-box; font-family: "Segoe UI", sans-serif; }

        body { background: radial-gradient(circle at top, #0b2740, #061726); color:#eaf6ff; }

        /* LAYOUT */
        .admin-layout { display:flex; min-height:100vh; }

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

        /* Logout Button specific */
        .logout-form { margin-top: auto; }
        .sidebar nav button.logout { background:#133b55; color: #ff5d4f; }
        .sidebar nav button.logout:hover { background:#e74c3c; color: white; }

        /* MAIN */
        .main { flex:1; padding:28px; }

        /* TOP BAR */
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .time-box { display:flex; gap:8px; align-items:center; opacity:.85; }

        /* SUMMARY CARDS */
        .summary { display:grid; grid-template-columns: repeat(4, 1fr); gap:18px; margin-bottom:30px; }
        .summary .card { background:#0f2f4a; padding:20px; border-radius:16px; position:relative; }
        .summary .card::before { content:""; position:absolute; left:0; top:0; bottom:0; width:5px; border-radius:16px 0 0 16px; background:#3bbcff; }

        .summary .pending::before { background:#ffd24c; }
        .summary .alert::before { background:#ff5d4f; }

        .summary h4 { margin-bottom: 5px; opacity: 0.8; font-size: 14px; }
        .summary p { font-size: 24px; font-weight: bold; }

        /* PANEL */
        .panel { background:#0f2f4a; padding:20px; border-radius:18px; }
        .panel h2 { margin-bottom:14px; }

        /* TABLE */
        table { width:100%; border-collapse:collapse; }
        thead { background:#124366; }
        th,td { padding:12px; text-align:left; }
        tbody tr { border-bottom:1px solid rgba(255,255,255,.08); }
        .note { margin-top:12px; font-size:13px; opacity:.7; }

        /* BUTTONS */
        .btn { padding:6px 12px; border:none; border-radius:8px; cursor:pointer; color:white; }
        .btn.approve { background:#2ecc71; }
        .btn.reject { background:#e74c3c; }
    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand">🌊 OceanEye</div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>

            <a href="{{ route('admin.users') }}"><i class="fa-solid fa-users"></i> Users</a>

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
            <h1>Welcome, {{ Auth::user()->name }}</h1>
            <div class="time-box">
                <i class="fa-regular fa-clock"></i>
                <span id="liveTime"></span>
            </div>
        </header>

        <section class="summary">
            <div class="card">
                <h4>Total Users</h4>
                <p>{{ \App\Models\User::count() }}</p>
            </div>
            <div class="card pending">
                <h4>Pending Approvals</h4>
                <p>{{ \App\Models\User::where('status', 'pending')->count() }}</p>
            </div>
            <div class="card alert">
                <h4>Active SOS</h4>
                <p>0</p>
            </div>
            <div class="card">
                <h4>Coast Guard Units</h4>
                <p>{{ \App\Models\User::where('role', 'coast_guard')->count() }}</p>
            </div>
        </section>

        <section class="panel">
            <h2>⏳ Pending User Approvals</h2>

            @if(session('success'))
                <div style="background: #2ecc71; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                    {{ session('success') }}
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
                        <td>{{ $user->name }}</td>

                        <td>
                            @if($user->role == 'fisherman')
                                <span style="color: #3bbcff;">Fisherman</span>
                            @else
                                <span style="color: orange;">Coast Guard</span>
                            @endif
                        </td>

                        <td>
                            {{ $user->mobile ?? $user->email }}
                        </td>

                        <td style="color: #ffd24c; font-weight: bold;">
                            {{ ucfirst($user->status) }}
                        </td>

                        <td>
                            <a href="{{ route('admin.approve', $user->id) }}"
                               class="btn approve"
                               style="text-decoration: none; display: inline-block;"
                               onclick="return confirm('Are you sure you want to Approve this user?')">
                                Approve
                            </a>

                            <a href="{{ route('admin.reject', $user->id) }}"
                               class="btn reject"
                               style="text-decoration: none; display: inline-block;"
                               onclick="return confirm('Are you sure you want to DELETE this user?')">
                                Reject
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: gray; padding: 20px;">
                            No pending approvals at the moment.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <p class="note">Users remain inactive until approved.</p>
        </section>

    </main>

</div>

<script>
    // LIVE CLOCK
    function updateTime(){
        document.getElementById("liveTime").innerText = new Date().toLocaleString("en-GB");
    }
    updateTime();
    setInterval(updateTime,1000);
</script>

</body>
</html>
