<!DOCTYPE html>
<html lang="id" class="">
<head>
    <!-- Interceptor Otentikasi API Otomatis (Bearer Token Sanctum) -->
    <script>
    (function () {
      var PUBLIC = ['/api/login', '/api/register', '/api/forgot-password', '/api/reset-password', '/api/test'];
      var orig = window.fetch.bind(window);
      window.fetch = function (input, init) {
        try {
          var url = typeof input === 'string' ? input : (input && input.url) || '';
          var path = url.replace(/^https?:\/\/[^/]+/, '').split('?')[0];
          if (path && !path.startsWith('/')) path = '/' + path;
          if (path.indexOf('/api/') === 0 && PUBLIC.indexOf(path) === -1) {
            init = init || {};
            var h = new Headers(init.headers || (typeof input !== 'string' && input.headers) || {});
            var t = localStorage.getItem('libra_token');
            if (t && !h.has('Authorization')) h.set('Authorization', 'Bearer ' + t);
            if (!h.has('Accept')) h.set('Accept', 'application/json');
            init.headers = h;
            return orig(input, init).then(function (res) {
              if (res.status === 401) {
                ['libra_token', 'libra_user', 'libra_remember'].forEach(function (k) { try { localStorage.removeItem(k); } catch (e) {} });
                try { sessionStorage.removeItem('libra_active'); } catch (e) {}
                if (location.pathname !== '/login') location.href = '/login';
              }
              return res;
            });
          }
        } catch (e) {}
        return orig(input, init);
      };
    })();
    </script>
    <script src="/js/auth-fetch.js"></script>
<script>
  /* Ingat Saya: kalau tidak dicentang saat login, sesi hanya berlaku selama browser/tab dibuka */
  (function(){try{if(localStorage.getItem('libra_remember')!=='1'&&sessionStorage.getItem('libra_active')!=='1'){localStorage.removeItem('libra_token');localStorage.removeItem('libra_user');}}catch(e){}})();
</script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sistem Libra</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'] },
                    colors: {
                        north: {
                            bg: '#F1EFEA',
                            card: '#FDFCF9',
                            dark: '#151512',
                            darkHover: '#23231F',
                            border: '#E5E2DA',
                            borderDark: '#262620',
                            lime: '#BEF264',
                            indigo: '#6366F1',
                            text: '#1A1916',
                            muted: '#78756D',
                        },
                        libra: {
                            dark: '#151512',
                            bg: '#F1EFEA',
                            primary: '#6366F1',
                            red: '#E11D48'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #F1EFEA;
            color: #1A1916;
        }
        .dark body {
            background-color: #151512;
            color: #EAE8E2;
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.25s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slideDown { animation: slideDown 0.3s ease-out; }
        .book-card {
            transition: all 0.2s ease;
        }
        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15);
        }
        .notification-dot {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.7; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.7; }
        }
        /* Tinggi layar mobile: 100vh di browser HP termasuk area address bar, sehingga
           navigasi bawah (absolute bottom-0) tertutup di luar layar. Pakai tinggi
           viewport yang benar-benar terlihat (dvh / --app-height dari JS). */
        body {
            height: 100vh;
            height: 100dvh;
            height: var(--app-height, 100dvh);
        }
        #mobile-bottom-nav {
            height: calc(4rem + env(safe-area-inset-bottom, 0px));
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        #detail-modal {
                z-index: 9999 !important;
        }
        #detail-modal .modal-content {
                z-index: 10000 !important;
        }
        .progress-bar {
            transition: width 0.5s ease;
        }
        .status-badge {
            transition: all 0.3s ease;
        }
        /* Modal animation */
        .modal-overlay {
            animation: modalFadeIn 0.3s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            animation: modalSlideUp 0.3s ease-out;
        }
        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
    <script>
        // Simpan tinggi viewport yang terlihat (aman untuk browser HP lama yang belum mengenal dvh)
        (function () {
            function setAppHeight() {
                document.documentElement.style.setProperty('--app-height', window.innerHeight + 'px');
            }
            setAppHeight();
            window.addEventListener('resize', setAppHeight);
            window.addEventListener('orientationchange', function () { setTimeout(setAppHeight, 200); });
        })();
    </script>
</head>
<body class="bg-[#F1EFEA] text-[#1A1916] dark:bg-[#151512] dark:text-[#EAE8E2] flex h-screen overflow-hidden transition-colors duration-200">

    <!-- SIDEBAR -->
    <aside class="hidden md:flex flex-col w-60 bg-[#151512] border-r border-[#262620] h-full transition-colors duration-200 select-none text-[#9C988F]">
        <div class="p-5 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#BEF264] flex items-center justify-center text-[#151512] font-black text-base shadow-sm">
                    L
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-lg font-bold text-white tracking-tight">LIBRA</span>
                    <span class="text-[9px] font-bold tracking-widest text-[#BEF264] uppercase">PORTAL</span>
                </div>
            </div>

            <!-- Workspace Card -->
            <div class="mt-4 p-2.5 rounded-xl bg-[#21211D] border border-[#2E2E28] flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#6366F1] text-white flex items-center justify-center text-xs font-bold shadow-sm">
                    MH
                </div>
                <div class="overflow-hidden leading-tight">
                    <p class="text-xs font-semibold text-white truncate" id="sidebar-user-name">Mahasiswa</p>
                    <p class="text-[10px] text-[#8A867D] truncate">Anggota Perpustakaan</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-2 space-y-1.5 overflow-y-auto hide-scrollbar text-xs">
            <p class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-[#6E6B64]">Menu Mahasiswa</p>
            <button onclick="switchTab('beranda')" id="nav-beranda-btn" class="nav-btn w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-semibold bg-[#BEF264] text-[#151512] transition-all" data-target="beranda">
                <i class="fa-solid fa-house w-4 text-sm"></i> <span data-i18n="nav-beranda">Beranda</span>
            </button>
            <button onclick="switchTab('buku')" id="nav-buku-btn" class="nav-btn w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-medium text-[#9C988F] hover:text-white hover:bg-[#21211D] transition-all" data-target="buku">
                <i class="fa-solid fa-magnifying-glass w-4 text-sm"></i> <span data-i18n="nav-pustaka">Pustaka</span>
            </button>
            <button onclick="switchTab('sirkulasi')" id="nav-sirkulasi-btn" class="nav-btn w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-medium text-[#9C988F] hover:text-white hover:bg-[#21211D] transition-all" data-target="sirkulasi">
                <i class="fa-solid fa-arrows-spin w-4 text-sm"></i> <span data-i18n="nav-sirkulasi">Sirkulasi</span>
            </button>
            <button onclick="switchTab('toko')" id="nav-toko-btn" class="nav-btn w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-medium text-[#9C988F] hover:text-white hover:bg-[#21211D] transition-all" data-target="toko">
                <i class="fa-solid fa-store w-4 text-sm"></i> <span>Toko ATK</span>
                <span id="cart-badge-sidebar" class="hidden ml-auto bg-[#BEF264] text-[#151512] text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center">0</span>
            </button>
            <button onclick="switchTab('profil')" id="nav-profil-btn" class="nav-btn w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-medium text-[#9C988F] hover:text-white hover:bg-[#21211D] transition-all" data-target="profil">
                <i class="fa-regular fa-user w-4 text-sm"></i> <span data-i18n="nav-profil">Profil</span>
            </button>
        </nav>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 flex flex-col h-full relative overflow-hidden">
        
        <!-- HEADER -->
        <header class="bg-[#F1EFEA] dark:bg-[#151512] text-[#1A1916] dark:text-[#EAE8E2] px-6 py-4 flex items-center justify-between border-b border-[#E5E2DA] dark:border-[#262620] z-20">
            <div>
                <p class="text-[10px] md:text-[11px] font-bold tracking-wider uppercase text-[#8A867D]">PORTAL MAHASISWA</p>
                <h2 id="header-title" class="font-extrabold text-xl md:text-2xl tracking-tight text-[#1A1916] dark:text-white">Beranda</h2>
                <p id="header-subtitle" class="text-xs text-[#78756D] mt-0.5">Selamat datang kembali, Mahasiswa!</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="toggleDarkMode()" id="dark-mode-toggle" title="Ganti tema gelap/terang" aria-label="Ganti tema gelap/terang" class="focus:outline-none relative p-2 w-9 h-9 flex items-center justify-center bg-[#FDFCF9] dark:bg-[#21211D] border border-[#E5E2DA] dark:border-[#2E2E28] rounded-xl text-[#78756D] hover:text-[#1A1916] dark:hover:text-white transition">
                    <i class="fa-regular fa-moon text-sm dark:hidden"></i>
                    <i class="fa-regular fa-sun text-sm hidden dark:inline text-[#BEF264]"></i>
                </button>

                <button onclick="toggleNotif(event)" class="focus:outline-none relative p-2 w-9 h-9 flex items-center justify-center bg-[#FDFCF9] dark:bg-[#21211D] border border-[#E5E2DA] dark:border-[#2E2E28] rounded-xl text-[#78756D] hover:text-[#1A1916] dark:hover:text-white transition">
                    <i class="fa-regular fa-bell text-sm"></i>
                    <span id="notif-badge" class="absolute top-1.5 right-1.5 flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#BEF264] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#BEF264]"></span>
                    </span>
                </button>

                <div id="notif-panel" class="hidden absolute right-6 top-16 w-80 bg-[#FDFCF9] dark:bg-[#1C1C18] rounded-2xl shadow-xl border border-[#E5E2DA] dark:border-[#262620] z-50 overflow-hidden text-[#1A1916] dark:text-[#EAE8E2]">
                    <div class="flex justify-between items-center p-4 border-b border-[#E5E2DA] dark:border-[#262620] bg-[#F7F5F0] dark:bg-[#21211D]">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-[#1A1916] dark:text-white">Notifikasi Terbaru</h3>
                        <button class="text-[11px] text-[#6366F1] dark:text-[#BEF264] font-semibold hover:underline" onclick="clearNotifications()">Tandai dibaca</button>
                    </div>
                    <div id="notif-list" class="p-4 space-y-3.5 max-h-72 overflow-y-auto hide-scrollbar">
                        <p class="text-xs text-[#78756D] text-center py-4">Memuat notifikasi...</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- KONTEN HALAMAN -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8 pb-24 md:pb-8">
            
            <!-- TAB 1: BERANDA -->
            <div id="beranda" class="tab-content active max-w-5xl mx-auto space-y-6">
                <h3 class="font-bold text-slate-800 dark:text-white text-base md:text-lg flex items-center gap-2 pt-2">
                    <i class="fa-solid fa-star text-amber-500"></i> <span data-i18n="recommended">Direkomendasikan Untukmu</span>
                </h3>
                <div id="recommended-books" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <!-- Akan diisi secara dinamis -->
                </div>

                <h3 class="font-bold text-slate-800 dark:text-white text-base md:text-lg flex items-center gap-2 pt-2">
                    <i class="fa-solid fa-heart text-rose-500"></i> <span data-i18n="my-wishlist">Wishlist Saya</span>
                </h3>
                <div id="wishlist-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <p id="wishlist-empty" data-i18n="wishlist-empty" class="col-span-full text-xs text-slate-400 py-6 bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 text-center">Belum ada buku di wishlist kamu.</p>
                </div>
            </div>

            <!-- TAB 2: PUSTAKA DIGITAL -->
            <div id="buku" class="tab-content max-w-5xl mx-auto space-y-6">
                <!-- Search Bar + Filter Genre -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="search-input" oninput="liveSearch()" placeholder="Cari judul atau penulis buku secara live..." class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 focus:outline-none focus:border-blue-500 dark:focus:border-blue-500 shadow-sm text-sm transition-colors text-slate-800 dark:text-white">
                    </div>
                    <div class="relative sm:w-56">
                        <i class="fa-solid fa-tags absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        <select id="genre-filter" onchange="liveSearch()" class="w-full appearance-none pl-11 pr-8 py-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 focus:outline-none focus:border-blue-500 dark:focus:border-blue-500 shadow-sm text-sm transition-colors text-slate-800 dark:text-white">
                            <option value="">Semua Genre</option>
                            @foreach(($genres ?? collect()) as $genre)
                            <option value="{{ $genre }}">{{ $genre }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <!-- Grid Buku dari Database (Supabase via Blade) -->
                <div id="live-library-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($books as $book)
                    <div class="book-card relative bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 hover:shadow-md transition-shadow cursor-pointer" 
                        onclick="openBookDetail('{{ $book->ISBN }}')" 
                        data-id="{{ $book->ISBN }}"
                        data-title="{{ $book->{'Book-Title'} }}"
                        data-author="{{ $book->{'Book-Author'} }}"
                        data-cover="{{ $book->{'Image-URL-M'} }}"
                        data-status="Tersedia" 
                        data-genre="{{ $book->genre ?? 'Umum' }}"
                        data-rating="4.5"
                        data-desc="Deskripsi buku tidak tersedia.">

                        <button type="button"
                            onclick="event.stopPropagation(); toggleWishlistFromCard('{{ $book->ISBN }}')"
                            class="wishlist-toggle-btn hidden absolute top-2 right-2 z-10 w-7 h-7 rounded-full bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm flex items-center justify-center shadow-sm text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors"
                            title="Wishlist">
                            <i class="fa-regular fa-heart wishlist-toggle-icon"></i>
                        </button>

                        <img src="{{ $book->{'Image-URL-M'} }}" alt="{{ $book->{'Book-Title'} }}" class="w-full aspect-[4/5] object-cover">
                    
                        <div class="p-3">
                            <h4 class="book-title font-bold text-xs md:text-sm text-slate-800 dark:text-white line-clamp-1">{{ $book->{'Book-Title'} }}</h4>
                            <p class="book-author text-[11px] text-slate-400 mt-0.5">{{ $book->{'Book-Author'} }}</p>
                            <p class="text-[10px] text-slate-400">Tahun: {{ $book->{'Year-Of-Publication'} }}</p>
                            
                            <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                <span class="status-badge inline-block px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[9px] font-semibold rounded-full">Tersedia</span>
                                @if($book->genre)
                                <span class="inline-block px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[9px] font-semibold rounded-full">{{ $book->genre }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination (dibangun oleh renderBookPagination) -->
                <div id="book-pagination" class="flex flex-wrap justify-center gap-2 pt-4"></div>
            </div>

            <!-- TAB 3: SIRKULASI -->
            <div id="sirkulasi" class="tab-content max-w-3xl mx-auto space-y-6">
                <!-- Kontainer status booking: dirender satu kartu PER booking aktif (bukan satu box global),
                     supaya booking baru tidak menimpa tampilan status/countdown booking sebelumnya. -->
                <div id="circ-status-container" class="space-y-4"></div>

                <!-- TEMPLATE (disembunyikan, dipakai oleh JS untuk clone per booking/pesanan) -->
                <!-- Satu template terpadu utk SEMUA jenis pesanan aktif (buku maupun Toko ATK,
                     ambil sendiri maupun kurir): dirender ringkas (accordion) secara default supaya
                     tab Sirkulasi tidak penuh sesak kalau ada banyak pesanan berjalan sekaligus;
                     detail timeline/countdown lengkap baru muncul saat barisnya diklik. -->
                <template id="circ-order-template">
                    <div class="circ-order-card bg-[#FDFCF9] dark:bg-[#1C1C18] rounded-2xl border border-[#E5E2DA] dark:border-[#262620] overflow-hidden">
                        <button type="button" class="circ-order-toggle-btn w-full flex items-center justify-between gap-3 p-4 text-left hover:bg-[#F7F5F0] dark:hover:bg-[#21211D] transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="circ-order-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="circ-order-icon-el text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="circ-order-title-el font-bold text-sm text-[#1A1916] dark:text-white truncate"></p>
                                    <p class="circ-order-status-label-el text-xs text-[#78756D] mt-0.5 truncate"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="circ-order-right-el font-mono text-xs font-bold text-[#1A1916] dark:text-[#EAE8E2]"></span>
                                <i class="fa-solid fa-chevron-down circ-order-chevron-el text-[#78756D] text-xs transition-transform duration-200"></i>
                            </div>
                        </button>
                        <div class="circ-order-detail-el hidden border-t border-[#E5E2DA] dark:border-[#262620] p-4"></div>
                    </div>
                </template>

                <div class="bg-[#FDFCF9] dark:bg-[#1C1C18] p-6 rounded-2xl border border-[#E5E2DA] dark:border-[#262620]">
                    <h4 class="font-bold text-xs uppercase tracking-wider mb-4 text-[#8A867D]" data-i18n="circ-active-loans">Peminjaman Aktif Saya</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs min-w-[500px]">
                            <thead>
                                <tr class="border-b border-[#E5E2DA] dark:border-[#262620] text-[#78756D]">
                                    <th class="pb-3 font-semibold uppercase text-[10px] tracking-wider" data-i18n="circ-table-title">Judul Buku</th>
                                    <th class="pb-3 font-semibold uppercase text-[10px] tracking-wider" data-i18n="circ-table-loan-date">Tgl Pinjam</th>
                                    <th class="pb-3 font-semibold uppercase text-[10px] tracking-wider" data-i18n="circ-table-due-date">Batas Kembali</th>
                                    <th class="pb-3 font-semibold uppercase text-[10px] tracking-wider" data-i18n="circ-table-action">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="active-loans-tbody" class="divide-y divide-[#E5E2DA] dark:divide-[#262620] text-[#1A1916] dark:text-[#EAE8E2]">
                                <tr>
                                    <td colspan="3" class="text-center py-8 text-[#78756D] italic">Memuat data peminjaman...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="fine-breakdown-card" class="hidden bg-[#FDFCF9] dark:bg-[#1C1C18] p-6 rounded-2xl border border-[#E5E2DA] dark:border-[#262620]">
                    <h4 class="font-bold text-xs uppercase tracking-wider mb-4 text-[#8A867D]" data-i18n="circ-fine-breakdown">Rincian Informasi Denda Berjalan</h4>
                    <div id="fine-breakdown-list" class="space-y-3">
                        <!-- Akan diisi secara dinamis -->
                    </div>
                </div>

                <div id="unpaid-fines-card" class="hidden bg-[#FDFCF9] dark:bg-[#1C1C18] p-6 rounded-2xl border border-[#E5E2DA] dark:border-[#262620]">
                    <h4 class="font-bold text-xs uppercase tracking-wider mb-1 text-[#8A867D]">Tagihan Denda</h4>
                    <p class="text-[11px] text-[#78756D] mb-4">Denda dari buku yang sudah dikembalikan. Lunasi untuk bisa meminjam lagi.</p>
                    <div id="unpaid-fines-list" class="space-y-3"></div>
                </div>
            </div>

            <!-- TAB 4: PROFIL -->
            <div id="profil" class="tab-content max-w-xl mx-auto space-y-6">
                <!-- Profil Mahasiswa -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-950 dark:from-slate-900 dark:to-black p-6 rounded-3xl text-center text-white shadow-md relative overflow-hidden">
                    <div class="w-20 h-20 bg-slate-700 rounded-full mx-auto mb-3 border-4 border-slate-600 overflow-hidden">
                        <img id="profile-card-avatar" src="https://api.dicebear.com/7.x/avataaars/svg?seed=libra" class="w-full h-full object-cover">
                    </div>
                    <h3 id="profile-card-name" class="font-bold text-lg">&nbsp;</h3>
                    <p class="text-xs text-slate-400">NIM: <span id="profile-card-nim">-</span></p>
                    <span id="profile-card-fakultas" class="inline-block bg-white/10 text-[10px] font-medium px-3 py-1 rounded-full mt-3 border border-white/10">-</span>
                </div>

                <!-- Pengaturan & Fitur -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden text-sm">
                    <div class="flex justify-between items-center p-4 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <div class="flex items-center gap-3 font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-globe text-lg text-emerald-500"></i> <span data-i18n="language">Bahasa</span>
                        </div>
                        <div class="relative">
                            <!-- select asli disembunyikan, tetap dipakai sebagai penyimpan nilai bahasa -->
                            <select id="language-select" onchange="changeLanguage(this.value)" class="sr-only" tabindex="-1" aria-hidden="true">
                                <option value="id">Indonesia (ID)</option>
                                <option value="en">English (EN)</option>
                            </select>
                            <button type="button" id="lang-btn" onclick="toggleLangMenu(event)" aria-haspopup="listbox" aria-expanded="false"
                                class="flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/70 dark:border-slate-700 transition-colors text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/60">
                                <span id="lang-btn-code" class="w-6 h-6 rounded-lg bg-emerald-500 text-white text-[10px] font-bold flex items-center justify-center transition-colors">ID</span>
                                <span id="lang-btn-label">Indonesia</span>
                                <i id="lang-btn-chevron" class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"></i>
                            </button>
                            <div id="lang-menu" role="listbox" class="hidden fixed z-[60] w-52 p-1.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl opacity-0 scale-95 origin-top-right transition duration-150 ease-out">
                                <button type="button" role="option" data-lang="id" onclick="selectLanguage('id')" class="lang-option w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                    <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white text-[11px] font-bold flex items-center justify-center">ID</span>
                                    <span class="flex-1 leading-tight">
                                        <span class="block text-xs font-semibold text-slate-800 dark:text-slate-100">Indonesia</span>
                                        <span class="block text-[10px] text-slate-400">Bahasa Indonesia</span>
                                    </span>
                                    <i class="lang-check fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                                </button>
                                <button type="button" role="option" data-lang="en" onclick="selectLanguage('en')" class="lang-option w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                    <span class="w-8 h-8 rounded-lg bg-blue-500 text-white text-[11px] font-bold flex items-center justify-center">EN</span>
                                    <span class="flex-1 leading-tight">
                                        <span class="block text-xs font-semibold text-slate-800 dark:text-slate-100">English</span>
                                        <span class="block text-[10px] text-slate-400">English language</span>
                                    </span>
                                    <i class="lang-check fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div onclick="showFineWidget()" class="flex justify-between items-center p-4 border-b border-slate-100 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <div class="flex items-center gap-3 font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-wallet text-lg text-rose-500"></i> <span data-i18n="fine-status">Status Informasi Denda</span>
                        </div>
                        <span class="text-xs text-blue-500 font-semibold flex items-center gap-1"><span data-i18n="show-action">Tampilkan</span> <i class="fa-solid fa-chevron-right text-[10px]"></i></span>
                    </div>

                    <!-- Formulir Feedback -->
                    <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fa-solid fa-comment-dots text-blue-500 text-sm"></i>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white" data-i18n="feedback-title">Kirim Masukan / Feedback</h4>
                        </div>
                        <p class="text-xs text-slate-400 leading-normal mb-4" data-i18n="feedback-desc">Bantu kami meningkatkan Sistem Libra dengan memberikan saran atau laporan kendala Anda.</p>
                        
                        <form id="feedback-form" onsubmit="event.preventDefault(); submitFeedback();" class="space-y-4">
                            <div>
                                <label data-i18n="feedback-label-cat" for="feedback-category" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kategori Masukan</label>
                                <select id="feedback-category" class="w-full text-xs px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white">
                                    <option value="Saran" data-i18n="feedback-opt-saran">Saran & Fitur Baru</option>
                                    <option value="Bug" data-i18n="feedback-opt-bug">Laporan Bug / Error</option>
                                    <option value="Pelayanan" data-i18n="feedback-opt-pelayanan">Fasilitas Perpustakaan</option>
                                    <option value="Lainnya" data-i18n="feedback-opt-lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label data-i18n="feedback-label-msg" for="feedback-message" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pesan Anda</label>
                                <textarea id="feedback-message" rows="4" required class="w-full text-xs px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white resize-none" placeholder="Tuliskan saran atau masukan Anda di sini..."></textarea>
                            </div>
                            <button type="submit" data-i18n="feedback-btn-submit" class="w-full py-2.5 bg-blue-600 text-white text-xs font-semibold rounded-xl shadow-sm hover:bg-blue-700 transition-colors">
                                Kirim Feedback
                            </button>
                        </form>
                    </div>

                    <button onclick="logoutSesi()" class="w-full p-4 text-center font-bold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors" data-i18n="logout">
                        Logout
                    </button>
                </div>
            </div>

            <!-- TAB 5: TOKO ATK -->
            <div id="toko" class="tab-content max-w-4xl mx-auto space-y-5">
                <!-- Header Toko (Northstar Dark Card) -->
                <div class="bg-[#151512] rounded-2xl p-5 text-white border border-[#262620] flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-[#BEF264] text-[#151512] flex items-center justify-center text-lg font-bold shadow-sm">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[#8A867D]">Katalog Toko</p>
                            <h4 class="font-bold text-lg text-white">Toko ATK Perpustakaan</h4>
                            <p class="text-xs text-[#9C988F] mt-0.5">Beli alat tulis & perlengkapan langsung dari perpustakaan.</p>
                        </div>
                    </div>
                    <!-- Tombol Keranjang -->
                    <button onclick="toggleCart()" class="relative bg-[#21211D] hover:bg-[#2A2A24] text-white font-semibold py-2 px-4 rounded-xl flex items-center gap-2 transition border border-[#2E2E28] text-xs">
                        <i class="fa-solid fa-cart-shopping text-[#BEF264]"></i>
                        <span class="hidden sm:inline">Keranjang</span>
                        <span id="cart-count-badge" class="hidden bg-[#BEF264] text-[#151512] text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </button>
                </div>

                <!-- Search & Filter -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text" id="toko-search" oninput="filterProduk()" placeholder="Cari nama atau kode produk..." class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-xl focus:ring-slate-500 focus:border-slate-500 pl-10 pr-4 py-2.5">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400"></i>
                    </div>
                    <select id="toko-kategori" onchange="filterProduk()" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm rounded-xl px-4 py-2.5 focus:ring-slate-500 focus:border-slate-500">
                        <option value="">Semua Kategori</option>
                        <option value="Alat Tulis">Alat Tulis</option>
                        <option value="Buku">Buku</option>
                        <option value="Perlengkapan">Perlengkapan</option>
                    </select>
                </div>

                <!-- Status Loading / Error -->
                <div id="toko-loading" class="text-center py-12 text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl mb-3 text-slate-500 dark:text-slate-400"></i>
                    <p class="text-sm">Memuat produk...</p>
                </div>
                <div id="toko-empty" class="hidden text-center py-12 text-slate-400">
                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm">Tidak ada produk ditemukan.</p>
                </div>
                <div id="toko-error" class="hidden text-center py-12 text-slate-400">
                    <i class="fa-solid fa-triangle-exclamation text-4xl mb-3 opacity-40"></i>
                    <p class="text-sm mb-3">Produk gagal dimuat. Periksa koneksi lalu coba lagi.</p>
                    <button type="button" onclick="loadProdukToko()" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold">Muat ulang</button>
                </div>

                <!-- Grid Produk -->
                <div id="toko-produk-grid" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>

                <!-- Riwayat Pembelian -->
                <div class="bg-[#FDFCF9] dark:bg-[#1C1C18] rounded-2xl border border-[#E5E2DA] dark:border-[#262620] overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b border-[#E5E2DA] dark:border-[#262620] bg-[#F7F5F0] dark:bg-[#21211D] flex-wrap gap-2">
                        <h4 class="font-bold text-sm text-[#1A1916] dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-[#78756D] dark:text-[#A8A49C]"></i> Riwayat Pembelian Saya
                        </h4>
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="inline-flex p-0.5 rounded-lg bg-[#EAE8E1] dark:bg-[#262620] text-[11px] font-medium text-[#78756D] dark:text-[#A8A49C]">
                                <button type="button" onclick="setRiwayatFilter('semua')" id="btn-riwayat-semua" class="px-2.5 py-1 rounded-md bg-[#FDFCF9] dark:bg-[#151512] shadow-sm text-[#1A1916] dark:text-white font-semibold transition">Semua</button>
                                <button type="button" onclick="setRiwayatFilter('proses')" id="btn-riwayat-proses" class="px-2.5 py-1 rounded-md text-[#78756D] dark:text-[#A8A49C] hover:text-[#1A1916] dark:hover:text-white transition">Sedang Dipesan</button>
                                <button type="button" onclick="setRiwayatFilter('selesai')" id="btn-riwayat-selesai" class="px-2.5 py-1 rounded-md text-[#78756D] dark:text-[#A8A49C] hover:text-[#1A1916] dark:hover:text-white transition">Selesai</button>
                            </div>
                            <button onclick="loadRiwayatPembelian()" class="text-xs text-[#1A1916] dark:text-white font-semibold hover:underline ml-1">
                                <i class="fa-solid fa-rotate-right mr-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                    <div id="riwayat-pembelian-list" class="divide-y divide-[#E5E2DA] dark:divide-[#262620] max-h-96 overflow-y-auto hide-scrollbar">
                        <div class="text-center py-8 text-[#78756D] text-xs italic">Memuat riwayat...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FLOATING BUTTON DENDA -->
        <div id="fine-widget" class="hidden fixed bottom-20 md:bottom-6 left-6 bg-rose-600 text-white py-3 px-4 rounded-xl shadow-lg shadow-rose-600/20 flex items-center gap-4 cursor-pointer hover:bg-rose-700 active:scale-95 transition-all z-30" onclick="showFineDetails()">
            <i class="fa-solid fa-triangle-exclamation text-base animate-pulse"></i>
            <div class="text-left font-sans">
                <p class="text-[9px] font-bold uppercase tracking-wider opacity-80">Total Denda</p>
                <p id="fine-amount-display" class="text-xs font-bold">Rp 0</p>

            </div>
        </div>

        <!-- BOTTOM NAVIGATION -->
        <nav id="mobile-bottom-nav" class="md:hidden bg-[#151512] border-t border-[#262620] flex justify-around items-center absolute bottom-0 w-full z-40 h-16 transition-colors duration-200">
            <button onclick="switchTab('beranda')" class="nav-btn-mobile flex flex-col items-center p-2 text-[#BEF264]" data-target="beranda">
                <i class="fa-solid fa-house text-base mb-1"></i>
                <span class="text-[9px] font-semibold">Beranda</span>
            </button>
            <button onclick="switchTab('buku')" class="nav-btn-mobile flex flex-col items-center p-2 text-[#9C988F] hover:text-white" data-target="buku">
                <i class="fa-solid fa-magnifying-glass text-base mb-1"></i>
                <span class="text-[9px] font-semibold">Pustaka</span>
            </button>
            <button onclick="switchTab('sirkulasi')" class="nav-btn-mobile flex flex-col items-center p-2 text-[#9C988F] hover:text-white" data-target="sirkulasi">
                <i class="fa-solid fa-arrows-spin text-base mb-1"></i>
                <span class="text-[9px] font-semibold">Sirkulasi</span>
            </button>
            <button onclick="switchTab('toko')" class="nav-btn-mobile flex flex-col items-center p-2 text-[#9C988F] hover:text-white relative" data-target="toko">
                <i class="fa-solid fa-store text-base mb-1"></i>
                <span class="text-[9px] font-semibold">Toko ATK</span>
                <span id="cart-badge-mobile" class="hidden absolute top-1 right-1 bg-[#BEF264] text-[#151512] text-[8px] font-bold rounded-full w-4 h-4 flex items-center justify-center">0</span>
            </button>
            <button onclick="switchTab('profil')" class="nav-btn-mobile flex flex-col items-center p-2 text-[#9C988F] hover:text-white" data-target="profil">
                <i class="fa-regular fa-user text-base mb-1"></i>
                <span class="text-[9px] font-semibold">Profil</span>
            </button>
        </nav>
    </main>

    <!-- MODAL DETAIL BUKU -->
    <div id="detail-modal" class="hidden fixed inset-0 z-50 flex justify-center items-center p-4 bg-black/60 backdrop-blur-sm modal-overlay" onclick="if(event.target === this) closeDetailModal()">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl p-6 shadow-2xl border border-slate-100 dark:border-slate-800 relative modal-content">
            <button onclick="closeDetailModal()" class="absolute top-4 right-4 w-8 h-8 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-full flex items-center justify-center hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="flex gap-4 mt-2">
                <div class="w-24 h-36 bg-slate-200 rounded-xl overflow-hidden flex-shrink-0 shadow">
                    <img id="modal-cover" src="" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col justify-center flex-1">
                    <span id="modal-genre-badge" class="text-[10px] font-bold text-blue-500 tracking-wider uppercase">Detail Buku</span>
                    <h4 id="modal-title" class="font-bold text-slate-800 dark:text-white text-base leading-tight mt-0.5 mb-1"></h4>
                    <p id="modal-author" class="text-xs text-slate-400 mb-2"></p>
                    <div class="flex items-center gap-2">
                        <span id="modal-status-badge" class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] font-semibold rounded-full">Tersedia</span>
                        <button id="modal-wishlist-btn" type="button" onclick="toggleWishlist()"
                            class="hidden w-7 h-7 rounded-full border border-rose-200 dark:border-rose-900 flex items-center justify-center text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                            title="Wishlist">
                            <i class="fa-regular fa-heart" id="modal-wishlist-icon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 dark:border-slate-800 pt-4">
                <h5 class="font-bold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Rekomendasi Buku Serupa</h5>
                <div id="modal-rekomendasi-container" class="flex gap-3 overflow-x-auto hide-scrollbar pb-1 snap-x">
                    <!-- Akan diisi secara dinamis -->
                </div>
            </div>

            <!-- Tombol Default untuk membuka form -->
            <div class="mt-6" id="booking-default-btn-container">
                <button onclick="openBookingForm()" class="w-full bg-slate-900 dark:bg-blue-600 text-white text-sm font-semibold py-3 rounded-xl shadow-md hover:bg-slate-800 dark:hover:bg-blue-700 active:scale-[0.98] transition-all">
                    <i class="fa-solid fa-book-open mr-2"></i> Booking & Antar ke Rumah
                </button>
            </div>

            <!-- Kartu Antrian: muncul kalau buku sedang dipinjam orang lain -->
            <div id="modal-queue-card" class="hidden mt-3">
                <!-- Diisi secara dinamis oleh loadQueueInfoForModal() -->
            </div>

            <!-- Form Peminjaman Buku (Hidden by default) -->
            <div id="booking-form-section" class="hidden mt-6 border-t border-slate-100 dark:border-slate-800 pt-4 space-y-4 text-xs">
                <h5 class="font-bold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-wider">Formulir Peminjaman Buku</h5>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-500 dark:text-slate-400 mb-1 font-semibold">Mulai Tanggal</label>
                        <input type="date" id="loan-start-date" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-slate-500 dark:text-slate-400 mb-1 font-semibold">Sampai Tanggal</label>
                        <input type="date" id="loan-due-date" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white">
                    </div>
                </div>
                
                <div>
                    <label class="block text-slate-500 dark:text-slate-400 mb-1.5 font-semibold">Opsi Pengambilan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-300">
                            <input type="radio" name="pickup-option" value="Booking" checked class="accent-blue-500">
                            <span>Ambil Sendiri</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-300">
                            <input type="radio" name="pickup-option" value="Booking Dikonfirmasi Admin" class="accent-blue-500">
                            <span>Jasa Pengantaran</span>
                        </label>
                    </div>
                </div>
                
                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="cancelBookingForm()" class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl border border-slate-200/60 dark:border-slate-700 hover:bg-slate-200 transition-all">Batal</button>
                    <button type="button" onclick="submitBookingForm()" class="flex-1 bg-blue-600 text-white font-semibold py-2.5 rounded-xl hover:bg-blue-700 transition-all shadow-md">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KERANJANG BELANJA ATK -->
    <div id="cart-modal" class="hidden fixed inset-0 z-[60] flex justify-center items-end sm:items-center p-0 sm:p-4 bg-black/60 backdrop-blur-sm modal-overlay" onclick="if(event.target===this) toggleCart()">
        <div class="bg-white dark:bg-slate-900 w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl border-t border-slate-100 dark:border-slate-800 modal-content flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800 flex-shrink-0">
                <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping text-slate-800 dark:text-slate-200"></i> Keranjang Belanja
                </h3>
                <button onclick="toggleCart()" class="w-8 h-8 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-full flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div id="cart-items-list" class="flex-1 overflow-y-auto p-5 space-y-3 hide-scrollbar">
                <div class="text-center py-8 text-slate-400 italic text-sm">Keranjang masih kosong.</div>
            </div>

            <!-- Pilihan metode pengiriman: sama persis konsepnya dengan booking buku -->
            <div class="px-5 pt-1 flex-shrink-0">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Metode Pengiriman</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-300 text-sm">
                        <input type="radio" name="atk-delivery-option" value="ambil_sendiri" checked class="accent-slate-800" onchange="toggleAtkAlamatField()">
                        <span>Ambil Sendiri</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-300 text-sm">
                        <input type="radio" name="atk-delivery-option" value="antar" class="accent-slate-800" onchange="toggleAtkAlamatField()">
                        <span>Diantar ke Rumah</span>
                    </label>
                </div>
                <div id="atk-alamat-wrap" class="hidden mt-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Alamat Pengiriman</label>
                    <textarea id="atk-alamat-input" rows="2" placeholder="Tulis alamat lengkap tujuan pengiriman..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-slate-500 text-sm text-slate-800 dark:text-white resize-none"></textarea>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/80 text-sm text-slate-700 dark:text-slate-300">
                            <input type="radio" name="atk-pay-option" value="di_tempat" checked class="accent-slate-800" onchange="updateAtkPayHint()">
                            <span>Bayar di Tempat</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/80 text-sm text-slate-700 dark:text-slate-300">
                            <input type="radio" name="atk-pay-option" value="online" class="accent-slate-800" onchange="updateAtkPayHint()">
                            <span>Bayar Sekarang</span>
                        </label>
                    </div>
                    <p id="atk-pay-hint" class="text-[11px] text-slate-400 mt-2">Bayar saat mengambil di meja sirkulasi</p>
                </div>
            </div>

            <div class="p-5 border-t border-slate-100 dark:border-slate-800 space-y-3 flex-shrink-0">
                <div class="flex justify-between items-center text-sm font-semibold text-slate-700 dark:text-slate-300">
                    <span>Total Pembayaran</span>
                    <span id="cart-total-price" class="text-slate-800 dark:text-slate-200 text-base font-bold">Rp 0</span>
                </div>
                <button onclick="prosesCheckout()" id="checkout-btn" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-slate-900/20 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-money-bill-wave mr-2"></i> Beli Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI PEMBELIAN ATK -->
    <div id="beli-modal" class="hidden fixed inset-0 z-[70] flex justify-center items-center p-4 bg-black/60 backdrop-blur-sm modal-overlay" onclick="if(event.target===this) closeBeli()">
        <div class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 modal-content">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">Tambah ke Keranjang</h3>
                    <button onclick="closeBeli()" class="w-8 h-8 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-full flex items-center justify-center hover:bg-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <!-- Info Produk -->
                <div class="flex gap-3 bg-slate-100 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/40 rounded-2xl p-4 mb-5">
                    <div class="w-10 h-10 bg-slate-200 dark:bg-slate-700/40 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i id="beli-produk-icon" class="fa-solid fa-pen text-slate-800 dark:text-slate-200"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p id="beli-produk-nama" class="font-bold text-sm text-slate-800 dark:text-white truncate"></p>
                        <p id="beli-produk-kode" class="text-xs text-slate-400 mt-0.5"></p>
                        <p id="beli-produk-harga-display" class="text-sm font-semibold text-slate-800 dark:text-slate-200 mt-1"></p>
                    </div>
                </div>
                <!-- Stok -->
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Stok tersedia: <span id="beli-produk-stok" class="font-bold text-slate-700 dark:text-slate-300">-</span></p>
                <!-- Input Jumlah -->
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2">Jumlah</label>
                <div class="flex items-center gap-3 mb-5">
                    <button onclick="changeQty(-1)" class="w-9 h-9 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 font-bold text-lg hover:bg-slate-200 transition">−</button>
                    <input type="number" id="beli-qty" min="1" value="1" class="flex-1 text-center text-base font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl py-2 text-slate-800 dark:text-white focus:outline-none focus:border-slate-500">
                    <button onclick="changeQty(1)" class="w-9 h-9 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 font-bold text-lg hover:bg-slate-200 transition">+</button>
                </div>
                <!-- Total -->
                <div class="flex justify-between items-center text-sm font-semibold mb-5 bg-slate-50 dark:bg-slate-800 rounded-xl px-4 py-3">
                    <span class="text-slate-600 dark:text-slate-400">Subtotal</span>
                    <span id="beli-subtotal" class="text-slate-800 dark:text-slate-200 font-bold">Rp 0</span>
                </div>
                <!-- Tombol -->
                <div class="flex gap-2">
                    <button onclick="closeBeli()" class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 transition-all text-sm">Batal</button>
                    <button onclick="tambahKeKeranjang()" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 rounded-xl transition-all shadow-md shadow-slate-900/20 text-sm">
                        <i class="fa-solid fa-cart-plus mr-1"></i> Tambah
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PEMBAYARAN PESANAN ATK (SIMULASI) -->
    <div id="pay-atk-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-3xl shadow-2xl p-6 border border-slate-200/60 dark:border-slate-800">
            <div class="flex items-start justify-between mb-1">
                <h3 class="font-bold text-base text-slate-800 dark:text-white">Bayar Pesanan</h3>
                <button type="button" onclick="closePayAtkModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 mb-4">SIMULASI PEMBAYARAN - tidak ada uang sungguhan</span>
            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3.5 mb-4 text-xs">
                <p id="pay-atk-items" class="font-semibold text-slate-800 dark:text-white"></p>
                <p id="pay-atk-amount" class="font-mono font-bold text-slate-900 dark:text-white text-lg mt-2"></p>
            </div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Metode Pembayaran</p>
            <div class="space-y-2 mb-5 text-sm">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer"><input type="radio" name="pay-atk-method" value="QRIS" checked class="accent-slate-800"><span>QRIS</span></label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer"><input type="radio" name="pay-atk-method" value="Transfer Bank" class="accent-slate-800"><span>Transfer Bank</span></label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer"><input type="radio" name="pay-atk-method" value="E-Wallet" class="accent-slate-800"><span>E-Wallet</span></label>
            </div>
            <button id="pay-atk-submit" type="button" onclick="submitPayAtk()" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition-all shadow-md text-sm">Bayar Sekarang</button>
            <p class="text-[11px] text-slate-400 mt-3 text-center">Belum siap? Tutup saja, kamu bisa membayarnya nanti dari Riwayat Pembelian.</p>
        </div>
    </div>

    <!-- MODAL PEMBAYARAN DENDA (SIMULASI) -->
    <div id="pay-fine-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-3xl shadow-2xl p-6 border border-slate-200/60 dark:border-slate-800">
            <div class="flex items-start justify-between mb-1">
                <h3 class="font-bold text-base text-slate-800 dark:text-white">Bayar Denda</h3>
                <button type="button" onclick="closePayFineModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 mb-4">SIMULASI PEMBAYARAN - tidak ada uang sungguhan</span>
            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3.5 mb-4 text-xs">
                <p id="pay-fine-book" class="font-semibold text-slate-800 dark:text-white"></p>
                <p id="pay-fine-detail" class="text-slate-400 mt-0.5"></p>
                <p id="pay-fine-amount" class="font-mono font-bold text-rose-500 text-lg mt-2"></p>
            </div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Metode Pembayaran</p>
            <div class="space-y-2 mb-5 text-sm">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer"><input type="radio" name="pay-fine-method" value="QRIS" checked class="accent-slate-800"><span>QRIS</span></label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer"><input type="radio" name="pay-fine-method" value="Transfer Bank" class="accent-slate-800"><span>Transfer Bank</span></label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer"><input type="radio" name="pay-fine-method" value="E-Wallet" class="accent-slate-800"><span>E-Wallet</span></label>
            </div>
            <button id="pay-fine-submit" type="button" onclick="submitPayFine()" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition-all shadow-md text-sm">Bayar Sekarang</button>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="success-toast" class="hidden fixed bottom-20 md:bottom-6 left-1/2 -translate-x-1/2 w-[92%] max-w-[400px] bg-slate-900 dark:bg-slate-800 text-white px-4 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 z-[100] animate-[fadeIn_0.3s_cubic-bezier(0.175,0.885,0.32,1.275)] border border-slate-800 dark:border-slate-700">
        <div id="toast-icon-wrap" class="bg-emerald-500 rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 shadow-md">
            <i id="toast-icon" class="fa-solid fa-check text-white text-sm"></i>
        </div>
        <div class="flex-1">
            <p id="toast-title" class="text-xs font-semibold text-emerald-400">Pemesanan Berhasil!</p>
            <p id="toast-desc" class="text-[11px] text-slate-300 leading-snug mt-0.5">Terima kasih telah meminjam! Buku sedang diantarkan oleh kurir ke alamatmu.</p>
        </div>
        <button onclick="document.getElementById('success-toast').classList.add('hidden')" class="text-slate-400 hover:text-white text-sm p-1">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT                                 -->
    <!-- ========================================== -->
    <script>
        // ============================================================
        // DATA & KONFIGURASI
        // ============================================================
        let currentLang = localStorage.getItem('libra-lang') || 'id';
        // Tab aktif disimpan di localStorage supaya saat halaman di-refresh,
        // pengguna tetap berada di tab yang sama (tidak balik ke Beranda).
        const VALID_TAB_IDS = ['beranda', 'buku', 'sirkulasi', 'profil', 'toko'];
        function getStoredTabId() {
            const stored = localStorage.getItem('libra-active-tab');
            return VALID_TAB_IDS.includes(stored) ? stored : 'beranda';
        }
        let activeTabId = getStoredTabId();
        let activeBookId = null;
        let userFine = 0;
        // wishlistBooks adalah cache di memori, sumber kebenarannya tetap
        // tabel `wishlists` di server (lihat loadWishlist()) supaya server
        // tahu siapa saja yang perlu dinotifikasi saat buku tersedia lagi.
        let wishlistBooks = [];
        let wishlistItems = []; // detail lengkap tiap entri wishlist/antrian (termasuk posisi & estimasi tanggal)

        // Sesi hanya berasal dari login (token + libra_user). Tanpa token -> kembali ke /login.
        (function () {
            let ok = false;
            try { ok = !!localStorage.getItem('libra_token') && !!JSON.parse(localStorage.getItem('libra_user') || 'null'); } catch (e) {}
            if (!ok) { window.location.replace('/login'); return; }
            const u = JSON.parse(localStorage.getItem('libra_user'));
            const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
            set('profile-card-name', u.nama_lengkap || u.name || u.username || '');
            set('profile-card-nim', u.nim || '-');
            set('profile-card-fakultas', u.fakultas || '-');
            const av = document.getElementById('profile-card-avatar');
            if (av) av.src = u.avatar_url || ('https://api.dicebear.com/7.x/avataaars/svg?seed=' + encodeURIComponent(u.username || u.name || 'libra'));
        })();

        function getCurrentUserId() {
            const userStr = localStorage.getItem('libra_user');
            if (!userStr) return null;
            try {
                const u = JSON.parse(userStr);
                return u.id || u.user_id || null;
            } catch (e) {
                return null;
            }
        }

        function getAuthHeaders(extra = {}) {
            const token = localStorage.getItem('libra_token');
            const h = { 'Accept': 'application/json', ...extra };
            if (token) h['Authorization'] = 'Bearer ' + token;
            return h;
        }

        async function loadWishlist() {
            const userId = getCurrentUserId();
            if (!userId) return;

            try {
                const response = await fetch(`/api/wishlist?user_id=${userId}`, { headers: getAuthHeaders() });
                if (!response.ok) throw new Error('Gagal mengambil wishlist');
                const items = await response.json();
                wishlistItems = items;
                wishlistBooks = items.map(item => item.book_id);
            } catch (e) {
                console.error('Gagal memuat wishlist:', e);
            }

            renderWishlist();
            syncWishlistUI();
        }

        // ============================================================
        // AMBIL DATA BUKU DARI DOM
        // ============================================================
        function getBooksFromDOM() {
            const books = [];
            document.querySelectorAll('#live-library-container > .book-card').forEach(card => {
                const id = card.dataset.id;
                const title = card.dataset.title;
                const author = card.dataset.author;
                const cover = card.dataset.cover;
                const status = card.dataset.status;
                const genre = card.dataset.genre || 'Science';
                const rating = parseFloat(card.dataset.rating) || 4.5;
                const desc = card.dataset.desc || 'Deskripsi buku tidak tersedia.';
                books.push({ id, title, author, cover, status, genre, rating, desc });
            });
            return books;
        }

        let booksData = getBooksFromDOM();

        // ============================================================
        // TRANSLASI
        // ============================================================
        const langDictionary = {
            id: {
                'nav-beranda': 'Beranda', 'nav-pustaka': 'Pustaka', 'nav-sirkulasi': 'Sirkulasi', 'nav-profil': 'Profil',
                'my-wishlist': 'Wishlist Saya', 'recommended': 'Direkomendasikan Untukmu',
                'dark-mode': 'Tema Gelap', 'language': 'Bahasa', 'fine-status': 'Status Informasi Denda', 
                'logout': 'Logout', 'show-action': 'Tampilkan',
                'wishlist-empty': 'Belum ada buku di wishlist kamu.',

                'feedback-title': 'Kirim Masukan / Feedback', 
                'feedback-desc': 'Bantu kami meningkatkan Sistem Libra dengan memberikan saran atau laporan kendala Anda.',
                'feedback-label-cat': 'Kategori Masukan',
                'feedback-opt-saran': 'Saran & Fitur Baru', 'feedback-opt-bug': 'Laporan Bug / Error', 
                'feedback-opt-pelayanan': 'Fasilitas Perpustakaan', 'feedback-opt-lainnya': 'Lainnya',
                'feedback-label-msg': 'Pesan Anda', 'feedback-btn-submit': 'Kirim Feedback',

                'circ-deadline-title': 'Batas Pengambilan Resv.',
                'circ-deadline-desc': 'Ambil buku langsung ke meja sirkulasi perpustakaan sebelum batas waktu habis.',
                'circ-remaining-time': 'Sisa Waktu:',
                'circ-courier-status': 'Status Pengiriman Kurir',
                'circ-status-1-title': 'Diantar Kurir',
                'circ-status-1-desc': 'Kurir sedang menuju ke Fakultas Ilmu Komputer. Estimasi 10 menit.',
                'circ-status-2-title': 'Buku Selesai Dikemas',
                'circ-status-2-desc': 'Buku telah diserahkan ke kurir internal.',
                'circ-status-3-title': 'Booking Dikonfirmasi',
                'circ-status-3-desc': 'Permintaan disetujui oleh sistem.',
                'circ-table-title': 'Judul Buku',
                'circ-table-loan-date': 'Tgl Pinjam',
                'circ-table-due-date': 'Batas Kembali',
                'circ-table-action': 'Aksi',
                'circ-loan-date-1': '22 Juni 2026',
                'circ-due-date-1': '29 Juni 2026 (Hari Ini)',
                'circ-loan-date-2': '15 Juni 2026',
                'circ-due-date-2': '05 Juli 2026',
                'circ-fine-item-title': 'Keterlambatan Pengembalian: "Sistem Basis Data"',
                'circ-fine-item-desc': 'Terlambat 5 hari × Rp 2.000 / hari',
                'circ-fine-note': '*Kembalikan bukunya ke perpustakaan, lalu bayar dendanya di bagian Tagihan Denda untuk mengaktifkan kembali hak peminjaman Anda.',
                'modal-btn-book': 'Ambil / Booking Buku',
                'modal-book-disabled': 'Buku Sedang Dipinjam'
            },
            en: {
                'nav-beranda': 'Home', 'nav-pustaka': 'Library', 'nav-sirkulasi': 'Circulation', 'nav-profil': 'Profile',
                'my-wishlist': 'My Wishlist', 'recommended': 'Recommended for You',
                'dark-mode': 'Dark Mode', 'language': 'Language', 'fine-status': 'Fine Information Status', 
                'logout': 'Logout', 'show-action': 'Show',
                'wishlist-empty': 'No books in your wishlist yet.',

                'feedback-title': 'Submit Feedback',
                'feedback-desc': 'Help us improve the Libra System by providing your suggestions or reporting your issues.',
                'feedback-label-cat': 'Feedback Category',
                'feedback-opt-saran': 'Suggestions & New Features', 'feedback-opt-bug': 'Bug Report / Error',
                'feedback-opt-pelayanan': 'Library Facilities', 'feedback-opt-lainnya': 'Others',
                'feedback-label-msg': 'Your Message', 'feedback-btn-submit': 'Submit Feedback',

                'circ-deadline-title': 'Reservation Pickup Deadline',
                'circ-deadline-desc': 'Pick up the book directly at the library circulation desk before the deadline.',
                'circ-remaining-time': 'Remaining Time:',
                'circ-courier-status': 'Courier Delivery Status',
                'circ-status-1-title': 'Out for Delivery',
                'circ-status-1-desc': 'Courier is heading to the Faculty of Computer Science. Estimated 10 mins.',
                'circ-status-2-title': 'Packaging Completed',
                'circ-status-2-desc': 'Book has been handed over to the internal campus courier.',
                'circ-status-3-title': 'Booking Confirmed',
                'circ-status-3-desc': 'Reservation request approved by the system.',
                'circ-table-title': 'Book Title',
                'circ-table-loan-date': 'Loan Date',
                'circ-table-due-date': 'Due Date',
                'circ-table-action': 'Action',
                'circ-loan-date-1': '22 June 2026',
                'circ-due-date-1': '29 June 2026 (Today)',
                'circ-loan-date-2': '15 June 2026',
                'circ-due-date-2': '05 July 2026',
                'circ-fine-item-title': 'Overdue Return: "Database Systems"',
                'circ-fine-item-desc': '5 days overdue × Rp 2,000 / day',
                'circ-fine-note': '*Return the book to the library, then pay the fine in the Fine Bills section to restore your borrowing privileges.',
                'modal-btn-book': 'Book / Reserve Book',
                'modal-book-disabled': 'Book is Currently Borrowed'
            }
        };

        const tabHeaderDictionary = {
            id: {
                'beranda': { title: 'Beranda', sub: 'Selamat datang kembali, Mahasiswa!' },
                'buku': { title: 'Pustaka Digital', sub: 'Cari koleksi katalog buku perpustakaan secara live' },
                'sirkulasi': { title: 'Sirkulasi', sub: 'Kelola peminjaman, tracking kurir, dan denda' },
                'profil': { title: 'Profil Pengguna', sub: 'Pengaturan akun, preferensi tema, dan masukan' },
                'toko': { title: 'Toko ATK', sub: 'Beli alat tulis & perlengkapan langsung dari perpustakaan' }
            },
            en: {
                'beranda': { title: 'Home', sub: 'Welcome back, Student!' },
                'buku': { title: 'Digital Library', sub: 'Search library book collections live' },
                'sirkulasi': { title: 'Circulation', sub: 'Manage loans, courier tracking, and fines' },
                'profil': { title: 'User Profile', sub: 'Account settings, theme preferences, and feedback' },
                'toko': { title: 'ATK Store', sub: 'Buy stationery & supplies directly from the library' }
            }
        };

        // ============================================================
        // FUNGSI NAVIGASI
        // ============================================================
        function switchTab(tabId) {
            activeTabId = tabId;
            localStorage.setItem('libra-active-tab', tabId);
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            const targetContent = document.getElementById(tabId);
            if(targetContent) targetContent.classList.add('active');

            document.querySelectorAll('.nav-btn').forEach(btn => {
                if(btn.getAttribute('data-target') === tabId) {
                    btn.className = "nav-btn w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-semibold bg-[#BEF264] text-[#151512] transition-all";
                } else {
                    btn.className = "nav-btn w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-medium text-[#9C988F] hover:text-white hover:bg-[#21211D] transition-all";
                }
            });

            document.querySelectorAll('.nav-btn-mobile').forEach(btn => {
                if(btn.getAttribute('data-target') === tabId) {
                    btn.className = 'nav-btn-mobile flex flex-col items-center p-2 text-[#BEF264]';
                } else {
                    btn.className = 'nav-btn-mobile flex flex-col items-center p-2 text-[#9C988F] hover:text-white';
                }
            });

            const headers = (tabHeaderDictionary[currentLang] || tabHeaderDictionary['id'])[tabId] || { title: tabId, sub: '' };
            document.getElementById('header-title').innerText = headers.title;
            document.getElementById('header-subtitle').innerText = headers.sub;
            
            if (tabId === 'buku') {
                const searchInput = document.getElementById('search-input');
                if (searchInput) searchInput.value = '';
                liveSearch();
            }
            if (tabId === 'beranda') {
                renderWishlist();
                renderRecommendedBooks();
            }
            if (tabId === 'sirkulasi') {
                loadUserLoans();
                startSirkulasiAutoRefresh();
            } else {
                stopSirkulasiAutoRefresh();
            }
            if (tabId === 'toko') {
                loadProdukToko();
                loadRiwayatPembelian();
            }
        }

        // ============================================================
        // AUTO-REFRESH TAB SIRKULASI
        // Supaya kartu "Batas Pengambilan Resv." dan "Status Pengiriman Kurir"
        // otomatis HILANG saat admin mengubah status peminjaman (mis. buku
        // sudah diambil / sudah sampai ke pengguna), tanpa pengguna perlu
        // reload halaman secara manual. Polling hanya berjalan selama tab
        // "sirkulasi" sedang aktif, dan otomatis berhenti saat pindah tab.
        // ============================================================
        let sirkulasiAutoRefreshInterval = null;

        function startSirkulasiAutoRefresh() {
            stopSirkulasiAutoRefresh(); // Cegah interval dobel
            sirkulasiAutoRefreshInterval = setInterval(() => {
                if (activeTabId === 'sirkulasi') {
                    loadUserLoans();
                }
            }, 30000); // 30 detik
        }

        function stopSirkulasiAutoRefresh() {
            if (sirkulasiAutoRefreshInterval) {
                clearInterval(sirkulasiAutoRefreshInterval);
                sirkulasiAutoRefreshInterval = null;
            }
        }

        function timeAgo(dateStr) {
            // Jaga-jaga kalau created_at kosong/null/tidak valid (misalnya data
            // lama sebelum kolom created_at diisi otomatis) -- tanpa ini,
            // new Date(null) dianggap 1 Jan 1970 dan menghasilkan angka
            // "puluhan ribu hari lalu" yang tidak masuk akal.
            if (!dateStr) return '-';
            const parsed = new Date(dateStr).getTime();
            if (isNaN(parsed)) return '-';

            const diffMs = Date.now() - parsed;
            const mins = Math.floor(diffMs / 60000);
            if (mins < 1) return 'Baru saja';
            if (mins < 60) return mins + ' menit lalu';
            const hours = Math.floor(mins / 60);
            if (hours < 24) return hours + ' jam lalu';
            const days = Math.floor(hours / 24);
            return days + ' hari lalu';
        }

        function notifColor(type) {
            if (type === 'warning') return 'text-rose-500';
            if (type === 'success') return 'text-green-500';
            return 'text-blue-500';
        }

        async function loadNotifications() {
            const userStr = localStorage.getItem('libra_user');
            const list = document.getElementById('notif-list');
            const badge = document.getElementById('notif-badge');
            if (!userStr) {
                list.innerHTML = '<p class="text-xs text-slate-400 text-center py-4">Silakan login untuk melihat notifikasi.</p>';
                if (badge) badge.classList.add('hidden');
                return;
            }
            const user = JSON.parse(userStr);

            try {
                const response = await fetch(`/api/notifications?user_id=${user.id}`, { headers: getAuthHeaders() });
                if (!response.ok) throw new Error('Gagal mengambil notifikasi');
                const notifications = await response.json();

                if (!notifications.length) {
                    list.innerHTML = '<p class="text-xs text-slate-400 text-center py-4">Belum ada notifikasi.</p>';
                    if (badge) badge.classList.add('hidden');
                    return;
                }

                list.innerHTML = notifications.map((n, i) => `
                    <div class="${i < notifications.length - 1 ? 'border-b border-slate-100 dark:border-slate-800' : ''} pb-2.5">
                        <div class="flex justify-between text-[11px] mb-1">
                            <span class="font-bold ${notifColor(n.type)}">${n.title}</span>
                            <span class="text-slate-400">${timeAgo(n.created_at)}</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-normal">${n.message}</p>
                    </div>
                `).join('');

                const hasUnread = notifications.some(n => !n.is_read);
                if (badge) badge.classList.toggle('hidden', !hasUnread);
            } catch (err) {
                console.error('Error loading notifications:', err);
                list.innerHTML = '<p class="text-xs text-rose-400 text-center py-4">Gagal memuat notifikasi.</p>';
            }
        }

        function toggleNotif(event) { 
            if(event) event.stopPropagation();
            const panel = document.getElementById('notif-panel');
            const willOpen = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            if (willOpen) loadNotifications();
        }

        async function clearNotifications() { 
            document.getElementById('notif-panel').classList.add('hidden');
            const badge = document.getElementById('notif-badge');
            if (badge) badge.classList.add('hidden');

            const userStr = localStorage.getItem('libra_user');
            if (!userStr) return;
            const user = JSON.parse(userStr);

            try {
                await fetch(`/api/notifications/read-all?user_id=${user.id}`, { method: 'PATCH', headers: getAuthHeaders() });
            } catch (err) {
                console.error('Error marking notifications as read:', err);
            }
        }

        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('libra-theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        }

        function logoutSesi() { 
            if(confirm(currentLang === 'id' ? "Keluar dari sesi?" : "Log out?")) { 
                localStorage.removeItem('libra_token');
                localStorage.removeItem('libra_user');
                localStorage.removeItem('libra_remember');
                localStorage.removeItem('libra_courier_seen');
                sessionStorage.removeItem('libra_active');
                window.location.href = '/login';
            } 
        }

        // ============================================================
        // FUNGSI BAHASA Test 1
        // ============================================================
        function changeLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('libra-lang', lang);
            applyTranslations();
            switchTab(activeTabId);
            renderWishlist();
            renderRecommendedBooks();
            updateCourierStatusVisibility();
            updateFineDisplay();
        }

        // ============================================================
        // DROPDOWN BAHASA (CUSTOM)
        // ============================================================
        function isLangMenuOpen() {
            const m = document.getElementById('lang-menu');
            return !!m && !m.classList.contains('hidden');
        }

        function openLangMenu() {
            const btn = document.getElementById('lang-btn');
            const menu = document.getElementById('lang-menu');
            if (!btn || !menu) return;
            menu.classList.remove('hidden');
            // Posisi fixed supaya tidak terpotong oleh kartu pengaturan (overflow-hidden)
            const r = btn.getBoundingClientRect();
            const w = menu.offsetWidth || 208;
            const h = menu.offsetHeight || 120;
            const left = Math.max(8, Math.min(r.right - w, window.innerWidth - w - 8));
            const openUp = r.bottom + h + 12 > window.innerHeight && r.top > h + 12;
            menu.style.left = left + 'px';
            menu.style.top = (openUp ? r.top - h - 8 : r.bottom + 8) + 'px';
            menu.style.transformOrigin = openUp ? 'bottom right' : 'top right';
            requestAnimationFrame(() => {
                menu.classList.remove('opacity-0', 'scale-95');
                menu.classList.add('opacity-100', 'scale-100');
            });
            btn.setAttribute('aria-expanded', 'true');
            document.getElementById('lang-btn-chevron').classList.add('rotate-180');
        }

        function closeLangMenu() {
            const btn = document.getElementById('lang-btn');
            const menu = document.getElementById('lang-menu');
            if (!menu || menu.classList.contains('hidden')) return;
            menu.classList.remove('opacity-100', 'scale-100');
            menu.classList.add('opacity-0', 'scale-95');
            setTimeout(() => menu.classList.add('hidden'), 150);
            if (btn) btn.setAttribute('aria-expanded', 'false');
            const chev = document.getElementById('lang-btn-chevron');
            if (chev) chev.classList.remove('rotate-180');
        }

        function toggleLangMenu(e) {
            if (e) e.stopPropagation();
            if (isLangMenuOpen()) closeLangMenu(); else openLangMenu();
        }

        function selectLanguage(lang) {
            const sel = document.getElementById('language-select');
            if (sel) sel.value = lang;
            closeLangMenu();
            if (lang !== currentLang) changeLanguage(lang);
        }

        // Samakan tampilan dropdown dengan bahasa aktif
        function syncLangDropdown() {
            const isEn = currentLang === 'en';
            const code = document.getElementById('lang-btn-code');
            const label = document.getElementById('lang-btn-label');
            if (code) {
                code.textContent = isEn ? 'EN' : 'ID';
                code.classList.toggle('bg-blue-500', isEn);
                code.classList.toggle('bg-emerald-500', !isEn);
            }
            if (label) label.textContent = isEn ? 'English' : 'Indonesia';
            document.querySelectorAll('#lang-menu .lang-option').forEach(opt => {
                const active = opt.dataset.lang === currentLang;
                opt.setAttribute('aria-selected', active ? 'true' : 'false');
                opt.classList.toggle('bg-slate-50', active);
                opt.classList.toggle('dark:bg-slate-800/60', active);
                const check = opt.querySelector('.lang-check');
                if (check) check.style.visibility = active ? 'visible' : 'hidden';
            });
        }

        document.addEventListener('click', (e) => {
            if (!isLangMenuOpen()) return;
            if (!e.target.closest('#lang-menu') && !e.target.closest('#lang-btn')) closeLangMenu();
        });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLangMenu(); });
        window.addEventListener('resize', closeLangMenu);
        window.addEventListener('scroll', closeLangMenu, true);

        function applyTranslations() {
            document.getElementById('language-select').value = currentLang;
            syncLangDropdown();
            const dict = langDictionary[currentLang];
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (dict[key]) el.innerText = dict[key];
            });
            
            const searchInp = document.getElementById('search-input');
            if(searchInp) searchInp.placeholder = currentLang === 'id' ? 'Cari judul buku secara live...' : 'Search book titles live...';
            
            const feedbackMsg = document.getElementById('feedback-message');
            if(feedbackMsg) feedbackMsg.placeholder = currentLang === 'id' ? 'Tuliskan saran atau masukan Anda di sini...' : 'Write your suggestions or feedback here...';
        }

        // ============================================================
        // FUNGSI PUSTAKA
        // ============================================================
        const BOOKS_PER_PAGE = 50;
        let bookPage = 1;

        // resetPage=true: dipanggil saat pencarian/filter berubah (kembali ke halaman 1)
        function liveSearch(resetPage = true) {
            const query = document.getElementById('search-input').value.toLowerCase().trim();
            const genreFilterEl = document.getElementById('genre-filter');
            const selectedGenre = genreFilterEl ? genreFilterEl.value : '';
            const container = document.getElementById('live-library-container');
            const cards = container.querySelectorAll('.book-card');
            if (resetPage) bookPage = 1;

            const matched = [];
            cards.forEach((card) => {
                const title = (card.dataset.title || '').toLowerCase();
                const author = (card.dataset.author || '').toLowerCase();
                const genre = card.dataset.genre || '';
                const matchesQuery = title.includes(query) || author.includes(query);
                const matchesGenre = !selectedGenre || genre === selectedGenre;
                if (matchesQuery && matchesGenre) matched.push(card); else card.style.display = 'none';
            });

            const totalPages = Math.max(1, Math.ceil(matched.length / BOOKS_PER_PAGE));
            if (bookPage > totalPages) bookPage = totalPages;
            const start = (bookPage - 1) * BOOKS_PER_PAGE;
            matched.forEach((card, i) => {
                card.style.display = (i >= start && i < start + BOOKS_PER_PAGE) ? '' : 'none';
            });
            renderBookPagination(totalPages);
        }

        function goToBookPage(n) {
            bookPage = n;
            liveSearch(false);
            const c = document.getElementById('live-library-container');
            if (c) c.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Klik "…" -> berubah jadi kotak angka; Enter untuk pindah halaman, Esc/klik di luar untuk batal.
        function openPageJump(btn, totalPages) {
            const inp = document.createElement('input');
            inp.type = 'number'; inp.min = 1; inp.max = totalPages;
            inp.placeholder = '1-' + totalPages;
            inp.className = 'w-20 px-2 py-2 rounded-lg text-sm text-center border border-blue-500 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 outline-none';
            let done = false;
            const go = () => {
                if (done) return; done = true;
                const v = parseInt(inp.value, 10);
                if (!isNaN(v)) goToBookPage(Math.min(totalPages, Math.max(1, v)));
                else renderBookPagination(totalPages);
            };
            inp.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); go(); }
                else if (e.key === 'Escape') { done = true; renderBookPagination(totalPages); }
            });
            inp.addEventListener('blur', go);
            btn.replaceWith(inp);
            inp.focus();
        }

        function renderBookPagination(totalPages) {
            const box = document.getElementById('book-pagination');
            if (!box) return;
            if (totalPages <= 1) { box.innerHTML = ''; return; }
            const base = 'px-4 py-2 rounded-lg text-sm transition-colors';
            const idle = 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800';
            const off = 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-300 dark:text-slate-600 cursor-not-allowed';
            // Daftar nomor: 1 ... (aktif-1) aktif (aktif+1) ... terakhir
            const nums = [];
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || Math.abs(i - bookPage) <= 1) nums.push(i);
                else if (nums[nums.length - 1] !== '...') nums.push('...');
            }
            let html = `<button type="button" ${bookPage === 1 ? 'disabled' : `onclick="goToBookPage(${bookPage - 1})"`} class="${base} ${bookPage === 1 ? off : idle}">Sebelumnya</button>`;
            nums.forEach(n => {
                if (n === '...') html += `<button type="button" title="Ke halaman…" onclick="openPageJump(this, ${totalPages})" class="px-2 py-2 text-slate-400 hover:text-blue-600 rounded-lg transition-colors">…</button>`;
                else html += `<button type="button" onclick="goToBookPage(${n})" class="${base} ${n === bookPage ? 'bg-blue-600 text-white font-semibold' : idle}">${n}</button>`;
            });
            html += `<button type="button" ${bookPage === totalPages ? 'disabled' : `onclick="goToBookPage(${bookPage + 1})"`} class="${base} ${bookPage === totalPages ? off : idle}">Selanjutnya</button>`;
            box.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', () => liveSearch());

        // ============================================================
        // FUNGSI BOOK DETAIL MODAL - VERSI PERBAIKAN
        // ============================================================
        function openBookDetail(id) {
            activeBookId = id;
            cancelBookingForm();

            // Cari card buku berdasarkan data-id
            const card = document.querySelector(`.book-card[data-id="${id}"]`);
            if (!card) {
                console.error('Buku tidak ditemukan dengan ID:', id);
                return;
            }
            
            // Ambil data dari atribut card
            const title = card.dataset.title;
            const author = card.dataset.author;
            const cover = card.dataset.cover;
            const status = card.dataset.status;
            const genre = card.dataset.genre || 'Buku';
            const rating = card.dataset.rating || '4.5';
            const desc = card.dataset.desc || 'Deskripsi buku tidak tersedia.';
            
            // Isi modal
            document.getElementById('modal-cover').src = cover;
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-author').textContent = author;
            document.getElementById('modal-genre-badge').textContent = genre;
            
            // Status badge
            const badge = document.getElementById('modal-status-badge');
            if (status === 'Tersedia') {
                badge.textContent = currentLang === 'id' ? 'Tersedia' : 'Available';
                badge.className = 'inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] font-semibold rounded-full';
            } else {
                badge.textContent = currentLang === 'id' ? 'Dipinjam' : 'On Loan';
                badge.className = 'inline-block px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-[10px] font-semibold rounded-full';
            }

            // Booking button: statusnya SELALU dicek ke server (loadQueueInfoForModal),
            // bukan cuma dari field `status` buku -- karena buku yang kelihatan
            // "Tersedia" pun bisa sedang DITAHAN (hold) khusus utk antrian #1.
            if (status === 'Dipinjam') {
                const bookingBtn = document.querySelector('#booking-default-btn-container button');
                bookingBtn.disabled = true;
                bookingBtn.className = 'w-full bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-sm font-semibold py-3 rounded-xl shadow-md cursor-not-allowed opacity-60';
                bookingBtn.innerHTML = `<i class="fa-solid fa-ban mr-2"></i> ${currentLang === 'id' ? 'Buku Sedang Dipinjam' : 'Book Currently Borrowed'}`;
            }
            // Baik status Tersedia maupun Dipinjam, selalu cek ke server utk hold/antrian.
            loadQueueInfoForModal(id);

            // Rekomendasi buku
            renderRecommendations(id);

            // Sinkronkan tampilan tombol wishlist (hanya muncul untuk buku Dipinjam)
            syncWishlistUI();

            // Tampilkan modal
            document.getElementById('detail-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // ===================== ANTRIAN BUKU (QUEUE) =====================

        /**
         * Ambil info antrian/hold terbaru dari server lalu atur TOMBOL BOOKING
         * UTAMA + kartu antrian di bawahnya sesuai salah satu dari 4 kondisi:
         *
         * 1. Buku tersedia & tidak ada hold         -> tombol booking aktif normal
         * 2. Buku tersedia & SEDANG giliranmu (hold) -> tombol booking aktif +
         *    catatan batas waktu sebelum hangus
         * 3. Buku tersedia tapi DITAHAN utk org lain -> tombol booking TERKUNCI
         *    + penjelasan sampai kapan
         * 4. Buku masih dipinjam org lain            -> tombol booking disabled
         *    (sudah diatur di openBookDetail) + kartu join/leave antrian
         */
        // Nonaktifkan tombol "Masuk Antrian" untuk user yang sedang meminjam buku ini.
        function disableJoinQueueButton(card) {
            const btn = card.querySelector('button[onclick^="joinQueue"]');
            if (!btn) return;
            btn.disabled = true;
            btn.removeAttribute('onclick');
            btn.className = 'mt-3 w-full text-xs font-semibold text-slate-400 bg-slate-200 dark:bg-slate-700 dark:text-slate-500 rounded-lg py-2.5 cursor-not-allowed opacity-70';
            btn.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Kamu sedang meminjam buku ini';
        }

        async function loadQueueInfoForModal(bookId) {
            const card = document.getElementById('modal-queue-card');
            const bookingBtn = document.querySelector('#booking-default-btn-container button');
            if (!card || !bookingBtn) return;

            const userId = getCurrentUserId();

            try {
                const url = userId
                    ? `/api/wishlist/queue-info?book_id=${bookId}&user_id=${userId}`
                    : `/api/wishlist/queue-info?book_id=${bookId}`;
                const res = await fetch(url, { headers: getAuthHeaders() });
                if (!res.ok) throw new Error('Gagal memuat info antrian');
                const info = await res.json();

                const estText = info.your_estimated_date
                    ? new Date(info.your_estimated_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                    : 'Belum bisa diperkirakan';

                // ===== Kondisi 3: buku tersedia tapi terkunci utk antrian org lain =====
                if (info.book_available && info.locked_for_you) {
                    const expiresText = info.hold_expires_at
                        ? new Date(info.hold_expires_at).toLocaleString('id-ID', { day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' })
                        : '-';

                    bookingBtn.disabled = true;
                    bookingBtn.className = 'w-full bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-sm font-semibold py-3 rounded-xl shadow-md cursor-not-allowed opacity-60';
                    bookingBtn.innerHTML = `<i class="fa-solid fa-lock mr-2"></i> Ditahan untuk Antrian Lain`;

                    card.innerHTML = `
                        <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl p-4 text-sm">
                            <p class="font-bold text-slate-600 dark:text-slate-300">
                                <i class="fa-solid fa-clock mr-1.5"></i> Buku ini sedang ditahan untuk orang pertama di antrian
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Kalau sampai <span class="font-semibold">${expiresText}</span> dia tidak booking, giliran otomatis pindah ke antrian berikutnya.
                            </p>
                            ${info.already_in_queue
                                ? `<p class="text-xs text-blue-600 dark:text-blue-400 mt-2 font-semibold">Kamu ada di antrian ke-${info.your_position} dari ${info.total_in_queue}.</p>`
                                : `<button onclick="joinQueue('${bookId}')" class="mt-3 w-full text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg py-2.5 transition-all">
                                        <i class="fa-solid fa-bell mr-1"></i> Masuk Antrian (jadi ke-${info.your_position})
                                   </button>`
                            }
                        </div>`;
                    if (info.already_borrowing) disableJoinQueueButton(card);
                    card.classList.remove('hidden');
                    return;
                }

                // ===== Kondisi 2: buku tersedia & SEDANG giliranmu =====
                if (info.book_available && info.is_your_turn) {
                    const expiresText = info.hold_expires_at
                        ? new Date(info.hold_expires_at).toLocaleString('id-ID', { day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' })
                        : '-';

                    bookingBtn.disabled = false;
                    bookingBtn.className = 'w-full bg-slate-900 dark:bg-blue-600 text-white text-sm font-semibold py-3 rounded-xl shadow-md hover:bg-slate-800 dark:hover:bg-blue-700 active:scale-[0.98] transition-all';
                    bookingBtn.innerHTML = `<i class="fa-solid fa-book-open mr-2"></i> Booking & Antar ke Rumah`;

                    card.innerHTML = `
                        <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 rounded-xl p-3 text-xs">
                            <p class="font-bold text-emerald-700 dark:text-emerald-400">
                                <i class="fa-solid fa-circle-check mr-1.5"></i> Ini giliranmu!
                            </p>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">Booking sebelum <span class="font-semibold">${expiresText}</span>, atau giliranmu akan hangus.</p>
                        </div>`;
                    card.classList.remove('hidden');
                    return;
                }

                // ===== Kondisi 1: buku tersedia bebas, tidak ada hold sama sekali =====
                if (info.book_available) {
                    bookingBtn.disabled = false;
                    bookingBtn.className = 'w-full bg-slate-900 dark:bg-blue-600 text-white text-sm font-semibold py-3 rounded-xl shadow-md hover:bg-slate-800 dark:hover:bg-blue-700 active:scale-[0.98] transition-all';
                    bookingBtn.innerHTML = `<i class="fa-solid fa-book-open mr-2"></i> Booking & Antar ke Rumah`;
                    card.classList.add('hidden');
                    return;
                }

                // ===== Kondisi 4: buku masih dipinjam org lain -- tampilkan join/leave antrian =====
                if (info.already_in_queue) {
                    card.innerHTML = `
                        <div class="bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4 text-sm">
                            <p class="font-bold text-blue-700 dark:text-blue-400">
                                <i class="fa-solid fa-list-ol mr-1.5"></i> Kamu antrian ke-${info.your_position} dari ${info.total_in_queue}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Perkiraan giliranmu: <span class="font-semibold">${estText}</span></p>
                            <button onclick="leaveQueue('${bookId}')" class="mt-3 w-full text-xs font-semibold text-rose-500 border border-rose-200 dark:border-rose-900/50 rounded-lg py-2 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all">
                                Batalkan Antrian
                            </button>
                        </div>`;
                } else {
                    card.innerHTML = `
                        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40 rounded-xl p-4 text-sm">
                            <p class="font-bold text-amber-700 dark:text-amber-500">
                                <i class="fa-solid fa-users mr-1.5"></i> ${info.total_in_queue} orang sedang mengantre
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Kalau kamu antre sekarang, kamu jadi antrian ke-${info.your_position}.
                                Perkiraan giliranmu: <span class="font-semibold">${estText}</span>
                            </p>
                            <button onclick="joinQueue('${bookId}')" class="mt-3 w-full text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg py-2.5 transition-all">
                                <i class="fa-solid fa-bell mr-1"></i> Masuk Antrian
                            </button>
                        </div>`;
                }
                if (info.already_borrowing) disableJoinQueueButton(card);
                card.classList.remove('hidden');
            } catch (err) {
                console.error('Gagal memuat info antrian:', err);
                card.classList.add('hidden');
            }
        }

        let joinQueueInFlight = false;
        async function joinQueue(bookId) {
            const userId = getCurrentUserId();
            if (!userId) {
                showToast('Info', 'Silakan login untuk ikut antrian.');
                return;
            }
            if (joinQueueInFlight) return;
            if (wishlistBooks.includes(bookId)) {
                showToast('Info', 'Kamu sudah ada di antrian buku ini.');
                return;
            }
            joinQueueInFlight = true;
            const qBtn = document.querySelector('#modal-queue-card button[onclick^="joinQueue"]');
            if (qBtn) { qBtn.disabled = true; qBtn.classList.add('opacity-60', 'cursor-not-allowed'); }

            const book = booksData.find(b => b.id === bookId);

            try {
                const res = await fetch('/api/wishlist', {
                    method: 'POST',
                    headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
                    body: JSON.stringify({ user_id: userId, book_id: bookId })
                });
                const data = await res.json();

                if (!res.ok) {
                    showToast('Tidak Bisa Masuk Antrian', data.message || 'Terjadi kesalahan, coba lagi.');
                    return;
                }

                if (!wishlistBooks.includes(bookId)) wishlistBooks.push(bookId);

                showToast('Berhasil Masuk Antrian', `Kamu telah masuk antrian untuk buku "${book ? book.title : ''}". Kami akan memberi tahu kamu saat giliranmu tiba.`);

                loadQueueInfoForModal(bookId);
                renderWishlist();
                syncWishlistUI();
            } catch (err) {
                console.error('Gagal masuk antrian:', err);
                showToast('Gagal', 'Terjadi kesalahan, coba lagi.');
            } finally {
                joinQueueInFlight = false;
                if (qBtn && document.body.contains(qBtn)) { qBtn.disabled = false; qBtn.classList.remove('opacity-60', 'cursor-not-allowed'); }
            }
        }

        async function leaveQueue(bookId) {
            const userId = getCurrentUserId();
            if (!userId) return;

            try {
                await fetch(`/api/wishlist?user_id=${userId}&book_id=${bookId}`, { method: 'DELETE', headers: getAuthHeaders() });
                const idx = wishlistBooks.findIndex(id => id === bookId);
                if (idx > -1) wishlistBooks.splice(idx, 1);

                showToast('Antrian Dibatalkan', 'Kamu telah keluar dari antrian buku ini.');

                loadQueueInfoForModal(bookId);
                renderWishlist();
                syncWishlistUI();
            } catch (err) {
                console.error('Gagal membatalkan antrian:', err);
                showToast('Gagal', 'Terjadi kesalahan, coba lagi.');
            }
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function renderRecommendations(currentId) {
            const container = document.getElementById('modal-rekomendasi-container');
            if (!container) return;
            
            const current = booksData.find(b => b.id === currentId);
            const currentGenre = (current?.genre || '').trim().toLowerCase();
            const recommendations = booksData
                .filter(b => b.id !== currentId && (b.genre || '').trim().toLowerCase() === currentGenre)
                .slice(0, 3);

            if (recommendations.length === 0) {
                container.innerHTML = `<p class="text-[11px] text-slate-400">Belum ada buku lain dengan genre yang sama.</p>`;
                return;
            }

            container.innerHTML = recommendations.map(book => `
                <div class="w-16 flex-shrink-0 text-center cursor-pointer" onclick="openBookDetail('${book.id}')">
                    <img src="${book.cover}" class="w-full aspect-[3/4] object-cover rounded-lg">
                    <p class="text-[9px] text-slate-400 mt-1 truncate">${book.title}</p>
                </div>
            `).join('');
        }

        // ============================================================
        // FUNGSI WISHLIST
        // ============================================================

        // Cek apakah buku dengan id tertentu sedang dipinjam oleh user yang sedang login,
        // berdasarkan activeLoans (hasil /api/pinjam?user_id=...) yang belum dikembalikan.
        function isBookBorrowedByMe(id) {
            return activeLoans.some(loan => loan.book_id === id && !loan.tanggal_kembali);
        }

        async function toggleWishlist() {
            const book = booksData.find(b => b.id === activeBookId);
            if(!book) return;

            if (book.status !== 'Dipinjam') {
                showToast(currentLang === 'id' ? 'Info' : 'Info', 
                    currentLang === 'id' ? 'Wishlist hanya untuk buku yang sedang dipinjam.' : 'Wishlist is only for borrowed books.');
                return;
            }

            if (isBookBorrowedByMe(activeBookId)) {
                showToast(currentLang === 'id' ? 'Info' : 'Info',
                    currentLang === 'id' ? 'Kamu tidak bisa mewishlist buku yang sedang kamu pinjam sendiri.' : "You can't wishlist a book you're currently borrowing yourself.");
                return;
            }

            const userId = getCurrentUserId();
            if (!userId) {
                showToast(currentLang === 'id' ? 'Info' : 'Info',
                    currentLang === 'id' ? 'Silakan login untuk memakai wishlist.' : 'Please log in to use wishlist.');
                return;
            }

            await setWishlistState(activeBookId, book.title, userId);
        }

        // Menambahkan/menghapus wishlist ke server, lalu update UI.
        // Dipisah supaya dipakai bareng oleh modal & kartu buku.
        async function setWishlistState(bookId, bookTitle, userId) {
            const idx = wishlistBooks.findIndex(id => id === bookId);
            const isId = currentLang === 'id';
            const willRemove = idx > -1;

            try {
                if (willRemove) {
                    await fetch(`/api/wishlist?user_id=${userId}&book_id=${bookId}`, { method: 'DELETE', headers: getAuthHeaders() });
                    wishlistBooks.splice(idx, 1);
                    showToast(isId ? "Dihapus dari Wishlist" : "Removed from Wishlist",
                        isId ? `Buku "${bookTitle}" berhasil dilepas.` : `Book "${bookTitle}" has been removed.`);
                } else {
                    await fetch('/api/wishlist', {
                        method: 'POST',
                        headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
                        body: JSON.stringify({ user_id: userId, book_id: bookId })
                    });
                    wishlistBooks.push(bookId);
                    showToast(isId ? "Ditambahkan ke Wishlist" : "Added to Wishlist",
                        isId ? `Buku "${bookTitle}" berhasil disimpan.` : `Book "${bookTitle}" has been added to your wishlist.`);
                }
            } catch (e) {
                console.error('Gagal mengubah wishlist:', e);
                showToast(isId ? 'Gagal' : 'Failed',
                    isId ? 'Terjadi kesalahan, coba lagi.' : 'Something went wrong, please try again.');
                return;
            }

            renderWishlist();
            syncWishlistUI();
        }

        // Toggle wishlist langsung dari kartu buku di halaman Pustaka (tanpa buka modal)
        async function toggleWishlistFromCard(id) {
            const card = document.querySelector(`.book-card[data-id="${id}"]`);
            if (!card) return;

            const isId = currentLang === 'id';

            if (card.dataset.status !== 'Dipinjam') {
                showToast(isId ? 'Info' : 'Info',
                    isId ? 'Wishlist hanya untuk buku yang sedang dipinjam.' : 'Wishlist is only for borrowed books.');
                return;
            }

            if (isBookBorrowedByMe(id)) {
                showToast(isId ? 'Info' : 'Info',
                    isId ? 'Kamu tidak bisa mewishlist buku yang sedang kamu pinjam sendiri.' : "You can't wishlist a book you're currently borrowing yourself.");
                return;
            }

            const userId = getCurrentUserId();
            if (!userId) {
                showToast(isId ? 'Info' : 'Info',
                    isId ? 'Silakan login untuk memakai wishlist.' : 'Please log in to use wishlist.');
                return;
            }

            await setWishlistState(id, card.dataset.title, userId);
        }

        // Sinkronkan tampilan ikon hati (kartu pustaka & tombol modal) dengan status buku & isi wishlist
        function syncWishlistUI() {
            // Kartu buku di halaman Pustaka
            document.querySelectorAll('#live-library-container > .book-card').forEach(card => {
                const id = card.dataset.id;
                const status = card.dataset.status;
                const btn = card.querySelector('.wishlist-toggle-btn');
                const icon = card.querySelector('.wishlist-toggle-icon');
                if (!btn || !icon) return;

                if (status === 'Dipinjam') {
                    btn.classList.remove('hidden');
                } else {
                    btn.classList.add('hidden');
                }

                if (isBookBorrowedByMe(id)) {
                    // Buku sedang dipinjam oleh user sendiri: tombol wishlist dinonaktifkan.
                    icon.className = 'fa-regular fa-heart wishlist-toggle-icon';
                    btn.classList.remove('bg-rose-500', 'text-white');
                    btn.classList.add('opacity-40', 'cursor-not-allowed');
                    btn.title = currentLang === 'id' ? 'Buku sedang kamu pinjam sendiri' : "You're currently borrowing this book";
                } else if (wishlistBooks.includes(id)) {
                    icon.className = 'fa-solid fa-heart wishlist-toggle-icon';
                    btn.classList.add('bg-rose-500', 'text-white');
                    btn.classList.remove('opacity-40', 'cursor-not-allowed');
                    btn.title = '';
                } else {
                    icon.className = 'fa-regular fa-heart wishlist-toggle-icon';
                    btn.classList.remove('bg-rose-500', 'text-white', 'opacity-40', 'cursor-not-allowed');
                    btn.title = '';
                }
            });

            // Tombol wishlist di modal detail buku (jika sedang terbuka / pernah dibuka)
            const modalBtn = document.getElementById('modal-wishlist-btn');
            const modalIcon = document.getElementById('modal-wishlist-icon');
            if (modalBtn && modalIcon && activeBookId) {
                const activeCard = document.querySelector(`.book-card[data-id="${activeBookId}"]`);
                const status = activeCard ? activeCard.dataset.status : null;

                if (status === 'Dipinjam') {
                    modalBtn.classList.remove('hidden');
                    if (wishlistBooks.includes(activeBookId)) {
                        modalIcon.className = 'fa-solid fa-heart';
                        modalBtn.classList.add('bg-rose-500', 'text-white', 'border-rose-500');
                    } else {
                        modalIcon.className = 'fa-regular fa-heart';
                        modalBtn.classList.remove('bg-rose-500', 'text-white', 'border-rose-500');
                    }
                } else {
                    modalBtn.classList.add('hidden');
                }
            }
        }

        function renderWishlist() {
            const container = document.getElementById('wishlist-container');
            const emptyMsg = document.getElementById('wishlist-empty');
            
            document.querySelectorAll('.wishlist-card-item').forEach(el => el.remove());

            if (wishlistBooks.length === 0) {
                if(emptyMsg) emptyMsg.classList.remove('hidden');
                return;
            }
            if(emptyMsg) emptyMsg.classList.add('hidden');

            wishlistBooks.forEach((id, index) => {
                const book = booksData.find(b => b.id === id);
                const queueDetail = wishlistItems.find(item => item.book_id === id);

                let queueBadge = '';
                let queueInfoLine = '';
                if (queueDetail && queueDetail.queue_position) {
                    queueBadge = `
                        <span class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] font-bold px-2 py-1 rounded-full shadow-sm">
                            Antrian ke-${queueDetail.queue_position}
                        </span>`;
                    const estText = queueDetail.estimated_date
                        ? new Date(queueDetail.estimated_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                        : 'Belum ada perkiraan';
                    queueInfoLine = `<p class="text-[9px] text-amber-600 dark:text-amber-500 mt-1 font-semibold"><i class="fa-regular fa-clock"></i> Perkiraan: ${estText}</p>`;
                }

                if(book) {
                    container.insertAdjacentHTML('beforeend', `
                        <div onclick="openBookDetail(${book.id})" class="wishlist-card-item bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 relative cursor-pointer">
                            ${queueBadge}
                            <button onclick="removeFromWishlist(${index}, event)" class="absolute top-2 right-2 bg-white/80 backdrop-blur-sm dark:bg-slate-900/80 w-6 h-6 rounded-full text-rose-500 flex items-center justify-center shadow-sm text-xs hover:bg-rose-50 transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <img src="${book.cover}" class="w-full aspect-[4/5] object-cover">
                            <div class="p-3">
                                <h4 class="font-bold text-xs line-clamp-1 text-slate-800 dark:text-white">${book.title}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">${book.author}</p>
                                ${queueInfoLine}
                            </div>
                        </div>
                    `);
                }
            });
        }

        async function removeFromWishlist(index, event) {
            event.stopPropagation();

            const bookId = wishlistBooks[index];
            const book = booksData.find(b => b.id === bookId);
            const userId = getCurrentUserId();
            if (!userId || !book) return;

            // setWishlistState akan mendeteksi bookId sudah ada di wishlistBooks,
            // sehingga otomatis melakukan DELETE ke server (bukan cuma di memori).
            await setWishlistState(bookId, book.title, userId);
        }

        // ============================================================
        // FUNGSI REKOMENDASI BUKU
        // ============================================================
        function renderRecommendedBooks() {
            const container = document.getElementById('recommended-books');
            if (!container) return;
            
            const recommended = booksData.slice(0, 4);
            container.innerHTML = recommended.map(book => `
                <div onclick="openBookDetail(${book.id})" class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 cursor-pointer hover:shadow-md transition-shadow">
                    <img src="${book.cover}" class="w-full aspect-[4/5] object-cover">
                    <div class="p-3">
                        <h4 class="font-bold text-xs md:text-sm text-slate-800 dark:text-white line-clamp-1">${book.title}</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">${book.author}</p>
                    </div>
                </div>
            `).join('');
        }

        // ============================================================
        // FUNGSI READING PROGRESS
        // ============================================================
        // FUNGSI SIRKULASI
        // ============================================================
        // Catatan perbaikan: sebelumnya hanya ada SATU box deadline & SATU box
        // status kurir untuk seluruh booking (berbasis localStorage global / .some()),
        // sehingga booking baru selalu menimpa tampilan booking sebelumnya, dan
        // setiap kali loadUserLoans() dipanggil, startCountdown() membuat interval
        // BARU tanpa membersihkan interval lama (menumpuk & reset ke nilai hardcode).
        // Sekarang setiap booking aktif (status Booking / Diantar Kurir) dirender
        // sebagai kartu terpisah berdasarkan id booking-nya masing-masing.
        const circCountdownIntervals = {};
        const expiredNotifiedLoans = new Set();

        function clearAllCircCountdowns() {
            Object.values(circCountdownIntervals).forEach(id => clearInterval(id));
            for (const key in circCountdownIntervals) delete circCountdownIntervals[key];
        }

        function startCountdownForCard(loanId, timerEl, deadlineDate) {
            if (circCountdownIntervals[loanId]) {
                clearInterval(circCountdownIntervals[loanId]);
            }

            const tick = () => {
                const diffMs = deadlineDate.getTime() - Date.now();
                if (diffMs <= 0) {
                    timerEl.textContent = '00:00:00';
                    // Booking sudah kadaluarsa. Penghapusan terjadi di backend.
                    // Cegah loop tak hingga: hanya panggil reload jika countdown sebelumnya pernah berjalan positif.
                    if (!expiredNotifiedLoans.has(loanId)) {
                        expiredNotifiedLoans.add(loanId);
                        if (timerEl.dataset.started) {
                            showToast(
                                'Batas Waktu Habis',
                                'Booking tidak diambil dalam waktu 2 jam dan telah dibatalkan otomatis.'
                            );
                            loadUserLoans();
                        }
                    }
                    clearInterval(circCountdownIntervals[loanId]);
                    delete circCountdownIntervals[loanId];
                    return;
                }
                timerEl.dataset.started = '1';
                const totalSeconds = Math.floor(diffMs / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                timerEl.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            };

            tick();
            if (deadlineDate.getTime() > Date.now()) {
                circCountdownIntervals[loanId] = setInterval(tick, 1000);
            }
        }

        function triggerToast() {
            const toast = document.getElementById('success-toast');
            const title = document.getElementById('modal-title').textContent;
            document.getElementById('toast-title').textContent = 'Pemesanan Berhasil!';
            document.getElementById('toast-desc').textContent = `Terima kasih telah meminjam buku "${title}"! Buku sedang diantarkan oleh kurir ke alamatmu.`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 5000);
            closeDetailModal();
        }

        function showFineDetails() {
            const isId = currentLang === 'id';
            showToast(isId ? "Total Denda Anda" : "Your Total Fine", 
                    isId ? `Total denda yang harus dibayar: Rp ${userFine.toLocaleString('id-ID')}` 
                    : `Total fine to be paid: Rp ${userFine.toLocaleString('id-ID')}`);
        }

        let finesLoaded = false; // true setelah data peminjaman selesai dimuat & denda dihitung

        function showFineWidget() {
            const isId = currentLang === 'id';
            // Data peminjaman belum selesai dimuat: jangan bilang "bersih" dulu.
            if (!finesLoaded) {
                showToast(isId ? "Memuat data denda..." : "Loading fine data...",
                        isId ? "Tunggu sebentar, lalu tekan lagi." : "Please wait a moment, then try again.");
                if (typeof loadUserLoans === 'function') loadUserLoans();
                return;
            }
            if (userFine > 0) {
                updateFineDisplay();
                const widget = document.getElementById('fine-widget');
                if (widget) widget.classList.remove('hidden');
                showToast(isId ? "Total Denda Anda" : "Your Total Fine",
                        (isId ? "Total denda yang harus dibayar: Rp " : "Total fine to be paid: Rp ") + userFine.toLocaleString('id-ID')
                        + (isId ? ". Rincian & pembayaran ada di tab Sirkulasi." : ". Details & payment are in the Circulation tab."));
                switchTab('sirkulasi');
            } else {
                showToast(isId ? "Akun Bersih!" : "Clean Account!",
                        isId ? "Akun Anda bersih dari denda!" : "Your account is free of fines!");
            }
        }

        function updateFineDisplay() {
            const amountEl = document.getElementById('fine-amount-display');
            if (amountEl) {
                amountEl.innerText = `Rp ${userFine.toLocaleString('id-ID')}`;
            }
        }

        // ============================================================
        // FUNGSI FEEDBACK
        // ============================================================
        async function submitFeedback() {
            const userStr = localStorage.getItem('libra_user');
            if (!userStr) {
                alert('Sesi habis. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }
            const user = JSON.parse(userStr);

            const category = document.getElementById('feedback-category').value;
            const message = document.getElementById('feedback-message').value.trim();
            
            if (!message) {
                const isId = currentLang === 'id';
                alert(isId ? 'Harap isi pesan feedback Anda!' : 'Please fill in your feedback message!');
                return;
            }

            const submitBtn = document.querySelector('#feedback-form button[type="button"]') 
                           || document.querySelector('#feedback-form button[onclick="submitFeedback()"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = currentLang === 'id' ? 'Mengirim...' : 'Sending...';
            }
            
            try {
                const response = await fetch('/api/complaints', {
                    method: 'POST',
                    headers: getAuthHeaders({
                        'Content-Type': 'application/json'
                    }),
                    body: JSON.stringify({
                        peminjam_id: user.id,
                        pesan: `[Kategori: ${category}] ${message}`,
                        status: 'pending'
                    })
                });

                if (!response.ok) {
                    const errData = await response.json();
                    throw new Error(errData.message || 'Gagal mengirim masukan');
                }
                
                document.getElementById('feedback-form').reset();
                
                const isId = currentLang === 'id';
                showToast(isId ? "Feedback Terkirim!" : "Feedback Sent!", 
                        isId ? "Terima kasih atas masukan Anda untuk peningkatan Sistem Libra." 
                        : "Thank you for your feedback to help enhance the Libra System.");
            } catch (err) {
                alert(err.message);
                console.error(err);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        }

        // ============================================================
        // FUNGSI TOAST NOTIFICATION
        // ============================================================
        function showToast(title, desc) {
            const toast = document.getElementById('success-toast');
            const titleEl = document.getElementById('toast-title');
            titleEl.textContent = title;
            // Reset ke warna default (emerald) supaya tidak kebawa warna merah
            // dari showToastToko() yang dipanggil sebelumnya.
            titleEl.className = 'text-xs font-semibold text-emerald-400';
            const w = document.getElementById('toast-icon-wrap'), ic = document.getElementById('toast-icon');
            if (w) w.className = 'bg-emerald-500 rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 shadow-md';
            if (ic) ic.className = 'fa-solid fa-check text-white text-sm';
            document.getElementById('toast-desc').textContent = desc;
            toast.classList.remove('hidden');
            clearTimeout(toast._tokoTimer);
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }

        // ============================================================
        // FUNGSI BOOKING PEMINJAMAN (SUPABASE)
        // ============================================================
        let activeLoans = [];

        // ============================================================
        // ORDER KURIR (pengantaran buku & ATK) -- dibaca oleh portal Kurir
        // ============================================================
        function buatOrderKurir({ prefix, id, items, alamat, total, status_bayar, metode_bayar }) {
            let userObj = {};
            try { userObj = JSON.parse(localStorage.getItem('libra_user') || '{}'); } catch (_) {}
            const orderId = id || (prefix + '-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' + Math.floor(1000 + Math.random() * 9000));
            const order = {
                id: orderId,
                order_id: orderId,
                user_id: userObj.id || getCurrentUserId(),
                user_name: userObj.nama_lengkap || userObj.name || 'Mahasiswa',
                user_identity: `${userObj.nim || '-'} (${userObj.fakultas || 'Mahasiswa'})`,
                user_phone: userObj.no_telepon || '-',
                alamat: alamat || userObj.alamat_kirim || 'Alamat Terdaftar Pengguna',
                items: items,
                total_items: items.reduce((sum, it) => sum + (it.qty || 1), 0),
                total: total || 0,
                total_pembayaran: total || 0,
                status: 'Menunggu',
                status_bayar: status_bayar || null,
                metode_bayar: metode_bayar || null,
                waktu_pemesanan: new Date().toISOString(),
                waktu_mulai_pengantaran: null,
                waktu_selesai_pengantaran: null,
                durasi_pengantaran_detik: null,
                total_durasi: null,
                tanggal: new Date().toISOString().slice(0, 10)
            };
            try {
                const list = JSON.parse(localStorage.getItem('libra_courier_orders') || '[]');
                list.unshift(order);
                localStorage.setItem('libra_courier_orders', JSON.stringify(list));
            } catch (e) { console.error('Gagal mencatat order kurir:', e); }
            // Kirim juga ke server supaya kurir di perangkat lain ikut melihat
            fetch('/api/orders', {
                method: 'POST',
                headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
                body: JSON.stringify(order)
            }).catch(() => { /* server belum siap: cukup localStorage */ });
            return order;
        }

        function openBookingForm() {
            // Pengguna yang masih punya denda tidak boleh meminjam (server juga menolak).
            if (userFine > 0) {
                alert(currentLang === 'id'
                    ? 'Kamu masih punya denda sebesar Rp ' + userFine.toLocaleString('id-ID') + '. Lunasi dulu di tab Sirkulasi (kembalikan buku yang terlambat, lalu bayar di Tagihan Denda) sebelum meminjam lagi.'
                    : 'You still have an outstanding fine of Rp ' + userFine.toLocaleString('id-ID') + '. Please settle it in the Circulation tab (return any late book, then pay under Fine Bills) before borrowing again.');
                return;
            }
            // Sembunyikan rekomendasi buku dan tombol default
            document.getElementById('modal-rekomendasi-container').parentElement.classList.add('hidden');
            document.getElementById('booking-default-btn-container').classList.add('hidden');
            
            // Set default tanggal pinjam (hari ini) dan kembali (7 hari lagi)
            const today = new Date();
            const startStr = today.toISOString().split('T')[0];
            const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
            const dueStr = nextWeek.toISOString().split('T')[0];
            
            document.getElementById('loan-start-date').value = startStr;
            document.getElementById('loan-start-date').min = startStr;
            document.getElementById('loan-due-date').value = dueStr;
            document.getElementById('loan-due-date').min = startStr;

            // Batas maksimal peminjaman: 1 bulan dari tanggal pinjam
            updateDueDateLimit(startStr);
            
            // Tampilkan form peminjaman
            document.getElementById('booking-form-section').classList.remove('hidden');
        }

        // Hitung tanggal maksimal (tanggal pinjam + 1 bulan) dan terapkan sebagai batas atas input
        function addOneMonth(dateStr) {
            const d = new Date(dateStr);
            d.setMonth(d.getMonth() + 1);
            return d.toISOString().split('T')[0];
        }

        function updateDueDateLimit(startStr) {
            const maxStr = addOneMonth(startStr);
            const dueInput = document.getElementById('loan-due-date');
            dueInput.max = maxStr;
            // Kalau tanggal kembali yang sudah dipilih melewati batas 1 bulan, tarik mundur otomatis
            if (dueInput.value && dueInput.value > maxStr) {
                dueInput.value = maxStr;
            }
        }

        // Saat admin/pengguna mengubah tanggal pinjam, batas 1 bulan ikut menyesuaikan
        document.getElementById('loan-start-date').addEventListener('change', (e) => {
            const startStr = e.target.value;
            if (!startStr) return;
            document.getElementById('loan-due-date').min = startStr;
            updateDueDateLimit(startStr);
        });

        function cancelBookingForm() {
            // Tampilkan kembali rekomendasi buku dan tombol default
            document.getElementById('modal-rekomendasi-container').parentElement.classList.remove('hidden');
            document.getElementById('booking-default-btn-container').classList.remove('hidden');
            
            // Sembunyikan form peminjaman
            document.getElementById('booking-form-section').classList.add('hidden');
        }

        let bookingInFlight = false;
        async function submitBookingForm() {
            if (bookingInFlight) return;
            const userStr = localStorage.getItem('libra_user');
            if (!userStr) {
                alert('Sesi habis. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }
            const user = JSON.parse(userStr);

            const startVal = document.getElementById('loan-start-date').value;
            const dueVal = document.getElementById('loan-due-date').value;
            const statusVal = document.querySelector('input[name="pickup-option"]:checked').value;

            if (!startVal || !dueVal) {
                alert('Mohon isi tanggal pinjam dan batas pengembalian!');
                return;
            }

            if (new Date(dueVal) <= new Date(startVal)) {
                alert('Batas pengembalian harus setelah tanggal pinjam!');
                return;
            }

            const maxDueVal = addOneMonth(startVal);
            if (dueVal > maxDueVal) {
                alert('Durasi peminjaman maksimal adalah 1 bulan dari tanggal pinjam. Silakan pilih tanggal pengembalian paling lambat ' + formatDateIndo(maxDueVal) + '.');
                return;
            }

            if (isBookBorrowedByMe(activeBookId)) {
                alert('Kamu sudah membooking buku ini. Cek tab Sirkulasi.');
                return;
            }

            bookingInFlight = true;
            const confirmBtn = document.querySelector('#booking-form-section button[onclick="submitBookingForm()"]');
            const originalText = confirmBtn.textContent;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Memproses...';

            try {
                const response = await fetch('/api/pinjam', {
                    method: 'POST',
                    headers: getAuthHeaders({
                        'Content-Type': 'application/json'
                    }),
                    body: JSON.stringify({
                        user_id: user.id,
                        book_id: activeBookId,
                        tanggal_pinjam: startVal,
                        tenggat_waktu: dueVal,
                        status: statusVal
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    // Laravel mengembalikan { message, errors: { field: [pesan] } } saat validasi gagal (422)
                    const firstFieldError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                    throw new Error(firstFieldError || data.message || 'Gagal menyimpan peminjaman');
                }

                // Jika memilih Jasa Pengantaran, buatkan order untuk kurir
                if (statusVal === 'Booking Dikonfirmasi Admin') {
                    const bookTitle = document.getElementById('modal-book-title')?.textContent || 'Buku Pustaka';
                    buatOrderKurir({
                        prefix: 'BK',
                        id: data.loan && data.loan.id ? String(data.loan.id) : undefined,
                        alamat: user.alamat_kirim || null,
                        items: [{ id: activeBookId, nama: bookTitle, tipe: 'Buku', harga: 0, qty: 1, subtotal: 0 }],
                        total: 0
                    });
                }

                // Sukses
                closeDetailModal();
                showToast('Peminjaman Berhasil!', `Buku berhasil disimpan ke sirkulasi Anda.`);
                
                // Refresh data peminjaman
                await loadUserLoans();
                // Pindah ke tab sirkulasi
                switchTab('sirkulasi');
            } catch (err) {
                alert(err.message);
                console.error(err);
            } finally {
                bookingInFlight = false;
                confirmBtn.disabled = false;
                confirmBtn.textContent = originalText;
            }
        }

        let atkOrders = []; // Pesanan Toko ATK milik user yang sedang login (dimuat bareng peminjaman buku)

        async function loadAtkOrders() {
            const userId = getCurrentUserId();
            if (!userId) return;

            try {
                const response = await fetch(`/api/pembelian-atk?user_id=${encodeURIComponent(userId)}`, { headers: getAuthHeaders() });
                if (!response.ok) throw new Error('Gagal mengambil data pesanan ATK');
                const data = await response.json();
                atkOrders = Array.isArray(data) ? data : (data.data || []);
            } catch (err) {
                console.error('Error loading ATK orders:', err);
                atkOrders = [];
            }
        }

        async function loadUserLoans() {
            const userId = getCurrentUserId();
            if (!userId) return;

            try {
                const response = await fetch(`/api/pinjam?user_id=${encodeURIComponent(userId)}`, { headers: getAuthHeaders() });
                if (!response.ok) throw new Error('Gagal mengambil data peminjaman');
                const data = await response.json();
                activeLoans = Array.isArray(data) ? data : (data.data || []);

                // Pesanan ATK dimuat bersamaan supaya kartu status di Sirkulasi
                // (buku + ATK) selalu dirender dari data yang sinkron.
                await loadAtkOrders();

                renderActiveLoans();
                calculateFines();
                updateCourierStatusVisibility();
                await updateBookStatuses();
                syncWishlistUI();
            } catch (err) {
                console.error('Error loading loans:', err);
            }
        }

        async function updateBookStatuses() {
            try {
                const response = await fetch('/api/pinjam-aktif', { headers: getAuthHeaders() });
                if (!response.ok) throw new Error('Gagal mengambil semua status peminjaman');

                // Daftar ISBN buku yang sedang dipinjam oleh siapa pun
                const borrowedBookISBNs = await response.json();

                // Update status di card buku halaman pustaka
                document.querySelectorAll('#live-library-container > .book-card').forEach(card => {
                    const isbn = card.dataset.id;
                    const statusBadge = card.querySelector('.status-badge');
                    
                    if (borrowedBookISBNs.includes(isbn)) {
                        card.dataset.status = 'Dipinjam';
                        if (statusBadge) {
                            statusBadge.textContent = currentLang === 'id' ? 'Dipinjam' : 'On Loan';
                            statusBadge.className = 'status-badge inline-block px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-[9px] font-semibold rounded-full';
                        }
                    } else {
                        card.dataset.status = 'Tersedia';
                        if (statusBadge) {
                            statusBadge.textContent = currentLang === 'id' ? 'Tersedia' : 'Available';
                            statusBadge.className = 'status-badge inline-block px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[9px] font-semibold rounded-full';
                        }
                    }
                });

                // Sinkronkan ulang data buku di Javascript
                booksData = getBooksFromDOM();

                // Sinkronkan tampilan tombol wishlist dengan status terbaru
                syncWishlistUI();
            } catch (err) {
                console.error('Error updating book statuses:', err);
            }
        }

        // ============================================================
        // PEMBAYARAN DENDA (SIMULASI) -- hanya untuk buku yang sudah dikembalikan
        // ============================================================
        function getUnpaidReturnedFines() {
            return (activeLoans || []).filter(l =>
                l.tanggal_kembali && Number(l.denda) > 0 && !l.denda_lunas_at
            );
        }

        function renderUnpaidFines() {
            const card = document.getElementById('unpaid-fines-card');
            const list = document.getElementById('unpaid-fines-list');
            if (!card || !list) return;
            const isId = currentLang === 'id';
            const items = getUnpaidReturnedFines();
            if (items.length === 0) {
                card.classList.add('hidden');
                list.innerHTML = '';
                return;
            }
            list.innerHTML = items.map(l => `
                <div class="p-3.5 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 rounded-xl flex justify-between items-center gap-3 text-xs">
                    <div class="min-w-0">
                        <h5 class="font-bold text-slate-800 dark:text-white truncate">${l.book_title || 'Buku'}</h5>
                        <p class="text-slate-400 mt-0.5">${isId ? 'Dikembalikan' : 'Returned'}: ${formatDateIndo(l.tanggal_kembali)}</p>
                        <span class="font-mono font-bold text-rose-500 text-sm">Rp ${Number(l.denda).toLocaleString('id-ID')}</span>
                    </div>
                    <button onclick="openPayFineModal('${l.id}')" class="shrink-0 px-3 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">${isId ? 'Bayar Denda' : 'Pay Fine'}</button>
                </div>
            `).join('');
            card.classList.remove('hidden');
        }

        let payFineLoanId = null;
        let payFineInFlight = false;

        function openPayFineModal(loanId) {
            const loan = (activeLoans || []).find(l => String(l.id) === String(loanId));
            if (!loan) return;
            const isId = currentLang === 'id';
            payFineLoanId = loan.id;
            document.getElementById('pay-fine-book').textContent = loan.book_title || 'Buku';
            document.getElementById('pay-fine-detail').textContent =
                (isId ? 'Keterlambatan pengembalian, dikembalikan ' : 'Late return, returned ') + formatDateIndo(loan.tanggal_kembali);
            document.getElementById('pay-fine-amount').textContent = 'Rp ' + Number(loan.denda).toLocaleString('id-ID');
            document.getElementById('pay-fine-modal').classList.remove('hidden');
        }

        function closePayFineModal() {
            if (payFineInFlight) return;
            document.getElementById('pay-fine-modal').classList.add('hidden');
            payFineLoanId = null;
        }

        async function submitPayFine() {
            if (payFineInFlight || !payFineLoanId) return;
            const isId = currentLang === 'id';
            const userStr = localStorage.getItem('libra_user');
            if (!userStr) {
                alert('Sesi habis. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }
            const user = JSON.parse(userStr);
            const method = (document.querySelector('input[name="pay-fine-method"]:checked') || {}).value || 'QRIS';
            const btn = document.getElementById('pay-fine-submit');
            const original = btn.textContent;
            payFineInFlight = true;
            btn.disabled = true;
            btn.textContent = isId ? 'Memproses pembayaran...' : 'Processing payment...';

            try {
                // Jeda singkat supaya terasa seperti proses pembayaran sungguhan (simulasi).
                await new Promise(r => setTimeout(r, 1800));
                const res = await fetch(`/api/pinjam/${encodeURIComponent(payFineLoanId)}/bayar-denda`, {
                    method: 'POST',
                    headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
                    body: JSON.stringify({ user_id: user.id, metode: method })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Pembayaran gagal');

                payFineInFlight = false;
                closePayFineModal();
                showToast(isId ? 'Denda Lunas!' : 'Fine Paid!',
                    isId ? 'Pembayaran denda berhasil (simulasi). Kamu bisa meminjam buku lagi.'
                         : 'Fine payment successful (simulation). You can borrow books again.');
                await loadUserLoans();
            } catch (err) {
                alert(err.message);
            } finally {
                payFineInFlight = false;
                btn.disabled = false;
                btn.textContent = original;
            }
        }

        function calculateFines() {
            let totalFine = 0;
            const overdueItems = [];

            activeLoans.forEach(loan => {
                if (!loan.tanggal_kembali && loan.tenggat_waktu) {
                    const dueDate = new Date(loan.tenggat_waktu);
                    const today = new Date();
                    dueDate.setHours(0, 0, 0, 0);
                    today.setHours(0, 0, 0, 0);

                    const diffTime = today - dueDate;
                    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

                    if (diffDays > 0) {
                        const fine = diffDays * 2000;
                        totalFine += fine;
                        overdueItems.push({
                            title: loan.book_title || 'Buku',
                            days: diffDays,
                            amount: fine
                        });
                    }
                }
            });

            // Total denda = denda berjalan (buku terlambat) + denda belum lunas dari buku yang sudah dikembalikan
            finesLoaded = true;
            userFine = totalFine + getUnpaidReturnedFines().reduce((sum, l) => sum + Number(l.denda), 0);
            renderUnpaidFines();
            
            // Render rincian denda berjalan
            const card = document.getElementById('fine-breakdown-card');
            const list = document.getElementById('fine-breakdown-list');
            if (card && list) {
                if (totalFine > 0) {
                    list.innerHTML = overdueItems.map(item => `
                        <div class="p-3.5 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 rounded-xl flex justify-between items-center text-xs">
                            <div>
                                <h5 class="font-bold text-slate-800 dark:text-white">Keterlambatan Pengembalian: "${item.title}"</h5>
                                <p class="text-slate-400 mt-0.5">Terlambat ${item.days} hari × Rp 2.000 / hari</p>
                            </div>
                            <span class="font-mono font-bold text-rose-500 text-sm">Rp ${item.amount.toLocaleString('id-ID')}</span>
                        </div>
                    `).join('') + `
                        <p class="text-[11px] text-slate-400 leading-normal italic" data-i18n="circ-fine-note">*Kembalikan bukunya ke perpustakaan, lalu bayar dendanya di bagian Tagihan Denda untuk mengaktifkan kembali hak peminjaman Anda.</p>
                    `;
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            }

            // Tampilkan/sembunyikan floating widget denda di kiri bawah
            const widget = document.getElementById('fine-widget');
            if (widget) {
                widget.classList.toggle('hidden', userFine === 0);
            }
            updateFineDisplay();
        }

        function formatDateIndo(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }

        // Perpanjang peminjaman: maksimal 3 hari, hanya sekali (dicek ulang di server).
        async function extendLoan(loanId, btn) {
            const isId = currentLang === 'id';
            const userStr = localStorage.getItem('libra_user');
            if (!userStr) { window.location.href = '/login'; return; }
            const user = JSON.parse(userStr);
            const sel = document.getElementById('ext-days-' + loanId);
            const hari = sel ? parseInt(sel.value, 10) : 3;

            const ok = confirm(isId
                ? `Perpanjang peminjaman ${hari} hari? Perpanjangan hanya bisa dilakukan SEKALI dan tidak bisa dibatalkan.`
                : `Extend this loan by ${hari} day(s)? You can only extend ONCE and it cannot be undone.`);
            if (!ok) return;

            const original = btn.textContent;
            btn.disabled = true;
            btn.textContent = isId ? 'Memproses...' : 'Processing...';
            try {
                const res = await fetch(`/api/pinjam/${encodeURIComponent(loanId)}/perpanjang`, {
                    method: 'POST',
                    headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
                    body: JSON.stringify({ user_id: user.id, hari })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || (isId ? 'Perpanjangan gagal.' : 'Extension failed.'));
                showToast(isId ? 'Perpanjangan Berhasil' : 'Loan Extended', data.message);
                await loadUserLoans();
            } catch (err) {
                alert(err.message);
                btn.disabled = false;
                btn.textContent = original;
            }
        }

        function renderActiveLoans() {
            const tbody = document.getElementById('active-loans-tbody');
            if (!tbody) return;

            // "Peminjaman Aktif" hanya untuk yang BELUM dikembalikan.
            // activeLoans berisi SELURUH riwayat peminjaman user (termasuk yang
            // sudah dikembalikan), jadi wajib disaring dulu di sini berdasarkan
            // tanggal_kembali sebelum dirender ke tabel ini.
            const stillActiveLoans = activeLoans.filter(loan => !loan.tanggal_kembali && loan.status !== 'Ditolak');

            if (stillActiveLoans.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-8 text-slate-400 italic">
                            Belum ada peminjaman aktif.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = stillActiveLoans.map(loan => {
                const isOverdue = new Date(loan.tenggat_waktu) < new Date() && !loan.tanggal_kembali;
                const dateClass = isOverdue ? 'text-rose-500 font-semibold' : 'text-slate-500 dark:text-slate-400';
                const dateSuffix = isOverdue ? ' (Terlambat)' : '';
                const isId = currentLang === 'id';
                const sudahDiperpanjang = !!loan.diperpanjang_at;
                const bisaPerpanjang = String(loan.status || '').toLowerCase().trim() === 'dipinjam'
                    && !sudahDiperpanjang && !isOverdue;
                let aksi = '<span class="text-slate-300">-</span>';
                if (sudahDiperpanjang) {
                    aksi = `<span class="text-[11px] text-slate-400">${isId ? 'Sudah diperpanjang' : 'Extended'} (+${Number(loan.perpanjangan_hari) || 0} ${isId ? 'hari' : 'days'})</span>`;
                } else if (bisaPerpanjang) {
                    aksi = `
                        <div class="flex items-center gap-1.5">
                            <select id="ext-days-${loan.id}" class="text-[11px] rounded-md border border-[#E5E2DA] dark:border-[#262620] bg-transparent px-1.5 py-1">
                                <option value="1">1 ${isId ? 'hari' : 'day'}</option>
                                <option value="2">2 ${isId ? 'hari' : 'days'}</option>
                                <option value="3" selected>3 ${isId ? 'hari' : 'days'}</option>
                            </select>
                            <button type="button" onclick="extendLoan('${loan.id}', this)"
                                class="text-[11px] font-semibold px-2.5 py-1 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
                                ${isId ? 'Perpanjang' : 'Extend'}
                            </button>
                        </div>`;
                }
                return `
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                        <td class="py-3.5 font-semibold text-slate-800 dark:text-white pr-4">
                            ${loan.book_title || 'Buku'}
                        </td>
                        <td class="py-3.5 text-slate-500 dark:text-slate-400">
                            ${formatDateIndo(loan.tanggal_pinjam)}
                        </td>
                        <td class="py-3.5 ${dateClass}">
                            ${formatDateIndo(loan.tenggat_waktu)}${dateSuffix}
                        </td>
                        <td class="py-3.5">${aksi}</td>
                    </tr>
                `;
            }).join('');
        }

        // ============================================================
        // STATUS PENGIRIMAN KURIR - SUMBER KEBENARAN
        // ============================================================
        // Daftar status di bawah ini HARUS selalu sama persis (termasuk huruf
        // besar/kecil) dengan opsi <select id="peminjam-status"> di halaman
        // admin (public/admin-dashboard.html), supaya saat admin mengubah
        // status sirkulasi, tampilan tracking kurir di sisi mahasiswa ikut
        // berubah otomatis (bukan lagi teks statis/dummy).
        //
        // Urutan array = urutan tahapan pengiriman kurir dari awal ke akhir.
        const COURIER_FLOW_STEPS = [
            {
                key: 'Booking Dikonfirmasi Admin',
                titleId: 'Booking Dikonfirmasi Admin', titleEn: 'Booking Confirmed by Admin',
                descId: 'Admin telah menyetujui permintaan peminjaman Anda.',
                descEn: 'The admin has approved your loan request.'
            },
            {
                key: 'Sedang Dikemas',
                titleId: 'Sedang Dikemas', titleEn: 'Being Packaged',
                descId: 'Buku Anda sedang dikemas oleh petugas perpustakaan.',
                descEn: 'Your book is being packaged by library staff.'
            },
            {
                key: 'Menunggu Kurir',
                titleId: 'Menunggu Kurir', titleEn: 'Waiting for Courier',
                descId: 'Buku Anda telah dikemas dan sedang menunggu diambil oleh kurir untuk diantar.',
                descEn: 'Your book has been packaged and is waiting to be picked up by the courier for delivery.'
            },
            {
                key: 'Sedang Diantar Kurir',
                titleId: 'Sedang Diantar Kurir', titleEn: 'Out for Delivery',
                descId: 'Buku sedang dalam perjalanan diantar kurir menuju Anda.',
                descEn: 'The book is currently on its way, being delivered by courier.'
            },
        ];

        // Alias untuk kompatibilitas data lama/legacy yang mungkin masih
        // memakai istilah dummy sebelumnya, supaya tidak tampil kosong.
        const COURIER_STATUS_ALIASES = {
            'diantar kurir': 'Sedang Diantar Kurir',
            'sedang diantar': 'Sedang Diantar Kurir',
            'sedang dikirim': 'Sedang Diantar Kurir'
        };

        function resolveCourierStatusKey(rawStatus) {
            const s = String(rawStatus || '').toLowerCase().trim();
            return COURIER_STATUS_ALIASES[s] || rawStatus;
        }

        function isCourierTrackedStatus(rawStatus) {
            const key = String(resolveCourierStatusKey(rawStatus) || '').toLowerCase().trim();
            return COURIER_FLOW_STEPS.some(step => step.key.toLowerCase().trim() === key);
        }

        function updateCourierStatusVisibility() {
            const container = document.getElementById('circ-status-container');
            const orderTpl = document.getElementById('circ-order-template');
            if (!container || !orderTpl) return;

            // Bersihkan semua kartu & interval lama sebelum render ulang,
            // supaya tidak ada kartu/interval "bekas" yang menumpuk.
            clearAllCircCountdowns();
            container.innerHTML = '';

            // --- Peminjaman buku yang masih perlu perhatian (belum dikembalikan) ---
            const pendingLoans = (activeLoans || []).filter(l => {
                const st = String(l.status || '').toLowerCase().trim();
                return !l.tanggal_kembali && (st === 'booking' || isCourierTrackedStatus(l.status));
            });
            pendingLoans.forEach(loan => {
                container.appendChild(buildOrderCard({
                    uid: 'buku-' + loan.id,
                    title: loan.book_title || 'Buku',
                    status: loan.status,
                    createdAt: loan.created_at,
                    type: 'buku',
                }, orderTpl));
            });

            // --- Pesanan Toko ATK yang masih berjalan (belum selesai/dibatalkan) ---
            const pendingAtkOrders = (atkOrders || []).filter(o => {
                const s = String(o.status || '').toLowerCase().trim();
                return s !== 'selesai' && s !== 'dibatalkan';
            });
            pendingAtkOrders.forEach(order => {
                let items = order.items || [];
                if (typeof items === 'string') {
                    try { items = JSON.parse(items); } catch (_) { items = []; }
                }
                const judul = Array.isArray(items) && items.length > 0
                    ? items.map(i => i.nama).filter(Boolean).join(', ')
                    : 'Pesanan ATK';
                container.appendChild(buildOrderCard({
                    uid: 'atk-' + order.id,
                    title: judul,
                    status: order.status,
                    createdAt: order.created_at,
                    type: 'atk',
                }, orderTpl));
            });

            // Terapkan bahasa aktif ke kartu-kartu yang baru saja di-clone.
            if (typeof applyTranslations === 'function') applyTranslations();
        }

        /**
         * Bangun SATU baris accordion pesanan (dipakai utk peminjaman buku
         * MAUPUN pesanan Toko ATK -- keduanya pakai kosakata status yang sama).
         * data: { uid, title, status, createdAt, type: 'buku'|'atk' }
         */
        function buildOrderCard(data, template) {
            const node = template.content.firstElementChild.cloneNode(true);

            const iconWrap = node.querySelector('.circ-order-icon-wrap');
            const iconEl = node.querySelector('.circ-order-icon-el');
            const titleEl = node.querySelector('.circ-order-title-el');
            const statusLabelEl = node.querySelector('.circ-order-status-label-el');
            const rightEl = node.querySelector('.circ-order-right-el');
            const detailEl = node.querySelector('.circ-order-detail-el');
            const toggleBtn = node.querySelector('.circ-order-toggle-btn');
            const chevronEl = node.querySelector('.circ-order-chevron-el');

            titleEl.textContent = data.title;

            const isPickup = String(data.status || '').toLowerCase().trim() === 'booking';
            const isCourier = !isPickup && isCourierTrackedStatus(data.status);

            if (isPickup) {
                // --- Menunggu diambil sendiri: badge kuning + countdown 2 jam ---
                iconWrap.className = 'circ-order-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-400';
                iconEl.className = 'circ-order-icon-el text-base fa-regular fa-clock';
                statusLabelEl.textContent = data.type === 'atk' ? 'Pesanan ATK \u2014 ambil sendiri' : 'Menunggu diambil sendiri';

                const createdAt = data.createdAt ? new Date(data.createdAt) : new Date();
                const deadlineDate = new Date(createdAt.getTime() + 2 * 60 * 60 * 1000);
                // Countdown tetap berjalan walau kartunya collapsed -- elemennya
                // tetap ada di DOM, cuma kontainer detailnya yang disembunyikan.
                startCountdownForCard(data.uid, rightEl, deadlineDate);

                detailEl.innerHTML = `
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        ${data.type === 'atk' ? 'Ambil pesananmu' : 'Ambil buku'} langsung di meja sirkulasi perpustakaan sebelum batas waktu di atas habis.
                    </p>`;
            } else if (isCourier) {
                // --- Sedang dalam proses pengiriman kurir: badge biru + nama tahap saat ini ---
                iconWrap.className = 'circ-order-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-400';
                iconEl.className = 'circ-order-icon-el text-base fa-solid fa-truck';

                const isId = currentLang === 'id';
                const statusKey = String(resolveCourierStatusKey(data.status) || '').toLowerCase().trim();
                const stepInfo = COURIER_FLOW_STEPS.find(step => step.key.toLowerCase().trim() === statusKey);
                statusLabelEl.textContent = stepInfo ? (isId ? stepInfo.titleId : stepInfo.titleEn) : data.status;
                rightEl.remove(); // tidak ada countdown numerik utk kurir, cukup label status di atas

                detailEl.innerHTML = buildCourierTimelineHtml(data.status);
            } else {
                // Status tak dikenali -- tampilkan apa adanya supaya tidak hilang diam-diam.
                iconWrap.className = 'circ-order-icon-wrap w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-500';
                iconEl.className = 'circ-order-icon-el text-base fa-solid fa-circle-question';
                statusLabelEl.textContent = data.status || '-';
                rightEl.remove();
            }

            toggleBtn.addEventListener('click', () => {
                const willOpen = detailEl.classList.contains('hidden');
                detailEl.classList.toggle('hidden', !willOpen);
                chevronEl.style.transform = willOpen ? 'rotate(180deg)' : '';
            });

            return node;
        }

        /** Bangun HTML timeline 4-langkah kurir (dipakai buku maupun pesanan ATK). */
        function buildCourierTimelineHtml(rawStatus) {
            const isId = currentLang === 'id';
            const statusKey = String(resolveCourierStatusKey(rawStatus) || '').toLowerCase().trim();
            const currentIndex = COURIER_FLOW_STEPS.findIndex(step => step.key.toLowerCase().trim() === statusKey);

            const stepsHtml = COURIER_FLOW_STEPS.map((step, idx) => {
                // done   : tahap sudah dilewati (lebih awal dari status saat ini)
                // active : tahap yang SEDANG berlangsung sekarang (sesuai status di database)
                // idle   : tahap yang belum tercapai
                const isDone = currentIndex > idx;
                const isActive = currentIndex === idx;
                const isLast = idx === COURIER_FLOW_STEPS.length - 1;

                const dotClass = isDone
                    ? 'bg-emerald-500'
                    : (isActive ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-700');

                const dotInner = isDone
                    ? '<i class="fa-solid fa-check text-[8px] text-white"></i>'
                    : (isActive ? '<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>' : '');

                const titleClass = isActive
                    ? 'text-sm font-bold text-blue-600 dark:text-blue-400'
                    : (isDone
                        ? 'text-sm font-semibold text-slate-600 dark:text-slate-400'
                        : 'text-sm font-semibold text-slate-400 dark:text-slate-600');

                const title = isId ? step.titleId : step.titleEn;
                const desc = isId ? step.descId : step.descEn;
                const marginClass = isLast ? '' : 'mb-6';

                return `
                    <div class="relative flex items-start gap-4 ${marginClass}">
                        <div class="z-10 w-5 h-5 rounded-full ${dotClass} flex items-center justify-center border-4 border-white dark:border-slate-900 flex-shrink-0">
                            ${dotInner}
                        </div>
                        <div>
                            <h5 class="${titleClass}">${title}</h5>
                            <p class="text-xs text-slate-400 mt-0.5">${desc}</p>
                        </div>
                    </div>
                `;
            }).join('');

            return `<div class="relative ml-2"><div class="absolute left-2.5 top-2 bottom-4 w-0.5 bg-slate-200 dark:bg-slate-800"></div>${stepsHtml}</div>`;
        }

        // ============================================================
        // EVENT LISTENER
        // ============================================================
        document.addEventListener('click', (e) => {
            const notifPanel = document.getElementById('notif-panel');
            if (notifPanel && !notifPanel.contains(e.target)) {
                notifPanel.classList.add('hidden');
            }
        });

        // Tutup modal dengan ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDetailModal();
            }
        });

        // ============================================================
        // TOKO ATK - PRODUK & KERANJANG
        // ============================================================
        let produkTokoData = [];       // Semua produk dari API
        let produkTokoFiltered = [];   // Produk setelah filter
        let keranjang = [];            // Item keranjang: { id, kode, nama, harga, stok, qty, kategori }
        let produkDipilih = null;      // Produk yang sedang dibuka di modal beli

        // Mapping icon berdasarkan kategori produk
        function getIconProduk(kategori) {
            const map = {
                'Alat Tulis': 'fa-pen',
                'Buku': 'fa-book',
                'Perlengkapan': 'fa-box',
            };
            return map[kategori] || 'fa-store';
        }

        function formatRupiah(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID');
        }

        // Tampilkan/sembunyikan input alamat sesuai metode pengiriman yang dipilih di keranjang
        function toggleAtkAlamatField() {
            const selected = document.querySelector('input[name="atk-delivery-option"]:checked');
            const wrap = document.getElementById('atk-alamat-wrap');
            if (!wrap) return;
            wrap.classList.toggle('hidden', !selected || selected.value !== 'antar');
            updateAtkPayHint();
        }

        // Keterangan di bawah pilihan pembayaran: menyesuaikan metode pengambilan
        function updateAtkPayHint() {
            const hint = document.getElementById('atk-pay-hint');
            if (!hint) return;
            const pay = document.querySelector('input[name="atk-pay-option"]:checked');
            const del = document.querySelector('input[name="atk-delivery-option"]:checked');
            if (pay && pay.value === 'online') {
                hint.textContent = 'Bayar online sekarang (simulasi, tanpa uang sungguhan)';
            } else if (del && del.value === 'antar') {
                hint.textContent = 'Bayar ke kurir saat barang tiba di rumah';
            } else {
                hint.textContent = 'Bayar saat mengambil di meja sirkulasi';
            }
        }

        // ============================================================
        // PEMBAYARAN PESANAN ATK (SIMULASI)
        // ============================================================
        let payAtkOrder = null;
        let payAtkInFlight = false;

        function openPayAtkModal(orderOrId) {
            const order = (orderOrId && typeof orderOrId === 'object')
                ? orderOrId
                : (riwayatAtkData || []).find(o => String(o.id) === String(orderOrId));
            if (!order) return;
            payAtkOrder = order;
            const items = order.items || [];
            document.getElementById('pay-atk-items').textContent =
                items.map(i => `${i.nama} x${i.qty}`).join(', ') || 'Pesanan Toko ATK';
            document.getElementById('pay-atk-amount').textContent = formatRupiah(order.total || 0);
            document.getElementById('pay-atk-modal').classList.remove('hidden');
        }

        function closePayAtkModal() {
            if (payAtkInFlight) return;
            document.getElementById('pay-atk-modal').classList.add('hidden');
            payAtkOrder = null;
        }

        async function submitPayAtk() {
            if (payAtkInFlight || !payAtkOrder) return;
            const isId = currentLang === 'id';
            const userId = getCurrentUserId();
            if (!userId) {
                alert('Sesi habis. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }
            const method = (document.querySelector('input[name="pay-atk-method"]:checked') || {}).value || 'QRIS';
            const btn = document.getElementById('pay-atk-submit');
            const original = btn.textContent;
            payAtkInFlight = true;
            btn.disabled = true;
            btn.textContent = isId ? 'Memproses pembayaran...' : 'Processing payment...';
            try {
                await new Promise(r => setTimeout(r, 1800));   // jeda simulasi
                const res = await fetch(`/api/pembelian-atk/${encodeURIComponent(payAtkOrder.id)}/bayar`, {
                    method: 'POST',
                    headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
                    body: JSON.stringify({ user_id: userId, metode: method })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false) throw new Error(data.message || 'Pembayaran gagal');

                payAtkInFlight = false;
                closePayAtkModal();
                showToast(isId ? 'Pembayaran Berhasil!' : 'Payment Successful!',
                    isId ? 'Pesanan ATK kamu sudah lunas (simulasi).' : 'Your ATK order is paid (simulation).');
                loadRiwayatPembelian();
            } catch (err) {
                alert(err.message);
            } finally {
                payAtkInFlight = false;
                btn.disabled = false;
                btn.textContent = original;
            }
        }

        // Ambil data produk dari API (/api/produk-atk); jika gagal tampil pesan error, bukan data palsu
        async function loadProdukToko() {
            const loading = document.getElementById('toko-loading');
            const empty = document.getElementById('toko-empty');
            const grid = document.getElementById('toko-produk-grid');
            if (loading) loading.classList.remove('hidden');
            if (empty) empty.classList.add('hidden');
            if (grid) grid.classList.add('hidden');

            const errBox = document.getElementById('toko-error');
            if (errBox) errBox.classList.add('hidden');
            try {
                const res = await fetch('/api/produk-atk', { headers: getAuthHeaders() });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                produkTokoData = Array.isArray(data) ? data : (data.data || []);
            } catch (e) {
                console.error('Gagal memuat produk ATK:', e);
                produkTokoData = [];
                if (loading) loading.classList.add('hidden');
                if (grid) grid.classList.add('hidden');
                if (empty) empty.classList.add('hidden');
                if (errBox) errBox.classList.remove('hidden');
                return;
            }

            populateKategoriFilter();
            filterProduk(); // langsung terapkan pencarian & kategori yang sedang aktif
        }

        // Isi dropdown filter kategori dari data produk
        function populateKategoriFilter() {
            const select = document.getElementById('toko-kategori');
            if (!select) return;
            const previousValue = select.value; // simpan pilihan kategori yang sedang aktif
            const categories = [...new Set(produkTokoData.map(p => p.kategori).filter(Boolean))];
            // Reset opsi
            select.innerHTML = '<option value="">Semua Kategori</option>';
            categories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                select.appendChild(opt);
            });
            // Kembalikan pilihan sebelumnya kalau kategorinya masih tersedia
            if (categories.includes(previousValue)) {
                select.value = previousValue;
            }
        }

        // Render grid produk
        function renderProdukGrid(data) {
            const loading = document.getElementById('toko-loading');
            const empty = document.getElementById('toko-empty');
            const grid = document.getElementById('toko-produk-grid');
            if (!grid) return;

            if (loading) loading.classList.add('hidden');

            if (!data || data.length === 0) {
                if (empty) empty.classList.remove('hidden');
                grid.classList.add('hidden');
                return;
            }

            if (empty) empty.classList.add('hidden');
            grid.classList.remove('hidden');

            grid.innerHTML = data.map(p => {
                const icon = getIconProduk(p.kategori);
                const habis = !p.stok || p.stok <= 0;
                const harga = formatRupiah(p.harga);
                return `
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col transition-all hover:shadow-md hover:-translate-y-0.5">
                    ${p.gambar
                        ? `<div class="aspect-[4/3] bg-slate-100 dark:bg-slate-800 overflow-hidden relative">
                               <img src="${p.gambar}" alt="${p.nama}" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                               <div class="hidden absolute inset-0 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800/40 dark:to-slate-900/30 p-4 flex items-center justify-center">
                                   <i class="fa-solid ${icon} text-3xl text-slate-500 dark:text-slate-400"></i>
                               </div>
                           </div>`
                        : `<div class="bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800/40 dark:to-slate-900/30 p-4 flex items-center justify-center aspect-[4/3]">
                               <i class="fa-solid ${icon} text-3xl text-slate-500 dark:text-slate-400"></i>
                           </div>`}
                    <div class="p-3 flex flex-col gap-1.5 flex-1">
                        <span class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">${p.kategori || '-'}</span>
                        <p class="text-xs font-bold text-slate-800 dark:text-white leading-tight line-clamp-2">${p.nama}</p>
                        <p class="text-[10px] text-slate-400">${p.kode}</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-auto">${harga}</p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-[9px] ${habis ? 'text-red-400' : 'text-emerald-500'} font-semibold">
                                ${habis ? 'Stok Habis' : 'Stok: ' + p.stok}
                            </span>
                        </div>
                        <button onclick="openBeli('${p.id}')"
                            class="mt-2 w-full py-1.5 text-xs font-bold rounded-xl transition-all ${habis ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed' : 'bg-slate-800 hover:bg-slate-900 text-white shadow-sm shadow-slate-900/20'}"
                            ${habis ? 'disabled' : ''}>
                            <i class="fa-solid fa-cart-plus mr-1"></i>${habis ? 'Habis' : 'Beli'}
                        </button>
                    </div>
                </div>`;
            }).join('');
        }

        // Filter produk berdasarkan pencarian & kategori
        function filterProduk() {
            const q = (document.getElementById('toko-search')?.value || '').toLowerCase();
            const kat = document.getElementById('toko-kategori')?.value || '';
            produkTokoFiltered = produkTokoData.filter(p => {
                const matchQ = !q || (p.nama || '').toLowerCase().includes(q) || (p.kode || '').toLowerCase().includes(q);
                const matchKat = !kat || p.kategori === kat;
                return matchQ && matchKat;
            });
            renderProdukGrid(produkTokoFiltered);
        }

        // Buka modal beli produk
        function openBeli(idOrProduk) {
            const produk = (idOrProduk && typeof idOrProduk === 'object')
                ? idOrProduk
                : produkTokoData.find(p => String(p.id) === String(idOrProduk));
            if (!produk) return;
            produkDipilih = produk;
            document.getElementById('beli-produk-nama').textContent = produk.nama;
            document.getElementById('beli-produk-kode').textContent = produk.kode;
            document.getElementById('beli-produk-harga-display').textContent = formatRupiah(produk.harga);
            document.getElementById('beli-produk-stok').textContent = produk.stok;
            document.getElementById('beli-produk-icon').className = `fa-solid ${getIconProduk(produk.kategori)} text-slate-800 dark:text-slate-200`;
            document.getElementById('beli-qty').value = 1;
            document.getElementById('beli-qty').max = produk.stok;
            updateSubtotal();
            document.getElementById('beli-modal').classList.remove('hidden');
        }

        function closeBeli() {
            document.getElementById('beli-modal').classList.add('hidden');
            produkDipilih = null;
        }

        function changeQty(delta) {
            const input = document.getElementById('beli-qty');
            const max = produkDipilih ? produkDipilih.stok : 999;
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            if (val > max) val = max;
            input.value = val;
            updateSubtotal();
        }

        function updateSubtotal() {
            if (!produkDipilih) return;
            const qty = parseInt(document.getElementById('beli-qty').value) || 1;
            document.getElementById('beli-subtotal').textContent = formatRupiah(produkDipilih.harga * qty);
        }

        // Tambah produk ke keranjang
        function tambahKeKeranjang() {
            if (!produkDipilih) return;
            const qty = parseInt(document.getElementById('beli-qty').value) || 1;
            if (qty < 1 || qty > produkDipilih.stok) {
                showToastToko('Jumlah tidak valid!', 'red');
                return;
            }

            const existIdx = keranjang.findIndex(k => k.id === produkDipilih.id);
            if (existIdx >= 0) {
                const newQty = keranjang[existIdx].qty + qty;
                if (newQty > produkDipilih.stok) {
                    showToastToko('Melebihi stok tersedia!', 'red');
                    return;
                }
                keranjang[existIdx].qty = newQty;
            } else {
                keranjang.push({ ...produkDipilih, qty });
            }

            updateCartBadge();
            closeBeli();
            showToastToko(`"${produkDipilih.nama}" ditambahkan ke keranjang!`, 'emerald');
        }

        // Update badge keranjang di nav
        function updateCartBadge() {
            const total = keranjang.reduce((s, k) => s + k.qty, 0);
            ['cart-badge-sidebar', 'cart-badge-mobile', 'cart-count-badge'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                if (total > 0) {
                    el.textContent = total > 99 ? '99+' : total;
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        }

        // Toggle modal keranjang
        function toggleCart() {
            const modal = document.getElementById('cart-modal');
            if (modal.classList.contains('hidden')) {
                renderKeranjang();
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        // Render isi keranjang
        function renderKeranjang() {
            const list = document.getElementById('cart-items-list');
            const totalEl = document.getElementById('cart-total-price');
            const checkoutBtn = document.getElementById('checkout-btn');
            if (!list) return;

            if (keranjang.length === 0) {
                list.innerHTML = '<div class="text-center py-8 text-slate-400 italic text-sm">Keranjang masih kosong.</div>';
                if (totalEl) totalEl.textContent = 'Rp 0';
                if (checkoutBtn) checkoutBtn.disabled = true;
                return;
            }

            if (checkoutBtn) checkoutBtn.disabled = false;
            const totalHarga = keranjang.reduce((s, k) => s + k.harga * k.qty, 0);
            if (totalEl) totalEl.textContent = formatRupiah(totalHarga);

            list.innerHTML = keranjang.map((item, idx) => `
                <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                    <div class="w-9 h-9 bg-slate-200 dark:bg-slate-700/40 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid ${getIconProduk(item.kategori)} text-slate-800 dark:text-slate-200 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 dark:text-white truncate">${item.nama}</p>
                        <p class="text-[10px] text-slate-400">${item.kode} &bull; ${formatRupiah(item.harga)}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button onclick="editQtyKeranjang(${idx}, -1)" class="w-6 h-6 bg-slate-200 dark:bg-slate-700 rounded-md text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs font-bold hover:bg-slate-300 transition">−</button>
                        <span class="text-xs font-bold text-slate-800 dark:text-white w-5 text-center">${item.qty}</span>
                        <button onclick="editQtyKeranjang(${idx}, 1)" class="w-6 h-6 bg-slate-200 dark:bg-slate-700 rounded-md text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs font-bold hover:bg-slate-300 transition">+</button>
                        <button onclick="hapusKeranjang(${idx})" class="w-6 h-6 bg-red-100 dark:bg-red-900/30 rounded-md text-red-500 flex items-center justify-center text-xs hover:bg-red-200 transition ml-1">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function editQtyKeranjang(idx, delta) {
            if (!keranjang[idx]) return;
            const newQty = keranjang[idx].qty + delta;
            if (newQty < 1) { hapusKeranjang(idx); return; }
            if (newQty > keranjang[idx].stok) { showToastToko('Melebihi stok!', 'red'); return; }
            keranjang[idx].qty = newQty;
            updateCartBadge();
            renderKeranjang();
        }

        function hapusKeranjang(idx) {
            keranjang.splice(idx, 1);
            updateCartBadge();
            renderKeranjang();
        }

        // Proses checkout pembelian
        async function prosesCheckout() {
            if (keranjang.length === 0) return;
            const userId = getCurrentUserId();
            if (!userId) {
                alert('Sesi habis. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }

            const metodeEl = document.querySelector('input[name="atk-delivery-option"]:checked');
            const metodePengiriman = metodeEl ? metodeEl.value : 'ambil_sendiri';
            const alamatKirim = metodePengiriman === 'antar'
                ? (document.getElementById('atk-alamat-input')?.value || '').trim()
                : null;

            if (metodePengiriman === 'antar' && (!alamatKirim || alamatKirim.length === 0)) {
                showToastToko('Silakan masukkan alamat pengiriman terlebih dahulu!', 'red');
                return;
            }

            const payEl = document.querySelector('input[name="atk-pay-option"]:checked');
            const metodeBayar = payEl ? payEl.value : 'di_tempat';

            const totalHarga = keranjang.reduce((s, k) => s + k.harga * k.qty, 0);

            const checkoutBtn = document.getElementById('checkout-btn');
            const originalBtnText = checkoutBtn ? checkoutBtn.innerHTML : '';
            if (checkoutBtn) { checkoutBtn.disabled = true; checkoutBtn.textContent = 'Memproses...'; }

            const payload = {
                user_id: userId,
                items: keranjang.map(k => ({ produk_id: k.id, kode: k.kode, nama: k.nama, harga: k.harga, qty: k.qty })),
                total: totalHarga,
                metode_pengiriman: metodePengiriman,
                alamat_kirim: alamatKirim || null,
                // Status awal SAMA kosakatanya dgn booking buku, supaya langsung
                // kebaca oleh kartu accordion Sirkulasi yang sudah ada.
                status: metodePengiriman === 'antar' ? 'Booking Dikonfirmasi Admin' : 'Booking',
                metode_bayar: metodeBayar,
            };

            try {
                const res = await fetch('/api/pembelian-atk', {
                    method: 'POST',
                    headers: getAuthHeaders({ 'Content-Type': 'application/json' }),
                    body: JSON.stringify(payload)
                });
                const data = await res.json().catch(() => ({}));

                if (!res.ok || data.success === false) {
                    showToastToko(data.message || 'Gagal memproses pesanan. Coba lagi.', 'red');
                    if (checkoutBtn) { checkoutBtn.disabled = false; checkoutBtn.innerHTML = originalBtnText; }
                    return;
                }

                if (data.order) {
                    simpanRiwayatLokal(data.order);
                }

                showToastToko(
                    metodePengiriman === 'antar'
                        ? 'Pesanan diterima! Akan diantar ke alamatmu, pantau statusnya di tab Sirkulasi.'
                        : 'Pesanan diterima! Ambil di meja sirkulasi dalam 2 jam.',
                    'emerald'
                );

                // Pesanan diantar -> teruskan ke kurir
                if (metodePengiriman === 'antar') {
                    buatOrderKurir({
                        prefix: 'ORD',
                        id: data.order && data.order.id,
                        status_bayar: 'Belum Dibayar',
                        metode_bayar: metodeBayar === 'di_tempat' ? 'Bayar di Tempat' : null,
                        alamat: alamatKirim,
                        total: totalHarga,
                        items: keranjang.map(k => ({
                            produk_id: k.id, id: k.id, kode: k.kode, nama: k.nama, tipe: 'ATK',
                            harga: k.harga, qty: k.qty, subtotal: k.harga * k.qty
                        }))
                    });
                }

                keranjang = [];
                updateCartBadge();
                document.getElementById('cart-modal').classList.add('hidden');
                // Reset pilihan form keranjang utk pesanan berikutnya
                document.querySelector('input[name="atk-delivery-option"][value="ambil_sendiri"]').checked = true;
                toggleAtkAlamatField();
                if (document.getElementById('atk-alamat-input')) document.getElementById('atk-alamat-input').value = '';
                const payDefault = document.querySelector('input[name="atk-pay-option"][value="di_tempat"]');
                if (payDefault) payDefault.checked = true;
                updateAtkPayHint();

                // Pilih "Bayar Sekarang": langsung buka simulasi pembayaran untuk pesanan ini
                if (metodeBayar === 'online' && data.order) {
                    setTimeout(() => openPayAtkModal(data.order), 250);
                }

                loadRiwayatPembelian();
                loadProdukToko(); // Refresh stok
                loadUserLoans();  // Refresh kartu status Sirkulasi (ikut tampilkan pesanan ATK baru)
            } catch (e) {
                console.error('Gagal checkout ATK:', e);
                showToastToko('Tidak bisa terhubung ke server. Coba lagi.', 'red');
            } finally {
                if (checkoutBtn) { checkoutBtn.disabled = false; checkoutBtn.innerHTML = originalBtnText; }
            }
        }

        function getRiwayatAtkKey() {
            const userId = getCurrentUserId();
            return `libra-riwayat-atk_${userId || 'guest'}`;
        }

        function simpanRiwayatLokal(payload) {
            if (!payload) return;
            try {
                const key = getRiwayatAtkKey();
                const riwayat = JSON.parse(localStorage.getItem(key) || '[]');
                const existIdx = riwayat.findIndex(o => String(o.id) === String(payload.id));
                if (existIdx >= 0) {
                    riwayat[existIdx] = { ...riwayat[existIdx], ...payload };
                } else {
                    riwayat.unshift({ ...payload, created_at: payload.created_at || new Date().toISOString() });
                }
                localStorage.setItem(key, JSON.stringify(riwayat.slice(0, 50)));
            } catch (e) { /* ignore */ }
        }

        function escapeHtmlAtk(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatWaktuAtk(dateStr) {
            if (!dateStr) return '-';
            try {
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return dateStr;
                return d.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }).replace('.', ':') + ' WIB';
            } catch (e) {
                return dateStr;
            }
        }

        function isOrderSelesai(status) {
            return String(status || '').toLowerCase() === 'selesai';
        }

        function isOrderProses(status) {
            const s = String(status || '').toLowerCase();
            return s !== 'selesai' && s !== 'dibatalkan';
        }

        let riwayatAtkData = [];
        let riwayatAtkFilter = 'semua'; // 'semua' | 'proses' | 'selesai'

        function setRiwayatFilter(filter) {
            riwayatAtkFilter = filter;
            const btnSemua = document.getElementById('btn-riwayat-semua');
            const btnProses = document.getElementById('btn-riwayat-proses');
            const btnSelesai = document.getElementById('btn-riwayat-selesai');

            const activeClass = 'px-2.5 py-1 rounded-md bg-white dark:bg-slate-700 shadow-sm text-slate-900 dark:text-white font-semibold transition';
            const inactiveClass = 'px-2.5 py-1 rounded-md text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition';

            if (btnSemua) btnSemua.className = filter === 'semua' ? activeClass : inactiveClass;
            if (btnProses) btnProses.className = filter === 'proses' ? activeClass : inactiveClass;
            if (btnSelesai) btnSelesai.className = filter === 'selesai' ? activeClass : inactiveClass;

            renderRiwayatPembelianList();
        }

        function renderRiwayatPembelianList() {
            const container = document.getElementById('riwayat-pembelian-list');
            if (!container) return;

            let filtered = riwayatAtkData;
            if (riwayatAtkFilter === 'proses') {
                filtered = riwayatAtkData.filter(r => isOrderProses(r.status));
            } else if (riwayatAtkFilter === 'selesai') {
                filtered = riwayatAtkData.filter(r => isOrderSelesai(r.status));
            }

            if (!filtered || filtered.length === 0) {
                let emptyMsg = 'Belum ada riwayat pembelian.';
                if (riwayatAtkFilter === 'proses') emptyMsg = 'Tidak ada pesanan yang sedang diproses saat ini.';
                if (riwayatAtkFilter === 'selesai') emptyMsg = 'Belum ada pesanan yang telah selesai.';
                container.innerHTML = `<div class="text-center py-8 text-slate-400 text-xs italic">${emptyMsg}</div>`;
                return;
            }

            container.innerHTML = filtered.map(r => {
                const status = r.status || 'Booking';
                const isSelesai = isOrderSelesai(status);
                const isBatal = String(status).toLowerCase().trim() === 'dibatalkan';
                const waktuPesan = formatWaktuAtk(r.created_at);
                const waktuSelesai = r.tanggal_selesai ? formatWaktuAtk(r.tanggal_selesai) : null;
                let items = r.items || [];
                if (typeof items === 'string') {
                    try { items = JSON.parse(items); } catch (_) { items = []; }
                }
                if (!Array.isArray(items)) items = [];
                const metode = r.metode_pengiriman === 'antar' ? 'Diantar ke Rumah' : 'Ambil Sendiri di Sirkulasi';

                // Status pembayaran
                let payHtml = '';
                if (!isBatal) {
                    if (r.status_bayar === 'Lunas') {
                        payHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300"><i class="fa-solid fa-circle-check text-[10px]"></i> Lunas${r.metode_bayar ? ' (' + escapeHtmlAtk(r.metode_bayar) + ')' : ''}</span>`;
                    } else if (r.metode_bayar === 'Bayar di Tempat') {
                        payHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"><i class="fa-solid fa-hand-holding-dollar text-[10px]"></i> ${r.metode_pengiriman === 'antar' ? 'Bayar ke kurir saat tiba' : 'Bayar di meja sirkulasi'}</span>`;
                    } else {
                        payHtml = `<button type="button" onclick="openPayAtkModal('${r.id}')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-blue-600 text-white hover:bg-blue-700 transition-colors"><i class="fa-solid fa-credit-card text-[10px]"></i> Bayar Sekarang</button>`;
                    }
                }

                // Status Badge
                let statusBadge = '';
                if (isSelesai) {
                    statusBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                        <i class="fa-solid fa-circle-check"></i> Selesai
                    </span>`;
                } else if (isBatal) {
                    statusBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                        <i class="fa-solid fa-circle-xmark"></i> Dibatalkan
                    </span>`;
                } else {
                    statusBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> ${escapeHtmlAtk(status)}
                    </span>`;
                }

                return `
                <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors flex flex-col gap-2.5">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            ${statusBadge}
                            <span class="text-[10px] font-mono text-slate-400">#${String(r.id || '').slice(0, 8)}</span>
                        </div>
                        <span class="text-[11px] text-slate-400 flex items-center gap-1">
                            <i class="fa-regular fa-clock text-[10px]"></i> Dipesan: ${waktuPesan}
                        </span>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 border border-slate-100 dark:border-slate-800 space-y-1.5">
                        ${items.map(item => `
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-700 dark:text-slate-200">
                                    ${escapeHtmlAtk(item.nama)} <span class="text-slate-400 font-normal">× ${item.qty}</span>
                                </span>
                                <span class="font-semibold text-slate-600 dark:text-slate-300">
                                    ${formatRupiah((item.harga || 0) * (item.qty || 1))}
                                </span>
                            </div>
                        `).join('')}
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-1 flex-wrap text-xs">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                <i class="fa-solid ${r.metode_pengiriman === 'antar' ? 'fa-truck-fast' : 'fa-store'} text-[10px]"></i>
                                ${metode}${r.alamat_kirim ? ` (${escapeHtmlAtk(r.alamat_kirim)})` : ''}
                            </span>
                            ${isSelesai && waktuSelesai ? `
                                <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <i class="fa-solid fa-check-double text-[10px]"></i> Selesai pada: ${waktuSelesai}
                                </span>
                            ` : ''}
                            ${!isSelesai && !isBatal && r.metode_pengiriman === 'ambil_sendiri' ? `
                                <span class="text-[10px] text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                    <i class="fa-solid fa-hourglass-half text-[10px]"></i> Batas ambil: 2 jam sejak pesanan dibuat
                                </span>
                            ` : ''}
                            ${payHtml}
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block">Total</span>
                            <span class="text-sm font-bold text-slate-900 dark:text-white">${formatRupiah(r.total || 0)}</span>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        // Muat riwayat pembelian
        async function loadRiwayatPembelian() {
            const container = document.getElementById('riwayat-pembelian-list');
            if (!container) return;

            let riwayat = [];

            // Coba dari API dulu
            try {
                const userId = getCurrentUserId();
                if (userId) {
                    const res = await fetch(`/api/pembelian-atk?user_id=${encodeURIComponent(userId)}`, { headers: getAuthHeaders() });
                    if (res.ok) {
                        const data = await res.json();
                        riwayat = Array.isArray(data) ? data : (data.data || []);
                        if (riwayat.length > 0) {
                            try {
                                localStorage.setItem(getRiwayatAtkKey(), JSON.stringify(riwayat));
                            } catch (_) {}
                        }
                    }
                }
            } catch (e) {
                console.error('Gagal mengambil riwayat ATK via API:', e);
            }

            // Fallback ke localStorage jika API kosong atau gagal
            if (riwayat.length === 0) {
                try {
                    riwayat = JSON.parse(localStorage.getItem(getRiwayatAtkKey()) || '[]');
                } catch (e) {
                    riwayat = [];
                }
            }

            riwayatAtkData = riwayat;
            renderRiwayatPembelianList();
        }

        // Toast khusus toko (reuse success-toast yang sudah ada)
        function showToastToko(msg, type = 'emerald') {
            const toast = document.getElementById('success-toast');
            const title = document.getElementById('toast-title');
            const desc = document.getElementById('toast-desc');
            if (!toast) return;
            if (title) title.textContent = type === 'emerald' ? 'Berhasil!' : 'Perhatian!';
            title.className = `text-xs font-semibold ${type === 'emerald' ? 'text-emerald-400' : 'text-red-400'}`;
            if (desc) desc.textContent = msg;
            const ok = type === 'emerald';
            const wrap = document.getElementById('toast-icon-wrap');
            const icon = document.getElementById('toast-icon');
            if (wrap) wrap.className = `${ok ? 'bg-emerald-500' : 'bg-red-500'} rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 shadow-md`;
            if (icon) icon.className = `fa-solid ${ok ? 'fa-check' : 'fa-xmark'} text-white text-sm`;
            toast.classList.remove('hidden');
            clearTimeout(toast._tokoTimer);
            toast._tokoTimer = setTimeout(() => toast.classList.add('hidden'), ok ? 3500 : 6000);
        }

        // Sinkron input qty dengan subtotal di modal beli
        document.addEventListener('input', function(e) {
            if (e.target && e.target.id === 'beli-qty') {
                updateSubtotal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('libra-theme') === 'dark') document.documentElement.classList.add('dark');
            applyTranslations();
            switchTab(activeTabId);
            loadWishlist();
            renderRecommendedBooks();
            loadUserLoans();
            loadNotifications();
            updateFineDisplay();

            if (userFine > 0) {
                showFineWidget();
            }

            // Debug: Cek apakah ada card buku
            const cards = document.querySelectorAll('.book-card');
            console.log('Total card buku ditemukan:', cards.length);
            cards.forEach(card => {
                console.log('Book ID:', card.dataset.id, 'Title:', card.dataset.title);
            });
        });

        // Sinkronisasi status pengantaran Kurir (browser yang sama)
        window.addEventListener('storage', function (e) {
            if (e.key !== 'libra_courier_orders') return;
            try {
                loadRiwayatPembelian();
                const orders = JSON.parse(e.newValue || '[]');
                const userId = getCurrentUserId();
                const mine = orders.filter(o => !o.user_id || String(o.user_id) === String(userId));
                const latest = mine[0];
                if (latest) {
                    const idText = latest.order_id || latest.id;
                    if (latest.status === 'Sedang Diantar') {
                        showToast('Pesanan Diantar', `Kurir telah memulai pengantaran pesanan Anda (${idText})! 🚚`);
                    } else if (latest.status === 'Selesai') {
                        showToast('Pesanan Sampai', `Pesanan Anda (${idText}) telah sampai di tujuan! 🎉`);
                    }
                }
            } catch (_) {}
        });

        // Sinkronisasi status pengantaran lintas perangkat: tanya server berkala
        // (kurir bisa memakai perangkat lain, jadi event 'storage' saja tidak cukup).
        const COURIER_POLL_MS = 15000;
        const COURIER_SEEN_KEY = 'libra_courier_seen';
        let courierPollInterval = null;

        async function pollCourierOrders() {
            if (document.hidden) return;
            const userId = getCurrentUserId();
            if (!userId) return;
            try {
                const res = await fetch('/api/orders?user_id=' + encodeURIComponent(userId), { headers: getAuthHeaders() });
                if (res.status === 401) {
                    if (courierPollInterval) {
                        clearInterval(courierPollInterval);
                        courierPollInterval = null;
                    }
                    return;
                }
                if (!res.ok) return;
                const orders = await res.json();
                if (!Array.isArray(orders)) return; // tabel belum dimigrasi

                let seen = null;
                try { seen = JSON.parse(localStorage.getItem(COURIER_SEEN_KEY) || 'null'); } catch (_) {}
                const next = {};
                let changed = false;

                orders.forEach(o => {
                    const id = o.order_id || o.id;
                    next[id] = o.status;
                    // Baseline pertama: jangan membunyikan notifikasi untuk pesanan lama
                    if (seen && seen[id] !== o.status) {
                        const isId = currentLang === 'id';
                        if (o.status === 'Sedang Diantar') {
                            showToast(isId ? 'Pesanan Diantar' : 'Order On the Way',
                                isId ? `Kurir sedang mengantar pesanan Anda (${id})! 🚚` : `The courier is delivering your order (${id})! 🚚`);
                        } else if (o.status === 'Selesai') {
                            showToast(isId ? 'Pesanan Sampai' : 'Order Delivered',
                                isId ? `Pesanan Anda (${id}) telah sampai di tujuan! 🎉` : `Your order (${id}) has arrived! 🎉`);
                        }
                        changed = true;
                    }
                });

                localStorage.setItem(COURIER_SEEN_KEY, JSON.stringify(next));
                if (changed) {
                    loadRiwayatPembelian();
                    loadUserLoans();
                }
            } catch (_) { /* jaringan putus: coba lagi di putaran berikutnya */ }
        }

        setTimeout(pollCourierOrders, 3000);
        courierPollInterval = setInterval(pollCourierOrders, COURIER_POLL_MS);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) pollCourierOrders(); });

        console.log('📚 Sistem Libra siap digunakan dengan Supabase via Blade!');
        console.log(`📊 Total buku: ${booksData.length}`);
    </script>
    <!-- Lapisan terjemahan tambahan: menerjemahkan teks yang tidak punya data-i18n -->
    <script>
    (function () {
        const EXACT = {
            'Sistem Informasi Perpustakaan': 'Library Information System',
            'Toko ATK': 'ATK Store',
            'Ganti tema gelap/terang': 'Toggle dark/light theme',
            'Notifikasi Terbaru': 'Latest Notifications',
            'Tandai dibaca': 'Mark as read',
            'Memuat notifikasi...': 'Loading notifications...',
            'Silakan login untuk melihat notifikasi.': 'Please log in to view notifications.',
            'Belum ada notifikasi.': 'No notifications yet.',
            'Gagal memuat notifikasi.': 'Failed to load notifications.',
            'Cari judul atau penulis buku secara live...': 'Search book titles or authors live...',
            'Cari judul buku secara live...': 'Search book titles live...',
            'Semua Genre': 'All Genres',
            'Tahun:': 'Year:',
            'Tersedia': 'Available',
            'Dipinjam': 'On Loan',
            'Sebelumnya': 'Previous',
            'Ke halaman…': 'Go to page…',
            'Selanjutnya': 'Next',
            'Memuat data peminjaman...': 'Loading loan data...',
            'Peminjaman Aktif Saya': 'My Active Loans',
            'Rincian Informasi Denda Berjalan': 'Current Fine Details',
            'Belum ada peminjaman aktif.': 'No active loans.',
            '(Terlambat)': '(Overdue)',
            '(Hari Ini)': '(Today)',
            // Nama produk & kategori Toko ATK
            'Pulpen Hitam': 'Black Pen',
            'Pulpen Biru': 'Blue Pen',
            'Pensil 2B': '2B Pencil',
            'Penghapus': 'Eraser',
            'Penggaris 30 cm': '30 cm Ruler',
            'Buku Tulis 38 Lembar': '38-Sheet Notebook',
            'Kertas HVS A4 (rim)': 'A4 Copy Paper (ream)',
            'Map Plastik': 'Plastic Folder',
            'Stapler Kecil': 'Small Stapler',
            'Highlighter': 'Highlighter',
            'Kertas': 'Paper',
            'Bayar di Tempat': 'Pay on Site',
            'Bayar Pesanan': 'Pay for Order',
            'Bayar saat mengambil di meja sirkulasi': 'Pay when you pick up at the circulation desk',
            'Bayar ke kurir saat barang tiba di rumah': 'Pay the courier when the items arrive at your home',
            'Bayar online sekarang (simulasi, tanpa uang sungguhan)': 'Pay online now (simulation, no real money)',
            'Bayar ke kurir saat tiba': 'Pay the courier on arrival',
            'Bayar di meja sirkulasi': 'Pay at the circulation desk',
            'Lunas': 'Paid',
            'Belum siap? Tutup saja, kamu bisa membayarnya nanti dari Riwayat Pembelian.': 'Not ready? Just close this, you can pay later from Purchase History.',
            'Pembayaran Berhasil!': 'Payment Successful!',
            'Pesanan ATK kamu sudah lunas (simulasi).': 'Your ATK order is paid (simulation).',
            'Tagihan Denda': 'Fine Bills',
            'Denda dari buku yang sudah dikembalikan. Lunasi untuk bisa meminjam lagi.': 'Fines from returned books. Pay them to borrow again.',
            'Bayar Denda': 'Pay Fine',
            'SIMULASI PEMBAYARAN - tidak ada uang sungguhan': 'PAYMENT SIMULATION - no real money involved',
            'Metode Pembayaran': 'Payment Method',
            'Transfer Bank': 'Bank Transfer',
            'Bayar Sekarang': 'Pay Now',
            'Memproses pembayaran...': 'Processing payment...',
            'Denda Lunas!': 'Fine Paid!',
            'Semua': 'All',
            'Sedang Dipesan': 'In Progress',
            'Selesai': 'Completed',
            'Dibatalkan': 'Cancelled',
            'Refresh': 'Refresh',
            'Memuat riwayat...': 'Loading history...',
            'Total': 'Total',
            'Ambil Sendiri di Sirkulasi': 'Self Pickup at Circulation',
            'Batas ambil: 2 jam sejak pesanan dibuat': 'Pickup deadline: 2 hours after the order is placed',
            'Tidak ada pesanan yang sedang diproses saat ini.': 'There are no orders in progress right now.',
            'Belum ada pesanan yang telah selesai.': 'No completed orders yet.',
            'Kamu sudah ada di antrian buku ini.': 'You are already in the queue for this book.',
            'Belum ada buku lain dengan genre yang sama.': 'There are no other books with the same genre yet.',
            'Kamu sudah membooking buku ini. Cek tab Sirkulasi.': 'You have already booked this book. Check the Circulation tab.',
            'Masuk Antrian': 'Join Queue',
            'Batalkan Antrian': 'Leave Queue',
            'Ditahan untuk Antrian Lain': 'Held for Another Queue',
            'Ini giliranmu!': "It's your turn!",
            'Booking & Antar ke Rumah': 'Book & Deliver to Home',
            'Perkiraan giliranmu:': 'Estimated turn:',
            'Perkiraan:': 'Estimate:',
            'Booking sebelum': 'Book before',
            ', atau giliranmu akan hangus.': ', or your turn will expire.',
            'Kalau sampai': 'If by',
            'dia tidak booking, giliran otomatis pindah ke antrian berikutnya.': 'they have not booked, the turn automatically moves to the next person in the queue.',
            'Buku': 'Book',
            'Beli alat tulis & perlengkapan langsung dari perpustakaan.': 'Buy stationery & supplies directly from the library.',
            'Keranjang': 'Cart',
            'Cari nama atau kode produk...': 'Search product name or code...',
            'Semua Kategori': 'All Categories',
            'Alat Tulis': 'Stationery',
            'Perlengkapan': 'Supplies',
            'Memuat produk...': 'Loading products...',
            'Tidak ada produk ditemukan.': 'No products found.',
            'Produk gagal dimuat. Periksa koneksi lalu coba lagi.': 'Failed to load products. Check your connection and try again.',
            'Muat ulang': 'Reload',
            'Belum ada riwayat pembelian.': 'No purchase history yet.',
            'Riwayat Pembelian': 'Purchase History',
            'Total Denda': 'Total Fine',
            'Beranda': 'Home',
            'Pustaka': 'Library',
            'Sirkulasi': 'Circulation',
            'Profil': 'Profile',
            'Detail Buku': 'Book Details',
            'Rekomendasi Buku Serupa': 'Similar Book Recommendations',
            'Formulir Peminjaman Buku': 'Book Loan Form',
            'Mulai Tanggal': 'Start Date',
            'Sampai Tanggal': 'Until Date',
            'Opsi Pengambilan': 'Pickup Option',
            'Ambil Sendiri': 'Self Pickup',
            'Jasa Pengantaran': 'Delivery Service',
            'Batal': 'Cancel',
            'Konfirmasi': 'Confirm',
            'Keranjang masih kosong.': 'Your cart is empty.',
            'Metode Pengiriman': 'Delivery Method',
            'Diantar ke Rumah': 'Deliver to Home',
            'Alamat Pengiriman': 'Delivery Address',
            'Tulis alamat lengkap tujuan pengiriman...': 'Write the full delivery address...',
            'Total Pembayaran': 'Total Payment',
            'Tambah ke Keranjang': 'Add to Cart',
            'Jumlah': 'Quantity',
            'Pemesanan Berhasil!': 'Order Successful!',
            'Stok Habis': 'Out of Stock',
            'Deskripsi buku tidak tersedia.': 'Book description is not available.',
            'Baru saja': 'Just now',
            'Berhasil!': 'Success!',
            'Perhatian!': 'Attention!',
            'Info': 'Info',
            'Gagal': 'Failed',
            'Tidak Bisa Masuk Antrian': 'Cannot Join Queue',
            'Berhasil Masuk Antrian': 'Joined Queue',
            'Antrian Dibatalkan': 'Queue Cancelled',
            'Peminjaman Berhasil!': 'Loan Successful!',
            'Buku berhasil disimpan ke sirkulasi Anda.': 'The book has been saved to your circulation.',
            'Silakan login untuk ikut antrian.': 'Please log in to join the queue.',
            'Terjadi kesalahan, coba lagi.': 'An error occurred, please try again.',
            'Kamu telah keluar dari antrian buku ini.': 'You have left the queue for this book.',
            'Kamu sedang meminjam buku ini': 'You are currently borrowing this book',
            'Buku ini sedang ditahan untuk orang pertama di antrian': 'This book is being held for the first person in the queue',
            'Belum bisa diperkirakan': 'Cannot be estimated yet',
            'Belum ada perkiraan': 'No estimate yet',
            'Menunggu Kurir': 'Waiting for Courier',
            'Sedang Diantar Kurir': 'Out for Delivery',
            'Menunggu diambil sendiri': 'Waiting for self pickup',
            'Pesanan ATK — ambil sendiri': 'ATK Order — self pickup',
            'Pesanan ATK': 'ATK Order',
            'Ambil pesananmu': 'Pick up your order',
            'Sesi habis. Silakan login kembali.': 'Session expired. Please log in again.',
            'Mohon isi tanggal pinjam dan batas pengembalian!': 'Please fill in the loan date and return deadline!',
            'Batas pengembalian harus setelah tanggal pinjam!': 'The return deadline must be after the loan date!',
            'Jumlah tidak valid!': 'Invalid quantity!',
            'Melebihi stok tersedia!': 'Exceeds available stock!',
            'Melebihi stok!': 'Exceeds stock!',
            'Gagal memproses pesanan. Coba lagi.': 'Failed to process the order. Please try again.',
            'Tidak bisa terhubung ke server. Coba lagi.': 'Cannot connect to the server. Please try again.',
            'Pesanan diterima! Akan diantar ke alamatmu, pantau statusnya di tab Sirkulasi.': 'Order received! It will be delivered to your address, track its status in the Circulation tab.',
            'Pesanan diterima! Ambil di meja sirkulasi dalam 2 jam.': 'Order received! Pick it up at the circulation desk within 2 hours.',
            'Terima kasih telah meminjam! Buku sedang diantarkan oleh kurir ke alamatmu.': 'Thank you for borrowing! The book is being delivered by courier to your address.'
        };

        // [pola, pengganti] untuk teks yang mengandung angka/nama dinamis
        const PATTERNS = [
            [/^Lunas \((.+)\)$/, (m, a) => 'Paid (' + a + ')'],
            [/^Dipesan: (.+)$/, (m, a) => 'Ordered: ' + translate(a)],
            [/^Selesai pada: (.+)$/, (m, a) => 'Completed on: ' + translate(a)],
            [/^(\d+) menit lalu$/, '$1 minutes ago'],
            [/^(\d+) jam lalu$/, '$1 hours ago'],
            [/^(\d+) hari lalu$/, '$1 days ago'],
            [/^Stok: (\d+)$/, 'Stock: $1'],
            [/^Stok tersedia:/, 'Available stock:'],
            [/^Tahun: (.+)$/, 'Year: $1'],
            [/^Terlambat (\d+) hari × Rp (.+) \/ hari$/, 'Overdue $1 days × Rp $2 / day'],
            [/^(\d+) orang sedang mengantre$/, '$1 people are in the queue'],
            [/^Kalau kamu antre sekarang, kamu jadi antrian ke-(\d+)\. Perkiraan giliranmu:$/, 'If you join the queue now, you will be number $1. Estimated turn:'],
            [/^Antrian ke-(\d+)$/, 'Queue #$1'],
            [/^Masuk Antrian \(jadi ke-(\d+)\)$/, 'Join Queue (you will be #$1)'],
            [/^Kamu ada di antrian ke-(\d+) dari (\d+)\.$/, 'You are number $1 of $2 in the queue.'],
            [/^Kamu antrian ke-(\d+) dari (\d+)$/, 'You are number $1 of $2 in the queue'],
            [/^Kamu telah masuk antrian untuk buku "(.*)"\. Kami akan memberi tahu kamu saat giliranmu tiba\.$/, 'You have joined the queue for the book "$1". We will notify you when it is your turn.'],
            [/^Terima kasih telah meminjam buku "(.*)"! Buku sedang diantarkan oleh kurir ke alamatmu\.$/, 'Thank you for borrowing "$1"! The book is being delivered by courier to your address.'],
            [/^"(.*)" ditambahkan ke keranjang!$/, (m, a) => '"' + translate(a) + '" added to the cart!'],
            [/^Durasi peminjaman maksimal adalah 1 bulan dari tanggal pinjam\. Silakan pilih tanggal pengembalian paling lambat (.+)\.$/, 'The maximum loan period is 1 month from the loan date. Please choose a return date no later than $1.'],
            [/^(Ambil pesananmu|Ambil buku) langsung di meja sirkulasi perpustakaan sebelum batas waktu di atas habis\.$/, 'Pick up your order/book directly at the library circulation desk before the deadline above.']
        ];

        const MONTHS = {Jan:'Jan',Feb:'Feb',Mar:'Mar',Apr:'Apr',Jun:'Jun',Jul:'Jul',Agu:'Aug',Sep:'Sep',Okt:'Oct',Nov:'Nov',Des:'Dec',Januari:'January',Februari:'February',Maret:'March',April:'April',Mei:'May',Juni:'June',Juli:'July',Agustus:'August',September:'September',Oktober:'October',November:'November',Desember:'December'};

        function translate(str) {
            const m = str.match(/^(\s*)([\s\S]*?)(\s*)$/);
            const lead = m[1], core = m[2], trail = m[3];
            if (!core) return str;
            const norm = core.replace(/\s+/g, ' ');
            if (EXACT[norm] !== undefined) return lead + EXACT[norm] + trail;
            for (const [re, rep] of PATTERNS) {
                if (re.test(norm)) return lead + norm.replace(re, rep) + trail;
            }
            // Tanggal berbahasa Indonesia, mis. "29 Juni 2026"
            const d = norm.match(/^(\d{1,2}) (Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember|Jan|Feb|Mar|Apr|Jun|Jul|Agu|Sep|Okt|Nov|Des)(?: (\d{4}))?(.*)$/);
            if (d) return lead + d[1] + ' ' + MONTHS[d[2]] + (d[3] ? ' ' + d[3] : '') + translate(d[4] || '') + trail;
            return str;
        }

        const SKIP = new Set(['SCRIPT', 'STYLE', 'TEXTAREA', 'NOSCRIPT']);
        const ATTRS = ['placeholder', 'title', 'aria-label'];
        const textState = new Map();   // node -> { orig, en }
        const attrState = new Map();   // "el|attr" handled via el -> {attr: {orig,en}}
        let busy = false;

        function processText(node) {
            const parent = node.parentNode;
            if (!parent || SKIP.has(parent.nodeName)) return;
            const st = textState.get(node);
            if (st && node.nodeValue === st.en) return; // sudah kita terjemahkan
            const current = node.nodeValue;
            const en = translate(current);
            if (en !== current) {
                textState.set(node, { orig: current, en });
                node.nodeValue = en;
            } else if (st) {
                textState.delete(node);
            }
        }

        function processAttrs(el) {
            for (const a of ATTRS) {
                if (!el.hasAttribute || !el.hasAttribute(a)) continue;
                const cur = el.getAttribute(a);
                let st = attrState.get(el);
                if (st && st[a] && cur === st[a].en) continue;
                const en = translate(cur);
                if (en !== cur) {
                    if (!st) { st = {}; attrState.set(el, st); }
                    st[a] = { orig: cur, en };
                    el.setAttribute(a, en);
                } else if (st && st[a]) {
                    delete st[a];
                }
            }
        }

        function walk(root) {
            if (!root) return;
            if (root.nodeType === 3) { processText(root); return; }
            if (root.nodeType !== 1) return;
            if (SKIP.has(root.nodeName)) return;
            processAttrs(root);
            const tw = document.createTreeWalker(root, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT, {
                acceptNode(n) {
                    return (n.nodeType === 1 && SKIP.has(n.nodeName)) ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
                }
            });
            let n;
            while ((n = tw.nextNode())) {
                if (n.nodeType === 3) processText(n); else processAttrs(n);
            }
        }

        function restore() {
            textState.forEach((st, node) => {
                if (node.nodeValue === st.en) node.nodeValue = st.orig;
            });
            textState.clear();
            attrState.forEach((st, el) => {
                for (const a in st) {
                    if (el.getAttribute(a) === st[a].en) el.setAttribute(a, st[a].orig);
                }
            });
            attrState.clear();
        }

        function sync() {
            busy = true;
            try {
                if (typeof currentLang !== 'undefined' && currentLang === 'en') walk(document.body);
                else restore();
            } finally { busy = false; }
        }

        const observer = new MutationObserver(muts => {
            if (busy || currentLang !== 'en') return;
            busy = true;
            try {
                for (const m of muts) {
                    if (m.type === 'childList') m.addedNodes.forEach(walk);
                    else if (m.type === 'characterData') processText(m.target);
                    else if (m.type === 'attributes') processAttrs(m.target);
                }
            } finally { busy = false; }
        });
        observer.observe(document.body, {
            childList: true, subtree: true, characterData: true,
            attributes: true, attributeFilter: ATTRS
        });

        // Sinkronkan setiap kali bahasa diganti
        const origChange = window.changeLanguage;
        window.changeLanguage = function (lang) {
            if (lang !== 'en') restore();
            origChange(lang);
            sync();
        };

        // Terjemahkan juga pesan alert()
        const origAlert = window.alert;
        window.alert = function (msg) {
            return origAlert.call(window, (currentLang === 'en' && typeof msg === 'string') ? translate(msg) : msg);
        };

        sync();
        window.addEventListener('load', sync);
    })();
    </script>
</body>
</html>