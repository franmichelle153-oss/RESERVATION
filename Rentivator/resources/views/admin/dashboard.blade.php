@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentivator - Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f4; display: flex; min-height: 100vh; color: #1a3d24; }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 99; }
        .sidebar-overlay.open { display: block; }
        .sidebar { width: 240px; min-width: 240px; background: #1e4d2b; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 100; transition: transform 0.28s cubic-bezier(.4,0,.2,1); }
        .sidebar-logo { display: flex; align-items: center; gap: 12px; padding: 28px 24px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-logo .logo-icon { width: 42px; height: 42px; background: #2d6a3f; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: white; }
        .sidebar-logo span { font-size: 16px; font-weight: 900; color: white; letter-spacing: 2px; }
        .sidebar-close { display: none; position: absolute; top: 16px; right: 14px; background: rgba(255,255,255,0.12); border: none; border-radius: 8px; width: 34px; height: 34px; color: white; font-size: 16px; cursor: pointer; align-items: center; justify-content: center; }
        .sidebar-nav { flex: 1; padding: 24px 0; }
        .nav-item { display: flex; align-items: center; gap: 14px; padding: 14px 24px; color: rgba(255,255,255,0.55); text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; transition: all 0.2s; border-left: 3px solid transparent; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.08); color: white; border-left: 3px solid #6DBE47; padding-left: 21px; }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; }
        .sidebar-bottom { padding: 16px; border-top: 1px solid rgba(255,255,255,0.08); }
        .farmer-portal-btn { width: 100%; padding: 12px; background: white; color: #1e4d2b; border: none; border-radius: 8px; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer; transition: all 0.2s; }
        .farmer-portal-btn:hover { background: #e8f5e9; }
        .sidebar-user { display: flex; align-items: center; gap: 10px; padding: 14px 16px 0; }
        .sidebar-user .avatar { width: 32px; height: 32px; background: #2d6a3f; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 13px; font-weight: 700; }
        .sidebar-user span { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.4); letter-spacing: 1px; text-transform: uppercase; }

        .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { background: white; padding: 0 32px; height: 68px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e8f0e8; position: sticky; top: 0; z-index: 50; gap: 12px; }
        .hamburger { display: none; background: none; border: none; color: #1e4d2b; font-size: 22px; cursor: pointer; padding: 4px 8px; border-radius: 8px; flex-shrink: 0; }
        .topbar-brand { display: flex; align-items: center; gap: 12px; }
        .topbar-brand .logo-icon { width: 38px; height: 38px; background: #1e4d2b; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 16px; color: white; flex-shrink: 0; }
        .topbar-brand-text .name { font-size: 15px; font-weight: 900; color: #1a3d24; letter-spacing: 2px; }
        .topbar-brand-text .sub { font-size: 10px; color: #8aaa92; letter-spacing: 2px; text-transform: uppercase; }
        .topbar-right { display: flex; align-items: center; gap: 20px; flex-shrink: 0; }
        .notif-btn { position: relative; width: 38px; height: 38px; border: none; background: transparent; cursor: pointer; color: #5a7a5e; font-size: 18px; display: flex; align-items: center; justify-content: center; }
        .notif-dot { position: absolute; top: 6px; right: 6px; width: 8px; height: 8px; background: #e74c3c; border-radius: 50%; border: 2px solid white; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 38px; height: 38px; background: #1e4d2b; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .user-text .user-name { font-size: 13px; font-weight: 700; color: #1a3d24; }
        .user-text .user-role { font-size: 10px; color: #8aaa92; letter-spacing: 1px; text-transform: uppercase; }

        .content { padding: 32px; flex: 1; min-width: 0; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .stat-card { background: white; border-radius: 16px; padding: 24px; border: 1px solid #e8f0e8; position: relative; overflow: hidden; }
        .stat-card.highlight { background: linear-gradient(135deg, #1e4d2b, #2d6a3f); border-color: transparent; }
        .stat-badge { display: inline-block; font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: #8aaa92; margin-bottom: 16px; }
        .stat-card.highlight .stat-badge { color: rgba(255,255,255,0.6); }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 16px; }
        .stat-icon.blue   { background: #e8f0ff; color: #3b82f6; }
        .stat-icon.green  { background: #e8f5e9; color: #22c55e; }
        .stat-icon.orange { background: #fff3e0; color: #f59e0b; }
        .stat-icon.purple { background: #f3e8ff; color: #a855f7; }
        .stat-label { font-size: 11px; font-weight: 700; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 6px; }
        .stat-card.highlight .stat-label { color: rgba(255,255,255,0.6); }
        .stat-value { font-size: 30px; font-weight: 900; color: #1a3d24; }
        .stat-card.highlight .stat-value { color: white; }
        .monitor-badge { position: absolute; top: 16px; right: 16px; background: #1e4d2b; color: white; font-size: 9px; font-weight: 800; letter-spacing: 1.5px; padding: 4px 10px; border-radius: 20px; }

        /* Toast */
        .toast { position: fixed; bottom: 24px; right: 24px; background: #1e4d2b; color: white; padding: 14px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); transform: translateY(80px); opacity: 0; transition: all 0.3s; z-index: 600; }
        .toast.show  { transform: translateY(0); opacity: 1; }
        .toast.error { background: #c0392b; }

        /* ── REVENUE CHART (DAGDAG LANG) ── */
        .revenue-section { margin-top: 24px; }
        .revenue-card {
            background: white;
            border-radius: 20px;
            padding: 28px 32px;
            border: 1px solid #e8f0e8;
            box-shadow: 0 2px 12px rgba(30,77,43,0.05);
        }
        .revenue-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .revenue-title {
            font-size: 14px;
            font-weight: 900;
            color: #1a3d24;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .revenue-title i { color: #1e4d2b; font-size: 15px; }
        .revenue-sub {
            font-size: 10px;
            color: #8aaa92;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 5px;
            margin-left: 25px;
        }
        .revenue-total-pill {
            background: linear-gradient(135deg, #1e4d2b, #2d6a3f);
            border-radius: 50px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .pill-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.55);
        }
        .pill-value { font-size: 14px; font-weight: 900; color: white; }
        .chart-wrap { position: relative; height: 280px; }

        @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-close { display: flex; }
            .main { margin-left: 0; }
            .topbar { padding: 0 16px; }
            .hamburger { display: block; }
            .user-text { display: none; }
            .content { padding: 20px 16px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        }
        @media (max-width: 600px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-card { padding: 16px; }
            .stat-value { font-size: 22px; }
            .topbar { padding: 0 12px; height: 58px; }
            .topbar-brand-text .sub { display: none; }
            .revenue-card { padding: 18px 16px; }
            .chart-wrap { height: 200px; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeSidebar()"><i class="fa-solid fa-xmark"></i></button>
 <div class="sidebar-logo" style="display:flex; align-items:center; justify-content:center; padding:8px 24px; border-bottom:1px solid rgba(255,255,255,0.08);">
    <x-app-logo />
</div>
    <nav class="sidebar-nav">
    <a class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="fa-solid fa-table-cells-large"></i> Dashboard
    </a>
    <a class="nav-item {{ request()->is('admin/vehicle') ? 'active' : '' }}" href="{{ route('admin.vehicle') }}">
        <i class="fa-solid fa-tractor"></i> Vehicle
    </a>
    <a class="nav-item {{ request()->is('admin/bookings') ? 'active' : '' }}" href="{{ route('admin.bookings') }}">
        <i class="fa-regular fa-calendar"></i> Bookings
    </a>
   
    <a class="nav-item {{ request()->is('admin/tenants') ? 'active' : '' }}" href="{{ route('admin.tenants') }}">
        <i class="fa-solid fa-users"></i> Tenants
    </a>
    <a class="nav-item {{ request()->is('admin/feedback') ? 'active' : '' }}" href="{{ route('admin.feedback') }}">
        <i class="fa-regular fa-comment"></i> Feedback
    </a>
</nav>
   <div class="sidebar-bottom">
    @include('components.sidebar-bottom')
</div>
</aside>

<!-- MAIN -->
<main class="main">
    <div class="topbar">
        <div style="display:flex; align-items:center; gap:12px;">
            <button class="hamburger" onclick="openSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="topbar-brand">
                <div class="topbar-brand-text">
                    <div class="name">DASHBOARD</div>
                </div>
            </div>
        </div>
        @include('components.topbar')
    </div>

    <div class="content">
        <!-- STATS ONLY -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-badge">+2 UNITS</div>
                <div class="stat-icon blue"><i class="fa-solid fa-cube"></i></div>
                <div class="stat-label">Total Vehicle</div>
                <div class="stat-value">{{ $totalVehicles }}</div>
            </div>
           <div class="stat-card">
    <div class="stat-badge">ON FIELD</div>
    <div class="stat-icon green"><i class="fa-regular fa-calendar-check"></i></div>
    <div class="stat-label">Active Reservation</div>
    <div class="stat-value">{{ $activeReservations }}</div>
</div>
            <div class="stat-card highlight">
                <div class="stat-badge">TOTAL AUDITED</div>
                <div class="stat-icon" style="background:rgba(255,255,255,0.15); color:#f59e0b;">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
            </div>
           {{-- BAGO --}}
<div class="stat-card" style="position:relative;">
    <span class="monitor-badge">TENANTS</span>
    <div class="stat-badge">&nbsp;</div>
    <div class="stat-icon purple"><i class="fa-solid fa-users"></i></div>
    <div class="stat-label">Total Tenants</div>
    <div class="stat-value">{{ $totalTenants }}</div>
</div>
        </div>

        {{-- ── REVENUE CHART (DAGDAG LANG) ── --}}
        <div class="revenue-section">
            <div class="revenue-card">
                <div class="revenue-header">
                    <div>
                        <div class="revenue-title">
                            <i class="fa-solid fa-chart-area"></i>
                            Revenue Overview
                        </div>
                        <div class="revenue-sub">Monthly net revenue for {{ date('Y') }}</div>
                    </div>
                    <div class="revenue-total-pill">
                        <span class="pill-label">Total</span>
                        <span class="pill-value">₱{{ number_format($totalRevenue, 2) }}</span>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

    </div><!-- /content -->
</main>

<!-- TOAST -->
<div class="toast" id="toast">
    <i class="fa-solid fa-circle-check"></i>
    <span id="toastMsg">Done.</span>
</div>

<script>
    function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('open'); document.body.style.overflow = ''; }

    function showToast(msg, isError = false) {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        t.className = 'toast' + (isError ? ' error' : '');
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    @if(session('success')) showToast("{{ session('success') }}"); @endif
    @if(session('error'))   showToast("{{ session('error') }}", true); @endif
</script>

{{-- ── CHART.JS (DAGDAG LANG) ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const labels = @json($revenueLabels);
    const data   = @json($revenueData);

    const ctx  = document.getElementById('revenueChart').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 280);
    grad.addColorStop(0, 'rgba(30,77,43,0.22)');
    grad.addColorStop(1, 'rgba(30,77,43,0.00)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Net Revenue',
                data,
                fill: true,
                backgroundColor: grad,
                borderColor: '#1e4d2b',
                borderWidth: 2.5,
                pointBackgroundColor: '#1e4d2b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2.5,
                pointRadius: 5,
                pointHoverRadius: 8,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e4d2b',
                    titleColor: 'rgba(255,255,255,0.55)',
                    titleFont: { size: 11, weight: '700' },
                    bodyColor: '#ffffff',
                    bodyFont: { size: 14, weight: '900' },
                    padding: 14,
                    cornerRadius: 12,
                    callbacks: {
                        label: ctx => ' ₱' + ctx.parsed.y.toLocaleString('en-PH', {
                            minimumFractionDigits: 2
                        })
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { size: 11, weight: '700' },
                        color: '#8aaa92',
                    }
                },
                y: {
                    grid: { color: '#f0f5f0' },
                    border: { display: false },
                    ticks: {
                        font: { size: 11, weight: '700' },
                        color: '#8aaa92',
                        callback: v => '₱' + v.toLocaleString('en-PH')
                    }
                }
            }
        }
    });
})();
</script>

@include('components.modals')
@include('components.notification-bell')
@include('components.feedback-modal')
</body>
</html>