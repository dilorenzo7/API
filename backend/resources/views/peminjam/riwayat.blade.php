@extends('layouts.app')
@section('title', 'Riwayat Peminjaman')

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
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Riwayat Peminjaman</h2>
        <p class="text-sm text-slate-400 mt-1">Pantau status semua pengajuan dan riwayat peminjaman kamu.</p>
    </div>
    <a href="{{ route('peminjam.katalog') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-2xl transition shadow-md shadow-indigo-200">
        <i class="bi bi-plus-lg"></i> Pinjam Alat Baru
    </a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6">
    <form action="{{ route('peminjam.riwayat') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Cari</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama alat..."
                    class="w-full pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Filter Status</label>
            <select name="status" onchange="this.form.submit()"
                class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Semua Status</option>
                <option value="diajukan"     {{ request('status') == 'diajukan'     ? 'selected' : '' }}>Diajukan</option>
                <option value="dipinjam"     {{ request('status') == 'dipinjam'     ? 'selected' : '' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Selesai</option>
                <option value="telat"        {{ request('status') == 'telat'        ? 'selected' : '' }}>Terlambat</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700 transition">Cari</button>
            @if(request('search') || request('status'))
                <a href="{{ route('peminjam.riwayat') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition flex items-center gap-1">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Riwayat Cards / Table --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100">
        <p class="text-sm font-semibold text-slate-700">
            Riwayat Transaksi
            @if(method_exists($riwayat, 'total'))
                <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ $riwayat->total() }}</span>
            @endif
        </p>
    </div>

    <div class="divide-y divide-slate-50">
        @forelse($riwayat as $item)
            @php
                $statusConfig = [
                    'diajukan'     => ['bg-amber-50 text-amber-700 border-amber-200',   'bi-hourglass-split',  'Menunggu Persetujuan', 'bg-amber-500'],
                    'dipinjam'     => ['bg-indigo-50 text-indigo-700 border-indigo-200', 'bi-box-seam',         'Sedang Dipinjam',      'bg-indigo-500'],
                    'dikembalikan' => ['bg-emerald-50 text-emerald-700 border-emerald-200','bi-check-circle-fill','Selesai',             'bg-emerald-500'],
                    'telat'        => ['bg-red-50 text-red-700 border-red-200',          'bi-clock-fill',       'Terlambat',            'bg-red-500'],
                ];
                [$sCls, $sIcon, $sLabel, $sDot] = $statusConfig[$item->status] ?? ['bg-slate-100 text-slate-600 border-slate-200', 'bi-dash', ucfirst($item->status), 'bg-slate-400'];
            @endphp

            <div class="p-5 hover:bg-slate-50/60 transition">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                    {{-- Kiri: Info Peminjaman --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-2xl {{ str_replace('text-', 'bg-', explode(' ', $sCls)[0]) }} flex items-center justify-center">
                                <i class="bi {{ $sIcon }} {{ explode(' ', $sCls)[1] }}"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Peminjaman #{{ $item->id }}</p>
                                <p class="text-sm font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}
                                    <span class="text-slate-400 font-normal mx-1">→</span>
                                    {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Daftar Alat --}}
                        <div class="flex flex-wrap gap-2 ml-13">
                            @foreach($item->detailPinjam as $detail)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-700 font-medium">
                                    <i class="bi bi-tools text-slate-400 text-[10px]"></i>
                                    {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                    <span class="text-slate-400">({{ $detail->jumlah }})</span>
                                </span>
                            @endforeach
                        </div>

                        {{-- Info Pengembalian --}}
                        @if($item->pengembalian)
                            <div class="mt-3 ml-13 flex items-center gap-4 text-xs text-slate-500">
                                <span><i class="bi bi-calendar-check me-1 text-emerald-500"></i>
                                    Dikembalikan: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($item->pengembalian->tgl_kembali)->format('d M Y') }}</strong>
                                </span>
                                @if($item->pengembalian->denda > 0)
                                    <span class="text-red-600 font-semibold">
                                        <i class="bi bi-cash me-1"></i>
                                        Denda: Rp {{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Kanan: Status + Aksi --}}
                    <div class="flex flex-col items-start md:items-end gap-3 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full border {{ $sCls }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sDot }}"></span>
                            {{ $sLabel }}
                        </span>

                        {{-- Tombol Batalkan (hanya jika diajukan) --}}
                        @if($item->status === 'diajukan')
                            <form action="{{ route('peminjam.riwayat') }}" method="POST"
                                  onsubmit="return confirm('Batalkan pengajuan peminjaman ini?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="peminjaman_id" value="{{ $item->id }}">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 rounded-xl transition border border-slate-200 hover:border-red-200">
                                    <i class="bi bi-x-circle"></i> Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400">
                <i class="bi bi-clock-history text-4xl block mb-3 text-slate-200"></i>
                <p class="text-sm font-medium text-slate-500">Belum ada riwayat peminjaman.</p>
                <a href="{{ route('peminjam.katalog') }}"
                   class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-xl hover:bg-indigo-700 transition">
                    <i class="bi bi-plus-lg"></i> Pinjam Sekarang
                </a>
            </div>
        @endforelse
    </div>

    @if(method_exists($riwayat, 'hasPages') && $riwayat->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $riwayat->links() }}</div>
    @endif
</div>

@endsection
