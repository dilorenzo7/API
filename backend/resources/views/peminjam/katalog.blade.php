@extends('layouts.app')

@section('title', 'Katalog Alat - Peminjam')

@section('content')
    <!-- Header & Search Filter -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Katalog Peralatan</h2>
            <p class="text-xs text-slate-500">Pilih alat yang dibutuhkan lalu tentukan tanggal pengembalian.</p>
        </div>

        <form action="{{ route('peminjam.katalog') }}" method="GET" class="flex flex-wrap md:flex-nowrap gap-2">
            <!-- Search -->
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari alat..." 
                   class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full md:w-48">
            
            <!-- Category Filter -->
            <select name="kategori_id" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
            
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-semibold hover:bg-slate-700 transition">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <!-- Alert Success/Error -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-base"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-base"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Form Transaksi Peminjaman -->
    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf

        <!-- Box Tanggal Pinjam & Kembali -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Rencana Tanggal Kembali</label>
                <input type="date" name="tgl_kembali_plan" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>
        </div>

        <!-- Grid Cards Alat -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @forelse($alats as $alat)
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition flex flex-col justify-between relative">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-600 text-[10px] font-semibold">
                                {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                            <span class="text-[10px] font-medium text-slate-400">Stok: <strong class="text-slate-700">{{ $alat->stok }}</strong></span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mb-2">{{ $alat->nama_alat }}</h3>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 mt-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" id="alat-{{ $alat->id }}" 
                                   class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                            <label for="alat-{{ $alat->id }}" class="text-xs text-slate-600 font-medium">Pilih</label>
                        </div>

                        <div class="w-24">
                            <input type="number" name="jumlah[]" value="1" min="1" max="{{ $alat->stok }}" 
                                   class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-center focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-8 rounded-2xl text-center text-slate-400 text-sm">
                    Alat tidak ditemukan.
                </div>
            @endforelse
        </div>

        <!-- Submit Button -->
        @if(count($alats) > 0)
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-200 transition flex items-center gap-2">
                    <i class="bi bi-send-fill"></i> Kirim Pengajuan Peminjaman
                </button>
            </div>
        @endif
    </form>
@endsection