<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rentivator - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

       body {
    font-family: 'Segoe UI', sans-serif;
    height: 100vh;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 40%, #e0f2f1 100%);
}

    .card {
    width: 370px;
    min-width: 320px;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(45,106,53,0.15), 0 0 0 1px rgba(109,190,71,0.1);
    display: flex;
    flex-direction: column;
}

        .card-header {
            background: linear-gradient(135deg, #2d6a3f, #3a7d4e);
            padding: 22px 24px 20px;
            text-align: center;
            color: white;
        }

       

        .card-header h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 3px;
        }

        .card-body {
            background: white;
            padding: 20px 24px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: #3a6b45;
            margin-bottom: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            bottom: 16px;
            color: #6DBE47;
            font-size: 15px;
        }

        .input-group input {
            width: 100%;
            padding: 16px 16px 16px 46px;
            border: 1.5px solid #ddeedd;
            border-radius: 12px;
            font-size: 15px;
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
            font-size: 14px;
        }

        .btn-submit {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #6DBE47, #4a9e2e);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 4px;
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
            margin-top: 24px;
            font-size: 14px;
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
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 3px solid #e74c3c;
        }

        @keyframes fadeOut {
            to { opacity: 0; pointer-events: none; }
        }

        /* Prevent iOS auto-zoom */
input, select, textarea {
    font-size: 16px !important;
}
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <x-app-logo />
<h1>RENTIVATOR</h1>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="error-msg">
                    <i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <div class="input-group">
                    <label>Email Address</label>
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="email@example.com" value="{{ old('email') }}" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-submit">Sign In</button>
            </form>

            <div class="footer-text">
                Don't have an account? <a href="/register">Register now</a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div id="overlay" style="
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.3);
        z-index: 9998;
    "></div>

    <div id="toast" style="
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 16px;
        padding: 32px 40px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        z-index: 9999;
    ">
        <div style="
            width: 60px; height: 60px;
            background: #e8f5e9;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        ">
            <i class="fa-solid fa-circle-check" style="color: #6DBE47; font-size: 28px;"></i>
        </div>
        <p style="font-size: 16px; font-weight: 700; color: #1a3d24; margin-bottom: 6px;">Account Created!</p>
        <p style="font-size: 13px; color: #8aaa92;">You can now sign in to your account.</p>
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('toast')?.remove();
            document.getElementById('overlay')?.remove();
        }, 3000);
    </script>
    @endif

</body>
</html>