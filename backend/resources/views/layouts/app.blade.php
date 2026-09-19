<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Peminjaman Alat') — PinjamAlat</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Tailwind CSS --}}
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 500; transition: all 150ms ease; }
        .sidebar-link.active { background-color: #4f46e5; color: #fff; box-shadow: 0 4px 14px rgba(99,102,241,.4); }
        .sidebar-link:not(.active) { color: #94a3b8; }
        .sidebar-link:not(.active):hover { background-color: #1e293b; color: #fff; }
        /* Scrollbar tipis */
        aside::-webkit-scrollbar { width: 4px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">

    {{-- ══════════════════ SIDEBAR ══════════════════ --}}
    <aside id="sidebar"
           class="w-full md:w-64 bg-slate-900 text-white shrink-0 flex flex-col min-h-screen
                  fixed md:static inset-y-0 left-0 z-40 -translate-x-full md:translate-x-0 transition-transform duration-300">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                <i class="bi bi-box-seam-fill text-lg"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-base leading-tight tracking-tight">PinjamAlat</h1>
                <span class="text-[10px] text-slate-400 capitalize font-medium">
                    Portal {{ ucfirst(auth()->user()->role ?? 'Sistem') }}
                </span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            @php $role = auth()->user()->role ?? ''; @endphp

            @if($role === 'admin')
            {{-- ─── ADMIN MENU ─── --}}
                <p class="px-3 pt-2 pb-1.5 text-[9px] font-bold tracking-widest text-slate-600 uppercase">Utama</p>
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill text-base"></i> Dashboard
                </a>

                <p class="px-3 pt-4 pb-1.5 text-[9px] font-bold tracking-widest text-slate-600 uppercase">Master Data</p>
                <a href="{{ route('admin.kategori.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill text-base"></i> Kategori Alat
                </a>
                <a href="{{ route('admin.alat.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.alat.*') ? 'active' : '' }}">
                    <i class="bi bi-tools text-base"></i> Data Alat
                </a>

                <p class="px-3 pt-4 pb-1.5 text-[9px] font-bold tracking-widest text-slate-600 uppercase">Transaksi</p>
                <a href="{{ route('admin.peminjaman.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.peminjaman.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-down-up text-base"></i> Peminjaman
                    @php $pending = \App\Models\Peminjaman::where('status','diajukan')->count(); @endphp
                    @if($pending > 0)
                        <span class="ml-auto bg-amber-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $pending }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.pengembalian.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.pengembalian.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-return-left text-base"></i> Pengembalian
                </a>

                <p class="px-3 pt-4 pb-1.5 text-[9px] font-bold tracking-widest text-slate-600 uppercase">Pengaturan</p>
                <a href="{{ route('admin.user.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill text-base"></i> Kelola User
                </a>

            @elseif($role === 'petugas')
            {{-- ─── PETUGAS MENU ─── --}}
                <p class="px-3 pt-2 pb-1.5 text-[9px] font-bold tracking-widest text-slate-600 uppercase">Persetujuan</p>
                <a href="{{ route('petugas.peminjaman.index') }}"
                   class="sidebar-link {{ request()->routeIs('petugas.peminjaman.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check-fill text-base"></i> Persetujuan Pinjam
                    @php $pendingPetugas = \App\Models\Peminjaman::where('status','diajukan')->count(); @endphp
                    @if($pendingPetugas > 0)
                        <span class="ml-auto bg-amber-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingPetugas }}</span>
                    @endif
                </a>
                <a href="{{ route('petugas.pengembalian.index') }}"
                   class="sidebar-link {{ request()->routeIs('petugas.pengembalian.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-return-left text-base"></i> Proses Pengembalian
                </a>

                <p class="px-3 pt-4 pb-1.5 text-[9px] font-bold tracking-widest text-slate-600 uppercase">Laporan</p>
                <a href="{{ route('petugas.laporan.index') }}"
                   class="sidebar-link {{ request()->routeIs('petugas.laporan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill text-base"></i> Cetak Laporan
                </a>

            @else
            {{-- ─── PEMINJAM MENU ─── --}}
                <p class="px-3 pt-2 pb-1.5 text-[9px] font-bold tracking-widest text-slate-600 uppercase">Menu</p>
                <a href="{{ route('peminjam.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('peminjam.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill text-base"></i> Dashboard
                </a>
                <a href="{{ route('peminjam.katalog') }}"
                   class="sidebar-link {{ request()->routeIs('peminjam.katalog') ? 'active' : '' }}">
                    <i class="bi bi-collection-fill text-base"></i> Katalog Alat
                </a>
                <a href="{{ route('peminjam.riwayat') }}"
                   class="sidebar-link {{ request()->routeIs('peminjam.riwayat') ? 'active' : '' }}">
                    <i class="bi bi-clock-history text-base"></i> Riwayat Pinjam
                </a>
            @endif

            {{-- Profil — semua role --}}
            <p class="px-3 pt-4 pb-1.5 text-[9px] font-bold tracking-widest text-slate-600 uppercase">Akun</p>
            <a href="{{ route('profile.show') }}"
               class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle text-base"></i> Profil Saya
            </a>
        </nav>

        {{-- User Footer --}}
        <div class="px-3 pb-4 pt-2 border-t border-slate-800">
            <a href="{{ route('profile.show') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-2xl hover:bg-slate-800 transition group mb-1">
                @if(auth()->user()->foto_profile)
                    <img src="{{ asset('storage/' . auth()->user()->foto_profile) }}"
                         alt="Foto Profil"
                         class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-700 group-hover:ring-indigo-500 transition shrink-0">
                @else
                    <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white text-sm shrink-0 group-hover:bg-indigo-500 transition">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate group-hover:text-indigo-300 transition">
                        {{ auth()->user()->name ?? 'User' }}
                    </p>
                    <p class="text-[10px] text-slate-500 capitalize">{{ auth()->user()->role ?? 'peminjam' }}</p>
                </div>
                <i class="bi bi-chevron-right text-slate-600 text-xs ml-auto group-hover:text-indigo-400 transition"></i>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition">
                    <i class="bi bi-box-arrow-right text-base"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ══════════════════ MOBILE TOPBAR ══════════════════ --}}
    <div class="md:hidden flex items-center justify-between bg-slate-900 text-white px-4 py-3 sticky top-0 z-30">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-sm">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <span class="font-bold text-sm">PinjamAlat</span>
        </div>
        <button onclick="toggleSidebar()" class="text-slate-400 hover:text-white p-1">
            <i class="bi bi-list text-xl" id="sidebar-icon"></i>
        </button>
    </div>

    {{-- Overlay Mobile --}}
    <div id="sidebar-overlay" onclick="toggleSidebar()"
         class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

    {{-- ══════════════════ MAIN CONTENT ══════════════════ --}}
    <main class="flex-1 p-5 md:p-8 overflow-y-auto min-h-screen">
        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const icon    = document.getElementById('sidebar-icon');
            const isOpen  = !sidebar.classList.contains('-translate-x-full');

            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                icon.className = 'bi bi-list text-xl';
            } else {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                icon.className = 'bi bi-x-lg text-xl';
            }
        }
    </script>
</body>
</html>
