<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Peminjaman Alat</title>
    <style>
        :root { --primary:#4f46e5; --primary-dark:#4338ca; --bg:#f1f5f9; --text:#0f172a; --muted:#64748b; --border:#e2e8f0; --danger:#dc2626; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
        .card { background:#fff; width:100%; max-width:460px; border-radius:16px; padding:36px 32px; box-shadow:0 10px 30px rgba(15,23,42,.08); }
        .brand { display:flex; align-items:center; gap:10px; font-weight:700; font-size:18px; color:var(--primary); margin-bottom:20px; text-decoration:none; }
        .brand span { background:var(--primary); color:#fff; width:34px; height:34px; border-radius:10px; display:grid; place-items:center; }
        h1 { font-size:24px; margin-bottom:6px; }
        .sub { color:var(--muted); font-size:14px; margin-bottom:24px; }
        .field { margin-bottom:16px; }
        label { display:block; font-size:13px; font-weight:600; margin-bottom:6px; }
        input, textarea { width:100%; padding:11px 14px; border:1px solid var(--border); border-radius:10px; font-size:14px; font-family:inherit; transition:border-color .15s, box-shadow .15s; }
        textarea { resize:vertical; min-height:80px; }
        input:focus, textarea:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(79,70,229,.15); }
        .is-invalid { border-color:var(--danger); }
        .error { color:var(--danger); font-size:12px; margin-top:5px; }
        .row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .btn { width:100%; padding:12px; background:var(--primary); color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:600; cursor:pointer; margin-top:6px; transition:background .15s; }
        .btn:hover { background:var(--primary-dark); }
        .foot { text-align:center; font-size:14px; color:var(--muted); margin-top:20px; }
        .foot a { color:var(--primary); font-weight:600; text-decoration:none; }
        @media (max-width:480px) { .row { grid-template-columns:1fr; } .card { padding:28px 22px; } }
    </style>
</head>
<body>
    <div class="card">
        <a href="{{ route('landing') }}" class="brand"><span>P</span> Peminjaman Alat</a>

        <h1>Buat Akun</h1>
        <p class="sub">Daftar sebagai peminjam untuk mulai meminjam alat.</p>

        <form method="POST" action="{{ route('register.store') }}" novalidate>
            @csrf

            <div class="field">
                <label for="name">Username</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nama pengguna" class="@error('name') is-invalid @enderror" required autofocus>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="@error('email') is-invalid @enderror" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="no_hp">No. Telepon</label>
                <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" placeholder="081234567890" class="@error('no_hp') is-invalid @enderror" required>
                @error('no_hp') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" placeholder="Alamat lengkap" class="@error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
                @error('alamat') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Min. 8 karakter" class="@error('password') is-invalid @enderror" required>
                </div>
                <div class="field">
                    <label for="password_confirmation">Konfirmasi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                </div>
            </div>
            @error('password') <div class="error" style="margin-top:-8px;margin-bottom:12px;">{{ $message }}</div> @enderror

            <button type="submit" class="btn">Daftar</button>
        </form>

        <p class="foot">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
    </div>
</body>
</html>