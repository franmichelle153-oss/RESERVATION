@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentivator - Bookings</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f4; display: flex; min-height: 100vh; color: #1a3d24; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 99; }
        .sidebar-overlay.open { display: block; }
        .sidebar { width: 240px; min-width: 240px; background: #1e4d2b; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 100; transition: transform 0.28s cubic-bezier(.4,0,.2,1); }
        .sidebar-close { display: none; position: absolute; top: 16px; right: 14px; background: rgba(255,255,255,0.12); border: none; border-radius: 8px; width: 34px; height: 34px; color: white; font-size: 16px; cursor: pointer; align-items: center; justify-content: center; }
        .sidebar-nav { flex: 1; padding: 24px 0; }
        .nav-item { display: flex; align-items: center; gap: 14px; padding: 14px 24px; color: rgba(255,255,255,0.55); text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; transition: all 0.2s; border-left: 3px solid transparent; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.08); color: white; border-left: 3px solid #6DBE47; padding-left: 21px; }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; }
        .sidebar-bottom { padding: 16px; border-top: 1px solid rgba(255,255,255,0.08); }
        .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { background: white; padding: 0 32px; height: 68px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e8f0e8; position: sticky; top: 0; z-index: 50; gap: 12px; }
        .hamburger { display: none; background: none; border: none; color: #1e4d2b; font-size: 22px; cursor: pointer; padding: 4px 8px; border-radius: 8px; flex-shrink: 0; }
        .topbar-brand-text .name { font-size: 15px; font-weight: 900; color: #1a3d24; letter-spacing: 2px; }
        .content { padding: 32px; flex: 1; min-width: 0; }

        /* STATS */
        .stats-mini { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }
        .stat-mini { background: white; border-radius: 12px; padding: 18px 20px; border: 1px solid #e8f0e8; display: flex; align-items: center; gap: 14px; cursor: pointer; transition: all 0.2s; }
        .stat-mini:hover { border-color: #6DBE47; box-shadow: 0 4px 16px rgba(30,77,43,0.08); }
        .stat-mini.active-stat { border-color: #6DBE47; box-shadow: 0 4px 16px rgba(30,77,43,0.12); }
        .stat-mini-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .stat-mini-icon.green  { background: #e8f5e9; color: #22c55e; }
        .stat-mini-icon.yellow { background: #fff8e1; color: #f59e0b; }
        .stat-mini-icon.red    { background: #fde8e8; color: #e74c3c; }
        .stat-mini-icon.blue   { background: #e8f0ff; color: #3b82f6; }
        .stat-mini-label { font-size: 10px; font-weight: 700; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 2px; }
        .stat-mini-value { font-size: 22px; font-weight: 900; color: #1a3d24; }

        /* MAIN TABS */
        .main-tabs { display: flex; gap: 4px; margin-bottom: 20px; background: white; padding: 6px; border-radius: 14px; border: 1px solid #e8f0e8; width: fit-content; }
        .main-tab { padding: 10px 24px; border: none; background: transparent; font-size: 12px; font-weight: 800; color: #8aaa92; cursor: pointer; border-radius: 10px; transition: all 0.2s; letter-spacing: 1px; text-transform: uppercase; display: flex; align-items: center; gap: 8px; }
        .main-tab:hover { background: #f4f7f4; color: #1a3d24; }
        .main-tab.active { background: #1e4d2b; color: white; }
        .main-tab .tab-count { background: rgba(255,255,255,0.2); color: inherit; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 900; }
        .main-tab:not(.active) .tab-count { background: #e8f0e8; color: #5a7a5e; }

        /* TAB PANELS */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* SECTION CARD */
        .section-card { background: white; border-radius: 16px; padding: 20px 24px; margin-bottom: 20px; border: 1px solid #e8f0e8; }
        .section-title { font-size: 16px; font-weight: 900; color: #1a3d24; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .section-title .accent { width: 4px; height: 20px; background: #6DBE47; border-radius: 4px; }

        /* SEARCH */
        .search-box { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border: 1.5px solid #ddeedd; border-radius: 10px; background: white; }
        .search-box i { color: #8aaa92; font-size: 14px; flex-shrink: 0; }
        .search-box input { border: none; outline: none; font-size: 13px; color: #1a3d24; background: transparent; width: 100%; min-width: 0; }
        .search-box input::placeholder { color: #b8d4bc; }
        .filter-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-tab { padding: 7px 16px; border: none; background: transparent; font-size: 11px; font-weight: 800; color: #8aaa92; cursor: pointer; border-radius: 8px; transition: all 0.2s; letter-spacing: 1px; text-transform: uppercase; }
        .filter-tab:hover, .filter-tab.active { background: #e8f5e9; color: #1e4d2b; }

        /* BOOKING ROW */
        .booking-row { background: white; border-radius: 12px; margin-bottom: 10px; border: 1px solid #e8f0e8; display: flex; align-items: stretch; min-height: 80px; overflow: visible; transition: all 0.2s; position: relative; }
        .booking-row:hover { border-color: #c8e6c9; box-shadow: 0 4px 16px rgba(30,77,43,0.08); }
        .booking-row > *:first-child { border-radius: 12px 0 0 12px; }
        .booking-row > *:last-child  { border-radius: 0 12px 12px 0; }
        .bk-cell { flex: 1 1 0; padding: 14px 16px; display: flex; flex-direction: column; justify-content: center; border-left: 1px solid #f0f5f0; min-width: 0; }
        .bk-cell:first-child { border-left: none; }
        .booking-actions { flex: 0 0 52px; width: 52px; display: flex; align-items: center; justify-content: center; position: relative; border-left: 1px solid #f0f5f0; padding: 0; }
        .booking-label { font-size: 9px; font-weight: 800; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 4px; white-space: nowrap; }
        .booking-value { font-size: 13px; font-weight: 700; color: #1a3d24; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .farmer-cell { display: flex; align-items: center; gap: 8px; }
        .farmer-avatar { width: 28px; height: 28px; background: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #2d6a3f; font-size: 12px; flex-shrink: 0; }
        .status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 20px; font-size: 9px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; white-space: nowrap; width: fit-content; }
        .status-confirmed { background: #e8f5e9; color: #1e7e34; }
        .status-pending   { background: #fff8e1; color: #f59e0b; }
        .status-cancelled { background: #fde8e8; color: #c0392b; }
        .status-completed { background: #e8f0ff; color: #3b82f6; }
        .status-audited   { background: #e8f5e9; color: #1e7e34; }
        .status-paid      { background: #fff8e1; color: #b45309; }

        /* ACTION BUTTONS */
        .action-btn { width: 28px; height: 28px; border: none; background: transparent; cursor: pointer; font-size: 14px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border-radius: 50%; }
        .action-btn.dots { color: #8aaa92; font-size: 13px; }
        .action-btn.dots:hover { background: #f0f5f0; color: #1a3d24; }
        .dots-menu { display: none; position: fixed; background: white; border: 1px solid #e8f0e8; border-radius: 12px; box-shadow: 0 8px 32px rgba(30,77,43,0.2); z-index: 9999; min-width: 200px; overflow: hidden; }
        .dots-menu.open { display: block; }
        .dots-menu-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; cursor: pointer; font-size: 12px; font-weight: 700; color: #1a3d24; transition: background 0.15s; border: none; background: none; width: 100%; text-align: left; }
        .dots-menu-item:hover { background: #f4f7f4; }
        .dots-menu-item.danger { color: #c0392b; }
        .dots-menu-item.danger:hover { background: #fde8e8; }
        .dots-menu-item i { width: 16px; text-align: center; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 48px; color: #c8e6c9; margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 18px; font-weight: 800; color: #8aaa92; margin-bottom: 8px; }
        .empty-state p  { font-size: 13px; color: #b8d4bc; }

        /* COMPLETED SECTION */
        .period-filter { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .period-btn-group { display: flex; gap: 6px; }
        .period-tab { padding: 8px 16px; border: 1.5px solid #ddeedd; border-radius: 8px; background: white; font-size: 11px; font-weight: 800; color: #8aaa92; cursor: pointer; letter-spacing: 1px; text-transform: uppercase; transition: all 0.2s; text-decoration: none; }
        .period-tab:hover, .period-tab.active { background: #1e4d2b; color: white; border-color: #1e4d2b; }
        .date-input { padding: 8px 14px; border: 1.5px solid #ddeedd; border-radius: 8px; font-size: 13px; font-weight: 700; color: #1a3d24; background: white; cursor: pointer; outline: none; }
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        thead th { text-align: left; font-size: 11px; font-weight: 800; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; padding: 12px 10px; border-bottom: 1.5px solid #e8f0e8; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #f0f5f0; transition: background 0.15s; }
        tbody tr:hover { background: #f9fcf9; }
        tbody td { padding: 14px 10px; font-size: 13px; color: #1a3d24; vertical-align: middle; }
        .exp-chip { display: inline-flex; align-items: center; gap: 4px; background: #fde8e8; color: #c0392b; border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 700; margin: 2px; white-space: nowrap; }
        .totals-row td { font-weight: 800; font-size: 13px; color: #1a3d24; background: #f4f7f4; border-top: 2px solid #c8e6c9; padding: 14px 10px; }
        .deduction-data-row td { background: #fff5f5; border-bottom: 1px solid #f0f5f0; }
        .deduction-data-row:hover td { background: #fde8e8 !important; }
        .deduction-bar { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 14px 4px; flex-wrap: wrap; }
        .deduction-bar-label { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; color: #8aaa92; letter-spacing: 1px; text-transform: uppercase; white-space: nowrap; }
        .deduction-bar-label i { color: #e74c3c; }
        .deduction-bar input[type="number"] { padding: 9px 14px; border: 1.5px solid #ddeedd; border-radius: 10px; font-size: 13px; font-weight: 700; color: #1a3d24; width: 130px; outline: none; }
        .deduction-bar input[type="text"]   { flex: 1; min-width: 160px; max-width: 260px; padding: 9px 14px; border: 1.5px solid #ddeedd; border-radius: 10px; font-size: 13px; font-weight: 700; color: #1a3d24; outline: none; }
        .apply-btn { padding: 10px 22px; background: #1e4d2b; color: white; border: none; border-radius: 10px; font-size: 12px; font-weight: 800; cursor: pointer; letter-spacing: 1px; transition: background 0.2s; white-space: nowrap; }
        .apply-btn:hover { background: #2d6a3f; }
        .remove-ded-btn { width: 24px; height: 24px; border: none; background: #fde8e8; color: #c0392b; border-radius: 50%; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; margin-left: 8px; flex-shrink: 0; transition: background 0.2s; }
        .remove-ded-btn:hover { background: #f8b4b4; }

        /* CONFIRM DIALOG */
        .confirm-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 600; align-items: center; justify-content: center; padding: 20px; }
        .confirm-overlay.open { display: flex; }
        .confirm-box { background: white; border-radius: 20px; padding: 40px 36px; text-align: center; max-width: 380px; width: 100%; box-shadow: 0 24px 60px rgba(0,0,0,0.3); animation: cfIn 0.22s cubic-bezier(.4,0,.2,1); }
        @keyframes cfIn { from { opacity:0; transform:scale(0.92); } to { opacity:1; transform:scale(1); } }
        .confirm-icon  { font-size: 52px; margin-bottom: 16px; display: block; }
        .confirm-title { font-size: 18px; font-weight: 900; color: #1a3d24; margin-bottom: 8px; }
        .confirm-msg   { font-size: 13px; color: #5a7a5e; line-height: 1.6; margin-bottom: 28px; }
        .confirm-btns  { display: flex; gap: 12px; }
        .confirm-btn-no  { flex: 1; padding: 13px; border: 1.5px solid #e8f0e8; border-radius: 10px; background: white; color: #5a7a5e; font-size: 13px; font-weight: 800; cursor: pointer; }
        .confirm-btn-yes { flex: 1; padding: 13px; border: none; border-radius: 10px; background: #1e4d2b; color: white; font-size: 13px; font-weight: 800; cursor: pointer; }
        .confirm-btn-yes.danger { background: #c0392b; }

        /* TOAST */
        .toast { position: fixed; bottom: 24px; right: 24px; background: #1e4d2b; color: white; padding: 14px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); transform: translateY(80px); opacity: 0; transition: all 0.3s; z-index: 700; }
        .toast.show  { transform: translateY(0); opacity: 1; }
        .toast.error { background: #c0392b; }

        @media (max-width: 900px) { .sidebar { transform: translateX(-100%); } .sidebar.open { transform: translateX(0); } .sidebar-close { display: flex; } .main { margin-left: 0; } .topbar { padding: 0 16px; } .hamburger { display: block; } .content { padding: 20px 16px; } .stats-mini { grid-template-columns: repeat(2,1fr); gap: 12px; } }
        @media (max-width: 600px) { .content { padding: 12px; } .section-card { padding: 14px; } .stats-mini { grid-template-columns: 1fr 1fr; gap: 10px; } .topbar { padding: 0 12px; height: 58px; } .main-tabs { width: 100%; } .main-tab { flex: 1; justify-content: center; padding: 10px 12px; } }
        @media (max-width: 400px) { .col-price { display: none; } }

       @media (max-width: 640px) {
    .booking-row {
        flex-direction: column;
        min-height: unset;
        border-radius: 14px;
        margin-bottom: 12px;
        overflow: visible;
        border-left: 4px solid #6DBE47;
    }

    /* Header strip — farmer name */
    .booking-row .bk-cell:first-child {
        background: #f4f9f4;
        border-top: none;
        border-left: none;
        border-bottom: 2px solid #e8f0e8;
        padding: 14px 16px;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    .booking-row .bk-cell:first-child .booking-label { 
        display: block;
        margin-bottom: 0;
        margin-right: 12px;
        width: 90px;
        flex-shrink: 0;
    }
    .booking-row .bk-cell:first-child .booking-value {
        font-size: 15px;
        font-weight: 900;
        color: #1a3d24;
        text-align: right;
    }
    .booking-row .bk-cell:first-child .farmer-cell {
        justify-content: flex-end;
    }

    /* All other cells */
    .booking-row .bk-cell {
        border-left: none;
        border-top: 1px solid #f0f5f0;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 11px 16px;
    }
    .booking-row .booking-label {
        margin-bottom: 0;
        margin-right: 12px;
        flex-shrink: 0;
        width: 90px;
        font-size: 9px;
    }
    .booking-row .booking-value {
        text-align: right;
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
        font-size: 13px;
    }

    /* Actions row at bottom */
    .booking-actions {
        flex: unset;
        width: 100%;
        border-left: none;
        border-top: 1px solid #f0f5f0;
        justify-content: flex-end;
        padding: 8px 14px;
    }

    /* Show price column always */
    .col-price { display: flex !important; }

    /* Filter tabs */
    .filter-row { flex-direction: column; align-items: stretch; }
    .filter-tabs { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 6px; 
    }
    .filter-tab { text-align: center; }

    /* Main tabs */
    .main-tabs { width: 100%; }
    .main-tab { flex: 1; padding: 10px 8px; font-size: 10px; }
}
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeSidebar()"><i class="fa-solid fa-xmark"></i></button>
    <div class="sidebar-logo" style="display:flex;align-items:center;justify-content:center;padding:8px 24px;border-bottom:1px solid rgba(255,255,255,0.08);">
        <x-app-logo />
    </div>
    <nav class="sidebar-nav">
        <a class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-table-cells-large"></i> Dashboard</a>
        <a class="nav-item {{ request()->is('admin/vehicle') ? 'active' : '' }}"    href="{{ route('admin.vehicle') }}"><i class="fa-solid fa-tractor"></i> Vehicle</a>
        <a class="nav-item {{ request()->is('admin/bookings') ? 'active' : '' }}"   href="{{ route('admin.bookings') }}"><i class="fa-regular fa-calendar"></i> Bookings</a>
        <a class="nav-item {{ request()->is('admin/tenants') ? 'active' : '' }}"    href="{{ route('admin.tenants') }}"><i class="fa-solid fa-users"></i> Tenants</a>
        <a class="nav-item {{ request()->is('admin/feedback') ? 'active' : '' }}"   href="{{ route('admin.feedback') }}"><i class="fa-regular fa-comment"></i> Feedback</a>
    </nav>
    <div class="sidebar-bottom">@include('components.sidebar-bottom')</div>
</aside>

<main class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="hamburger" onclick="openSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="topbar-brand"><div class="topbar-brand-text"><div class="name">BOOKINGS</div></div></div>
        </div>
        @include('components.topbar')
    </div>

    <div class="content">

        {{-- STATS --}}
        <div class="stats-mini">
            <div class="stat-mini" onclick="switchToTab('active'); setStatusFilter('confirmed')" title="View confirmed">
                <div class="stat-mini-icon green"><i class="fa-solid fa-circle-check"></i></div>
                <div><div class="stat-mini-label">Confirmed</div><div class="stat-mini-value">{{ $confirmed }}</div></div>
            </div>
            <div class="stat-mini" onclick="switchToTab('active'); setStatusFilter('pending')" title="View pending">
                <div class="stat-mini-icon yellow"><i class="fa-regular fa-clock"></i></div>
                <div><div class="stat-mini-label">Pending</div><div class="stat-mini-value">{{ $pending }}</div></div>
            </div>
            <div class="stat-mini" onclick="switchToTab('active'); setStatusFilter('cancelled')" title="View cancelled">
                <div class="stat-mini-icon red"><i class="fa-solid fa-ban"></i></div>
                <div><div class="stat-mini-label">Cancelled</div><div class="stat-mini-value">{{ $cancelled }}</div></div>
            </div>
            <div class="stat-mini" onclick="switchToTab('completed')" title="View completed">
                <div class="stat-mini-icon blue"><i class="fa-regular fa-circle-check"></i></div>
                <div><div class="stat-mini-label">Completed</div><div class="stat-mini-value">{{ $completed }}</div></div>
            </div>
        </div>

        {{-- MAIN TABS --}}
        <div class="main-tabs">
            <button class="main-tab active" id="tab-active" onclick="switchToTab('active')">
                <i class="fa-regular fa-calendar"></i> Active Bookings
                <span class="tab-count">{{ $bookings->count() }}</span>
            </button>
            <button class="main-tab" id="tab-completed" onclick="switchToTab('completed')">
                <i class="fa-regular fa-circle-check"></i> Completed & Sales
                <span class="tab-count">{{ $completedTransactions->count() }}</span>
            </button>
        </div>

        {{-- ══════════════════════════════════════
             TAB 1: ACTIVE BOOKINGS
        ══════════════════════════════════════ --}}
        <div class="tab-panel active" id="panel-active">
            <div class="section-card">
                <div class="section-title"><div class="accent"></div> Active Bookings</div>
                <div class="filter-row">
                    <div class="search-box" style="flex:1;min-width:180px;max-width:400px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchInput" placeholder="Filter bookings..." oninput="filterBookings()">
                    </div>
                    <div class="filter-tabs">
                        <button class="filter-tab" data-filter="all">All</button>
                        <button class="filter-tab active" data-filter="pending">Pending</button>
                        <button class="filter-tab" data-filter="confirmed">Confirmed</button>
                        <button class="filter-tab" data-filter="cancelled">Cancelled</button>
                    </div>
                </div>
            </div>

            <div id="bookingsList">
                @forelse($bookings as $b)
                    @php
                        $statusClass = match($b->status) {
                            'confirmed' => 'status-confirmed',
                            'pending'   => 'status-pending',
                            'cancelled' => 'status-cancelled',
                            default     => 'status-pending'
                        };
                        $total    = ($b->vehicle->rate ?? 0) * (int)($b->hectares ?? 1);
                        $hectares = (int)($b->hectares ?? 1);
                    @endphp
                    <div class="booking-row"
                         data-status="{{ $b->status }}"
                         data-search="{{ strtolower(($b->user->name ?? '').' '.($b->vehicle->name ?? '')) }}">

                        <div class="bk-cell">
                            <div class="booking-label">Farmer</div>
                            <div class="farmer-cell" style="margin-top:4px;">
                                <div class="farmer-avatar"><i class="fa-regular fa-user"></i></div>
                                <span class="booking-value">{{ $b->user->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="bk-cell">
                            <div class="booking-label">Vehicle</div>
                            <div class="booking-value">{{ $b->vehicle->name ?? 'N/A' }}</div>
                            <div style="font-size:9px;color:#8aaa92;margin-top:2px;text-transform:uppercase;letter-spacing:1px;">{{ $b->vehicle->type ?? '' }}</div>
                        </div>
                        <div class="bk-cell">
                            <div class="booking-label">Date</div>
                            <div class="booking-value">{{ \Carbon\Carbon::parse($b->reservation_date)->format('M d, Y') }}</div>
                            <div style="font-size:9px;color:#8aaa92;margin-top:2px;">{{ $hectares }} ha</div>
                        </div>
                        <div class="bk-cell col-price">
                            <div class="booking-label">Price</div>
                            <div class="booking-value" style="color:#1e4d2b;font-size:13px;font-weight:900;">₱{{ number_format($total) }}</div>
                            <div style="font-size:9px;color:#8aaa92;margin-top:2px;">₱{{ number_format($b->vehicle->rate ?? 0) }} × {{ $hectares }} ha</div>
                        </div>
                        <div class="bk-cell">
                            <div class="booking-label">Status</div>
                            <div style="margin-top:4px;">
                                <span class="status-badge {{ $statusClass }}">{{ strtoupper($b->status) }}</span>
                            </div>
                        </div>
                        <div class="booking-actions">
                            <div style="position:relative;">
                                <button type="button" class="action-btn dots bk-dots-btn" data-id="{{ $b->id }}">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="dots-menu" id="dots-{{ $b->id }}">
                                    @if($b->status === 'pending')
                                        <button class="dots-menu-item bk-action-btn"
                                                data-action="confirm" data-id="{{ $b->id }}"
                                                data-name="{{ $b->user->name ?? 'N/A' }}"
                                                data-date="{{ \Carbon\Carbon::parse($b->reservation_date)->format('M d, Y') }}">
                                            <i class="fa-regular fa-circle-check" style="color:#22c55e;"></i> Confirm
                                        </button>
                                        <button class="dots-menu-item danger bk-action-btn"
                                                data-action="cancel" data-id="{{ $b->id }}"
                                                data-name="{{ $b->user->name ?? 'N/A' }}"
                                                data-date="{{ \Carbon\Carbon::parse($b->reservation_date)->format('M d, Y') }}">
                                            <i class="fa-regular fa-circle-xmark"></i> Cancel
                                        </button>
                                        <button class="dots-menu-item danger bk-action-btn"
                                                data-action="delete" data-id="{{ $b->id }}"
                                                data-name="{{ $b->user->name ?? 'N/A' }}"
                                                data-date="{{ \Carbon\Carbon::parse($b->reservation_date)->format('M d, Y') }}">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    @endif
                                    @if($b->status === 'confirmed')
                                        <button class="dots-menu-item bk-action-btn"
                                                data-action="complete" data-id="{{ $b->id }}"
                                                data-name="{{ $b->user->name ?? 'N/A' }}"
                                                data-date="{{ \Carbon\Carbon::parse($b->reservation_date)->format('M d, Y') }}">
                                            <i class="fa-regular fa-circle-check" style="color:#3b82f6;"></i> Mark as Completed
                                        </button>
                                    @endif
                                    @if(in_array($b->status, ['cancelled']))
                                        <button class="dots-menu-item danger bk-action-btn"
                                                data-action="delete" data-id="{{ $b->id }}"
                                                data-name="{{ $b->user->name ?? 'N/A' }}"
                                                data-date="{{ \Carbon\Carbon::parse($b->reservation_date)->format('M d, Y') }}">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fa-regular fa-calendar-xmark"></i>
                        <h3>No active bookings</h3>
                        <p>Pending and confirmed bookings will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div><!-- /panel-active -->

        {{-- ══════════════════════════════════════
             TAB 2: COMPLETED BOOKINGS
             Columns: Farmer | Vehicle | Date | Gross | Auto-Deducted | Net | Status
             (Extra Expenses column removed)
        ══════════════════════════════════════ --}}
        <div class="tab-panel" id="panel-completed">
            <div class="section-card">
                <div class="section-title" style="margin-bottom:12px;"><div class="accent"></div> Completed Bookings & Sales</div>

                {{-- Period filter --}}
                <form method="GET" action="{{ route('admin.bookings') }}" id="periodForm">
                    <div class="period-filter">
                        <div class="period-btn-group">
                            @foreach(['daily'=>'Daily','weekly'=>'Weekly','monthly'=>'Monthly'] as $key => $label)
                                <a href="{{ route('admin.bookings', ['period'=>$key,'date'=>$filterDate,'tab'=>'completed']) }}"
                                   class="period-tab {{ $period === $key ? 'active' : '' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                        <input type="date" name="date" class="date-input" value="{{ $filterDate }}"
                               onchange="this.form.submit()">
                        <input type="hidden" name="period" value="{{ $period }}">
                        <input type="hidden" name="tab" value="completed">
                    </div>
                </form>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Farmer</th>
                                <th>Vehicle</th>
                                <th>Date</th>
                                <th>Gross</th>
                                <th>Auto-Deducted</th>
                                <th>Net</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="completedTbody">
                            @forelse($completedTransactions as $tx)
                           @php
    $allAutoExps = $tx->expenses->filter(fn($e) =>
        str_starts_with($e->label, 'Driver:') ||
        str_starts_with($e->label, 'Helper:') ||
        str_starts_with($e->label, 'Diesel')  // ← str_starts_with instead of exact match
    );

    $groupedExps = collect();

    // Group drivers by name, sum amounts
    $drivers = $allAutoExps->filter(fn($e) => str_starts_with($e->label, 'Driver:'));
    $driverGroups = $drivers->groupBy(fn($e) => 
        preg_replace('/\s*\(\d+\s*ha\)/', '', $e->label) // strip "(X ha)" to group by name
    );
    foreach ($driverGroups as $driverName => $items) {
        $totalAmt = $items->sum('amount');
        $totalHa  = $items->sum(fn($e) => (int) preg_match('/\((\d+)\s*ha\)/', $e->label, $m) ? $m[1] : 0);
        $groupedExps->push((object)[
            'label'  => $driverName . ($totalHa ? " ({$totalHa} ha)" : ''),
            'amount' => $totalAmt,
            'type'   => 'driver',
        ]);
    }

    // Group helpers by name, sum amounts
    $helpers = $allAutoExps->filter(fn($e) => str_starts_with($e->label, 'Helper:'));
    $helperGroups = $helpers->groupBy(fn($e) =>
        preg_replace('/\s*\(\d+\s*ha\)/', '', $e->label)
    );
    foreach ($helperGroups as $helperName => $items) {
        $totalAmt = $items->sum('amount');
        $totalHa  = $items->sum(fn($e) => (int) preg_match('/\((\d+)\s*ha\)/', $e->label, $m) ? $m[1] : 0);
        $groupedExps->push((object)[
            'label'  => $helperName . ($totalHa ? " ({$totalHa} ha)" : ''),
            'amount' => $totalAmt,
            'type'   => 'helper',
        ]);
    }

    // All diesel entries summed
    $diesels = $allAutoExps->filter(fn($e) => str_starts_with($e->label, 'Diesel'));
    if ($diesels->isNotEmpty()) {
        $groupedExps->push((object)[
            'label'  => 'Diesel',
            'amount' => $diesels->sum('amount'),
            'type'   => 'diesel',
        ]);
    }

    $autoTotal = $groupedExps->sum('amount');
@endphp
                            <tr id="ctx-row-{{ $tx->id }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:28px;height:28px;background:#e8f5e9;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#2d6a3f;font-size:12px;flex-shrink:0;">
                                            <i class="fa-regular fa-user"></i>
                                        </div>
                                        <span style="font-weight:700;">{{ $tx->reservation->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:700;">{{ $tx->vehicle->name ?? 'N/A' }}</div>
                                    <div style="font-size:10px;color:#8aaa92;text-transform:uppercase;letter-spacing:1px;">{{ $tx->vehicle->type ?? '' }}</div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M d, Y') }}</td>
                                <td style="font-weight:700;">₱{{ number_format($tx->gross_amount,2) }}</td>
                               <td>
    @if($groupedExps->isEmpty())
        <span style="color:#b8d4bc;font-size:11px;">None</span>
    @else
        @foreach($groupedExps as $ae)
    @php
        $isDriver = $ae->type === 'driver';
        $isHelper = $ae->type === 'helper';
        $icon  = $isDriver ? 'fa-id-badge'     : ($isHelper ? 'fa-person-digging' : 'fa-gas-pump');
        $color = $isDriver ? '#6366f1'          : ($isHelper ? '#f59e0b'           : '#e74c3c');
        $bg    = $isDriver ? '#e8f0ff'          : ($isHelper ? '#fff3e0'           : '#fde8e8');
    @endphp
    <div style="display:inline-flex;align-items:center;gap:5px;
                background:{{ $bg }};color:{{ $color }};
                border-radius:6px;padding:3px 9px;font-size:10px;
                font-weight:700;margin:2px;white-space:nowrap;">
        <i class="fa-solid {{ $icon }}" style="font-size:9px;"></i>
        {{ $ae->label }}: ₱{{ number_format($ae->amount, 0) }}
    </div>
@endforeach
    @endif
</td>
                                <td style="font-weight:800;color:#1e4d2b;" id="ctx-net-{{ $tx->id }}">
                                    ₱{{ number_format($tx->net_amount,2) }}
                                </td>
                                <td>
                                    <span class="status-badge {{ $tx->audit_status === 'audited' ? 'status-audited' : 'status-paid' }}"
                                          id="ctx-status-{{ $tx->id }}">
                                        <i class="fa-regular fa-circle-check"></i>
                                        {{ $tx->audit_status === 'audited' ? 'Audited' : 'Paid' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state" style="padding:40px 20px;">
                                        <i class="fa-regular fa-calendar-xmark"></i>
                                        <p>No completed bookings for this period.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            @if($completedTransactions->count() > 0)
                            <tr>
                                <td colspan="7" style="padding:0;border-top:1.5px dashed #ddeedd;">
                                    <div class="deduction-bar">
                                        <div class="deduction-bar-label"><i class="fa-solid fa-minus"></i> Additional Deduction</div>
                                        <input type="number" id="globalDeductionAmt" placeholder="Amount" min="0" step="0.01">
                                        <input type="text"   id="globalDeductionReason" placeholder="Reason (e.g. Repair)">
                                        <button class="apply-btn" onclick="applyGlobalDeduction()">Apply</button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="totals-row">
                                <td colspan="5" style="text-align:right;padding-right:16px;color:#8aaa92;font-size:11px;letter-spacing:1.5px;">TOTAL NET SALES</td>
                                <td id="completedNetTotal">₱{{ number_format($completedNet,2) }}</td>
                                <td></td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div><!-- /panel-completed -->

    </div><!-- /content -->
</main>

{{-- ACTIVE BOOKING CONFIRM DIALOG --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <span class="confirm-icon" id="confirmIcon"></span>
        <div class="confirm-title" id="confirmTitle">Are you sure?</div>
        <div class="confirm-msg"   id="confirmMsg"></div>
        <div class="confirm-btns">
            <button class="confirm-btn-no" onclick="closeConfirm()">No, Go Back</button>
            <button class="confirm-btn-yes" id="confirmYesBtn" onclick="executeAction()">Yes, Proceed</button>
        </div>
    </div>
</div>

<form method="POST" id="actionForm" style="display:none;">
    @csrf
    <input type="hidden" name="action" id="actionInput">
</form>

<div class="toast" id="toast"><i class="fa-solid fa-circle-check"></i><span id="toastMsg">Action completed.</span></div>

<script>
    const BASE_URL = window.location.origin;

    // ── Sidebar
    function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('open'); document.body.style.overflow='hidden'; }
    function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('open'); document.body.style.overflow=''; }

    // ── Tab switching
    function switchToTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.main-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('panel-' + tab).classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
    }

    // ── Set status filter
    function setStatusFilter(status) {
        document.querySelectorAll('.filter-tab').forEach(t => {
            t.classList.toggle('active', t.dataset.filter === status);
        });
        applyFilters();
    }

    // ── On page load
    (function initPage() {
        const params = new URLSearchParams(window.location.search);
        const tab    = params.get('tab');
        if (tab === 'completed') {
            switchToTab('completed');
        } else {
            switchToTab('active');
            applyFilters();
        }
    })();

    // ── Filter buttons
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
        });
    });

    function filterBookings() { applyFilters(); }

    function applyFilters() {
        const q            = (document.getElementById('searchInput').value || '').toLowerCase();
        const activeFilter = document.querySelector('.filter-tab.active');
        const filterStatus = activeFilter ? activeFilter.dataset.filter : 'pending';
        let visibleCount   = 0;
        document.querySelectorAll('.booking-row').forEach(row => {
            const matchSearch = !q || (row.dataset.search || '').includes(q);
            const matchStatus = filterStatus === 'all' || row.dataset.status === filterStatus;
            const show        = matchSearch && matchStatus;
            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        let emptyEl = document.getElementById('noResultsMsg');
        if (visibleCount === 0) {
            if (!emptyEl) {
                emptyEl = document.createElement('div');
                emptyEl.id = 'noResultsMsg';
                emptyEl.className = 'empty-state';
                emptyEl.innerHTML = '<i class="fa-regular fa-calendar-xmark"></i><h3>No bookings found</h3><p>No bookings match the current filter.</p>';
                document.getElementById('bookingsList').appendChild(emptyEl);
            }
            emptyEl.style.display = '';
        } else if (emptyEl) {
            emptyEl.style.display = 'none';
        }
    }

    // ── Dots menu
    document.addEventListener('click', function(e) {
       const dotsBtn = e.target.closest('.bk-dots-btn');
if (dotsBtn) {
    e.stopPropagation();
    const id      = dotsBtn.dataset.id;
    const menu    = document.getElementById('dots-' + id);
    const wasOpen = menu && menu.classList.contains('open');
    closeAllMenus();
    if (menu && !wasOpen) {
        // Iposition ang menu gamit ang button coordinates
        const rect = dotsBtn.getBoundingClientRect();
        const menuWidth = 200;
        let top  = rect.bottom + 6;
        let left = rect.right - menuWidth;

        // Kung malapit sa ibaba ng screen, buksan pataas
        // Kung malapit sa ibaba ng screen, buksan pataas
menu.style.visibility = 'hidden';
menu.style.display = 'block';
const menuHeight = menu.offsetHeight;
menu.style.display = '';
menu.style.visibility = '';

if (top + menuHeight > window.innerHeight - 8) {
    top = rect.top - menuHeight - 6;
}
        // Kung malapit sa kaliwa ng screen
        if (left < 8) left = 8;

        menu.style.top  = top + 'px';
        menu.style.left = left + 'px';
        menu.classList.add('open');
    }
    return;
}
        const actionBtn = e.target.closest('.bk-action-btn');
        if (actionBtn) {
            e.stopPropagation();
            closeAllMenus();
            const { action, id, name, date } = actionBtn.dataset;
            openConfirmDialog(action, parseInt(id, 10), name, date);
            return;
        }
        closeAllMenus();
    });

    function closeAllMenus() {
        document.querySelectorAll('.dots-menu.open').forEach(m => m.classList.remove('open'));
    }

    // ── Confirm dialog
    let pendingAction = null;
    let pendingId     = null;

    const dialogConfig = {
        confirm:  { icon: '<i class="fa-regular fa-circle-check" style="color:#1e4d2b;font-size:52px;"></i>',  title: 'Confirm Booking',      msgFn: (n,d) => `Confirm reservation of ${n} on ${d}?`,                        btnText: 'Yes, Confirm',  btnClass: '' },
        cancel:   { icon: '<i class="fa-regular fa-circle-xmark" style="color:#c0392b;font-size:52px;"></i>', title: 'Cancel Booking',       msgFn: (n,d) => `Cancel reservation of ${n} on ${d}? This cannot be undone.`,  btnText: 'Yes, Cancel',   btnClass: 'danger' },
        delete:   { icon: '<i class="fa-regular fa-trash-can" style="color:#c0392b;font-size:52px;"></i>',    title: 'Delete Booking',       msgFn: (n,d) => `Permanently delete reservation of ${n} on ${d}?`,             btnText: 'Yes, Delete',   btnClass: 'danger' },
        complete: { icon: '<i class="fa-regular fa-circle-check" style="color:#3b82f6;font-size:52px;"></i>', title: 'Mark as Completed',    msgFn: (n,d) => `Mark reservation of ${n} on ${d} as completed?\nStaff costs will be auto-deducted.`, btnText: 'Yes, Complete', btnClass: '' },
    };

    function openConfirmDialog(action, id, name, date) {
        if (!dialogConfig[action]) { showToast('Unknown action.', true); return; }
        pendingAction = action;
        pendingId     = parseInt(id, 10);
        const cfg = dialogConfig[action];
        document.getElementById('confirmIcon').innerHTML    = cfg.icon;
        document.getElementById('confirmTitle').textContent = cfg.title;
        document.getElementById('confirmMsg').textContent   = cfg.msgFn(name, date);
        const btn = document.getElementById('confirmYesBtn');
        btn.textContent = cfg.btnText;
        btn.className   = 'confirm-btn-yes ' + cfg.btnClass;
        document.getElementById('confirmOverlay').classList.add('open');
    }

    function closeConfirm() {
        document.getElementById('confirmOverlay').classList.remove('open');
        pendingAction = null;
        pendingId     = null;
    }

    function executeAction() {
    if (!pendingAction || !pendingId || isNaN(pendingId)) {
        showToast('Invalid booking ID. Please try again.', true);
        closeConfirm();
        return;
    }
    const form       = document.getElementById('actionForm');
    const id         = pendingId;
    const actionName = pendingAction; // ← i-save muna bago mag-closeConfirm
    closeConfirm();
    form.action = `${BASE_URL}/admin/bookings/${id}/action`;
    document.getElementById('actionInput').value = actionName; // ← gamit ang saved value
    form.submit();
}

    // ── Recompute total net (sums net column minus any deduction rows)
    function recomputeCompletedTotal() {
        let total = 0;
        document.querySelectorAll('[id^="ctx-net-"]').forEach(el => {
            total += parseFloat(el.textContent.replace(/[₱,]/g, '')) || 0;
        });
        document.querySelectorAll('.deduction-amount-cell').forEach(el => {
            total -= parseFloat(el.dataset.amount) || 0;
        });
        const el = document.getElementById('completedNetTotal');
        if (el) el.textContent = '₱' + formatNum(Math.max(total, 0));
    }

    // ── Global deductions
    function renderDeductionRow(ded, id) {
        const tbody = document.getElementById('completedTbody');
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.className     = 'deduction-data-row';
        tr.dataset.dedId = id;
        tr.innerHTML = `
            <td colspan="4" style="padding:12px 10px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:#fde8e8;border-radius:50%;color:#c0392b;font-size:12px;font-weight:900;">−</span>
                    <span style="font-weight:700;font-size:13px;color:#c0392b;">Additional Deduction</span>
                    <span style="font-size:11px;font-style:italic;color:#8aaa92;">— ${escHtml(ded.reason)}</span>
                    <button class="remove-ded-btn" onclick="removeDeduction(${id})"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </td>
            <td></td>
            <td class="deduction-amount-cell" data-amount="${ded.amount}" style="font-weight:800;color:#c0392b;">− ₱${formatNum(ded.amount)}</td>
            <td><span class="status-badge" style="background:#fde8e8;color:#c0392b;">Deducted</span></td>`;
        tbody.appendChild(tr);
    }

    function applyGlobalDeduction() {
        const amt    = parseFloat(document.getElementById('globalDeductionAmt').value) || 0;
        const reason = (document.getElementById('globalDeductionReason').value || '').trim();
        if (amt <= 0) { showToast('Enter a deduction amount.', true); return; }
        if (!reason)  { showToast('Enter a reason for the deduction.', true); return; }

        const dateInput = document.querySelector('input[name="date"]');

        fetch('/admin/deductions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                amount:         amt,
                reason:         reason,
                deduction_date: dateInput ? dateInput.value : new Date().toISOString().slice(0, 10),
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderDeductionRow(data.deduction, data.deduction.id);
                recomputeCompletedTotal();
                document.getElementById('globalDeductionAmt').value    = '';
                document.getElementById('globalDeductionReason').value = '';
                showToast('Deduction applied.');
            } else {
                showToast('Failed to save deduction.', true);
            }
        })
        .catch(() => showToast('Network error.', true));
    }

    function removeDeduction(id) {
        fetch(`/admin/deductions/${id}`, {
            method:  'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-ded-id="${id}"]`)?.remove();
                recomputeCompletedTotal();
                showToast('Deduction removed.');
            }
        })
        .catch(() => showToast('Network error.', true));
    }

    // Restore deductions on page load
    (function() {
        const deductions = @json($deductions);
        deductions.forEach(d => renderDeductionRow(d, d.id));
        if (deductions.length > 0) recomputeCompletedTotal();
    })();

    // ── Toast
    function showToast(msg, isError = false) {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        t.className = 'toast' + (isError ? ' error' : '');
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    @if(session('success')) showToast(@json(session('success'))); @endif
    @if(session('error'))   showToast(@json(session('error')), true); @endif
    @if(session('switch_to_completed')) switchToTab('completed'); @endif

    // ── Helpers
    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function formatNum(n) {
        return parseFloat(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.getElementById('confirmOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeConfirm();
    });
</script>
@include('components.modals')
@include('components.notification-bell')
@include('components.feedback-modal')
</body>
</html>