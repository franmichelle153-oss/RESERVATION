{{--
    NOTIFICATION BELL COMPONENT
    I-include ONCE bago mag-</body>:
        @include('components.notification-bell')
--}}

<div class="rp-notif-wrap-panel">
    <div id="rp-notif-dropdown"
         style="display:none; position:fixed; top:62px; right:16px; width:360px;
                max-width:calc(100vw - 24px); border-radius:16px; overflow:hidden;
                z-index:1500; box-shadow:0 20px 60px rgba(0,0,0,0.25);"
         class="rp-notif-panel">

        {{-- Header --}}
        <div id="rp-notif-header"
             style="padding:14px 18px 12px; border-bottom:1px solid;
                    display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="font-size:15px; font-weight:900;" class="rp-notif-head-title">Notifications</span>
                <span id="rp-notif-unread-pill"
                      style="display:none; background:#e74c3c; color:white; font-size:10px;
                             font-weight:800; padding:2px 8px; border-radius:20px; line-height:1.6;">
                    0 new
                </span>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
                <button onclick="RpNotif.markAllRead()" id="rp-notif-read-all-btn"
                        title="Mark all as read"
                        style="background:none; border:none; cursor:pointer; padding:6px 8px;
                               border-radius:8px; transition:background 0.15s; line-height:1;"
                        class="rp-notif-ctrl-btn">
                    <i class="fa-solid fa-check-double" style="font-size:13px;"></i>
                </button>
                <button onclick="RpNotif.toggleSelectMode()" id="rp-notif-delete-toggle-btn"
                        title="Delete notifications"
                        style="background:none; border:none; cursor:pointer; padding:6px 8px;
                               border-radius:8px; transition:background 0.15s; line-height:1;"
                        class="rp-notif-ctrl-btn">
                    <i class="fa-regular fa-trash-can" style="font-size:13px;" id="rp-notif-trash-icon"></i>
                </button>
            </div>
        </div>

        {{-- Select-mode action bar --}}
        <div id="rp-notif-select-bar"
             style="display:none; padding:10px 18px; border-bottom:1px solid;
                    align-items:center; justify-content:space-between; gap:10px;"
             class="rp-notif-select-bar-wrap">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;
                          font-size:12px; font-weight:700;" class="rp-notif-sel-all-label">
                <input type="checkbox" id="rp-notif-select-all"
                       onchange="RpNotif.selectAll(this.checked)"
                       style="width:15px; height:15px; accent-color:#6DBE47; cursor:pointer; flex-shrink:0;">
                Select all
            </label>
            <div style="display:flex; align-items:center; gap:8px;">
                <button onclick="RpNotif.toggleSelectMode()"
                        style="background:none; border:1.5px solid; border-radius:8px;
                               padding:5px 12px; font-size:11px; font-weight:700; cursor:pointer;"
                        class="rp-notif-cancel-sel-btn">
                    Cancel
                </button>
                <button onclick="RpNotif.deleteSelected()"
                        style="background:#c0392b; color:white; border:none; border-radius:8px;
                               padding:6px 14px; font-size:11px; font-weight:800; cursor:pointer;
                               display:flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-trash" style="font-size:10px;"></i> Delete
                </button>
            </div>
        </div>

        {{-- List --}}
        <div id="rp-notif-list" style="overflow-y:auto; max-height:420px;">
            <div id="rp-notif-loading"
                 style="padding:48px 20px; text-align:center; color:#8aaa92; font-size:13px;">
                <i class="fa-solid fa-spinner fa-spin"
                   style="font-size:22px; display:block; margin-bottom:10px;"></i>
                Loading…
            </div>
            <div id="rp-notif-empty"
                 style="display:none; padding:52px 20px; text-align:center;">
                <i class="fa-regular fa-bell-slash"
                   style="font-size:36px; display:block; margin-bottom:12px; color:#8aaa92;"></i>
                <div style="font-size:14px; font-weight:800; margin-bottom:4px;"
                     class="rp-notif-empty-title">You're all caught up!</div>
                <div style="font-size:12px; color:#8aaa92;">No notifications yet.</div>
            </div>
        </div>
    </div>
</div>

<style>
    .rp-notif-panel            { background:#ffffff; border:1px solid #e0e0e0; }
    #rp-notif-header           { border-color:#e8f0e8 !important; background:#ffffff; }
    .rp-notif-head-title       { color:#1a3d24; }
    .rp-notif-ctrl-btn         { color:#8aaa92; }
    .rp-notif-ctrl-btn:hover   { background:#f0f7f0 !important; color:#1e4d2b !important; }
    .rp-notif-select-bar-wrap  { border-color:#e8f0e8 !important; background:#f8fdf8; }
    .rp-notif-sel-all-label    { color:#1a3d24; }
    .rp-notif-cancel-sel-btn   { border-color:#ddd !important; color:#5a7a5e; background:#fff; }
    .rp-notif-empty-title      { color:#1a3d24; }
    #rp-notif-list::-webkit-scrollbar       { width:5px; }
    #rp-notif-list::-webkit-scrollbar-track { background:#f5f5f5; }
    #rp-notif-list::-webkit-scrollbar-thumb { background:#c8d8c8; border-radius:3px; }

    body.rp-dark .rp-notif-panel           { background:#162a1c !important; border-color:#1e4d2b !important; }
    body.rp-dark #rp-notif-header          { background:#162a1c !important; border-color:#1e4d2b !important; }
    body.rp-dark .rp-notif-head-title      { color:#d4edda !important; }
    body.rp-dark .rp-notif-ctrl-btn        { color:#8aaa92 !important; }
    body.rp-dark .rp-notif-ctrl-btn:hover  { background:#1e4d2b !important; color:#6DBE47 !important; }
    body.rp-dark .rp-notif-select-bar-wrap { background:#0f1f14 !important; border-color:#1e4d2b !important; }
    body.rp-dark .rp-notif-sel-all-label   { color:#d4edda !important; }
    body.rp-dark .rp-notif-cancel-sel-btn  { border-color:#2d6a3f !important; color:#d4edda !important; background:#0f1f14 !important; }
    body.rp-dark .rp-notif-empty-title     { color:#d4edda !important; }
    body.rp-dark #rp-notif-list::-webkit-scrollbar-track { background:#0f1f14; }
    body.rp-dark #rp-notif-list::-webkit-scrollbar-thumb { background:#2d6a3f; }
    body.rp-dark #rp-notif-loading         { color:#8aaa92 !important; }

    .rp-notif-item:hover { cursor:pointer; }
    body.rp-dark .rp-notif-item-title { color:#d4edda !important; }
    .rp-notif-item-title              { color:#1a3d24; }
    #rp-notif-delete-toggle-btn.rp-trash-active { background:#fde8e8 !important; color:#c0392b !important; }
    body.rp-dark #rp-notif-delete-toggle-btn.rp-trash-active { background:rgba(192,57,43,0.18) !important; color:#ff7675 !important; }

    /* Custom Modal */
.rp-confirm-overlay {
    position:fixed; inset:0; background:rgba(0,0,0,0.5);
    z-index:9999; display:flex; align-items:center; justify-content:center;
}
.rp-confirm-box {
    background:#fff; border-radius:16px; padding:32px 28px;
    min-width:300px; max-width:360px; text-align:center;
    box-shadow:0 20px 60px rgba(0,0,0,0.25);
}
.rp-confirm-icon {
    width:56px; height:56px; border-radius:50%;
    background:#fde8e8; display:flex; align-items:center;
    justify-content:center; margin:0 auto 16px;
}
.rp-confirm-icon i { font-size:24px; color:#e74c3c; }
.rp-confirm-title {
    font-size:16px; font-weight:900; color:#1a3d24; margin-bottom:8px;
}
.rp-confirm-msg {
    font-size:13px; color:#8aaa92; margin-bottom:24px; line-height:1.6;
}
.rp-confirm-btns {
    display:flex; gap:10px; justify-content:center;
}
.rp-confirm-btns button {
    padding:10px 24px; border-radius:10px; font-size:13px;
    font-weight:800; cursor:pointer; border:none; transition:all 0.2s;
}
.rp-confirm-cancel {
    background:#f0f7f0; color:#1e4d2b;
}
.rp-confirm-cancel:hover { background:#e0f0e0; }
.rp-confirm-delete {
    background:#e74c3c; color:white;
}
.rp-confirm-delete:hover { background:#c0392b; }

body.rp-dark .rp-confirm-box { background:#162a1c; }
body.rp-dark .rp-confirm-title { color:#d4edda; }
body.rp-dark .rp-confirm-cancel { background:#1e4d2b; color:#d4edda; }
</style>

<script>
const RN_CSRF = '{{ csrf_token() }}';

function rpConfirm(message, onConfirm) {
    const overlay = document.createElement('div');
    overlay.className = 'rp-confirm-overlay';
    overlay.innerHTML = `
        <div class="rp-confirm-box">
            <div class="rp-confirm-icon"><i class="fa-solid fa-trash"></i></div>
            <div class="rp-confirm-title">Are you sure?</div>
            <div class="rp-confirm-msg">${message}</div>
            <div class="rp-confirm-btns">
                <button class="rp-confirm-cancel" onclick="this.closest('.rp-confirm-overlay').remove()">Cancel</button>
                <button class="rp-confirm-delete" id="rp-confirm-yes">Delete</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    overlay.querySelector('#rp-confirm-yes').onclick = () => {
        overlay.remove();
        onConfirm();
    };
}
const RpNotif = {
    data:        [],
    unreadCount: 0,
    selectMode:  false,
    _polling:    null,

    init() {
        this.fetchCount();
        this._polling = setInterval(() => this.fetchCount(), 20000);
    },

    async fetchCount() {
        try {
            const r = await fetch('/notifications/count', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': RN_CSRF }
            });
            if (!r.ok) return;
            const d = await r.json();
            this.unreadCount = d.unread_count;
            this._updateBadge(d.unread_count);
        } catch (_) {}
    },

    async fetchAll() {
        const loading = document.getElementById('rp-notif-loading');
        const empty   = document.getElementById('rp-notif-empty');
        document.querySelectorAll('.rp-notif-item').forEach(el => el.remove());
        if (loading) loading.style.display = 'block';
        if (empty)   empty.style.display   = 'none';

        try {
            const r = await fetch('/notifications', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': RN_CSRF }
            });
            if (!r.ok) throw new Error('fetch failed');
            const d = await r.json();
            this.data        = d.notifications;
            this.unreadCount = d.unread_count;
            this._updateBadge(d.unread_count);
            if (loading) loading.style.display = 'none';
            this._render();
        } catch (e) {
            if (loading) loading.style.display = 'none';
        }
    },

    _render() {
        const list  = document.getElementById('rp-notif-list');
        const empty = document.getElementById('rp-notif-empty');
        if (!list) return;
        if (!this.data.length) {
            if (empty) empty.style.display = 'block';
            return;
        }
        if (empty) empty.style.display = 'none';
        this.data.forEach(n => list.insertAdjacentHTML('beforeend', this._itemHtml(n)));
    },

    _itemHtml(n) {
        const dark       = document.body.classList.contains('rp-dark');
        const unread     = !n.read;
        const unreadBg   = dark ? 'rgba(109,190,71,0.07)' : 'rgba(109,190,71,0.05)';
        const bg         = unread ? unreadBg : 'transparent';
        const leftBorder = unread ? '3px solid #6DBE47' : '3px solid transparent';
        const payloadJson = JSON.stringify(n.data || {}).replace(/'/g, '&#39;');

        return `
        <div class="rp-notif-item"
             data-id="${n.id}"
             data-read="${n.read ? '1' : '0'}"
             data-type="${n.type}"
             data-payload='${payloadJson}'
             style="display:flex; align-items:flex-start; gap:12px; padding:12px 16px 12px 13px;
                    border-bottom:1px solid ${dark ? '#1e4d2b' : '#f0f0f0'};
                    position:relative; background:${bg}; border-left:${leftBorder};
                    transition:background 0.15s; cursor:pointer;"
             onmouseenter="this.style.background='${dark ? 'rgba(30,77,43,0.45)' : '#f0faf0'}';
                           const d=this.querySelector('.rp-notif-del'); if(d)d.style.opacity='1';"
             onmouseleave="this.style.background=this.dataset.read==='1'?'transparent':'${bg}';
                           const d=this.querySelector('.rp-notif-del'); if(d)d.style.opacity='0';"
             onclick="RpNotif.clickItem(${n.id}, this)">

            <input type="checkbox" class="rp-notif-chk" data-id="${n.id}"
                   style="display:none; width:15px; height:15px; accent-color:#6DBE47;
                          cursor:pointer; flex-shrink:0; margin-top:4px;"
                   onclick="event.stopPropagation()">

            <div style="width:38px; height:38px; border-radius:10px; flex-shrink:0;
                        display:flex; align-items:center; justify-content:center;
                        background:${n.color}22;">
                <i class="fa-solid ${n.icon}" style="font-size:15px; color:${n.color};"></i>
            </div>

            <div style="flex:1; min-width:0;">
                <div class="rp-notif-item-title"
                     style="font-size:13px; font-weight:${unread ? '800' : '600'};
                            margin-bottom:3px; line-height:1.3; padding-right:24px;">${n.title}</div>
                <div style="font-size:12px; color:#8aaa92; line-height:1.55;
                            margin-bottom:4px; word-break:break-word;">${n.message}</div>
                <div style="font-size:10px; color:#8aaa92; font-weight:600; display:flex; align-items:center; gap:5px;">
                    <i class="fa-regular fa-clock" style="font-size:9px;"></i> ${n.time_ago}
                </div>
            </div>

            ${unread ? `<div class="rp-notif-dot" style="width:8px;height:8px;background:#6DBE47;
                             border-radius:50%;flex-shrink:0;margin-top:5px;"></div>` : ''}

            <button class="rp-notif-del"
                    onclick="event.stopPropagation(); RpNotif.deleteSingle(${n.id}, this.closest('.rp-notif-item'))"
                    style="position:absolute; top:8px; right:8px; background:rgba(192,57,43,0.1);
                           border:none; cursor:pointer; color:#e74c3c; opacity:0;
                           transition:opacity 0.15s; padding:4px 6px; border-radius:6px;
                           font-size:11px; line-height:1; display:flex; align-items:center;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>`;
    },

    _updateBadge(count) {
        const badge = document.getElementById('rp-notif-badge');
        const pill  = document.getElementById('rp-notif-unread-pill');
        if (badge) {
            badge.style.display = count > 0 ? 'flex' : 'none';
            badge.textContent   = count > 99 ? '99+' : count;
        }
        if (pill) {
            pill.style.display = count > 0 ? 'inline' : 'none';
            pill.textContent   = count + ' new';
        }
    },

    async clickItem(id, el) {
        if (this.selectMode) {
            const chk = el.querySelector('.rp-notif-chk');
            if (chk) chk.checked = !chk.checked;
            return;
        }

        if (el.dataset.read === '0') {
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': RN_CSRF }
                });
                el.dataset.read = '1';
                el.style.borderLeft = '3px solid transparent';
                const titleEl = el.querySelector('.rp-notif-item-title');
                if (titleEl) titleEl.style.fontWeight = '600';
                el.querySelectorAll('.rp-notif-dot').forEach(d => d.remove());
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this._updateBadge(this.unreadCount);
            } catch (_) {}
        }

        const type = el.dataset.type || '';
        let payload = {};
        try { payload = JSON.parse(el.dataset.payload || '{}'); } catch (_) {}

        const dd = document.getElementById('rp-notif-dropdown');
        if (dd) dd.style.display = 'none';

        switch (type) {
            case 'booking_placed':
                window.location.href = '/admin/bookings';
                break;
            case 'booking_approved':
            case 'booking_cancelled':
                case 'booking_rescheduled':
                window.location.href = '/tenant/reservation';
                break;
          case 'booking_completed':
    // Buksan ang feedback modal
    if (payload.reservation_id) {
        window.location.href = '/tenant/history';
        sessionStorage.setItem('open_feedback_reservation', payload.reservation_id);
    } else {
        window.location.href = '/tenant/history';
    }
    break;
case 'audit_added':
    window.location.href = '/tenant/history';
    break;
        }
    },

    async markAllRead() {
        try {
            await fetch('/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': RN_CSRF }
            });
            this.unreadCount = 0;
            this._updateBadge(0);
            await this.fetchAll();
        } catch (_) {}
    },

  async deleteSingle(id, el) {
    if (!el) return;
    rpConfirm('This notification will be permanently deleted.', async () => {
        try {
            await fetch(`/notifications/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': RN_CSRF }
            });
            this._animateRemove(el, id);
        } catch (_) {}
    });
},

    _animateRemove(el, id) {
        const h = el.offsetHeight;
        el.style.transition  = 'opacity 0.18s, max-height 0.28s ease, padding 0.28s ease';
        el.style.overflow    = 'hidden';
        el.style.maxHeight   = h + 'px';
        el.style.opacity     = '0';
        setTimeout(() => { el.style.maxHeight = '0'; el.style.padding = '0'; }, 20);
        setTimeout(() => {
            el.remove();
            this.data = this.data.filter(n => n.id != id);
            if (el.dataset.read === '0') {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this._updateBadge(this.unreadCount);
            }
            if (!document.querySelectorAll('.rp-notif-item').length) {
                const empty = document.getElementById('rp-notif-empty');
                if (empty) empty.style.display = 'block';
            }
        }, 300);
    },

   async deleteSelected() {
    const checked = [...document.querySelectorAll('.rp-notif-chk:checked')];
    const ids = checked.map(c => parseInt(c.dataset.id));
    if (!ids.length) {
        rpConfirm('Please select at least one notification first.', () => {});
        return;
    }
    rpConfirm('Selected notifications will be permanently deleted.', async () => {
        try {
            await fetch('/notifications', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': RN_CSRF },
                body: JSON.stringify({ ids })
            });
            this.toggleSelectMode(false);
            await this.fetchAll();
        } catch (_) {}
    });
},

    toggleSelectMode(force) {
        this.selectMode = (force !== undefined) ? force : !this.selectMode;
        const bar          = document.getElementById('rp-notif-select-bar');
        const trashBtn     = document.getElementById('rp-notif-delete-toggle-btn');
        const selectAllChk = document.getElementById('rp-notif-select-all');

        document.querySelectorAll('.rp-notif-chk').forEach(c => {
            c.style.display = this.selectMode ? 'block' : 'none';
            c.checked = false;
        });
        document.querySelectorAll('.rp-notif-del').forEach(d => {
            d.style.display = this.selectMode ? 'none' : '';
        });

        if (bar)          bar.style.display = this.selectMode ? 'flex' : 'none';
        if (trashBtn)     trashBtn.classList.toggle('rp-trash-active', this.selectMode);
        if (selectAllChk) selectAllChk.checked = false;
    },

    selectAll(checked) {
        document.querySelectorAll('.rp-notif-chk').forEach(c => c.checked = checked);
    },
};

function rpToggleNotif(e) {
    e.stopPropagation();
    const dd = document.getElementById('rp-notif-dropdown');
    if (!dd) return;
    const isOpen = dd.style.display !== 'none';
    dd.style.display = isOpen ? 'none' : 'block';
    if (!isOpen) {
        RpNotif.toggleSelectMode(false);
        RpNotif.fetchAll();
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.rp-notif-wrap') && !e.target.closest('.rp-notif-wrap-panel')) {
        const dd = document.getElementById('rp-notif-dropdown');
        if (dd) dd.style.display = 'none';
        RpNotif.toggleSelectMode(false);
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    const dd = document.getElementById('rp-notif-dropdown');
    if (dd) dd.style.display = 'none';
    RpNotif.toggleSelectMode(false);
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => RpNotif.init());
} else {
    RpNotif.init();
}
</script>