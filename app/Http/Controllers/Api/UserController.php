<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\LibraChangeEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // PATCH /api/profil
    // Pengguna mengubah nama, nomor telepon, dan/atau alamat miliknya sendiri.
    // Email & password punya endpoint terpisah karena butuh konfirmasi.
    public function updateProfil(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            // NIM hanya angka: ini sekaligus mencegah NIM diubah menjadi 'admin' / 'kurir'
            // (peran admin dan kurir ditentukan dari nilai NIM).
            'nim'          => ['sometimes', 'required', 'string', 'max:50', 'regex:/^[0-9]+$/', Rule::unique('users', 'nim')->ignore($user->id)],
            'fakultas'     => ['sometimes', 'required', 'string', 'max:255'],
            'nama_lengkap' => ['sometimes', 'required', 'string', 'max:255'],
            'no_telepon'   => ['sometimes', 'required', 'string', 'min:8', 'max:50', 'regex:/^[0-9]+$/'],
            'alamat_kirim' => ['sometimes', 'required', 'string', 'min:10', 'max:500'],
        ], [
            'nim.required'          => 'NIM tidak boleh kosong.',
            'nim.regex'             => 'NIM hanya boleh berisi angka.',
            'nim.unique'            => 'NIM ini sudah dipakai akun lain.',
            'fakultas.required'     => 'Program studi tidak boleh kosong.',
            'nama_lengkap.required' => 'Nama tidak boleh kosong.',
            'no_telepon.required'   => 'Nomor telepon tidak boleh kosong.',
            'no_telepon.regex'      => 'Nomor telepon hanya boleh berisi angka.',
            'no_telepon.min'        => 'Nomor telepon minimal 8 digit.',
            'alamat_kirim.required' => 'Alamat tidak boleh kosong.',
            'alamat_kirim.min'      => 'Alamat terlalu pendek, tulis alamat selengkapnya.',
            'alamat_kirim.max'      => 'Alamat maksimal 500 karakter.',
        ]);

        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang diubah.'], 422);
        }

        if (array_key_exists('nim', $data)) {
            // Akun admin / kurir dikenali dari NIM-nya, jadi tidak boleh diganti lewat profil.
            if ($user->isAdmin() || $user->isKurir()) {
                return response()->json(['success' => false, 'message' => 'Akun ini tidak dapat mengubah NIM.'], 403);
            }
            // NIM juga dipakai untuk login (bersama username), jangan sampai bentrok dengan username akun lain.
            $bentrok = User::where('username', $data['nim'])->where('id', '!=', $user->id)->exists();
            if ($bentrok) {
                return response()->json(['success' => false, 'message' => 'NIM ini sudah dipakai akun lain.'], 422);
            }
        }

        foreach ($data as $field => $value) {
            $user->{$field} = trim($value);
        }
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $user,
        ]);
    }

    // POST /api/profil/email   body: { email, password }
    // Email baru disimpan dulu di pending_email dan dikirimi tautan verifikasi.
    // Email lama tetap aktif sampai tautan diklik (lihat route email.change.verify).
    public function changeEmail(Request $request)
    {
        $user = $request->user();
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'email'    => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id, 'id'),
                Rule::unique('users', 'pending_email')->ignore($user->id, 'id'),
            ],
            'password' => ['required', 'string'],
        ], [
            'email.required'  => 'Email baru wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.unique'    => 'Email ini sudah dipakai akun lain.',
            'password.required' => 'Masukkan password saat ini untuk konfirmasi.',
        ]);

        if (! Hash::check($data['password'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password saat ini salah.'], 422);
        }
        if (strcasecmp($data['email'], (string) $user->email) === 0) {
            return response()->json(['success' => false, 'message' => 'Email baru sama dengan email saat ini.'], 422);
        }

        $user->pending_email = $data['email'];
        $user->save();

        try {
            Notification::route('mail', $data['email'])->notify(
                new LibraChangeEmailNotification((string) $user->id, $data['email'], (string) ($user->nama_lengkap ?? ''))
            );
        } catch (\Throwable $e) {
            report($e);
            $user->pending_email = null;
            $user->save();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email verifikasi ke alamat baru. Periksa pengaturan email server lalu coba lagi.',
            ], 500);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Tautan verifikasi dikirim ke ' . $data['email'] . '.',
            'pending_email' => $data['email'],
        ]);
    }

    // POST /api/profil/password   body: { password_lama, password_baru, konfirmasi_password }
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'password_lama'       => ['required', 'string'],
            'password_baru'       => ['required', 'string', 'min:6', 'different:password_lama'],
            'konfirmasi_password' => ['required', 'same:password_baru'],
        ], [
            'password_lama.required'       => 'Password saat ini wajib diisi.',
            'password_baru.required'       => 'Password baru wajib diisi.',
            'password_baru.min'            => 'Password baru minimal 6 karakter.',
            'password_baru.different'      => 'Password baru harus berbeda dari password saat ini.',
            'konfirmasi_password.required' => 'Konfirmasi password wajib diisi.',
            'konfirmasi_password.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        $user = $request->user();

        if (! Hash::check($data['password_lama'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password saat ini salah.'], 422);
        }

        $user->password = $data['password_baru']; // di-hash otomatis oleh cast 'hashed'
        $user->save();

        // Keluarkan sesi lain (perangkat lain) supaya password lama tidak bisa dipakai lagi.
        $current = $user->currentAccessToken();
        if ($current && method_exists($current, 'getKey')) {
            $user->tokens()->where('id', '!=', $current->getKey())->delete();
        }

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
    }

    // PATCH /api/profil/alamat
    // Pengguna memperbarui alamat pengantarannya sendiri (mis. pindah rumah/kos).
    // Hanya mengubah akun yang sedang login; pesanan lama tidak berubah karena
    // alamat sudah disalin ke pesanan saat pesanan dibuat.
    public function updateAlamat(Request $request)
    {
        $data = $request->validate([
            'alamat_kirim' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'alamat_kirim.required' => 'Alamat tidak boleh kosong.',
            'alamat_kirim.min'      => 'Alamat terlalu pendek, tulis alamat selengkapnya.',
            'alamat_kirim.max'      => 'Alamat maksimal 500 karakter.',
        ]);

        $user = $request->user();
        $user->alamat_kirim = trim($data['alamat_kirim']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui.',
            'user'    => $user,
        ]);
    }

    // GET /api/users
    public function index()
    {
        return response()->json(User::all());
    }

    // GET /api/users/{id}
    public function show($id)
    {
        $user = User::find($id);
        if (! $user) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($user);
    }

    public function showByUsername($username)
    {
        // Handle username dengan spasi atau dash
        $username = str_replace('-', ' ', $username);
        
        // Cari user berdasarkan username (case insensitive) ATAU NIM
        $user = User::where(function ($query) use ($username) {
            $query->whereRaw('LOWER(username) = ?', [strtolower($username)])
                  ->orWhere('nim', $username);
        })->first();

        if (! $user) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        $books = DB::table('books')->get();

        // Daftar genre unik (buang yg kosong/null), diurutkan abjad, utk dropdown filter.
        $genres = $books->pluck('genre')->filter()->unique()->sort()->values();

        return view('libra', [
            'books'    => $books,
            'genres'   => $genres,
        ]);
    }

    // POST /api/users
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'nim'           => 'required|string|max:50',
            'fakultas'      => 'nullable|string|max:255',
            'no_telepon'    => 'nullable|string|max:30',
            'alamat_kirim'  => 'nullable|string',
            'avatar_url'    => 'nullable|string'
        ]);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    // PUT /api/users/{id}
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap'  => 'sometimes|required|string|max:255',
            'nim'           => 'sometimes|required|string|max:50',
            'fakultas'      => 'nullable|string|max:255',
            'no_telepon'    => 'nullable|string|max:30',
            'alamat_kirim'  => 'nullable|string',
            'avatar_url'    => 'nullable|string'
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    // DELETE /api/users/{id}
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}