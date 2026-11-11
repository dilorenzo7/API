<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    // Menampilkan Form Register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Memproses Register (khusus peminjam)
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_hp'    => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'alamat'   => ['required', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'Username wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'no_hp.required'     => 'No. telepon wajib diisi.',
            'no_hp.regex'        => 'Format no. telepon tidak valid (contoh: 081234567890).',
            'alamat.required'    => 'Alamat wajib diisi.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'no_hp'    => $data['no_hp'],
            'alamat'   => $data['alamat'],
            'password' => $data['password'], // otomatis di-hash oleh cast 'hashed'
            'role'     => 'peminjam',        // dipaksa, tidak dari input form
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
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

        if ($user->foto_profile) {
            Storage::disk('public')->delete($user->foto_profile);
        }

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