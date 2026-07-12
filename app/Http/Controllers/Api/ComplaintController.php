<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ComplaintController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('complaints')) {
            return response()->json([]);
        }
        $rows = DB::table('complaints')->get();
        return response()->json($rows);
    }

    public function show($id)
    {
        if (! Schema::hasTable('complaints')) return response()->json(['message' => 'Not Found'], 404);
        $row = DB::table('complaints')->where('id', $id)->first();
        if (! $row) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($row);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 405);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Not implemented'], 405);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Not implemented'], 405);
    }
}
