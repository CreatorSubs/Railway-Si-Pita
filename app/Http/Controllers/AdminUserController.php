<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // Helper untuk cek apakah user adalah Owner
    private function isOwner()
    {
        $userEmail = Auth::user() ? Auth::user()->email : session('user_email', '');

        return strtolower($userEmail) === 'admin@diskominfo.go.id';
    }

    // Menampilkan Form Buat Akun Admin (Khusus Owner)
    public function create()
    {
        if (! $this->isOwner()) {
            abort(403, 'Akses Ditolak: Hanya Owner (admin@diskominfo.go.id) yang dapat membuat akun admin baru.');
        }

        return view('pages.admin.create_user');
    }

    // Menyimpan Akun Admin Baru (Khusus Owner)
    public function store(Request $request)
    {
        if (! $this->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk menambah akun admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'Akun admin baru berhasil dibuat!');
    }

    // Kelola Semua Akun Admin (Khusus Owner)
    public function index()
    {
        if (! $this->isOwner()) {
            abort(403, 'Akses Ditolak: Hanya Owner yang dapat mengelola daftar admin.');
        }

        $users = User::all();
        $isOwner = true;

        return view('pages.admin.users_list', compact('users', 'isOwner'));
    }

    // Toggle Aktif/Nonaktif Akun Admin (Khusus Owner)
    public function toggleStatus($id)
    {
        if (! $this->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengubah status akun.');
        }

        $user = User::findOrFail($id);

        if (strtolower($user->email) === 'admin@diskominfo.go.id') {
            return redirect()->back()->with('error', 'Akun Owner utama tidak dapat dinonaktifkan!');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    // View History Pembuatan Sertifikat (Khusus Owner)
    public function history()
    {
        if (! $this->isOwner()) {
            abort(403, 'Akses Ditolak: Fitur ini hanya untuk Owner (admin@diskominfo.go.id).');
        }

        $certificates = Certificate::latest()->get();

        return view('pages.admin.history', compact('certificates'));
    }
}
