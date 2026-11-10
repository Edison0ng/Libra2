<!DOCTYPE html>
<html lang="id">
<head>
<script>
  /* Ingat Saya: kalau tidak dicentang saat login, sesi hanya berlaku selama browser/tab dibuka */
  (function(){try{if(localStorage.getItem('libra_remember')!=='1'&&sessionStorage.getItem('libra_active')!=='1'){localStorage.removeItem('libra_token');localStorage.removeItem('libra_user');}}catch(e){}})();
</script>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LIBRA — Login</title>
<script>
  (function() {
    const token = localStorage.getItem('libra_token');
    const userStr = localStorage.getItem('libra_user');
    if (token && userStr) {
      try {
        const user = JSON.parse(userStr);
        const isAdmin = user.role === 'admin' || user.nim === 'admin' || user.username === 'admin';
        const isKurir = user.role === 'kurir' || user.nim === 'kurir' || user.username === 'kurir';
        if (isAdmin) {
          window.location.href = '/admin-dashboard.html';
        } else if (isKurir) {
          window.location.href = '/kurir.html';
        } else {
          window.location.href = '/' + (user.username || user.nim);
        }
      } catch (e) {}
    }
  })();
</script>

<!-- Google Fonts: Sora untuk heading/brand, Inter untuk body -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

<style>
  /* ===================== ROOT DESIGN TOKENS (Proto 4 LIBRA) ===================== */
  :root{
    --bg: #FFFFFF;
    --panel-solid: #FFFFFF;
    --panel-2: #F4F6FA;
    --line: #E3E7F0;
    --text: #1A2033;
    --muted: #6B7280;
    --muted-2: #8A92A6; /* kontras ditingkatkan dari #AEB4C2 utk aksesibilitas di atas putih */
    --accent: #3D6BFF;
    --accent-2: #2F56E0;
    --danger: #E0264F;
    --radius: 14px;

    /* turunan warna untuk badge/status, supaya konsisten dgn tema */
    --success: #18A869;
    --success-bg: rgba(24, 168, 105, 0.10);
    --danger-bg: rgba(224, 38, 79, 0.08);
    --accent-bg: rgba(61, 107, 255, 0.10);
  }

  *{ margin:0; padding:0; box-sizing:border-box; }

  html, body{
    height:100%;
    background: var(--bg);
    color: var(--text);
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;
  }

  a{ color: var(--accent-2); text-decoration:none; }
  a:hover{ text-decoration: underline; }

  /* ===================== LAYOUT UTAMA: 2 KOLOM ===================== */
  .auth-shell{
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr; /* panel info di kiri dihapus, tinggal form login */
  }

  /* Background sedikit gradasi supaya tidak flat datar */
  .auth-shell{
    background:
      radial-gradient(900px 500px at 0% 0%, rgba(61,107,255,0.04), transparent 60%),
      radial-gradient(700px 500px at 100% 100%, rgba(61,107,255,0.03), transparent 60%),
      var(--bg);
  }

  /* ===================== KOLOM KIRI: PANEL INFO & SIRKULASI ===================== */
  .info-panel{
    padding: 56px 64px;
    display:flex;
    flex-direction: column;
    background: #FAFBFD;
    border-right: 1px solid var(--line);
  }

  .brand-row{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom: 48px;
  }

  .brand-row .logo-mark{
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Sora', sans-serif;
    font-weight:800;
    font-size:16px;
    color:#fff;
    flex-shrink:0;
  }

  .brand-row .brand-name{
    font-family:'Sora', sans-serif;
    font-weight:700;
    font-size: 18px;
    letter-spacing: 0.5px;
    color: var(--text);
  }

  /* Judul sekunder pada panel info (konten pendukung, bukan judul utama dokumen) */
  .info-panel h2{
    font-family:'Sora', sans-serif;
    font-weight:700;
    font-size: 28px;
    line-height:1.3;
    margin-bottom: 8px;
  }

  .info-panel .subtitle{
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 32px;
  }

  /* Ledger / log style container */
  .ledger{
    background: var(--panel-solid);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
    overflow: hidden;
    display:flex;
    flex-direction: column;
    margin-bottom: auto;
  }

  .ledger-header{
    padding: 16px 20px;
    border-bottom: 1px solid var(--line);
    display:flex;
    align-items:center;
    justify-content:space-between;
    background: var(--panel-2);
  }

  .ledger-header .label{
    font-size: 12px;
    font-weight:600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }

  /* ledger-row: satu baris informasi/log */
.ledger-row{
  display:flex;
  align-items:flex-start;
  gap:14px;
  padding: 16px 20px;
  border-bottom: 1px solid var(--line);
}

.ledger-row:last-child{ border-bottom: none; }

.ledger-row:hover{
  background: rgba(16,24,40,0.02);
}

.badge{
  flex-shrink:0;
  flex-grow:0;
  width: 104px;              /* lebar tetap & seragam, kunci utama perataan */
  box-sizing: border-box;
  text-align: center;        /* teks badge tetap center meski lebar seragam */
  font-size: 11px;
  font-weight:700;
  padding: 4px 6px;
  border-radius: 999px;
  letter-spacing: 0.03em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;   /* jaga-jaga jika ada label lebih panjang di masa depan */
  margin-top: 2px;
}

.badge.new{ background: var(--accent-bg); color: var(--accent-2); }
.badge.empty{ background: var(--danger-bg); color: var(--danger); }
.badge.update{ background: var(--success-bg); color: var(--success); }

.ledger-row .row-content{
  flex: 1 1 auto;
  min-width: 0;              /* mencegah overflow mendorong lebar kolom badge */
}

.ledger-row .row-content p{
  font-size: 13.5px;
  line-height:1.55;
  color: var(--text);
}

.ledger-row .row-content .row-time{
  display:block;
  margin-top: 4px;
  font-size: 11.5px;
  color: var(--muted-2);
}

  .ledger-footer{
    padding: 14px 20px;
    text-align:center;
    font-size: 12px;
    color: var(--muted-2);
    border-top: 1px solid var(--line);
  }

  /* ===================== KOLOM KANAN: CARD LOGIN ===================== */
  .form-panel{
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 40px;
  }

  .login-card{
    width: 100%;
    max-width: 400px;
    background: var(--panel-solid);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    box-shadow: 0 4px 24px rgba(16, 24, 40, 0.06);
    padding: 40px 36px;
  }

  .login-card .card-logo{
    text-align:center;
    margin-bottom: 6px;
  }

  /* Judul utama dokumen: identitas LIBRA pada kartu login (satu-satunya h1 halaman) */
  .login-card .card-logo .logo-text{
    display:block;
    font-family:'Sora', sans-serif;
    font-weight:800;
    font-size: 32px;
    letter-spacing: 1px;
    background: linear-gradient(135deg, var(--text), var(--accent-2));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
  }

  .login-card .card-tagline{
    text-align:center;
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 32px;
  }

  /* err-banner: kotak pesan error bawaan Proto 4 */
  .err-banner{
    display:none; /* default hidden, di-toggle lewat JS */
    align-items:flex-start;
    gap:10px;
    background: var(--danger-bg);
    border: 1px solid rgba(255, 84, 112, 0.35);
    color: var(--danger);
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13px;
    line-height:1.4;
    margin-bottom: 20px;
  }

  .err-banner.show{ display:flex; }

  .info-banner{
    display:none; align-items:flex-start; gap:10px;
    background: rgba(61, 220, 151, 0.12);
    border: 1px solid rgba(61, 220, 151, 0.35);
    color: #3DDC97; border-radius:10px; padding:12px 14px;
    font-size:13px; line-height:1.4; margin-bottom:20px;
  }
  .info-banner.show{ display:flex; }

  .verify-box{
    display:none; margin-bottom:20px; padding:14px;
    border:1px solid var(--line, #222B42); border-radius:10px;
    background: rgba(255,255,255,0.03); font-size:13px; line-height:1.5;
  }
  .verify-box.show{ display:block; }
  .verify-box input{
    width:100%; margin:10px 0; padding:10px 12px; border-radius:8px;
    border:1px solid var(--line, #222B42); background:transparent;
    color:inherit; font-size:13px;
  }
  .verify-box button{
    padding:9px 14px; border-radius:8px; border:0; cursor:pointer;
    background:#3D6BFF; color:#fff; font-weight:600; font-size:13px;
  }
  .verify-box button:disabled{ opacity:.6; cursor:default; }
  #verifyMsg{ margin-top:10px; }

  .err-banner .err-icon{
    flex-shrink:0;
    font-weight:700;
  }

  .field-group{
    margin-bottom: 18px;
  }

  .field-group label{
    display:block;
    font-size: 12.5px;
    font-weight:600;
    color: var(--muted);
    margin-bottom: 7px;
  }

  .input-wrap{
    position: relative;
    display:flex;
    align-items:center;
    background: var(--panel-2);
    border: 1px solid var(--line);
    border-radius: 10px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .input-wrap:focus-within{
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(61,107,255,0.15);
  }

  .input-wrap .input-icon{
    width: 42px;
    flex-shrink:0;
    display:flex;
    align-items:center;
    justify-content:center;
    color: var(--muted-2);
  }

  .input-wrap input{
    flex:1;
    background: transparent;
    border: none;
    outline: none;
    color: var(--text);
    font-family:'Inter', sans-serif;
    font-size: 14px;
    padding: 12px 12px 12px 0;
  }

  .input-wrap input::placeholder{
    color: var(--muted-2);
  }

  .toggle-eye{
    width: 42px;
    flex-shrink:0;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    color: var(--muted-2);
    background:none;
    border:none;
    padding:0;
  }

  .toggle-eye:hover{ color: var(--muted); }

  /* baris remember me + lupa password */
  .options-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom: 24px;
    font-size: 13px;
  }

  .remember-me{
    display:flex;
    align-items:center;
    gap:8px;
    color: var(--muted);
    cursor:pointer;
    user-select:none;
  }

  .remember-me input[type="checkbox"]{
    width: 15px;
    height: 15px;
    accent-color: var(--accent);
    cursor:pointer;
  }

  .forgot-link{
    font-size: 13px;
    color: var(--accent-2);
  }

  /* tombol utama */
  .btn-primary{
    width:100%;
    background: var(--accent);
    color:#fff;
    border:none;
    border-radius: 10px;
    padding: 13px 0;
    font-family:'Inter', sans-serif;
    font-weight:600;
    font-size: 14.5px;
    cursor:pointer;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
  }

  .btn-primary:hover{
    background: var(--accent-2);
    box-shadow: 0 6px 18px rgba(61,107,255,0.35);
  }

  .btn-primary:active{
    transform: translateY(1px);
  }

  .signup-footer{
    text-align:center;
    margin-top: 24px;
    font-size: 13px;
    color: var(--muted);
  }

  .signup-footer a{
    font-weight:600;
  }

  /* ===================== RESPONSIVE: STACKED 1 KOLOM DI MOBILE ===================== */
  @media (max-width: 880px){
    .auth-shell{
      grid-template-columns: 1fr;
    }

    .info-panel{
      border-right: none;
      border-bottom: 1px solid var(--line);
      padding: 36px 24px;
    }

    .info-panel h2{ font-size: 22px; }

    .form-panel{
      padding: 32px 20px 56px;
    }

    .login-card{
      padding: 32px 24px;
    }
  }

  /* ===================== HIASAN: RAK BUKU, PENANDA HALAMAN, LATAR TITIK ===================== */
  .auth-shell{
    position: relative;
    overflow: hidden;
    padding-bottom: 168px;            /* ruang untuk rak buku di dasar halaman */
  }

  /* Titik-titik halus di pojok kiri atas, memudar ke tengah */
  .auth-shell::before{
    content:'';
    position:absolute;
    inset:0;
    background-image: radial-gradient(rgba(61,107,255,0.22) 1px, transparent 1.3px);
    background-size: 22px 22px;
    -webkit-mask-image: radial-gradient(620px 380px at 8% 6%, #000, transparent 72%);
            mask-image: radial-gradient(620px 380px at 8% 6%, #000, transparent 72%);
    pointer-events:none;
  }

  /* Cahaya lembut di balik kartu supaya kartu terasa "terangkat" */
  .auth-shell::after{
    content:'';
    position:absolute;
    left:50%; top:46%;
    width: 760px; height: 760px;
    transform: translate(-50%, -50%);
    background: radial-gradient(closest-side, rgba(61,107,255,0.08), transparent);
    pointer-events:none;
  }

  .form-panel, .login-card{ position: relative; z-index: 1; }

  /* Penanda halaman (bookmark) yang menjuntai dari sisi atas kartu */
  .login-card::before{
    content:'';
    position:absolute;
    top:-12px;
    right:30px;
    width:24px;
    height:58px;
    background: linear-gradient(180deg, #F0745F, #E0503A);
    clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);
    filter: drop-shadow(0 2px 2px rgba(16,24,40,0.18));
  }

  /* Rak buku */
  .shelf{
    position:absolute;
    left:0; right:0; bottom:0;
    height: 150px;
    z-index: 0;
    pointer-events:none;
  }

  .shelf .books{
    position:absolute;
    left:0; right:0; bottom:10px;
    height: calc(100% - 10px);
    display:flex;
    align-items:flex-end;
    padding-left: 6px;
  }

  .shelf .book{
    position:relative;
    flex: 0 0 auto;
    margin-right: 2px;
    border-radius: 3px 3px 1px 1px;
    transform-origin: bottom left;
    rotate: var(--tilt, 0deg);
    pointer-events:auto;
    transition: transform 0.25s ease;
    animation: shelve 0.65s cubic-bezier(.2,.8,.2,1) backwards;
    animation-delay: calc(var(--i) * 16ms);
  }

  .shelf .book::before{        /* dua garis tipis di punggung buku */
    content:'';
    position:absolute;
    left:0; right:0; top:12%;
    height:6px;
    border-top:2px solid rgba(255,255,255,0.55);
    border-bottom:2px solid rgba(255,255,255,0.55);
  }

  .shelf .book::after{         /* label judul */
    content:'';
    position:absolute;
    left:22%; right:22%; bottom:16%;
    height:22%;
    background: rgba(255,255,255,0.30);
    border-radius:2px;
  }

  .shelf .book:hover{ transform: translateY(-10px); }
  .shelf .gap{ flex: 0 0 auto; }

  .shelf.settled .book{ animation: none; }   /* animasi hanya sekali, tidak diulang saat layar diubah ukurannya */

  .shelf .board{
    position:absolute;
    left:0; right:0; bottom:0;
    height:10px;
    background: linear-gradient(#DCE2F0, #C6CEE3);
    box-shadow: 0 -1px 0 rgba(16,24,40,0.06);
  }

  @keyframes shelve{
    from{ transform: translateY(46px); opacity:0; }
    to  { transform: translateY(0);    opacity:1; }
  }

  @media (prefers-reduced-motion: reduce){
    .shelf .book{ animation: none; transition: none; }
  }

  @media (max-width: 880px){
    .auth-shell{ padding-bottom: 128px; }
    .shelf{ height: 112px; }
    .login-card::before{ right:22px; }
  }

  /* ===================== LATAR HALAMAN LOGIN ===================== */
  /* Lapisan (atas -> bawah): pola ikon buku halus, cahaya hangat di dekat rak,
     kabut biru di pojok, lalu gradasi langit-ke-krem sebagai dasar. */
  html, body{ background: #EEF3FF; }
  .auth-shell{
    background:
      url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140' viewBox='0 0 140 140' fill='none' stroke='%233D6BFF' stroke-opacity='0.10' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M18 30c6-4 13-4 19 0v22c-6-4-13-4-19 0zM37 30c6-4 13-4 19 0v22c-6-4-13-4-19 0z'/%3E%3Cpath d='M96 24h14v26l-7-5-7 5z'/%3E%3Cpath d='M30 100l22-22 8 8-22 22-10 2z'/%3E%3Cpath d='M98 96h22M98 104h22M98 112h14'/%3E%3C/svg%3E"),
      radial-gradient(800px 420px at 50% 100%, rgba(255, 214, 153, 0.35), transparent 70%),
      radial-gradient(900px 600px at 0% 0%, rgba(61, 107, 255, 0.14), transparent 65%),
      radial-gradient(700px 500px at 100% 20%, rgba(124, 92, 255, 0.10), transparent 65%),
      linear-gradient(180deg, #E6EEFF 0%, #F3F6FF 55%, #FFF7EA 100%);
    background-size: 140px 140px, auto, auto, auto, auto;
  }
  .login-card{
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05), 0 18px 48px rgba(47, 86, 224, 0.14);
  }
</style>
</head>
<body>

<div class="auth-shell">

  <!-- ===================== KOLOM KANAN: CARD LOGIN TERPADU ===================== -->
  <section class="form-panel">
    <div class="login-card">
      <div class="card-logo">
        <!-- h1: satu-satunya judul utama dokumen, merepresentasikan identitas & tujuan halaman -->
        <h1 class="logo-text">LIBRA</h1>
      </div>
      <p class="card-tagline">Sistem Informasi Perpustakaan Terpadu</p>

      <!-- err-banner: muncul via JS jika form kosong -->
      <div class="err-banner" id="errBanner">
        <span class="err-icon">⚠</span>
        <span id="errBannerText">NIM/Username dan kata sandi wajib diisi.</span>
      </div>

      <div class="info-banner" id="infoBanner">
        <span class="err-icon">✓</span>
        <span id="infoBannerText"></span>
      </div>

      <!-- Muncul jika login ditolak karena email belum diverifikasi -->
      <div class="verify-box" id="verifyBox">
        <strong>Email belum diverifikasi.</strong>
        <div>Salah ketik email saat mendaftar? Isi email yang benar di bawah. Jika sudah benar, kosongkan saja.</div>
        <input type="email" id="verifyEmail" placeholder="Email baru (opsional)" autocomplete="email">
        <button type="button" id="verifyResendBtn">Kirim ulang verifikasi</button>
        <div id="verifyMsg"></div>
      </div>

      <form id="loginForm" novalidate>
        <!-- Input NIM / Username Admin -->
        <div class="field-group">
          <label for="username">Username</label>
          <div class="input-wrap">
            <span class="input-icon">
              <!-- ikon user -->
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="8" r="4"></circle>
                <path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"></path>
              </svg>
            </span>
            <input type="text" id="username" name="username" placeholder="Username" autocomplete="username">
          </div>
        </div>

        <!-- Input Password dengan toggle show/hide -->
        <div class="field-group">
          <label for="password">Kata Sandi</label>
          <div class="input-wrap">
            <span class="input-icon">
              <!-- ikon lock -->
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="5" y="11" width="14" height="9" rx="2"></rect>
                <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
              </svg>
            </span>
            <input type="password" id="password" name="password" placeholder="Kata Sandi" autocomplete="current-password">
            <button type="button" class="toggle-eye" id="togglePassword" aria-label="Tampilkan/sembunyikan kata sandi">
              <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <!-- Remember me + Lupa Password -->
        <div class="options-row">
          <label class="remember-me">
            <input type="checkbox" id="rememberMe" name="rememberMe">
            Ingat Saya
          </label>
          <a href="{{ route('forgot-password') }}" class="forgot-link">Lupa Password?</a>
        </div>

        <!-- Tombol submit -->
        <button type="submit" class="btn-primary">Login</button>
      </form>

      <p class="signup-footer">
        Belum memiliki akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
      </p>
    </div>
  </section>

  <!-- Hiasan rak buku (dekoratif, disembunyikan dari pembaca layar) -->
  <div class="shelf" id="shelf" aria-hidden="true">
    <div class="books" id="books"></div>
    <div class="board"></div>
  </div>

</div>

<script>
  // ===================== HIASAN RAK BUKU =====================
  (function () {
    const shelf = document.getElementById('shelf');
    const host = document.getElementById('books');
    if (!shelf || !host) return;

    const palette = ['#1F2A5C', '#3D6BFF', '#1E9E8F', '#F2B134', '#E8604C', '#7A4DA8', '#8DB580', '#2F56E0', '#B8C4EE'];

    // Pengacak tetap (seed), supaya susunan buku sama setiap kali halaman dibuka
    function rng(seed) {
      return function () {
        seed = (seed * 1664525 + 1013904223) % 4294967296;
        return seed / 4294967296;
      };
    }

    function build() {
      host.innerHTML = '';
      const rand = rng(11);
      const width = host.clientWidth || window.innerWidth;
      const frag = document.createDocumentFragment();
      let x = 6, i = 0;

      while (x < width) {
        // sesekali ada celah kosong di rak
        if (i > 0 && rand() > 0.92) {
          const gap = document.createElement('span');
          gap.className = 'gap';
          const gw = 14 + Math.floor(rand() * 26);
          gap.style.width = gw + 'px';
          frag.appendChild(gap);
          x += gw;
        }

        const w = 22 + Math.floor(rand() * 20);
        const h = 48 + Math.floor(rand() * 46);
        const c = palette[Math.floor(rand() * palette.length)];
        const book = document.createElement('span');
        book.className = 'book';
        book.style.cssText = 'width:' + w + 'px;height:' + h + '%;background:' + c + ';--i:' + i;

        // satu dari sepuluh buku bersandar miring
        if (rand() > 0.9) book.style.setProperty('--tilt', '-8deg');

        frag.appendChild(book);
        x += w + 2;
        i++;
      }
      host.appendChild(frag);
    }

    build();
    // setelah animasi masuk selesai, jangan diulang ketika ukuran jendela berubah
    setTimeout(function () { shelf.classList.add('settled'); }, 2200);

    let t;
    window.addEventListener('resize', function () {
      clearTimeout(t);
      t = setTimeout(function () { shelf.classList.add('settled'); build(); }, 150);
    });
  })();
</script>

<script>
  // ===================== AUTO-REDIRECT IF ALREADY LOGGED IN =====================
  (function() {
    const token = localStorage.getItem('libra_token');
    const userStr = localStorage.getItem('libra_user');
    if (token && userStr) {
      try {
        const user = JSON.parse(userStr);
        if (user) {
          const isAdmin = user.nim && user.nim.toLowerCase() === 'admin';
          const isKurir = (user.role && user.role.toLowerCase() === 'kurir') || (user.nim && user.nim.toLowerCase() === 'kurir');
          window.location.href = isAdmin ? 'admin-dashboard.html' : (isKurir ? 'kurir.html' : ('/' + (user.username ?? user.nim)));
        }
      } catch (e) {
        console.error('Error auto-redirecting:', e);
      }
    }
  })();

  // ===================== TOGGLE SHOW/HIDE PASSWORD =====================
  const togglePasswordBtn = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eyeIcon');

  // Path SVG untuk ikon "mata tertutup" (saat password ditampilkan)
  const EYE_OPEN = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle>';
  const EYE_CLOSED = '<path d="M3 3l18 18"></path><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 7 11 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61C4.13 8.36 2 11.6 1 12c0 0 1 2 2.6 3.8"></path>';

  togglePasswordBtn.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    eyeIcon.innerHTML = isPassword ? EYE_CLOSED : EYE_OPEN;
  });

  // ===================== HANDLE SUBMIT LOGIN =====================
  const loginForm = document.getElementById('loginForm');
  const errBanner = document.getElementById('errBanner');
  const errBannerText = document.getElementById('errBannerText');
  const usernameInput = document.getElementById('username');

  function showError(message){
    errBannerText.textContent = message;
    errBanner.classList.add('show');
  }

  function hideError(){
    errBanner.classList.remove('show');
  }

  // Base URL API backend Laravel. Gunakan URL aplikasi saat ini agar
  // request tetap mengarah ke backend Laravel yang sama.
  const API_BASE_URL = '{{ url('/api') }}';

  loginForm.addEventListener('submit', async function(e){
    e.preventDefault(); // mencegah submit default (reload halaman)

    const usernameVal = usernameInput.value.trim();
    const passwordVal = passwordInput.value.trim();

    // Validasi: form tidak boleh kosong
    if(usernameVal === '' || passwordVal === ''){
      showError('NIM/Username dan kata sandi wajib diisi.');
      return;
    }

    hideError();

    const submitBtn = loginForm.querySelector('.btn-primary');
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Memproses...';

try{
    const res = await fetch(`${API_BASE_URL}/login`, {
        method: 'POST',
        mode: 'cors',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            nim: usernameVal,
            password: passwordVal
        })
    });

    console.log("Status:", res.status);   // <-- TAMBAHKAN DI SINI

    const data = await res.json();

    console.log("Response:", data);       // <-- TAMBAHKAN DI SINI

    if(data.code === 'email_not_verified'){
        showError(data.message);
        document.getElementById('verifyBox').classList.add('show');
        document.getElementById('verifyEmail').placeholder = 'Email baru (saat ini: ' + (data.email || '-') + ')';
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
        return;
    }

    if(!res.ok || !data.success){
        showError(data.message || 'Username atau kata sandi salah.');
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
        return;
    }

      // Simpan token & data user untuk dipakai halaman lain
      localStorage.setItem('libra_token', data.token);
      localStorage.setItem('libra_user', JSON.stringify(data.user));
      // "Ingat Saya": dicentang -> tetap login walau browser ditutup
      try {
        const remember = document.getElementById('rememberMe');
        if (remember && remember.checked) localStorage.setItem('libra_remember', '1');
        else localStorage.removeItem('libra_remember');
        sessionStorage.setItem('libra_active', '1');
      } catch (e) {}

      // Redirect sesuai role yang dikirim backend (admin / mahasiswa)
      window.location.href = data.redirect || 'index.html';

    } catch (err){
      console.error('Login error:', err);
      showError('Tidak bisa terhubung ke server. Pastikan backend sedang berjalan.');
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    }
  });

  // ===================== VERIFIKASI EMAIL =====================
  try {
    if(new URLSearchParams(location.search).get('registered') === '1'){
      document.getElementById('infoBannerText').textContent =
        'Pendaftaran berhasil! Buka email Anda dan klik tautan verifikasi sebelum login.';
      document.getElementById('infoBanner').classList.add('show');
    }
  } catch(e){}

  document.getElementById('verifyResendBtn').addEventListener('click', async function(){
    const btn = this;
    const msg = document.getElementById('verifyMsg');
    const nim = usernameInput.value.trim();
    const pwd = passwordInput.value.trim();
    if(!nim || !pwd){ msg.textContent = 'Isi NIM dan kata sandi di form login terlebih dahulu.'; return; }

    btn.disabled = true;
    msg.textContent = 'Mengirim...';
    try{
      const res = await fetch(`${API_BASE_URL}/email/resend`, {
        method: 'POST',
        headers: {'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ nim: nim, password: pwd, email: document.getElementById('verifyEmail').value.trim() })
      });
      const data = await res.json();
      msg.textContent = data.message || (res.ok ? 'Terkirim.' : 'Gagal mengirim.');
      if(res.status === 429){ msg.textContent = 'Terlalu banyak percobaan. Coba lagi sebentar.'; }
    }catch(e){
      msg.textContent = 'Tidak bisa terhubung ke server.';
    }
    btn.disabled = false;
  });

  // ===================== REAL-TIME CLEAR ERROR SAAT MENGETIK =====================
  // Begitu user mulai memperbaiki input, err-banner langsung hilang tanpa perlu submit ulang
  usernameInput.addEventListener('input', () => {
    if(errBanner.classList.contains('show')){
      hideError();
    }
  });

  passwordInput.addEventListener('input', () => {
    if(errBanner.classList.contains('show')){
      hideError();
    }
  });
</script>

</body>
</html>