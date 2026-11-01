<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LIBRA — Lupa Password</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">

<style>
  :root{
    --bg: #0A0E17;
    --panel-solid: #10172A;
    --panel-2: #151D34;
    --line: #222B42;
    --text: #E7EAF2;
    --muted: #8A92A6;
    --muted-2: #737F9C;
    --accent: #3D6BFF;
    --accent-2: #7C9CFF;
    --danger: #FF5470;
    --success: #3DDC97;
    --success-bg: rgba(61, 220, 151, 0.12);
    --danger-bg: rgba(255, 84, 112, 0.12);
    --radius: 14px;
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

  .auth-shell{
    min-height: 100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 24px;
    background:
      radial-gradient(900px 500px at 0% 0%, rgba(61,107,255,0.08), transparent 60%),
      radial-gradient(700px 500px at 100% 100%, rgba(124,156,255,0.06), transparent 60%),
      var(--bg);
  }

  .auth-card{
    width: 100%;
    max-width: 420px;
    background: var(--panel-solid);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    padding: 40px 36px;
  }

  .card-logo{ text-align:center; margin-bottom: 6px; }

  .card-logo .logo-text{
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

  h1.page-title{
    text-align:center;
    font-family:'Sora', sans-serif;
    font-weight:700;
    font-size: 20px;
    margin: 18px 0 6px;
  }

  .card-tagline{
    text-align:center;
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 28px;
    line-height:1.5;
  }

  .err-banner, .ok-banner{
    display:none;
    align-items:flex-start;
    gap:10px;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13px;
    line-height:1.4;
    margin-bottom: 20px;
  }

  .err-banner{
    background: var(--danger-bg);
    border: 1px solid rgba(255, 84, 112, 0.35);
    color: var(--danger);
  }

  .ok-banner{
    background: var(--success-bg);
    border: 1px solid rgba(61, 220, 151, 0.35);
    color: var(--success);
  }

  .err-banner.show, .ok-banner.show{ display:flex; }
  .err-banner .err-icon, .ok-banner .err-icon{ flex-shrink:0; font-weight:700; }

  .field-group{ margin-bottom: 18px; }

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

  .input-wrap input::placeholder{ color: var(--muted-2); }

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
    transition: background 0.2s ease, box-shadow 0.2s ease;
    margin-top: 6px;
  }

  .btn-primary:hover{
    background: var(--accent-2);
    box-shadow: 0 6px 18px rgba(61,107,255,0.35);
  }

  .btn-primary:disabled{ opacity:.6; cursor:not-allowed; }

  .back-footer{
    text-align:center;
    margin-top: 24px;
    font-size: 13px;
    color: var(--muted);
  }

  .back-footer a{ font-weight:600; }
</style>
</head>
<body>

<div class="auth-shell">
  <div class="auth-card">
    <div class="card-logo">
      <h1 style="display:none;">Lupa Password LIBRA</h1>
      <span class="logo-text">LIBRA</span>
    </div>
    <h1 class="page-title">Lupa Kata Sandi?</h1>
    <p class="card-tagline">Masukkan NIM dan email yang terdaftar pada akun Anda. Kami akan mengirimkan link untuk membuat kata sandi baru ke email tersebut.</p>

    <div class="err-banner" id="errBanner">
      <span class="err-icon">⚠</span>
      <span id="errBannerText">Terjadi kesalahan.</span>
    </div>

    <div class="ok-banner" id="okBanner">
      <span class="err-icon">✓</span>
      <span id="okBannerText">Link reset telah dikirim.</span>
    </div>

    <form id="forgotForm" novalidate>
      <div class="field-group">
        <label for="nim">NIM</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="8" r="4"></circle>
              <path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"></path>
            </svg>
          </span>
          <input type="text" id="nim" name="nim" placeholder="Masukkan NIM Anda" autocomplete="username">
        </div>
      </div>

      <div class="field-group">
        <label for="email">Email Terdaftar</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="3" y="5" width="18" height="14" rx="2"></rect>
              <path d="M3 7l9 6 9-6"></path>
            </svg>
          </span>
          <input type="email" id="email" name="email" placeholder="nama@email.com" autocomplete="email">
        </div>
      </div>

      <button type="submit" class="btn-primary" id="submitBtn">Kirim Link Reset</button>
    </form>

    <p class="back-footer">
      Ingat kata sandi Anda? <a href="{{ route('login.view') }}">Kembali ke Login</a>
    </p>
  </div>
</div>

<script>
  const API_BASE_URL = '{{ url('/api') }}';

  const form = document.getElementById('forgotForm');
  const nimInput = document.getElementById('nim');
  const emailInput = document.getElementById('email');
  const errBanner = document.getElementById('errBanner');
  const errBannerText = document.getElementById('errBannerText');
  const okBanner = document.getElementById('okBanner');
  const okBannerText = document.getElementById('okBannerText');
  const submitBtn = document.getElementById('submitBtn');

  function showError(message){
    okBanner.classList.remove('show');
    errBannerText.textContent = message;
    errBanner.classList.add('show');
  }

  function showOk(message){
    errBanner.classList.remove('show');
    okBannerText.textContent = message;
    okBanner.classList.add('show');
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const nimVal = nimInput.value.trim();
    const emailVal = emailInput.value.trim();

    if (nimVal === '' || emailVal === '') {
      showError('NIM dan email wajib diisi.');
      return;
    }

    errBanner.classList.remove('show');
    okBanner.classList.remove('show');

    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Mengirim...';

    try {
      const res = await fetch(`${API_BASE_URL}/forgot-password`, {
        method: 'POST',
        mode: 'cors',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ nim: nimVal, email: emailVal })
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        showError(data.message || 'Gagal mengirim link reset. Coba lagi.');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        return;
      }

      showOk(data.message || 'Link reset kata sandi telah dikirim ke email Anda.');
      form.reset();
      submitBtn.textContent = 'Terkirim';
    } catch (err) {
      console.error('Forgot password error:', err);
      showError('Tidak bisa terhubung ke server. Pastikan backend sedang berjalan.');
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  });

  [nimInput, emailInput].forEach(el => {
    el.addEventListener('input', () => {
      errBanner.classList.remove('show');
    });
  });
</script>

</body>
</html>