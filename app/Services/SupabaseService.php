<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

/**
 * SupabaseService
 *
 * Wrapper tipis di atas Supabase REST API (PostgREST) supaya AuthController
 * tidak perlu tahu detail HTTP header / endpoint Supabase.
 *
 * PENTING:
 *  - Service ini menggunakan SERVICE ROLE KEY (bukan anon key), karena semua
 *    query dijalankan dari sisi server (backend Laravel), bukan dari browser.
 *  - Service Role Key otomatis bypass Row Level Security (RLS) di Supabase,
 *    jadi RLS boleh (dan sebaiknya) tetap AKTIF di tabel users, tanpa perlu
 *    membuat policy untuk role anon/authenticated. Ini mencegah tabel users
 *    diakses langsung dari frontend menggunakan anon key.
 *  - Jangan pernah expose SERVICE ROLE KEY ke kode frontend/browser.
 */
class SupabaseService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.supabase.url'), '/') . '/rest/v1';
        $this->apiKey  = config('services.supabase.service_key');
    }

    protected function client()
    {
        return Http::withHeaders([
            'apikey'        => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * Ambil baris dari sebuah tabel dengan filter PostgREST.
     * Contoh: $supabase->select('users', ['username' => 'eq.rahmad']);
     */
    public function select(string $table, array $filters = [], array $extra = []): Response
    {
        $query = array_merge($filters, $extra);
        return $this->client()->get("/{$table}", $query);
    }

    /**
     * Insert satu baris baru ke tabel. Mengembalikan representasi baris
     * yang baru dibuat (Prefer: return=representation).
     */
    public function insert(string $table, array $data): Response
    {
        return $this->client()
            ->withHeaders(['Prefer' => 'return=representation'])
            ->post("/{$table}", $data);
    }
}
