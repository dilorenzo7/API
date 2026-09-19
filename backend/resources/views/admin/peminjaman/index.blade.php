@extends('layouts.app')
@section('title', 'Kelola Peminjaman')

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
        <h2 class="text-2xl font-bold text-slate-800">Transaksi Peminjaman</h2>
        <p class="text-sm text-slate-400 mt-1">Kelola semua data transaksi peminjaman peralatan.</p>
    </div>
    <a href="{{ route('admin.peminjaman.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-2xl transition shadow-md shadow-indigo-200">
        <i class="bi bi-plus-lg"></i> Tambah Peminjaman
    </a>
</div>

{{-- Table Card --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm font-semibold text-slate-700">
            Semua Transaksi
            <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ $peminjamans->total() }}</span>
        </p>
        <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="flex gap-2">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam / status..."
                    class="pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 w-64">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700 transition">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.peminjaman.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition">Reset</a>
            @endif
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
                    <tr class="hover:bg-slate-50 transition border-b border-slate-50 align-top">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($peminjaman->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $peminjaman->user->name ?? 'User Dihapus' }}</p>
                                    <p class="text-xs text-slate-400">{{ $peminjaman->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <ul class="space-y-1">
                                @foreach($peminjaman->detailPinjam as $detail)
                                    <li class="flex items-center gap-2 text-sm">
                                        <span class="text-slate-700">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                        <span class="bg-slate-100 text-slate-500 text-xs px-1.5 py-0.5 rounded-md">{{ $detail->jumlah }} pcs</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="py-4 px-5 text-sm text-slate-600">
                            {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}
                        </td>
                        <td class="py-4 px-5 text-sm font-semibold text-slate-700">
                            {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}
                        </td>
                        <td class="py-4 px-5">
                            @php
                                $statusConfig = [
                                    'diajukan'     => ['bg-amber-100 text-amber-700',  'bi-hourglass-split', 'Diajukan'],
                                    'dipinjam'     => ['bg-indigo-100 text-indigo-700', 'bi-box-seam',        'Dipinjam'],
                                    'dikembalikan' => ['bg-emerald-100 text-emerald-700','bi-check-circle',  'Dikembalikan'],
                                    'telat'        => ['bg-red-100 text-red-700',       'bi-clock-fill',     'Telat'],
                                ];
                                [$cls, $icon, $label] = $statusConfig[$peminjaman->status] ?? ['bg-slate-100 text-slate-600', 'bi-dash', ucfirst($peminjaman->status)];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $cls }}">
                                <i class="bi {{ $icon }}"></i> {{ $label }}
                            </span>
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex flex-col items-end gap-2">
                                <form action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <select name="status" onchange="this.form.submit()"
                                        class="text-xs border border-slate-200 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white text-slate-700 cursor-pointer">
                                        <option value="diajukan"     {{ $peminjaman->status == 'diajukan'     ? 'selected' : '' }}>Diajukan</option>
                                        <option value="dipinjam"     {{ $peminjaman->status == 'dipinjam'     ? 'selected' : '' }}>Dipinjam</option>
                                        <option value="dikembalikan" {{ $peminjaman->status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                        <option value="telat"        {{ $peminjaman->status == 'telat'        ? 'selected' : '' }}>Telat</option>
                                    </select>
                                </form>
                                <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus data peminjaman ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-xl transition">
                                        <i class="bi bi-trash3-fill text-[10px]"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-slate-400">
                            <i class="bi bi-journal-x text-4xl block mb-3 text-slate-200"></i>
                            <p class="text-sm">Belum ada data peminjaman.</p>
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
