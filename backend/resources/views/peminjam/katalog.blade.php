@extends('layouts.app')
@section('title', 'Katalog Alat')

@section('content')

{{-- Alerts --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-2">
        <i class="bi bi-check-circle-fill text-emerald-500 text-base"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm flex items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill text-red-500 text-base"></i> {{ session('error') }}
    </div>
@endif

{{-- Page Header --}}
<div class="mb-8 p-6 md:p-8 bg-gradient-to-r from-indigo-600 to-violet-600 rounded-3xl text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
    <div class="relative z-10">
        <h2 class="text-2xl md:text-3xl font-bold mb-1">Katalog Peralatan</h2>
        <p class="text-indigo-100 text-sm max-w-lg">Pilih alat yang dibutuhkan, tentukan jumlah dan tanggal pengembalian, lalu kirim pengajuan.</p>
    </div>
    <i class="bi bi-collection-fill absolute -bottom-6 -right-4 text-[8rem] text-white/10 pointer-events-none"></i>
</div>

{{-- Filter Bar --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6">
    <form action="{{ route('peminjam.katalog') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Cari Alat</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama alat..."
                    class="w-full pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Kategori</label>
            <select name="kategori_id" onchange="this.form.submit()"
                class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition">
                <i class="bi bi-search me-1"></i> Cari
            </button>
            @if($search || $kategoriId)
                <a href="{{ route('peminjam.katalog') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Form Peminjaman --}}
<form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST" id="form-pinjam">
    @csrf

    {{-- Tanggal --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
            <i class="bi bi-calendar3 text-indigo-500"></i> Tentukan Periode Peminjaman
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Rencana Tanggal Kembali</label>
                <input type="date" name="tgl_kembali_plan"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>
        </div>
    </div>

    {{-- Keranjang Pilihan (floating counter) --}}
    <div id="cart-bar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-4 text-sm font-semibold">
        <i class="bi bi-cart3 text-indigo-400 text-lg"></i>
        <span id="cart-count">0</span> alat dipilih
        <button type="submit"
            class="ml-2 px-4 py-1.5 bg-indigo-500 hover:bg-indigo-400 text-white text-xs font-bold rounded-xl transition">
            <i class="bi bi-send-fill me-1"></i> Ajukan
        </button>
    </div>

    {{-- Grid Cards Alat --}}
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-700">
            Alat Tersedia
            <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ count($alats) }}</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        @forelse($alats as $alat)
            <div class="alat-card bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200 flex flex-col overflow-hidden"
                 id="card-{{ $alat->id }}">

                {{-- Gambar / Placeholder --}}
                @if($alat->gambar)
                    <div class="h-36 bg-slate-100 overflow-hidden">
                        <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="h-36 bg-gradient-to-br from-indigo-50 to-violet-50 flex items-center justify-center text-indigo-300 text-5xl">
                        <i class="bi bi-tools"></i>
                    </div>
                @endif

                <div class="p-4 flex flex-col flex-1">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                            <h4 class="font-bold text-slate-800 text-sm mt-1.5">{{ $alat->nama_alat }}</h4>
                        </div>
                        @php
                            $kondisiClass = match(true) {
                                strtolower($alat->status_kondisi) === 'baik' => 'bg-emerald-50 text-emerald-700',
                                str_contains(strtolower($alat->status_kondisi), 'ringan') => 'bg-amber-50 text-amber-700',
                                default => 'bg-red-50 text-red-700'
                            };
                        @endphp
                        <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $kondisiClass }}">
                            {{ $alat->status_kondisi }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 mb-4">
                        <i class="bi bi-box-seam text-slate-400 text-xs"></i>
                        <span class="text-xs text-slate-500">Stok tersedia:</span>
                        <span class="text-xs font-bold {{ $alat->stok == 0 ? 'text-red-500' : 'text-slate-800' }}">{{ $alat->stok }} unit</span>
                    </div>

                    {{-- Pilih & Jumlah --}}
                    <div class="mt-auto pt-3 border-t border-slate-100">
                        @if($alat->stok > 0)
                            <label class="flex items-center gap-3 cursor-pointer group" for="alat-{{ $alat->id }}">
                                <div class="relative">
                                    <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}"
                                           id="alat-{{ $alat->id }}"
                                           onchange="toggleCard(this)"
                                           class="sr-only peer">
                                    <div class="w-5 h-5 rounded-lg border-2 border-slate-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex items-center justify-center transition">
                                        <i class="bi bi-check text-white text-xs hidden peer-checked:block" id="check-{{ $alat->id }}"></i>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-slate-600 group-hover:text-indigo-600 transition flex-1">Pilih Alat Ini</span>
                                <input type="number" name="jumlah[]" value="1" min="1" max="{{ $alat->stok }}"
                                       id="jumlah-{{ $alat->id }}"
                                       onclick="event.preventDefault()"
                                       class="w-20 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-center focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </label>
                        @else
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <i class="bi bi-x-circle-fill text-red-400"></i>
                                <span>Stok habis, tidak dapat dipinjam</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-3xl text-center text-slate-400 border border-slate-100">
                <i class="bi bi-search text-4xl block mb-3 text-slate-200"></i>
                <p class="text-sm font-medium">Alat tidak ditemukan.</p>
                @if($search || $kategoriId)
                    <a href="{{ route('peminjam.katalog') }}" class="mt-3 inline-block text-xs text-indigo-600 font-semibold hover:underline">Lihat semua alat</a>
                @endif
            </div>
        @endforelse
    </div>

    @if(count($alats) > 0)
        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-2xl shadow-lg shadow-indigo-200 transition">
                <i class="bi bi-send-fill"></i> Kirim Pengajuan Peminjaman
            </button>
        </div>
    @endif
</form>

<script>
function toggleCard(checkbox) {
    const card = document.getElementById('card-' + checkbox.value);
    const checkIcon = document.getElementById('check-' + checkbox.value);
    if (checkbox.checked) {
        card.classList.add('ring-2', 'ring-indigo-500', 'border-indigo-300');
        if (checkIcon) checkIcon.classList.remove('hidden');
    } else {
        card.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-300');
        if (checkIcon) checkIcon.classList.add('hidden');
    }
    updateCartBar();
}

function updateCartBar() {
    const checked = document.querySelectorAll('input[name="alat_id[]"]:checked').length;
    const bar = document.getElementById('cart-bar');
    const count = document.getElementById('cart-count');
    count.textContent = checked;
    if (checked > 0) {
        bar.classList.remove('hidden');
        bar.classList.add('flex');
    } else {
        bar.classList.add('hidden');
        bar.classList.remove('flex');
    }
}
</script>
@endsection
