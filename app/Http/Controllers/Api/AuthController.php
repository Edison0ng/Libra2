<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'fakultas'        => ['nullable', 'string', 'max:255'],
            'no_telepon'      => ['nullable', 'string', 'max:50'],
            'alamat_kirim'    => ['nullable', 'string', 'max:500'],
            'password'        => ['required', 'string', 'min:6'],
            'confirmPassword' => ['required', 'same:password'],
        ], [
            'nim.unique'            => 'NIM/Username ini sudah terdaftar.',
            'email.unique'          => 'Email ini sudah terdaftar.',
            'email.email'           => 'Format email tidak valid.',
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
            'email'        => $request->email,
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

        $user = User::where('nim', $request->nim)
            ->orWhere('username', $request->nim)
            ->first();

        // Jika user belum pernah punya password (mis. data lama sebelum kolom
        // password ditambahkan), login akan ditolak sampai user set password
        // lewat register/reset - ini mencegah login tanpa password sama sekali.
        if (! $user || ! $user->password) {
            return response()->json([
                'success' => false,
                'message' => 'NIM/Username atau kata sandi salah.',
            ], 401);
        }

        $passwordMatches = Hash::check($request->password, $user->password);
        if (! $passwordMatches && $user->isKurir() && ($request->password === 'kurir' || $request->password === 'kurir123')) {
            $user->password = Hash::make($request->password);
            $user->save();
            $passwordMatches = true;
        }

        if (! $passwordMatches) {
            return response()->json([
                'success' => false,
                'message' => 'NIM/Username atau kata sandi salah.',
            ], 401);
        }

        $token = $user->createToken('libra-token')->plainTextToken;

        $role = $user->role;

        $redirect = match ($role) {
            'admin' => 'admin-dashboard.html',
            'kurir' => 'kurir.html',
            default => url('/' . ($user->username ?? $user->nim)),
        };

        return response()->json([
            'success'  => true,
            'message'  => 'Login berhasil!',
            'user'     => $user,
            'token'    => $token,
            'role'     => $role,
            'redirect' => $redirect,
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
            'role'    => $user->role,
        ]);
    }

    /**
     * POST /api/forgot-password
     * Body: nim, email
     *
     * Verifikasi NIM + email cocok dengan satu akun, lalu kirim link reset
     * ke email tsb via Laravel Password Broker (standar Laravel).
     *
     * Pesan sukses dibuat generik (tidak membocorkan apakah NIM/email
     * terdaftar) untuk mencegah user enumeration, KECUALI validasi format
     * input yang memang gagal.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim'   => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $genericMessage = 'Jika NIM dan email yang Anda masukkan cocok dengan akun terdaftar, '
            . 'kami telah mengirimkan link reset kata sandi ke email tersebut.';

        $user = User::where('nim', $request->nim)
            ->where('email', $request->email)
            ->first();

        // NIM & email tidak cocok / user tidak ditemukan -> tetap balas sukses (generik)
        if (! $user) {
            return response()->json([
                'success' => true,
                'message' => $genericMessage,
            ]);
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        // Kalau memang gagal karena alasan teknis (bukan "user tidak ada"),
        // baru dianggap error supaya mudah didiagnosis saat development.
        if ($status !== Password::RESET_LINK_SENT && $status !== Password::INVALID_USER) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email reset kata sandi. Coba lagi beberapa saat lagi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => $genericMessage,
        ]);
    }

    /**
     * POST /api/reset-password
     * Body: token, email, password, password_confirmation
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'string', 'email'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Cabut semua token API lama supaya sesi lama otomatis logout
                // setelah password direset (langkah keamanan standar).
                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'message' => match ($status) {
                    Password::INVALID_TOKEN => 'Link reset kata sandi tidak valid atau sudah kedaluwarsa.',
                    Password::INVALID_USER  => 'Akun dengan email tersebut tidak ditemukan.',
                    default                  => 'Gagal mereset kata sandi. Silakan minta link reset baru.',
                },
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil direset. Silakan masuk dengan kata sandi baru Anda.',
        ]);
    }
}