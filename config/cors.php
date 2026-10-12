<?php

// Konfigurasi CORS untuk backend LIBRA.
// Saat development: frontend & backend beda port (localhost) -> butuh CORS.
// Saat production: frontend di Vercel, backend di Railway -> beda domain -> WAJIB diatur di sini.

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Daftar origin frontend yang diizinkan akses API ini.
    // Isi via env FRONTEND_URL di Railway, contoh: https://libra-fmipa.vercel.app
    // Bisa lebih dari satu domain (misalnya domain production + preview Vercel),
    // pisahkan dengan koma di env FRONTEND_URL.
    'allowed_origins' => array_filter(array_map(
        'trim',
        explode(',', env('FRONTEND_URL', 'http://127.0.0.1:5500,http://localhost:5500'))
    )),

    // Izinkan juga semua subdomain preview Vercel (*.vercel.app) secara otomatis,
    // supaya deploy preview branch tetap bisa akses API tanpa perlu update env tiap kali.
    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Tetap false karena kita pakai Bearer token (localStorage), bukan cookie session.
    'supports_credentials' => false,

];
