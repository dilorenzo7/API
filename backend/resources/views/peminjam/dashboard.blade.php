@extends('layouts.app')

@section('title', 'Dashboard - Peminjam')

@section('content')
    <!-- Banner Welcome -->
    <div class="mb-8 p-6 md:p-8 bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-3xl text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Halo, {{ $user->name }}! 👋</h2>
            <p class="text-indigo-100 text-sm md:text-base max-w-xl">
                Selamat datang di portal peminjaman alat. Pilih barang yang kamu butuhkan dan ajukan peminjaman dengan mudah.
            </p>
        </div>
        <i class="bi bi-tools absolute -bottom-6 -right-6 text-9xl text-white/10 pointer-events-none"></i>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Total Pengajuan</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $totalPeminjaman }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="bi bi-journal-text"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Sedang Dipinjam</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $sedangDipinjam }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Menunggu Approval</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $menungguPersetujuan }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Selesai/Dikembalikan</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $totalSelesai }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Content Row: Alat Tersedia & Riwayat Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Preview Alat Tersedia (2 Cols) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Alat Siap Dipinjam</h3>
                <a href="{{ route('peminjam.katalog') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($alatTersedia as $alat)
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition">
                        <span class="inline-block px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-[10px] font-semibold mb-2">
                            {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                        <h4 class="font-bold text-slate-800 text-sm mb-1 truncate">{{ $alat->nama_alat }}</h4>
                        <p class="text-xs text-slate-400 mb-3">Sisa Stok: <span class="font-semibold text-slate-700">{{ $alat->stok }}</span></p>
                        <a href="{{ route('peminjam.katalog') }}" class="block text-center w-full py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white text-xs font-semibold rounded-lg transition">
                            Pinjam
                        </a>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-6 rounded-2xl text-center text-slate-400 text-sm">
                        Tidak ada alat yang tersedia.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Peminjaman Terbaru (1 Col) -->
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Aktivitas Terakhir</h3>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm space-y-3">
                @forelse($peminjamanTerbaru as $item)
                    <div class="p-3 rounded-xl bg-slate-50 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-800">
                                {{ $item->detailPinjam->first()->alat->nama_alat ?? 'Alat' }}
                                @if($item->detailPinjam->count() > 1)
                                    <span class="text-[10px] font-normal text-slate-500">(+{{ $item->detailPinjam->count() - 1 }} lainnya)</span>
                                @endif
                            </p>
                            <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</p>
                        </div>
                        <div>
                            @if($item->status == 'diajukan')
                                <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold">Diajukan</span>
                            @elseif($item->status == 'dipinjam')
                                <span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-bold">Dipinjam</span>
                            @elseif($item->status == 'dikembalikan')
                                <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">Selesai</span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-bold">Telat</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-xs text-slate-400 py-4">Belum ada riwayat aktivitas.</p>
                @endforelse
            </div>
        </div>

    </div>
@endsection