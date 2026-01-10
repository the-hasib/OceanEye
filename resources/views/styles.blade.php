<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Common Styles for All Dashboards */
    body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; }

    /* Navbar Gradient */
    .navbar {
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .logo { font-size: 24px; font-weight: bold; }
    .container { max-width: 1000px; margin: 30px auto; padding: 20px; }

    /* Grid Layout */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    /* Cards */
    .card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: 0.3s;
        cursor: pointer;
    }
    .card:hover { transform: translateY(-5px); }
    .card i { font-size: 40px; color: #0072ff; margin-bottom: 15px; }
    .card h3 { margin: 10px 0; color: #333; }
    .card p { color: #777; font-size: 13px; }

    /* Special Cards */
    .card.sos { border-bottom: 5px solid #ff4d4d; }
    .card.sos i { color: #ff4d4d; }

    /* Logout Button */
    .logout-btn {
        background: rgba(255,255,255,0.2);
        border: 1px solid white;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
    }
    .logout-btn:hover { background: white; color: #333; }
</style>
