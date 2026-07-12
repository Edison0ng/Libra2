<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InvoiceController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('invoices')) {
            return response()->json([]);
        }
        $invoices = DB::table('invoices')->get();
        return response()->json($invoices);
    }

    public function show($id)
    {
        if (! Schema::hasTable('invoices')) return response()->json(['message' => 'Not Found'], 404);
        $inv = DB::table('invoices')->where('id', $id)->first();
        if (! $inv) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($inv);
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
