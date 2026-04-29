{{-- MODALS + SCRIPTS — i-include ISANG BESES LANG, bago mag-close ng </body> --}}
@php use Illuminate\Support\Facades\Storage; @endphp

{{-- ── EDIT PROFILE MODAL ── --}}
<div id="rp-edit-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; padding:20px;">
    <div style="border-radius:20px; width:100%; max-width:480px; max-height:92vh; overflow-y:auto; box-shadow:0 24px 60px rgba(0,0,0,0.25); animation:rpModalIn 0.25s cubic-bezier(.4,0,.2,1);" id="rp-edit-modal-card">
        <div style="background:linear-gradient(135deg,#1e4d2b,#2d6a3f); padding:28px 32px; border-radius:20px 20px 0 0; position:relative;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="position:relative; cursor:pointer;" onclick="document.getElementById('rp-photo-input').click()">
                    <div style="width:72px; height:72px; border-radius:50%; overflow:hidden; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; border:3px solid rgba(255,255,255,0.4);">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ Storage::url(auth()->user()->profile_picture) }}" style="width:100%; height:100%; object-fit:cover;" id="rp-modal-avatar">
                        @else
                            <span id="rp-modal-initials" style="color:white; font-size:24px; font-weight:800;">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</span>
                            <img src="" id="rp-modal-avatar" style="display:none; width:100%; height:100%; object-fit:cover;">
                        @endif
                    </div>
                    <div style="position:absolute; bottom:0; right:0; width:24px; height:24px; background:#6DBE47; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white;">
                        <i class="fa-solid fa-camera" style="font-size:10px; color:white;"></i>
                    </div>
                </div>
                <div>
                    <div style="font-size:18px; font-weight:900; color:white;">Edit Profile</div>
                    <div style="font-size:11px; color:rgba(255,255,255,0.6); margin-top:3px;">Click photo to change</div>
                </div>
            </div>
            <button onclick="rpCloseEdit()" style="position:absolute; top:16px; right:16px; width:34px; height:34px; background:rgba(255,255,255,0.15); border:none; border-radius:8px; color:white; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="rp-edit-body" style="padding:28px 32px;">
            <input type="file" id="rp-photo-input" accept="image/*" style="display:none;" onchange="rpPreviewPhoto(this)">

            {{-- Full Name --}}
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px;" class="rp-field-label">Full Name</label>
                <div class="rp-input-wrap" style="display:flex; align-items:center; gap:12px; border:1.5px solid; border-radius:10px; padding:12px 16px;">
                    <i class="fa-regular fa-user rp-input-icon" style="font-size:14px; flex-shrink:0;"></i>
                    <input type="text" id="rp-name" value="{{ auth()->user()->name ?? '' }}" class="rp-input" style="border:none; outline:none; font-size:14px; background:transparent; width:100%; font-family:'Segoe UI',sans-serif;" placeholder="Juan Dela Cruz">
                </div>
            </div>

            {{-- Phone + Email --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px;">
                <div>
                    <label style="display:block; font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px;" class="rp-field-label">Phone Number</label>
                    <div class="rp-input-wrap" style="display:flex; align-items:center; gap:12px; border:1.5px solid; border-radius:10px; padding:12px 16px;">
                        <i class="fa-solid fa-phone rp-input-icon" style="font-size:14px; flex-shrink:0;"></i>
                        <input type="text" id="rp-phone" value="{{ auth()->user()->phone_number ?? '' }}" class="rp-input" style="border:none; outline:none; font-size:14px; background:transparent; width:100%; font-family:'Segoe UI',sans-serif;" placeholder="09XXXXXXXXX">
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px;" class="rp-field-label">Email</label>
                    <div class="rp-input-wrap rp-input-readonly" style="display:flex; align-items:center; gap:12px; border:1.5px solid; border-radius:10px; padding:12px 16px; opacity:0.6;">
                        <i class="fa-solid fa-envelope rp-input-icon" style="font-size:14px; flex-shrink:0;"></i>
                        <input type="text" value="{{ auth()->user()->email ?? '' }}" class="rp-input" style="border:none; outline:none; font-size:14px; background:transparent; width:100%; font-family:'Segoe UI',sans-serif;" readonly>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px;" class="rp-field-label">Location / Address</label>
                <div class="rp-input-wrap" style="display:flex; align-items:center; gap:12px; border:1.5px solid; border-radius:10px; padding:12px 16px;">
                    <i class="fa-solid fa-location-dot rp-input-icon" style="font-size:14px; flex-shrink:0;"></i>
                    <input type="text" id="rp-address" value="{{ auth()->user()->address ?? '' }}" class="rp-input" style="border:none; outline:none; font-size:14px; background:transparent; width:100%; font-family:'Segoe UI',sans-serif;" placeholder="Street, City, Province">
                </div>
            </div>

            {{-- Save Profile --}}
            <button onclick="rpSaveProfile()" id="rp-save-btn" style="width:100%; padding:14px; background:linear-gradient(135deg,#1e4d2b,#2d6a3f); color:white; border:none; border-radius:12px; font-size:14px; font-weight:800; letter-spacing:1px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; font-family:'Segoe UI',sans-serif;">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
            <div id="rp-save-msg" style="display:none; margin-top:14px; padding:10px 14px; border-radius:8px; font-size:12px; font-weight:700; text-align:center;"></div>

            {{-- ── CHANGE PASSWORD SECTION ── --}}
            <div style="margin-top:28px; padding-top:24px; border-top:1px solid;" id="rp-pw-divider">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                    <div style="width:36px; height:36px; background:linear-gradient(135deg,#1e4d2b,#2d6a3f); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-lock" style="font-size:14px; color:#6DBE47;"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:800;" class="rp-section-title">Change Password</div>
                        <div style="font-size:11px; color:#8aaa92; margin-top:1px;">Enter current password to update</div>
                    </div>
                </div>

                {{-- Current Password --}}
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px;" class="rp-field-label">Current Password</label>
                    <div class="rp-input-wrap" style="display:flex; align-items:center; gap:12px; border:1.5px solid; border-radius:10px; padding:12px 16px;">
                        <i class="fa-solid fa-key rp-input-icon" style="font-size:14px; flex-shrink:0;"></i>
                        <input type="password" id="rp-pw-current" class="rp-input" style="border:none; outline:none; font-size:14px; background:transparent; width:100%; font-family:'Segoe UI',sans-serif;" placeholder="••••••••" oninput="rpVerifyCurrentPw(this.value)">
                        <button type="button" onclick="rpTogglePw('rp-pw-current','rp-eye-current')" style="background:none; border:none; cursor:pointer; padding:0; color:#8aaa92; flex-shrink:0;">
                            <i class="fa-regular fa-eye rp-input-icon" id="rp-eye-current" style="font-size:14px;"></i>
                        </button>
                    </div>
                    <div id="rp-pw-current-status" style="display:none; font-size:11px; font-weight:700; margin-top:6px; padding:6px 10px; border-radius:6px;"></div>
                </div>

                {{-- New Password — locked until current password verified --}}
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px;" class="rp-field-label">New Password</label>
                    <div class="rp-input-wrap rp-pw-locked" id="rp-wrap-new" style="display:flex; align-items:center; gap:12px; border:1.5px solid; border-radius:10px; padding:12px 16px; opacity:0.45; pointer-events:none;">
                        <i class="fa-solid fa-lock rp-input-icon" style="font-size:14px; flex-shrink:0;"></i>
                        <input type="password" id="rp-pw-new" class="rp-input" disabled style="border:none; outline:none; font-size:14px; background:transparent; width:100%; font-family:'Segoe UI',sans-serif;" placeholder="Verify current password first">
                        <button type="button" onclick="rpTogglePw('rp-pw-new','rp-eye-new')" style="background:none; border:none; cursor:pointer; padding:0; color:#8aaa92; flex-shrink:0;">
                            <i class="fa-regular fa-eye rp-input-icon" id="rp-eye-new" style="font-size:14px;"></i>
                        </button>
                    </div>
                </div>

                {{-- Confirm Password — locked until current password verified --}}
                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:10px; font-weight:800; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px;" class="rp-field-label">Confirm New Password</label>
                    <div class="rp-input-wrap rp-pw-locked" id="rp-wrap-confirm" style="display:flex; align-items:center; gap:12px; border:1.5px solid; border-radius:10px; padding:12px 16px; opacity:0.45; pointer-events:none;">
                        <i class="fa-solid fa-lock rp-input-icon" style="font-size:14px; flex-shrink:0;"></i>
                        <input type="password" id="rp-pw-confirm" class="rp-input" disabled style="border:none; outline:none; font-size:14px; background:transparent; width:100%; font-family:'Segoe UI',sans-serif;" placeholder="Verify current password first">
                        <button type="button" onclick="rpTogglePw('rp-pw-confirm','rp-eye-confirm')" style="background:none; border:none; cursor:pointer; padding:0; color:#8aaa92; flex-shrink:0;">
                            <i class="fa-regular fa-eye rp-input-icon" id="rp-eye-confirm" style="font-size:14px;"></i>
                        </button>
                    </div>
                </div>

                <button onclick="rpChangePassword()" id="rp-pw-btn" style="width:100%; padding:14px; background:linear-gradient(135deg,#2d6a3f,#1e4d2b); color:white; border:none; border-radius:12px; font-size:14px; font-weight:800; letter-spacing:1px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; font-family:'Segoe UI',sans-serif;">
                    <i class="fa-solid fa-shield-halved"></i> Update Password
                </button>
                <div id="rp-pw-msg" style="display:none; margin-top:14px; padding:10px 14px; border-radius:8px; font-size:12px; font-weight:700; text-align:center;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── DISPLAY & ACCESSIBILITY MODAL ── --}}
<div id="rp-display-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; padding:20px;">
    <div id="rp-display-modal" style="border-radius:20px; width:100%; max-width:420px; box-shadow:0 24px 60px rgba(0,0,0,0.25); animation:rpModalIn 0.25s cubic-bezier(.4,0,.2,1); overflow:hidden;">
        <div style="background:linear-gradient(135deg,#1e4d2b,#2d6a3f); padding:24px 28px; position:relative; display:flex; align-items:center; gap:14px;">
            <div style="width:44px; height:44px; background:rgba(255,255,255,0.15); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:18px; color:#6DBE47;">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <div style="font-size:16px; font-weight:900; color:white;">Display & Accessibility</div>
                <div style="font-size:11px; color:rgba(255,255,255,0.55); margin-top:2px;">Customize your experience</div>
            </div>
            <button onclick="rpCloseDisplay(false)" style="position:absolute; top:14px; right:16px; width:32px; height:32px; background:rgba(255,255,255,0.15); border:none; border-radius:8px; color:white; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div id="rp-display-body" style="padding:24px 28px;">
            {{-- Dark Mode Row --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 0; border-bottom:1px solid;" id="rp-dark-row">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; color:#6DBE47; background:#1e4d2b;">
                        <i class="fa-solid fa-moon" id="rp-dark-icon"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:800;" class="rp-section-title">Dark Mode</div>
                        <div style="font-size:11px; color:#8aaa92; margin-top:2px;">Switch to dark theme</div>
                    </div>
                </div>
                {{-- Toggle with hover effect --}}
                <div
                    onclick="rpToggleDarkMode()"
                    id="rp-dark-toggle"
                    style="width:52px; height:28px; border-radius:14px; position:relative; cursor:pointer; transition:background 0.3s, box-shadow 0.2s; flex-shrink:0;"
                    onmouseenter="rpDarkToggleHover(true)"
                    onmouseleave="rpDarkToggleHover(false)"
                    title="Toggle dark mode"
                >
                    <div id="rp-dark-knob" style="position:absolute; top:3px; left:3px; width:22px; height:22px; background:white; border-radius:50%; transition:transform 0.3s; box-shadow:0 2px 6px rgba(0,0,0,0.2);"></div>
                </div>
            </div>

            {{-- Font Size Row --}}
            <div style="padding:18px 0;">
                <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
                    <div style="width:40px; height:40px; background:#1e4d2b; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; color:#6DBE47;">
                        <i class="fa-solid fa-text-height"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:800;" class="rp-section-title">Font Size</div>
                        <div style="font-size:11px; color:#8aaa92; margin-top:2px;">Adjust text size</div>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">
                    <button onclick="rpSetFontSize('small')" id="rp-font-small" class="rp-font-btn" style="padding:12px; border:2px solid; border-radius:10px; cursor:pointer; font-weight:800; transition:all 0.2s; font-family:'Segoe UI',sans-serif;">
                        <span style="font-size:13px; display:block; margin-bottom:4px;">Aa</span>Small
                    </button>
                    <button onclick="rpSetFontSize('medium')" id="rp-font-medium" class="rp-font-btn" style="padding:12px; border:2px solid; border-radius:10px; cursor:pointer; font-weight:800; transition:all 0.2s; font-family:'Segoe UI',sans-serif;">
                        <span style="font-size:16px; display:block; margin-bottom:4px;">Aa</span>Medium
                    </button>
                    <button onclick="rpSetFontSize('large')" id="rp-font-large" class="rp-font-btn" style="padding:12px; border:2px solid; border-radius:10px; cursor:pointer; font-weight:800; transition:all 0.2s; font-family:'Segoe UI',sans-serif;">
                        <span style="font-size:20px; display:block; margin-bottom:4px;">Aa</span>Large
                    </button>
                </div>
            </div>

            <button onclick="rpSavePreferences()" style="width:100%; padding:13px; background:linear-gradient(135deg,#1e4d2b,#2d6a3f); color:white; border:none; border-radius:12px; font-size:13px; font-weight:800; letter-spacing:1px; cursor:pointer; margin-top:8px; font-family:'Segoe UI',sans-serif;">
                <i class="fa-solid fa-floppy-disk"></i> &nbsp;Save Preferences
            </button>
        </div>
    </div>
</div>

{{-- ── LOGOUT CONFIRM MODAL ── --}}
<div id="rp-logout-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:2000; align-items:center; justify-content:center; padding:20px;">
    <div id="rp-logout-modal" style="border-radius:20px; padding:40px 36px; text-align:center; max-width:360px; width:100%; box-shadow:0 24px 60px rgba(0,0,0,0.3); animation:rpModalIn 0.22s cubic-bezier(.4,0,.2,1);">
        <div style="width:72px; height:72px; background:rgba(192,57,43,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:28px; color:#ff7675;">
            <i class="fa-solid fa-right-from-bracket"></i>
        </div>
        <div id="rp-logout-title" style="font-size:20px; font-weight:900; margin-bottom:10px;">Sign Out?</div>
        <div style="font-size:13px; color:#8aaa92; line-height:1.7; margin-bottom:28px;">Are you sure you want to log out of your Rentivator account?</div>
        <div style="display:flex; gap:12px;">
            <button onclick="rpCloseLogout()" id="rp-logout-cancel" style="flex:1; padding:13px; border:1.5px solid; border-radius:10px; font-size:13px; font-weight:800; cursor:pointer; font-family:'Segoe UI',sans-serif;">
                Cancel
            </button>
            <form method="POST" action="{{ route('logout') }}" style="flex:1; margin:0;">
                @csrf
                <button type="submit" style="width:100%; padding:13px; border:none; border-radius:10px; background:#c0392b; color:white; font-size:13px; font-weight:800; cursor:pointer; font-family:'Segoe UI',sans-serif;">
                    Yes, Sign Out
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── STYLES ── --}}
<style>
    @keyframes rpModalIn {
        from { opacity:0; transform:scale(0.94); }
        to   { opacity:1; transform:scale(1); }
    }

    /* Remove ALL text shadows globally */
    *, *::before, *::after { text-shadow: none !important; }

    /* Prevent browser autofill blue/yellow background on ALL inputs */
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0px 1000px transparent inset !important;
        box-shadow: 0 0 0px 1000px transparent inset !important;
        transition: background-color 5000s ease-in-out 0s;
        -webkit-text-fill-color: inherit !important;
    }
    body.rp-dark input:-webkit-autofill,
    body.rp-dark input:-webkit-autofill:hover,
    body.rp-dark input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0px 1000px #0f1f14 inset !important;
        -webkit-text-fill-color: #d4edda !important;
    }

    /* Topbar username/role — dark by default (white topbar pages like tenant/vehicle) */
    .rp-topbar-username { color: #1a3d24 !important; }
    .rp-topbar-role     { color: #8aaa92 !important; }

    /* Dark mode override → white text */
    body.rp-dark .rp-topbar-username { color: #d4edda !important; }
    body.rp-dark .rp-topbar-role     { color: rgba(255,255,255,0.55) !important; }

    /* Pages with dark topbar (admin pages use .topbar-dark class or inline dark bg) */
    .topbar-dark .rp-topbar-username,
    .topbar .rp-topbar-username[data-dark="1"] { color: white !important; }

    /* ════════════════════════════════
       GLOBAL PAGE FIXES (all pages)
       ════════════════════════════════ */

    /* Remove column divider lines in history cards */
    .hk-cell { border-left: none !important; }

    /* BINAGO #1: Alisin ang puting/kulay na background ng search bars sa dark mode */
    body.rp-dark .history-toolbar,
    body.rp-dark .search-bar-wrap,
    body.rp-dark .filter-bar,
    body.rp-dark .search-card,
    body.rp-dark .section-card { background: transparent !important; border-color: #1a3d24 !important; }

    /* Dark mode — page body background bleeds through properly */
    body.rp-dark .content { background: transparent !important; }
    body.rp-dark .main    { background: #0f1f14 !important; }
    .rp-dd-icon {
        background: #e8f5e9;
        color: #1e4d2b;
        transition: background 0.2s, color 0.2s;
    }
    .rp-dd-btn { color: #1a3d24; }
    .rp-dd-label { color: #1a3d24; }
    .rp-dd-btn:hover { background: #f0f7f0 !important; }

    /* ════════════════════════════════
       LIGHT MODE (default)
       ════════════════════════════════ */

    /* Dropdown */
    #rp-dropdown {
        background: #ffffff !important;
        border-color: #e0e0e0 !important;
        box-shadow: 0 12px 40px rgba(0,0,0,0.12) !important;
    }
    #rp-dropdown-header {
        background: #f5f5f5;
        border-color: #e0e0e0;
    }
    #rp-dropdown-name  { color: #1a3d24; font-weight: 800; }
    #rp-dropdown-email { color: #666; }

    /* Edit profile modal — light */
    #rp-edit-modal-card { background: #ffffff; }
    #rp-edit-body { background: #ffffff; color: #1a3d24; }
    .rp-field-label { color: #8aaa92; }
    .rp-section-title { color: #1a3d24; }
    .rp-input { color: #1a3d24; }
    .rp-input-wrap { border-color: #e8f0e8 !important; background: #ffffff; }
    .rp-input-icon { color: #8aaa92; }
    #rp-pw-divider { border-color: #e8f0e8; }

    /* Display modal — light */
    #rp-display-modal { background: #ffffff; }
    #rp-display-body  { background: #ffffff; color: #1a3d24; }
    #rp-dark-row      { border-color: #e8f0e8 !important; }

    /* Logout modal — light */
    #rp-logout-modal { background: #ffffff; }
    #rp-logout-title { color: #1a3d24; }
    #rp-logout-cancel {
        background: #ffffff;
        border-color: #e0e0e0 !important;
        color: #5a7a5e;
    }

    /* Font buttons — light */
    .rp-font-btn {
        background: #ffffff;
        border-color: #e8f0e8;
        color: #8aaa92;
    }
    .rp-font-btn:hover {
        border-color: #6DBE47 !important;
        background: #f0faf0 !important;
        color: #1e4d2b !important;
    }

    /* Toggle track — light (off) */
    #rp-dark-toggle { background: #ccc; }

    /* Toggle hover — both modes */
    #rp-dark-toggle:hover {
        box-shadow: 0 0 0 4px rgba(109,190,71,0.25) !important;
    }
    #rp-dark-toggle:active {
        box-shadow: 0 0 0 6px rgba(109,190,71,0.18) !important;
        transform: scale(0.97);
    }

    /* ════════════════════════════════
       DARK MODE
       ════════════════════════════════ */
    body.rp-dark { background:#0f1f14 !important; color:#d4edda !important; }
    body.rp-dark *, body.rp-dark *::before, body.rp-dark *::after { text-shadow: none !important; }
    body.rp-dark .sidebar  { background:#0d1a10 !important; }
    body.rp-dark .topbar   { background:#162a1c !important; border-color:#1a3d24 !important; }

    /* BINAGO #2: Reservation card at iba pang cards — transparent background sa dark mode (hindi na puti) */
    body.rp-dark .vehicle-card, body.rp-dark .history-card,
    body.rp-dark .stat-card, body.rp-dark .booking-row, body.rp-dark .stat-mini,
    body.rp-dark .feedback-card,
    body.rp-dark .card, body.rp-dark .panel, body.rp-dark .box {
        background: #162a1c !important; border-color:#1a3d24 !important; color:#d4edda !important;
    }
    body.rp-dark .fleet-title, body.rp-dark .section-title, body.rp-dark .hk-value,
    body.rp-dark .card-name, body.rp-dark .rate-value, body.rp-dark .booking-value,
    body.rp-dark .vehicle-model, body.rp-dark .stat-mini-value, body.rp-dark .stat-value,
    body.rp-dark .topbar-brand-text .name, body.rp-dark .user-text .user-name,
    body.rp-dark .stat-label, body.rp-dark .stat-badge { color:#d4edda !important; }

    /* Inputs — transparent bg so parent container color shows through, no dark rectangle */
    body.rp-dark input, body.rp-dark select, body.rp-dark textarea {
        background: transparent !important;
        color: #d4edda !important;
        border-color: #1e4d2b !important;
    }
    /* Autofill override for dark mode — match parent bg, no yellow/blue flash */
    body.rp-dark input:-webkit-autofill,
    body.rp-dark input:-webkit-autofill:hover,
    body.rp-dark input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0px 1000px #0f1f14 inset !important;
        box-shadow: 0 0 0px 1000px #0f1f14 inset !important;
        -webkit-text-fill-color: #d4edda !important;
        caret-color: #d4edda;
    }
    /* Input wrappers (rp-input-wrap, form-input, search-field) get the dark bg */
    body.rp-dark .rp-input-wrap,
    body.rp-dark .form-input,
    body.rp-dark .search-field { background: #0f1f14 !important; border-color: #2d6a3f !important; }

    /* BINAGO #1 (karagdagan): search-card — transparent sa dark mode, hindi dapat may kulay */
    body.rp-dark .search-card { background: transparent !important; border-color: #1a3d24 !important; }

    body.rp-dark table { background:#162a1c !important; }
    body.rp-dark th    { background:#0f1f14 !important; color:#6DBE47 !important; }
    body.rp-dark td    { color:#d4edda !important; border-color:#1e4d2b !important; }
    body.rp-dark tr:hover td { background:#1e4d2b !important; }

    /* Remove ALL white/light backgrounds from wrapper cards in dark mode */
    body.rp-dark .modal,
    body.rp-dark .confirm-box,
    body.rp-dark .toast { background: #162a1c !important; color: #d4edda !important; }
    body.rp-dark .toast-msg { color: #d4edda !important; }

    /* Remove card/section backgrounds that show white in dark mode */
    body.rp-dark [class*="section-card"],
    body.rp-dark [class*="history-toolbar"],
    body.rp-dark [class*="search-card"],
    body.rp-dark [class*="filter-bar"],
    body.rp-dark [class*="booking-row"],
    body.rp-dark [class*="stat-card"] {
        background: transparent !important;
        border-color: #1a3d24 !important;
    }

    /* ── Dropdown — dark ── */
    body.rp-dark #rp-dropdown {
        background: #162a1c !important;
        border-color: #1e4d2b !important;
        box-shadow: 0 12px 40px rgba(0,0,0,0.4) !important;
    }
    body.rp-dark #rp-dropdown-header {
        background: #0f1f14 !important;
        border-color: #1e4d2b !important;
    }
    body.rp-dark #rp-dropdown-name  { color: #d4edda !important; }
    body.rp-dark #rp-dropdown-email { color: #8aaa92 !important; }
    body.rp-dark .rp-dd-btn         { color: #d4edda !important; }
    body.rp-dark .rp-dd-label       { color: #d4edda !important; }
    body.rp-dark .rp-dd-btn:hover   { background: #1e4d2b !important; }
    /* Icon pill in dark mode — visible green-tinted bg */
    body.rp-dark .rp-dd-icon {
        background: #1e4d2b !important;
        color: #6DBE47 !important;
    }

    /* ── Edit modal — dark ── */
    body.rp-dark #rp-edit-modal-card { background: #162a1c !important; }
    body.rp-dark #rp-edit-body       { background: #162a1c !important; color: #d4edda !important; }
    body.rp-dark .rp-field-label     { color: #8aaa92 !important; }
    body.rp-dark .rp-section-title   { color: #d4edda !important; }
    body.rp-dark .rp-input           { color: #d4edda !important; background: transparent !important; }
    body.rp-dark .rp-input-wrap      { border-color: #2d6a3f !important; background: #0f1f14 !important; }
    body.rp-dark .rp-input-icon      { color: #6DBE47 !important; }
    body.rp-dark #rp-pw-divider      { border-color: #1e4d2b !important; }
    /* Make sure inputs inside rp-input-wrap don't get their own bg box */
    body.rp-dark .rp-input-wrap input,
    body.rp-dark .rp-input-wrap input:-webkit-autofill,
    body.rp-dark .rp-input-wrap input:-webkit-autofill:focus {
        background: transparent !important;
        -webkit-box-shadow: 0 0 0px 1000px #0f1f14 inset !important;
        -webkit-text-fill-color: #d4edda !important;
    }

    /* ── Display modal — dark ── */
    body.rp-dark #rp-display-modal { background: #162a1c !important; }
    body.rp-dark #rp-display-body  { background: #162a1c !important; color: #d4edda !important; }
    body.rp-dark #rp-dark-row      { border-color: #1e4d2b !important; }
    body.rp-dark .rp-section-title { color: #d4edda !important; }

    /* Font buttons — dark */
    body.rp-dark .rp-font-btn {
        background: #0f1f14 !important;
        border-color: #2d6a3f !important;
        color: #8aaa92 !important;
    }
    body.rp-dark .rp-font-btn:hover {
        border-color: #6DBE47 !important;
        background: #1e4d2b !important;
        color: #6DBE47 !important;
    }

    /* Toggle track — dark mode (when dark is OFF inside dark mode) */
    body.rp-dark #rp-dark-toggle { background: #0f1f14; }

    /* ── Logout modal — dark ── */
    body.rp-dark #rp-logout-modal  { background: #162a1c !important; }
    body.rp-dark #rp-logout-title  { color: #d4edda !important; }
    body.rp-dark #rp-logout-cancel {
        background: #0f1f14 !important;
        border-color: #2d6a3f !important;
        color: #d4edda !important;
    }

    /* Scrollbars */
    body.rp-dark ::-webkit-scrollbar       { width:6px; }
    body.rp-dark ::-webkit-scrollbar-track { background:#0f1f14; }
    body.rp-dark ::-webkit-scrollbar-thumb { background:#2d6a3f; border-radius:3px; }

    /* ─────────────────────────────────────────────────────────
       FONT SIZE — BINAGO: Small=10px, Medium=12px, Large=14px
       ───────────────────────────────────────────────────────── */
    body.rp-font-small  p, body.rp-font-small  span, body.rp-font-small  div,
    body.rp-font-small  a, body.rp-font-small  label, body.rp-font-small  input,
    body.rp-font-small  td, body.rp-font-small  th, body.rp-font-small  li,
    body.rp-font-small  h1, body.rp-font-small  h2, body.rp-font-small  h3,
    body.rp-font-small  h4, body.rp-font-small  button { font-size:10px !important; }

    body.rp-font-medium p, body.rp-font-medium span, body.rp-font-medium div,
    body.rp-font-medium a, body.rp-font-medium label, body.rp-font-medium input,
    body.rp-font-medium td, body.rp-font-medium th, body.rp-font-medium li,
    body.rp-font-medium h1, body.rp-font-medium h2, body.rp-font-medium h3,
    body.rp-font-medium h4, body.rp-font-medium button { font-size:12px !important; }

    body.rp-font-large  p, body.rp-font-large  span, body.rp-font-large  div,
    body.rp-font-large  a, body.rp-font-large  label, body.rp-font-large  input,
    body.rp-font-large  td, body.rp-font-large  th, body.rp-font-large  li,
    body.rp-font-large  h1, body.rp-font-large  h2, body.rp-font-large  h3,
    body.rp-font-large  h4, body.rp-font-large  button { font-size:14px !important; }

    /* Notification bell */
    .notif-btn, .notif-btn i { font-size:20px !important; }

    /* ── Deduction row (pink background) — transparent sa dark mode ── */
    body.rp-dark tr.deduction-data-row,
    body.rp-dark tr.deduction-data-row td {
        background: transparent !important;
        background-color: transparent !important;
    }

    /* ── Totals row (Total Sales — white background) — transparent sa dark mode ── */
    body.rp-dark tr.totals-row,
    body.rp-dark tr.totals-row td {
        background: transparent !important;
        background-color: transparent !important;
    }

    /* ── Daily/Weekly/Monthly dropdown — visible sa dark mode ── */
    body.rp-dark .dropdown-menu,
    body.rp-dark .period-dropdown,
    body.rp-dark .filter-dropdown,
    body.rp-dark [class*="dropdown"]:not(#rp-dropdown) {
        background: #162a1c !important;
        border: 1px solid #2d6a3f !important;
        color: #d4edda !important;
    }
    body.rp-dark .dropdown-menu li,
    body.rp-dark .dropdown-menu a,
    body.rp-dark .dropdown-menu span,
    body.rp-dark .dropdown-menu div,
    body.rp-dark [class*="dropdown-item"],
    body.rp-dark [class*="dropdown"] li,
    body.rp-dark [class*="dropdown"] a {
        color: #d4edda !important;
        background: transparent !important;
    }
    body.rp-dark .dropdown-menu li:hover,
    body.rp-dark [class*="dropdown-item"]:hover,
    body.rp-dark [class*="dropdown"] li:hover {
        background: #1e4d2b !important;
        color: #6DBE47 !important;
    }
    /* Active font button — stays highlighted even without hover */
    body.rp-dark .rp-font-btn.rp-font-active {
        border-color: #1e4d2b !important;
        background: #1e4d2b !important;
        color: #6DBE47 !important;
    }

    /* ════════════════════════════════════════════════════════
       FIX 1: Dark mode — inline white/fff bg elements → dark green box
       Targets modals like the audit expense summary cards
       ════════════════════════════════════════════════════════ */
    body.rp-dark div[style*="background:#fff"],
    body.rp-dark div[style*="background: #fff"],
    body.rp-dark div[style*="background:#ffffff"],
    body.rp-dark div[style*="background: #ffffff"],
    body.rp-dark div[style*="background:white"],
    body.rp-dark div[style*="background: white"],
    body.rp-dark div[style*="background-color:#fff"],
    body.rp-dark div[style*="background-color: #fff"],
    body.rp-dark div[style*="background-color:#ffffff"],
    body.rp-dark div[style*="background-color: #ffffff"],
    body.rp-dark div[style*="background-color:white"],
    body.rp-dark div[style*="background-color: white"] {
        background: #162a1c !important;
        background-color: #162a1c !important;
        border: 1px solid #2d6a3f !important;
        color: #d4edda !important;
    }

    /* ════════════════════════════════════════════════════════
       FIX 2: Period/filter dropdowns — always visible with bg when open
       (Daily / Weekly / Monthly and similar filter lists)
       ════════════════════════════════════════════════════════ */
    body.rp-dark [class*="view-select"],
    body.rp-dark [class*="period-list"],
    body.rp-dark [class*="view-list"],
    body.rp-dark [class*="filter-list"],
    body.rp-dark [class*="date-options"],
    body.rp-dark [class*="range-options"] {
        background: #162a1c !important;
        border: 1px solid #2d6a3f !important;
        color: #d4edda !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.45) !important;
    }
    body.rp-dark [class*="view-select"] *,
    body.rp-dark [class*="period-list"] *,
    body.rp-dark [class*="view-list"] *,
    body.rp-dark [class*="filter-list"] *,
    body.rp-dark [class*="date-options"] *,
    body.rp-dark [class*="range-options"] * {
        color: #d4edda !important;
    }
    body.rp-dark [class*="view-select"] li:hover,
    body.rp-dark [class*="period-list"] li:hover,
    body.rp-dark [class*="view-list"] li:hover,
    body.rp-dark [class*="filter-list"] li:hover {
        background: #1e4d2b !important;
        color: #6DBE47 !important;
    }

    /* ════════════════════════════════════════════════════════
       FIX 3: Daily / Weekly / Monthly dropdown hover + active indicator
       Works in both light and dark mode
       ════════════════════════════════════════════════════════ */

    /* Light mode — hover */
    [class*="view-option"]:hover,
    [class*="period-item"]:hover,
    [class*="dropdown-item"]:hover,
    .dropdown-menu li:hover,
    .dropdown-menu a:hover,
    [class*="view-list"] li:hover,
    [class*="period-list"] li:hover,
    [class*="filter-list"] li:hover {
        background: #e8f5e9 !important;
        color: #1e4d2b !important;
        cursor: pointer;
    }

    /* Light mode — active/selected */
    [class*="view-option"].active,
    [class*="view-option"][class*="active"],
    [class*="period-item"].active,
    [class*="dropdown-item"].active,
    [class*="view-option"][aria-selected="true"],
    .dropdown-menu li.active,
    .dropdown-menu a.active,
    [class*="view-list"] li.active,
    [class*="period-list"] li.active {
        background: #1e4d2b !important;
        color: #6DBE47 !important;
        font-weight: 800 !important;
    }

    /* Dark mode — hover */
    body.rp-dark [class*="view-option"]:hover,
    body.rp-dark [class*="period-item"]:hover,
    body.rp-dark [class*="dropdown-item"]:hover,
    body.rp-dark .dropdown-menu li:hover,
    body.rp-dark .dropdown-menu a:hover,
    body.rp-dark [class*="view-list"] li:hover,
    body.rp-dark [class*="period-list"] li:hover,
    body.rp-dark [class*="filter-list"] li:hover {
        background: #1e4d2b !important;
        color: #6DBE47 !important;
        cursor: pointer;
    }

    /* Dark mode — active/selected */
    body.rp-dark [class*="view-option"].active,
    body.rp-dark [class*="view-option"][class*="active"],
    body.rp-dark [class*="period-item"].active,
    body.rp-dark [class*="dropdown-item"].active,
    body.rp-dark .dropdown-menu li.active,
    body.rp-dark .dropdown-menu a.active,
    body.rp-dark [class*="view-list"] li.active,
    body.rp-dark [class*="period-list"] li.active {
        background: #2d6a3f !important;
        color: #6DBE47 !important;
        font-weight: 800 !important;
    }
</style>

{{-- ── JAVASCRIPT ── --}}
<script>
    // Persisted (saved) state
    let rpDarkMode   = false;
    let rpFontSize   = 'medium';
    // Preview state — temporary while Display modal is open
    let rpDarkPreview = false;
    let rpFontPreview = 'medium';

    let rpDropOpen = false;
    const RP_CSRF  = '{{ csrf_token() }}';

    // ── Init on page load ──
    (function rpInit() {
        const savedDark = localStorage.getItem('rp_dark_mode');
        const savedFont = localStorage.getItem('rp_font_size') || 'medium';
        rpDarkMode  = savedDark === '1';
        rpFontSize  = savedFont;
        rpApplyDark(rpDarkMode);
        rpApplyFont(rpFontSize);

        // ── Global autocomplete OFF — prevents browser from filling email into search bars ──
        function rpKillAutofill() {
            document.querySelectorAll('input[type="text"], input[type="search"], input:not([type])').forEach(function(inp) {
                const skip = ['rp-name','rp-phone','rp-address','modalName','modalPhone','modalLocation'];
                if (skip.includes(inp.id)) return;
                inp.setAttribute('autocomplete', 'off');
                inp.setAttribute('autocomplete', 'chrome-off');
            });
            document.querySelectorAll('#rp-pw-current, #rp-pw-new, #rp-pw-confirm').forEach(function(inp) {
                inp.setAttribute('autocomplete', 'new-password');
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', rpKillAutofill);
        } else {
            rpKillAutofill();
        }

        new MutationObserver(rpKillAutofill).observe(document.body, { childList: true, subtree: true });
    })();

    // ── Dropdown ──
    function rpToggleDropdown(e) {
        e.stopPropagation();
        const dd = document.getElementById('rp-dropdown');
        const ch = document.getElementById('rp-chevron');
        rpDropOpen = !rpDropOpen;
        dd.style.display   = rpDropOpen ? 'block' : 'none';
        ch.style.transform = rpDropOpen ? 'rotate(180deg)' : '';
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.rp-profile-wrap')) {
            const dd = document.getElementById('rp-dropdown');
            const ch = document.getElementById('rp-chevron');
            if (dd) dd.style.display = 'none';
            if (ch) ch.style.transform = '';
            rpDropOpen = false;
        }
    });

    // ── Edit Profile ──
    function rpOpenEditProfile() {
        document.getElementById('rp-dropdown').style.display = 'none';
        rpDropOpen = false;
        ['rp-pw-current','rp-pw-new','rp-pw-confirm'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.value = ''; el.disabled = (id !== 'rp-pw-current'); }
        });
        rpPwCurrentVerified = false;
        const status = document.getElementById('rp-pw-current-status');
        if (status) status.style.display = 'none';
        const wrapNew  = document.getElementById('rp-wrap-new');
        const wrapConf = document.getElementById('rp-wrap-confirm');
        const inNew    = document.getElementById('rp-pw-new');
        const inConf   = document.getElementById('rp-pw-confirm');
        if (wrapNew && wrapConf && inNew && inConf) {
            rpLockPwFields(wrapNew, wrapConf, inNew, inConf);
        }
        document.getElementById('rp-pw-msg').style.display = 'none';
        document.getElementById('rp-edit-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function rpCloseEdit() {
        document.getElementById('rp-edit-overlay').style.display = 'none';
        document.getElementById('rp-save-msg').style.display = 'none';
        document.getElementById('rp-pw-msg').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.getElementById('rp-edit-overlay').addEventListener('click', function(e) {
        if (e.target === this) rpCloseEdit();
    });

    function rpPreviewPhoto(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const av  = document.getElementById('rp-modal-avatar');
            const ini = document.getElementById('rp-modal-initials');
            if (av)  { av.src = e.target.result; av.style.display = 'block'; }
            if (ini) ini.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }

    function rpSaveProfile() {
        const btn     = document.getElementById('rp-save-btn');
        const msg     = document.getElementById('rp-save-msg');
        const name    = document.getElementById('rp-name').value.trim();
        const phone   = document.getElementById('rp-phone').value.trim();
        const address = document.getElementById('rp-address').value.trim();
        const photo   = document.getElementById('rp-photo-input').files[0];
        if (!name) { rpShowMsg(msg, 'Name is required.', false); return; }
        btn.disabled  = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
        const fd = new FormData();
        fd.append('_token', RP_CSRF);
        fd.append('name', name);
        fd.append('phone_number', phone);
        fd.append('address', address);
        if (photo) fd.append('profile_picture', photo);
        fetch('/profile/update', { method:'POST', body:fd })
        .then(r => r.json())
        .then(data => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
            if (data.success) {
                rpShowMsg(msg, '✓ Profile saved!', true);
                const u = data.user;
                ['rp-topbar-name','rp-sidebar-name'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = u.name;
                });
                const dn = document.getElementById('rp-dropdown-name');
                if (dn) dn.textContent = u.name;
                if (u.profile_picture) {
                    ['rp-topbar-avatar','rp-sidebar-avatar','rp-modal-avatar'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) { el.src = u.profile_picture; el.style.display = 'block'; }
                    });
                    ['rp-topbar-initials','rp-sidebar-initials','rp-modal-initials'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.style.display = 'none';
                    });
                }
                setTimeout(rpCloseEdit, 1500);
            } else {
                rpShowMsg(msg, data.message || 'Failed to save.', false);
            }
        })
        .catch(() => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
            rpShowMsg(msg, 'Network error. Try again.', false);
        });
    }

    function rpShowMsg(el, text, success) {
        el.textContent      = text;
        el.style.display    = 'block';
        el.style.background = success ? '#e8f5e9' : '#fde8e8';
        el.style.color      = success ? '#1e7e34'  : '#c0392b';
    }

    // ── Verify current password in real-time → unlock new/confirm fields ──
    let rpPwVerifyTimer = null;
    let rpPwCurrentVerified = false;

    function rpVerifyCurrentPw(val) {
        const status    = document.getElementById('rp-pw-current-status');
        const wrapNew   = document.getElementById('rp-wrap-new');
        const wrapConf  = document.getElementById('rp-wrap-confirm');
        const inNew     = document.getElementById('rp-pw-new');
        const inConf    = document.getElementById('rp-pw-confirm');

        if (!val) {
            rpPwCurrentVerified = false;
            status.style.display = 'none';
            rpLockPwFields(wrapNew, wrapConf, inNew, inConf);
            return;
        }

        clearTimeout(rpPwVerifyTimer);
        status.style.display  = 'block';
        status.style.background = '#f0f7f0';
        status.style.color    = '#8aaa92';
        status.textContent    = 'Checking...';

        rpPwVerifyTimer = setTimeout(() => {
            fetch('/profile/verify-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': RP_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ current_password: val })
            })
            .then(r => r.json())
            .then(data => {
                if (data.valid) {
                    rpPwCurrentVerified = true;
                    status.style.background = '#e8f5e9';
                    status.style.color      = '#1e7e34';
                    status.textContent      = '✓ Password verified — you can now set a new password';
                    wrapNew.style.opacity        = '1';
                    wrapNew.style.pointerEvents  = 'auto';
                    wrapConf.style.opacity       = '1';
                    wrapConf.style.pointerEvents = 'auto';
                    inNew.disabled  = false;
                    inConf.disabled = false;
                    inNew.placeholder  = 'Min. 8 characters';
                    inConf.placeholder = 'Re-enter new password';
                } else {
                    rpPwCurrentVerified = false;
                    status.style.background = '#fde8e8';
                    status.style.color      = '#c0392b';
                    status.textContent      = '✗ Incorrect password';
                    rpLockPwFields(wrapNew, wrapConf, inNew, inConf);
                }
            })
            .catch(() => {
                status.style.background = '#fde8e8';
                status.style.color      = '#c0392b';
                status.textContent      = 'Network error. Try again.';
            });
        }, 600);
    }

    function rpLockPwFields(wrapNew, wrapConf, inNew, inConf) {
        wrapNew.style.opacity        = '0.45';
        wrapNew.style.pointerEvents  = 'none';
        wrapConf.style.opacity       = '0.45';
        wrapConf.style.pointerEvents = 'none';
        inNew.disabled  = true;
        inConf.disabled = true;
        inNew.value     = '';
        inConf.value    = '';
        inNew.placeholder  = 'Verify current password first';
        inConf.placeholder = 'Verify current password first';
    }

    // ── Password Toggle (show/hide) ──
    function rpTogglePw(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (!input) return;
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        if (icon) icon.className = isHidden ? 'fa-regular fa-eye-slash rp-input-icon' : 'fa-regular fa-eye rp-input-icon';
    }

    // ── Change Password ──
    function rpChangePassword() {
        const btn        = document.getElementById('rp-pw-btn');
        const msg        = document.getElementById('rp-pw-msg');
        const current    = document.getElementById('rp-pw-current').value;
        const newPw      = document.getElementById('rp-pw-new').value;
        const confirmPw  = document.getElementById('rp-pw-confirm').value;

        if (!rpPwCurrentVerified) { rpShowMsg(msg, 'Please verify your current password first.', false); return; }
        if (!current)               { rpShowMsg(msg, 'Please enter your current password.', false); return; }
        if (!newPw || newPw.length < 8) { rpShowMsg(msg, 'New password must be at least 8 characters.', false); return; }
        if (newPw !== confirmPw)    { rpShowMsg(msg, 'New passwords do not match.', false); return; }
        if (current === newPw)      { rpShowMsg(msg, 'New password must be different from current.', false); return; }

        btn.disabled  = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';

        fetch('/profile/change-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': RP_CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                current_password:      current,
                new_password:          newPw,
                new_password_confirmation: confirmPw,
            })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Update Password';
            if (data.success) {
                rpShowMsg(msg, '✓ Password updated successfully!', true);
                ['rp-pw-current','rp-pw-new','rp-pw-confirm'].forEach(id => {
                    document.getElementById(id).value = '';
                });
            } else {
                rpShowMsg(msg, data.message || 'Failed to update password.', false);
            }
        })
        .catch(() => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Update Password';
            rpShowMsg(msg, 'Network error. Try again.', false);
        });
    }

    // ── Display & Accessibility ──
    function rpOpenDisplay() {
        document.getElementById('rp-dropdown').style.display = 'none';
        rpDropOpen = false;
        rpDarkPreview = rpDarkMode;
        rpFontPreview = rpFontSize;
        rpSyncDarkToggle(rpDarkPreview);
        rpSyncFontButtons(rpFontPreview);
        document.getElementById('rp-display-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function rpCloseDisplay(saved) {
        if (!saved) {
            rpApplyDark(rpDarkMode);
            rpApplyFont(rpFontSize);
        }
        document.getElementById('rp-display-overlay').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.getElementById('rp-display-overlay').addEventListener('click', function(e) {
        if (e.target === this) rpCloseDisplay(false);
    });

    function rpToggleDarkMode() {
        rpDarkPreview = !rpDarkPreview;
        rpApplyDark(rpDarkPreview);
        rpSyncDarkToggle(rpDarkPreview);
        rpSyncFontButtons(rpFontPreview);
    }
    function rpSetFontSize(size) {
        rpFontPreview = size;
        rpApplyFont(size);
        rpSyncFontButtons(size);
        document.querySelectorAll('.rp-font-btn').forEach(b => b.classList.remove('rp-font-active'));
        const activeBtn = document.getElementById('rp-font-' + size);
        if (activeBtn) activeBtn.classList.add('rp-font-active');
    }

    function rpDarkToggleHover(on) {
        const toggle = document.getElementById('rp-dark-toggle');
        if (!toggle) return;
        toggle.style.boxShadow = on ? '0 0 0 4px rgba(109,190,71,0.3)' : '';
    }

    function rpApplyDark(on) {
        document.body.classList.toggle('rp-dark', on);
    }
    function rpApplyFont(size) {
        document.body.classList.remove('rp-font-small','rp-font-medium','rp-font-large');
        document.body.classList.add('rp-font-' + size);
    }
    function rpSyncDarkToggle(on) {
        const toggle = document.getElementById('rp-dark-toggle');
        const knob   = document.getElementById('rp-dark-knob');
        if (toggle) toggle.style.background = on ? '#2d6a3f' : (document.body.classList.contains('rp-dark') ? '#0f1f14' : '#ccc');
        if (knob)   knob.style.transform    = on ? 'translateX(24px)' : 'translateX(0)';
    }
    function rpSyncFontButtons(active) {
        const dark = document.body.classList.contains('rp-dark');
        ['small','medium','large'].forEach(s => {
            const btn = document.getElementById('rp-font-' + s);
            if (!btn) return;
            if (s === active) {
                btn.style.borderColor = '#1e4d2b';
                btn.style.background  = dark ? '#1e4d2b' : '#e8f5e9';
                btn.style.color       = dark ? '#6DBE47' : '#1e4d2b';
            } else {
                btn.style.borderColor = dark ? '#2d6a3f' : '#e8f0e8';
                btn.style.background  = dark ? '#0f1f14' : '#ffffff';
                btn.style.color       = '#8aaa92';
            }
        });
    }

    function rpSavePreferences() {
        rpDarkMode = rpDarkPreview;
        rpFontSize = rpFontPreview;
        localStorage.setItem('rp_dark_mode', rpDarkMode ? '1' : '0');
        localStorage.setItem('rp_font_size', rpFontSize);
        fetch('/profile/preferences', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': RP_CSRF },
            body: JSON.stringify({ dark_mode: rpDarkMode ? '1' : '0', font_size: rpFontSize })
        }).catch(() => {});
        rpCloseDisplay(true);
    }

    // ── Logout ──
    function rpOpenLogout() {
        const dd = document.getElementById('rp-dropdown');
        if (dd) dd.style.display = 'none';
        rpDropOpen = false;
        document.getElementById('rp-logout-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function rpCloseLogout() {
        document.getElementById('rp-logout-overlay').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.getElementById('rp-logout-overlay').addEventListener('click', function(e) {
        if (e.target === this) rpCloseLogout();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;
        rpCloseEdit();
        rpCloseDisplay(false);
        rpCloseLogout();
        const dd = document.getElementById('rp-dropdown');
        if (dd) dd.style.display = 'none';
        rpDropOpen = false;
    });

    // ── DARK MODE: Fix white backgrounds on elements inside modals/overlays/dropdowns ──
    function rpKillWhiteBackgrounds() {
        if (!document.body.classList.contains('rp-dark')) return;
        document.querySelectorAll('*').forEach(function(el) {
            const cs = window.getComputedStyle(el);
            const bg = cs.backgroundColor;
            if (
                bg === 'rgb(255, 255, 255)' ||
                bg === 'rgba(255, 255, 255, 1)' ||
                bg === 'rgb(248, 248, 248)' ||
                bg === 'rgb(245, 245, 245)' ||
                bg === 'rgb(250, 250, 250)' ||
                bg === 'rgb(252, 252, 252)'
            ) {
                // Skip our own rp- modals (they manage their own dark bg via CSS)
                const skipIds = ['rp-edit-modal-card','rp-edit-body','rp-display-modal',
                                 'rp-display-body','rp-logout-modal','rp-dropdown',
                                 'rp-dropdown-header'];
                if (skipIds.includes(el.id)) return;
                if (el.closest('#rp-edit-overlay, #rp-display-overlay, #rp-logout-overlay, #rp-dropdown')) return;

                // Elements inside other modals, overlays, popups, or dropdowns
                // → give them a visible dark green box (not transparent) so content stays readable
                if (el.closest('[class*="modal"], [class*="overlay"], [class*="popup"], [class*="dropdown"], [class*="audit"], [class*="expense"], [class*="dialog"], [class*="panel"]')) {
                    el.style.setProperty('background', '#162a1c', 'important');
                    el.style.setProperty('background-color', '#162a1c', 'important');
                    el.style.setProperty('color', '#d4edda', 'important');
                } else {
                    // Plain layout wrappers → transparent so the body bg shows through
                    el.style.setProperty('background', 'transparent', 'important');
                    el.style.setProperty('background-color', 'transparent', 'important');
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', rpKillWhiteBackgrounds);
    const _rpApplyDark = rpApplyDark;
    rpApplyDark = function(on) {
        _rpApplyDark(on);
        if (on) setTimeout(rpKillWhiteBackgrounds, 50);
    };
    setTimeout(rpKillWhiteBackgrounds, 100);

    // ── Period dropdown hover + active indicator (Daily / Weekly / Monthly) ──
    // JS event delegation — gumagana kahit anong class name ang gamit sa ibang blade files
    (function rpPeriodDropdownInteraction() {
        const PERIOD_SELECTOR =
            '.dropdown-menu, [class*="period"], [class*="view-list"], ' +
            '[class*="filter-list"], [class*="date-drop"], [class*="range-drop"], ' +
            '[class*="view-drop"], [class*="view-option-wrap"]';

        const ITEM_SELECTOR =
            'li, a, [class*="view-option"], [class*="period-item"], [class*="dropdown-item"]';

        function getPeriodItem(e) {
            const item = e.target.closest(ITEM_SELECTOR);
            if (!item) return null;
            const parent = item.closest(PERIOD_SELECTOR);
            if (!parent || item.closest('#rp-dropdown')) return null;
            return item;
        }

        // HOVER IN — highlight on mouseover
        document.addEventListener('mouseover', function(e) {
            const item = getPeriodItem(e);
            if (!item || item.dataset.rpActive === '1') return;
            const dark = document.body.classList.contains('rp-dark');
            item.style.setProperty('background',  dark ? '#1e4d2b' : '#e8f5e9', 'important');
            item.style.setProperty('color',       dark ? '#6DBE47' : '#1e4d2b', 'important');
            item.style.setProperty('transition',  'background 0.15s, color 0.15s', 'important');
            item.style.cursor = 'pointer';
        });

        // HOVER OUT — reset unless it's the active item
        document.addEventListener('mouseout', function(e) {
            const item = getPeriodItem(e);
            if (!item || item.dataset.rpActive === '1') return;
            item.style.removeProperty('background');
            item.style.removeProperty('color');
        });

        // CLICK — mark clicked as active, clear siblings
        document.addEventListener('click', function(e) {
            const item = getPeriodItem(e);
            if (!item) return;

            // Reset all siblings in the same dropdown
            const container = item.closest(PERIOD_SELECTOR);
            if (container) {
                container.querySelectorAll(ITEM_SELECTOR).forEach(function(sib) {
                    sib.dataset.rpActive = '0';
                    sib.style.removeProperty('background');
                    sib.style.removeProperty('color');
                    sib.style.removeProperty('font-weight');
                });
            }

            // Apply active style to clicked item
            const dark = document.body.classList.contains('rp-dark');
            item.dataset.rpActive = '1';
            item.style.setProperty('background',  dark ? '#2d6a3f' : '#1e4d2b', 'important');
            item.style.setProperty('color',       '#6DBE47',                     'important');
            item.style.setProperty('font-weight', '800',                         'important');
        });
    })();
</script>