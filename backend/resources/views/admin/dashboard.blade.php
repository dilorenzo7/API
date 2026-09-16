@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')

@section('content')
    <!-- Banner Welcome Admin -->
    <div class="mb-8 p-6 md:p-8 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl text-white shadow-xl shadow-slate-200 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold mb-3 border border-indigo-500/30">
                    <i class="bi bi-shield-lock-fill"></i> Panel Administrator
                </span>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-slate-300 text-xs md:text-sm max-w-xl leading-relaxed">
                    Anda login sebagai <strong class="text-indigo-400 uppercase tracking-wide">{{ auth()->user()->role }}</strong>. Pantau seluruh statistik inventaris dan aktivitas peminjaman dari halaman ini.
                </p>
            </div>
        </div>
        <i class="bi bi-person-badge absolute -bottom-8 -right-6 text-9xl text-white/5 pointer-events-none"></i>
    </div>

    <!-- Ringkasan Statistik Sistem (Optional - jika controller mengirimkan data) -->
    @if(isset($totalAlat) || isset($totalPeminjaman) || isset($pendingApproval))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Total Peralatan</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $totalAlat ?? 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="bi bi-box-seam-fill"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Peminjaman Aktif</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $totalPeminjaman ?? 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="bi bi-arrow-repeat"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Perlu Persetujuan</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $pendingApproval ?? 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabel Log Aktivitas Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Log Aktivitas Terbaru</h3>
                    <p class="text-[11px] text-slate-400">Daftar riwayat aksi pengubah sistem oleh pengguna.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-5">Waktu</th>
                        <th class="py-3.5 px-5">User</th>
                        <th class="py-3.5 px-5">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Waktu Log -->
                            <td class="py-3.5 px-5 font-medium text-slate-500 whitespace-nowrap">
                                <i class="bi bi-clock text-slate-400 mr-1.5"></i>
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                            </td>

                            <!-- Nama User -->
                            <td class="py-3.5 px-5 font-bold text-slate-800 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    {{ $log->user->name ?? 'Sistem' }}
                                </div>
                            </td>

                            <!-- Detail Aktivitas -->
                            <td class="py-3.5 px-5 leading-relaxed text-slate-600">
                                {{ $log->aktivitas }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-400">
                                <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
                                Belum ada log aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($logs, 'hasPages') && $logs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection