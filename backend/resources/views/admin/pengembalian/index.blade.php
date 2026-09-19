@extends('layouts.app')
@section('title', 'Kelola Pengembalian')

@section('content')

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-2">
        <i class="bi bi-check-circle-fill text-emerald-500"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm flex items-center gap-2">
        <i class="bi bi-exclamation-circle-fill text-red-500"></i> {{ session('error') }}
    </div>
@endif

{{-- Page Header --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Pengembalian Alat</h2>
        <p class="text-sm text-slate-400 mt-1">Daftar peminjaman aktif yang siap diproses pengembaliannya.</p>
    </div>
    @php $terlambat = $peminjamans->filter(fn($p) => now()->gt($p->tgl_kembali_plan))->count(); @endphp
    @if($terlambat > 0)
        <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 text-sm font-semibold rounded-2xl border border-red-200">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ $terlambat }} Terlambat
        </span>
    @endif
</div>

{{-- Table Card --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm font-semibold text-slate-700">
            Alat Sedang Dipinjam
            <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ $peminjamans->total() }}</span>
        </p>
        <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex gap-2">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                    class="pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 w-56">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700 transition">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-5 border-b border-slate-100">Peminjam</th>
                    <th class="py-3 px-5 border-b border-slate-100">Alat Dipinjam</th>
                    <th class="py-3 px-5 border-b border-slate-100">Tgl Pinjam</th>
                    <th class="py-3 px-5 border-b border-slate-100">Rencana Kembali</th>
                    <th class="py-3 px-5 border-b border-slate-100">Status</th>
                    <th class="py-3 px-5 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamans as $peminjaman)
                    @php $isTerlambat = now()->gt($peminjaman->tgl_kembali_plan); @endphp
                    <tr class="hover:bg-slate-50 transition border-b border-slate-50 {{ $isTerlambat ? 'bg-red-50/30' : '' }}">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($peminjaman->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $peminjaman->user->name ?? 'User Dihapus' }}</p>
                                    <p class="text-xs text-slate-400">{{ $peminjaman->user->no_hp ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <ul class="space-y-1">
                                @foreach($peminjaman->detailPinjam as $detail)
                                    <li class="flex items-center gap-2 text-sm">
                                        <span class="text-slate-700">{{ $detail->alat->nama_alat ?? '-' }}</span>
                                        <span class="bg-slate-100 text-slate-500 text-xs px-1.5 py-0.5 rounded-md">{{ $detail->jumlah }} pcs</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="py-4 px-5 text-sm text-slate-600">
                            {{ $peminjaman->tgl_pinjam->format('d M Y') }}
                        </td>
                        <td class="py-4 px-5">
                            <span class="text-sm font-semibold {{ $isTerlambat ? 'text-red-600' : 'text-slate-700' }}">
                                {{ $peminjaman->tgl_kembali_plan->format('d M Y') }}
                            </span>
                            @if($isTerlambat)
                                <span class="block text-xs text-red-400 mt-0.5">
                                    {{ now()->diffInDays($peminjaman->tgl_kembali_plan) }} hari terlambat
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-5">
                            @if($isTerlambat)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                    <i class="bi bi-clock-fill"></i> Terlambat
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                                    <i class="bi bi-box-seam"></i> Dipinjam
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            <a href="{{ route('admin.pengembalian.create', $peminjaman->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm shadow-indigo-200">
                                <i class="bi bi-arrow-return-left"></i> Proses Kembali
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-slate-400">
                            <i class="bi bi-check-circle text-4xl block mb-3 text-emerald-300"></i>
                            <p class="text-sm font-medium text-slate-500">Tidak ada alat yang sedang dipinjam.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($peminjamans->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $peminjamans->links() }}</div>
    @endif
</div>

@endsection
