<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MySembako</title>
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
            margin-right: 8px;
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
        .input-wrapper.password-confirm {
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
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="/icon/Logo_Mysembako.svg" alt="Logo" class="logo" onerror="this.style.display='none'">
    </div>

    <div class="content">
        <div class="form-container">
            <h1>Buat Akun Baru</h1>
            <div class="subtitle">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
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

            <!-- 
                Semua data registrasi (Name, Email, Phone, Password) akan disimpan ke tabel users di database.
                Pastikan backend menerima input ini dan melakukan validasi di controller (lihat AuthPagesController@submitRegister).
                Formulir ini akan menambahkan data user baru ke database users jika validasi berhasil.
            -->
            <form method="POST" action="{{ route('register.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">No Telepon</label>
                    <div class="input-wrapper">
                        <input type="tel" id="phone" name="phone" placeholder="Contoh: 08123456789" value="{{ old('phone') }}" pattern="[\d\s+]{6,20}" required>
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

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-wrapper password-confirm">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')" aria-label="Show/Hide Password">
                            <img src="/icon/HideIcon.svg" alt="Hide Password" class="toggle-icon" id="icon-password_confirmation" style="width:22px;height:22px;vertical-align:middle;">
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Daftar</button>
            </form>
            <div class="subtitle" style="font-size:13px;color:#888;line-height:1.6;">
                Dengan mendaftar, Anda menyetujui kebijakan privasi & ketentuan penggunaan MySembako.<br>
                Semua data registrasi Anda akan tersimpan di database pengguna MySembako.
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleBtn = passwordInput.nextElementSibling;
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
    </script>
</body>
</html>
