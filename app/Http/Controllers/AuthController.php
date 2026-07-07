<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * POST /api/register
     * Membuat akun baru di tabel `users` pada Supabase.
     * Tidak ada data akun yang di-hardcode: semua pengecekan (username/email
     * sudah dipakai atau belum) dilakukan dengan query real-time ke Supabase.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname'         => ['required', 'string', 'max:150'],
            'username'         => ['required', 'string', 'max:50'],
            'email'            => ['required', 'email', 'max:150'],
            'password'         => ['required', 'string', 'min:6'],
            'confirmPassword'  => ['required', 'same:password'],
        ], [
            'confirmPassword.same' => 'Kata sandi tidak cocok!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $fullname = trim($request->input('fullname'));
        $username = trim($request->input('username'));
        $email    = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        // Cek real-time apakah username atau email sudah terdaftar di Supabase.
        $existingUsername = $this->supabase->select('users', ['username' => 'eq.' . $username]);
        if ($existingUsername->successful() && count($existingUsername->json()) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Username sudah digunakan.',
            ], 409);
        }

        $existingEmail = $this->supabase->select('users', ['email' => 'eq.' . $email]);
        if ($existingEmail->successful() && count($existingEmail->json()) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terdaftar.',
            ], 409);
        }

        // Password di-hash di sisi Laravel (bcrypt) sebelum dikirim ke Supabase.
        // Tabel `users` di Supabase TIDAK PERNAH menyimpan password polos.
        $hashedPassword = Hash::make($password);

        $insertResponse = $this->supabase->insert('users', [
            'fullname' => $fullname,
            'username' => $username,
            'email'    => $email,
            'password' => $hashedPassword,
        ]);

        if (! $insertResponse->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data ke database.',
                'detail'  => $insertResponse->json(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil! Mengarahkan ke halaman login...',
        ], 201);
    }

    /**
     * POST /api/login
     * Memeriksa kecocokan username/email + password langsung ke Supabase,
     * tanpa membedakan role (admin/user) dan tanpa data akun hardcoded.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Username dan kata sandi wajib diisi.',
            ], 422);
        }

        $identifier = trim($request->input('username'));
        $password   = $request->input('password');

        // `identifier` bisa berupa username ATAU email, jadi kita cek dua-duanya.
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;
        $filterField = $isEmail ? 'email' : 'username';
        $filterValue = $isEmail ? strtolower($identifier) : $identifier;

        $response = $this->supabase->select('users', [
            $filterField => 'eq.' . $filterValue,
        ]);

        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi database.',
            ], 500);
        }

        $rows = $response->json();

        if (count($rows) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Username/email atau kata sandi salah.',
            ], 401);
        }

        $user = $rows[0];

        if (! Hash::check($password, $user['password'])) {
            return response()->json([
                'success' => false,
                'message' => 'Username/email atau kata sandi salah.',
            ], 401);
        }

        // Login berhasil -> respon dinamis berisi nama pengguna yang login.
        // (Tidak ada pemisahan role admin/user di tahap ini, sesuai permintaan.)
        return response()->json([
            'success' => true,
            'message' => "Selamat datang kembali, {$user['fullname']}!",
            'user'    => [
                'fullname' => $user['fullname'],
                'username' => $user['username'],
                'email'    => $user['email'],
            ],
        ], 200);
    }
}
