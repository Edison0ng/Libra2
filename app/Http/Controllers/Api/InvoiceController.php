<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // GET /api/invoices
    public function index()
    {
        return response()->json(Invoice::all());
    }

    // POST /api/invoices
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_isbn'        => 'required|string',
            'peminjam_id'      => 'required',
            'tanggal_pinjam'   => 'required|date',
            'tanggal_kembali'  => 'nullable|date',
            'status'           => 'required|string'
        ]);

        $invoice = Invoice::create($validated);

        return response()->json([
            'message' => 'Invoice berhasil ditambahkan',
            'data' => $invoice
        ], 201);
    }

    // GET /api/invoices/{id}
    public function show(string $id)
    {
        return response()->json(
            Invoice::findOrFail($id)
        );
    }

    // PUT /api/invoices/{id}
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'book_isbn'        => 'sometimes|required|string',
            'peminjam_id'      => 'sometimes|required',
            'tanggal_pinjam'   => 'sometimes|required|date',
            'tanggal_kembali'  => 'nullable|date',
            'status'           => 'sometimes|required|string'
        ]);

        $invoice->update($validated);

        return response()->json([
            'message' => 'Invoice berhasil diupdate',
            'data' => $invoice
        ]);
    }

    // DELETE /api/invoices/{id}
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->delete();

        return response()->json([
            'message' => 'Invoice berhasil dihapus'
        ]);
    }
}
