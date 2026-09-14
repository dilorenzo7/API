@extends('layouts.app')

@section('title', 'Dashboard - Portal Peminjam')
@section('header-title', 'Dashboard Peminjam')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Sambutan --}}
    <div class="mb-6 bg-blue-600 rounded-xl p-6 text-white shadow">
        <h2 class="text-xl font-bold">Selamat datang, {{ $user->name }}! 👋</h2>
        <p class="text-blue-100 text-sm mt-1">Kelola peminjaman alat kamu dari sini.</p>
        <a href="{{ route('peminjam.katalog') }}"
           class="mt-4 inline-block bg-white text-blue-600 text-sm font-semibold px-5 py-2 rounded-lg hover:bg-blue-50 transition">
            + Ajukan Peminjaman Baru
        </a>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $totalPeminjaman }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Peminjaman</p>
        </div>
        <div class="bg-white rounded-xl border border-yellow-200 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-yellow-500">{{ $menungguPersetujuan }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu Persetujuan</p>
        </div>
        <div class="bg-white rounded-xl border border-blue-200 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-blue-500">{{ $sedangDipinjam }}</p>
            <p class="text-xs text-gray-500 mt-1">Sedang Dipinjam</p>
        </div>
        <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-emerald-500">{{ $totalSelesai }}</p>
            <p class="text-xs text-gray-500 mt-1">Sudah Dikembalikan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Peminjaman Terbaru --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-sm">Aktivitas Terbaru</h3>
                <a href="{{ route('peminjam.riwayat') }}" class="text-blue-600 text-xs hover:underline">
                    Lihat semua →
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($peminjamanTerbaru as $p)
                    <div class="p-4 flex items-start gap-3">
                        {{-- Ikon status --}}
                        <div class="mt-0.5 text-lg">
                            @if($p->status === 'diajukan')      ⏳
                            @elseif($p->status === 'dipinjam')  📦
                            @elseif($p->status === 'dikembalikan') ✅
                            @elseif($p->status === 'telat')     ⚠️
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                @foreach($p->detailPinjam as $d)
                                    {{ $d->alat->nama_alat ?? '-' }}@if(!$loop->last), @endif
                                @endforeach
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Pinjam: {{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d M Y') }}
                                · Kembali: {{ \Carbon\Carbon::parse($p->tgl_kembali_plan)->format('d M Y') }}
                            </p>
                        </div>
                        <span class="shrink-0 px-2 py-0.5 text-xs font-semibold rounded-full
                            @if($p->status === 'diajukan')       bg-yellow-100 text-yellow-700
                            @elseif($p->status === 'dipinjam')   bg-blue-100 text-blue-700
                            @elseif($p->status === 'dikembalikan') bg-emerald-100 text-emerald-700
                            @elseif($p->status === 'telat')      bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-600
                            @endif">
                            {{ ucfirst($p->status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-sm">
                        Belum ada aktivitas peminjaman.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Preview Alat Tersedia --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-sm">Alat Tersedia</h3>
                <a href="{{ route('peminjam.katalog') }}" class="text-blue-600 text-xs hover:underline">
                    Lihat semua →
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($alatTersedia as $alat)
                    <div class="p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0 text-lg">
                            🔧
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $alat->nama_alat }}</p>
                            <p class="text-xs text-gray-500">{{ $alat->kategori->nama_kategori ?? '-' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold {{ $alat->stok <= 2 ? 'text-red-500' : 'text-emerald-600' }}">
                                {{ $alat->stok }} unit
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($alat->status_kondisi === 'baik') bg-emerald-100 text-emerald-700
                                @elseif($alat->status_kondisi === 'rusak ringan') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($alat->status_kondisi) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-sm">
                        Tidak ada alat tersedia.
                    </div>
                @endforelse
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100">
                <a href="{{ route('peminjam.katalog') }}"
                   class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 rounded-lg transition">
                    Ajukan Peminjaman →
                </a>
            </div>
        </div>

    </div>
@endsection
