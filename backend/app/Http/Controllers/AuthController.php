<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan Role sesuai matriks Anda
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.peminjaman.index');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.dashboard');
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Menampilkan halaman profil
    public function showProfile()
    {
        return view('profile', ['user' => auth()->user()]);
    }

    // Memproses upload / ganti foto profil
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profile' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto_profile.required' => 'File foto wajib dipilih.',
            'foto_profile.image'    => 'File harus berupa gambar.',
            'foto_profile.mimes'    => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto_profile.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = auth()->user();

        // Hapus foto lama jika ada
        if ($user->foto_profile) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        // Simpan foto baru
        $path = $request->file('foto_profile')->store('foto_profile', 'public');
        $user->update(['foto_profile' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    // Menghapus foto profil
    public function destroyFoto()
    {
        $user = auth()->user();

        if (!$user->foto_profile) {
            return back()->with('error', 'Anda belum memiliki foto profil.');
        }

        Storage::disk('public')->delete($user->foto_profile);
        $user->update(['foto_profile' => null]);

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}