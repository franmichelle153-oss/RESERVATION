@php use Illuminate\Support\Facades\Storage; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rentivator - Fleet</title>
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
        .topbar-brand-text .name { font-size: 15px; font-weight: 900; color: #1a3d24; letter-spacing: 2px; }
        .topbar-brand-text .sub  { font-size: 10px; color: #8aaa92; letter-spacing: 2px; text-transform: uppercase; }

        .topbar-right { display: flex; align-items: center; gap: 20px; flex-shrink: 0; }

        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 38px; height: 38px; background: #1e4d2b; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 14px; flex-shrink: 0;
        }
        .user-text .user-name { font-size: 13px; font-weight: 700; color: #1a3d24; }
        .user-text .user-role { font-size: 10px; color: #8aaa92; letter-spacing: 1px; text-transform: uppercase; }

        .content { padding: 0; flex: 1; min-width: 0; }

        /* Search bar */
        .search-bar-wrap { padding: 24px 32px 0; }
        .search-card {
            background: white; border-radius: 16px; padding: 16px 24px;
            box-shadow: 0 4px 20px rgba(30,77,43,0.08);
            display: flex; align-items: center; gap: 16px;
            border: 1px solid #e8f0e8;
        }
        .search-field {
            flex: 1; display: flex; align-items: center; gap: 12px;
            border: 1.5px solid #e8f0e8; border-radius: 10px;
            padding: 12px 18px; transition: border-color 0.2s;
        }
        .search-field:focus-within { border-color: #6DBE47; }
        .search-field i { color: #8aaa92; font-size: 15px; flex-shrink: 0; }
        .search-field input {
            border: none; outline: none; font-size: 14px;
            color: #1a3d24; background: transparent; width: 100%;
        }
        .search-field input::placeholder { color: #b8d4bc; }

        /* Fleet grid */
        .fleet-content { padding: 32px; padding-top: 28px; }
        .fleet-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
        }
        .fleet-title-block { display: flex; align-items: center; gap: 14px; }
        .fleet-accent { width: 5px; height: 32px; background: #6DBE47; border-radius: 4px; }
        .fleet-title { font-size: 22px; font-weight: 900; color: #1a3d24; }
        .fleet-count { font-size: 12px; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; }

        .vehicle-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }

        .vehicle-card {
            background: white; border-radius: 16px; overflow: hidden;
            border: 1px solid #e8f0e8; transition: all 0.25s cubic-bezier(.4,0,.2,1);
        }
        .vehicle-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(30,77,43,0.12); border-color: #c8e6c9; }

        .card-img-placeholder {
            width: 100%; height: 200px;
            display: flex; align-items: center; justify-content: center;
            font-size: 56px; color: #6DBE47;
        }
        .card-img-wrap { position: relative; }
        .card-status-bar {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 8px 14px;
            background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
            display: flex; align-items: center; gap: 6px;
        }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .status-dot.available   { background: #6DBE47; }
        .status-dot.onfield     { background: #f59e0b; }
        .status-dot.maintenance { background: #e74c3c; }
        .status-label { font-size: 10px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; color: white; }
        .card-body { padding: 20px; }
        .card-type { font-size: 10px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #8aaa92; margin-bottom: 6px; }
        .card-name { font-size: 18px; font-weight: 900; color: #1a3d24; margin-bottom: 16px; line-height: 1.2; }
        .card-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding-top: 16px; border-top: 1px solid #e8f0e8; gap: 12px;
        }
        .rate-block .rate-value { font-size: 20px; font-weight: 900; color: #1a3d24; }
        .rate-block .rate-value span { font-size: 12px; font-weight: 500; color: #8aaa92; }

        .btn-book {
            display: flex; align-items: center; gap: 8px;
            padding: 11px 20px; background: #1e4d2b; color: white;
            border: none; border-radius: 10px;
            font-size: 12px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s; white-space: nowrap;
        }
        .btn-book:hover { background: #2d6a3f; transform: scale(1.03); }
        .btn-book:disabled, .btn-book.disabled {
            background: #ccc; cursor: not-allowed; transform: none;
        }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.6); z-index: 300;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: white; border-radius: 20px;
            width: 100%; max-width: 540px;
            max-height: 92vh; overflow-y: auto;
            animation: modalIn 0.25s cubic-bezier(.4,0,.2,1);
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(30px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-header {
            background: #1e4d2b; padding: 28px 32px;
            display: flex; align-items: center; gap: 18px;
            border-radius: 20px 20px 0 0; position: relative;
        }
        .modal-header-icon {
            width: 56px; height: 56px; background: rgba(255,255,255,0.1);
            border-radius: 14px; display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #6DBE47; flex-shrink: 0;
        }
        .modal-header-text .modal-title { font-size: 20px; font-weight: 900; color: white; letter-spacing: 1px; }
        .modal-header-text .modal-sub   { font-size: 10px; color: rgba(255,255,255,0.55); letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
        .modal-body   { padding: 28px 32px; }
        .form-row     { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group   { display: flex; flex-direction: column; gap: 8px; }
        .form-group.full { grid-column: 1/-1; }
        .form-label   { font-size: 10px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #8aaa92; }
        .form-input   {
            display: flex; align-items: center; gap: 12px;
            border: 1.5px solid #e8f0e8; border-radius: 10px;
            padding: 14px 16px; background: #f4f7f4; transition: all 0.2s;
        }
        .form-input:focus-within { border-color: #6DBE47; background: white; }
        .form-input i { color: #8aaa92; font-size: 15px; flex-shrink: 0; }
        .form-input input {
    border: none; outline: none; font-size: 14px; color: #1a3d24;
    background: transparent; width: 100%; font-family: inherit;
    /* iOS auto-zoom fix */
    -webkit-text-size-adjust: 100%;
}

        .btn-confirm {
            width: 100%; padding: 16px; background: #1e4d2b; color: white;
            border: none; border-radius: 12px;
            font-size: 13px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            gap: 10px; margin-top: 8px; transition: all 0.2s;
        }
        .btn-confirm:hover { background: #2d6a3f; }

        .btn-close-modal {
            position: absolute; top: 20px; right: 20px;
            width: 36px; height: 36px; background: rgba(255,255,255,0.15);
            border: none; border-radius: 8px;
            color: white; font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .btn-close-modal:hover { background: rgba(255,255,255,0.25); }

        /* Inline calendar */
        .cal-wrap {
            margin-top: 0; background: #f4f7f4;
            border: 1.5px solid #e8f0e8; border-radius: 14px;
            padding: 20px; display: none;
        }
        .cal-header {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 14px;
        }
        .cal-nav-btn {
            background: #e8f5e9; border: none; border-radius: 8px;
            width: 34px; height: 34px; cursor: pointer;
            font-size: 16px; color: #1e4d2b;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.2s;
        }
        .cal-nav-btn:hover { background: #c8e6c9; }
        .cal-month-label { font-weight: 900; font-size: 15px; color: #1a3d24; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; }
        .cal-weekday {
            text-align: center; font-size: 10px; font-weight: 800;
            color: #8aaa92; padding: 6px 0; letter-spacing: 0.5px;
        }
        .cal-day {
            text-align: center; padding: 9px 4px; border-radius: 9px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            background: white; color: #1a3d24;
            border: 1.5px solid transparent;
            transition: all 0.15s; user-select: none;
        }
        .cal-day:hover:not(.cal-day--disabled):not(.cal-day--blocked):not(.cal-day--pending) {
            border-color: #6DBE47; color: #1e4d2b; background: #f0faf0;
        }
        .cal-day--empty    { background: transparent; cursor: default; border-color: transparent; }
        .cal-day--disabled { background: #f0f0f0; color: #ccc; cursor: not-allowed; }
        .cal-day--blocked  { background: #e74c3c; color: white; cursor: not-allowed; }
        .cal-day--pending  { background: #fff3cd; color: #856404; cursor: not-allowed; }
        .cal-day--selected { background: #1e4d2b !important; color: white !important; border-color: #1e4d2b !important; }
        .cal-legend { display: flex; gap: 14px; margin-top: 14px; flex-wrap: wrap; }
        .cal-legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #8aaa92; font-weight: 600; }
        .cal-legend-dot  { width: 12px; height: 12px; border-radius: 4px; flex-shrink: 0; }

        /* Confirm dialog modal */
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
            animation: modalIn 0.22s cubic-bezier(.4,0,.2,1);
        }
        .confirm-icon { font-size: 52px; margin-bottom: 16px; display: block; }
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
            flex: 1; padding: 13px; border: none;
            border-radius: 10px; background: #1e4d2b; color: white;
            font-size: 13px; font-weight: 800; cursor: pointer; transition: all 0.2s;
        }
        .confirm-btn-ok:hover { background: #2d6a3f; }

        /* Toast */
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

        /* Hectare hint */
        .hectare-hint {
            display: none;
            align-items: center; gap: 10px;
            background: #fff8e1; border: 1.5px solid #fcd34d;
            border-radius: 10px; padding: 10px 14px;
            margin-top: 8px;
            font-size: 12px; font-weight: 700; color: #92400e;
            line-height: 1.4;
        }
        .hectare-hint i { color: #f59e0b; font-size: 15px; flex-shrink: 0; }
        .hectare-hint.error {
            background: #fef2f2; border-color: #fca5a5; color: #991b1b;
        }

        /* ── PRICE BREAKDOWN POPUP ── */
        .price-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55); z-index: 600;
            align-items: center; justify-content: center; padding: 20px;
        }
        .price-overlay.open { display: flex; }
        .price-box {
            background: white; border-radius: 20px;
            width: 100%; max-width: 420px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.3);
            animation: modalIn 0.22s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }
        .price-box-header {
            background: linear-gradient(135deg, #1e4d2b, #2d6a3f);
            padding: 22px 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .price-box-header-left .price-box-date {
            font-size: 11px; font-weight: 800; color: rgba(255,255,255,0.6);
            letter-spacing: 2px; text-transform: uppercase; margin-bottom: 4px;
        }
        .price-box-header-left .price-box-vehicle {
            font-size: 18px; font-weight: 900; color: white;
        }
        .price-box-close {
            width: 34px; height: 34px; background: rgba(255,255,255,0.15);
            border: none; border-radius: 8px; color: white; font-size: 16px;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .price-box-close:hover { background: rgba(255,255,255,0.25); }
        .price-box-body { padding: 24px; }

        /* Breakdown table */
        .breakdown-title {
            font-size: 10px; font-weight: 800; color: #8aaa92;
            letter-spacing: 2px; text-transform: uppercase; margin-bottom: 12px;
        }
        .breakdown-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 0; border-bottom: 1px solid #f0f5f0;
        }
        .breakdown-row:last-child { border-bottom: none; }
        .breakdown-row-left { display: flex; align-items: center; gap: 10px; }
        .breakdown-row-icon {
            width: 30px; height: 30px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }
        .breakdown-row-icon.green  { background: #e8f5e9; color: #22c55e; }
        .breakdown-row-icon.blue   { background: #e8f0ff; color: #6366f1; }
        .breakdown-row-icon.orange { background: #fff3e0; color: #f59e0b; }
        .breakdown-row-icon.red    { background: #fde8e8; color: #e74c3c; }
        .breakdown-row-label { font-size: 13px; font-weight: 700; color: #1a3d24; }
        .breakdown-row-sub   { font-size: 10px; color: #8aaa92; margin-top: 1px; }
        .breakdown-row-amount { font-size: 14px; font-weight: 800; color: #1a3d24; }
        .breakdown-row-amount.deduct { color: #e74c3c; }

        .breakdown-divider {
            border: none; border-top: 2px dashed #e8f0e8; margin: 12px 0;
        }
        .breakdown-net-row {
            display: flex; align-items: center; justify-content: space-between;
            background: #1e4d2b; border-radius: 12px; padding: 16px 18px; margin-top: 8px;
        }
        .breakdown-net-label {
            font-size: 11px; font-weight: 800; color: rgba(255,255,255,0.6);
            letter-spacing: 1.5px; text-transform: uppercase;
        }
        .breakdown-net-sub { font-size: 11px; color: rgba(255,255,255,0.4); margin-top: 2px; }
        .breakdown-net-value { font-size: 24px; font-weight: 900; color: white; }

        /* Slot progress in price popup */
        .slot-bar-wrap { margin-bottom: 20px; }
        .slot-bar-label {
            display: flex; justify-content: space-between;
            font-size: 11px; font-weight: 700; color: #8aaa92; margin-bottom: 6px;
        }
        .slot-bar-track {
            background: #e8f0e8; border-radius: 8px; height: 8px; overflow: hidden;
        }
        .slot-bar-fill { height: 100%; border-radius: 8px; transition: width 0.3s; }

        .price-box-footer { padding: 0 24px 24px; display: flex; gap: 12px; }
        .price-btn-cancel {
            flex: 1; padding: 13px; border: 1.5px solid #e8f0e8;
            border-radius: 10px; background: white; color: #5a7a5e;
            font-size: 13px; font-weight: 800; cursor: pointer;
        }
        .price-btn-confirm {
            flex: 2; padding: 13px; border: none;
            border-radius: 10px; background: #1e4d2b; color: white;
            font-size: 13px; font-weight: 800; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .price-btn-confirm:hover { background: #2d6a3f; }
        .price-btn-confirm:disabled { background: #aaa; cursor: not-allowed; }

        /* Org-only banner (for non-staff items) */
        .org-note {
            display: flex; align-items: flex-start; gap: 8px;
            background: #f0f9ff; border: 1.5px solid #bae6fd;
            border-radius: 10px; padding: 10px 14px; margin-top: 14px;
            font-size: 12px; font-weight: 600; color: #0369a1; line-height: 1.5;
        }
        .org-note i { color: #0ea5e9; font-size: 14px; flex-shrink: 0; margin-top: 1px; }

        @media (max-width: 1200px) { .vehicle-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-close { display: flex; }
            .main { margin-left: 0; }
            .topbar { padding: 0 16px; }
            .hamburger { display: block; }
            .user-text { display: none; }
            .content { padding: 0; }
            .fleet-content { padding: 24px 16px; }
            .search-bar-wrap { padding: 16px 16px 0; }
        }
        @media (max-width: 640px) {
            .vehicle-grid { grid-template-columns: 1fr; }
            .form-row     { grid-template-columns: 1fr; }
            .search-card  { padding: 12px 16px; }
            .modal-body   { padding: 20px; }
        }

        /* Prevent iOS auto-zoom on inputs */
@media (max-width: 768px) {
    .modal .form-input input,
    .modal input[type="text"],
    .modal input[type="number"],
    .modal input[type="tel"],
    .search-field input {
        font-size: 16px !important;
        transform: scale(1);
    }
}

input, select, textarea {
    font-size: 16px !important;
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
        <a class="nav-item active" href="{{ route('tenant.vehicle') }}">
            <i class="fa-solid fa-tractor"></i> My Vehicle
        </a>
        <a class="nav-item" href="{{ route('tenant.reservation') }}">
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

<!-- MAIN -->
<main class="main">
    <div class="topbar">
        <div style="display:flex; align-items:center; gap:12px;">
            <button class="hamburger" onclick="openSidebar()"><i class="fa-solid fa-bars"></i></button>
            <div class="topbar-brand">
                <div class="topbar-brand-text">
                    <div class="name">MY VEHICLE</div>
                </div>
            </div>
        </div>
        @include('components.topbar')
    </div>

    <div class="content">
        <div class="search-bar-wrap">
            <div class="search-card">
                <div class="search-field">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" placeholder="Search equipment by model or type..." oninput="filterVehicles()">
                </div>
            </div>
        </div>

        <div class="fleet-content">
            <div class="fleet-header">
                <div class="fleet-title-block">
                    <div class="fleet-accent"></div>
                    <div>
                        <div class="fleet-title">Available Units</div>
                        <div class="fleet-count" id="unitCount">Loading...</div>
                    </div>
                </div>
            </div>

            <div class="vehicle-grid" id="vehicleGrid">
                @forelse($vehicles as $v)
                    @php
                        $iconMap = [
                            'Harvester' => 'fa-wheat-awn',
                            'Tractor'   => 'fa-tractor',
                            'Sprayer'   => 'fa-spray-can',
                            'Planter'   => 'fa-seedling',
                        ];
                        $bgMap = [
                            'Tractor'   => 'linear-gradient(135deg,#e8f5e9,#f1f8e9)',
                            'Harvester' => 'linear-gradient(135deg,#fff8e1,#fef9c3)',
                            'Sprayer'   => 'linear-gradient(135deg,#e8f0ff,#ede9fe)',
                            'Planter'   => 'linear-gradient(135deg,#fff3e0,#fde68a)',
                        ];
                        $icon        = $iconMap[$v->type] ?? 'fa-tractor';
                        $bg          = $bgMap[$v->type]   ?? 'linear-gradient(135deg,#e8f5e9,#f1f8e9)';
                        $isMaintenance = $v->status === 'maintenance';
                    @endphp

                    <div class="vehicle-card"
                         data-type="{{ strtolower($v->type) }}"
                         data-name="{{ strtolower($v->name) }}">

                        <div class="card-img-wrap">
                            @if($v->image_data)
    <img src="{{ $v->image_data }}"
         style="width:100%;height:200px;object-fit:cover;" alt="{{ $v->name }}">
@else
                                <div class="card-img-placeholder" style="background: {{ $bg }};">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>
                            @endif
                            <div class="card-status-bar">
                                <div class="status-dot {{ $v->status }}"></div>
                                <span class="status-label">
                                    {{ $v->status === 'available' ? 'Available' : ($v->status === 'onfield' ? 'On Field' : 'Maintenance') }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="card-type">{{ $v->type }}</div>
                            <div class="card-name">{{ $v->name }}</div>
                            <div class="card-footer">
                                <div class="rate-block">
                                    <div class="rate-value">₱{{ number_format($v->rate) }} <span>/ hectare</span></div>
                                </div>
                                @if($isMaintenance)
                                    <button class="btn-book disabled" disabled title="Under Maintenance">
                                        <i class="fa-solid fa-wrench"></i> Unavailable
                                    </button>
                                @else
                                    <button class="btn-book"
                                        onclick="openModal(
                                            '{{ addslashes($v->name) }}',
                                            '{{ $v->type }}',
                                            {{ $v->rate }},
                                            {{ $v->id }},
                                            '{{ $v->status }}',
                                            {{ $v->max_hectares }}
                                        )">
                                        <i class="fa-solid fa-calendar-plus"></i> Book Unit
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:#8aaa92;">
                        <i class="fa-solid fa-tractor" style="font-size:48px; color:#c8e6c9; display:block; margin-bottom:16px;"></i>
                        <h3 style="font-weight:800;">No vehicles available</h3>
                        <p style="font-size:13px; margin-top:8px;">Check back later.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</main>

<!-- BOOKING MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="handleOverlayClick(event)">
    <div class="modal" id="reservationModal">
        <div class="modal-header">
            <div class="modal-header-icon"><i class="fa-solid fa-tractor"></i></div>
            <div class="modal-header-text">
                <div class="modal-title">UNIT RESERVATION</div>
                <div class="modal-sub" id="modalSubtitle">Deployment Configuration</div>
            </div>
            <button class="btn-close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name / Operator</label>
                    <div class="form-input">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" id="modalName" value="{{ auth()->user()->name ?? '' }}" placeholder="Your name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <div class="form-input">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" id="modalPhone" value="{{ auth()->user()->phone_number ?? '' }}" placeholder="+63 9XX XXX XXXX">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Deployment Location</label>
                    <div class="form-input">
                        <i class="fa-solid fa-location-dot"></i>
                        <input type="text" id="modalLocation" value="{{ auth()->user()->address ?? '' }}" placeholder="Farm Address">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Hectares</label>
                    <div class="form-input">
                        <i class="fa-solid fa-maximize"></i>
                        <input type="number" id="modalHectares" placeholder="e.g. 5" min="1"
                               oninput="validateHectares()">
                    </div>
                    <div class="hectare-hint" id="hectareHint">
                        <i class="fa-solid fa-circle-info"></i>
                        <span id="hectareHintMsg"></span>
                    </div>
                </div>
            </div>

            <!-- SINGLE DATE PICKER -->
            <div style="margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">Reservation Date</label>
                    <div class="form-input" onclick="toggleCalendar()" style="cursor:pointer;">
                        <i class="fa-regular fa-calendar"></i>
                        <input type="text" id="dateDisplay" placeholder="Click to select a date" readonly 
       style="cursor:pointer; font-size: 16px;">
                    </div>
                    <input type="hidden" id="modalReservationDate">
                </div>

                <div class="cal-wrap" id="calWrap" style="margin-top:12px;">
                    <div class="cal-header">
                        <button type="button" class="cal-nav-btn" onclick="prevMonth()">‹</button>
                        <span class="cal-month-label" id="calMonthLabel"></span>
                        <button type="button" class="cal-nav-btn" onclick="nextMonth()">›</button>
                    </div>
                    <div class="cal-grid" id="calWeekdays">
                        <div class="cal-weekday">Su</div>
                        <div class="cal-weekday">Mo</div>
                        <div class="cal-weekday">Tu</div>
                        <div class="cal-weekday">We</div>
                        <div class="cal-weekday">Th</div>
                        <div class="cal-weekday">Fr</div>
                        <div class="cal-weekday">Sa</div>
                    </div>
                    <div class="cal-grid" id="calDays"></div>
                    <div class="cal-legend">
                        <div class="cal-legend-item">
                            <div class="cal-legend-dot" style="background:#e74c3c;"></div> Reserved
                        </div>
                        <div class="cal-legend-item">
                            <div class="cal-legend-dot" style="background:#fff3cd; border:1.5px solid #fcd34d;"></div> Pending
                        </div>
                        <div class="cal-legend-item">
                            <div class="cal-legend-dot" style="background:#1e4d2b;"></div> Selected
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="modalVehicleId">

            <!-- Confirm reservation — triggers price breakdown popup -->
            <button class="btn-confirm" onclick="askConfirmReservation()">
                <i class="fa-regular fa-circle-check"></i> Confirm Reservation
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     PRICE BREAKDOWN POPUP
══════════════════════════════════════════ -->
<div class="price-overlay" id="priceOverlay" onclick="if(event.target===this)closePricePopup()">
    <div class="price-box">
        <div class="price-box-header">
            <div class="price-box-header-left">
                <div class="price-box-date" id="priceBoxDate">Date</div>
                <div class="price-box-vehicle" id="priceBoxVehicle">Vehicle Name</div>
            </div>
            <button class="price-box-close" onclick="closePricePopup()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="price-box-body">

            <!-- Slot availability bar -->
            <div class="slot-bar-wrap" id="priceSlotBar">
                <div class="slot-bar-label">
                    <span id="priceSlotUsed"></span>
                    <span id="priceSlotRemaining" style="font-weight:800;"></span>
                </div>
                <div class="slot-bar-track">
                    <div class="slot-bar-fill" id="priceSlotFill"></div>
                </div>
            </div>

            <!-- Breakdown -->
            <div class="breakdown-title">Price Breakdown</div>
            <div id="breakdownRows"></div>

            <hr class="breakdown-divider">

            <!-- Net total -->
            <div class="breakdown-net-row">
                <div>
                    <div class="breakdown-net-label">Your Total</div>
                    <div class="breakdown-net-sub" id="priceNetSub"></div>
                </div>
                <div class="breakdown-net-value" id="priceNetValue">₱0</div>
            </div>

            <!-- Info note -->
            <div class="org-note" id="orgNote" style="display:none;">
                <i class="fa-solid fa-circle-info"></i>
                <span id="orgNoteText"></span>
            </div>
        </div>
        <div class="price-box-footer">
            <button class="price-btn-cancel" onclick="closePricePopup()">Go Back</button>
            <button class="price-btn-confirm" id="priceBtnConfirm" onclick="submitReservation()">
                <i class="fa-regular fa-circle-check"></i> Submit Reservation
            </button>
        </div>
    </div>
</div>

<!-- SLOT INFO POPUP -->
<div class="confirm-overlay" id="slotPopupOverlay" onclick="if(event.target===this)closeSlotPopup()">
    <div class="confirm-box" style="max-width:400px;text-align:left;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <div>
                <div style="font-size:11px;font-weight:800;color:#8aaa92;letter-spacing:2px;text-transform:uppercase;">Slot Info</div>
                <div style="font-size:18px;font-weight:900;color:#1a3d24;" id="slotPopupDate"></div>
            </div>
            <button onclick="closeSlotPopup()"
                style="background:#f4f7f4;border:none;border-radius:8px;width:34px;height:34px;
                       cursor:pointer;font-size:16px;color:#8aaa92;">✕</button>
        </div>

        <!-- Progress bar -->
        <div style="margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:12px;font-weight:700;color:#5a7a5e;" id="slotPopupUsed"></span>
                <span style="font-size:12px;font-weight:800;" id="slotPopupRemaining"></span>
            </div>
            <div style="background:#e8f0e8;border-radius:8px;height:10px;overflow:hidden;">
                <div id="slotPopupBar" style="height:100%;border-radius:8px;transition:width 0.3s;"></div>
            </div>
        </div>

        <!-- Tenant list -->
        <div style="margin-bottom:16px;">
            <div style="font-size:11px;font-weight:800;color:#8aaa92;letter-spacing:1.5px;
                        text-transform:uppercase;margin-bottom:8px;">Reservations</div>
            <div id="slotPopupTenants"></div>
        </div>

        <button id="slotPopupSelectBtn" class="btn-confirm"
            style="margin-top:0;padding:13px;font-size:13px;">
            <i class="fa-solid fa-calendar-check"></i> Select This Date
        </button>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
    <div class="toast-icon" id="toastIcon"></div>
    <div class="toast-msg"  id="toastMsg"></div>
</div>

<script>
    const allBlockedDates    = @json($blockedDates       ?? []);
    const allPendingDates    = @json($pendingDates        ?? []);
    const vehicleStatusMap   = @json($statusBlocked       ?? []);
    const slotData           = @json($slotData            ?? []);
    const vehicleStaffData   = @json($vehicleStaffData    ?? []);

    let currentVehicleId   = null;
    let currentVehicleName = '';
    let currentVehicleType = '';
    let currentRate        = 0;
    let currentMaxHectares = 10;
    let calYear, calMonth;
    let selectedDate       = null;
    let blockedForVehicle  = [];
    let pendingForVehicle  = [];

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

    // ── Search ──
    function filterVehicles() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.vehicle-card');
        let visible = 0;
        cards.forEach(card => {
            const match = card.dataset.name.includes(query) || card.dataset.type.includes(query);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('unitCount').textContent =
            visible + ' Unit' + (visible !== 1 ? 's' : '') + ' Found';
    }

    // ── Open booking modal ──
    function openModal(name, type, rate, vehicleId, status, maxHectares) {
        currentVehicleId   = vehicleId;
        currentVehicleName = name;
        currentVehicleType = type;
        currentRate        = rate;
        currentMaxHectares = maxHectares;
        selectedDate       = null;

        blockedForVehicle = allBlockedDates[vehicleId]
            ? Object.values(allBlockedDates[vehicleId]) : [];
        pendingForVehicle = allPendingDates[vehicleId]
            ? Object.values(allPendingDates[vehicleId]) : [];

        document.getElementById('modalVehicleId').value  = vehicleId;
        document.getElementById('dateDisplay').value     = '';
        const hectInput       = document.getElementById('modalHectares');
        hectInput.max         = currentMaxHectares;
        hectInput.placeholder = `e.g. ${currentMaxHectares}`;
        hectInput.value       = '';
        document.getElementById('modalReservationDate').value = '';
        document.getElementById('modalSubtitle').textContent  =
            name.toUpperCase() + ' · ₱' + Number(rate).toLocaleString() + '/hectare';

        const today = new Date();
        calYear  = today.getFullYear();
        calMonth = today.getMonth();
        document.getElementById('calWrap').style.display = 'none';
        renderCalendar();

        document.getElementById('modalOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    function handleOverlayClick(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    function toggleCalendar() {
        const cal = document.getElementById('calWrap');
        cal.style.display = cal.style.display === 'block' ? 'none' : 'block';
    }

    function prevMonth() {
        calMonth--;
        if (calMonth < 0) { calMonth = 11; calYear--; }
        renderCalendar();
    }
    function nextMonth() {
        calMonth++;
        if (calMonth > 11) { calMonth = 0; calYear++; }
        renderCalendar();
    }

    function renderCalendar() {
        const months = ['January','February','March','April','May','June',
                        'July','August','September','October','November','December'];
        document.getElementById('calMonthLabel').textContent = months[calMonth] + ' ' + calYear;

        const firstDay    = new Date(calYear, calMonth, 1).getDay();
        const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
        const today       = new Date(); today.setHours(0,0,0,0);

        const cells = [];

        for (let i = 0; i < firstDay; i++) {
            cells.push('<div class="cal-day cal-day--empty"></div>');
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const mm      = String(calMonth + 1).padStart(2, '0');
            const dd      = String(d).padStart(2, '0');
            const dateStr = `${calYear}-${mm}-${dd}`;
            const dateObj = new Date(calYear, calMonth, d);

            const isPast     = dateObj < today;
            const maxH       = currentMaxHectares;
            const usedH      = (slotData[currentVehicleId] && slotData[currentVehicleId][dateStr])
                ? slotData[currentVehicleId][dateStr].used : 0;
            const isBlocked  = usedH >= maxH;
            const isSelected = selectedDate === dateStr;

            const remaining = maxH - usedH;

            let cls = 'cal-day';
            if (isPast)        cls += ' cal-day--disabled';
            else if (isBlocked) cls += ' cal-day--blocked';
            if (isSelected)    cls += ' cal-day--selected';

            const clickable = !isPast && !isBlocked;
            const onclick   = clickable
                ? `onclick="handleDateClick('${dateStr}', ${maxH}, ${usedH})"`
                : ((!isPast && isBlocked) ? `onclick="showSlotPopup('${dateStr}', ${maxH}, ${usedH})"` : '');

            let dotHtml = '';
            if (!isPast && usedH > 0 && !isBlocked) {
                dotHtml = `<div style="width:6px;height:6px;background:#f59e0b;border-radius:50%;margin:2px auto 0;"></div>`;
            }

            cells.push(`<div class="${cls}" ${onclick} title="${remaining} ha remaining">${d}${dotHtml}</div>`);
        }

        document.getElementById('calDays').innerHTML = cells.join('');
    }

    function pickDate(dateStr) {
        selectedDate = dateStr;
        document.getElementById('dateDisplay').value          = formatDisplay(dateStr);
        document.getElementById('modalReservationDate').value = dateStr;
        document.getElementById('calWrap').style.display      = 'none';
        renderCalendar();
        validateHectares();

        const info      = slotData[currentVehicleId] && slotData[currentVehicleId][dateStr];
        const usedH     = info ? info.used : 0;
        const remaining = currentMaxHectares - usedH;
        const hectInput = document.getElementById('modalHectares');
        hectInput.max         = remaining;
        hectInput.placeholder = `Max ${remaining} ha available`;

        if (hectInput.value && Number(hectInput.value) > remaining) {
            hectInput.value = remaining;
        }
    }

    function formatDisplay(dateStr) {
        const [y, m, d] = dateStr.split('-');
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return `${months[parseInt(m) - 1]} ${parseInt(d)}, ${y}`;
    }

    function validateHectares() {
        const hint      = document.getElementById('hectareHint');
        const hintMsg   = document.getElementById('hectareHintMsg');
        const hintIcon  = hint.querySelector('i');
        const hectares  = Number(document.getElementById('modalHectares').value);
        const date      = document.getElementById('modalReservationDate').value;

        if (!date) {
            hint.className = 'hectare-hint';
            hintIcon.className = 'fa-solid fa-circle-info';
            hintMsg.textContent = 'Please select a reservation date first to see available slots.';
            hint.style.display = 'flex';
            return;
        }

        const info      = slotData[currentVehicleId] && slotData[currentVehicleId][date];
        const usedH     = info ? info.used : 0;
        const remaining = currentMaxHectares - usedH;

        if (!hectares || hectares < 1) {
            hint.style.display = 'none';
            return;
        }

        hint.style.display = 'none';
    }

    // ── ASK CONFIRM — shows PRICE BREAKDOWN popup ──
    function askConfirmReservation() {
        const name     = document.getElementById('modalName').value.trim();
        const phone    = document.getElementById('modalPhone').value.trim();
        const location = document.getElementById('modalLocation').value.trim();
        const hectares = Number(document.getElementById('modalHectares').value);
        const date     = document.getElementById('modalReservationDate').value;

        if (!name || !phone || !location || !date) {
            showToast('warning', 'Please fill in all fields and select a date.');
            return;
        }
        if (!hectares || hectares < 1) {
            showToast('warning', 'Please enter the total hectares needed.');
            validateHectares();
            return;
        }

        const info      = slotData[currentVehicleId] && slotData[currentVehicleId][date];
        const usedH     = info ? info.used : 0;
        const remaining = currentMaxHectares - usedH;
        if (hectares > remaining) {
            showToast('warning', `Only ${remaining} ha available on this date. Please adjust.`);
            validateHectares();
            return;
        }

        // All good — open price breakdown popup
        openPriceBreakdown(date, hectares);
    }

    // ══════════════════════════════════════════════════════
    //  PRICE BREAKDOWN POPUP — expenses × hectares
    // ══════════════════════════════════════════════════════
    function openPriceBreakdown(dateStr, hectares) {
        const staff   = vehicleStaffData[currentVehicleId] || {};
        const gross   = currentRate * hectares;

        // ── Multiply every staff cost by hectares ──────────
        const driverPayBase     = parseFloat(staff.driver_pay     || 0);
        const helperPayEachBase = parseFloat(staff.helper_pay_each || 0);
        const dieselCostBase    = parseFloat(staff.diesel_cost    || 0);

        const driverPay     = driverPayBase     * hectares;
        const helperPayEach = helperPayEachBase * hectares;   // per helper, already × ha
        const dieselCost    = dieselCostBase    * hectares;

        const isHarvester   = (staff.type || currentVehicleType) === 'Harvester';
        const helperCount = isHarvester 
    ? [staff.helper1_name, staff.helper2_name, staff.helper3_name]
        .filter(n => n && n.trim() !== '').length 
    : 0;
        const helperTotal   = helperCount * helperPayEach;
        const totalStaff    = driverPay + helperTotal + dieselCost;

        // Header
        document.getElementById('priceBoxDate').textContent    = formatDisplay(dateStr);
        document.getElementById('priceBoxVehicle').textContent = currentVehicleName;

        // Slot bar
        const info      = slotData[currentVehicleId] && slotData[currentVehicleId][dateStr];
        const usedH     = info ? info.used : 0;
        const remaining = currentMaxHectares - usedH;
        document.getElementById('priceSlotUsed').textContent      = `${usedH + hectares} / ${currentMaxHectares} ha after booking`;
        document.getElementById('priceSlotRemaining').textContent  = `${remaining - hectares} ha left`;
        document.getElementById('priceSlotRemaining').style.color  = (remaining - hectares) <= 0 ? '#e74c3c' : '#1e7e34';
        const fillPct = Math.min(100, ((usedH + hectares) / currentMaxHectares) * 100);
        document.getElementById('priceSlotFill').style.width      = fillPct + '%';
        document.getElementById('priceSlotFill').style.background = fillPct >= 100 ? '#e74c3c' : '#6DBE47';

        // ── Build breakdown rows ───────────────────────────
        const orgShare = Math.max(gross - totalStaff, 0);
        let rows = '';

        // Gross charge
        rows += `
            <div class="breakdown-row">
                <div class="breakdown-row-left">
                    <div class="breakdown-row-icon green"><i class="fa-solid fa-tractor"></i></div>
                    <div>
                        <div class="breakdown-row-label">Service Charge</div>
                        <div class="breakdown-row-sub">₱${fmtNum(currentRate)} × ${hectares} ha</div>
                    </div>
                </div>
                <div class="breakdown-row-amount">₱${fmtNum(gross)}</div>
            </div>`;

        // Driver (rate × ha)
        if (staff.driver_name && driverPayBase > 0) {
            rows += `
                <div class="breakdown-row">
                    <div class="breakdown-row-left">
                        <div class="breakdown-row-icon blue"><i class="fa-solid fa-id-badge"></i></div>
                        <div>
                            <div class="breakdown-row-label">Driver: ${escHtml(staff.driver_name)}</div>
                            <div class="breakdown-row-sub">₱${fmtNum(driverPayBase)} × ${hectares} ha</div>
                        </div>
                    </div>
                    <div class="breakdown-row-amount deduct">− ₱${fmtNum(driverPay)}</div>
                </div>`;
        }

        // Helpers (rate × ha each)
        if (isHarvester) {
            const helpers = [
                { name: staff.helper1_name },
                { name: staff.helper2_name },
                { name: staff.helper3_name },
            ].filter(h => h.name);
            helpers.forEach((h, idx) => {
                rows += `
                    <div class="breakdown-row">
                        <div class="breakdown-row-left">
                            <div class="breakdown-row-icon orange"><i class="fa-solid fa-person-digging"></i></div>
                            <div>
                                <div class="breakdown-row-label">Helper ${idx+1}: ${escHtml(h.name)}</div>
                                <div class="breakdown-row-sub">₱${fmtNum(helperPayEachBase)} × ${hectares} ha</div>
                            </div>
                        </div>
                        <div class="breakdown-row-amount deduct">− ₱${fmtNum(helperPayEach)}</div>
                    </div>`;
            });
        }

        // Diesel (rate × ha)
        if (dieselCostBase > 0) {
            rows += `
                <div class="breakdown-row">
                    <div class="breakdown-row-left">
                        <div class="breakdown-row-icon red"><i class="fa-solid fa-gas-pump"></i></div>
                        <div>
                            <div class="breakdown-row-label">Diesel</div>
                            <div class="breakdown-row-sub">₱${fmtNum(dieselCostBase)} × ${hectares} ha</div>
                        </div>
                    </div>
                    <div class="breakdown-row-amount deduct">− ₱${fmtNum(dieselCost)}</div>
                </div>`;
        }

        // Organization share row (only if there are staff costs)
        if (totalStaff > 0) {
            rows += `
                <div class="breakdown-row" style="background:#f4fdf4;border-radius:10px;padding:10px 12px;margin-top:4px;">
                    <div class="breakdown-row-left">
                        <div class="breakdown-row-icon green"><i class="fa-solid fa-building"></i></div>
                        <div>
                            <div class="breakdown-row-label" style="color:#1e7e34;">Goes to Organization</div>
                            <div class="breakdown-row-sub">After staff & fuel deductions</div>
                        </div>
                    </div>
                    <div class="breakdown-row-amount" style="color:#1e7e34;">₱${fmtNum(orgShare)}</div>
                </div>`;
        }

        document.getElementById('breakdownRows').innerHTML = rows;

        // Net shown to tenant = gross (they pay the service charge)
        document.getElementById('priceNetValue').textContent = '₱' + fmtNum(gross);
        document.getElementById('priceNetSub').textContent   = `${hectares} hectare${hectares > 1 ? 's' : ''} × ₱${fmtNum(currentRate)}`;

        // Hide org note
        document.getElementById('orgNote').style.display = 'none';

        document.getElementById('priceOverlay').classList.add('open');
    }

    function closePricePopup() {
        document.getElementById('priceOverlay').classList.remove('open');
    }

    // ── SUBMIT ──
    function submitReservation() {
        closePricePopup();

        const vehicleId = document.getElementById('modalVehicleId').value;
        const name      = document.getElementById('modalName').value.trim();
        const phone     = document.getElementById('modalPhone').value.trim();
        const location  = document.getElementById('modalLocation').value.trim();
        const hectares  = document.getElementById('modalHectares').value.trim();
        const date      = document.getElementById('modalReservationDate').value;

        const btn       = document.querySelector('.btn-confirm');
        btn.disabled    = true;
        btn.innerHTML   = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

        fetch('{{ route("tenant.reservation.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                vehicle_id:       vehicleId,
                operator_name:    name,
                contact_number:   phone,
                location:         location,
                hectares:         hectares,
                reservation_date: date,
            })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa-regular fa-circle-check"></i> Confirm Reservation';
            if (data.success) {
                showToast('success', 'Reservation submitted! Waiting for admin confirmation.', () => {
                    closeModal();
                    window.location.reload();
                });
            } else {
                showToast('warning', data.message || 'Something went wrong. Please try again.');
            }
        })
        .catch(() => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa-regular fa-circle-check"></i> Confirm Reservation';
            showToast('network', 'Network error. Please try again.');
        });
    }

    function handleDateClick(dateStr, maxH, usedH) {
        if (usedH > 0) {
            showSlotPopup(dateStr, maxH, usedH);
        } else {
            pickDate(dateStr);
        }
    }

    function showSlotPopup(dateStr, maxH, usedH) {
        const remaining = maxH - usedH;
        const info = (slotData[currentVehicleId] && slotData[currentVehicleId][dateStr])
            ? slotData[currentVehicleId][dateStr] : null;

        const tenants = info ? info.tenants : [];
        const isFull  = remaining <= 0;

        let tenantRows = tenants.map(t => `
            <div style="display:flex;justify-content:space-between;align-items:center;
                        padding:8px 12px;background:#f4f7f4;border-radius:8px;margin-bottom:6px;">
                <div>
                    <div style="font-size:13px;font-weight:700;color:#1a3d24;">${escHtml(t.name)}</div>
                    <div style="font-size:11px;color:#8aaa92;text-transform:uppercase;letter-spacing:1px;">${t.status}</div>
                </div>
                <div style="font-size:14px;font-weight:900;color:#1e4d2b;">${t.hectares} ha</div>
            </div>
        `).join('');

        document.getElementById('slotPopupDate').textContent  = formatDisplay(dateStr);
        document.getElementById('slotPopupUsed').textContent  = usedH + ' / ' + maxH + ' ha used';
        document.getElementById('slotPopupBar').style.width   = Math.min(100, (usedH / maxH) * 100) + '%';
        document.getElementById('slotPopupBar').style.background = isFull ? '#e74c3c' : '#6DBE47';
        document.getElementById('slotPopupTenants').innerHTML = tenantRows || '<p style="color:#8aaa92;font-size:13px;">No bookings yet.</p>';
        document.getElementById('slotPopupRemaining').textContent = isFull ? 'FULLY BOOKED' : remaining + ' ha remaining';
        document.getElementById('slotPopupRemaining').style.color = isFull ? '#e74c3c' : '#1e7e34';

        const selectBtn = document.getElementById('slotPopupSelectBtn');
        if (isFull) {
            selectBtn.style.display = 'none';
        } else {
            selectBtn.style.display = 'block';
            selectBtn.onclick = () => { pickDate(dateStr); closeSlotPopup(); };
        }

        document.getElementById('slotPopupOverlay').classList.add('open');
    }

    function closeSlotPopup() {
        document.getElementById('slotPopupOverlay').classList.remove('open');
    }

    function showToast(type, msg, callback) {
        const icons = {
            success: { icon: 'fa-circle-check',        color: '#1e4d2b' },
            warning: { icon: 'fa-triangle-exclamation', color: '#b45309' },
            error:   { icon: 'fa-circle-xmark',        color: '#b91c1c' },
            network: { icon: 'fa-wifi',                color: '#6b7280' },
        };
        const t = icons[type] || icons.warning;
        document.getElementById('toastIcon').innerHTML =
            `<i class="fa-solid ${t.icon}" style="font-size:40px; color:${t.color};"></i>`;
        document.getElementById('toastMsg').textContent = msg;
        document.getElementById('toast').classList.add('show');
        setTimeout(() => {
            document.getElementById('toast').classList.remove('show');
            if (callback) callback();
        }, 2200);
    }

    function escHtml(str) {
        return String(str || '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function fmtNum(n) {
        return parseFloat(n).toLocaleString('en-PH', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const total = document.querySelectorAll('.vehicle-card').length;
        document.getElementById('unitCount').textContent =
            total + ' Unit' + (total !== 1 ? 's' : '') + ' Found';
    });
</script>

@include('components.modals')
@include('components.notification-bell')
@include('components.feedback-modal')
</body>
</html>