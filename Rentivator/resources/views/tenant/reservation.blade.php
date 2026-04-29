  <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentivator - Reservation</title>
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

        .content { padding: 32px; flex: 1; min-width: 0; }

        /* ── FILTER BAR ── */
        .filter-bar {
            background: white; border-radius: 12px; padding: 16px 20px;
            border: 1px solid #e8f0e8;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; margin-bottom: 16px;
        }
        .filter-label { font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; color: #8aaa92; }
        .filter-group { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .filter-btn {
            padding: 8px 16px; border: 1.5px solid #e8f0e8;
            border-radius: 8px; background: white;
            font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
            color: #8aaa92; cursor: pointer; transition: all 0.2s;
        }
        .filter-btn.active { border-color: #1e4d2b; background: #1e4d2b; color: white; }
        .filter-btn:hover:not(.active) { border-color: #2d6a3f; color: #1e4d2b; }

        /* ── BOOKING CARD ── */
        .booking-card {
            background: white; border-radius: 14px; border: 1px solid #e8f0e8;
            margin-bottom: 10px; overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .booking-card:hover { border-color: #c8e6c9; box-shadow: 0 4px 16px rgba(30,77,43,0.08); }

        .card-inner {
            display: flex;
            align-items: stretch;
            min-height: 80px;
        }

        .bk-cell {
            padding: 14px 16px;
            display: flex; flex-direction: column; justify-content: center;
            border-left: 1px solid #f0f5f0;
            min-width: 0;
        }

        .cell-vehicle { flex: 1 1 0; border-left: none; }
        .cell-date    { flex: 1 1 0; }
        .cell-status  { flex: 1 1 0; }
        .cell-fee     { flex: 1 1 0; }
        .bk-action    { flex: 0 0 120px; width: 120px; display: flex; align-items: center; justify-content: center; padding: 0 16px; border-left: 1px solid #f0f5f0; gap: 6px; flex-direction: column; }

        .bk-label { font-size: 9px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #8aaa92; margin-bottom: 5px; white-space: nowrap; }
        .bk-value { font-size: 13px; font-weight: 800; color: #1a3d24; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bk-sub   { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #8aaa92; margin-top: 3px; white-space: nowrap; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 10px; border-radius: 20px;
            font-size: 9px; font-weight: 800; letter-spacing: 1px;
            text-transform: uppercase; white-space: nowrap; width: fit-content;
        }
        .status-confirmed { background: #e8f5e9; color: #2d6a3f; }
        .status-pending   { background: #fff8e1; color: #d97706; }
        .status-cancelled { background: #fde8e8; color: #c0392b; }

        .fee-amount { font-size: 16px; font-weight: 900; color: #1a3d24; }

        /* ── ACTION BUTTONS ── */
        .btn-delete {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 8px; background: #fde8e8;
            border: 1.5px solid #f5c6c6; border-radius: 7px;
            color: #c0392b; font-size: 9px; font-weight: 800;
            letter-spacing: 1px; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s; white-space: nowrap;
        }
        .btn-delete i { font-size: 9px; }
        .btn-delete:hover { background: #c0392b; color: white; border-color: #c0392b; }

        /* ── NEW: Cancel button (orange/warning style) ── */
        .btn-cancel-res {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 8px; background: #fff3cd;
            border: 1.5px solid #ffc107; border-radius: 7px;
            color: #856404; font-size: 9px; font-weight: 800;
            letter-spacing: 1px; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s; white-space: nowrap;
        }
        .btn-cancel-res i { font-size: 9px; }
        .btn-cancel-res:hover { background: #d97706; color: white; border-color: #d97706; }

        /* ── CANCELLATION REASON TOOLTIP (shown on cancelled cards) ── */
        .reason-tag {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 9px; color: #c0392b; font-weight: 700;
            margin-top: 5px; cursor: pointer;
            text-decoration: underline dotted;
        }

        /* ── CANCEL REASON MODAL ── */
        #cancelReasonModal {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            align-items: center; justify-content: center;
            z-index: 9999;
        }
        #cancelReasonModal.open { display: flex; }
        .cancel-modal-box {
            background: white; border-radius: 16px;
            padding: 36px 40px; max-width: 420px; width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .cancel-modal-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: #fff3cd; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 16px;
            font-size: 22px; color: #d97706;
        }
        .cancel-modal-title {
            font-size: 17px; font-weight: 900; color: #1a3d24; margin-bottom: 8px;
        }
        .cancel-modal-sub {
            font-size: 12px; color: #8aaa92; margin-bottom: 20px; line-height: 1.6;
        }
        .cancel-modal-textarea {
            width: 100%; border: 1.5px solid #e8f0e8;
            border-radius: 10px; padding: 12px 14px;
            font-size: 13px; font-family: inherit; color: #1a3d24;
            resize: none; height: 100px; outline: none;
            transition: border-color 0.2s;
        }
        .cancel-modal-textarea:focus { border-color: #d97706; }
        .cancel-modal-error {
            font-size: 11px; color: #c0392b; font-weight: 700;
            margin-top: 6px; display: none; text-align: left;
        }
        .cancel-modal-actions {
            display: flex; gap: 12px; justify-content: center; margin-top: 20px;
        }
        .btn-modal-back {
            padding: 11px 28px; border-radius: 10px;
            border: 1.5px solid #e8f0e8; background: white;
            color: #8aaa92; font-size: 12px; font-weight: 800;
            letter-spacing: 1px; text-transform: uppercase; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-modal-back:hover { border-color: #c8d8c8; color: #1a3d24; }
        .btn-modal-confirm-cancel {
            padding: 11px 28px; border-radius: 10px;
            border: none; background: #d97706;
            color: white; font-size: 12px; font-weight: 800;
            letter-spacing: 1px; text-transform: uppercase; cursor: pointer;
            transition: background 0.2s;
        }
        .btn-modal-confirm-cancel:hover { background: #b45309; }
        .btn-modal-confirm-cancel:disabled { background: #fcd34d; cursor: not-allowed; }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center; padding: 80px 20px;
            background: white; border-radius: 16px; border: 1px solid #e8f0e8;
        }
        .empty-state i { font-size: 52px; color: #c8e6c9; margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 20px; font-weight: 800; color: #8aaa92; margin-bottom: 8px; }
        .empty-state p  { font-size: 13px; color: #b8d4bc; margin-bottom: 20px; }
        .empty-state a  {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; background: #1e4d2b; color: white;
            border-radius: 10px; text-decoration: none;
            font-size: 12px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;
        }
        .empty-state a:hover { background: #2d6a3f; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-close { display: flex; }
            .main { margin-left: 0; }
            .topbar { padding: 0 16px; }
            .hamburger { display: block; }
            .content { padding: 20px 16px; }
        }

        @media (max-width: 600px) {
            .content { padding: 12px; }
            .filter-bar { padding: 12px 14px; }
            .card-inner { min-height: 60px; }
            .bk-cell   { padding: 10px 6px; min-width: 0; max-width: 100%; overflow: hidden; }
            .bk-action { flex: 0 0 56px; width: 56px; padding: 0 4px; overflow: hidden; gap: 4px; }
            .bk-label  { font-size: 7px; letter-spacing: 0.5px; white-space: normal; word-break: break-word; line-height: 1.2; margin-bottom: 3px; }
            .bk-value  { font-size: 10px; white-space: normal; word-break: break-word; line-height: 1.3; overflow: hidden; }
            .bk-sub    { font-size: 8px; white-space: normal; word-break: break-word; margin-top: 2px; overflow: hidden; }
            .cell-vehicle { flex: 0 0 22%; }
            .cell-date    { flex: 0 0 30%; }
            .cell-status  { flex: 0 0 22%; }
            .cell-fee     { flex: 0 0 26%; }
            .fee-amount { font-size: 12px; }
            .status-badge { font-size: 7px; padding: 3px 5px; gap: 2px; white-space: normal; word-break: break-word; }
            .filter-btn { padding: 6px 10px; font-size: 10px; }
            .btn-delete, .btn-cancel-res { font-size: 7px; padding: 3px 5px; }
            .cancel-modal-box { padding: 24px 20px; }
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
        <a class="nav-item active" href="{{ route('tenant.reservation') }}">
            <i class="fa-regular fa-calendar-check"></i> Reservation
        </a>
        <a class="nav-item" href="{{ route('tenant.history') }}">
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
                    <div class="name">RESERVATION</div>
                </div>
            </div>
        </div>
        @include('components.topbar')
    </div>

    <div class="content">

        <div class="filter-bar">
            <span class="filter-label">Status</span>
            <div class="filter-group">
                <button class="filter-btn active" onclick="filterStatus('all', this)">All</button>
                <button class="filter-btn" onclick="filterStatus('pending', this)">Pending</button>
                <button class="filter-btn" onclick="filterStatus('confirmed', this)">Confirmed</button>
                <button class="filter-btn" onclick="filterStatus('cancelled', this)">Cancelled</button>
            </div>
        </div>

        @forelse($reservations as $r)
            @php
                $total    = ($r->vehicle->rate ?? 0) * ($r->hectares ?? 1);
                $hectares = number_format($r->hectares ?? 1, 0);
            @endphp

            <div class="booking-card" data-status="{{ $r->status }}">
                <div class="card-inner">

                    {{-- Vehicle --}}
                    <div class="bk-cell cell-vehicle">
                        <div class="bk-label">Vehicle</div>
                        <div class="bk-value">{{ $r->vehicle->name ?? 'N/A' }}</div>
                        <div class="bk-sub">{{ $r->vehicle->type ?? '' }}</div>
                    </div>

                    {{-- Date --}}
                    <div class="bk-cell cell-date">
                        <div class="bk-label">Date</div>
                        <div class="bk-value">
                            <i class="fa-regular fa-calendar" style="color:#6DBE47; margin-right:4px;"></i>
                            {{ $r->reservation_date ? \Carbon\Carbon::parse($r->reservation_date)->format('M d, Y') : 'N/A' }}
                        </div>
                        <div class="bk-sub">{{ $hectares }} hectares</div>
                    </div>

                    {{-- Status --}}
                    <div class="bk-cell cell-status">
                        <div class="bk-label">Status</div>
                        <div style="margin-top:4px;">
                            @if($r->status === 'confirmed')
                                <span class="status-badge status-confirmed">
                                    <i class="fa-regular fa-circle-check"></i> Confirmed
                                </span>
                            @elseif($r->status === 'cancelled')
                                <span class="status-badge status-cancelled">
                                    <i class="fa-regular fa-circle-xmark"></i> Cancelled
                                </span>
                                {{-- Show reason if available --}}
                                @if($r->cancellation_reason)
                                    <div class="reason-tag" onclick="showReason('{{ addslashes($r->cancellation_reason) }}')">
                                        <i class="fa-solid fa-circle-info" style="font-size:8px;"></i>
                                        View reason
                                    </div>
                                @endif
                            @else
                                <span class="status-badge status-pending">
                                    <i class="fa-regular fa-clock"></i> Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Fee --}}
                    <div class="bk-cell cell-fee">
                        <div class="bk-label">Total Fee</div>
                        <div class="fee-amount">₱{{ number_format($total) }}</div>
                        <div class="bk-sub">₱{{ number_format($r->vehicle->rate ?? 0) }} × {{ $hectares }} ha</div>
                    </div>

                    {{-- Action --}}
                    <div class="bk-action">
                        {{-- Pending: show Cancel button --}}
                        @if($r->status === 'pending')
                            <button class="btn-cancel-res" onclick="openCancelModal({{ $r->id }})">
                                <i class="fa-solid fa-ban"></i> Cancel
                            </button>
                        @endif

                        {{-- Cancelled: show Delete button --}}
                        @if($r->status === 'cancelled')
                            <button class="btn-delete" onclick="confirmDelete({{ $r->id }})">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        @endif
                    </div>

                </div>
            </div>

        @empty
            <div class="empty-state">
                <i class="fa-regular fa-calendar-xmark"></i>
                <h3>No reservations yet</h3>
                <p>Book a vehicle to get started.</p>
                <a href="{{ route('tenant.vehicle') }}">
                    <i class="fa-solid fa-tractor"></i> Browse Fleet
                </a>
            </div>
        @endforelse

    </div>
</main>

{{-- ══════════════════════════════════════════════════════
     CANCEL REASON MODAL
══════════════════════════════════════════════════════ --}}
<div id="cancelReasonModal" onclick="handleModalBackdrop(event)">
    <div class="cancel-modal-box">
        <div class="cancel-modal-icon">
            <i class="fa-solid fa-ban"></i>
        </div>
        <div class="cancel-modal-title">Cancel Reservation</div>
        <div class="cancel-modal-sub">
            Please let us know why you're cancelling. This will be sent to the admin for review.
        </div>

        <textarea
            id="cancelReasonText"
            class="cancel-modal-textarea"
            placeholder="e.g. Change of schedule, found another service, weather concerns..."
            maxlength="500"
            oninput="onReasonInput()"
        ></textarea>
        <div id="cancelReasonError" class="cancel-modal-error">
            <i class="fa-solid fa-triangle-exclamation"></i> Please enter at least 5 characters.
        </div>

        <div class="cancel-modal-actions">
            <button class="btn-modal-back" onclick="closeCancelModal()">Go Back</button>
            <button class="btn-modal-confirm-cancel" id="btnConfirmCancel" onclick="submitCancel()">
                <i class="fa-solid fa-ban"></i> Confirm Cancel
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     VIEW REASON MODAL (for cancelled cards)
══════════════════════════════════════════════════════ --}}
<div id="viewReasonModal" onclick="if(event.target===this)this.style.display='none'"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45);
            align-items:center; justify-content:center; z-index:9999;">
    <div style="background:white; border-radius:16px; padding:32px 36px;
                max-width:380px; width:90%; text-align:center;
                box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="font-size:32px; color:#c0392b; margin-bottom:12px;">
            <i class="fa-regular fa-circle-xmark"></i>
        </div>
        <div style="font-size:15px; font-weight:900; color:#1a3d24; margin-bottom:10px;">
            Cancellation Reason
        </div>
        <div id="viewReasonText"
             style="font-size:13px; color:#5a7a62; line-height:1.7;
                    background:#fde8e8; border-radius:10px; padding:14px 16px;
                    text-align:left; margin-bottom:20px;">
        </div>
        <button onclick="document.getElementById('viewReasonModal').style.display='none'"
                style="padding:11px 28px; border-radius:10px;
                       border: 1.5px solid #e8f0e8; background:white;
                       color:#8aaa92; font-size:12px; font-weight:800;
                       letter-spacing:1px; text-transform:uppercase; cursor:pointer;">
            Close
        </button>
    </div>
</div>

<script>
    let _cancelTargetId = null;

    // ── Sidebar ──
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

    // ── Filter ──
    function filterStatus(status, el) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.booking-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
        });
    }

    // ── Cancel Modal ──
    function openCancelModal(id) {
        _cancelTargetId = id;
        document.getElementById('cancelReasonText').value = '';
        document.getElementById('cancelReasonError').style.display = 'none';
        document.getElementById('cancelReasonModal').classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('cancelReasonText').focus(), 100);
    }

    function closeCancelModal() {
        document.getElementById('cancelReasonModal').classList.remove('open');
        document.body.style.overflow = '';
        _cancelTargetId = null;
    }

    function handleModalBackdrop(e) {
        if (e.target === document.getElementById('cancelReasonModal')) {
            closeCancelModal();
        }
    }

    function onReasonInput() {
        const val = document.getElementById('cancelReasonText').value.trim();
        const err = document.getElementById('cancelReasonError');
        if (val.length >= 5) err.style.display = 'none';
    }

   function submitCancel() {
    const reason = document.getElementById('cancelReasonText').value.trim();
    const err    = document.getElementById('cancelReasonError');
    const btn    = document.getElementById('btnConfirmCancel');

    if (reason.length < 5) {
        err.style.display = 'block';
        document.getElementById('cancelReasonText').focus();
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

    const targetId = _cancelTargetId; // ← AYOS: sine-save bago ma-null

    fetch(`/tenant/reservation/${targetId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            closeCancelModal();

            document.querySelectorAll('.booking-card').forEach(card => {
                const cancelBtn = card.querySelector(`button[onclick="openCancelModal(${targetId})"]`);
                if (cancelBtn) {
                    card.dataset.status = 'cancelled';

                    const statusCell = card.querySelector('.cell-status div[style]');
                    if (statusCell) {
                        statusCell.innerHTML = `
                            <span class="status-badge status-cancelled">
                                <i class="fa-regular fa-circle-xmark"></i> Cancelled
                            </span>
                            <div class="reason-tag" onclick="showReason('${reason.replace(/'/g, "\\'")}')">
                                <i class="fa-solid fa-circle-info" style="font-size:8px;"></i>
                                View reason
                            </div>`;
                    }

                    const actionCell = card.querySelector('.bk-action');
                    if (actionCell) {
                        actionCell.innerHTML = `
                            <button class="btn-delete" onclick="confirmDelete(${targetId})">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>`;
                    }
                }
            });

            showToast('Reservation successfully cancelled.');
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-ban"></i> Confirm Cancel';
            alert(d.message || 'Failed to cancel. Please try again.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-ban"></i> Confirm Cancel';
        alert('Something went wrong. Please try again.');
    });
}

// ← DAGDAG: i-paste ito pagkatapos ng submitCancel()
function showToast(msg) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed; bottom:28px; left:50%; transform:translateX(-50%);
        background:#1e4d2b; color:white; padding:14px 28px;
        border-radius:12px; font-size:13px; font-weight:800;
        letter-spacing:1px; z-index:99999;
        box-shadow:0 8px 24px rgba(0,0,0,0.2);
        display:flex; align-items:center; gap:10px;
    `;
    toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color:#6DBE47;"></i> ${msg}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.4s';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

    // ── View Cancellation Reason ──
    function showReason(reason) {
        document.getElementById('viewReasonText').textContent = reason;
        document.getElementById('viewReasonModal').style.display = 'flex';
    }

    // ── Delete (existing logic, unchanged) ──
    function confirmDelete(id) {
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position:fixed; inset:0; background:rgba(0,0,0,0.45);
            display:flex; align-items:center; justify-content:center;
            z-index:9999;`;

        overlay.innerHTML = `
            <div style="background:white; border-radius:16px; padding:36px 40px;
                        max-width:380px; width:90%; text-align:center;
                        box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <div style="font-size:17px; font-weight:900; color:#1a3d24; margin-bottom:10px;">
                    Delete Reservation
                </div>
                <div style="font-size:13px; color:#8aaa92; margin-bottom:28px; line-height:1.6;">
                    Are you sure you want to delete this cancelled reservation?
                    This action cannot be undone.
                </div>
                <div style="display:flex; gap:12px; justify-content:center;">
                    <button id="modal-cancel"
                            style="padding:11px 28px; border-radius:10px;
                                   border:1.5px solid #e8f0e8; background:white;
                                   color:#8aaa92; font-size:12px; font-weight:800;
                                   letter-spacing:1px; text-transform:uppercase; cursor:pointer;">
                        Cancel
                    </button>
                    <button id="modal-confirm"
                            style="padding:11px 28px; border-radius:10px;
                                   border:none; background:#c0392b;
                                   color:white; font-size:12px; font-weight:800;
                                   letter-spacing:1px; text-transform:uppercase; cursor:pointer;">
                        Delete
                    </button>
                </div>
            </div>`;

        document.body.appendChild(overlay);
        document.getElementById('modal-cancel').onclick = () => overlay.remove();
        overlay.onclick = (e) => { if (e.target === overlay) overlay.remove(); };

        document.getElementById('modal-confirm').onclick = () => {
            overlay.remove();
            fetch(`/tenant/reservation/${id}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.querySelectorAll('.booking-card').forEach(card => {
                        if (card.querySelector(`button[onclick="confirmDelete(${id})"]`)) {
                            card.style.transition = 'opacity 0.3s';
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 300);
                        }
                    });
                } else {
                    alert('Failed to delete. Please try again.');
                }
            })
            .catch(() => alert('Something went wrong.'));
        };
    }
</script>

@include('components.modals')
@include('components.notification-bell')
@include('components.feedback-modal')
</body>
</html>