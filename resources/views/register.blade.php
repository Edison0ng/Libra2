<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LIBRA — Daftar Akun</title>

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
    --radius: 14px;
    --success: #3DDC97;
    --danger-bg: rgba(255, 84, 112, 0.12);
  }

  *{ margin:0; padding:0; box-sizing:border-box; }

  html, body{
    min-height:100%;
    background: var(--bg);
    color: var(--text);
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;
  }

  a{ color: var(--accent-2); text-decoration:none; }
  a:hover{ text-decoration: underline; }

  .register-shell{
    min-height: 100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 40px 20px;
    background:
      radial-gradient(900px 500px at 50% 0%, rgba(61,107,255,0.08), transparent 60%),
      var(--bg);
  }

  .register-card{
    width: 100%;
    max-width: 480px;
    background: var(--panel-solid);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    padding: 44px 40px;
  }

  .card-logo{ text-align:center; margin-bottom: 6px; }

  .card-logo .logo-text{
    font-family:'Sora', sans-serif;
    font-weight:800;
    font-size: 30px;
    letter-spacing: 1px;
    background: linear-gradient(135deg, var(--text), var(--accent-2));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
  }

  .card-heading{
    text-align:center;
    font-family:'Sora', sans-serif;
    font-weight:700;
    font-size: 18px;
    margin-top: 14px;
    margin-bottom: 4px;
  }

  .card-tagline{
    text-align:center;
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 28px;
  }

  .err-banner{
    display:none;
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
  .err-banner .err-icon{ flex-shrink:0; font-weight:700; }

  .success-banner{
    display:none;
    align-items:flex-start;
    gap:10px;
    background: rgba(61, 220, 151, 0.12);
    border: 1px solid rgba(61, 220, 151, 0.35);
    color: var(--success);
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13px;
    line-height:1.4;
    margin-bottom: 20px;
  }

  .success-banner.show{ display:flex; }

  .field-group{ margin-bottom: 16px; }

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

  .input-wrap.input-error{
    border-color: var(--danger);
    box-shadow: 0 0 0 3px rgba(255,84,112,0.15);
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

  .field-hint{
    font-size: 11.5px;
    color: var(--muted-2);
    margin-top: 6px;
  }

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
    margin-top: 8px;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
  }

  .btn-primary:hover{
    background: var(--accent-2);
    box-shadow: 0 6px 18px rgba(61,107,255,0.35);
  }

  .btn-primary:active{ transform: translateY(1px); }
  .btn-primary:disabled{ opacity: 0.6; cursor: not-allowed; }

  .login-footer{
    text-align:center;
    margin-top: 22px;
    font-size: 13px;
    color: var(--muted);
  }

  .login-footer a{ font-weight:600; }

  @media (max-width: 480px){
    .register-card{ padding: 32px 24px; }
  }
</style>
</head>
<body>

<div class="register-shell">
  <div class="register-card">

    <div class="card-logo"><span class="logo-text">LIBRA</span></div>
    <h1 class="card-heading">Daftar Akun Mahasiswa</h1>
    <p class="card-tagline">Lengkapi data berikut untuk membuat akun perpustakaan.</p>

    <div class="err-banner" id="errBanner">
      <span class="err-icon">⚠</span>
      <span id="errBannerText">Kata sandi tidak cocok!</span>
    </div>

    <div class="success-banner" id="successBanner">
      <span class="err-icon">✓</span>
      <span>Pendaftaran berhasil! Mengarahkan ke halaman login...</span>
    </div>

    <form id="registerForm" novalidate>

      <!-- Nama Lengkap -->
      <div class="field-group">
        <label for="namaLengkap">Nama Lengkap</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="12" cy="8" r="4"></circle>
              <path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"></path>
            </svg>
          </span>
          <input type="text" id="namaLengkap" name="namaLengkap" placeholder="Masukkan nama lengkap" autocomplete="name">
        </div>
      </div>

      <!-- NIM -->
      <div class="field-group">
        <label for="nim">NIM (dipakai sebagai username)</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="3" y="4" width="18" height="16" rx="2"></rect>
              <path d="M7 9h10M7 13h6"></path>
            </svg>
          </span>
          <input type="text" id="nim" name="nim" placeholder="Contoh: 220194851" autocomplete="username">
        </div>
        <p class="field-hint">NIM ini akan dipakai untuk login.</p>
      </div>

      <!-- Fakultas -->
      <div class="field-group">
        <label for="fakultas">Fakultas / Program Studi</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M12 3l9 5-9 5-9-5 9-5z"></path>
              <path d="M5 10v5c0 1.5 3 3 7 3s7-1.5 7-3v-5"></path>
            </svg>
          </span>
          <input type="text" id="fakultas" name="fakultas" placeholder="Contoh: Informatika" autocomplete="off">
        </div>
      </div>

      <!-- No Telepon -->
      <div class="field-group">
        <label for="noTelepon">No. Telepon</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.68 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.32 1.85.55 2.81.68A2 2 0 0 1 22 16.92z"></path>
            </svg>
          </span>
          <input type="text" id="noTelepon" name="noTelepon" placeholder="Contoh: 081234567890" autocomplete="tel">
        </div>
      </div>

      <!-- Alamat Kirim -->
      <div class="field-group">
        <label for="alamatKirim">Alamat Pengiriman</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
          </span>
          <input type="text" id="alamatKirim" name="alamatKirim" placeholder="Alamat untuk pengiriman buku" autocomplete="street-address">
        </div>
      </div>

      <!-- Kata Sandi -->
      <div class="field-group">
        <label for="password">Kata Sandi</label>
        <div class="input-wrap" id="passwordWrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="5" y="11" width="14" height="9" rx="2"></rect>
              <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
            </svg>
          </span>
          <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="new-password">
          <button type="button" class="toggle-eye" id="togglePassword" aria-label="Tampilkan/sembunyikan kata sandi">
            <svg id="eyeIcon1" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
          </button>
        </div>
      </div>

      <!-- Konfirmasi Kata Sandi -->
      <div class="field-group">
        <label for="confirmPassword">Konfirmasi Kata Sandi</label>
        <div class="input-wrap" id="confirmPasswordWrap">
          <span class="input-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="5" y="11" width="14" height="9" rx="2"></rect>
              <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
            </svg>
          </span>
          <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Ulangi kata sandi" autocomplete="new-password">
          <button type="button" class="toggle-eye" id="toggleConfirmPassword" aria-label="Tampilkan/sembunyikan konfirmasi kata sandi">
            <svg id="eyeIcon2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary">Daftar Akun Baru</button>
    </form>

    <p class="login-footer">
      Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </p>

  </div>
</div>

<script>
  const EYE_OPEN = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle>';
  const EYE_CLOSED = '<path d="M3 3l18 18"></path><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 7 11 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61C4.13 8.36 2 11.6 1 12c0 0 1 2 2.6 3.8"></path>';

  function setupPasswordToggle(buttonId, inputId, eyeIconId){
    const btn = document.getElementById(buttonId);
    const input = document.getElementById(inputId);
    const icon = document.getElementById(eyeIconId);

    btn.addEventListener('click', () => {
      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';
      icon.innerHTML = isPassword ? EYE_CLOSED : EYE_OPEN;
    });
  }

  setupPasswordToggle('togglePassword', 'password', 'eyeIcon1');
  setupPasswordToggle('toggleConfirmPassword', 'confirmPassword', 'eyeIcon2');

  // ===================== VALIDASI & SUBMIT FORM REGISTER =====================
  // Base URL API backend Laravel. Gunakan URL aplikasi saat ini agar
  // request tetap mengarah ke backend Laravel yang sama.
  const API_BASE_URL = '{{ url('/api') }}';

  const registerForm = document.getElementById('registerForm');
  const errBanner = document.getElementById('errBanner');
  const errBannerText = document.getElementById('errBannerText');
  const successBanner = document.getElementById('successBanner');

  const namaLengkapInput = document.getElementById('namaLengkap');
  const nimInput = document.getElementById('nim');
  const fakultasInput = document.getElementById('fakultas');
  const noTeleponInput = document.getElementById('noTelepon');
  const alamatKirimInput = document.getElementById('alamatKirim');
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('confirmPassword');
  const passwordWrap = document.getElementById('passwordWrap');
  const confirmPasswordWrap = document.getElementById('confirmPasswordWrap');

  function showError(message){
    successBanner.classList.remove('show');
    errBannerText.textContent = message;
    errBanner.classList.add('show');
  }

  function hideError(){
    errBanner.classList.remove('show');
  }

  function clearPasswordErrorState(){
    passwordWrap.classList.remove('input-error');
    confirmPasswordWrap.classList.remove('input-error');
  }

  registerForm.addEventListener('submit', async function(e){
    e.preventDefault();

    hideError();
    clearPasswordErrorState();

    const namaLengkapVal = namaLengkapInput.value.trim();
    const nimVal = nimInput.value.trim();
    const fakultasVal = fakultasInput.value.trim();
    const noTeleponVal = noTeleponInput.value.trim();
    const alamatKirimVal = alamatKirimInput.value.trim();
    const passwordVal = passwordInput.value;
    const confirmPasswordVal = confirmPasswordInput.value;

    // Field wajib: nama lengkap, NIM, password, konfirmasi password
    if(!namaLengkapVal || !nimVal || !passwordVal || !confirmPasswordVal){
      showError('Nama lengkap, NIM, dan kata sandi wajib diisi.');
      return;
    }

    if(passwordVal !== confirmPasswordVal){
      passwordWrap.classList.add('input-error');
      confirmPasswordWrap.classList.add('input-error');
      showError('Kata sandi tidak cocok!');
      return;
    }

    const submitBtn = registerForm.querySelector('.btn-primary');
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Memproses...';

    try{
      const res = await fetch(`${API_BASE_URL}/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          nama_lengkap: namaLengkapVal,
          nim: nimVal,
          fakultas: fakultasVal,
          no_telepon: noTeleponVal,
          alamat_kirim: alamatKirimVal,
          password: passwordVal,
          confirmPassword: confirmPasswordVal
        })
      });

      const data = await res.json();

      if(!res.ok || !data.success){
        showError(data.message || 'Pendaftaran gagal. Silakan coba lagi.');
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
        return;
      }

      localStorage.setItem('libra_token', data.token);
      localStorage.setItem('libra_user', JSON.stringify(data.user));

      successBanner.classList.add('show');

      setTimeout(() => {
        window.location.href = '{{ route('login') }}';
      }, 1200);

    } catch (err){
      console.error('Register error:', err);
      showError('Tidak bisa terhubung ke server. Pastikan backend sedang berjalan.');
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    }
  });

  [namaLengkapInput, nimInput, fakultasInput, noTeleponInput, alamatKirimInput, passwordInput, confirmPasswordInput].forEach((input) => {
    input.addEventListener('input', () => {
      if(errBanner.classList.contains('show')){
        hideError();
      }
    });
  });

  [passwordInput, confirmPasswordInput].forEach((input) => {
    input.addEventListener('input', () => {
      clearPasswordErrorState();
    });
  });
</script>

</body>
</html>
