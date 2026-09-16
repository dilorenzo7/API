@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Peminjam')

@section('content')
    <!-- Header & Filter -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Riwayat Peminjaman</h2>
            <p class="text-xs text-slate-500">Pantau status pengajuan dan riwayat peminjaman barang Kamu di sini.</p>
        </div>

        <form action="{{ route('peminjam.riwayat') }}" method="GET" class="flex flex-wrap md:flex-nowrap gap-2">
            <!-- Search Kode Transaksi / Barang -->
            <div class="relative w-full md:w-56">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / kode..." 
                       class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
            
            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
            </select>
            
            @if(request('search') || request('status'))
                <a href="{{ route('peminjam.riwayat') }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200 transition flex items-center gap-1">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Alert Notifications -->
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

    <!-- Riwayat Table Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[11px] font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-5">Kode / Tanggal Pinjam</th>
                        <th class="py-3.5 px-5">Daftar Alat</th>
                        <th class="py-3.5 px-5">Rencana Kembali</th>
                        <th class="py-3.5 px-5">Tgl Dikembalikan</th>
                        <th class="py-3.5 px-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($riwayat as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Kode Transaksi & Tanggal -->
                            <td class="py-4 px-5 align-top">
                                <span class="font-bold text-slate-800 block">#{{ $item->kode_peminjaman ?? $item->id }}</span>
                                <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</span>
                            </td>

                            <!-- Barang yang Dipinjam -->
                            <td class="py-4 px-5 align-top">
                                <ul class="space-y-1">
                                    @foreach($item->detailPinjam as $detail)
                                        <li class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            <span class="font-medium text-slate-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-[10px] text-slate-400 font-semibold">({{ $detail->jumlah }} unit)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <!-- Tanggal Rencana Kembali -->
                            <td class="py-4 px-5 align-top font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') }}
                            </td>

                            <!-- Tanggal Realisasi Kembali -->
                            <td class="py-4 px-5 align-top">
                                @if($item->tgl_kembali_real)
                                    <span class="text-slate-600 font-medium">{{ \Carbon\Carbon::parse($item->tgl_kembali_real)->format('d M Y') }}</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Badge Status -->
                            <td class="py-4 px-5 align-top text-center">
                                @if($item->status == 'diajukan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Diajukan
                                    </span>
                                @elseif($item->status == 'dipinjam')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Dipinjam
                                    </span>
                                @elseif($item->status == 'dikembalikan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                    </span>
                                @elseif($item->status == 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-200 text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Terlambat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
                                Belum ada riwayat peminjaman yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($riwayat, 'hasPages') && $riwayat->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
@endsection