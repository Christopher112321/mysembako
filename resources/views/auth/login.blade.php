<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MySembako</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            min-height: 100vh;
            background: #fff;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
        .header {
            display: flex;
            align-items: center;
            padding: 22px 0 0 32px;
        }
        .logo {
            width: auto;
            height: 36px;
        }
        .brand {
            font-weight: 700;
            font-size: 16px;
            color: #ef6b49;
            letter-spacing: 0.08em;
        }
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .form-container {
            width: 100%;
            max-width: 420px;
        }
        h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 4px;
            margin-top: 16px;
            text-align: center;
        }
        h1 {
            margin-bottom: 4px;
            margin-top: 16px;
            text-align: center;
        }
        .subtitle {
            font-size: 17px;
            text-align: center;
            margin-bottom: 24px;
            color: #222;
            font-weight: 400;
        }
        .subtitle a {
            color: #343bc5;
            text-decoration: underline;
            font-weight: 400;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            font-size: 15px;
            font-weight: 500;
            color: #343434;
            margin-left: 8px;
            margin-bottom: 3px;
            display: block;
            letter-spacing: 0.02em;
        }
        .input-wrapper {
            background: #fff;
            border: 1.5px solid #bababa;
            border-radius: 20px;
            height: 40px;
            display: flex;
            align-items: center;
            padding: 0 16px;
        }
        .input-wrapper.password {
            padding: 0 12px 0 16px;
            position: relative;
        }
        input {
            border: none;
            outline: none;
            background: transparent;
            flex: 1;
            font-size: 15px;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            color: #252525;
            padding: 0;
        }
        .toggle-password {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            font-size: 14px;
            color: #999;
        }
        .btn-submit {
            width: 100%;
            height: 40px;
            border-radius: 20px;
            background: #ef6b49;
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            border: none;
            margin-top: 10px;
            margin-bottom: 16px;
            cursor: pointer;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            letter-spacing: 0.03em;
        }
        .btn-submit:hover {
            background: #e55a39;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-buka-toko {
            width: 100%;
            height: 40px;
            border-radius: 20px;
            background: transparent;
            color: #ef6b49;
            font-weight: 600;
            font-size: 15px;
            border: 2px solid #ef6b49;
            margin-top: 8px;
            margin-bottom: 16px;
            cursor: pointer;
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
            letter-spacing: 0.03em;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .btn-buka-toko:hover {
            background: #ef6b49;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="/icon/Logo_Mysembako.svg" alt="Logo" class="logo" onerror="this.style.display='none'">
    </div>

    <div class="content">
        <div class="form-container">
            <h1>Welcome to MySembako</h1>
            <div class="subtitle">
                New here? <a href="{{ route('register') }}">Create an account</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper password">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')" aria-label="Show/Hide Password">
                            <img src="/icon/HideIcon.svg" alt="Hide Password" class="toggle-icon" id="icon-password" style="width:22px;height:22px;vertical-align:middle;">
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Login</button>
            </form>

            <a href="{{ route('buka-toko') }}" class="btn-buka-toko">
                Buka Toko
            </a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleBtn = passwordInput.parentNode.querySelector('.toggle-password');
            const icon = toggleBtn.querySelector('img');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.src = '/icon/UnhideIcon.svg';
                icon.alt = 'Show Password';
            } else {
                passwordInput.type = 'password';
                icon.src = '/icon/HideIcon.svg';
                icon.alt = 'Hide Password';
            }
        }

        // Login hanya untuk akun yang telah terdaftar pada database.
        // User akan login cukup dengan email & password.
        // Sistem backend akan otomatis memfilter dan mengenali role user (buyer, owner, admin)
        // Redirect akan mengikuti role user yang ditemukan di database:
        // buyer -> /home, owner -> /owner/dashboard, admin -> /admin/dashboard
        // Jika email/password tidak sesuai atau tidak terdaftar, error akan tampil di sini.
    </script>
</body>
</html>
