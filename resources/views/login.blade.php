<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OceanEye</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-image: url("{{ asset('login.jpg') }}");
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* --- IPHONE 15 FRAME --- */
        .iphone-15 {
            width: 360px;
            height: 740px;
            border: 0.5px solid rgba(0, 0, 0, 0.8);
            border-radius: 55px;
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 30px;
            text-align: center;
            animation: float 15s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .dynamic-island {
            width: 120px;
            height: 35px;
            background: black;
            border-radius: 20px;
            margin-bottom: 40px;
            margin-top: 5px;
        }

        .welcome-text {
            font-size: 14px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .logo-icon {
            font-size: 70px;
            background: linear-gradient(180deg, #00d2ff 0%, #3a7bd5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
            filter: drop-shadow(0 0 15px rgba(0, 210, 255, 0.4));
        }

        h1.brand-name {
            font-size: 36px;
            font-weight: 800;
            color: white;
            margin-bottom: 5px;
            letter-spacing: 1px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        p.tagline {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 30px;
        }

        /* --- ALERTS (SUCCESS & ERROR) --- */
        .alert {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 15px;
            text-align: center;
        }
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #d4edda;
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #f8d7da;
        }

        .form-area { width: 100%; margin-top: 10px; }
        .input-group { position: relative; margin-bottom: 30px; text-align: left; }

        input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            color: white;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }
        input::placeholder { color: rgba(255, 255, 255, 0.6); }
        input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #00d2ff;
            box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
        }
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            opacity: 0.7;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 15px;
            background: white;
            color: #006994;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-login:hover { transform: scale(1.02); background: #e0f7fa; }

        .bottom-links { margin-top: 25px; font-size: 13px; color: rgba(255, 255, 255, 0.8); }
        .bottom-links a { color: #00d2ff; text-decoration: none; font-weight: bold; }
        .copyright {
            position: absolute;
            bottom: -40px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="iphone-15">
    <div class="dynamic-island"></div>

    <div class="welcome-text">Welcome to</div>
    <div class="logo-icon"><i class="fas fa-water"></i></div>
    <h1 class="brand-name">OceanEye</h1>
    <p class="tagline">Advanced Marine Safety System</p>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    <form action="{{ route('login.post') }}" method="POST" class="form-area">
        @csrf

        <div class="input-group">
            <input type="text" name="email" placeholder="Email or Number" value="{{ old('email') }}" required>
            <i class="fas fa-satellite-dish input-icon"></i>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
            <i class="fas fa-lock input-icon"></i>
        </div>

        <button type="submit" class="btn-login">Sign In</button>
    </form>

    <p class="bottom-links">
        No Account? <a href="{{ route('register') }}">Register Vessel</a>
    </p>

    <div class="copyright">Team The Error Squad. All rights reserved.</div>
</div>

</body>
</html>
