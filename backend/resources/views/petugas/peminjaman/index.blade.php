@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman - Panel Petugas')
@section('header-title', 'Persetujuan Pengajuan Peminjaman')

@section('content')
    {{-- Alert sukses / error --}}
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

    {{-- Info role --}}
    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg shadow-sm">
        Halo, <strong>{{ auth()->user()->name }}</strong>! Berikut daftar pengajuan peminjaman yang perlu ditindaklanjuti.
    </div>

    {{-- Tabel Pengajuan Peminjaman --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Daftar Pengajuan Peminjaman</h3>
            {{-- Badge jumlah pengajuan baru --}}
            @php
                $jumlahDiajukan = $peminjamans->where('status', 'diajukan')->count();
            @endphp
            @if($jumlahDiajukan > 0)
                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $jumlahDiajukan }} Menunggu Persetujuan
                </span>
            @else
                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full">
                    Semua Sudah Ditangani
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">#</th>
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat yang Diajukan</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $i => $peminjaman)
                        <tr class="hover:bg-gray-50 transition align-top
                            {{ $peminjaman->status === 'diajukan' ? 'bg-yellow-50' : '' }}">

                            <td class="py-3 px-4 border-b text-gray-500">{{ $i + 1 }}</td>

                            {{-- Peminjam --}}
                            <td class="py-3 px-4 border-b">
                                <p class="font-semibold text-gray-900">{{ $peminjaman->user->name ?? 'User Dihapus' }}</p>
                                <p class="text-xs text-gray-500">{{ $peminjaman->user->email ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $peminjaman->user->no_hp ?? '-' }}</p>
                            </td>

                            {{-- Daftar alat --}}
                            <td class="py-3 px-4 border-b">
                                <ul class="space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <li class="flex items-center gap-2">
                                            <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
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
                            <td class="py-3 px-4 border-b text-sm font-semibold">
                                {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}
                            </td>

                            {{-- Badge status --}}
                            <td class="py-3 px-4 border-b">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($peminjaman->status === 'diajukan')   bg-yellow-100 text-yellow-800
                                    @elseif($peminjaman->status === 'dipinjam') bg-blue-100 text-blue-800
                                    @elseif($peminjaman->status === 'dikembalikan') bg-emerald-100 text-emerald-800
                                    @elseif($peminjaman->status === 'telat')   bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ ucfirst($peminjaman->status) }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-4 border-b">
                                @if($peminjaman->status === 'diajukan')
                                    <div class="flex flex-col gap-2 min-w-[120px]">
                                        {{-- Tombol Setujui --}}
                                        <form action="{{ route('petugas.peminjaman.setujui', $peminjaman->id) }}" method="POST"
                                              onsubmit="return confirm('Setujui peminjaman dari {{ $peminjaman->user->name ?? 'user ini' }}?')">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                ✓ Setujui
                                            </button>
                                        </form>

                                        {{-- Tombol Tolak --}}
                                        <form action="{{ route('petugas.peminjaman.tolak', $peminjaman->id) }}" method="POST"
                                              onsubmit="return confirm('Tolak dan hapus pengajuan dari {{ $peminjaman->user->name ?? 'user ini' }}? Peminjam dapat mengajukan ulang.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                ✕ Tolak
                                            </button>
                                        </form>
                                    </div>
                                @elseif($peminjaman->status === 'dipinjam')
                                    {{-- Form proses pengembalian --}}
                                    <button onclick="toggleForm('form-kembali-{{ $peminjaman->id }}')"
                                        class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition w-full">
                                        ↩ Proses Kembali
                                    </button>

                                    <div id="form-kembali-{{ $peminjaman->id }}" class="hidden mt-2">
                                        <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST"
                                              class="space-y-2 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Kondisi Kembali</label>
                                                <select name="kondisi_kembali" required
                                                    class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-400">
                                                    <option value="">-- Pilih Kondisi --</option>
                                                    <option value="baik">Baik</option>
                                                    <option value="rusak ringan">Rusak Ringan</option>
                                                    <option value="rusak sedang">Rusak Sedang</option>
                                                    <option value="rusak berat">Rusak Berat</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Denda (Rp)</label>
                                                <input type="number" name="denda" value="0" min="0"
                                                    class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-400">
                                                <p class="text-xs text-gray-400 mt-0.5">Isi 0 jika tidak ada denda</p>
                                            </div>
                                            <button type="submit"
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                Simpan Pengembalian
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sudah selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <p class="text-lg">📋</p>
                                <p>Belum ada data pengajuan peminjaman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Script toggle form pengembalian --}}
    <script>
        function toggleForm(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
@endsection
