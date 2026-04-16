<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi berhasil dibaca'
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }
}