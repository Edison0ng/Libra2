<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications?user_id=...
     * Mengambil daftar notifikasi milik user tertentu, terbaru lebih dulu.
     */
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
        ]);

        $notifications = Notification::where('user_id', $request->user_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($notifications);
    }

    /**
     * PATCH /api/notifications/read-all?user_id=...
     * Menandai semua notifikasi milik user tertentu sebagai sudah dibaca.
     */
    public function readAll(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
        ]);

        Notification::where('user_id', $request->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}