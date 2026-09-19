@extends('layouts.app')
@section('title', 'Kelola User')

@section('content')

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-2">
        <i class="bi bi-check-circle-fill text-emerald-500"></i> {{ session('success') }}
    </div>
@endif

{{-- Page Header --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Kelola Pengguna</h2>
        <p class="text-sm text-slate-400 mt-1">Manajemen akun dan hak akses pengguna sistem.</p>
    </div>
    <a href="{{ route('admin.user.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-2xl transition shadow-md shadow-indigo-200">
        <i class="bi bi-person-plus-fill"></i> Tambah User
    </a>
</div>

{{-- Stats Role --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center"><i class="bi bi-shield-fill-check"></i></div>
        <div><p class="text-xs text-slate-400">Admin</p><p class="font-bold text-slate-800">{{ $users->where('role','admin')->count() }}</p></div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="bi bi-person-badge-fill"></i></div>
        <div><p class="text-xs text-slate-400">Petugas</p><p class="font-bold text-slate-800">{{ $users->where('role','petugas')->count() }}</p></div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="bi bi-people-fill"></i></div>
        <div><p class="text-xs text-slate-400">Peminjam</p><p class="font-bold text-slate-800">{{ $users->where('role','peminjam')->count() }}</p></div>
    </div>
</div>

{{-- Table Card --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm font-semibold text-slate-700">
            Daftar Pengguna
            <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ $users->total() }}</span>
        </p>
        <form action="{{ route('admin.user.index') }}" method="GET" class="flex gap-2">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, email, role..."
                    class="pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 w-60">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700 transition">Cari</button>
            @if($search ?? false)
                <a href="{{ route('admin.user.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-5 border-b border-slate-100">Pengguna</th>
                    <th class="py-3 px-5 border-b border-slate-100">Email</th>
                    <th class="py-3 px-5 border-b border-slate-100">Role</th>
                    <th class="py-3 px-5 border-b border-slate-100">No. HP</th>
                    <th class="py-3 px-5 border-b border-slate-100">Bergabung</th>
                    <th class="py-3 px-5 border-b border-slate-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition border-b border-slate-50">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                @if($user->foto_profile)
                                    <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="{{ $user->name }}"
                                         class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-100">
                                @else
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm
                                        {{ $user->role === 'admin' ? 'bg-violet-100 text-violet-700' : ($user->role === 'petugas' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $user->name }}</p>
                                    @if($user->alamat)
                                        <p class="text-xs text-slate-400 truncate max-w-[160px]">{{ $user->alamat }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5 text-sm text-slate-600">{{ $user->email }}</td>
                        <td class="py-4 px-5">
                            @php
                                $roleClass = match($user->role) {
                                    'admin'   => 'bg-violet-100 text-violet-700',
                                    'petugas' => 'bg-blue-100 text-blue-700',
                                    default   => 'bg-emerald-100 text-emerald-700',
                                };
                                $roleIcon = match($user->role) {
                                    'admin'   => 'bi-shield-fill-check',
                                    'petugas' => 'bi-person-badge-fill',
                                    default   => 'bi-person-fill',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $roleClass }}">
                                <i class="bi {{ $roleIcon }}"></i> {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-sm text-slate-600">{{ $user->no_hp ?? '-' }}</td>
                        <td class="py-4 px-5 text-xs text-slate-400">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-4 px-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.user.edit', $user->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-xl transition">
                                    <i class="bi bi-pencil-fill text-[10px]"></i> Edit
                                </a>
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus user \'{{ $user->name }}\'?')">
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
                        <td colspan="6" class="py-16 text-center text-slate-400">
                            <i class="bi bi-people text-4xl block mb-3 text-slate-200"></i>
                            <p class="text-sm">Belum ada pengguna.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $users->links() }}</div>
    @endif
</div>

@endsection
