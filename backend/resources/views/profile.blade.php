@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-800">Profil Saya</h2>
        <p class="text-sm text-slate-400 mt-1">Kelola informasi dan foto profil kamu.</p>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-sm flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm flex items-center gap-3">
            <i class="bi bi-exclamation-circle-fill text-lg"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Validasi Error --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Foto Profil --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 mb-6">
        <h3 class="text-base font-bold text-slate-700 mb-5">Foto Profil</h3>

        <div class="flex flex-col sm:flex-row items-center gap-6">
            {{-- Preview Foto --}}
            <div class="shrink-0">
                @if($user->foto_profile)
                    <img id="preview"
                         src="{{ asset('storage/' . $user->foto_profile) }}"
                         alt="Foto Profil"
                         onclick="openLightbox()"
                         class="w-28 h-28 rounded-full object-cover ring-4 ring-indigo-100 shadow cursor-pointer hover:opacity-90 transition">
                @else
                    <div id="preview-placeholder"
                         class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-4xl ring-4 ring-indigo-50 shadow">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <img id="preview" src="" alt="Preview" onclick="openLightbox()" class="w-28 h-28 rounded-full object-cover ring-4 ring-indigo-100 shadow hidden cursor-pointer hover:opacity-90 transition">
                @endif
            </div>

            {{-- Form Upload --}}
            <div class="flex-1 w-full">
                <form action="{{ route('profile.updateFoto') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <label class="block text-xs font-semibold text-slate-500 mb-2">
                        Pilih foto baru (jpg, jpeg, png, webp — maks. 2MB)
                    </label>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="file"
                               name="foto_profile"
                               id="foto_profile"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               onchange="openCropper(event)"
                               class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl p-1">

                        <button type="submit"
                                class="shrink-0 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm shadow-indigo-200">
                            <i class="bi bi-upload me-1"></i> Simpan
                        </button>
                    </div>
                </form>

                {{-- Tombol Hapus Foto --}}
                @if($user->foto_profile)
                    <form action="{{ route('profile.destroyFoto') }}" method="POST" class="mt-3"
                          onsubmit="return confirm('Yakin ingin menghapus foto profil?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-xs font-semibold text-red-500 hover:text-red-700 transition flex items-center gap-1">
                            <i class="bi bi-trash3"></i> Hapus foto profil
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Card Info Akun --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-700 mb-5">Informasi Akun</h3>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 py-3 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-400 w-32 shrink-0">Nama</span>
                <span class="text-sm font-semibold text-slate-700">{{ $user->name }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 py-3 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-400 w-32 shrink-0">Email</span>
                <span class="text-sm text-slate-700">{{ $user->email }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 py-3 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-400 w-32 shrink-0">Role</span>
                <span class="text-sm capitalize">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold
                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : ($user->role === 'petugas' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700') }}">
                        {{ $user->role }}
                    </span>
                </span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 py-3 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-400 w-32 shrink-0">No. HP</span>
                <span class="text-sm text-slate-700">{{ $user->no_hp ?? '-' }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 py-3">
                <span class="text-xs font-semibold text-slate-400 w-32 shrink-0">Alamat</span>
                <span class="text-sm text-slate-700">{{ $user->alamat ?? '-' }}</span>
            </div>
        </div>
    </div>

</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

{{-- Modal Cropper --}}
<div id="cropperModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-5">
        <h3 class="text-sm font-bold text-slate-700 mb-3">Atur Foto Profil</h3>
        <div class="w-full h-72 bg-slate-100 rounded-xl overflow-hidden">
            <img id="cropperImage" src="" class="max-w-full block">
        </div>
        <div class="flex items-center gap-3 mt-4">
            <i class="bi bi-zoom-out text-slate-400"></i>
            <input type="range" id="zoomRange" min="0" max="2" step="0.01" value="0" class="flex-1">
            <i class="bi bi-zoom-in text-slate-400"></i>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button type="button" id="cropperCancel" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-lg">Batal</button>
            <button type="button" id="cropperConfirm" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg">Gunakan Foto</button>
        </div>
    </div>
</div>

{{-- Lightbox Lihat Foto --}}
<div id="lightboxModal" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center p-4" onclick="closeLightbox(event)">
    <button type="button" onclick="closeLightbox(event)" class="absolute top-5 right-5 text-white text-3xl leading-none">&times;</button>
    <img id="lightboxImage" src="" alt="Foto Profil" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl">
</div>

<script>
(function () {
    const input = document.getElementById('foto_profile');
    const preview = document.getElementById('preview');
    const placeholder = document.getElementById('preview-placeholder');
    const modal = document.getElementById('cropperModal');
    const image = document.getElementById('cropperImage');
    const zoomRange = document.getElementById('zoomRange');
    const btnCancel = document.getElementById('cropperCancel');
    const btnConfirm = document.getElementById('cropperConfirm');
    let cropper = null;
    let originalName = 'foto.jpg';

    window.openCropper = function (event) {
        const file = event.target.files[0];
        if (!file) return;
        originalName = file.name;

        const reader = new FileReader();
        reader.onload = function (ev) {
            image.src = ev.target.result;
            modal.classList.remove('hidden');
            modal.classList.add('flex');

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
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        input.value = '';
        if (cropper) { cropper.destroy(); cropper = null; }
    });

    btnConfirm.addEventListener('click', function () {
        if (!cropper) return;
        cropper.getCroppedCanvas({ width: 500, height: 500 }).toBlob(function (blob) {
            const croppedFile = new File([blob], originalName, { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(croppedFile);
            input.files = dt.files;

            const url = URL.createObjectURL(blob);
            preview.src = url;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            cropper.destroy();
            cropper = null;
        }, 'image/jpeg', 0.9);
    });

    window.openLightbox = function () {
        if (!preview.src || preview.classList.contains('hidden')) return;
        document.getElementById('lightboxImage').src = preview.src;
        const lb = document.getElementById('lightboxModal');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
    };

    window.closeLightbox = function (e) {
        if (e) e.stopPropagation();
        const lb = document.getElementById('lightboxModal');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
    };
})();
</script>
@endsection
