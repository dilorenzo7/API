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
                         class="w-28 h-28 rounded-full object-cover ring-4 ring-indigo-100 shadow">
                @else
                    <div id="preview-placeholder"
                         class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-4xl ring-4 ring-indigo-50 shadow">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <img id="preview" src="" alt="Preview" class="w-28 h-28 rounded-full object-cover ring-4 ring-indigo-100 shadow hidden">
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
                               onchange="previewFoto(event)"
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

{{-- Script Preview Foto Sebelum Upload --}}
<script>
    function previewFoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById('preview');
            const placeholder = document.getElementById('preview-placeholder');

            preview.src = e.target.result;
            preview.classList.remove('hidden');

            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
