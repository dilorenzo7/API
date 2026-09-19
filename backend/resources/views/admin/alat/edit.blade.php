@extends('layouts.app')

@section('title', 'Edit Alat - Panel Admin')
@section('header-title', 'Edit Data Alat')

@section('content')
    <div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        
        {{-- Notifikasi Error Server --}}
        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.alat.update', $alat->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Nama Alat -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="nama_alat">
                    Nama Alat <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_alat" id="nama_alat" value="{{ old('nama_alat', $alat->nama_alat) }}" required
                       class="w-full px-3 py-2 border @error('nama_alat') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nama_alat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori Alat -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="kategori_id">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="kategori_id" id="kategori_id" required 
                        class="w-full px-3 py-2 border @error('kategori_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori as $item)
                        <option value="{{ $item->id }}" {{ old('kategori_id', $alat->kategori_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid Stok & Status Kondisi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="stok">
                        Stok <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stok" id="stok" value="{{ old('stok', $alat->stok) }}" min="0" required
                           class="w-full px-3 py-2 border @error('stok') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('stok')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="kondisi">
                        Status Kondisi <span class="text-red-500">*</span>
                    </label>
                    <select name="kondisi" id="kondisi" required 
                            class="w-full px-3 py-2 border @error('kondisi') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Baik" {{ old('kondisi', $alat->status_kondisi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ old('kondisi', $alat->status_kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Sedang" {{ old('kondisi', $alat->status_kondisi) == 'Rusak Sedang' ? 'selected' : '' }}>Rusak Sedang</option>
                        <option value="Rusak Berat" {{ old('kondisi', $alat->status_kondisi) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('kondisi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="deskripsi">Deskripsi / Spesifikasi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                          class="w-full px-3 py-2 border @error('deskripsi') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Masukkan deskripsi singkat...">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Foto Alat -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="foto">
                    Foto Alat <span class="text-xs text-gray-400 font-normal">(Biarkan kosong jika tidak ingin mengubah foto)</span>
                </label>
                
                {{-- Preview Gambar Lama Jika Ada --}}
                @if($alat->gambar)
                    <div class="mb-2">
                        <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-20 h-20 object-cover rounded-lg border">
                    </div>
                @endif

                <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('foto')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.alat.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui Data</button>
            </div>
        </form>
    </div>
@endsection