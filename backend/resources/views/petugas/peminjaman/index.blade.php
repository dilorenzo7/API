@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman - Panel Petugas')

@section('content')

    {{-- Alert --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm flex items-center gap-3">
            <i class="bi bi-exclamation-circle-fill text-lg"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Header Banner --}}
    <div class="mb-8 p-6 md:p-8 bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-3xl text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Persetujuan Peminjaman</h2>
            <p class="text-indigo-100 text-sm max-w-xl">
                Halo, <strong>{{ auth()->user()->name }}</strong>! Berikut daftar pengajuan peminjaman yang perlu ditindaklanjuti.
            </p>
        </div>
        <i class="bi bi-arrow-down-up absolute -bottom-6 -right-6 text-9xl text-white/10 pointer-events-none"></i>
    </div>

    {{-- Stats --}}
    @php $jumlahDiajukan = $peminjamans->where('status', 'diajukan')->count(); @endphp
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Total Pengajuan</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $peminjamans->count() }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="bi bi-journal-text"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Menunggu Persetujuan</p>
                <h3 class="text-2xl font-bold text-amber-500">{{ $jumlahDiajukan }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Sudah Ditangani</p>
                <h3 class="text-2xl font-bold text-emerald-600">{{ $peminjamans->count() - $jumlahDiajukan }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Daftar Pengajuan Peminjaman</h3>
            @if($jumlahDiajukan > 0)
                <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $jumlahDiajukan }} Menunggu
                </span>
            @else
                <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full">
                    Semua Sudah Ditangani
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="py-3 px-5 border-b border-slate-100">#</th>
                        <th class="py-3 px-5 border-b border-slate-100">Peminjam</th>
                        <th class="py-3 px-5 border-b border-slate-100">Alat yang Diajukan</th>
                        <th class="py-3 px-5 border-b border-slate-100">Tgl Pinjam</th>
                        <th class="py-3 px-5 border-b border-slate-100">Rencana Kembali</th>
                        <th class="py-3 px-5 border-b border-slate-100">Status</th>
                        <th class="py-3 px-5 border-b border-slate-100 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $i => $peminjaman)
                        <tr class="hover:bg-slate-50 transition align-top border-b border-slate-50
                            {{ $peminjaman->status === 'diajukan' ? 'bg-amber-50/40' : '' }}">

                            <td class="py-4 px-5 text-xs text-slate-400">{{ $i + 1 }}</td>

                            <td class="py-4 px-5">
                                <p class="font-semibold text-slate-800 text-sm">{{ $peminjaman->user->name ?? 'User Dihapus' }}</p>
                                <p class="text-xs text-slate-400">{{ $peminjaman->user->email ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $peminjaman->user->no_hp ?? '-' }}</p>
                            </td>

                            <td class="py-4 px-5">
                                <ul class="space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <li class="flex items-center gap-2 text-sm">
                                            <span class="text-slate-700 font-medium">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="bg-slate-100 text-slate-500 text-xs px-1.5 py-0.5 rounded-md">{{ $detail->jumlah }} pcs</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td class="py-4 px-5 text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-5 text-sm font-semibold text-slate-700">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}
                            </td>

                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($peminjaman->status === 'diajukan')      bg-amber-100 text-amber-700
                                    @elseif($peminjaman->status === 'dipinjam')  bg-indigo-100 text-indigo-700
                                    @elseif($peminjaman->status === 'dikembalikan') bg-emerald-100 text-emerald-700
                                    @elseif($peminjaman->status === 'telat')     bg-red-100 text-red-700
                                    @else bg-slate-100 text-slate-500 @endif">
                                    {{ ucfirst($peminjaman->status) }}
                                </span>
                            </td>

                            <td class="py-4 px-5">
                                @if($peminjaman->status === 'diajukan')
                                    <div class="flex flex-col gap-2 min-w-[130px]">
                                        <form action="{{ route('petugas.peminjaman.setujui', $peminjaman->id) }}" method="POST"
                                              onsubmit="return confirm('Setujui peminjaman dari {{ $peminjaman->user->name ?? 'user ini' }}?')">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition">
                                                <i class="bi bi-check-lg me-1"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('petugas.peminjaman.tolak', $peminjaman->id) }}" method="POST"
                                              onsubmit="return confirm('Tolak dan hapus pengajuan dari {{ $peminjaman->user->name ?? 'user ini' }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition">
                                                <i class="bi bi-x-lg me-1"></i> Tolak
                                            </button>
                                        </form>
                                    </div>

                                @elseif($peminjaman->status === 'dipinjam')
                                    <button onclick="toggleForm('form-kembali-{{ $peminjaman->id }}')"
                                        class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition w-full">
                                        <i class="bi bi-arrow-return-left me-1"></i> Proses Kembali
                                    </button>

                                    <div id="form-kembali-{{ $peminjaman->id }}" class="hidden mt-3">
                                        <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST"
                                              class="space-y-2 bg-slate-50 border border-slate-200 rounded-2xl p-3">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-500 mb-1">Kondisi Kembali</label>
                                                <select name="kondisi_kembali" required
                                                    class="w-full text-xs border border-slate-200 rounded-xl px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                                                    <option value="">-- Pilih Kondisi --</option>
                                                    <option value="baik">Baik</option>
                                                    <option value="rusak ringan">Rusak Ringan</option>
                                                    <option value="rusak sedang">Rusak Sedang</option>
                                                    <option value="rusak berat">Rusak Berat</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-500 mb-1">Denda (Rp)</label>
                                                <input type="number" name="denda" value="0" min="0"
                                                    class="w-full text-xs border border-slate-200 rounded-xl px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                                            </div>
                                            <button type="submit"
                                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition">
                                                Simpan Pengembalian
                                            </button>
                                        </form>
                                    </div>

                                @else
                                    <span class="text-xs text-slate-400 italic">Sudah selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="bi bi-clipboard-check text-4xl block mb-2"></i>
                                <p class="text-sm">Belum ada data pengajuan peminjaman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleForm(id) {
            document.getElementById(id).classList.toggle('hidden');
        }
    </script>
@endsection
