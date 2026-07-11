<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class BookController extends Controller
{
    public function index()
    {
        $books = DB::table('books')->get();
        
        // Ambil user default (user pertama)
        $userData = User::first();
        
        if (!$userData) {
            $userData = (object) [
                'id' => 1,
                'name' => 'Ahmad Fauzi',
                'username' => 'ahmadfauzi',
                'nim' => '220194850',
                'fakultas' => 'Fakultas Ilmu Komputer',
                'avatar_url' => null,
                'email' => 'ahmad@example.com'
            ];
        }
        
        return view('libra', [
            'books' => $books,
            'userData' => $userData
        ]);
    }
}