@extends('layouts.app')

@section('title', 'Persetujuan Pengembalian - Panel Petugas')
@section('header-title', 'Persetujuan Pengembalian Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Daftar Alat Sedang Dipinjam</h3>
            @php $jumlahTerlambat = $peminjamans->where('hari_terlambat', '>', 0)->count(); @endphp
            @if($jumlahTerlambat > 0)
                <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $jumlahTerlambat }} Terlambat Dikembalikan
                </span>
            @else
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $peminjamans->count() }} Sedang Dipinjam
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">#</th>
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat Dipinjam</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Keterlambatan</th>
                        <th class="py-3 px-4 border-b text-center">Proses Kembali</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $i => $peminjaman)
                        <tr class="hover:bg-gray-50 transition align-top
                            {{ $peminjaman->hari_terlambat > 0 ? 'bg-red-50' : '' }}">

                            <td class="py-3 px-4 border-b text-gray-500">{{ $i + 1 }}</td>

                            {{-- Peminjam --}}
                            <td class="py-3 px-4 border-b">
                                <p class="font-semibold text-gray-900">{{ $peminjaman->user->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $peminjaman->user->no_hp ?? '-' }}</p>
                            </td>

                            {{-- Alat --}}
                            <td class="py-3 px-4 border-b">
                                <ul class="space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <li class="flex items-center gap-1">
                                            <span class="font-medium">{{ $detail->alat->nama_alat ?? '-' }}</span>
                                            <span class="bg-gray-200 text-gray-600 text-xs px-1.5 py-0.5 rounded">
                                                {{ $detail->jumlah }} pcs
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- Tanggal --}}
                            <td class="py-3 px-4 border-b text-sm">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4 border-b text-sm font-semibold
                                {{ $peminjaman->hari_terlambat > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}
                            </td>

                            {{-- Keterlambatan --}}
                            <td class="py-3 px-4 border-b">
                                @if($peminjaman->hari_terlambat > 0)
                                    <p class="text-red-600 font-semibold text-xs">
                                        {{ $peminjaman->hari_terlambat }} hari terlambat
                                    </p>
                                    <p class="text-red-500 text-xs">
                                        Est. denda: Rp {{ number_format($peminjaman->estimasi_denda, 0, ',', '.') }}
                                    </p>
                                @else
                                    <span class="text-emerald-600 text-xs font-medium">Tepat waktu</span>
                                @endif
                            </td>

                            {{-- Form proses pengembalian --}}
                            <td class="py-3 px-4 border-b">
                                <button onclick="toggleForm('form-kembali-{{ $peminjaman->id }}')"
                                    class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition w-full">
                                    ↩ Proses Kembali
                                </button>

                                <div id="form-kembali-{{ $peminjaman->id }}" class="hidden mt-2">
                                    <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST"
                                          class="space-y-2 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        @csrf

                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Kondisi Alat</label>
                                            <select name="kondisi_kembali" required
                                                class="w-full text-xs border border-gray-300 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-400">
                                                <option value="">-- Pilih --</option>
                                                <option value="baik">Baik</option>
                                                <option value="rusak ringan">Rusak Ringan</option>
                                                <option value="rusak sedang">Rusak Sedang</option>
                                                <option value="rusak berat">Rusak Berat</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                Denda (Rp)
                                                @if($peminjaman->hari_terlambat > 0)
                                                    <span class="text-red-500">
                                                        — Est. Rp {{ number_format($peminjaman->estimasi_denda, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </label>
                                            <input type="number" name="denda"
                                                value="{{ $peminjaman->estimasi_denda }}"
                                                min="0"
                                                class="w-full text-xs border border-gray-300 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-400">
                                        </div>

                                        <button type="submit"
                                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                            ✓ Konfirmasi Pengembalian
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <p class="text-lg">✅</p>
                                <p>Tidak ada alat yang sedang dipinjam.</p>
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
