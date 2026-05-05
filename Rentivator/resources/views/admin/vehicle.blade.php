@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rentivator - Vehicles</title>
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
        .page-banner { background: linear-gradient(135deg, #1e4d2b, #2d6a3f); border-radius: 16px; padding: 28px 32px; margin-bottom: 20px; color: white; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .page-banner h1 { font-size: 28px; font-weight: 900; }
        .page-banner p { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 4px; }
        .btn-add { display: flex; align-items: center; gap: 8px; padding: 12px 22px; background: white; color: #1e4d2b; border: none; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; letter-spacing: 1px; transition: all 0.2s; white-space: nowrap; }
        .btn-add:hover { background: #e8f5e9; }
        .search-bar-wrap { margin-bottom: 24px; }
        .search-box { display: flex; align-items: center; gap: 12px; padding: 14px 20px; background: white; border: 1.5px solid #ddeedd; border-radius: 12px; box-shadow: 0 2px 8px rgba(30,77,43,0.06); width: 100%; }
        .search-box i { color: #8aaa92; font-size: 16px; flex-shrink: 0; }
        .search-box input { border: none; outline: none; font-size: 14px; color: #1a3d24; background: transparent; width: 100%; }
        .search-box input::placeholder { color: #b8d4bc; }
        .search-count { font-size: 12px; font-weight: 700; color: #8aaa92; white-space: nowrap; letter-spacing: 1px; }
        .vehicle-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .vehicle-card { background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e8f0e8; transition: all 0.2s; }
        .vehicle-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(30,77,43,0.12); }
        .vehicle-img { width: 100%; height: 160px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
        .vehicle-img img { width: 100%; height: 100%; object-fit: cover; }
        .vehicle-img .icon-fallback { font-size: 48px; color: #6DBE47; }
        .vehicle-info { padding: 16px; }
        .vehicle-status-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .vehicle-actions { display: flex; gap: 8px; }
        .action-btn { width: 30px; height: 30px; border: none; background: transparent; cursor: pointer; color: #8aaa92; font-size: 14px; border-radius: 6px; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
        .action-btn:hover { background: #f0f5f0; color: #1a3d24; }
        .action-btn.delete:hover { background: #fde8e8; color: #e74c3c; }
        .vehicle-name-block { margin-bottom: 12px; }
        .vehicle-model { font-size: 16px; font-weight: 800; color: #1a3d24; }
        .vehicle-category { font-size: 10px; font-weight: 700; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; }
        .rate-value { font-size: 22px; font-weight: 900; color: #1a3d24; }
        .rate-value span { font-size: 12px; color: #8aaa92; font-weight: 500; }
        .status-badge { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; white-space: nowrap; }
        .status-available   { background: #e8f5e9; color: #1e7e34; }
        .status-onfield     { background: #fff3e0; color: #e65100; }
        .status-maintenance { background: #fde8e8; color: #c0392b; }

        /* Staff badge on card */
        .staff-info { margin-top: 10px; padding-top: 10px; border-top: 1px solid #f0f5f0; }
        .staff-badge { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #5a7a5e; margin-bottom: 3px; }
        .staff-badge i { color: #6DBE47; font-size: 11px; width: 14px; }

        /* MODAL */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center; padding: 16px; }
        .modal-overlay.open { display: flex; }
        .modal { background: white; border-radius: 18px; width: 100%; max-width: 560px; padding: 32px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); animation: modalIn 0.25s ease; max-height: 90vh; overflow-y: auto; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .modal-title { font-size: 20px; font-weight: 900; color: #1a3d24; }
        .modal-close { width: 34px; height: 34px; background: #f4f7f4; border: none; border-radius: 8px; cursor: pointer; color: #8aaa92; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .modal-close:hover { background: #fde8e8; color: #e74c3c; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 11px; font-weight: 800; color: #8aaa92; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; border: 1.5px solid #ddeedd; border-radius: 10px; font-size: 16px; color: #1a3d24; background: white; outline: none; transition: border-color 0.2s; font-family: 'Segoe UI', sans-serif; }
        .form-control:focus { border-color: #6DBE47; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .img-upload-box { border: 2px dashed #ddeedd; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #f9fdf9; position: relative; overflow: hidden; }
        .img-upload-box:hover { border-color: #6DBE47; background: #f0faf0; }
        .img-upload-box i { font-size: 28px; color: #6DBE47; margin-bottom: 8px; }
        .img-upload-box p { font-size: 12px; color: #8aaa92; font-weight: 600; }
        .img-preview { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; display: none; margin-top: 10px; }
        .btn-submit { width: 100%; padding: 14px; background: #1e4d2b; color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; transition: all 0.2s; margin-top: 8px; }
        .btn-submit:hover { background: #2d6a3f; }
        .empty-state { text-align: center; padding: 60px 20px; grid-column: 1/-1; }
        .empty-state i { font-size: 48px; color: #c8e6c9; margin-bottom: 16px; display: block; }
        .empty-state h3 { font-size: 18px; font-weight: 800; color: #8aaa92; margin-bottom: 8px; }
        .no-results { text-align: center; padding: 60px 20px; grid-column: 1/-1; display: none; }
        .no-results i { font-size: 48px; color: #c8e6c9; margin-bottom: 16px; display: block; }
        .no-results h3 { font-size: 18px; font-weight: 800; color: #8aaa92; margin-bottom: 8px; }
        .toast { position: fixed; bottom: 24px; right: 24px; background: #1e4d2b; color: white; padding: 14px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); transform: translateY(80px); opacity: 0; transition: all 0.3s; z-index: 300; }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.error { background: #c0392b; }

        /* Staff section in modal */
        .staff-section { background: #f4f7f4; border-radius: 12px; padding: 18px; margin-bottom: 18px; border: 1.5px solid #e8f0e8; }
        .staff-section-title { font-size: 11px; font-weight: 800; color: #1e4d2b; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }

        @media (max-width: 1200px) { .vehicle-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 900px) { .sidebar { transform: translateX(-100%); } .sidebar.open { transform: translateX(0); } .sidebar-close { display: flex; } .main { margin-left: 0; } .topbar { padding: 0 16px; } .hamburger { display: block; } .content { padding: 20px 16px; } .vehicle-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; } }
        @media (max-width: 600px) { .vehicle-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; } .form-row { grid-template-columns: 1fr; } .topbar { padding: 0 12px; height: 58px; } }
        @media (max-width: 380px) { .vehicle-grid { grid-template-columns: 1fr; } }

      
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
            <div class="topbar-brand"><div class="topbar-brand-text"><div class="name">VEHICLE</div></div></div>
        </div>
        @include('components.topbar')
    </div>

    <div class="content">
        <div class="page-banner">
            <div><h1>LIST</h1><p>Add, update, or remove vehicles and assign staff</p></div>
            <button type="button" class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> ADD</button>
        </div>

        <div class="search-bar-wrap">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="vehicleSearch" placeholder="Search by vehicle name or type..." oninput="searchVehicles()" autocomplete="off">
                <span class="search-count" id="searchCount"></span>
            </div>
        </div>

        <div class="vehicle-grid" id="vehicleGrid">
            @forelse($vehicles as $v)
                @php
                    $statusClass = $v->status === 'available' ? 'status-available' : ($v->status === 'onfield' ? 'status-onfield' : 'status-maintenance');
                    $statusLabel = $v->status === 'available' ? 'Available' : ($v->status === 'onfield' ? 'On Field' : 'Maintenance');
                    $icon = $v->type === 'Harvester' ? 'fa-wheat-awn' : 'fa-tractor';
                    $bgMap = ['Tractor'=>'linear-gradient(135deg,#e8f5e9,#f1f8e9)','Harvester'=>'linear-gradient(135deg,#fff8e1,#fef9c3)','Sprayer'=>'linear-gradient(135deg,#e8f0ff,#ede9fe)','Planter'=>'linear-gradient(135deg,#fff3e0,#fde68a)'];
                    $iconColorMap = ['Tractor'=>'#6DBE47','Harvester'=>'#f59e0b','Sprayer'=>'#6366f1','Planter'=>'#e65100'];
                    $bg = $bgMap[$v->type] ?? 'linear-gradient(135deg,#e8f5e9,#f1f8e9)';
                    $iconColor = $iconColorMap[$v->type] ?? '#6DBE47';
                    // Count helpers dynamically
                    $helperCount = collect([$v->helper1_name ?? null, $v->helper2_name ?? null, $v->helper3_name ?? null])->filter()->count();
                @endphp
                <div class="vehicle-card" data-name="{{ strtolower($v->name) }}" data-type="{{ strtolower($v->type) }}" data-status="{{ strtolower($v->status) }}">
                    <div class="vehicle-img" style="background:{{ $bg }};">
                        @if($v->image_data)
    <img src="{{ $v->image_data }}" alt="{{ $v->name }}">
@else
                            <i class="fa-solid {{ $icon }} icon-fallback" style="color:{{ $iconColor }};"></i>
                        @endif
                    </div>
                    <div class="vehicle-info">
                        <div class="vehicle-status-row">
                            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            <div class="vehicle-actions">
                                <button type="button" class="action-btn" title="Edit"
                                    onclick="openEditModal(
                                        {{ $v->id }},
                                        '{{ addslashes($v->name) }}',
                                        '{{ $v->type }}',
                                        '{{ $v->rate }}',
                                        '{{ $v->max_hectares }}',
                                        '{{ $v->status }}',
                                        '{{ $v->image_path ? Storage::url($v->image_path) : '' }}',
                                        '{{ $v->estimated_fix_days ?? '' }}',
                                        '{{ addslashes($v->driver_name ?? '') }}',
                                        '{{ $v->driver_pay ?? 0 }}',
                                        '{{ addslashes($v->helper1_name ?? '') }}',
                                        '{{ addslashes($v->helper2_name ?? '') }}',
                                        '{{ addslashes($v->helper3_name ?? '') }}',
                                        '{{ $v->helper_pay_each ?? 0 }}',
                                        '{{ $v->diesel_cost ?? 0 }}'
                                    )">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="action-btn delete" title="Delete"
                                    onclick="openDeleteModal({{ $v->id }}, '{{ addslashes($v->name) }}')">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                        <div class="vehicle-name-block">
                            <div class="vehicle-model">{{ $v->name }}</div>
                            <div class="vehicle-category">{{ $v->type }}</div>
                        </div>
                        <div class="rate-value">₱{{ number_format($v->rate) }} <span>/ hectare</span></div>
                        @if($v->driver_name)
                        <div class="staff-info">
                            <div class="staff-badge"><i class="fa-solid fa-id-badge"></i> Driver: {{ $v->driver_name }}</div>
                            @if($v->type === 'Harvester' && $helperCount > 0)
                            <div class="staff-badge">
                                <i class="fa-solid fa-people-group"></i>
                                {{ $helperCount }} Helper{{ $helperCount > 1 ? 's' : '' }} assigned
                            </div>
                            @endif
                            @if($v->diesel_cost)
                            <div class="staff-badge"><i class="fa-solid fa-gas-pump"></i> Diesel: ₱{{ number_format($v->diesel_cost) }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fa-solid fa-tractor"></i>
                    <h3>No vehicles yet</h3>
                    <p>Click the ADD button to register your first vehicle.</p>
                </div>
            @endforelse
            <div class="no-results" id="noResults">
                <i class="fa-solid fa-magnifying-glass"></i>
                <h3>No vehicles found</h3>
                <p>Try a different search keyword.</p>
            </div>
        </div>
    </div>
</main>

{{-- ADD MODAL --}}
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Add New Vehicle</div>
            <button type="button" class="modal-close" onclick="closeAddModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.vehicle.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Vehicle Name / Model</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. John Deere 6R 150" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Type / Category</label>
                    <select name="type" id="addType" class="form-control" required onchange="onAddTypeChange(this.value)">
                        <option value="">Select type...</option>
                        <option value="Tractor">Tractor</option>
                        <option value="Harvester">Harvester</option>
                        <option value="Sprayer">Sprayer</option>
                        <option value="Planter">Planter</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Price / Hectare (₱)</label>
                    <input type="number" name="rate" class="form-control" placeholder="150" min="1" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Max Hectares Per Day</label>
                <input type="number" name="max_hectares" class="form-control" placeholder="e.g. 4" min="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" id="addStatus" class="form-control" required onchange="toggleAddFixDays(this.value)">
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group" id="addFixDaysGroup" style="display:none;">
                <label class="form-label">Estimated Days to Fix</label>
                <input type="number" name="estimated_fix_days" id="editFixDays" class="form-control" min="1" max="365" placeholder="e.g. 3">
            </div>

            {{-- STAFF SECTION --}}
            <div class="staff-section">
                <div class="staff-section-title"><i class="fa-solid fa-users"></i> Staff & Cost Setup</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Driver Name</label>
                        <input type="text" name="driver_name" class="form-control" placeholder="e.g. Juan dela Cruz">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Driver Pay (₱/day)</label>
                        <input type="number" name="driver_pay" class="form-control" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Diesel Cost (₱/day)</label>
                    <input type="number" name="diesel_cost" class="form-control" placeholder="0.00" min="0" step="0.01">
                </div>
                {{-- Helpers (Harvester only) --}}
                <div id="addHelperSection" style="display:none;">
                    <div style="font-size:10px;font-weight:800;color:#8aaa92;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px;margin-top:4px;">Helpers (Harvester)</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Helper 1 Name</label>
                            <input type="text" name="helper1_name" class="form-control" placeholder="Helper 1">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Helper 2 Name</label>
                            <input type="text" name="helper2_name" class="form-control" placeholder="Helper 2">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Helper 3 Name <span style="font-size:10px;color:#b8d4bc;font-weight:500;text-transform:none;letter-spacing:0;">(optional)</span></label>
                            <input type="text" name="helper3_name" class="form-control" placeholder="Helper 3 (optional)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Helper Pay Each (₱/day)</label>
                            <input type="number" name="helper_pay_each" class="form-control" placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Vehicle Photo</label>
                <div class="img-upload-box" onclick="document.getElementById('addPhotoInput').click()">
                    <input type="file" name="photo" id="addPhotoInput" accept="image/*" style="display:none;" onchange="previewImage(this,'addPreview')">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <p>Click to upload photo<br><small style="color:#b8d4bc;">JPG, PNG, WEBP — max 2MB</small></p>
                    <img id="addPreview" class="img-preview" src="" alt="Preview">
                </div>
            </div>
            <button type="submit" class="btn-submit"><i class="fa-solid fa-plus"></i> &nbsp;Add Vehicle</button>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Edit Vehicle</div>
            <button type="button" class="modal-close" onclick="closeEditModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Vehicle Name / Model</label>
                <input type="text" name="name" id="editName" class="form-control" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Type / Category</label>
                    <select name="type" id="editType" class="form-control" required onchange="onEditTypeChange(this.value)">
                        <option value="Tractor">Tractor</option>
                        <option value="Harvester">Harvester</option>
                        <option value="Sprayer">Sprayer</option>
                        <option value="Planter">Planter</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Price / Hectare (₱)</label>
                    <input type="number" name="rate" id="editRate" class="form-control" min="1" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Max Hectares Per Day</label>
                <input type="number" name="max_hectares" id="editMaxHectares" class="form-control" min="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" id="editStatus" class="form-control" required onchange="toggleEditFixDays(this.value)">
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group" id="editFixDaysGroup" style="display:none;">
                <label class="form-label">Estimated Days to Fix</label>
                <input type="number" name="estimated_fix_days" id="editFixDays" class="form-control" min="1" max="365" placeholder="e.g. 3">
            </div>

            {{-- STAFF SECTION --}}
            <div class="staff-section">
                <div class="staff-section-title"><i class="fa-solid fa-users"></i> Staff & Cost Setup</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Driver Name</label>
                        <input type="text" name="driver_name" id="editDriverName" class="form-control" placeholder="e.g. Juan dela Cruz">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Driver Pay (₱/day)</label>
                        <input type="number" name="driver_pay" id="editDriverPay" class="form-control" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Diesel Cost (₱/day)</label>
                    <input type="number" name="diesel_cost" id="editDieselCost" class="form-control" placeholder="0.00" min="0" step="0.01">
                </div>
                <div id="editHelperSection" style="display:none;">
                    <div style="font-size:10px;font-weight:800;color:#8aaa92;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px;margin-top:4px;">Helpers (Harvester)</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Helper 1 Name</label>
                            <input type="text" name="helper1_name" id="editHelper1" class="form-control" placeholder="Helper 1">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Helper 2 Name</label>
                            <input type="text" name="helper2_name" id="editHelper2" class="form-control" placeholder="Helper 2">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Helper 3 Name <span style="font-size:10px;color:#b8d4bc;font-weight:500;text-transform:none;letter-spacing:0;">(optional)</span></label>
                            <input type="text" name="helper3_name" id="editHelper3" class="form-control" placeholder="Helper 3 (optional)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Helper Pay Each (₱/day)</label>
                            <input type="number" name="helper_pay_each" id="editHelperPay" class="form-control" placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Change Photo (optional)</label>
                <div class="img-upload-box" onclick="document.getElementById('editPhotoInput').click()">
                    <input type="file" name="photo" id="editPhotoInput" accept="image/*" style="display:none;" onchange="previewImage(this,'editPreview')">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <p>Click to change photo<br><small style="color:#b8d4bc;">Leave blank to keep current</small></p>
                    <img id="editPreview" class="img-preview" src="" alt="Preview">
                </div>
            </div>
            <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> &nbsp;Save Changes</button>
        </form>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <div class="modal-title">Delete Vehicle</div>
            <button type="button" class="modal-close" onclick="closeDeleteModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <p style="color:#5a7a5e;font-size:14px;margin-bottom:24px;">Are you sure you want to remove <strong id="deleteVehicleName"></strong>? This cannot be undone.</p>
        <form method="POST" id="deleteForm">
            @csrf @method('DELETE')
            <div style="display:flex;gap:12px;">
                <button type="button" onclick="closeDeleteModal()" style="flex:1;padding:12px;border:1.5px solid #ddeedd;border-radius:10px;background:white;color:#1a3d24;font-size:13px;font-weight:800;cursor:pointer;">Cancel</button>
                <button type="submit" style="flex:1;padding:12px;border:none;border-radius:10px;background:#c0392b;color:white;font-size:13px;font-weight:800;cursor:pointer;">Delete</button>
            </div>
        </form>
    </div>
</div>

<div class="toast" id="toast"><i class="fa-solid fa-circle-check"></i><span id="toastMsg">Action completed.</span></div>

<script>
    function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('open'); document.body.style.overflow='hidden'; }
    function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('open'); document.body.style.overflow=''; }
    function openAddModal()  { document.getElementById('addModal').classList.add('open'); document.body.style.overflow='hidden'; }
    function closeAddModal() { document.getElementById('addModal').classList.remove('open'); document.body.style.overflow=''; }
    function closeEditModal() { document.getElementById('editModal').classList.remove('open'); document.body.style.overflow=''; }
    function openDeleteModal(id, name) { document.getElementById('deleteVehicleName').textContent=name; document.getElementById('deleteForm').action='/admin/vehicle/'+id; document.getElementById('deleteModal').classList.add('open'); document.body.style.overflow='hidden'; }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.remove('open'); document.body.style.overflow=''; }

    document.querySelectorAll('.modal-overlay').forEach(o => o.addEventListener('click', function(e) { if (e.target===this) { this.classList.remove('open'); document.body.style.overflow=''; } }));

    // Add modal type change
    function onAddTypeChange(type) {
        document.getElementById('addHelperSection').style.display = type === 'Harvester' ? 'block' : 'none';
    }
    function onEditTypeChange(type) {
        document.getElementById('editHelperSection').style.display = type === 'Harvester' ? 'block' : 'none';
    }

    function toggleAddFixDays(status) {
        const g = document.getElementById('addFixDaysGroup');
        const i = document.getElementById('addFixDays');
        g.style.display = status === 'maintenance' ? 'block' : 'none';
        i.required = status === 'maintenance';
    }
    function toggleEditFixDays(status) {
        const g = document.getElementById('editFixDaysGroup');
        const i = document.getElementById('editFixDays');
        g.style.display = status === 'maintenance' ? 'block' : 'none';
        i.required = status === 'maintenance';
    }

    function openEditModal(id, name, type, rate, maxHectares, status, imgUrl, fixDays, driverName, driverPay, h1, h2, h3, helperPayEach, dieselCost) {
        document.getElementById('editName').value        = name;
        document.getElementById('editType').value        = type;
        document.getElementById('editRate').value        = rate;
        document.getElementById('editMaxHectares').value = maxHectares;
        const safeStatus = (status === 'onfield') ? 'available' : status;
        document.getElementById('editStatus').value      = safeStatus;
        document.getElementById('editFixDays').value     = fixDays || '';
        toggleEditFixDays(safeStatus);
        // Staff
        document.getElementById('editDriverName').value  = driverName || '';
        document.getElementById('editDriverPay').value   = driverPay  || '';
        document.getElementById('editHelper1').value     = h1 || '';
        document.getElementById('editHelper2').value     = h2 || '';
        document.getElementById('editHelper3').value     = h3 || '';
        document.getElementById('editHelperPay').value   = helperPayEach || '';
        document.getElementById('editDieselCost').value  = dieselCost || '';
        onEditTypeChange(type);
        document.getElementById('editForm').action = '/admin/vehicle/' + id;
        const preview = document.getElementById('editPreview');
        if (imgUrl) { preview.src = imgUrl; preview.style.display = 'block'; } else { preview.style.display = 'none'; }
        document.getElementById('editModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function searchVehicles() {
        const query = document.getElementById('vehicleSearch').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.vehicle-card');
        let visible = 0;
        cards.forEach(card => {
            const match = card.dataset.name.includes(query) || card.dataset.type.includes(query) || card.dataset.status.includes(query);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('noResults').style.display = (visible === 0 && query !== '') ? 'block' : 'none';
        const c = document.getElementById('searchCount');
        c.textContent = query !== '' ? visible + ' result' + (visible !== 1 ? 's' : '') : '';
    }

    function showToast(msg, isError = false) {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        t.className = 'toast' + (isError ? ' error' : '');
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }
    @if(session('success')) showToast("{{ session('success') }}"); @endif
    @if(session('deleted')) showToast("{{ session('deleted') }}", true); @endif
</script>
@include('components.modals')
@include('components.notification-bell')
@include('components.feedback-modal')
</body>
</html>