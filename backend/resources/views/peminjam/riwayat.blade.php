@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Portal Peminjam')
@section('header-title', 'Riwayat Peminjaman Saya')

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

    {{-- Filter status --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
        <form action="{{ route('peminjam.riwayat') }}" method="GET" class="flex flex-wrap gap-2">
            @foreach(['', 'diajukan', 'dipinjam', 'dikembalikan', 'telat'] as $s)
                <button type="submit" name="status" value="{{ $s }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition
                    {{ ($status ?? '') === $s
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    @if($s === '')           Semua
                    @elseif($s === 'diajukan')   ⏳ Menunggu
                    @elseif($s === 'dipinjam')   📦 Dipinjam
                    @elseif($s === 'dikembalikan') ✅ Dikembalikan
                    @elseif($s === 'telat')      ⚠️ Telat
                    @endif
                    {{-- Badge jumlah --}}
                </button>
            @endforeach
        </form>
    </div>

    {{-- Tombol ajukan baru --}}
    <div class="flex justify-end mb-4">
        <a href="{{ route('peminjam.katalog') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Ajukan Peminjaman Baru
        </a>
    </div>

    {{-- Daftar peminjaman --}}
    @forelse($peminjamans as $p)
        <div class="bg-white rounded-xl border shadow-sm mb-4 overflow-hidden
            @if($p->status === 'diajukan')      border-yellow-200
            @elseif($p->status === 'dipinjam')  border-blue-200
            @elseif($p->status === 'dikembalikan') border-emerald-200
            @elseif($p->status === 'telat')     border-red-300
            @else border-gray-200 @endif">

            {{-- Header card --}}
            <div class="px-5 py-3 flex justify-between items-center border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-3">
                    <span class="text-lg">
                        @if($p->status === 'diajukan')      ⏳
                        @elseif($p->status === 'dipinjam')  📦
                        @elseif($p->status === 'dikembalikan') ✅
                        @elseif($p->status === 'telat')     ⚠️
                        @endif
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            Peminjaman #{{ $p->id }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Diajukan {{ $p->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    @if($p->status === 'diajukan')      bg-yellow-100 text-yellow-800
                    @elseif($p->status === 'dipinjam')  bg-blue-100 text-blue-800
                    @elseif($p->status === 'dikembalikan') bg-emerald-100 text-emerald-800
                    @elseif($p->status === 'telat')     bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ ucfirst($p->status) }}
                </span>
            </div>

            {{-- Isi card --}}
            <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">

                {{-- Alat dipinjam --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Alat Dipinjam</p>
                    <ul class="space-y-1">
                        @foreach($p->detailPinjam as $d)
                            <li class="flex items-center gap-2">
                                <span class="text-gray-800 font-medium">{{ $d->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded">
                                    {{ $d->jumlah }} pcs
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Info tanggal --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Jadwal</p>
                    <p class="text-gray-700">
                        <span class="text-gray-500">Pinjam:</span>
                        {{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d M Y') }}
                    </p>
                    <p class="text-gray-700 mt-1">
                        <span class="text-gray-500">Rencana Kembali:</span>
                        <span class="font-semibold {{ \Carbon\Carbon::today()->gt($p->tgl_kembali_plan) && !in_array($p->status, ['dikembalikan', 'telat']) ? 'text-red-600' : '' }}">
                            {{ \Carbon\Carbon::parse($p->tgl_kembali_plan)->format('d M Y') }}
                        </span>
                    </p>
                    {{-- Peringatan terlambat --}}
                    @if(\Carbon\Carbon::today()->gt($p->tgl_kembali_plan) && $p->status === 'dipinjam')
                        <p class="text-red-600 text-xs mt-1 font-semibold">
                            ⚠️ Terlambat {{ \Carbon\Carbon::parse($p->tgl_kembali_plan)->diffInDays(\Carbon\Carbon::today()) }} hari!
                        </p>
                    @endif
                </div>

                {{-- Info pengembalian / status --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Status Pengembalian</p>
                    @if($p->pengembalian)
                        <p class="text-gray-700">
                            <span class="text-gray-500">Tgl Kembali:</span>
                            {{ \Carbon\Carbon::parse($p->pengembalian->tgl_kembali)->format('d M Y') }}
                        </p>
                        <p class="text-gray-700 mt-1">
                            <span class="text-gray-500">Kondisi:</span>
                            {{ ucfirst($p->pengembalian->kondisi_kembali) }}
                        </p>
                        @if($p->pengembalian->denda > 0)
                            <p class="text-red-600 font-bold mt-1">
                                Denda: Rp {{ number_format($p->pengembalian->denda, 0, ',', '.') }}
                            </p>
                        @else
                            <p class="text-emerald-600 text-xs mt-1">✓ Tidak ada denda</p>
                        @endif
                    @elseif($p->status === 'diajukan')
                        <div class="flex items-center gap-2 text-yellow-600">
                            <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                            <span class="text-xs">Menunggu persetujuan petugas</span>
                        </div>
                    @elseif($p->status === 'dipinjam')
                        <div class="flex items-center gap-2 text-blue-600">
                            <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                            <span class="text-xs">Alat sedang digunakan</span>
                        </div>
                    @else
                        <p class="text-gray-400 text-xs italic">-</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm py-12 text-center text-gray-500">
            <p class="text-3xl mb-3">📋</p>
            <p class="font-medium">Belum ada riwayat peminjaman.</p>
            <a href="{{ route('peminjam.katalog') }}"
               class="mt-3 inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                Ajukan Peminjaman Pertama →
            </a>
        </div>
    @endforelse
@endsection
