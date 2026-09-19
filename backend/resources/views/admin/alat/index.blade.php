@extends('layouts.app')
@section('title', 'Kelola Alat')

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
        <h2 class="text-2xl font-bold text-slate-800">Data Alat</h2>
        <p class="text-sm text-slate-400 mt-1">Kelola inventaris peralatan laboratorium.</p>
    </div>
    <a href="{{ route('admin.alat.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-2xl transition shadow-md shadow-indigo-200">
        <i class="bi bi-plus-lg"></i> Tambah Alat
    </a>
</div>

{{-- Table Card --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

    {{-- Toolbar --}}
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm font-semibold text-slate-700">
            Inventaris Alat
            <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ $alats->total() }}</span>
        </p>
        <form action="{{ route('admin.alat.index') }}" method="GET" class="flex gap-2">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat..."
                    class="pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 w-56">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700 transition">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.alat.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-5 border-b border-slate-100">Alat</th>
                    <th class="py-3 px-5 border-b border-slate-100">Kategori</th>
                    <th class="py-3 px-5 border-b border-slate-100">Stok</th>
                    <th class="py-3 px-5 border-b border-slate-100">Kondisi</th>
                    <th class="py-3 px-5 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alats as $alat)
                    <tr class="hover:bg-slate-50 transition border-b border-slate-50">
                        {{-- Gambar + Nama --}}
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                @if($alat->gambar)
                                    <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                                         class="w-12 h-12 object-cover rounded-2xl border border-slate-100 shadow-sm">
                                @else
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $alat->nama_alat }}</p>
                                    @if($alat->deskripsi)
                                        <p class="text-xs text-slate-400 truncate max-w-[180px]">{{ $alat->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full">
                                {{ $alat->kategori->nama_kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="py-4 px-5">
                            <span class="text-sm font-bold {{ $alat->stok == 0 ? 'text-red-500' : ($alat->stok <= 3 ? 'text-amber-500' : 'text-slate-800') }}">
                                {{ $alat->stok }}
                            </span>
                            <span class="text-xs text-slate-400"> unit</span>
                            @if($alat->stok == 0)
                                <span class="ml-1 px-1.5 py-0.5 bg-red-100 text-red-600 text-[10px] font-bold rounded">Habis</span>
                            @elseif($alat->stok <= 3)
                                <span class="ml-1 px-1.5 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-bold rounded">Sedikit</span>
                            @endif
                        </td>
                        <td class="py-4 px-5">
                            @php
                                $kondisi = strtolower($alat->status_kondisi);
                                $kondisiClass = match(true) {
                                    $kondisi === 'baik' => 'bg-emerald-50 text-emerald-700',
                                    str_contains($kondisi, 'ringan') => 'bg-amber-50 text-amber-700',
                                    default => 'bg-red-50 text-red-700'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $kondisiClass }}">
                                {{ $alat->status_kondisi }}
                            </span>
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.alat.edit', $alat->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-xl transition">
                                    <i class="bi bi-pencil-fill text-[10px]"></i> Edit
                                </a>
                                <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus alat \'{{ $alat->nama_alat }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-xl transition">
                                        <i class="bi bi-trash3-fill text-[10px]"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center text-slate-400">
                            <i class="bi bi-tools text-4xl block mb-3 text-slate-200"></i>
                            <p class="text-sm">Belum ada data alat.</p>
                            <a href="{{ route('admin.alat.create') }}" class="mt-3 inline-block text-xs text-indigo-600 font-semibold hover:underline">+ Tambah sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($alats->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $alats->links() }}</div>
    @endif
</div>

@endsection
