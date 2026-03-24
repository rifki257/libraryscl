<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->role !== 'kepper') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak punya akses!');
        }
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Cek akses
        if (auth()->user()->role !== 'kepper') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak punya akses!');
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

        return redirect()->route('dashboard')->with('success', 'User baru berhasil didaftarkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function bulkUpdateKelas(Request $request)
    {
        $dataArray = json_decode($request->ids, true); // Mengambil data dari input id="bulk-ids"

        foreach ($dataArray as $item) {
            User::whereIn('id', $item['ids'])->update([
                'kelas' => $item['new_kelas']
            ]);
        }

        return back()->with('success', 'Berhasil memperbarui kelas.');
    }

    /**
     * Remove the specified resource from storage.
     */
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
