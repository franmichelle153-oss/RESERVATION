@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentivator - Tenants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f4;
            display: flex;
            min-height: 100vh;
            color: #1a3d24;
        }

        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.45); z-index: 99;
        }
        .sidebar-overlay.open { display: block; }

        .sidebar {
            width: 240px; min-width: 240px;
            background: #1e4d2b;
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform 0.28s cubic-bezier(.4,0,.2,1);
        }
        .sidebar-logo {
            display: flex; align-items: center; gap: 12px;
            padding: 28px 24px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo .logo-icon {
            width: 42px; height: 42px; background: #2d6a3f; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: white;
        }
        .sidebar-logo span { font-size: 16px; font-weight: 900; color: white; letter-spacing: 2px; }
        .sidebar-close {
            display: none; position: absolute; top: 16px; right: 14px;
            background: rgba(255,255,255,0.12); border: none; border-radius: 8px;
            width: 34px; height: 34px; color: white; font-size: 16px;
            cursor: pointer; align-items: center; justify-content: center;
        }
        .sidebar-nav { flex: 1; padding: 24px 0; }
        .nav-item {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 24px; color: rgba(255,255,255,0.55);
            text-decoration: none; font-size: 13px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            transition: all 0.2s; border-left: 3px solid transparent;
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.08); color: white;
            border-left: 3px solid #6DBE47; padding-left: 21px;
        }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; }
        .sidebar-bottom { padding: 16px; border-top: 1px solid rgba(255,255,255,0.08); }
        .farmer-portal-btn {
            width: 100%; padding: 12px; background: white; color: #1e4d2b;
            border: none; border-radius: 8px; font-size: 11px; font-weight: 800;
            letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer; transition: all 0.2s;
        }
        .farmer-portal-btn:hover { background: #e8f5e9; }
        .sidebar-user { display: flex; align-items: center; gap: 10px; padding: 14px 16px 0; }
        .sidebar-user .avatar {
            width: 32px; height: 32px; background: #2d6a3f; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 13px; font-weight: 700;
        }
        .sidebar-user span { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.4); letter-spacing: 1px; text-transform: uppercase; }

        .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-width: 0; }

        .topbar {
            background: white; padding: 0 32px; height: 68px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #e8f0e8;
            position: sticky; top: 0; z-index: 50; gap: 12px;
        }
        .hamburger {
            display: none; background: none; border: none;
            color: #1e4d2b; font-size: 22px; cursor: pointer;
            padding: 4px 8px; border-radius: 8px; flex-shrink: 0;
        }
        .topbar-brand { display: flex; align-items: center; gap: 12px; }
        .topbar-brand .logo-icon {
            width: 38px; height: 38px; background: #1e4d2b; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: white; flex-shrink: 0;
        }
        .topbar-brand-text .name { font-size: 15px; font-weight: 900; color: #1a3d24; letter-spacing: 2px; }
        .topbar-brand-text .sub  { font-size: 10px; color: #8aaa92; letter-spacing: 2px; text-transform: uppercase; }
        .topbar-right { display: flex; align-items: center; gap: 20px; flex-shrink: 0; }
        .notif-btn {
            position: relative; width: 38px; height: 38px;
            border: none; background: transparent; cursor: pointer;
            color: #5a7a5e; font-size: 18px;
            display: flex; align-items: center; justify-content: center;
        }
        .notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 8px; height: 8px; background: #e74c3c;
            border-radius: 50%; border: 2px solid white;
        }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 38px; height: 38px; background: #1e4d2b; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 14px; flex-shrink: 0;
        }
        .user-text .user-name { font-size: 13px; font-weight: 700; color: #1a3d24; }
        .user-text .user-role { font-size: 10px; color: #8aaa92; letter-spacing: 1px; text-transform: uppercase; }

        .content { padding: 32px; flex: 1; min-width: 0; }

        .page-banner {
            background: linear-gradient(135deg, #1e4d2b, #2d6a3f);
            border-radius: 16px; padding: 28px 32px; margin-bottom: 28px;
            color: white; display: flex; align-items: center;
            justify-content: space-between; flex-wrap: wrap; gap: 16px;
        }
        .page-banner h1 { font-size: 28px; font-weight: 900; }
        .page-banner p  { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 4px; }

        .section-card {
            background: white; border-radius: 16px;
            padding: 20px 24px; margin-bottom: 20px;
            border: 1px solid #e8f0e8;
        }
        .search-box {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; border: 1.5px solid #ddeedd;
            border-radius: 10px; background: white;
        }
        .search-box i { color: #8aaa92; font-size: 14px; flex-shrink: 0; }
        .search-box input {
            border: none; outline: none; font-size: 13px; color: #1a3d24;
            background: transparent; width: 100%; min-width: 0;
        }
        .search-box input::placeholder { color: #b8d4bc; }

        .stats-mini {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 16px; margin-bottom: 24px;
        }
        .stat-mini {
            background: white; border-radius: 12px;
            padding: 18px 20px; border: 1px solid #e8f0e8;
            display: flex; align-items: center; gap: 14px;
        }
        .stat-mini-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .stat-mini-icon.green  { background: #e8f5e9; color: #22c55e; }
        .stat-mini-icon.blue   { background: #e8f0ff; color: #3b82f6; }
        .stat-mini-icon.yellow { background: #fff8e1; color: #f59e0b; }
        .stat-mini-label { font-size: 10px; font-weight: 700; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 2px; }
        .stat-mini-value { font-size: 22px; font-weight: 900; color: #1a3d24; }

        /* ── TENANT ROW — always single flex row ── */
        .tenant-row {
            background: white; border-radius: 12px;
            margin-bottom: 10px; border: 1px solid #e8f0e8;
            display: flex; align-items: stretch;
            min-height: 80px; overflow: visible;
            transition: all 0.2s; position: relative;
        }
        .tenant-row:hover { border-color: #c8e6c9; box-shadow: 0 4px 16px rgba(30,77,43,0.08); }
        .tenant-row > *:first-child { border-radius: 12px 0 0 12px; }
        .tenant-row > *:last-child  { border-radius: 0 12px 12px 0; }

        /* Each cell is equal width */
        .bk-cell {
    flex: 1 1 0;
    padding: 14px 16px;
    display: flex; 
    flex-direction: column; 
    justify-content: center;
    align-items: flex-start;  /* existing cells left-aligned */
    border-left: 1px solid #f0f5f0;
    min-width: 0;
}
        .bk-cell:first-child { border-left: none; }

        .booking-label { font-size: 9px; font-weight: 800; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 4px; white-space: nowrap; }
        .booking-value { font-size: 13px; font-weight: 700; color: #1a3d24; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .farmer-cell { display: flex; align-items: center; gap: 8px; }
        .farmer-avatar {
            width: 32px; height: 32px; background: #e8f5e9; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #2d6a3f; font-size: 13px; flex-shrink: 0; font-weight: 700;
        }

        .badge-reservations {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 20px;
            font-size: 10px; font-weight: 800;
            letter-spacing: 1px; text-transform: uppercase; white-space: nowrap;
            background: #e8f0ff; color: #3b82f6;
        }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 48px; color: #c8e6c9; margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 18px; font-weight: 800; color: #8aaa92; margin-bottom: 8px; }
        .empty-state p  { font-size: 13px; color: #b8d4bc; }

        /* Toast */
        .toast {
            position: fixed; bottom: 24px; right: 24px;
            background: #1e4d2b; color: white;
            padding: 14px 20px; border-radius: 10px;
            font-size: 13px; font-weight: 700;
            display: flex; align-items: center; gap: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            transform: translateY(80px); opacity: 0;
            transition: all 0.3s; z-index: 300;
        }
        .toast.show  { transform: translateY(0); opacity: 1; }
        .toast.error { background: #c0392b; }

        /* ════════════════════════════════
           RESPONSIVE — sidebar/topbar only
           Tenant row stays single flex row
        ════════════════════════════════ */
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-close { display: flex; }
            .main { margin-left: 0; }
            .topbar { padding: 0 16px; }
            .hamburger { display: block; }
            .user-text { display: none; }
            .content { padding: 20px 16px; }
            .page-banner { padding: 20px; }
            .page-banner h1 { font-size: 22px; }
            .stats-mini { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        }

        @media (max-width: 600px) {
            .content { padding: 12px; }
            .stats-mini { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-mini { padding: 14px; }
            .stat-mini-value { font-size: 18px; }
            .section-card { padding: 14px; }
            .topbar { padding: 0 12px; height: 58px; }
            .topbar-brand-text .sub { display: none; }
            .bk-cell { padding: 10px 10px; }
            .booking-label { font-size: 8px; }
            .booking-value { font-size: 11px; }
        }

        /* Hide address on very small phones to keep row clean */
        @media (max-width: 400px) {
            .cell-address { display: none; }
        }

        @media (max-width: 640px) {
    .tenant-row {
        flex-direction: column;
        min-height: unset;
    }
    .tenant-row > *:first-child { border-radius: 12px 12px 0 0; }
    .tenant-row > *:last-child  { border-radius: 0 0 12px 12px; }
    .bk-cell {
        border-left: none;
        border-top: 1px solid #f0f5f0;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
    }
    .bk-cell:first-child { border-top: none; }
    .booking-label {
        margin-bottom: 0;
        margin-right: 12px;
        flex-shrink: 0;
        width: 80px;
    }
    .booking-value {
        text-align: right;
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
    }
    .cell-address { display: flex !important; }
}

.block-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; background: #fde8e8; color: #c0392b;
    border: 1.5px solid #f8b4b4; border-radius: 8px;
    font-size: 11px; font-weight: 800; cursor: pointer;
    letter-spacing: 1px; text-transform: uppercase; transition: all 0.2s;
    white-space: nowrap;
}
.block-btn:hover { background: #c0392b; color: white; border-color: #c0392b; }
.block-confirm-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.55); z-index: 600;
    align-items: center; justify-content: center; padding: 20px;
}
.block-confirm-overlay.open { display: flex; }
.block-confirm-box {
    background: white; border-radius: 20px; padding: 40px 36px;
    text-align: center; max-width: 380px; width: 100%;
    box-shadow: 0 24px 60px rgba(0,0,0,0.3);
}
.block-confirm-title { font-size: 18px; font-weight: 900; color: #1a3d24; margin-bottom: 8px; }
.block-confirm-msg { font-size: 13px; color: #5a7a5e; line-height: 1.6; margin-bottom: 28px; }
.block-confirm-btns { display: flex; gap: 12px; }
.block-btn-no  { flex: 1; padding: 13px; border: 1.5px solid #e8f0e8; border-radius: 10px; background: white; color: #5a7a5e; font-size: 13px; font-weight: 800; cursor: pointer; }
.block-btn-yes { flex: 1; padding: 13px; border: none; border-radius: 10px; background: #c0392b; color: white; font-size: 13px; font-weight: 800; cursor: pointer; }
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
        <a class="nav-item" href="{{ route('admin.dashboard') }}">
            <i class="fa-solid fa-table-cells-large"></i> Dashboard
        </a>
        <a class="nav-item" href="{{ route('admin.vehicle') }}">
            <i class="fa-solid fa-tractor"></i> Vehicle
        </a>
        <a class="nav-item" href="{{ route('admin.bookings') }}">
            <i class="fa-regular fa-calendar"></i> Bookings
        </a>
       
        <a class="nav-item active" href="{{ route('admin.tenants') }}">
            <i class="fa-solid fa-users"></i> Tenants
        </a>
        <a class="nav-item" href="{{ route('admin.feedback') }}">
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
                    <div class="name">TENANTS</div>
                </div>
            </div>
        </div>
        @include('components.topbar')
    </div>

    <div class="content">

        {{-- SEARCH --}}
        <div class="section-card">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Search by name, email, or phone..." oninput="filterTenants()">
            </div>
        </div>

        {{-- TENANT LIST --}}
        <div id="tenantList">
            @forelse ($tenants as $tenant)
                <div class="tenant-row"
                     data-search="{{ strtolower($tenant->name . ' ' . $tenant->email . ' ' . $tenant->phone_number) }}">

                    {{-- Name --}}
                    <div class="bk-cell">
                        <div class="booking-label">Tenant</div>
                        <div class="farmer-cell" style="margin-top:4px;">
                            <div class="farmer-avatar">
                                {{ strtoupper(substr($tenant->name, 0, 1)) }}
                            </div>
                            <span class="booking-value">{{ $tenant->name }}</span>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="bk-cell">
                        <div class="booking-label">Email</div>
                        <div class="booking-value">{{ $tenant->email }}</div>
                    </div>

                    {{-- Phone --}}
                    <div class="bk-cell">
                        <div class="booking-label">Phone Number</div>
                        <div class="booking-value">{{ $tenant->phone_number ?? '—' }}</div>
                    </div>

                    {{-- Address --}}
                    <div class="bk-cell cell-address">
                        <div class="booking-label">Address</div>
                        <div class="booking-value">{{ $tenant->address ?? '—' }}</div>
                    </div>

                    {{-- Block --}}
<div class="bk-cell" style="flex:0 0 140px; min-width:140px; align-items:center;">
    <button class="block-btn"
            data-id="{{ $tenant->id }}"
            data-name="{{ $tenant->name }}"
            onclick="openBlockConfirm(this.dataset.id, this.dataset.name)">
        <i class="fa-solid fa-ban"></i> Block
    </button>
</div>

                </div>
            @empty
                <div class="empty-state">
                    <i class="fa-solid fa-users-slash"></i>
                    <h3>No tenants found</h3>
                    <p>Tenants who have made at least one reservation will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</main>

{{-- TOAST --}}
<div class="toast" id="toast">
    <i class="fa-solid fa-circle-check"></i>
    <span id="toastMsg">Action completed.</span>
</div>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    function filterTenants() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.tenant-row').forEach(row => {
            const search = row.dataset.search || '';
            row.style.display = (!q || search.includes(q)) ? '' : 'none';
        });
    }

    function showToast(msg, isError = false) {
        const toast = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        toast.className = 'toast' + (isError ? ' error' : '');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    @if(session('success'))
        showToast("{{ session('success') }}");
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", true);
    @endif
</script>

{{-- BLOCK CONFIRM --}}
<div class="block-confirm-overlay" id="blockOverlay">
    <div class="block-confirm-box">
        <i class="fa-solid fa-ban" style="font-size:52px; color:#c0392b; display:block; margin-bottom:16px;"></i>
        <div class="block-confirm-title">Block Tenant?</div>
        <div class="block-confirm-msg" id="blockMsg"></div>
        <div class="block-confirm-btns">
            <button class="block-btn-no" onclick="closeBlockConfirm()">No, Go Back</button>
            <button class="block-btn-yes" onclick="executeBlock()">Yes, Block</button>
        </div>
    </div>
</div>

<form method="POST" id="blockForm" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
    let blockId = null;
    function openBlockConfirm(id, name) {
        blockId = id;
        document.getElementById('blockMsg').textContent =
            'This will permanently delete ' + name + '\'s account. This cannot be undone.';
        document.getElementById('blockOverlay').classList.add('open');
    }
    function closeBlockConfirm() {
        blockId = null;
        document.getElementById('blockOverlay').classList.remove('open');
    }
    function executeBlock() {
        if (!blockId) return;
        const form = document.getElementById('blockForm');
        form.action = '/admin/tenants/' + blockId;
        form.submit();
    }
    document.getElementById('blockOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeBlockConfirm();
    });
</script>

@include('components.modals')
@include('components.notification-bell')
@include('components.feedback-modal')
</body>
</html>