<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::query();
        if ($request->has('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }
        return response()->json($query->orderBy('tanggal_donasi', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'title' => 'required|string',
            'author' => 'required|string',
            'category' => 'required|string',
            'condition' => 'required|string',
            'note' => 'nullable|string',
            'tanggal_donasi' => 'required|date'
        ]);

        $donation = Donation::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'author' => $request->author,
            'category' => $request->category,
            'condition' => $request->condition,
            'note' => $request->note,
            'status' => 'Menunggu Verifikasi',
            'tanggal_donasi' => $request->tanggal_donasi
        ]);

        return response()->json([
            'success' => true,
            'donation' => $donation
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Donation::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);
        $donation->update($request->all());
        return response()->json($donation);
    }

    public function destroy($id)
    {
        Donation::findOrFail($id)->delete();
        return response()->json(['message' => 'Donation deleted']);
    }
}
