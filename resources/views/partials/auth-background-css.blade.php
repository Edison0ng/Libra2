{{-- Latar bersama halaman auth (login-style): pola buku, cahaya, titik, rak buku. Dipakai oleh daftar & verifikasi email. --}}
  .auth-bg{ position:relative; overflow:hidden; padding-bottom:168px; }
  .auth-bg > *:not(.shelf){ position:relative; z-index:1; }
  /* Titik-titik halus di pojok kiri atas, memudar ke tengah */
  .auth-bg::before{
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
  .auth-bg::after{
    content:'';
    position:absolute;
    left:50%; top:46%;
    width: 760px; height: 760px;
    transform: translate(-50%, -50%);
    background: radial-gradient(closest-side, rgba(61,107,255,0.08), transparent);
    pointer-events:none;
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
    .auth-bg{ padding-bottom: 128px; }
    .shelf{ height: 112px; }
  }
  html, body{ background: #EEF3FF; }
  .auth-bg{
    background:
      url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140' viewBox='0 0 140 140' fill='none' stroke='%233D6BFF' stroke-opacity='0.10' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M18 30c6-4 13-4 19 0v22c-6-4-13-4-19 0zM37 30c6-4 13-4 19 0v22c-6-4-13-4-19 0z'/%3E%3Cpath d='M96 24h14v26l-7-5-7 5z'/%3E%3Cpath d='M30 100l22-22 8 8-22 22-10 2z'/%3E%3Cpath d='M98 96h22M98 104h22M98 112h14'/%3E%3C/svg%3E"),
      radial-gradient(800px 420px at 50% 100%, rgba(255, 214, 153, 0.35), transparent 70%),
      radial-gradient(900px 600px at 0% 0%, rgba(61, 107, 255, 0.14), transparent 65%),
      radial-gradient(700px 500px at 100% 20%, rgba(124, 92, 255, 0.10), transparent 65%),
      linear-gradient(180deg, #E6EEFF 0%, #F3F6FF 55%, #FFF7EA 100%);
    background-size: 140px 140px, auto, auto, auto, auto;
  }