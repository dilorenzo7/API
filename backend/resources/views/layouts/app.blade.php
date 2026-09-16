<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Peminjaman Alat')</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Sidebar Navbar -->
    <aside class="w-full md:w-64 bg-slate-900 text-white shrink-0 flex flex-col justify-between p-4 min-h-screen">
        <div>
            <!-- Brand Logo -->
            <div class="flex items-center gap-3 px-3 py-4 border-b border-slate-800 mb-6">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div>
                    <h1 class="font-bold text-base leading-tight">PinjamAlat</h1>
                    <span class="text-xs text-slate-400 capitalize">Portal {{ auth()->user()->role ?? 'Sistem' }}</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1">
                @if(auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'petugas'))
                    <!-- MENU ADMIN / PETUGAS -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill text-base"></i>
                        Dashboard
                    </a>

                    <div class="pt-3 pb-1 px-4 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Master Data</div>

                    <a href="{{ route('admin.kategori.index') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition {{ request()->routeIs('admin.kategori.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-tags-fill text-base"></i>
                        Kategori Alat
                    </a>

                    <a href="{{ route('admin.alat.index') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition {{ request()->routeIs('admin.alat.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-tools text-base"></i>
                        Data Alat
                    </a>

                    <div class="pt-3 pb-1 px-4 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Transaksi</div>

                    <a href="{{ route('admin.peminjaman.index') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition {{ request()->routeIs('admin.peminjaman.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-arrow-down-up text-base"></i>
                        Peminjaman
                    </a>

                    <a href="{{ route('admin.pengembalian.index') }}" 
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition {{ request()->routeIs('admin.pengembalian.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-arrow-return-left text-base"></i>
                        Pengembalian
                    </a>

                    @if(auth()->user()->role == 'admin')
                        <div class="pt-3 pb-1 px-4 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Pengaturan</div>

                        <a href="{{ route('admin.user.index') }}" 
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition {{ request()->routeIs('admin.user.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="bi bi-people-fill text-base"></i>
                            Kelola User
                        </a>
                    @endif

                @else
                    <!-- MENU PEMINJAM -->
                    <a href="{{ route('peminjam.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('peminjam.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill text-lg"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('peminjam.katalog') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('peminjam.katalog') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-collection-fill text-lg"></i>
                        Katalog Alat
                    </a>

                    <a href="{{ route('peminjam.riwayat') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition {{ request()->routeIs('peminjam.riwayat') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-clock-history text-lg"></i>
                        Riwayat Pinjam
                    </a>
                @endif
            </nav>
        </div>

        <!-- User Profile & Logout -->
        <div class="pt-4 border-t border-slate-800">
            <div class="flex items-center justify-between px-3 py-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-slate-200 text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="truncate max-w-[110px]">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-[10px] text-slate-400 capitalize">{{ auth()->user()->role ?? 'Peminjam' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-lg transition" title="Logout">
                        <i class="bi bi-box-arrow-right text-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        @yield('content')
    </main>

</body>
</html>