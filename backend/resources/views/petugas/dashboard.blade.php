@extends('layouts.app')
@section('title', 'Dashboard Petugas')

@section('content')

@php $hariIni = \Carbon\Carbon::today(); @endphp

{{-- Alert --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-3">
        <i class="bi bi-check-circle-fill text-lg"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm flex items-center gap-3">
        <i class="bi bi-exclamation-circle-fill text-lg"></i> {{ session('error') }}
    </div>
@endif

{{-- Welcome Banner --}}
<div class="mb-8 p-6 md:p-8 bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-3xl text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-indigo-100 text-xs font-semibold mb-3 border border-white/20">
                <i class="bi bi-person-badge-fill"></i> Panel Petugas
            </span>
            <h2 class="text-2xl md:text-3xl font-bold mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-indigo-100 text-sm max-w-xl">
                Pantau pengajuan yang menunggu persetujuan dan peminjaman yang akan jatuh tempo dari halaman ini.
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('petugas.peminjaman.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 text-xs font-semibold rounded-xl transition">
                <i class="bi bi-clipboard-check-fill"></i> Persetujuan Pinjam
            </a>
            <a href="{{ route('petugas.pengembalian.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500/60 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition">
                <i class="bi bi-arrow-return-left"></i> Proses Pengembalian
            </a>
            <a href="{{ route('petugas.laporan.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500/60 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Cetak Laporan
            </a>
        </div>
    </div>
    <i class="bi bi-clipboard-check absolute -bottom-8 -right-6 text-[10rem] text-white/10 pointer-events-none"></i>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('petugas.peminjaman.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3 {{ $menungguPersetujuan > 0 ? 'ring-2 ring-amber-300' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div>
            <p class="text-2xl font-bold {{ $menungguPersetujuan > 0 ? 'text-amber-600' : 'text-slate-800' }}">{{ $menungguPersetujuan }}</p>
            <p class="text-xs text-slate-400 font-medium">Menunggu Persetujuan</p>
        </div>
    </a>

    <a href="{{ route('petugas.pengembalian.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $sedangDipinjam }}</p>
            <p class="text-xs text-slate-400 font-medium">Sedang Dipinjam</p>
        </div>
    </a>

    <a href="{{ route('petugas.pengembalian.index') }}"
       class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col gap-3 {{ $terlambat > 0 ? 'ring-2 ring-red-200' : '' }}">
        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-lg">
            <i class="bi bi-clock-fill"></i>
        </div>
        <div>
            <p class="text-2xl font-bold {{ $terlambat > 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $terlambat }}</p>
            <p class="text-xs text-slate-400 font-medium">Terlambat</p>
        </div>
    </a>

    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="bi bi-check2-circle"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-emerald-600">{{ $dikembalikanHariIni }}</p>
            <p class="text-xs text-slate-400 font-medium">Dikembalikan Hari Ini</p>
        </div>
    </div>
</div>

{{-- Dua Kolom: Pengajuan Terbaru & Jatuh Tempo --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- Pengajuan Terbaru --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-hourglass-split text-amber-500"></i> Pengajuan Terbaru
            </h3>
            <a href="{{ route('petugas.peminjaman.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Lihat semua →
            </a>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse($pengajuanTerbaru as $p)
                <div class="px-6 py-4 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shrink-0">
                        {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $p->user->name ?? 'User dihapus' }}</p>
                        <p class="text-xs text-slate-500 truncate">
                            {{ $p->detailPinjam->map(fn($d) => ($d->alat->nama_alat ?? 'Alat dihapus') . ' ×' . $d->jumlah)->implode(', ') }}
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1">
                            <i class="bi bi-calendar-event"></i>
                            {{ $p->tgl_pinjam->format('d M Y') }} – {{ $p->tgl_kembali_plan->format('d M Y') }}
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100 shrink-0">
                        Diajukan
                    </span>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-slate-400">
                    <i class="bi bi-inbox text-3xl block mb-2"></i>
                    <p class="text-sm">Tidak ada pengajuan yang menunggu.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Jatuh Tempo --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-alarm-fill text-red-500"></i> Paling Dekat Jatuh Tempo
            </h3>
            <a href="{{ route('petugas.pengembalian.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Proses pengembalian →
            </a>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse($jatuhTempo as $p)
                @php
                    // negatif = sudah lewat jatuh tempo
                    $tglPlan = \Carbon\Carbon::parse($p->tgl_kembali_plan)->startOfDay();
                    $selisih = (int) $hariIni->diffInDays($tglPlan, false);
                @endphp
                <div class="px-6 py-4 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold shrink-0">
                        {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $p->user->name ?? 'User dihapus' }}</p>
                        <p class="text-xs text-slate-500 truncate">
                            {{ $p->detailPinjam->map(fn($d) => ($d->alat->nama_alat ?? 'Alat dihapus') . ' ×' . $d->jumlah)->implode(', ') }}
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1">
                            <i class="bi bi-calendar-check"></i>
                            Kembali: {{ $tglPlan->format('d M Y') }}
                        </p>
                    </div>

                    @if($selisih < 0)
                        <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-[10px] font-bold border border-red-100 shrink-0">
                            Terlambat {{ abs($selisih) }} hari
                        </span>
                    @elseif($selisih === 0)
                        <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100 shrink-0">
                            Jatuh tempo hari ini
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100 shrink-0">
                            {{ $selisih }} hari lagi
                        </span>
                    @endif
                </div>
            @empty
                <div class="px-6 py-10 text-center text-slate-400">
                    <i class="bi bi-check2-all text-3xl block mb-2"></i>
                    <p class="text-sm">Tidak ada peminjaman yang sedang berjalan.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection