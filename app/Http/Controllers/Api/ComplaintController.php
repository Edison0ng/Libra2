<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    // GET /api/complaints
    public function index()
    {
        return response()->json(
            Complaint::all()
        );
    }

    // POST /api/complaints
    public function store(Request $request)
    {
        $validated = $request->validate([
            'peminjam_id' => 'required',
            'pesan'       => 'required|string',
            'status'      => 'required|string'
        ]);

        $complaint = Complaint::create($validated);

        return response()->json([
            'message' => 'Complaint berhasil ditambahkan',
            'data' => $complaint
        ], 201);
    }

    // GET /api/complaints/{id}
    public function show(string $id)
    {
        return response()->json(
            Complaint::findOrFail($id)
        );
    }

    // PUT /api/complaints/{id}
    public function update(Request $request, string $id)
    {
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'peminjam_id' => 'sometimes|required',
            'pesan'       => 'sometimes|required|string',
            'status'      => 'sometimes|required|string'
        ]);

        $complaint->update($validated);

        return response()->json([
            'message' => 'Complaint berhasil diupdate',
            'data' => $complaint
        ]);
    }

    // DELETE /api/complaints/{id}
    public function destroy(string $id)
    {
        $complaint = Complaint::findOrFail($id);

        $complaint->delete();

        return response()->json([
            'message' => 'Complaint berhasil dihapus'
        ]);
    }
}