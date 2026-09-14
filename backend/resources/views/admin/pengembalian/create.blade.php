@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 max-w-2xl mx-auto">

        <div class="p-5 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Detail Peminjaman</h3>
        </div>

        <div class="p-5 space-y-2 text-sm text-gray-700 border-b border-gray-200">
            <p><span class="font-semibold">Peminjam:</span> {{ $peminjaman->user->name ?? 'User Dihapus' }}</p>
            <p><span class="font-semibold">Tgl Pinjam:</span> {{ $peminjaman->tgl_pinjam->format('d M Y') }}</p>
            <p><span class="font-semibold">Rencana Kembali:</span> {{ $peminjaman->tgl_kembali_plan->format('d M Y') }}</p>
            <div>
                <span class="font-semibold">Alat Dipinjam:</span>
                <ul class="list-disc list-inside">
                    @foreach ($peminjaman->detailPinjam as $detail)
                        <li>{{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }})</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <form action="{{ route('admin.pengembalian.store', $peminjaman->id) }}" method="POST" class="p-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali', now()->format('Y-m-d')) }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Alat Saat Dikembalikan</label>
                <select name="kondisi_kembali"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="" disabled {{ old('kondisi_kembali') ? '' : 'selected' }}>-- Pilih Kondisi --</option>
                    <option value="Baik" {{ old('kondisi_kembali') == 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Rusak Ringan" {{ old('kondisi_kembali') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="Rusak Sedang" {{ old('kondisi_kembali') == 'Rusak Sedang' ? 'selected' : '' }}>Rusak Sedang</option>
                    <option value="Rusak Berat" {{ old('kondisi_kembali') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <p class="text-xs text-gray-500">Denda keterlambatan dihitung otomatis (Rp 1.000/hari) jika tanggal kembali melewati rencana kembali. Diproses oleh: {{ auth()->user()->name }}</p>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Konfirmasi Pengembalian
                </button>
                <a href="{{ route('admin.pengembalian.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection