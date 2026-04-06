<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'kepper') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        $admins = \App\Models\User::whereIn('role', ['kepper', 'petugas'])->get();

        return view('akun_admin', compact('admins'));
    }

    public function indexAnggota()
    {
        $users = \App\Models\User::where('role', 'anggota')->get();
        return view('akun_user', compact('users'));
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
        $dataArray = json_decode($request->ids, true);

        foreach ($dataArray as $item) {
            User::whereIn('id', $item['ids'])->update([
                'kelas' => $item['new_kelas']
            ]);
        }

        return back()->with('success', 'Berhasil memperbarui kelas.');
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

        $user = \App\Models\User::findOrFail($id);
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password ' . $user->name . ' berhasil diperbarui!');
    }
}
