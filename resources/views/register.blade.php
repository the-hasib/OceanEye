<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OceanEye</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- BASIC RESET --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* --- ERROR MESSAGE STYLE --- */
        .error-msg {
            color: #ff6b6b; /* Bright Red */
            font-size: 11px;
            margin-top: 5px;
            display: block;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
        }
        .error-msg a {
            color: #fff;
            text-decoration: underline;
        }

        /* --- BACKGROUND --- */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('reg.jpg') }}");
            background-size: cover;
            background-position: center;
            z-index: -1;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* --- ANIMATION --- */
        @keyframes floatFrame {
            0% { transform: translateY(0px); box-shadow: 0 25px 50px rgba(0,0,0,0.8); }
            50% { transform: translateY(-20px); box-shadow: 0 35px 60px rgba(0,0,0,0.6); }
            100% { transform: translateY(0px); box-shadow: 0 25px 50px rgba(0,0,0,0.8); }
        }

        /* --- IPHONE FRAME --- */
        .iphone-15 {
            width: 360px;
            height: 740px;
            border: 0.5px solid #1a1a1a;
            border-radius: 55px;
            position: relative;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: floatFrame 15s ease-in-out infinite;
            overflow: hidden;
        }

        /* --- DYNAMIC ISLAND --- */
        .dynamic-island {
            width: 120px;
            height: 35px;
            background: black;
            border-radius: 20px;
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 20;
        }

        /* --- SCROLLABLE AREA --- */
        .scroll-container {
            width: 100%;
            height: 100%;
            overflow-y: auto;
            padding: 10px;
            padding-top: 60px;
            padding-bottom: 20px;
            scrollbar-width: none;
        }
        .scroll-container::-webkit-scrollbar { display: none; }

        /* --- HEADER --- */
        h2 {
            color: white;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            letter-spacing: 1px;
        }

        /* --- ROLE SWITCHER --- */
        .role-switcher {
            display: flex;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            padding: 5px;
            margin-bottom: 25px;
        }

        .role-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            color: rgba(255,255,255, 0.6);
            font-weight: bold;
            cursor: pointer;
            border-radius: 25px;
            transition: 0.3s;
        }

        .role-btn.active {
            background: #00d2ff;
            color: #000;
            box-shadow: 0 0 15px rgba(0, 210, 255, 0.6);
        }

        /* --- INPUT FIELDS --- */
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            color: #ccc;
            font-size: 12px;
            margin-bottom: 5px;
            padding-left: 5px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            padding-right: 40px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #00d2ff;
            background: rgba(255, 255, 255, 0.2);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 38px;
            color: #aaa;
        }

        /* --- SUBMIT BUTTON --- */
        .btn-register {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 15px;
            background: white;
            color: #0072ff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(0, 114, 255, 0.2);
        }

        .btn-register:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 198, 255, 0.4);
            background: #f8f9fa;
        }

        /* --- FOOTER --- */
        .footer-link {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: white;
        }
        .footer-link a { color: #00d2ff; text-decoration: none; font-weight: bold; }
        .footer-link a:hover { text-decoration: underline; text-shadow: 0 0 5px #00d2ff; }

        .copyright-text {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 300;
            letter-spacing: 0.5px;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 10px;
        }

        .hidden { display: none; }

    </style>
</head>
<body>

<div class="bg-container"></div>

<div class="iphone-15">

    <div class="dynamic-island"></div>

    <div class="scroll-container">

        <h2>Create Account</h2>

        <div class="role-switcher">
            <button type="button" class="role-btn active" onclick="setRole('fisherman')">Fisherman</button>
            <button type="button" class="role-btn" onclick="setRole('coast_guard')">Coast Guard</button>
        </div>

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <input type="hidden" name="role" id="selected_role" value="fisherman">

            <div id="fisherman-fields">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter Full Name" value="{{ old('name') }}">
                    <i class="fas fa-user input-icon"></i>
                </div>

                <div class="input-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile" placeholder="017xxxxxxxx" value="{{ old('mobile') }}">
                    <i class="fas fa-phone input-icon"></i>
                    @error('mobile')
                    <small class="error-msg">{{ $message }} <a href="{{ route('login') }}">Login Here</a></small>
                    @enderror
                </div>

                <div class="input-group">
                    <label>Fishing License No</label>
                    <input type="text" name="license_no" placeholder="License Number" value="{{ old('license_no') }}">
                    <i class="fas fa-id-card input-icon"></i>
                    @error('license_no')
                    <small class="error-msg">{{ $message }} <a href="{{ route('login') }}">Login Here</a></small>
                    @enderror
                </div>

                <div class="input-group">
                    <label>NID Number</label>
                    <input type="text" name="nid" placeholder="National ID" value="{{ old('nid') }}">
                    <i class="fas fa-fingerprint input-icon"></i>
                </div>

                <div class="input-group">
                    <label>Address / Base Port</label>
                    <input type="text" name="address" placeholder="Home Port Address" value="{{ old('address') }}">
                    <i class="fas fa-anchor input-icon"></i>
                </div>
            </div>

            <div id="guard-fields" class="hidden">
                <div class="input-group">
                    <label>Officer Name</label>
                    <input type="text" name="officer_name" placeholder="Officer Name" value="{{ old('officer_name') }}">
                    <i class="fas fa-user-shield input-icon"></i>
                </div>

                <div class="input-group">
                    <label>Official Email</label>
                    <input type="email" name="email" placeholder="official@coastguard.gov.bd" value="{{ old('email') }}">
                    <i class="fas fa-envelope input-icon"></i>
                    @error('email')
                    <small class="error-msg">{{ $message }} <a href="{{ route('login') }}">Login Here</a></small>
                    @enderror
                </div>

                <div class="input-group">
                    <label>Service ID</label>
                    <input type="text" name="service_id" placeholder="Official Service ID" value="{{ old('service_id') }}">
                    <i class="fas fa-badge-sheriff input-icon"></i>
                    @error('service_id')
                    <small class="error-msg">{{ $message }} <a href="{{ route('login') }}">Login Here</a></small>
                    @enderror
                </div>

                <div class="input-group">
                    <label>Station Name / Zone</label>
                    <input type="text" name="station_zone" placeholder="e.g. Chittagong West Zone" value="{{ old('station_zone') }}">
                    <i class="fas fa-building input-icon"></i>
                </div>
            </div>

            <div class="input-group" style="margin-top: 20px;">
                <label>Set Password</label>
                <input type="password" name="password" placeholder="Create a strong password" required>
                <i class="fas fa-lock input-icon"></i>
            </div>

            <button type="submit" class="btn-register">REGISTER NOW</button>

            <p class="footer-link">
                Already have an ID? <a href="{{ route('login') }}">Login Here</a>
            </p>

            <div class="copyright-text">
                &copy; Team The Error Squad. All rights reserved.
            </div>

            <br>
        </form>
    </div>
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
