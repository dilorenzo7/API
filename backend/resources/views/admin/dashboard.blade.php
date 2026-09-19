@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')

{{-- Welcome Banner --}}
<div class="mb-8 p-6 md:p-8 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl text-white shadow-xl shadow-slate-200 relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold mb-3 border border-indigo-500/30">
                <i class="bi bi-shield-lock-fill"></i> Panel Administrator
            </span>
            <h2 class="text-2xl md:text-3xl font-bold mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-slate-400 text-sm max-w-xl">
                Pantau statistik inventaris dan seluruh aktivitas peminjaman dari halaman ini.
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('admin.peminjaman.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition">
                <i class="bi bi-arrow-down-up"></i> Lihat Peminjaman
            </a>
            <a href="{{ route('admin.user.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold rounded-xl transition">
                <i class="bi bi-people-fill"></i> Kelola User
            </a>
        </div>
    </div>
    <i class="bi bi-person-badge absolute -bottom-8 -right-6 text-[10rem] text-white/5 pointer-events-none"></i>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <a href="{{ route('admin.alat.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
            <i class="bi bi-tools"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalAlat }}</p>
            <p class="text-xs text-slate-400 font-medium">Total Alat</p>
        </div>
    </a>

    <a href="{{ route('admin.kategori.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3">
        <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg">
            <i class="bi bi-tags-fill"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalKategori }}</p>
            <p class="text-xs text-slate-400 font-medium">Kategori</p>
        </div>
    </a>

    <a href="{{ route('admin.user.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalUser }}</p>
            <p class="text-xs text-slate-400 font-medium">Pengguna</p>
        </div>
    </a>

    <a href="{{ route('admin.peminjaman.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalPeminjaman }}</p>
            <p class="text-xs text-slate-400 font-medium">Sedang Dipinjam</p>
        </div>
    </a>

    <a href="{{ route('admin.peminjaman.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3 {{ $pendingApproval > 0 ? 'ring-2 ring-amber-300' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div>
            <p class="text-2xl font-bold {{ $pendingApproval > 0 ? 'text-amber-600' : 'text-slate-800' }}">{{ $pendingApproval }}</p>
            <p class="text-xs text-slate-400 font-medium">Perlu Persetujuan</p>
        </div>
    </a>

    <a href="{{ route('admin.pengembalian.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3 {{ $totalTerlambat > 0 ? 'ring-2 ring-red-200' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-lg">
            <i class="bi bi-clock-fill"></i>
        </div>
        <div>
            <p class="text-2xl font-bold {{ $totalTerlambat > 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $totalTerlambat }}</p>
            <p class="text-xs text-slate-400 font-medium">Terlambat</p>
        </div>
    </a>
</div>

{{-- Log Aktivitas --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="bi bi-journal-text"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Log Aktivitas Terbaru</h3>
                <p class="text-xs text-slate-400">Riwayat aksi pengguna pada sistem.</p>
            </div>
        </div>
        <span class="text-xs text-slate-400">20 terbaru</span>
    </div>

    <div class="divide-y divide-slate-50">
        @forelse($logs as $log)
            <div class="flex items-start gap-4 px-5 py-3.5 hover:bg-slate-50/60 transition">
                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                    {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                </div>
                {{-- Konten --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-700 leading-relaxed">
                        <span class="font-semibold text-slate-900">{{ $log->user->name ?? 'Sistem' }}</span>
                        — {{ $log->aktivitas }}
                    </p>
                    <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                        <i class="bi bi-clock"></i>
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400">
                <i class="bi bi-inbox text-4xl block mb-3 text-slate-200"></i>
                <p class="text-sm">Belum ada log aktivitas.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
