<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OceanEye</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- ADMIN DASHBOARD CSS --- */
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 0; }

        /* Navbar */
        .navbar {
            background: #2c3e50; /* Dark Blue-Grey for Admin */
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo { font-size: 24px; font-weight: bold; }
        .logo span { font-size: 12px; background: #e74c3c; padding: 2px 8px; border-radius: 10px; margin-left: 5px; }

        .logout-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .logout-btn:hover { background: #e74c3c; border-color: #e74c3c; }

        /* Container */
        .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }

        /* Grid */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Card */
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: 0.3s;
            cursor: pointer;
            border-top: 5px solid #2c3e50;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }

        .card i { font-size: 40px; color: #2c3e50; margin-bottom: 15px; }
        .card h3 { margin: 10px 0; color: #333; }
        .card p { color: #777; font-size: 13px; }

        /* SOS Alert Card (Special Style) */
        .card.sos { border-top-color: #e74c3c; }
        .card.sos i { color: #e74c3c; }

    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">OceanEye <span>ADMIN</span></div>
    <div class="user-info">
        Welcome, <b>{{ Auth::user()->name }}</b>
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</div>

<div class="container">
    <h2>System Administration</h2>

    <div class="grid-container">
        <div class="card">
            <i class="fas fa-users-cog"></i>
            <h3>Manage Users</h3>
            <p>Add, remove or edit user roles.</p>
        </div>

        <div class="card sos">
            <i class="fas fa-satellite-dish"></i>
            <h3>SOS Monitor</h3>
            <p>View active emergency signals.</p>
        </div>

        <div class="card">
            <i class="fas fa-database"></i>
            <h3>System Logs</h3>
            <p>Check database and server health.</p>
        </div>

        <div class="card">
            <i class="fas fa-ship"></i>
            <h3>Vessel Database</h3>
            <p>View all registered ships.</p>
        </div>
    </div>
</div>

</body>
</html>
