@extends('layouts.app')

@section('title', 'Katalog Alat - Portal Peminjam')
@section('header-title', 'Katalog Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── FORM SEARCH & FILTER (GET) ── --}}
    <form action="{{ route('peminjam.katalog') }}" method="GET">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="🔍 Cari nama alat..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:w-56">
                    <select name="kategori_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ ($kategoriId ?? '') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Cari
                    </button>
                    @if($search || $kategoriId)
                        <a href="{{ route('peminjam.katalog') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- ── FORM AJUKAN PEMINJAMAN (POST) ── --}}
    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50">
                <h3 class="text-base font-bold text-gray-800">Pilih Alat & Isi Detail Peminjaman</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Centang alat yang ingin dipinjam, isi jumlah, lalu klik "Ajukan Peminjaman".
                </p>
            </div>

            <div class="p-5">
                {{-- Tanggal --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Pinjam <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tgl_pinjam"
                               value="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rencana Tanggal Kembali <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tgl_kembali_plan"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                </div>

                {{-- Tabel alat --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                                <th class="py-3 px-4 border-b w-10">Pilih</th>
                                <th class="py-3 px-4 border-b">Nama Alat</th>
                                <th class="py-3 px-4 border-b">Kategori</th>
                                <th class="py-3 px-4 border-b">Kondisi</th>
                                <th class="py-3 px-4 border-b">Stok</th>
                                <th class="py-3 px-4 border-b w-32">Jumlah Pinjam</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm">
                            @forelse($alats as $alat)
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="py-3 px-4 border-b text-center">
                                        <input type="checkbox"
                                               name="alat_id[]"
                                               value="{{ $alat->id }}"
                                               class="w-4 h-4 rounded border-gray-300 text-blue-600 cursor-pointer"
                                               onchange="toggleJumlah({{ $alat->id }}, this.checked)">
                                    </td>
                                    <td class="py-3 px-4 border-b font-medium text-gray-900">
                                        {{ $alat->nama_alat }}
                                    </td>
                                    <td class="py-3 px-4 border-b text-gray-500 text-xs">
                                        {{ $alat->kategori->nama_kategori ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                            @if($alat->status_kondisi === 'baik') bg-emerald-100 text-emerald-700
                                            @elseif($alat->status_kondisi === 'rusak ringan') bg-yellow-100 text-yellow-700
                                            @elseif($alat->status_kondisi === 'rusak sedang') bg-orange-100 text-orange-700
                                            @else bg-red-100 text-red-700 @endif">
                                            {{ ucfirst($alat->status_kondisi) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        <span class="font-semibold {{ $alat->stok <= 2 ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $alat->stok }}
                                        </span>
                                        <span class="text-gray-400 text-xs"> unit</span>
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        <input type="number"
                                               name="jumlah[]"
                                               id="jumlah-{{ $alat->id }}"
                                               value="1" min="1" max="{{ $alat->stok }}"
                                               disabled
                                               class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-400">
                                        <p class="text-2xl mb-2">📦</p>
                                        <p>Tidak ada alat yang cocok.</p>
                                        @if($search || $kategoriId)
                                            <a href="{{ route('peminjam.katalog') }}"
                                               class="text-blue-600 text-sm hover:underline mt-1 inline-block">
                                                Tampilkan semua alat →
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Info alat terpilih --}}
                <div id="info-terpilih" class="hidden mt-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
                    <span id="jumlah-terpilih">0</span> alat dipilih
                </div>
            </div>

            <div class="p-5 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                <p class="text-xs text-gray-500">
                    Pengajuan akan dikirim ke petugas untuk disetujui.
                </p>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition text-sm">
                    Ajukan Peminjaman →
                </button>
            </div>
        </div>
    </form>

    <script>
        let terpilih = 0;

        function toggleJumlah(alatId, isChecked) {
            const input = document.getElementById('jumlah-' + alatId);
            input.disabled = !isChecked;
            if (!isChecked) input.value = 1;

            terpilih += isChecked ? 1 : -1;
            const info = document.getElementById('info-terpilih');
            document.getElementById('jumlah-terpilih').textContent = terpilih;
            info.classList.toggle('hidden', terpilih === 0);
        }
    </script>
@endsection
