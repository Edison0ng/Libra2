<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Libra</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        libra: {
                            dark: '#1E293B',
                            bg: '#F8FAFC',
                            primary: '#3B82F6',
                            red: '#E11D48'
                        }
                    }
                }
            }
        }
    </script>
    <style>
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
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex h-screen overflow-hidden transition-colors duration-200">

    <!-- SIDEBAR -->
    <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 h-full transition-colors duration-200">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-book-bookmark text-blue-600"></i> LIBRA
            </h1>
            <p class="text-[10px] text-slate-400 font-medium tracking-widest mt-1 uppercase">Sistem Informasi Perpustakaan</p>
        </div>
        <nav class="flex-1 p-4 space-y-1.5">
            <button onclick="switchTab('beranda')" id="nav-beranda-btn" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-semibold bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 transition-all" data-target="beranda">
                <i class="fa-solid fa-house w-5 text-lg"></i> <span data-i18n="nav-beranda">Beranda</span>
            </button>
            <button onclick="switchTab('buku')" id="nav-buku-btn" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" data-target="buku">
                <i class="fa-solid fa-magnifying-glass w-5 text-lg"></i> <span data-i18n="nav-pustaka">Pustaka</span>
            </button>
            <button onclick="switchTab('sirkulasi')" id="nav-sirkulasi-btn" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" data-target="sirkulasi">
                <i class="fa-solid fa-arrows-spin w-5 text-lg"></i> <span data-i18n="nav-sirkulasi">Sirkulasi</span>
            </button>
            <button onclick="switchTab('profil')" id="nav-profil-btn" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" data-target="profil">
                <i class="fa-regular fa-user w-5 text-lg"></i> <span data-i18n="nav-profil">Profil</span>
            </button>
        </nav>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 flex flex-col h-full relative overflow-hidden">
        
        <!-- HEADER -->
        <header class="bg-slate-900 dark:bg-slate-950 text-white px-6 py-4 flex items-center justify-between shadow-md z-20">
            <div>
                <h2 id="header-title" class="font-bold text-lg md:text-xl tracking-wide">Beranda</h2>
                <p id="header-subtitle" class="text-xs text-slate-400">Selamat datang kembali, Mahasiswa!</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="toggleNotif(event)" class="focus:outline-none relative p-2 bg-slate-800 dark:bg-slate-900 rounded-xl hover:bg-slate-700 transition-colors">
                    <i class="fa-regular fa-bell text-lg"></i>
                    <span id="notif-badge" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                    </span>
                </button>

                <div id="notif-panel" class="hidden absolute right-6 top-16 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-800 z-50 overflow-hidden text-slate-800 dark:text-slate-100">
                    <div class="flex justify-between items-center p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                        <h3 class="font-bold text-sm">Notifikasi Terbaru</h3>
                        <button class="text-xs text-blue-500 font-semibold" onclick="clearNotifications()">Tandai dibaca</button>
                    </div>
                    <div id="notif-list" class="p-4 space-y-3.5 max-h-72 overflow-y-auto hide-scrollbar">
                        <p class="text-xs text-slate-400 text-center py-4">Memuat notifikasi...</p>
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
                <!-- Search Bar -->
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="search-input" oninput="liveSearch()" placeholder="Cari judul atau penulis buku secara live..." class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 focus:outline-none focus:border-blue-500 dark:focus:border-blue-500 shadow-sm text-sm transition-colors text-slate-800 dark:text-white">
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
                        data-genre="Science"
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
                            
                            <span class="status-badge inline-block mt-2 px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[9px] font-semibold rounded-full">Tersedia</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center gap-2 pt-4">
                    <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Sebelumnya</button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">1</button>
                    <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">2</button>
                    <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">3</button>
                    <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Selanjutnya</button>
                </div>
            </div>

            <!-- TAB 3: SIRKULASI -->
            <div id="sirkulasi" class="tab-content max-w-3xl mx-auto space-y-6">
                <!-- Kontainer status booking: dirender satu kartu PER booking aktif (bukan satu box global),
                     supaya booking baru tidak menimpa tampilan status/countdown booking sebelumnya. -->
                <div id="circ-status-container" class="space-y-4"></div>

                <!-- TEMPLATE (disembunyikan, dipakai oleh JS untuk clone per booking) -->
                <template id="circ-deadline-template">
                    <div class="circ-deadline-card bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-400 p-2.5 rounded-xl">
                                <i class="fa-regular fa-clock text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300 circ-deadline-title-el" data-i18n="circ-deadline-title">Batas Pengambilan Resv.</h4>
                                <p class="text-xs text-amber-600 dark:text-amber-400/80 circ-deadline-desc-el" data-i18n="circ-deadline-desc">Ambil buku langsung ke meja sirkulasi perpustakaan.</p>
                                <p class="text-[11px] text-amber-500/80 dark:text-amber-500/70 mt-0.5 circ-deadline-book-el"></p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-slate-900 px-4 py-2 rounded-xl border border-amber-200 dark:border-amber-800 font-mono font-bold text-amber-600 dark:text-amber-400 text-sm w-max self-end sm:self-auto">
                            <span data-i18n="circ-remaining-time">Sisa Waktu:</span> <span class="countdown-timer-el">--:--:--</span>
                        </div>
                    </div>
                </template>

                <template id="circ-courier-template">
                    <div class="courier-status-card bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800">
                        <h4 class="font-bold text-sm mb-1 text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-2" data-i18n="circ-courier-status">
                            <i class="fa-solid fa-truck text-blue-500"></i> Status Pengiriman Kurir
                        </h4>
                        <p class="text-[11px] text-slate-400 mb-4 circ-courier-book-el"></p>
                        <div class="relative ml-2 courier-steps-container">
                            <!-- Diisi otomatis oleh JS (buildCourierCard) sesuai status ASLI dari database -->
                        </div>
                    </div>
                </template>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800">
                    <h4 class="font-bold text-sm mb-4 text-slate-700 dark:text-slate-300 uppercase tracking-wider" data-i18n="circ-active-loans">Peminjaman Aktif Saya</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs min-w-[500px]">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400">
                                    <th class="pb-3 font-semibold" data-i18n="circ-table-title">Judul Buku</th>
                                    <th class="pb-3 font-semibold" data-i18n="circ-table-loan-date">Tgl Pinjam</th>
                                    <th class="pb-3 font-semibold" data-i18n="circ-table-due-date">Batas Kembali</th>
                                </tr>
                            </thead>
                            <tbody id="active-loans-tbody" class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-300">
                                <tr>
                                    <td colspan="3" class="text-center py-8 text-slate-400 italic">Memuat data peminjaman...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="fine-breakdown-card" class="hidden bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800">
                    <h4 class="font-bold text-sm mb-4 text-slate-700 dark:text-slate-300 uppercase tracking-wider" data-i18n="circ-fine-breakdown">Rincian Informasi Denda Berjalan</h4>
                    <div id="fine-breakdown-list" class="space-y-3">
                        <!-- Akan diisi secara dinamis -->
                    </div>
                </div>
            </div>

            <!-- TAB 4: PROFIL -->
            <div id="profil" class="tab-content max-w-xl mx-auto space-y-6">
                <!-- Profil Mahasiswa -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-950 dark:from-slate-900 dark:to-black p-6 rounded-3xl text-center text-white shadow-md relative overflow-hidden">
                    <div class="w-20 h-20 bg-slate-700 rounded-full mx-auto mb-3 border-4 border-slate-600 overflow-hidden">
                        <img src="{{ $userData->avatar_url ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.($userData->username ?? $userData->name ?? 'Ahmad') }}" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-lg">{{ $userData->name ?? 'Ahmad Fauzi' }}</h3>
                    <p class="text-xs text-slate-400">NIM: {{ $userData->nim ?? '220194850' }}</p>
                    <p class="text-xs text-slate-400">Username: {{ $userData->username ?? 'username' }}</p>
                    <span class="inline-block bg-white/10 text-[10px] font-medium px-3 py-1 rounded-full mt-3 border border-white/10">{{ $userData->fakultas ?? 'Fakultas Ilmu Komputer' }}</span>
                </div>

                <!-- Pengaturan & Fitur -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden text-sm">
                    <div class="flex justify-between items-center p-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3 font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-regular fa-moon text-lg text-indigo-500"></i> <span data-i18n="dark-mode">Tema Gelap</span>
                        </div>
                        <button onclick="toggleDarkMode()" id="dark-mode-toggle" class="w-11 h-6 bg-slate-200 dark:bg-blue-600 rounded-full relative p-1 transition-colors duration-200">
                            <div class="w-4 h-4 bg-white rounded-full absolute top-1 left-1 dark:left-6 transition-all duration-200 shadow-sm"></div>
                        </button>
                    </div>
                    
                    <div class="flex justify-between items-center p-4 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <div class="flex items-center gap-3 font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-globe text-lg text-emerald-500"></i> <span data-i18n="language">Bahasa</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <select id="language-select" onchange="changeLanguage(this.value)" class="text-xs bg-transparent text-slate-500 dark:text-slate-400 font-semibold focus:outline-none cursor-pointer border-none outline-none text-right appearance-none pr-1">
                                <option value="id" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-white">Indonesia (ID)</option>
                                <option value="en" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-white">English (EN)</option>
                            </select>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 pointer-events-none"></i>
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
                        Keluar Sesi
                    </button>
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
        <nav class="md:hidden bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex justify-around items-center absolute bottom-0 w-full z-40 h-16 transition-colors duration-200">
            <button onclick="switchTab('beranda')" class="nav-btn-mobile flex flex-col items-center p-2 text-blue-600 dark:text-blue-400" data-target="beranda">
                <i class="fa-solid fa-house text-lg mb-1"></i>
                <span class="text-[9px] font-semibold">Beranda</span>
            </button>
            <button onclick="switchTab('buku')" class="nav-btn-mobile flex flex-col items-center p-2 text-slate-400" data-target="buku">
                <i class="fa-solid fa-magnifying-glass text-lg mb-1"></i>
                <span class="text-[9px] font-semibold">Pustaka</span>
            </button>
            <button onclick="switchTab('sirkulasi')" class="nav-btn-mobile flex flex-col items-center p-2 text-slate-400" data-target="sirkulasi">
                <i class="fa-solid fa-arrows-spin text-lg mb-1"></i>
                <span class="text-[9px] font-semibold">Sirkulasi</span>
            </button>
            <button onclick="switchTab('profil')" class="nav-btn-mobile flex flex-col items-center p-2 text-slate-400" data-target="profil">
                <i class="fa-regular fa-user text-lg mb-1"></i>
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

    <!-- TOAST NOTIFICATION -->
    <div id="success-toast" class="hidden fixed bottom-20 md:bottom-6 left-1/2 -translate-x-1/2 w-[92%] max-w-[400px] bg-slate-900 dark:bg-slate-800 text-white px-4 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 z-50 animate-[fadeIn_0.3s_cubic-bezier(0.175,0.885,0.32,1.275)] border border-slate-800 dark:border-slate-700">
        <div class="bg-emerald-500 rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 shadow-md">
            <i class="fa-solid fa-check text-white text-sm"></i>
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
        const VALID_TAB_IDS = ['beranda', 'buku', 'sirkulasi', 'profil'];
        function getStoredTabId() {
            const stored = localStorage.getItem('libra-active-tab');
            return VALID_TAB_IDS.includes(stored) ? stored : 'beranda';
        }
        let activeTabId = getStoredTabId();
        let activeBookId = null;
        let userFine = 0;
        let wishlistBooks = JSON.parse(localStorage.getItem('libra-wishlist') || '[]');

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
                'logout': 'Keluar Sesi', 'show-action': 'Tampilkan',
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
                'circ-loan-date-1': '22 Juni 2026',
                'circ-due-date-1': '29 Juni 2026 (Hari Ini)',
                'circ-loan-date-2': '15 Juni 2026',
                'circ-due-date-2': '05 Juli 2026',
                'circ-fine-item-title': 'Keterlambatan Pengembalian: "Sistem Basis Data"',
                'circ-fine-item-desc': 'Terlambat 5 hari × Rp 2.000 / hari',
                'circ-fine-note': '*Silakan lakukan pembayaran denda langsung di meja loket sirkulasi perpustakaan pusat untuk mengaktifkan kembali hak peminjaman penuh Anda.',
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
                'circ-loan-date-1': '22 June 2026',
                'circ-due-date-1': '29 June 2026 (Today)',
                'circ-loan-date-2': '15 June 2026',
                'circ-due-date-2': '05 July 2026',
                'circ-fine-item-title': 'Overdue Return: "Database Systems"',
                'circ-fine-item-desc': '5 days overdue × Rp 2,000 / day',
                'circ-fine-note': '*Please complete your fine payment at the central library circulation desk to restore full borrowing privileges.',
                'modal-btn-book': 'Book / Reserve Book',
                'modal-book-disabled': 'Book is Currently Borrowed'
            }
        };

        const tabHeaderDictionary = {
            id: {
                'beranda': { title: 'Beranda', sub: 'Selamat datang kembali, Mahasiswa!' },
                'buku': { title: 'Pustaka Digital', sub: 'Cari koleksi katalog buku perpustakaan secara live' },
                'sirkulasi': { title: 'Sirkulasi', sub: 'Kelola peminjaman, tracking kurir, dan denda' },
                'profil': { title: 'Profil Pengguna', sub: 'Pengaturan akun, preferensi tema, dan masukan' }
            },
            en: {
                'beranda': { title: 'Home', sub: 'Welcome back, Student!' },
                'buku': { title: 'Digital Library', sub: 'Search library book collections live' },
                'sirkulasi': { title: 'Circulation', sub: 'Manage loans, courier tracking, and fines' },
                'profil': { title: 'User Profile', sub: 'Account settings, theme preferences, and feedback' }
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
                    btn.className = "nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-semibold bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 transition-all";
                } else {
                    btn.className = "nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all";
                }
            });

            document.querySelectorAll('.nav-btn-mobile').forEach(btn => {
                if(btn.getAttribute('data-target') === tabId) {
                    btn.className = 'nav-btn-mobile flex flex-col items-center p-2 text-blue-600 dark:text-blue-400';
                } else {
                    btn.className = 'nav-btn-mobile flex flex-col items-center p-2 text-slate-400';
                }
            });

            const headers = tabHeaderDictionary[currentLang][tabId];
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
                const response = await fetch(`/api/notifications?user_id=${user.id}`);
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
                await fetch(`/api/notifications/read-all?user_id=${user.id}`, { method: 'PATCH' });
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

        function applyTranslations() {
            document.getElementById('language-select').value = currentLang;
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
        function liveSearch() {
            const query = document.getElementById('search-input').value.toLowerCase().trim();
            const container = document.getElementById('live-library-container');
            const cards = container.querySelectorAll('.book-card');
            
            cards.forEach((card) => {
                const title = (card.dataset.title || '').toLowerCase();
                const author = (card.dataset.author || '').toLowerCase();
                const isMatch = title.includes(query) || author.includes(query);
                card.style.display = isMatch ? '' : 'none';
            });
        }

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

            // Booking button
            const bookingBtn = document.querySelector('#booking-default-btn-container button');
            if (status === 'Dipinjam') {
                bookingBtn.disabled = true;
                bookingBtn.className = 'w-full bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-sm font-semibold py-3 rounded-xl shadow-md cursor-not-allowed opacity-60';
                bookingBtn.innerHTML = `<i class="fa-solid fa-ban mr-2"></i> ${currentLang === 'id' ? 'Buku Sedang Dipinjam' : 'Book Currently Borrowed'}`;
            } else {
                bookingBtn.disabled = false;
                bookingBtn.className = 'w-full bg-slate-900 dark:bg-blue-600 text-white text-sm font-semibold py-3 rounded-xl shadow-md hover:bg-slate-800 dark:hover:bg-blue-700 active:scale-[0.98] transition-all';
                bookingBtn.innerHTML = `<i class="fa-solid fa-book-open mr-2"></i> ${currentLang === 'id' ? 'Booking & Antar ke Rumah' : 'Book & Delivery to Home'}`;
            }

            // Rekomendasi buku
            renderRecommendations(id);

            // Sinkronkan tampilan tombol wishlist (hanya muncul untuk buku Dipinjam)
            syncWishlistUI();

            // Tampilkan modal
            document.getElementById('detail-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function renderRecommendations(currentId) {
            const container = document.getElementById('modal-rekomendasi-container');
            if (!container) return;
            
            const recommendations = booksData.filter(b => b.id !== currentId).slice(0, 3);
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

        function toggleWishlist() {
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

            const idx = wishlistBooks.findIndex(id => id === activeBookId);
            const isId = currentLang === 'id';
            
            if(idx > -1) {
                wishlistBooks.splice(idx, 1);
                showToast(isId ? "Dihapus dari Wishlist" : "Removed from Wishlist", 
                        isId ? `Buku "${book.title}" berhasil dilepas.` : `Book "${book.title}" has been removed.`);
            } else {
                wishlistBooks.push(activeBookId);
                showToast(isId ? "Ditambahkan ke Wishlist" : "Added to Wishlist", 
                        isId ? `Buku "${book.title}" berhasil disimpan.` : `Book "${book.title}" has been added to your wishlist.`);
            }

            localStorage.setItem('libra-wishlist', JSON.stringify(wishlistBooks));
            renderWishlist();
            syncWishlistUI();
        }

        // Toggle wishlist langsung dari kartu buku di halaman Pustaka (tanpa buka modal)
        function toggleWishlistFromCard(id) {
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

            const title = card.dataset.title;
            const idx = wishlistBooks.findIndex(bookId => bookId === id);

            if (idx > -1) {
                wishlistBooks.splice(idx, 1);
                showToast(isId ? "Dihapus dari Wishlist" : "Removed from Wishlist",
                    isId ? `Buku "${title}" berhasil dilepas.` : `Book "${title}" has been removed.`);
            } else {
                wishlistBooks.push(id);
                showToast(isId ? "Ditambahkan ke Wishlist" : "Added to Wishlist",
                    isId ? `Buku "${title}" berhasil disimpan.` : `Book "${title}" has been added to your wishlist.`);
            }

            localStorage.setItem('libra-wishlist', JSON.stringify(wishlistBooks));
            renderWishlist();
            syncWishlistUI();
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
                if(book) {
                    container.insertAdjacentHTML('beforeend', `
                        <div onclick="openBookDetail(${book.id})" class="wishlist-card-item bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 relative cursor-pointer">
                            <button onclick="removeFromWishlist(${index}, event)" class="absolute top-2 right-2 bg-white/80 backdrop-blur-sm dark:bg-slate-900/80 w-6 h-6 rounded-full text-rose-500 flex items-center justify-center shadow-sm text-xs hover:bg-rose-50 transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <img src="${book.cover}" class="w-full aspect-[4/5] object-cover">
                            <div class="p-3">
                                <h4 class="font-bold text-xs line-clamp-1 text-slate-800 dark:text-white">${book.title}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">${book.author}</p>
                            </div>
                        </div>
                    `);
                }
            });
        }

        function removeFromWishlist(index, event) {
            event.stopPropagation();
            wishlistBooks.splice(index, 1);
            localStorage.setItem('libra-wishlist', JSON.stringify(wishlistBooks));
            renderWishlist();
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
                    // Booking sudah kadaluarsa. Penghapusan sebenarnya terjadi
                    // di backend (lihat PinjamController::cancelExpiredBookings,
                    // dijalankan tiap kali data peminjaman di-fetch). Di sini
                    // kita cukup beri tahu pengguna & minta data terbaru supaya
                    // kartu ini hilang begitu backend selesai menghapusnya.
                    if (!timerEl.dataset.expiredNotified) {
                        timerEl.dataset.expiredNotified = '1';
                        showToast(
                            'Batas Waktu Habis',
                            'Booking tidak diambil dalam waktu 2 jam dan telah dibatalkan otomatis.'
                        );
                        loadUserLoans();
                    }
                    clearInterval(circCountdownIntervals[loanId]);
                    delete circCountdownIntervals[loanId];
                    return;
                }
                const totalSeconds = Math.floor(diffMs / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                timerEl.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            };

            tick();
            circCountdownIntervals[loanId] = setInterval(tick, 1000);
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

        function showFineWidget() {
            const widget = document.getElementById('fine-widget');
            const amountEl = document.getElementById('fine-amount-display');
            if (!widget || !amountEl) return;

            if (userFine > 0) {
                amountEl.innerText = `Rp ${userFine.toLocaleString('id-ID')}`;
                widget.classList.remove('hidden');
            } else {
                const isId = currentLang === 'id';
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
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
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
            document.getElementById('toast-title').textContent = title;
            document.getElementById('toast-desc').textContent = desc;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }

        // ============================================================
        // FUNGSI BOOKING PEMINJAMAN (SUPABASE)
        // ============================================================
        let activeLoans = [];

        function openBookingForm() {
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

        async function submitBookingForm() {
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

            const confirmBtn = document.querySelector('#booking-form-section button[onclick="submitBookingForm()"]');
            const originalText = confirmBtn.textContent;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Memproses...';

            try {
                const response = await fetch('/api/pinjam', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
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
                confirmBtn.disabled = false;
                confirmBtn.textContent = originalText;
            }
        }

        async function loadUserLoans() {
            const userStr = localStorage.getItem('libra_user');
            if (!userStr) return;
            const user = JSON.parse(userStr);

            try {
                const response = await fetch(`/api/pinjam?user_id=${user.id}`);
                if (!response.ok) throw new Error('Gagal mengambil data peminjaman');
                activeLoans = await response.json();
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
                const response = await fetch('/api/pinjam');
                if (!response.ok) throw new Error('Gagal mengambil semua status peminjaman');
                const allLoans = await response.json();

                // Dapatkan ISBN buku yang sedang dipinjam (tanggal_kembali adalah null)
                const borrowedBookISBNs = allLoans
                    .filter(l => !l.tanggal_kembali)
                    .map(l => l.book_id);

                // Update status di card buku halaman pustaka
                document.querySelectorAll('#live-library-container > .book-card').forEach(card => {
                    const isbn = card.dataset.id;
                    const statusBadge = card.querySelector('.status-badge');
                    
                    if (borrowedBookISBNs.includes(isbn)) {
                        card.dataset.status = 'Dipinjam';
                        if (statusBadge) {
                            statusBadge.textContent = currentLang === 'id' ? 'Dipinjam' : 'On Loan';
                            statusBadge.className = 'status-badge inline-block mt-2 px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-[9px] font-semibold rounded-full';
                        }
                    } else {
                        card.dataset.status = 'Tersedia';
                        if (statusBadge) {
                            statusBadge.textContent = currentLang === 'id' ? 'Tersedia' : 'Available';
                            statusBadge.className = 'status-badge inline-block mt-2 px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[9px] font-semibold rounded-full';
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

            userFine = totalFine;
            
            // Render rincian denda berjalan
            const card = document.getElementById('fine-breakdown-card');
            const list = document.getElementById('fine-breakdown-list');
            if (card && list) {
                if (userFine > 0) {
                    list.innerHTML = overdueItems.map(item => `
                        <div class="p-3.5 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 rounded-xl flex justify-between items-center text-xs">
                            <div>
                                <h5 class="font-bold text-slate-800 dark:text-white">Keterlambatan Pengembalian: "${item.title}"</h5>
                                <p class="text-slate-400 mt-0.5">Terlambat ${item.days} hari × Rp 2.000 / hari</p>
                            </div>
                            <span class="font-mono font-bold text-rose-500 text-sm">Rp ${item.amount.toLocaleString('id-ID')}</span>
                        </div>
                    `).join('') + `
                        <p class="text-[11px] text-slate-400 leading-normal italic" data-i18n="circ-fine-note">*Silakan lakukan pembayaran denda langsung di meja loket sirkulasi perpustakaan pusat untuk mengaktifkan kembali hak peminjaman penuh Anda.</p>
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

        function renderActiveLoans() {
            const tbody = document.getElementById('active-loans-tbody');
            if (!tbody) return;

            // "Peminjaman Aktif" hanya untuk yang BELUM dikembalikan.
            // activeLoans berisi SELURUH riwayat peminjaman user (termasuk yang
            // sudah dikembalikan), jadi wajib disaring dulu di sini berdasarkan
            // tanggal_kembali sebelum dirender ke tabel ini.
            const stillActiveLoans = activeLoans.filter(loan => !loan.tanggal_kembali);

            if (stillActiveLoans.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center py-8 text-slate-400 italic">
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
            'Diantar Kurir': 'Sedang Diantar Kurir'
        };

        function resolveCourierStatusKey(rawStatus) {
            return COURIER_STATUS_ALIASES[rawStatus] || rawStatus;
        }

        function isCourierTrackedStatus(rawStatus) {
            const key = resolveCourierStatusKey(rawStatus);
            return COURIER_FLOW_STEPS.some(step => step.key === key);
        }

        function updateCourierStatusVisibility() {
            const container = document.getElementById('circ-status-container');
            const deadlineTpl = document.getElementById('circ-deadline-template');
            const courierTpl = document.getElementById('circ-courier-template');
            if (!container || !deadlineTpl || !courierTpl) return;

            // Bersihkan semua kartu & interval lama sebelum render ulang,
            // supaya tidak ada kartu/interval "bekas" yang menumpuk.
            clearAllCircCountdowns();
            container.innerHTML = '';

            // Booking yang belum selesai (belum dikembalikan) & masih butuh perhatian:
            // - status 'Booking' -> mahasiswa memilih ambil sendiri ke meja sirkulasi.
            // - salah satu status resmi di COURIER_FLOW_STEPS -> booking kurir yang
            //   sedang diproses admin (nilainya SELALU berasal dari database, bukan teks tetap).
            const pendingLoans = activeLoans.filter(l =>
                !l.tanggal_kembali && (l.status === 'Booking' || isCourierTrackedStatus(l.status))
            );

            pendingLoans.forEach(loan => {
                if (loan.status === 'Booking') {
                    container.appendChild(buildDeadlineCard(loan, deadlineTpl));
                } else if (isCourierTrackedStatus(loan.status)) {
                    container.appendChild(buildCourierCard(loan, courierTpl));
                }
            });

            // Terapkan bahasa aktif ke kartu-kartu yang baru saja di-clone.
            if (typeof applyTranslations === 'function') applyTranslations();
        }

        function buildDeadlineCard(loan, template) {
            const node = template.content.firstElementChild.cloneNode(true);

            const bookEl = node.querySelector('.circ-deadline-book-el');
            if (bookEl) bookEl.textContent = loan.book_title || '';

            // Belum ada kolom "batas pengambilan" tersendiri di database, jadi
            // deadline diambil dari waktu booking dibuat + jendela pengambilan 2 jam.
            // Dasarnya adalah created_at milik booking INI SAJA, sehingga tiap
            // booking punya deadline sendiri-sendiri dan tidak saling menimpa.
            const createdAt = loan.created_at ? new Date(loan.created_at) : new Date();
            const pickupWindowMs = 2 * 60 * 60 * 1000;
            const deadlineDate = new Date(createdAt.getTime() + pickupWindowMs);

            const timerEl = node.querySelector('.countdown-timer-el');
            if (timerEl) {
                startCountdownForCard(loan.id, timerEl, deadlineDate);
            }

            return node;
        }

        function buildCourierCard(loan, template) {
            const node = template.content.firstElementChild.cloneNode(true);

            const bookEl = node.querySelector('.circ-courier-book-el');
            if (bookEl) bookEl.textContent = loan.book_title || '';

            const stepsContainer = node.querySelector('.courier-steps-container');
            if (stepsContainer) {
                const isId = currentLang === 'id';
                const statusKey = resolveCourierStatusKey(loan.status);
                const currentIndex = COURIER_FLOW_STEPS.findIndex(step => step.key === statusKey);

                stepsContainer.innerHTML = COURIER_FLOW_STEPS.map((step, idx) => {
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

                // Garis vertikal timeline (dibuat ulang tiap render karena innerHTML di-reset)
                const line = document.createElement('div');
                line.className = 'absolute left-2.5 top-2 bottom-4 w-0.5 bg-slate-200 dark:bg-slate-800';
                stepsContainer.prepend(line);
            }

            return node;
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

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('libra-theme') === 'dark') document.documentElement.classList.add('dark');
            applyTranslations();
            switchTab(activeTabId);
            renderWishlist();
            syncWishlistUI();
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

        console.log('📚 Sistem Libra siap digunakan dengan Supabase via Blade!');
        console.log(`📊 Total buku: ${booksData.length}`);
    </script>
</body>
</html>