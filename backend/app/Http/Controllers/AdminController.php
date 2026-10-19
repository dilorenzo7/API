<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Pengembalian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Halaman dashboard admin: hanya ringkasan statistik (tanpa log aktivitas)
    public function index()
    {
        $totalAlat        = Alat::count();
        $totalUser        = User::count();
        $totalPeminjaman  = Peminjaman::where('status', 'dipinjam')->count();
        $pendingApproval  = Peminjaman::where('status', 'diajukan')->count();
        $totalTerlambat   = Peminjaman::where('status', 'telat')->count();
        $totalKategori    = Kategori::count();

        return view('admin.dashboard', compact(
            'totalAlat', 'totalUser',
            'totalPeminjaman', 'pendingApproval', 'totalTerlambat', 'totalKategori'
        ));
    }

    // Halaman log aktivitas (terpisah dari dashboard)
    public function indexLog(Request $request)
    {
        $search = $request->input('search');

        $logs = LogAktivitas::with('user')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('aktivitas', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.log.index', compact('logs', 'search'));
    }

    // FITUR KELOLA USER

    // Daftar user (dengan fitur pencarian dan paginasi)
    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('role', 'like', "%{$search}%");
        })
        ->orderByRaw("FIELD(role, 'admin', 'petugas', 'peminjam')")
        ->orderBy('name')
        ->paginate(10)
        ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    // Menampilkan form tambah user baru
    public function createUser()
    {
        return view('admin.user.create');
    }

    // Menyimpan user baru ke database
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas,peminjam',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'alamat' => 'nullable|string',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_profile')) {
            $fotoPath = $request->file('foto_profile')->store('foto_profile', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'foto_profile' => $fotoPath,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    // Menampilkan form edit data user
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    // Memperbarui data user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,petugas,peminjam',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'alamat' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('alamat')) {
            $data['alamat'] = $request->alamat;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profile')) {
            if ($user->foto_profile) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            $data['foto_profile'] = $request->file('foto_profile')->store('foto_profile', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    // Menghapus user
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    // FITUR KELOLA KATEGORI

    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    // 2. Menampilkan form tambah kategori
    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    // 3. Menyimpan kategori baru 
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit kategori
    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    // 5. Memperbarui kategori
    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,'. $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui');
    }

    // 6. Menghapus Kategori
    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alat()->count() > 0) {
            return redirect()->route('admin.kategori.index')
            ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // CRUD ALAT
    
    // 1. Menampilkan daftar alat
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }
    
    // 2. Menampilkan form tambah alat
    public function createAlat()
    {
        $kategori = Kategori::all();
        return view('admin.alat.create', compact('kategori'));
    }

    // 3. Menyimpan alat baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'nama_alat' => $request->nama_alat,
            'kategori_id' => $request->kategori_id,
            'stok' => $request->stok,
            'status_kondisi' => $request->kondisi,
            'deskripsi' => $request->deskripsi,
        ];

        // Handle Upload Gambar/Foto jika ada
        if ($request->hasFile('foto')) {
            $data['gambar'] = $request->file('foto')->store('alat', 'public');
        }

        Alat::create($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit alat
    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategori = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategori'));
    }

    // 5. Memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'nama_alat' => $request->nama_alat,
            'kategori_id' => $request->kategori_id,
            'stok' => $request->stok,
            'status_kondisi' => $request->kondisi,
            'deskripsi' => $request->deskripsi,
        ];

        // Handle Update Gambar jika ada file baru
        if ($request->hasFile('foto')) {
            // Hapus gambar lama jika ada
            if ($alat->gambar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($alat->gambar);
            }

            $data['gambar'] = $request->file('foto')->store('alat', 'public');
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    // 6. Menghapus data alat
    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil dihapus.');
    }

    // CRUD PEMINJAMAN
    
    // 1. Menampilkan daftar peminjaman
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman() 
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // 3. Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);
            
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];
                $alat = Alat::findOrFail($alatId);

                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 4. Memperbarui status peminjaman 
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,dikembalikan,telat',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = $detail->alat;
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi untuk dipinjam.");
                    }
                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama == 'dipinjam' && ($statusBaru == 'dikembalikan')) {
                foreach ($peminjaman->detailPinjam as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->update(['status' => $statusBaru]);
            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    // Menghapus data peminjaman
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

        if ($peminjaman->status == 'dipinjam') {
            foreach ($peminjaman->detailPinjam as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    // CRUD PENGEMBALIAN
    
    // 1. Menampilkan daftar pengembalian
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.index', compact('peminjamans', 'search'));
    }

    // 2. Menampilkan form proses pengembalian
    public function createPengembalian($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);
        return view('admin.pengembalian.create', compact('peminjaman'));
    }

    // 3. Menyimpan/memproses data pengembalian
    public function storePengembalian(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        $request->validate([
            'tgl_kembali' => 'required|date',
            'kondisi_kembali' => 'required|in:Baik,Rusak Ringan,Rusak Sedang,Rusak Berat',
        ]);

        DB::beginTransaction();
        try {
            $dendaPerHari = 1000;
            $denda = 0;

            if (strtotime($request->tgl_kembali) > strtotime($peminjaman->tgl_kembali_plan)) {
                $telatHari = (strtotime($request->tgl_kembali) - strtotime($peminjaman->tgl_kembali_plan)) / 86400;
                $denda = $telatHari * $dendaPerHari;
            }

            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => $request->tgl_kembali,
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $denda,
                'petugas_id' => auth()->id(),
            ]);

            foreach ($peminjaman->detailPinjam as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'dikembalikan']);

            DB::commit();

            $pesan = 'Pengembalian berhasil diproses.';
            if ($denda > 0) {
                $pesan .= ' Denda keterlambatan: Rp ' . number_format($denda, 0, ',', '.');
            }

            return redirect()->route('admin.pengembalian.index')->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 4. Menghapus data pengembalian
    public function destroyPengembalian($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->delete();

        return redirect()->route('admin.pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus.');
    }
}



//npm install -g @opencode/cli
//# 1. Setting Docker + WSL2 + Ubuntu (PowerShell admin, lalu restart)
//wsl --install
# Docker Desktop → Settings → General: centang "Use the WSL 2 based engine"
# Docker Desktop → Settings → Resources → WSL Integration: aktifkan Ubuntu

# 2. Connect WSL di VS Code (install ekstensi "WSL"; pojok kiri bawah harus "WSL: Ubuntu")
//cd ~/nama-projek && code .

# 3. Copy .env dari .env.example, lalu EDIT bagian database:
//cp .env.example .env
#   DB_CONNECTION=mysql
#   DB_HOST=mysql-server        <- nama service docker, bukan localhost
#   DB_PORT=3306
#   DB_DATABASE=API_ujikom
#   DB_USERNAME=api_ujikom
#   DB_PASSWORD=api_ujikom

# 5. Install Composer (dependency / folder vendor, lewat container)
//docker compose run --rm app-laravel composer install

# 6. Docker compose up & build
//docker compose up -d --build

# 4. Generate key (dikerjakan SETELAH 5 dan 6)
//docker compose exec app-laravel php artisan key:generate

# 7. Install Laravel Sanctum (kalau belum ada di composer.json)
//docker compose exec app-laravel composer require laravel/sanctum

# 8. Install barryvdh/laravel-dompdf
//docker compose exec app-laravel composer require barryvdh/laravel-dompdf

# 9. Install API (route api.php + tabel token)
//docker compose exec app-laravel php artisan install:api

# 10. Migrate database & seeder (+ link storage buat gambar/foto)
//docker compose exec app-laravel php artisan migrate --seed
//docker compose exec app-laravel php artisan storage:link

//#!/bin/bash
#
# install-and-start.sh — Instalasi & menjalankan SiPinjam otomatis di WSL + Docker Desktop
#
# Cara pakai:
#   chmod +x install-and-start.sh
#   ./install-and-start.sh
#
# Script ini akan:
#   1. Cek Docker & Docker Compose tersedia
#   2. Setup file .env (copy dari template + generate key otomatis via container)
#   3. Build & jalankan semua container
#   4. Tunggu sampai database siap
#   5. Verifikasi aplikasi NYALA
#

//set -e

# Warna untuk output
//HIJAU='\033[0;32m'
//KUNING='\033[1;33m'
//MERAH='\033[0;31m'
//BIRU='\033[0;34m'
//RESET='\033[0m'

//info()    { echo -e "${BIRU}[INFO]${RESET} $1"; }
//sukses()  { echo -e "${HIJAU}[OK]${RESET} $1"; }
//peringatan() { echo -e "${KUNING}[!]${RESET} $1"; }
//gagal()   { echo -e "${MERAH}[GAGAL]${RESET} $1"; exit 1; }

//echo ""
//echo "=============================================="
//echo "  SiPinjam — Auto Install & Start (WSL+Docker)"
//echo "=============================================="
//echo ""

# ── 1. Cek Docker ──────────────────────────────────────────────
//info "Mengecek Docker..."

//if ! command -v docker &> /dev/null; then
   // gagal "Docker tidak ditemukan!

    //Pastikan:
    //1. Docker Desktop sudah terinstall & berjalan di Windows
    //2. Settings → Resources → WSL Integration → Ubuntu tercentang
    //3. Tutup terminal ini dan buka lagi, lalu jalankan script sekali lagi."
//fi
//sukses "Docker ditemukan: $(docker --version)"

//if ! docker compose version &> /dev/null; then
  //  gagal "Docker Compose tidak ditemukan. Update Docker Desktop ke versi terbaru."
//fi
//sukses "Docker Compose ditemukan: $(docker compose version --short)"

# Cek Docker daemon jalan
//if ! docker info &> /dev/null; then
  //  gagal "Docker daemon tidak jalan!

    //Buka Docker Desktop di Windows dan tunggu sampai status 'Running',
    //lalu jalankan script ini lagi."
//fi
//sukses "Docker daemon berjalan"

# ── 2. Cek posisi direktori ────────────────────────────────────
//if [ ! -f "docker-compose.yml" ]; then
  //  gagal "File docker-compose.yml tidak ditemukan!

    //Jalankan script ini dari folder api-ujikom:
      //cd ~/api-ujikom
      //./install-and-start.sh"
//fi
//sukses "Berada di folder project yang benar"

# ── 3. Setup .env ──────────────────────────────────────────────
//info "Menyiapkan file .env..."

//if [ ! -f "backend/.env" ]; then
  //  cp backend/.env.example backend/.env
    //sukses "backend/.env dibuat dari template"
//else
  //  peringatan "backend/.env sudah ada, dilewati (tidak ditimpa)"
//fi

# ── 4. Build & jalankan container ──────────────────────────────
//info "Menjalankan Docker Compose (ini butuh beberapa menit saat pertama kali)..."

//docker compose up -d --build

//sukses "Perintah docker compose dikirim"

# ── 5. Tunggu MySQL siap ──────────────────────────────────────
//info "Menunggu database MySQL siap..."

//SEBELUM=0
//for i in $(seq 1 24); do
  //  if docker compose exec -T mysql-server mysqladmin ping -h localhost -u api_ujikom --password=api_ujikom123 &> /dev/null; then
    //    echo ""
      //  sukses "MySQL siap!"
        //break
    //fi
//    printf "."
  //  sleep 5
    //if [ "$i" -eq 24 ]; then
     //   echo ""
       // peringatan "MySQL belum merespon setelah 2 menit. Cek log:"
        //echo "  docker compose logs mysql-server"
    //fi
//done

# ── 6. Tunggu Laravel selesai setup ────────────────────────────
//info "Menunggu Laravel selesai install dependency + migration..."
//info "(composer install + npm build bisa makan waktu 3-5 menit pertama kali)"

//for i in $(seq 1 60); do
  //  if docker compose exec -T app-laravel php artisan migrate:status &> /dev/null; then
    //    echo ""
      //  sukses "Laravel siap & database ter-migrate!"
        //break
    //fi
    # Tampilkan progress tiap 30 detik
   // if [ $((i % 6)) -eq 0 ]; then
     //   echo ""
       // info "Masih proses... (${i}x5 detik)"
   // else
     //   printf "."
   // fi
   // sleep 5
   // if [ "$i" -eq 60 ]; then
     //   echo ""
       // gagal "Laravel tidak siap setelah 5 menit. Cek log:
//  docker compose logs app-laravel"
  //  fi
//done

# ── 7. Verifikasi akhir ────────────────────────────────────────
//echo ""
//echo "=============================================="
//echo "  VERIFIKASI"
//echo "=============================================="

# Cek APP_KEY
//KEY=$(docker compose exec -T app-laravel grep '^APP_KEY=' .env 2>/dev/null | cut -d= -f2)
//if [ -n "$KEY" ] && [ "$KEY" != "" ]; then
  //  sukses "APP_KEY terisi"
//else
  //  peringatan "APP_KEY kosong — generate manual: docker compose exec app-laravel php artisan key:generate"
//fi

# Cek HTTP response
//sleep 3
//HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000 2>/dev/null || echo "000")
//if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
  //  sukses "Aplikasi merespon HTTP $HTTP_CODE di http://localhost:8000"
//else
  //  peringatan "Aplikasi belum merespon (HTTP $HTTP_CODE). Tunggu 1 menit lagi lalu coba buka browser."
//fi

# Cek phpMyAdmin
//PMA_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8081 2>/dev/null || echo "000")
//if [ "$PMA_CODE" = "200" ]; then
  //  sukses "phpMyAdmin merespon di http://localhost:8081"
//else
  //  peringatan "phpMyAdmin belum merespon (HTTP $PMA_CODE)"
//fi

//echo ""
//echo "=============================================="
//echo -e "  ${HIJAU}SELESAI! Aplikasi SiPinjam sudah NYALA 🎉${RESET}"
//echo "=============================================="
//echo ""
//echo "  Aplikasi   : http://localhost:8000"
//echo "  phpMyAdmin : http://localhost:8081"
//echo "               user: api_ujikom / pass: api_ujikom123"
//echo ""
//echo "  Perintah berguna:"
//echo "    docker compose ps          # lihat status container"
//echo "    docker compose logs -f     # lihat log"
//echo "    docker compose down        # matikan (data tersimpan)"
//echo ""