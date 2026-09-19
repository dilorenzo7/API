@extends('layouts.app')

@section('title', 'Edit User - Panel Admin')
@section('header-title', 'Edit Data Pengguna')

@section('content')
<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Password Baru 
                <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah password)</span>
            </label>
            <input type="password" name="password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Role / Hak Akses</label>
            <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="peminjam" {{ $user->role == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                <option value="petugas" {{ $user->role == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>


        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Foto Profil</label>
            @if($user->foto_profile)
                <div class="flex items-center gap-3 mb-2">
                    <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="Foto" class="w-14 h-14 rounded-full object-cover ring-2 ring-slate-100">
                    <span class="text-xs text-gray-400">Foto saat ini</span>
                </div>
            @endif
            <input type="file" name="foto_profile" id="foto_profile" accept="image/jpg,image/jpeg,image/png,image/webp"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah foto. jpg, jpeg, png, webp — maks. 2MB</p>
            @error('foto_profile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">No. HP</label>
            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
            @if($user->alamat)
                <p class="text-xs text-gray-400 mb-2">Alamat saat ini: <span class="text-gray-600">{{ $user->alamat }}</span></p>
            @endif
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-2">
                <select id="provinsi" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Provinsi</option>
                </select>
                <select id="kabupaten" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="">Pilih Kota/Kabupaten</option>
                </select>
                <select id="kecamatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>
            <textarea id="alamat_detail" placeholder="Detail alamat baru (kosongkan jika tidak ingin mengubah)..." rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            <input type="hidden" name="alamat" id="alamat">
            @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.user.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui</button>
        </div>
    </form>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

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
            <button type="button" id="cropperConfirm" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg">Gunakan Foto</button>
        </div>
    </div>
</div>

<script>
(function () {
    const input = document.getElementById('foto_profile');
    const modal = document.getElementById('cropperModal');
    const image = document.getElementById('cropperImage');
    const zoomRange = document.getElementById('zoomRange');
    const btnCancel = document.getElementById('cropperCancel');
    const btnConfirm = document.getElementById('cropperConfirm');
    let cropper = null;
    let originalName = 'foto.jpg';

    input.addEventListener('change', function (e) {
        const file = e.target.files[0];
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
    });

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
        cropper.getCroppedCanvas({ width: 500, height: 500 }).toBlob(function (blob) {
            const croppedFile = new File([blob], originalName, { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(croppedFile);
            input.files = dt.files;

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            cropper.destroy();
            cropper = null;
        }, 'image/jpeg', 0.9);
    });
})();
</script>


<script>
(function () {
    const provinsiEl = document.getElementById('provinsi');
    const kabupatenEl = document.getElementById('kabupaten');
    const kecamatanEl = document.getElementById('kecamatan');
    const detailEl = document.getElementById('alamat_detail');
    const alamatHidden = document.getElementById('alamat');
    const form = provinsiEl.closest('form');
    const BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    fetch(BASE + '/provinces.json').then(r => r.json()).then(data => {
        data.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.name; opt.dataset.id = p.id;
            opt.textContent = p.name;
            provinsiEl.appendChild(opt);
        });
    });

    provinsiEl.addEventListener('change', function () {
        kabupatenEl.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kabupatenEl.disabled = true;
        kecamatanEl.disabled = true;
        const id = this.options[this.selectedIndex].dataset.id;
        if (!id) return;
        fetch(BASE + '/regencies/' + id + '.json').then(r => r.json()).then(data => {
            data.forEach(k => {
                const opt = document.createElement('option');
                opt.value = k.name; opt.dataset.id = k.id;
                opt.textContent = k.name;
                kabupatenEl.appendChild(opt);
            });
            kabupatenEl.disabled = false;
        });
    });

    kabupatenEl.addEventListener('change', function () {
        kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kecamatanEl.disabled = true;
        const id = this.options[this.selectedIndex].dataset.id;
        if (!id) return;
        fetch(BASE + '/districts/' + id + '.json').then(r => r.json()).then(data => {
            data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.name;
                opt.textContent = d.name;
                kecamatanEl.appendChild(opt);
            });
            kecamatanEl.disabled = false;
        });
    });

    form.addEventListener('submit', function () {
        const parts = [detailEl.value.trim(), kecamatanEl.value, kabupatenEl.value, provinsiEl.value].filter(Boolean);
        alamatHidden.value = parts.join(', ');
    });
})();
</script>

@endsection