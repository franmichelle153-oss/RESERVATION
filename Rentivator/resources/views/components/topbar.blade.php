@php use Illuminate\Support\Facades\Storage; @endphp

<div class="topbar-right" style="display:flex; align-items:center; gap:16px; flex-shrink:0;">

    {{-- ── Notification Bell ── --}}
    <div class="rp-notif-wrap" style="position:relative;">
        <button
            id="rp-notif-btn"
            onclick="rpToggleNotif(event)"
            style="position:relative; width:38px; height:38px; border:none; background:transparent;
                   cursor:pointer; display:flex; align-items:center; justify-content:center;
                   border-radius:10px; transition:background 0.2s;"
            onmouseenter="this.style.background='rgba(255,255,255,0.12)'"
            onmouseleave="this.style.background='transparent'"
            title="Notifications">
            <i class="fa-regular fa-bell" style="font-size:18px;" id="rp-notif-icon"></i>
            <span id="rp-notif-badge"
                  style="display:none; position:absolute; top:3px; right:3px; min-width:18px; height:18px;
                         background:#e74c3c; border-radius:9px; border:2px solid transparent;
                         font-size:10px; font-weight:800; color:white;
                         align-items:center; justify-content:center; padding:0 3px; line-height:1;">
                0
            </span>
        </button>
    </div>

    {{-- ── Profile Button + Dropdown ── --}}
    <div class="rp-profile-wrap" style="position:relative;">
        <button class="rp-profile-btn" onclick="rpToggleDropdown(event)"
                style="display:flex; align-items:center; gap:10px; background:none; border:none;
                       cursor:pointer; padding:4px 8px; border-radius:10px; transition:background 0.2s;"
                onmouseenter="this.style.background='rgba(255,255,255,0.1)'"
                onmouseleave="this.style.background='none'">
            <div style="width:38px; height:38px; border-radius:10px; overflow:hidden; flex-shrink:0;
                        background:#1e4d2b; display:flex; align-items:center; justify-content:center;">
                @if(auth()->user()->profile_picture)
                    <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                         style="width:100%; height:100%; object-fit:cover;" id="rp-topbar-avatar">
                @else
                    <span id="rp-topbar-initials" style="color:white; font-weight:700; font-size:14px;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </span>
                    <img src="" id="rp-topbar-avatar"
                         style="display:none; width:100%; height:100%; object-fit:cover;">
                @endif
            </div>
            <div class="user-text" style="text-align:left;">
                <div style="font-size:13px; font-weight:700;" class="rp-topbar-username" id="rp-topbar-name">
                    {{ auth()->user()->name ?? 'User' }}
                </div>
                <div style="font-size:10px; letter-spacing:1px; text-transform:uppercase;" class="rp-topbar-role">
                    {{ auth()->user()->role ?? 'Member' }}
                </div>
            </div>
            <i class="fa-solid fa-chevron-down"
               style="font-size:10px; color:rgba(255,255,255,0.6); transition:transform 0.2s;"
               id="rp-chevron"></i>
        </button>

        <div id="rp-dropdown"
             style="display:none; position:absolute; top:calc(100% + 10px); right:0;
                    border:1px solid; border-radius:16px; min-width:220px; z-index:1000; overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid;" id="rp-dropdown-header">
                <div style="font-size:13px; font-weight:800;" id="rp-dropdown-name">
                    {{ auth()->user()->name ?? 'User' }}
                </div>
                <div style="font-size:11px; margin-top:2px;" id="rp-dropdown-email">
                    {{ auth()->user()->email ?? '' }}
                </div>
            </div>
            <div style="padding:8px 0;">
                <button onclick="rpOpenEditProfile()" class="rp-dd-btn"
                        style="width:100%; padding:12px 20px; background:none; border:none; cursor:pointer;
                               display:flex; align-items:center; gap:12px; font-size:13px; font-weight:700;
                               text-align:left; transition:background 0.15s;">
                    <div class="rp-dd-icon" style="width:32px; height:32px; border-radius:8px;
                                                   display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <span class="rp-dd-label">Edit Profile</span>
                </button>
                <button onclick="rpOpenDisplay()" class="rp-dd-btn"
                        style="width:100%; padding:12px 20px; background:none; border:none; cursor:pointer;
                               display:flex; align-items:center; gap:12px; font-size:13px; font-weight:700;
                               text-align:left; transition:background 0.15s;">
                    <div class="rp-dd-icon" style="width:32px; height:32px; border-radius:8px;
                                                   display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <span class="rp-dd-label">Display & Accessibility</span>
                </button>
            </div>
        </div>
    </div>
</div>