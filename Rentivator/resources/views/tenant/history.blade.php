@php use Illuminate\Support\Facades\Storage; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentivator - History</title>
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
            width: 240px; min-width: 240px; background: #1e4d2b;
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 100; transition: transform 0.28s cubic-bezier(.4,0,.2,1);
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
            transition: all 0.2s; cursor: pointer;
            border-left: 3px solid transparent;
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.08); color: white;
            border-left: 3px solid #6DBE47; padding-left: 21px;
        }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; }
        .sidebar-bottom { padding: 16px; border-top: 1px solid rgba(255,255,255,0.08); }
        .sidebar-user { display: flex; align-items: center; gap: 10px; padding: 10px 0 0; }
        .sidebar-user .avatar {
            width: 32px; height: 32px; background: #2d6a3f; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 13px; font-weight: 700;
        }
        .sidebar-user-info .name { font-size: 12px; font-weight: 800; color: white; }
        .sidebar-user-info .role { font-size: 10px; color: rgba(255,255,255,0.4); letter-spacing: 1px; text-transform: uppercase; }

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

        .section-card {
            background: white; border-radius: 16px;
            padding: 28px 32px; margin-bottom: 20px;
            border: 1px solid #e8f0e8;
        }
        .section-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .section-title-block { display: flex; align-items: center; gap: 16px; }
        .section-icon {
            width: 52px; height: 52px; background: #e8f5e9; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #1e4d2b; flex-shrink: 0;
        }
        .section-title { font-size: 22px; font-weight: 900; color: #1a3d24; letter-spacing: 1px; }
        .section-sub   { font-size: 11px; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; }

        .history-toolbar {
            background: white; border-radius: 12px; padding: 14px 20px;
            border: 1px solid #e8f0e8;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; margin-bottom: 16px;
        }
        .toolbar-left { display: flex; align-items: center; gap: 14px; }

        .btn-top-delete {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 18px; background: white; color: #e74c3c;
            border: 1.5px solid #fde8e8; border-radius: 8px;
            font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-top-delete:hover { background: #fde8e8; }

        .select-panel {
            display: none; align-items: center; gap: 14px; flex-wrap: wrap;
        }
        .select-panel.visible { display: flex; }

        .select-all-wrap { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .select-all-wrap input[type="checkbox"] {
            width: 18px; height: 18px; accent-color: #1e4d2b; cursor: pointer;
        }
        .select-all-label { font-size: 12px; font-weight: 800; color: #5a7a5e; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; user-select: none; }
        .selected-count { font-size: 11px; color: #8aaa92; font-weight: 700; }

        .btn-delete-selected {
            display: none; align-items: center; gap: 8px;
            padding: 9px 18px; background: #c0392b; color: white;
            border: none; border-radius: 8px;
            font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-delete-selected.visible { display: flex; }
        .btn-delete-selected:hover { background: #a93226; }

        .btn-cancel-select {
            display: flex; align-items: center; gap: 6px;
            padding: 9px 14px; background: white; color: #8aaa92;
            border: 1.5px solid #e8f0e8; border-radius: 8px;
            font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-cancel-select:hover { background: #f4f7f4; }

        /* ── HISTORY CARD — always single flex row ── */
        .history-card {
            background: white; border-radius: 12px;
            margin-bottom: 10px; border: 1px solid #e8f0e8;
            display: flex; align-items: stretch;
            min-height: 80px; overflow: visible;
            transition: all 0.2s; position: relative;
        }
        .history-card:hover { border-color: #c8e6c9; box-shadow: 0 4px 16px rgba(30,77,43,0.08); }
        .history-card.selected { border-color: #1e4d2b; background: #f9fdf9; }
        .history-card > *:first-child { border-radius: 12px 0 0 12px; }
        .history-card > *:last-child  { border-radius: 0 12px 12px 0; }

        /* Checkbox column — hidden by default, shown in select mode */
        .card-check {
            flex: 0 0 48px; width: 48px;
            display: none; align-items: center; justify-content: center;
            border-right: 1px solid #f0f5f0;
        }
        .card-check.visible { display: flex; }
        .card-check input[type="checkbox"] {
            width: 18px; height: 18px; accent-color: #1e4d2b; cursor: pointer;
        }

        /* Equal-width data cells */
        .hk-cell {
            flex: 1 1 0;
            padding: 14px 16px;
            display: flex; flex-direction: column; justify-content: center;
            border-left: 1px solid #f0f5f0;
            min-width: 0;
        }
        .hk-cell:first-of-type { border-left: none; }

        .hk-label {
            font-size: 9px; font-weight: 800; letter-spacing: 2px;
            text-transform: uppercase; color: #8aaa92; margin-bottom: 5px;
            white-space: nowrap;
        }
        .hk-value {
            font-size: 13px; font-weight: 800; color: #1a3d24;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .hk-sub {
            font-size: 9px; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; color: #8aaa92; margin-top: 3px;
            white-space: nowrap;
        }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 10px; border-radius: 20px;
            font-size: 9px; font-weight: 800; letter-spacing: 1px;
            text-transform: uppercase; white-space: nowrap;
            width: fit-content;
        }
        .status-completed { background: #e8f5e9; color: #2d6a3f; }

        .fee-value { font-size: 16px; font-weight: 900; color: #1a3d24; }

        .empty-state {
            text-align: center; padding: 80px 20px;
            background: white; border-radius: 16px; border: 1px solid #e8f0e8;
        }
        .empty-state i { font-size: 52px; color: #c8e6c9; margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 20px; font-weight: 800; color: #8aaa92; margin-bottom: 8px; }
        .empty-state p  { font-size: 13px; color: #b8d4bc; }

        .confirm-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55); z-index: 500;
            align-items: center; justify-content: center; padding: 20px;
        }
        .confirm-overlay.open { display: flex; }
        .confirm-box {
            background: white; border-radius: 20px;
            padding: 40px 36px; text-align: center;
            max-width: 360px; width: 100%;
            box-shadow: 0 24px 60px rgba(0,0,0,0.3);
            animation: cfIn 0.22s cubic-bezier(.4,0,.2,1);
        }
        @keyframes cfIn {
            from { opacity: 0; transform: scale(0.92); }
            to   { opacity: 1; transform: scale(1); }
        }
        .confirm-icon  { font-size: 52px; margin-bottom: 16px; display: block; }
        .confirm-title { font-size: 18px; font-weight: 900; color: #1a3d24; margin-bottom: 8px; }
        .confirm-msg   { font-size: 13px; color: #5a7a5e; line-height: 1.6; margin-bottom: 28px; }
        .confirm-btns  { display: flex; gap: 12px; }
        .confirm-btn-cancel {
            flex: 1; padding: 13px; border: 1.5px solid #e8f0e8;
            border-radius: 10px; background: white; color: #5a7a5e;
            font-size: 13px; font-weight: 800; cursor: pointer; transition: all 0.2s;
        }
        .confirm-btn-cancel:hover { background: #f4f7f4; }
        .confirm-btn-ok {
            flex: 1; padding: 13px; border: none; border-radius: 10px;
            background: #c0392b; color: white;
            font-size: 13px; font-weight: 800; cursor: pointer; transition: all 0.2s;
        }
        .confirm-btn-ok:hover { background: #a93226; }

        .toast {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background: white; border-radius: 16px;
            padding: 32px 40px; text-align: center;
            z-index: 9999; min-width: 280px; max-width: 360px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
            opacity: 0; transition: all 0.25s cubic-bezier(.4,0,.2,1);
            pointer-events: none;
        }
        .toast.show { opacity: 1; transform: translate(-50%, -50%) scale(1); pointer-events: auto; }
        .toast-icon { margin-bottom: 14px; display: block; text-align: center; }
        .toast-msg  { font-size: 14px; font-weight: 700; color: #1a3d24; line-height: 1.5; }

        /* ════════════════════════════════
           RESPONSIVE — sidebar/topbar only
           History card stays single flex row
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
        }

        @media (max-width: 600px) {
            .content { padding: 12px; }
            .section-card { padding: 16px; }
            .history-toolbar { padding: 12px 14px; }
            .hk-cell { padding: 10px 10px; }
            .hk-label { font-size: 8px; }
            .hk-value { font-size: 11px; }
            .hk-sub { font-size: 8px; }
            .fee-value { font-size: 13px; }
            .btn-top-delete,
            .btn-cancel-select,
            .btn-delete-selected { font-size: 10px; padding: 8px 10px; }
        }

        /* Hide fee on very small phones */
        @media (max-width: 400px) {
            .cell-fee { display: none; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeSidebar()"><i class="fa-solid fa-xmark"></i></button>
    <div class="sidebar-logo" style="display:flex; align-items:center; justify-content:center; padding:8px 24px; border-bottom:1px solid rgba(255,255,255,0.08);">
    <x-app-logo />
</div>
    <nav class="sidebar-nav">
        <a class="nav-item" href="{{ route('tenant.vehicle') }}">
            <i class="fa-solid fa-tractor"></i> My Vehicle
        </a>
        <a class="nav-item" href="{{ route('tenant.reservation') }}">
            <i class="fa-regular fa-calendar-check"></i> Reservation
        </a>
        <a class="nav-item active" href="{{ route('tenant.history') }}">
            <i class="fa-regular fa-clock"></i> History
        </a>
    </nav>
    <div class="sidebar-bottom">
        @include('components.sidebar-bottom')
    </div>
</aside>

<main class="main">
    <div class="topbar">
        <div style="display:flex; align-items:center; gap:12px;">
            <button class="hamburger" onclick="openSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="topbar-brand">
                <div class="topbar-brand-text">
                    <div class="name">HISTORY</div>
                </div>
            </div>
        </div>
        @include('components.topbar')
    </div>

    <div class="content">

        @if($reservations->count() > 0)
        <div class="history-toolbar">
            <div class="toolbar-left">
                <div class="select-panel" id="selectPanel">
                    <label class="select-all-wrap">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                        <span class="select-all-label">Select All</span>
                    </label>
                    <span class="selected-count" id="selectedCount"></span>
                </div>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <button class="btn-delete-selected" id="btnDeleteSelected" onclick="askDeleteSelected()">
                    <i class="fa-regular fa-trash-can"></i> Delete Selected
                </button>
                <button class="btn-cancel-select" id="btnCancelSelect" style="display:none;" onclick="exitSelectionMode()">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </button>
                <button class="btn-top-delete" id="btnTopDelete" onclick="enterSelectionMode()">
                    <i class="fa-regular fa-trash-can"></i> Delete
                </button>
            </div>
        </div>
        @endif

        @forelse($reservations as $r)
            @php
                $total    = ($r->vehicle->rate ?? 0) * ($r->hectares ?? 1);
                $hectares = (int)($r->hectares ?? 1);
            @endphp

            <div class="history-card" id="hcard-{{ $r->id }}">

                {{-- Checkbox (shown only in select mode) --}}
                <div class="card-check" id="check-{{ $r->id }}">
                    <input type="checkbox" class="row-check" value="{{ $r->id }}" onchange="onRowCheck()">
                </div>

                {{-- Vehicle --}}
                <div class="hk-cell">
                    <div class="hk-label">Vehicle</div>
                    <div class="hk-value">{{ $r->vehicle->name ?? 'N/A' }}</div>
                    <div class="hk-sub">{{ $r->vehicle->type ?? '' }}</div>
                </div>

                {{-- Date --}}
                <div class="hk-cell">
                    <div class="hk-label">Date</div>
                    <div class="hk-value">
                        <i class="fa-regular fa-calendar" style="color:#6DBE47; margin-right:5px;"></i>{{ \Carbon\Carbon::parse($r->reservation_date)->format('M d, Y') }}
                    </div>
                    <div class="hk-sub">{{ $hectares }} hectares</div>
                </div>

                {{-- Status --}}
                <div class="hk-cell">
                    <div class="hk-label">Status</div>
                    <div style="margin-top:4px;">
                        <span class="status-badge status-completed">
                            <i class="fa-regular fa-circle-check"></i> Completed
                        </span>
                    </div>
                </div>

                {{-- Fee --}}
                <div class="hk-cell cell-fee">
                    <div class="hk-label">Fee Settled</div>
                    <div class="fee-value">₱{{ number_format($total) }}</div>
                    <div class="hk-sub">₱{{ number_format($r->vehicle->rate ?? 0) }} × {{ $hectares }} ha</div>
                </div>

            </div>
        @empty
            <div class="empty-state">
                <i class="fa-regular fa-clock"></i>
                <h3>No history yet</h3>
                <p>Completed reservations will appear here.</p>
            </div>
        @endforelse

    </div>
</main>

<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <span class="confirm-icon"><i class="fa-regular fa-trash-can" style="color:#c0392b; font-size:52px;"></i></span>
        <div class="confirm-title" id="confirmTitle">Delete Record?</div>
        <div class="confirm-msg" id="confirmMsg">Are you sure you want to delete this history record?</div>
        <div class="confirm-btns">
            <button class="confirm-btn-cancel" onclick="closeConfirm()">No, Keep It</button>
            <button class="confirm-btn-ok" id="confirmOkBtn">Yes, Delete</button>
        </div>
    </div>
</div>

<div class="toast" id="toast">
    <div class="toast-icon" id="toastIcon"></div>
    <div class="toast-msg"  id="toastMsg"></div>
</div>

<script>
    let pendingMode = null;

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

    function enterSelectionMode() {
        document.getElementById('selectPanel').classList.add('visible');
        document.getElementById('btnCancelSelect').style.display = 'flex';
        document.getElementById('btnTopDelete').style.display = 'none';
        document.querySelectorAll('.card-check').forEach(el => el.classList.add('visible'));
    }

    function exitSelectionMode() {
        document.getElementById('selectPanel').classList.remove('visible');
        document.getElementById('btnCancelSelect').style.display = 'none';
        document.getElementById('btnTopDelete').style.display = 'flex';
        document.getElementById('btnDeleteSelected').classList.remove('visible');
        document.getElementById('selectedCount').textContent = '';
        document.getElementById('selectAll').checked = false;
        document.querySelectorAll('.history-card').forEach(c => c.classList.remove('selected'));
        document.querySelectorAll('.card-check').forEach(el => {
            el.classList.remove('visible');
            el.querySelector('input').checked = false;
        });
    }

    function toggleSelectAll(cb) {
        document.querySelectorAll('.row-check').forEach(c => {
            c.checked = cb.checked;
            c.closest('.history-card').classList.toggle('selected', cb.checked);
        });
        updateDeleteBtn();
    }

    function onRowCheck() {
        const all     = document.querySelectorAll('.row-check');
        const checked = document.querySelectorAll('.row-check:checked');
        document.getElementById('selectAll').checked = (all.length === checked.length && all.length > 0);
        all.forEach(c => c.closest('.history-card').classList.toggle('selected', c.checked));
        updateDeleteBtn();
    }

    function updateDeleteBtn() {
        const count = document.querySelectorAll('.row-check:checked').length;
        const btn   = document.getElementById('btnDeleteSelected');
        const countEl = document.getElementById('selectedCount');
        if (count > 0) {
            btn.classList.add('visible');
            countEl.textContent = count + ' selected';
        } else {
            btn.classList.remove('visible');
            countEl.textContent = '';
        }
    }

    function askDeleteSelected() {
        const count = document.querySelectorAll('.row-check:checked').length;
        if (count === 0) return;
        pendingMode = 'selected';
        document.getElementById('confirmTitle').textContent = 'Delete ' + count + ' Record(s)?';
        document.getElementById('confirmMsg').textContent =
            'Are you sure you want to delete ' + count + ' selected history record(s)? This cannot be undone.';
        document.getElementById('confirmOverlay').classList.add('open');
    }

    function closeConfirm() {
        document.getElementById('confirmOverlay').classList.remove('open');
        pendingMode = null;
    }

    document.getElementById('confirmOkBtn').addEventListener('click', function () {
        if (pendingMode === 'selected') {
            const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(c => c.value);
            closeConfirm();
            fetch('/tenant/history/delete-selected', {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    ids.forEach(id => document.getElementById('hcard-' + id)?.remove());
                    showToast('success', 'Selected records deleted.');
                    exitSelectionMode();
                    checkEmpty();
                } else {
                    showToast('error', data.message || 'Could not delete records.');
                }
            })
            .catch(() => showToast('error', 'Network error.'));
        }
    });

    function checkEmpty() {
        if (document.querySelectorAll('.history-card').length === 0) {
            window.location.reload();
        }
    }

    function showToast(type, msg, callback) {
        const icons = {
            success: { icon: 'fa-circle-check', color: '#1e4d2b' },
            error:   { icon: 'fa-circle-xmark', color: '#b91c1c' },
        };
        const t = icons[type] || icons.success;
        document.getElementById('toastIcon').innerHTML =
            `<i class="fa-solid ${t.icon}" style="font-size:40px; color:${t.color};"></i>`;
        document.getElementById('toastMsg').textContent = msg;
        document.getElementById('toast').classList.add('show');
        setTimeout(() => {
            document.getElementById('toast').classList.remove('show');
            if (callback) callback();
        }, 2200);
    }
</script>
@include('components.modals')
@include('components.notification-bell')
@include('components.feedback-modal')
</body>
</html>