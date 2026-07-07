<?php

// Tambahkan blok 'supabase' di bawah ini ke dalam array yang di-return
// oleh file config/services.php pada project Laravel Anda.
// Jangan mengganti seluruh isi file, cukup gabungkan key ini ke array yang ada.

return [

    // ... entri service lain yang sudah ada (mailgun, aws, dll) tetap di sini ...

    'supabase' => [
        'url'         => env('SUPABASE_URL'),
        'service_key' => env('SUPABASE_SERVICE_KEY'),
    ],

];
