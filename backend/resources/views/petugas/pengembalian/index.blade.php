@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Petugas')

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
    @php $jumlahTerlambat = $peminjamans->where('hari_terlambat', '>', 0)->count(); @endphp
    <div class="mb-8 p-6 md:p-8 rounded-3xl text-white shadow-xl relative overflow-hidden
        {{ $jumlahTerlambat > 0 ? 'bg-gradient-to-r from-red-500 to-red-700 shadow-red-200' : 'bg-gradient-to-r from-indigo-600 to-indigo-800 shadow-indigo-200' }}">
        <div class="relative z-10">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Proses Pengembalian</h2>
            <p class="text-white/80 text-sm max-w-xl">
                @if($jumlahTerlambat > 0)
                    Perhatian! Ada <strong>{{ $jumlahTerlambat }} peminjaman</strong> yang melewati batas pengembalian.
                @else
                    Semua peminjaman masih dalam batas waktu. Proses pengembalian di sini.
                @endif
            </p>
        </div>
        <i class="bi bi-arrow-return-left absolute -bottom-6 -right-6 text-9xl text-white/10 pointer-events-none"></i>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Sedang Dipinjam</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $peminjamans->count() }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Terlambat</p>
                <h3 class="text-2xl font-bold text-red-500">{{ $jumlahTerlambat }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-xl">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 mb-1">Tepat Waktu</p>
                <h3 class="text-2xl font-bold text-emerald-600">{{ $peminjamans->count() - $jumlahTerlambat }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Daftar Alat Sedang Dipinjam</h3>
            @if($jumlahTerlambat > 0)
                <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $jumlahTerlambat }} Terlambat
                </span>
            @else
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $peminjamans->count() }} Sedang Dipinjam
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="py-3 px-5 border-b border-slate-100">#</th>
                        <th class="py-3 px-5 border-b border-slate-100">Peminjam</th>
                        <th class="py-3 px-5 border-b border-slate-100">Alat Dipinjam</th>
                        <th class="py-3 px-5 border-b border-slate-100">Tgl Pinjam</th>
                        <th class="py-3 px-5 border-b border-slate-100">Rencana Kembali</th>
                        <th class="py-3 px-5 border-b border-slate-100">Keterlambatan</th>
                        <th class="py-3 px-5 border-b border-slate-100 text-center">Proses Kembali</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $i => $peminjaman)
                        <tr class="hover:bg-slate-50 transition align-top border-b border-slate-50
                            {{ $peminjaman->hari_terlambat > 0 ? 'bg-red-50/40' : '' }}">

                            <td class="py-4 px-5 text-xs text-slate-400">{{ $i + 1 }}</td>

                            <td class="py-4 px-5">
                                <p class="font-semibold text-slate-800 text-sm">{{ $peminjaman->user->name ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $peminjaman->user->no_hp ?? '-' }}</p>
                            </td>

                            <td class="py-4 px-5">
                                <ul class="space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <li class="flex items-center gap-2 text-sm">
                                            <span class="text-slate-700 font-medium">{{ $detail->alat->nama_alat ?? '-' }}</span>
                                            <span class="bg-slate-100 text-slate-500 text-xs px-1.5 py-0.5 rounded-md">{{ $detail->jumlah }} pcs</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td class="py-4 px-5 text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-5 text-sm font-semibold {{ $peminjaman->hari_terlambat > 0 ? 'text-red-600' : 'text-slate-700' }}">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}
                            </td>

                            <td class="py-4 px-5">
                                @if($peminjaman->hari_terlambat > 0)
                                    <span class="inline-flex flex-col">
                                        <span class="text-red-600 font-semibold text-xs">{{ $peminjaman->hari_terlambat }} hari terlambat</span>
                                        <span class="text-red-400 text-xs">Est. Rp {{ number_format($peminjaman->estimasi_denda, 0, ',', '.') }}</span>
                                    </span>
                                @else
                                    <span class="text-emerald-600 text-xs font-semibold">Tepat waktu</span>
                                @endif
                            </td>

                            <td class="py-4 px-5">
                                <button onclick="toggleForm('form-kembali-{{ $peminjaman->id }}')"
                                    class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition w-full">
                                    <i class="bi bi-arrow-return-left me-1"></i> Proses Kembali
                                </button>

                                <div id="form-kembali-{{ $peminjaman->id }}" class="hidden mt-3">
                                    <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST"
                                          class="space-y-2 bg-slate-50 border border-slate-200 rounded-2xl p-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Kondisi Alat</label>
                                            <select name="kondisi_kembali" required
                                                class="w-full text-xs border border-slate-200 rounded-xl px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                                                <option value="">-- Pilih --</option>
                                                <option value="baik">Baik</option>
                                                <option value="rusak ringan">Rusak Ringan</option>
                                                <option value="rusak sedang">Rusak Sedang</option>
                                                <option value="rusak berat">Rusak Berat</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">
                                                Denda (Rp)
                                                @if($peminjaman->hari_terlambat > 0)
                                                    <span class="text-red-400">— Est. Rp {{ number_format($peminjaman->estimasi_denda, 0, ',', '.') }}</span>
                                                @endif
                                            </label>
                                            <input type="number" name="denda"
                                                value="{{ $peminjaman->estimasi_denda }}"
                                                min="0"
                                                class="w-full text-xs border border-slate-200 rounded-xl px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                                        </div>
                                        <button type="submit"
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition">
                                            <i class="bi bi-check-lg me-1"></i> Konfirmasi Pengembalian
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="bi bi-check-circle text-4xl block mb-2 text-emerald-400"></i>
                                <p class="text-sm">Tidak ada alat yang sedang dipinjam.</p>
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
