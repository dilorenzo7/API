@extends('layouts.app')
@section('title', 'Log Aktivitas')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Log Aktivitas</h2>
        <p class="text-sm text-slate-400 mt-1">Riwayat seluruh aksi pengguna pada sistem.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition self-start md:self-auto">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

{{-- Card --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm font-semibold text-slate-700">
            Semua Aktivitas
            <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ $logs->total() }}</span>
        </p>
        <form action="{{ route('admin.log.index') }}" method="GET" class="flex gap-2">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau aktivitas..."
                    class="pl-8 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 w-64">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700 transition">Cari</button>
            @if($search)
                <a href="{{ route('admin.log.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition flex items-center gap-1">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="divide-y divide-slate-50">
        @forelse($logs as $log)
            <div class="flex items-start gap-4 px-5 py-3.5 hover:bg-slate-50/60 transition">
                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                    {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                </div>
                {{-- Konten --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-700 leading-relaxed">
                        <span class="font-semibold text-slate-900">{{ $log->user->name ?? 'Sistem' }}</span>
                        — {{ $log->aktivitas }}
                    </p>
                    <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                        <i class="bi bi-clock"></i>
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400">
                <i class="bi bi-inbox text-4xl block mb-3 text-slate-200"></i>
                <p class="text-sm">
                    {{ $search ? 'Tidak ada log yang cocok dengan pencarian.' : 'Belum ada log aktivitas.' }}
                </p>
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $logs->links() }}</div>
    @endif
</div>

@endsection
