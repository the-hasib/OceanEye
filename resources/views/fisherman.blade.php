<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2>📊 Dashboard Overview</h2>

    <div class="grid-container">
        <div class="card sos" onclick="openSosModal()">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>SOS Alert</h3>
            <p style="font-size: 13px; opacity: 0.9;">CLICK FOR EMERGENCY</p>
        </div>

        <div class="card weather">
            <i class="fas fa-cloud-bolt"></i>
            <h3>Weather</h3>
            <p style="font-size: 13px; opacity: 0.7;">Storm Warning: None</p>
        </div>

        <div class="card map-card">
            <div id="map"></div>
        </div>
    </div>

    <div id="sosModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 style="color: #ff5d4f;"><i class="fas fa-exclamation-circle"></i> Emergency SOS</h3>
            <p style="margin-bottom: 15px; color: #333;">Please select which boat is in danger:</p>

            <form action="{{ route('sos.send') }}" method="POST" id="sosForm">
                @csrf
                <input type="hidden" name="latitude" id="lat_val">
                <input type="hidden" name="longitude" id="lng_val">

                <div class="form-group">
                    <select name="boat_id" required style="background: white; color: #333; border: 1px solid #ddd;">
                        <option value="" disabled selected>Select Your Boat</option>
                        @foreach($boats as $boat)
                            <option value="{{ $boat->id }}">{{ $boat->boat_name }} ({{ $boat->registration_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeSosModal()" style="background: #ccc; color: black;" class="btn-submit">Cancel</button>
                    <button type="button" onclick="confirmSOS()" style="background: #ff5d4f;" class="btn-submit">🔴 SEND SOS</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; display: flex; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 25px; border-radius: 10px; width: 90%; max-width: 400px; text-align: center; }
    </style>

    <h2>🚤 Boat Management</h2>
</div>

<script>
    // 1. Modal Functions
    function openSosModal() {
        document.getElementById('sosModal').style.display = 'flex';
    }
    function closeSosModal() {
        document.getElementById('sosModal').style.display = 'none';
    }

    // 2. SOS Logic with Location
    function confirmSOS() {
        // ফর্ম ভ্যালিডেশন (বোট সিলেক্ট করেছে তো?)
        var boatSelect = document.querySelector('select[name="boat_id"]');
        if(boatSelect.value === "") {
            alert("Please select a boat first!");
            return;
        }

        if(!confirm("🚨 ARE YOU SURE? Sending SOS for " + boatSelect.options[boatSelect.selectedIndex].text)) {
            return;
        }

        alert("Processing SOS...");

        // if found location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('lat_val').value = position.coords.latitude;
                document.getElementById('lng_val').value = position.coords.longitude;
                document.getElementById('sosForm').submit();
            }, function() {
                // if not found location
                document.getElementById('sosForm').submit();
            });
        } else {
            document.getElementById('sosForm').submit();
        }
    }
</script>
