<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LIBRA — Verifikasi Email</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<style>
  :root{ --bg:#0A0E17; --panel:#10172A; --line:#222B42; --text:#E7EAF2; --muted:#8A92A6;
         --accent:#3D6BFF; --success:#3DDC97; --danger:#FF5470; }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{ min-height:100vh; display:flex; align-items:center; justify-content:center; padding:16px;
        background:var(--bg); color:var(--text); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
  .card{ width:100%; max-width:420px; background:var(--panel); border:1px solid var(--line);
         border-radius:16px; padding:36px 28px; text-align:center; }
  .logo{ font-family:'Sora',sans-serif; font-weight:800; letter-spacing:.12em; font-size:22px; margin-bottom:24px; }
  .icon{ width:56px; height:56px; border-radius:50%; margin:0 auto 18px; display:flex;
         align-items:center; justify-content:center; font-size:26px; font-weight:700; }
  .ok .icon{ background:rgba(61,220,151,.12); color:var(--success); }
  .bad .icon{ background:rgba(255,84,112,.12); color:var(--danger); }
  h1{ font-family:'Sora',sans-serif; font-size:20px; margin-bottom:10px; }
  p{ color:var(--muted); font-size:14px; line-height:1.6; margin-bottom:24px; }
  a.btn{ display:inline-block; background:var(--accent); color:#fff; text-decoration:none;
         font-weight:600; font-size:14px; padding:12px 24px; border-radius:10px; }
</style>
</head>
<body>
@php
  $content = [
    'success' => ['ok',  '✓', 'Email berhasil diverifikasi', 'Akun LIBRA Anda sudah aktif. Silakan login untuk mulai menggunakan layanan.'],
    'already' => ['ok',  '✓', 'Email sudah terverifikasi', 'Akun Anda sudah aktif sebelumnya. Silakan login.'],
    'expired' => ['bad', '!', 'Tautan kedaluwarsa atau tidak valid', 'Tautan verifikasi sudah tidak berlaku. Login dengan NIM dan kata sandi Anda, lalu pilih "Kirim ulang verifikasi" untuk mendapat tautan baru.'],
    'invalid' => ['bad', '!', 'Tautan tidak cocok dengan akun', 'Email akun mungkin sudah diganti atau akun tidak ditemukan. Login lalu minta tautan verifikasi baru.'],
  ][$status] ?? ['bad', '!', 'Terjadi kesalahan', 'Silakan coba lagi.'];
@endphp
  <main class="card {{ $content[0] }}">
    <div class="logo">LIBRA</div>
    <div class="icon">{{ $content[1] }}</div>
    <h1>{{ $content[2] }}</h1>
    <p>{{ $content[3] }}</p>
    <a class="btn" href="{{ route('login') }}">Ke Halaman Login</a>
  </main>
</body>
</html>