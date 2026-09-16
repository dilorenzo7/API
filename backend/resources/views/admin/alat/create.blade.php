@extends('layouts.app')

@section('title', 'Tambah Kategori - Panel Admin')

@section('content')
    <!-- Container Utama: Menempatkan Form Tepat di Tengah Halaman -->
    <div class="min-h-[75vh] flex flex-col justify-center items-center px-4">
        
        <!-- Header / Breadcrumb Singkat -->
        <div class="w-full max-w-xl mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Tambah Kategori Alat</h2>
                <p class="text-xs text-slate-400">Tambahkan kategori baru untuk mengelompokkan peralatan.</p>
            </div>
            <a href="{{ route('admin.kategori.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Card Form di Tengah -->
        <div class="w-full max-w-xl bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8">
            <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Input Nama Kategori -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Kategori <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="bi bi-tag-fill"></i>
                        </span>
                        <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" required 
                               placeholder="Contoh: Jaringan, Mikrokontroler, Power Tools"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    @error('nama_kategori')
                        <p class="mt-1 text-xs text-rose-500 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.kategori.index') }}"
                       class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition flex items-center gap-1.5">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-600/30 transition flex items-center gap-1.5">
                        <i class="bi bi-check-lg"></i> Simpan
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection