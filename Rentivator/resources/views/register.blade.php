<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentivator - Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 40%, #e0f2f1 100%);
        }

        .card {
            width: 450px;
            min-width: 450px;
            border-radius: 24px;
            overflow: visible;
            box-shadow: 0 20px 60px rgba(45,106,53,0.15), 0 0 0 1px rgba(109,190,71,0.1);
            display: flex;
            flex-direction: column;
        }

        .card-header {
            background: linear-gradient(135deg, #2d6a3f, #3a7d4e);
            padding: 16px 36px 14px;
            text-align: center;
            color: white;
        }

       

        .card-header h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 3px;
        }

        .card-body {
            background: white;
            padding: 24px 32px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .divider {
            font-size: 9px;
            font-weight: 800;
            color: #4a9e2e;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            margin: 0 0 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0f0e3;
        }

        .row { display: flex; gap: 10px;  overflow: visible; }
        .row .input-group { flex: 1; }

        .input-group {
            position: relative;
            margin-bottom: 14px;
        }

        .input-group label {
            display: block;
            font-size: 10px;
            font-weight: 800;
            color: #3a6b45;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-group i:not(.eye-icon) {
            position: absolute;
            left: 12px;
            bottom: 12px;
            color: #6DBE47;
            font-size: 12px;
}

        .input-group input {
            width: 100%;
            padding: 11px 36px 11px 34px;
            border: 1.5px solid #ddeedd;
            border-radius: 10px;
            font-size: 13px;
            background: #f7fbf7;
            outline: none;
            transition: all 0.2s;
            color: #1a3d24;
        }

        .input-group input:focus {
            border-color: #6DBE47;
            background: white;
            box-shadow: 0 0 0 3px rgba(109,190,71,0.12);
        }

        .input-group input::placeholder {
            color: #b8d4bc;
            font-size: 12px;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #6DBE47, #4a9e2e);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 16px;
            transition: all 0.25s;
            letter-spacing: 1px;
            box-shadow: 0 4px 18px rgba(109,190,71,0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(109,190,71,0.4);
        }

        .footer-text {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #8aaa92;
        }

        .footer-text a {
            color: #2d6a3f;
            font-weight: 700;
            text-decoration: none;
        }

        .error-msg {
            background: #fde8e8;
            color: #c0392b;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 11px;
            margin-bottom: 12px;
            border-left: 3px solid #e74c3c;
        }

        .error-msg ul { margin: 4px 0 0 14px; }

        .success-msg {
            background: #e8f5e9;
            color: #2d6a3f;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 12px;
            border-left: 3px solid #6DBE47;
        }
    </style>
</head>
<body>
    <div class="card">
    <div class="card-header">
    <div style="
        width: 100px; 
        height: 100px; 
        margin: 0 auto 6px; 
        display: flex;
        align-items: center;
        justify-content: center;
        transform: scale(0.75);
        transform-origin: center center;
    ">
        <x-app-logo />
    </div>
    <h1>RENTIVATOR</h1>
</div>
        <div class="card-body">

            @if(session('success'))
                <div class="success-msg">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-msg">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/register">
                @csrf
                <div class="divider">Personal Info</div>
                <div class="row">
                    <div class="input-group">
                        <label>Full Name</label>
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="name" placeholder="Juan Dela Cruz" value="{{ old('name') }}" required>
                    </div>
                    <div class="input-group">
                        <label>Phone</label>
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" name="phone_number" placeholder="09XXXXXXXXX" value="{{ old('phone_number') }}">
                    </div>
                </div>
                <div class="input-group">
                    <label>Location</label>
                    <i class="fa-solid fa-location-dot"></i>
                    <input type="text" name="address" placeholder="Street, City, Province" value="{{ old('address') }}">
                </div>
                <div class="divider" style="margin-top: 6px;">Account Info</div>
                <div class="input-group">
                    <label>Gmail Address</label>
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="yourname@gmail.com" value="{{ old('email') }}" required>
                </div>
                <div class="row">
                  <div class="input-group">
        <label>Password</label>
        <i class="fa-solid fa-lock"></i>
        <input type="password" id="password" name="password" 
               placeholder="Min. 6 chars" required>
       <i class="fa-solid fa-eye eye-icon" id="eye1open" 
   onclick="togglePw('password','eye1open','eye1closed')"
   style="position:absolute; right:10px; bottom:12px; cursor:pointer; left:auto;"></i>
<i class="fa-solid fa-eye-slash eye-icon" id="eye1closed" 
   onclick="togglePw('password','eye1open','eye1closed')"
   style="position:absolute; right:10px; bottom:12px; cursor:pointer; left:auto; display:none;"></i>
    </div>
                    <div class="input-group">
    <label>Confirm</label>
    <i class="fa-solid fa-lock"></i>
    <input type="password" id="password_confirmation" name="password_confirmation" 
           placeholder="Repeat" required>
   
</div>
</div>         
                <button type="submit" class="btn-submit">Create Account</button>
            </form>

            <div class="footer-text">
                Already have an account? <a href="/login">Sign in here</a>
            </div>
        </div>
    </div>
    <script>
function togglePw(inputId, openId, closedId) {
    var inp = document.getElementById(inputId);
    var isHidden = inp.type === 'password';
    inp.type = isHidden ? 'text' : 'password';
    document.getElementById(openId).style.display = isHidden ? 'none' : 'inline';
    document.getElementById(closedId).style.display = isHidden ? 'inline' : 'none';
}
</script>
</body>
</html>