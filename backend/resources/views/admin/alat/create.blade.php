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
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/jpeg,image/png,image/jpg" onchange="openCropperAlat(event)">
                            <small class="text-muted d-block mt-1">Format gambar: JPG, JPEG, PNG (Maksimal 2MB).</small>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="previewWrap" class="mt-2 hidden">
                                <img id="previewAlat" src="" class="rounded border" style="width:96px;height:96px;object-fit:cover;">
                                <span id="previewFileName" class="text-muted small ms-2"></span>
                            </div>
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

<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<div id="cropperModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4" style="position:fixed;">
    <div class="bg-white rounded-2xl shadow-xl p-5" style="max-width:420px;width:100%;">
        <h3 class="fw-bold mb-3" style="font-size:14px;">Atur Foto Alat</h3>
        <div style="width:100%;height:280px;background:#f1f5f9;border-radius:12px;overflow:hidden;">
            <img id="cropperImage" src="" style="max-width:100%;display:block;">
        </div>
        <div class="d-flex align-items-center gap-2 mt-3">
            <i class="bi bi-zoom-out text-muted"></i>
            <input type="range" id="zoomRange" min="0" max="2" step="0.01" value="0" class="flex-fill">
            <i class="bi bi-zoom-in text-muted"></i>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" id="cropperCancel" class="btn btn-secondary btn-sm">Batal</button>
            <button type="button" id="cropperConfirm" class="btn btn-primary btn-sm">Gunakan Foto</button>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('cropperModal');
    const image = document.getElementById('cropperImage');
    const zoomRange = document.getElementById('zoomRange');
    const btnCancel = document.getElementById('cropperCancel');
    const btnConfirm = document.getElementById('cropperConfirm');
    let cropper = null;
    let originalName = 'foto.jpg';
    let inputEl = null;

    window.openCropperAlat = function (event) {
        inputEl = event.target;
        const file = inputEl.files[0];
        if (!file) return;
        originalName = file.name;

        const reader = new FileReader();
        reader.onload = function (ev) {
            image.src = ev.target.result;
            modal.classList.remove('hidden');
            modal.style.display = 'flex';

            if (cropper) cropper.destroy();
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                background: false,
                zoomOnWheel: true,
                ready: function () { zoomRange.value = 0; }
            });
        };
        reader.readAsDataURL(file);
    };

    zoomRange.addEventListener('input', function () {
        if (cropper) cropper.zoomTo(parseFloat(this.value));
    });

    btnCancel.addEventListener('click', function () {
        modal.style.display = 'none';
        if (inputEl) inputEl.value = '';
        if (cropper) { cropper.destroy(); cropper = null; }
    });

    btnConfirm.addEventListener('click', function () {
        if (!cropper || !inputEl) return;
        cropper.getCroppedCanvas({ width: 600, height: 600 }).toBlob(function (blob) {
            const croppedFile = new File([blob], originalName, { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(croppedFile);
            inputEl.files = dt.files;

            const url = URL.createObjectURL(blob);
            const previewWrap = document.getElementById('previewWrap');
            document.getElementById('previewAlat').src = url;
            document.getElementById('previewFileName').textContent = originalName;
            previewWrap.classList.remove('hidden');

            modal.style.display = 'none';
            cropper.destroy();
            cropper = null;
        }, 'image/jpeg', 0.9);
    });
})();
</script>

@endsection