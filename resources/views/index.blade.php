<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MySembako</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:#f6f7fb; margin:0; }
        .wrap { max-width: 920px; margin: 48px auto; padding: 0 16px; }
        .card { background:#fff; border-radius:12px; padding:24px; box-shadow:0 10px 24px rgba(0,0,0,.06); }
        .brand { font-weight:700; color:#ef6b49; }
    </style>
</head>
<body>
    <div class="wrap">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
            <div style="width:22px; height:22px; background:#ef6b49; border-radius:4px;"></div>
            <div class="brand">MySembako</div>
        </div>
        <div class="card">
            <h2 style="margin:0 0 8px;">Halo!</h2>
            <p style="margin:0 0 12px;">Anda berhasil diarahkan ke halaman index setelah menekan tombol login.</p>
            <p style="margin:0;">
                <a href="{{ route('login') }}" style="color:#2563eb; text-decoration:none;">Kembali ke Login</a>
            </p>
        </div>
    </div>
</body>
</html>


