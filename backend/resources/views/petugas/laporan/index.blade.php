@extends('layouts.app')

@section('title', 'Cetak Laporan - Panel Petugas')
@section('header-title', 'Laporan Peminjaman')

@section('content')  

    {{-- Filter (Sembunyi saat diprint) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 no-print">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-base font-bold text-gray-800">Filter Laporan</h3>
        </div>
        <form action="{{ route('petugas.laporan.index') }}" method="GET" class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Semua Status --</option>
                        <option value="diajukan"    {{ $status === 'diajukan'    ? 'selected' : '' }}>Diajukan</option>
                        <option value="dipinjam"    {{ $status === 'dipinjam'    ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="telat"       {{ $status === 'telat'       ? 'selected' : '' }}>Telat</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Tampilkan
                    </button>
                    <a href="{{ route('petugas.laporan.index') }}"
                        class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Ringkasan statistik Layar (Sembunyi saat diprint) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 no-print">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $totalSemua }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Transaksi</p>
        </div>
        <div class="bg-white rounded-lg border border-blue-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $totalDipinjam }}</p>
            <p class="text-xs text-gray-500 mt-1">Sedang Dipinjam</p>
        </div>
        <div class="bg-white rounded-lg border border-emerald-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $totalDikembalikan }}</p>
            <p class="text-xs text-gray-500 mt-1">Sudah Dikembalikan</p>
        </div>
        <div class="bg-white rounded-lg border border-red-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Denda</p>
        </div>
    </div>

    {{-- Tombol cetak --}}
    <div class="flex justify-end mb-4 no-print">
        <button onclick="cetakLaporan()"
            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition flex items-center gap-2">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    {{-- AREA UTAMA LAPORAN --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="area-cetak">

        {{-- Header khusus Surat/Laporan Resmi --}}
        <div class="cetak-header text-center mb-6 border-b-2 border-gray-800 pb-4">
            <h1 class="text-2xl font-bold text-gray-900 uppercase tracking-wide">Laporan Peminjaman Alat</h1>
            <p class="text-sm text-gray-600 mt-1">
                @if($startDate && $endDate)
                    Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                @else
                    Periode: <strong>Semua Waktu</strong>
                @endif
                @if($status) | Status: <strong>{{ ucfirst($status) }}</strong> @endif
            </p>
            <p class="text-xs text-gray-500 mt-1">Dicetak oleh: {{ auth()->user()->name }} pada {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="tabel-laporan w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">#</th>
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Tgl Kembali</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($peminjamans as $i => $p)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-2 px-4 border-b text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-2 px-4 border-b font-medium">{{ $p->user->name ?? '-' }}</td>
                            <td class="py-2 px-4 border-b">
                                @foreach($p->detailPinjam as $d)
                                    <span class="block">• {{ $d->alat->nama_alat ?? '-' }} ({{ $d->jumlah }})</span>
                                @endforeach
                            </td>
                            <td class="py-2 px-4 border-b">
                                {{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d/m/Y') }}
                            </td>
                            <td class="py-2 px-4 border-b">
                                {{ \Carbon\Carbon::parse($p->tgl_kembali_plan)->format('d/m/Y') }}
                            </td>
                            <td class="py-2 px-4 border-b">
                                {{ $p->pengembalian ? \Carbon\Carbon::parse($p->pengembalian->tgl_kembali)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-2 px-4 border-b">
                                <span class="badge-status px-2 py-0.5 text-xs font-semibold rounded-full
                                    @if($p->status === 'diajukan')      bg-yellow-100 text-yellow-800
                                    @elseif($p->status === 'dipinjam')  bg-blue-100 text-blue-800
                                    @elseif($p->status === 'dikembalikan') bg-emerald-100 text-emerald-800
                                    @elseif($p->status === 'telat')     bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b">
                                @if($p->pengembalian && $p->pengembalian->denda > 0)
                                    <span class="text-red-600 font-semibold">
                                        Rp {{ number_format($p->pengembalian->denda, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                Tidak ada data untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($peminjamans->count() > 0)
                <tfoot>
                    <tr class="bg-gray-50 font-semibold text-sm">
                        <td colspan="7" class="py-3 px-4 border-t text-right text-gray-700">Total Denda:</td>
                        <td class="py-3 px-4 border-t text-red-600">
                            Rp {{ number_format($totalDenda, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Styling Khusus Cetak & Layar --}}
    <style>
        /* Sembunyikan header cetak di layar biasa */
        .cetak-header {
            display: none;
        }

        /* ATURAN KHUSUS CETAK / PRINT */
        @media print {
            @page {
                size: A4 landscape;
                margin: 1cm;
            }

            /* Sembunyikan elemen navigasi, sidebar, filter, & tombol */
            aside, nav, header, .no-print { 
                display: none !important; 
            }

            /* Munculkan header cetak */
            .cetak-header {
                display: block !important;
            }

            body { 
                background: #ffffff !important;
                font-family: Arial, sans-serif !important;
                color: #000000 !important;
            }

            #area-cetak { 
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
            }

            /* Perbaikan paksa tabel agar rapi dan bertabel saat diprint */
            .tabel-laporan {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 11px !important;
            }

            .tabel-laporan th, 
            .tabel-laporan td {
                border: 1px solid #000000 !important;
                padding: 6px 8px !important;
            }

            .tabel-laporan th {
                background-color: #f2f2f2 !important;
                font-weight: bold !important;
                text-align: left !important;
            }

            /* Hilangkan background badge biar tetap jelas di kertas */
            .badge-status {
                background: transparent !important;
                padding: 0 !important;
                font-weight: bold !important;
            }
        }
    </style>

    <script>
        function cetakLaporan() {
            window.print();
        }
    </script>
@endsection