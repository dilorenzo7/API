@extends('layouts.app')

@section('content')
<!-- Import Bootstrap 5 CSS & Icons CDN khusus halaman ini -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container-fluid p-4">
    <div class="row justify-content-start">
        <div class="col-12 col-xl-10">
            <div class="card shadow-sm border-0 rounded-3">
                
                <!-- Header Card -->
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between py-3 px-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Data Alat
                    </h5>
                    <a href="{{ route('admin.alat.index') }}" class="btn btn-sm btn-light shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <!-- Body Card -->
                <div class="card-body p-4">

                    {{-- Notifikasi Error Server --}}
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.alat.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama Alat -->
                        <div class="mb-3">
                            <label for="nama_alat" class="form-label fw-semibold">Nama Alat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_alat') is-invalid @enderror" id="nama_alat" name="nama_alat" value="{{ old('nama_alat') }}" placeholder="Contoh: Proyektor Epson EB-X400" required>
                            @error('nama_alat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori Alat -->
                        <div class="mb-3">
                            <label for="kategori_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id" required>
                                <option value="" selected disabled>-- Pilih Kategori --</option>
                                @foreach($kategori as $item)
                                    <option value="{{ $item->id }}" {{ old('kategori_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Grid Stok & Kondisi -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stok" class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', 1) }}" min="0" placeholder="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="kondisi" class="form-label fw-semibold">Status Kondisi <span class="text-danger">*</span></label>
                                <select class="form-select @error('kondisi') is-invalid @enderror" id="kondisi" name="kondisi" required>
                                    <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Sedang" {{ old('kondisi') == 'Rusak Sedang' ? 'selected' : '' }}>Rusak Sedang</option>
                                    <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                                @error('kondisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi / Spesifikasi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan deskripsi singkat atau spesifikasi alat...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Upload Foto -->
                        <div class="mb-4">
                            <label for="foto" class="form-label fw-semibold">Foto Alat</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/jpeg,image/png,image/jpg">
                            <small class="text-muted d-block mt-1">Format gambar: JPG, JPEG, PNG (Maksimal 2MB).</small>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="reset" class="btn btn-secondary px-4">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Data
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection