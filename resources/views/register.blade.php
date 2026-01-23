<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OceanEye</title>
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
            /* SAME BACKGROUND AS LOGIN PAGE */
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

        /* --- IPHONE 15 FRAME (EXACT MATCH WITH LOGIN) --- */
        .iphone-15 {
            width: 360px;  /* Exact match */
            height: 740px; /* Exact match */
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
            animation: float 25s ease-in-out infinite;
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
            margin-bottom: 20px;
            margin-top: 5px;
            flex-shrink: 0;
        }

        /* --- HEADER SECTION --- */
        .logo-icon {
            font-size: 50px;
            background: linear-gradient(180deg, #00d2ff 0%, #3a7bd5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
            filter: drop-shadow(0 0 10px rgba(0, 210, 255, 0.4));
        }

        h1.brand-name {
            font-size: 26px;
            font-weight: 800;
            color: white;
            margin-bottom: 5px;
            letter-spacing: 1px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        p.tagline {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 15px;
        }

        /* --- SCROLLABLE FORM AREA --- */
        .scroll-container {
            width: 100%;
            overflow-y: auto;
            padding-right: 5px;
            scrollbar-width: none;
            flex: 1;
        }
        .scroll-container::-webkit-scrollbar { display: none; }

        /* --- ROLE SWITCHER --- */
        .role-switcher {
            display: flex;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 20px;
        }
        .role-btn {
            flex: 1; padding: 8px; border: none;
            background: transparent; color: rgba(255,255,255,0.6);
            font-size: 12px; font-weight: bold; cursor: pointer;
            border-radius: 10px; transition: 0.3s;
        }
        .role-btn.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* --- INPUTS --- */
        .input-group { position: relative; margin-bottom: 15px; text-align: left; }

        input {
            width: 100%;
            padding: 10px 15px;
            padding-right: 35px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            font-size: 13px;
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
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            color: white; opacity: 0.7; font-size: 12px;
        }

        /* --- BUTTON --- */
        .btn-register {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 12px;
            background: white;
            color: #006994;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            margin-bottom: 10px;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-register:hover { transform: scale(1.02); background: #e0f7fa; }

        .bottom-links { margin-top: 5px; font-size: 12px; color: rgba(255, 255, 255, 0.8); }
        .bottom-links a { color: #00d2ff; text-decoration: none; font-weight: bold; }

        .copyright {
            font-size: 10px; color: rgba(255, 255, 255, 0.5);
            margin-top: 15px; width: 100%; text-align: center;
            position: absolute; bottom: 20px; left: 0;
        }

        .hidden { display: none; }
        .error-msg { color: #ff6b6b; font-size: 10px; margin-top: 3px; display: block; font-weight: bold; }
    </style>
</head>
<body>

<div class="iphone-15">
    <div class="dynamic-island"></div>

    <div class="logo-icon"><i class="fas fa-water"></i></div>
    <h1 class="brand-name">OceanEye</h1>
    <p class="tagline">Create Your Account</p>

    <div class="scroll-container">

        <div class="role-switcher">
            <button type="button" class="role-btn active" onclick="setRole('fisherman')">Fisherman</button>
            <button type="button" class="role-btn" onclick="setRole('coast_guard')">Coast Guard</button>
        </div>

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <input type="hidden" name="role" id="selected_role" value="fisherman">

            <div id="fisherman-fields">
                <div class="input-group">
                    <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}">
                    <i class="fas fa-user input-icon"></i>
                </div>
                <div class="input-group">
                    <input type="text" name="mobile" placeholder="Mobile Number" value="{{ old('mobile') }}">
                    <i class="fas fa-phone input-icon"></i>
                    @error('mobile') <small class="error-msg">{{ $message }}</small> @enderror
                </div>
                <div class="input-group">
                    <input type="text" name="license_no" placeholder="Fishing License No" value="{{ old('license_no') }}">
                    <i class="fas fa-id-card input-icon"></i>
                </div>
                <div class="input-group">
                    <input type="text" name="nid" placeholder="NID Number" value="{{ old('nid') }}">
                    <i class="fas fa-fingerprint input-icon"></i>
                </div>
                <div class="input-group">
                    <input type="text" name="address" placeholder="Home Port Address" value="{{ old('address') }}">
                    <i class="fas fa-anchor input-icon"></i>
                </div>
            </div>

            <div id="guard-fields" class="hidden">
                <div class="input-group">
                    <input type="text" name="officer_name" placeholder="Officer Name" value="{{ old('officer_name') }}">
                    <i class="fas fa-user-shield input-icon"></i>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Official Email" value="{{ old('email') }}">
                    <i class="fas fa-envelope input-icon"></i>
                    @error('email') <small class="error-msg">{{ $message }}</small> @enderror
                </div>
                <div class="input-group">
                    <input type="text" name="service_id" placeholder="Service ID" value="{{ old('service_id') }}">
                    <i class="fas fa-badge-sheriff input-icon"></i>
                </div>
                <div class="input-group">
                    <input type="text" name="station_zone" placeholder="Station Zone" value="{{ old('station_zone') }}">
                    <i class="fas fa-building input-icon"></i>
                </div>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Set Password" required>
                <i class="fas fa-lock input-icon"></i>
            </div>

            <button type="submit" class="btn-register">Register Now</button>
        </form>

        <p class="bottom-links">
            Already Registered? <a href="{{ route('login') }}">Sign In</a>
        </p>

        <div style="height: 30px;"></div>
    </div>

    <div class="copyright">Team The Error Squad. All rights reserved.</div>
</div>

<script>
    function setRole(role) {
        const fishermanFields = document.getElementById('fisherman-fields');
        const guardFields = document.getElementById('guard-fields');
        const roleInput = document.getElementById('selected_role');
        const buttons = document.querySelectorAll('.role-btn');

        roleInput.value = role;

        if (role === 'fisherman') {
            fishermanFields.classList.remove('hidden');
            guardFields.classList.add('hidden');
            buttons[0].classList.add('active');
            buttons[1].classList.remove('active');
        } else {
            guardFields.classList.remove('hidden');
            fishermanFields.classList.add('hidden');
            buttons[1].classList.add('active');
            buttons[0].classList.remove('active');
        }
    }
</script>

</body>
</html>
