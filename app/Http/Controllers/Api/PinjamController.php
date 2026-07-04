<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pinjam;
use Illuminate\Http\Request;

class PinjamController extends Controller
{
    public function index()
    {
        return response()->json(Pinjam::all());
    }

    public function store(Request $request)
    {
        $pinjam = Pinjam::create($request->all());

        return response()->json($pinjam, 201);
    }

    public function show(string $id)
    {
        return response()->json(Pinjam::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $pinjam = Pinjam::findOrFail($id);

        $pinjam->update($request->all());

        return response()->json($pinjam);
    }

    public function destroy(string $id)
    {
        Pinjam::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Pinjam deleted'
        ]);
    }
}