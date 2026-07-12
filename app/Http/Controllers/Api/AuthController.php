<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * POST /api/register
     * Body: nama_lengkap, nim, fakultas, no_telepon, alamat_kirim, password, confirmPassword
     *
     * Field disesuaikan dengan struktur tabel `users` yang sudah ada di Supabase
     * (dibuat oleh bagian admin), BUKAN username/email seperti versi sebelumnya.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap'    => ['required', 'string', 'max:255'],
            'nim'             => ['required', 'string', 'max:50', 'unique:users,nim'],
            'fakultas'        => ['nullable', 'string', 'max:255'],
            'no_telepon'      => ['nullable', 'string', 'max:50'],
            'alamat_kirim'    => ['nullable', 'string', 'max:500'],
            'password'        => ['required', 'string', 'min:6'],
            'confirmPassword' => ['required', 'same:password'],
        ], [
            'nim.unique'            => 'NIM/Username ini sudah terdaftar.',
            'confirmPassword.same'  => 'Kata sandi tidak cocok!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'nim'          => $request->nim,
            'fakultas'     => $request->fakultas,
            'no_telepon'   => $request->no_telepon,
            'alamat_kirim' => $request->alamat_kirim,
            'password'     => Hash::make($request->password),
        ]);

        $token = $user->createToken('libra-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil!',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * POST /api/login
     * Body: nim, password
     * (form frontend masih memberi label "Username", tapi isinya NIM mahasiswa
     * atau username khusus admin, sesuai kesepakatan tim)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim'      => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'NIM/Username dan kata sandi wajib diisi.',
            ], 422);
        }

        $user = User::where('nim', $request->nim)->first();

        // Jika user belum pernah punya password (mis. data lama sebelum kolom
        // password ditambahkan), login akan ditolak sampai user set password
        // lewat register/reset - ini mencegah login tanpa password sama sekali.
        if (! $user || ! $user->password || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIM/Username atau kata sandi salah.',
            ], 401);
        }

        $token = $user->createToken('libra-token')->plainTextToken;

        return response()->json([
            'success'  => true,
            'message'  => 'Login berhasil!',
            'user'     => $user,
            'token'    => $token,
            'role'     => $user->isAdmin() ? 'admin' : 'mahasiswa',
            'redirect' => $user->isAdmin() ? 'admin-dashboard.html' : 'index.html',
        ]);
    }

    /**
     * POST /api/logout (butuh header Authorization: Bearer <token>)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
        ]);
    }

    /**
     * GET /api/user (butuh header Authorization: Bearer <token>)
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user'    => $user,
            'role'    => $user->isAdmin() ? 'admin' : 'mahasiswa',
        ]);
    }
}
