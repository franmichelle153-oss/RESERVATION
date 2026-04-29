{{-- FEEDBACK MODAL — i-include sa tenant pages --}}
<div id="feedbackModalOverlay"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55);
            z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:white; border-radius:24px; padding:40px 36px; max-width:420px;
                width:100%; text-align:center; box-shadow:0 24px 60px rgba(0,0,0,0.3);
                animation:fbIn 0.25s cubic-bezier(.4,0,.2,1); position:relative;">

        {{-- X button --}}
        <button onclick="closeFeedbackModal()"
                style="position:absolute; top:16px; right:16px; background:#f4f7f4;
                       border:none; border-radius:10px; width:34px; height:34px;
                       cursor:pointer; color:#8aaa92; font-size:16px;
                       display:flex; align-items:center; justify-content:center;">
            <i class="fa-solid fa-xmark"></i>
        </button>

        {{-- Icon --}}
        <div style="width:64px; height:64px; background:#e8f5e9; border-radius:50%;
                    display:flex; align-items:center; justify-content:center;
                    margin:0 auto 20px; font-size:28px; color:#1e4d2b;">
            <i class="fa-solid fa-star"></i>
        </div>

        <div style="font-size:20px; font-weight:900; color:#1a3d24; margin-bottom:6px;">
            How was your experience?
        </div>
        <div style="font-size:13px; color:#8aaa92; margin-bottom:28px;">
            Your feedback helps us improve our service.
        </div>

        {{-- Stars --}}
        <div style="display:flex; justify-content:center; gap:10px; margin-bottom:24px;"
             id="fbStarRow">
            @for($i = 1; $i <= 5; $i++)
            <button onclick="setFbRating({{ $i }})"
                    data-star="{{ $i }}"
                    style="background:none; border:none; cursor:pointer; font-size:36px;
                           color:#ddd; transition:color 0.15s, transform 0.15s;"
                    onmouseenter="hoverFbStar({{ $i }})"
                    onmouseleave="unhoverFbStar()">
                <i class="fa-solid fa-star"></i>
            </button>
            @endfor
        </div>

        {{-- Comment --}}
        <textarea id="fbComment" placeholder="Leave a comment... (optional)"
                  style="width:100%; padding:12px 16px; border:1.5px solid #ddeedd;
                         border-radius:12px; font-size:13px; color:#1a3d24; resize:none;
                         outline:none; font-family:'Segoe UI',sans-serif; min-height:90px;
                         margin-bottom:20px; transition:border-color 0.2s;"
                  onfocus="this.style.borderColor='#6DBE47'"
                  onblur="this.style.borderColor='#ddeedd'"></textarea>

        {{-- Buttons --}}
        <div style="display:flex; gap:12px;">
            <button onclick="closeFeedbackModal()"
                    style="flex:1; padding:13px; border:1.5px solid #e8f0e8;
                           border-radius:10px; background:white; color:#5a7a5e;
                           font-size:13px; font-weight:800; cursor:pointer;">
                Skip
            </button>
            <button onclick="submitFeedback()"
                    id="fbSubmitBtn"
                    style="flex:2; padding:13px; border:none; border-radius:10px;
                           background:#1e4d2b; color:white; font-size:13px;
                           font-weight:800; cursor:pointer; transition:background 0.2s;">
                <i class="fa-regular fa-paper-plane"></i> Submit
            </button>
        </div>

        {{-- Thank you state (hidden) --}}
        <div id="fbThankYou" style="display:none; padding:20px 0 0;">
            <div style="font-size:32px; margin-bottom:12px;">🎉</div>
            <div style="font-size:17px; font-weight:900; color:#1a3d24; margin-bottom:6px;">
                Thank you!
            </div>
            <div style="font-size:13px; color:#8aaa92;">
                Your feedback has been submitted.
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fbIn {
    from { opacity:0; transform:scale(0.92); }
    to   { opacity:1; transform:scale(1); }
}
</style>

<script>
let _fbRating       = 0;
let _fbReservation  = null;
const _fbCsrf       = '{{ csrf_token() }}';

function openFeedbackModal(reservationId) {
    _fbRating      = 0;
    _fbReservation = reservationId;
    document.getElementById('fbComment').value = '';
    document.getElementById('fbThankYou').style.display = 'none';
    document.getElementById('fbSubmitBtn').style.display = '';
    document.querySelectorAll('#fbStarRow button').forEach(b => {
        b.style.color = '#ddd';
        b.style.transform = '';
    });
    const overlay = document.getElementById('feedbackModalOverlay');
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeFeedbackModal() {
    document.getElementById('feedbackModalOverlay').style.display = 'none';
    document.body.style.overflow = '';
    sessionStorage.removeItem('open_feedback_reservation');
}

function setFbRating(n) {
    _fbRating = n;
    document.querySelectorAll('#fbStarRow button').forEach(b => {
        const s = parseInt(b.dataset.star);
        b.style.color     = s <= n ? '#f59e0b' : '#ddd';
        b.style.transform = s <= n ? 'scale(1.15)' : '';
    });
}

function hoverFbStar(n) {
    document.querySelectorAll('#fbStarRow button').forEach(b => {
        b.style.color = parseInt(b.dataset.star) <= n ? '#f59e0b' : '#ddd';
    });
}

function unhoverFbStar() {
    document.querySelectorAll('#fbStarRow button').forEach(b => {
        const s = parseInt(b.dataset.star);
        b.style.color = s <= _fbRating ? '#f59e0b' : '#ddd';
    });
}

async function submitFeedback() {
    if (!_fbRating) {
        alert('Please select a star rating.');
        return;
    }
    const btn = document.getElementById('fbSubmitBtn');
    btn.disabled    = true;
    btn.textContent = 'Submitting...';

    try {
      const res = await fetch('/tenant/feedback', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': _fbCsrf,
        'Accept':       'application/json',
    },
    body: JSON.stringify({
        reservation_id: _fbReservation,
        rating:         _fbRating,
        comment:        document.getElementById('fbComment').value.trim() || null,
    })
});

// ← Ipakita ang exact server error
if (!res.ok) {
    const errText = await res.text();
    console.error('Server response:', errText);
    alert('Server error ' + res.status + ': ' + errText.substring(0, 200));
    btn.disabled    = false;
    btn.textContent = 'Submit';
    return;
}

const data = await res.json();
if (data.success) {
            document.getElementById('fbThankYou').style.display = 'block';
            btn.style.display = 'none';
            sessionStorage.removeItem('open_feedback_reservation');
            setTimeout(closeFeedbackModal, 2000);
        }
   } catch(e) {
    console.error('Fetch error:', e);
    btn.disabled    = false;
    btn.textContent = 'Submit';
    alert('Error: ' + e.message);
}
}

// Auto-open kung galing sa notification click
document.addEventListener('DOMContentLoaded', function() {
    const rid = sessionStorage.getItem('open_feedback_reservation');
    if (rid) openFeedbackModal(rid);
});
</script>