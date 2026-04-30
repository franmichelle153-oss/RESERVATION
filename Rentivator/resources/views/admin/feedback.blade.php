<?php
// feedback.blade.php - Rentivator Feedback Management Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentivator - Feedback</title>
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
            text-decoration: none;
            font-size: 13px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            transition: all 0.2s; border-left: 3px solid transparent;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.08); color: white;
            border-left: 3px solid #6DBE47; padding-left: 21px;
        }

        .nav-item i { width: 18px; text-align: center; font-size: 15px; }

        .sidebar-bottom {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .farmer-portal-btn {
            width: 100%; padding: 12px;
            background: white; color: #1e4d2b;
            border: none; border-radius: 8px;
            font-size: 11px; font-weight: 800;
            letter-spacing: 1.5px; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s;
        }

        .farmer-portal-btn:hover { background: #e8f5e9; }

        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 16px 0;
        }

        .sidebar-user .avatar {
            width: 32px; height: 32px; background: #2d6a3f;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 13px; font-weight: 700;
        }

        .sidebar-user span {
            font-size: 11px; font-weight: 700;
            color: rgba(255,255,255,0.4); letter-spacing: 1px; text-transform: uppercase;
        }

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
        .topbar-brand-text .sub { font-size: 10px; color: #8aaa92; letter-spacing: 2px; text-transform: uppercase; }

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
            border-radius: 16px; padding: 28px 32px;
            margin-bottom: 28px; color: white;
            display: flex; align-items: center;
            justify-content: space-between; flex-wrap: wrap; gap: 16px;
        }

        .page-banner h1 { font-size: 28px; font-weight: 900; }
        .page-banner p { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 4px; }

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
            border: none; outline: none;
            font-size: 13px; color: #1a3d24;
            background: transparent; width: 100%; min-width: 0;
        }
        .search-box input::placeholder { color: #b8d4bc; }

        .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 0; }

        .filter-tab {
            padding: 7px 16px; border: none; background: transparent;
            font-size: 11px; font-weight: 800; color: #8aaa92;
            cursor: pointer; border-radius: 8px; transition: all 0.2s;
            letter-spacing: 1px; text-transform: uppercase;
        }

        .filter-tab:hover { background: #f0f5f0; color: #1a3d24; }
        .filter-tab.active { background: #e8f5e9; color: #1e4d2b; }
        .filter-tab.positive.active { background: #e8f5e9; color: #1e7e34; }
        .filter-tab.negative.active { background: #fde8e8; color: #c0392b; }
        .filter-tab.neutral.active { background: #fff8e1; color: #f59e0b; }

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

        .stat-mini-icon.green { background: #e8f5e9; color: #22c55e; }
        .stat-mini-icon.red { background: #fde8e8; color: #e74c3c; }
        .stat-mini-icon.yellow { background: #fff8e1; color: #f59e0b; }

        .stat-mini-label { font-size: 10px; font-weight: 700; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 2px; }
        .stat-mini-value { font-size: 22px; font-weight: 900; color: #1a3d24; }

        .feedback-card {
            background: white; border-radius: 14px;
            padding: 24px 28px; margin-bottom: 16px;
            border: 1px solid #e8f0e8;
            transition: all 0.2s;
        }

        .feedback-card:hover { box-shadow: 0 4px 16px rgba(30,77,43,0.08); border-color: #c8e6c9; }

        .feedback-header {
            display: flex; align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px; flex-wrap: wrap; gap: 8px;
        }

        .feedback-user { display: flex; align-items: center; gap: 12px; }

        .feedback-avatar {
            width: 40px; height: 40px; background: #e8f5e9;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #2d6a3f; font-size: 16px; flex-shrink: 0;
        }

        .feedback-name { font-size: 15px; font-weight: 800; color: #1a3d24; }

        .stars { margin-top: 3px; }
        .stars i { font-size: 13px; }
        .star-filled { color: #f59e0b; }
        .star-empty { color: #ddd; }

        .feedback-date { font-size: 12px; color: #8aaa92; white-space: nowrap; }

        .feedback-meta {
            font-size: 11px; color: #8aaa92;
            margin-bottom: 12px;
            display: flex; align-items: center; gap: 6px;
        }

        .feedback-text {
            font-size: 14px; color: #5a7a5e;
            font-style: italic; line-height: 1.65;
            margin-bottom: 16px;
        }

        .feedback-footer { display: flex; gap: 20px; }

        .feedback-action {
            font-size: 12px; font-weight: 800;
            color: #8aaa92; text-transform: uppercase;
            letter-spacing: 1.5px; cursor: pointer;
            border: none; background: transparent;
            transition: color 0.2s;
        }

        .feedback-action:hover { color: #1e4d2b; }
        .feedback-action.danger:hover { color: #e74c3c; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 10px; font-weight: 800;
            letter-spacing: 1px; text-transform: uppercase; white-space: nowrap;
        }

        .status-positive { background: #e8f5e9; color: #1e7e34; }
        .status-negative { background: #fde8e8; color: #c0392b; }
        .status-neutral { background: #fff8e1; color: #f59e0b; }

        .rating-overview {
            background: linear-gradient(135deg, #1e4d2b, #2d6a3f);
            border-radius: 16px; padding: 24px 28px;
            margin-bottom: 24px; color: white;
            display: flex; align-items: center;
            gap: 24px; flex-wrap: wrap;
        }

        .rating-big { font-size: 52px; font-weight: 900; line-height: 1; color: white; }
        .rating-stars i { font-size: 20px; color: #f59e0b; }
        .rating-label { font-size: 12px; color: rgba(255,255,255,0.6); margin-top: 4px; letter-spacing: 1px; }
        .rating-breakdown { flex: 1; min-width: 180px; }

        .rating-bar-row {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 8px;
        }

        .rating-bar-label { font-size: 11px; color: rgba(255,255,255,0.7); width: 16px; text-align: right; flex-shrink: 0; }

        .rating-bar-track {
            flex: 1; height: 6px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px; overflow: hidden;
        }

        .rating-bar-fill {
            height: 100%; background: #6DBE47; border-radius: 10px;
            transition: width 0.4s ease;
        }

        .rating-bar-count { font-size: 11px; color: rgba(255,255,255,0.5); width: 14px; text-align: left; flex-shrink: 0; }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 48px; color: #c8e6c9; margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 18px; font-weight: 800; color: #8aaa92; margin-bottom: 8px; }
        .empty-state p { font-size: 13px; color: #b8d4bc; }

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
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.error { background: #c0392b; }

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
            .stats-mini { grid-template-columns: repeat(3, 1fr); gap: 12px; }
            .rating-overview { padding: 20px; }
        }
        @media (max-width: 600px) {
            .stats-mini { grid-template-columns: 1fr 1fr; }
            .stats-mini > div:last-child { grid-column: span 2; }
            .feedback-card { padding: 16px; }
            .feedback-name { font-size: 13px; }
            .section-card { padding: 14px; }
            .topbar { padding: 0 12px; height: 58px; }
            .topbar-brand-text .sub { display: none; }
            .rating-big { font-size: 40px; }
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
                    <div class="name">FEEDBACK</div>
                </div>
            </div>
        </div>
       @include('components.topbar')
    </div>

    <div class="content">

        @php
            $positive  = $feedbacks->where('sentiment','positive')->count();
            $negative  = $feedbacks->where('sentiment','negative')->count();
            $neutral   = $feedbacks->where('sentiment','neutral')->count();
            $total     = $feedbacks->count();
            $avgRating = $total > 0 ? round($feedbacks->avg('rating'), 1) : 0;

            $ratingCounts = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
            foreach ($feedbacks as $f) {
                if (isset($ratingCounts[$f->rating])) $ratingCounts[$f->rating]++;
            }
            $maxCount = max(array_values($ratingCounts)) ?: 1;
        @endphp

@php
    $total    = $feedbacks->count();
    $maxCount = count($ratingCounts) ? max(array_values($ratingCounts)) : 1;
@endphp

{{-- AVG RATING ONLY --}}
@if($total > 0)
<div class="rating-overview" style="margin-bottom:24px;">
    <div>
        <div class="rating-big">{{ $avgRating }}</div>
        <div class="rating-stars">
            @for($i = 1; $i <= 5; $i++)
                <i class="fa-solid fa-star"
                   style="color:{{ $i <= round($avgRating) ? '#f59e0b' : 'rgba(255,255,255,0.2)' }};"></i>
            @endfor
        </div>
        <div class="rating-label">{{ $total }} REVIEW{{ $total !== 1 ? 'S' : '' }}</div>
    </div>
    <div class="rating-breakdown">
        @for($star = 5; $star >= 1; $star--)
        <div class="rating-bar-row">
            <div class="rating-bar-label">{{ $star }}</div>
            <div class="rating-bar-track">
                <div class="rating-bar-fill"
                     style="width:{{ $maxCount > 0 ? round(($ratingCounts[$star] / $maxCount) * 100) : 0 }}%;"></div>
            </div>
            <div class="rating-bar-count">{{ $ratingCounts[$star] }}</div>
        </div>
        @endfor
    </div>
</div>
@endif



{{-- FEEDBACK LIST --}}
<div id="feedbackList">
@forelse($feedbacks as $f)
    <div class="feedback-card"
         data-search="{{ strtolower($f->user->name . ' ' . $f->comment) }}">
        <div class="feedback-header">
            <div class="feedback-user">
                <div class="feedback-avatar">
                    {{ strtoupper(substr($f->user->name, 0, 1)) }}
                </div>
                <div>
                    <span class="feedback-name">{{ $f->user->name }}</span>
                    <div class="stars" style="margin-top:4px;">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $f->rating ? 'star-filled' : 'star-empty' }}"></i>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="feedback-date">{{ $f->created_at->format('M d, Y') }}</div>
        </div>

        @if($f->comment)
        <div class="feedback-text">"{{ $f->comment }}"</div>
        @endif
    </div>
@empty
    <div class="empty-state">
        <i class="fa-regular fa-comment-dots"></i>
        <h3>No feedback yet</h3>
        <p>Feedback from tenants will appear here after completed bookings.</p>
    </div>
@endforelse
</div>

    </div>
</main>

<!-- TOAST -->
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

    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
        });
    });

   
    function applyFilters() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const activeTab = document.querySelector('.filter-tab.active');
        const filterSentiment = activeTab ? activeTab.dataset.filter : 'all';

        document.querySelectorAll('.feedback-card').forEach(card => {
            const search = card.dataset.search || '';
            const sentiment = card.dataset.sentiment || '';
            const matchSearch = !q || search.includes(q);
            const matchSentiment = filterSentiment === 'all' || sentiment === filterSentiment;
            card.style.display = (matchSearch && matchSentiment) ? '' : 'none';
        });
    }

    function showToast(msg, isError = false) {
        const toast = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        toast.className = 'toast' + (isError ? ' error' : '');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    @if(session('acknowledged')) showToast("{{ session('acknowledged') }}"); @endif
    @if(session('archived')) showToast("{{ session('archived') }}"); @endif
</script>
  @include('components.modals')
    @include('components.notification-bell')
    @include('components.feedback-modal')
</body>
</html>