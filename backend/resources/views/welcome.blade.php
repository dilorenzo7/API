<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PiLat - Peminjaman Alat</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/pilat-logo.svg') }}">
    <style>
        :root { --primary:#4f46e5; --primary-dark:#4338ca; --soft:#eef2ff; --bg:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e2e8f0; }
        * { box-sizing:border-box; margin:0; padding:0; }
        html { scroll-behavior:smooth; }
        body { font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif; color:var(--text); background:var(--bg); line-height:1.6; }
        a { text-decoration:none; }
        .container { max-width:1100px; margin:0 auto; padding:0 24px; }

        /* Navbar */
        nav { position:sticky; top:0; background:rgba(255,255,255,.9); backdrop-filter:blur(10px); border-bottom:1px solid var(--border); z-index:10; }
        .nav-inner { display:flex; align-items:center; justify-content:space-between; height:68px; }
        .brand { display:flex; align-items:center; gap:10px; font-weight:700; font-size:18px; color:var(--primary); }
        .nav-links { display:flex; align-items:center; gap:28px; }
        .nav-links a.link { color:var(--muted); font-size:14px; font-weight:500; }
        .nav-links a.link:hover { color:var(--primary); }
        .btn { display:inline-block; padding:10px 20px; border-radius:10px; font-size:14px; font-weight:600; transition:all .15s; }
        .btn-primary { background:var(--primary); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-outline { border:1px solid var(--border); color:var(--text); background:#fff; }
        .btn-outline:hover { border-color:var(--primary); color:var(--primary); }
        .btn-lg { padding:14px 28px; font-size:15px; }

        /* Hero */
        .hero { background:linear-gradient(180deg,var(--soft),#fff); padding:90px 0 100px; }
        .hero-inner { display:grid; grid-template-columns:1.1fr .9fr; gap:48px; align-items:center; }
        .badge { display:inline-block; background:#fff; color:var(--primary); border:1px solid #c7d2fe; padding:6px 14px; border-radius:999px; font-size:13px; font-weight:600; margin-bottom:18px; }
        .hero h1 { font-size:46px; line-height:1.15; margin-bottom:18px; letter-spacing:-.02em; }
        .hero h1 em { font-style:normal; color:var(--primary); }
        .hero p { color:var(--muted); font-size:17px; margin-bottom:30px; max-width:480px; }
        .hero-cta { display:flex; gap:12px; flex-wrap:wrap; }
        .hero-mascot { text-align:center; }
        .hero-mascot img { max-width:100%; height:auto; }

        /* Sections */
        section { padding:80px 0; }
        .sec-head { text-align:center; max-width:560px; margin:0 auto 48px; }
        .sec-head h2 { font-size:32px; letter-spacing:-.01em; margin-bottom:10px; }
        .sec-head p { color:var(--muted); }

        .grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:22px; }
        .feature { border:1px solid var(--border); border-radius:16px; padding:28px; transition:all .2s; }
        .feature:hover { border-color:#c7d2fe; box-shadow:0 10px 30px rgba(79,70,229,.08); transform:translateY(-3px); }
        .feature .ico { width:48px; height:48px; background:var(--soft); border-radius:12px; display:grid; place-items:center; font-size:24px; margin-bottom:16px; }
        .feature h3 { font-size:17px; margin-bottom:8px; }
        .feature p { color:var(--muted); font-size:14px; }

        .steps-bg { background:#f8fafc; }
        .step { text-align:center; padding:10px; }
        .step .num { width:52px; height:52px; border-radius:50%; background:var(--primary); color:#fff; font-weight:700; font-size:20px; display:grid; place-items:center; margin:0 auto 16px; }
        .step h3 { font-size:17px; margin-bottom:6px; }
        .step p { color:var(--muted); font-size:14px; }

        .cta { padding:0 0 80px; }
        .cta-box { background:var(--primary); color:#fff; border-radius:24px; padding:56px 32px; text-align:center; }
        .cta-box h2 { font-size:30px; margin-bottom:10px; }
        .cta-box p { opacity:.85; margin-bottom:28px; }
        .cta-box .btn { background:#fff; color:var(--primary); }
        .cta-box .btn:hover { background:var(--soft); }

        footer { border-top:1px solid var(--border); padding:28px 0; text-align:center; color:var(--muted); font-size:13px; }

        @media (max-width:860px) {
            .hero-inner { grid-template-columns:1fr; }
            .hero h1 { font-size:34px; }
            .grid-3 { grid-template-columns:1fr; }
            .nav-links a.link { display:none; }
            .hero { padding:60px 0; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav>
        <div class="container nav-inner">
            <a href="{{ route('landing') }}" class="brand"><img src="{{ asset('images/pilat-logo.svg') }}" alt="PiLat" width="34" height="34"> PiLat</a>
            <div class="nav-links">
                <a href="#fitur" class="link">Fitur</a>
                <a href="#cara" class="link">Cara Meminjam</a>

                @auth
                    @php
                        $dash = match(auth()->user()->role) {
                            'admin'   => route('admin.dashboard'),
                            'petugas' => route('petugas.dashboard'),
                            default   => route('peminjam.dashboard'),
                        };
                    @endphp
                    <a href="{{ $dash }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="hero">
        <div class="container hero-inner">
            <div>
                <span class="badge">PiLat · Peminjaman Alat</span>
                <h1>Pinjam alat jadi <em>cepat, rapi,</em> dan terpantau.</h1>
                <p>Ajukan peminjaman dari mana saja, pantau statusnya secara real-time, dan kembalikan alat tanpa ribet pencatatan manual.</p>
                <div class="hero-cta">
                    @auth
                        <a href="{{ $dash }}" class="btn btn-primary btn-lg">Buka Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
                        <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Login</a>
                    @endauth
                </div>
            </div>

            <div class="hero-mascot">
                <img src="{{ asset('images/pilat-maskot.svg') }}" alt="Pilo, maskot PiLat" width="300">
            </div>
        </div>
    </header>

    <!-- Fitur -->
    <section id="fitur">
        <div class="container">
            <div class="sec-head">
                <h2>Semua yang Kamu Butuhkan</h2>
                <p>Dirancang supaya proses peminjaman lebih mudah untuk peminjam maupun petugas.</p>
            </div>
            <div class="grid-3">
                <div class="feature">
                    <div class="ico">📦</div>
                    <h3>Katalog Alat</h3>
                    <p>Lihat daftar alat beserta kategori dan ketersediaannya sebelum mengajukan peminjaman.</p>
                </div>
                <div class="feature">
                    <div class="ico">✅</div>
                    <h3>Persetujuan Petugas</h3>
                    <p>Setiap pengajuan diperiksa dan disetujui petugas, sehingga peminjaman lebih terkontrol.</p>
                </div>
                <div class="feature">
                    <div class="ico">📊</div>
                    <h3>Riwayat & Laporan</h3>
                    <p>Seluruh riwayat peminjaman dan pengembalian tercatat otomatis dan bisa dicetak sebagai laporan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cara meminjam -->
    <section id="cara" class="steps-bg">
        <div class="container">
            <div class="sec-head">
                <h2>Cara Meminjam</h2>
                <p>Hanya empat langkah sederhana.</p>
            </div>
            <div class="grid-3" style="grid-template-columns:repeat(4,1fr);">
                <div class="step"><div class="num">1</div><h3>Daftar</h3><p>Buat akun peminjam dengan data diri kamu.</p></div>
                <div class="step"><div class="num">2</div><h3>Pilih Alat</h3><p>Cari alat yang dibutuhkan di katalog.</p></div>
                <div class="step"><div class="num">3</div><h3>Ajukan</h3><p>Kirim pengajuan dan tunggu persetujuan petugas.</p></div>
                <div class="step"><div class="num">4</div><h3>Kembalikan</h3><p>Ajukan pengembalian setelah selesai dipakai.</p></div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta" style="padding-top:80px;">
        <div class="container">
            <div class="cta-box">
                <h2>Siap meminjam alat?</h2>
                <p>Buat akun gratis dan ajukan peminjaman pertamamu hari ini.</p>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-lg">Daftar Sekarang</a>
                @else
                    <a href="{{ $dash }}" class="btn btn-lg">Ke Dashboard</a>
                @endguest
            </div>
        </div>
    </section>

    <footer>
        <div class="container">&copy; {{ date('Y') }} PiLat - Peminjaman Alat. All rights reserved.</div>
    </footer>

    <style>
        @media (max-width:860px) { #cara .grid-3 { grid-template-columns:1fr 1fr !important; } }
        @media (max-width:520px) { #cara .grid-3 { grid-template-columns:1fr !important; } }
    </style>
</body>
</html>