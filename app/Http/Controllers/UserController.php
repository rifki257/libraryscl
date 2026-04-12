<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // admin
    public function index(Request $request)
    {
    // Proteksi role
    if (auth()->user()->role !== 'kepper') {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    $query = \App\Models\User::whereIn('role', ['kepper', 'petugas']);

    // Logika Pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%');
        });
        $admins = $query->latest()->get();
    } else {
        $admins = $query->latest()->paginate(1); 
    }

    if ($request->ajax()) {
        return view('admin.table_admin_rows', compact('admins'))->render();
    }

    return view('akun_admin', compact('admins'));
    }

    public function indexSiswa(Request $request)
    {
    $query = \App\Models\User::where('role', 'anggota');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
            ->orWhere('nis', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('no_hp', 'like', "%$search%");
        });
    }

    if ($request->filled('filter_kelas')) {
        $query->where('kelas', $request->filter_kelas);
    }

    if ($request->ajax()) {
        $users = $query->orderBy('kelas', 'asc')->get();
        return view('admin.table_siswa_rows', compact('users'))->render();
    }

    $users = $query->orderBy('kelas', 'asc')->paginate(10)->withQueryString();
    
    return view('partials.siswa', [
        'users' => $users,
        'title' => 'Daftar Siswa'
    ]);
    }

    public function create()
    {
        if (!in_array(auth()->user()->role, ['kepper', 'petugas'])) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak punya akses!');
        }
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $userLogin = auth()->user();

        if (!in_array($userLogin->role, ['kepper', 'petugas'])) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak punya akses!');
        }

        if ($userLogin->role === 'petugas') {
            $request->merge(['role' => 'anggota']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'no_hp' => ['required', 'string'],
            'role' => ['required', 'in:anggota,petugas,kepper'],
            'nis' => [$request->role === 'anggota' ? 'required' : 'nullable', 'string', 'unique:users,nis'],
            'kelas' => [$request->role === 'anggota' ? 'required' : 'nullable', 'string'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'role' => $request->role,
            'nis' => $request->role === 'anggota' ? $request->nis : null,
            'kelas' => $request->role === 'anggota' ? $request->kelas : null,
        ]);

        return back()->with('status', 'User baru berhasil didaftarkan!');
    }
    // delete
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->ids);
        User::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', count($ids) . ' akun berhasil dihapus.');
    }
    public function bulkUpdateKelas(Request $request)
    {
    $ids = $request->input('ids');
    $newKelas = $request->input('kelas');

    if ($ids && $newKelas) {
        \App\Models\User::whereIn('id', $ids)->update([
            'kelas' => $newKelas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diupdate!'
        ]);
    }

    return response()->json(['success' => false, 'message' => 'Data kurang'], 400);
    }

    public function destroy(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Akun berhasil dihapus!');
    }

    public function updatePassword(Request $request, $id)
    {
    $request->validate([
        'password' => ['required', 'string', 'min:8'],
    ]);

    try {
        $user = \App\Models\User::findOrFail($id);
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password ' . $user->name . ' berhasil diperbarui!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui password.'
        ], 500);
    }
    }

    public function destroySiswa($id)
{
    try {
        $user = \App\Models\User::where('role', 'anggota')->findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => 'Data siswa ' . $user->name . ' berhasil dihapus!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Gagal menghapus: ' . $e->getMessage()
        ], 500);
    }
}

    public function resetPassword($id)
    {
    $user = \App\Models\User::where('id', $id)->first();

    if (!$user) {
        return response()->json(['error' => 'User tidak ditemukan'], 404);
    }

    $user->update([
        'password' => \Illuminate\Support\Facades\Hash::make('12345678')
    ]);

    return response()->json(['success' => 'Password ' . $user->name . ' berhasil direset!']);
    }

    // reset pw user
    public function resetPasswordSiswa($id)
{
    try {
        $user = \App\Models\User::findOrFail($id);
        $user->update([
            'password' => \Hash::make('12345678')
        ]);

        return response()->json([
            'success' => 'Password ' . $user->name . ' berhasil direset!'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
