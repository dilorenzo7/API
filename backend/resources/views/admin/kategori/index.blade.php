@extends('layouts.app')
@section('title', 'Kelola Kategori')

@section('content')

{{-- Alerts --}}
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center justify-between gap-3">
        <span class="flex items-center gap-2"><i class="bi bi-check-circle-fill text-emerald-500"></i> {{ session('success') }}</span>
        <button @click="show = false" class="text-emerald-400 hover:text-emerald-700"><i class="bi bi-x-lg"></i></button>
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
        <h2 class="text-2xl font-bold text-slate-800">Kategori Alat</h2>
        <p class="text-sm text-slate-400 mt-1">Kelola pengelompokan jenis peralatan laboratorium.</p>
    </div>
    <a href="{{ route('admin.kategori.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-2xl transition shadow-md shadow-indigo-200">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
    </a>
</div>

{{-- Search & Table Card --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

    {{-- Toolbar --}}
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm font-semibold text-slate-700">
            Daftar Kategori
            <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ $kategoris->total() }}</span>
        </p>
        <form action="{{ route('admin.kategori.index') }}" method="GET" class="flex gap-2">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
                    class="pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 w-56">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700 transition">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-5 border-b border-slate-100 w-16 text-center">No</th>
                    <th class="py-3 px-5 border-b border-slate-100">Nama Kategori</th>
                    <th class="py-3 px-5 border-b border-slate-100">Jumlah Alat</th>
                    <th class="py-3 px-5 border-b border-slate-100">Dibuat</th>
                    <th class="py-3 px-5 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoris as $index => $kategori)
                    <tr class="hover:bg-slate-50 transition border-b border-slate-50 group">
                        <td class="py-4 px-5 text-center text-xs text-slate-400">{{ $kategoris->firstItem() + $index }}</td>
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                    <i class="bi bi-tags-fill"></i>
                                </div>
                                <span class="font-semibold text-slate-800 text-sm">{{ $kategori->nama_kategori }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">
                                {{ $kategori->alat->count() }} alat
                            </span>
                        </td>
                        <td class="py-4 px-5 text-xs text-slate-400">
                            {{ $kategori->created_at->format('d M Y') }}
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.kategori.edit', $kategori->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-xl transition">
                                    <i class="bi bi-pencil-fill text-[10px]"></i> Edit
                                </a>
                                <form action="{{ route('admin.kategori.destroy', $kategori->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus kategori \'{{ $kategori->nama_kategori }}\'?')">
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
                            <i class="bi bi-tags text-4xl block mb-3 text-slate-200"></i>
                            <p class="text-sm">Belum ada data kategori.</p>
                            <a href="{{ route('admin.kategori.create') }}" class="mt-3 inline-block text-xs text-indigo-600 font-semibold hover:underline">+ Tambah sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kategoris->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $kategoris->links() }}</div>
    @endif
</div>

@endsection
