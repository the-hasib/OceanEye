<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fisherman Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* --- GLOBAL STYLES --- */
        * { margin:0; padding:0; box-sizing:border-box; font-family: "Segoe UI", sans-serif; }
        body { background: #f0f4f8; color:#333; display: flex; flex-direction: column; min-height: 100vh; }

        /* Navbar */
        .navbar { background: #0b3558; padding: 15px 20px; display: flex; justify-content: space-between; color: white; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; }
        .logout-btn { background: #ff5b5b; border: none; padding: 5px 15px; color: white; border-radius: 5px; cursor: pointer; margin-left: 10px; }

        /* Layout */
        .container { padding: 30px; max-width: 1200px; margin: auto; flex: 1; width: 100%; }
        h2, h3 { color: #0b3558; margin-bottom: 15px; }

        /* Dashboard Cards (Old Design) */
        .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card i { font-size: 40px; margin-bottom: 15px; color: #3bbcff; }
        .card.sos i { color: #ff4757; }

        /* Boat Section */
        .boat-section { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .boat-form-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }

        /* Forms */
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-submit { width: 100%; background: #3bbcff; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-submit:hover { background: #1faaf0; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; color: #555; }

        /* Alerts */
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }

        /* --- FOOTER STYLE --- */
        .footer {
            text-align: center;
            padding: 20px;
            background: #0b3558; /* Matching Navbar */
            color: white;
            font-size: 14px;
            margin-top: auto; /* Pushes footer to bottom */
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">🌊 OceanEye</div>
    <div class="user-info">
        {{ Auth::user()->name }}
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

        <h2>📊 Dashboard Overview</h2>
        <div class="grid-container">

            <form action="{{ route('sos.send') }}" method="POST" id="sosForm" style="display: contents;">
                @csrf
                <div class="card sos" onclick="confirmSOS()" style="cursor: pointer;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>SOS Alert</h3>
                    <p>CLICK FOR EMERGENCY</p>
                </div>
            </form>
            <div class="card">
                <i class="fas fa-cloud-sun-rain"></i>
                <h3>Weather</h3>
                <p>Check storm warnings.</p>
            </div>
            <div class="card">
                <i class="fas fa-fish"></i>
                <h3>Log Catch</h3>
                <p>Daily fishing report.</p>
            </div>
        </div>

        <script>
            function confirmSOS() {
                if(confirm("Are you sure you want to send an SOS Emergency Signal? Coast Guard will be notified!")) {
                    document.getElementById('sosForm').submit();
                }
            }
        </script>

    <h2>🚤 Boat Management</h2>

    <div class="boat-section">
        <div class="boat-form-card">
            <h3>Register New Boat</h3>
            <form action="{{ route('boats.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Boat Name</label>
                    <input type="text" name="boat_name" placeholder="Mayer Doa" required>
                </div>
                <div class="form-group">
                    <label>Registration Number</label>
                    <input type="text" name="registration_number" placeholder="REG-011233" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="boat_type">
                        <option value="Trawler">Fishing Trawler</option>
                        <option value="Small Boat">Small Fishing Boat (Nouka)</option>
                        <option value="Speedboat">Speedboat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Capacity (Persons)</label>
                    <input type="number" name="capacity" placeholder="5" required>
                </div>
                <button type="submit" class="btn-submit">Register Boat</button>
            </form>
        </div>

        <div>
            <h3 style="margin-left: 5px;">📋 My Registered Boats</h3>
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
                        <td>{{ $boat->boat_name }}</td>
                        <td>{{ $boat->registration_number }}</td>
                        <td>{{ $boat->boat_type }}</td>
                        <td>
                            <form action="{{ route('boats.delete', $boat->id) }}" method="POST" onsubmit="return confirm('Delete this boat?')">
                                @csrf
                                @method('DELETE')
                                <button style="color: red; background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: gray; padding: 20px;">
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
    Team The Error Squad. All rights reserved.
</div>

</body>
</html>
