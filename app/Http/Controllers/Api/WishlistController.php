<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * GET /api/wishlist?user_id=...
     * Ambil semua book_id yang di-wishlist oleh user tsb.
     */
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
        ]);

        $wishlist = Wishlist::where('user_id', $request->user_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($wishlist);
    }

    /**
     * POST /api/wishlist
     * body: { user_id, book_id }
     * Tambahkan buku ke wishlist user. Kalau sudah ada, tidak dobel
     * (idempotent) supaya frontend tidak perlu cek dulu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'string'],
            'book_id' => ['required', 'string'],
        ]);

        $item = Wishlist::firstOrCreate([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);

        return response()->json($item, 201);
    }

    /**
     * DELETE /api/wishlist?user_id=...&book_id=...
     * Hapus buku dari wishlist user.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'book_id' => ['required'],
        ]);

        Wishlist::where('user_id', $request->user_id)
            ->where('book_id', $request->book_id)
            ->delete();

        return response()->json(['success' => true]);
    }
}
