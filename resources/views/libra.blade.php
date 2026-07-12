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
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-2.5">
                            <div class="flex justify-between text-[11px] mb-1">
                                <span class="font-bold text-rose-500">Pengingat Pengembalian</span>
                                <span class="text-slate-400">2 jam lalu</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-normal">Buku "Kalkulus Jilid 1" harus dikembalikan besok pagi.</p>
                        </div>
                        <div class="pb-1">
                            <div class="flex justify-between text-[11px] mb-1">
                                <span class="font-bold text-green-500">Buku Tersedia!</span>
                                <span class="text-slate-400">5 jam lalu</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-normal">Buku "Clean Code" yang Anda tunggu sekarang siap di-booking.</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- KONTEN HALAMAN -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8 pb-24 md:pb-8">
            
            <!-- TAB 1: BERANDA -->
            <div id="beranda" class="tab-content active max-w-5xl mx-auto space-y-6">
                <h3 class="font-bold text-slate-800 dark:text-white text-base md:text-lg flex items-center gap-2 pt-2">
                    <i class="fa-solid fa-hourglass-half text-blue-500"></i> <span data-i18n="continue-reading">Lanjutkan Membaca</span>
                </h3>
                
                <div id="reading-progress" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Akan diisi secara dinamis -->
                </div>

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
                    <div class="book-card bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 hover:shadow-md transition-shadow cursor-pointer" 
                        onclick="openBookDetail('{{ $book->ISBN }}')" 
                        data-id="{{ $book->ISBN }}"
                        data-title="{{ $book->{'Book-Title'} }}"
                        data-author="{{ $book->{'Book-Author'} }}"
                        data-cover="{{ $book->{'Image-URL-M'} }}"
                        data-status="Tersedia" 
                        data-genre="Science"
                        data-rating="4.5"
                        data-desc="Deskripsi buku tidak tersedia.">
                        
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
                <div id="circ-deadline-box" class="hidden bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-400 p-2.5 rounded-xl">
                            <i class="fa-regular fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300" data-i18n="circ-deadline-title">Batas Pengambilan Resv.</h4>
                            <p class="text-xs text-amber-600 dark:text-amber-400/80" data-i18n="circ-deadline-desc">Ambil buku langsung ke meja sirkulasi perpustakaan.</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 px-4 py-2 rounded-xl border border-amber-200 dark:border-amber-800 font-mono font-bold text-amber-600 dark:text-amber-400 text-sm w-max self-end sm:self-auto">
                        <span data-i18n="circ-remaining-time">Sisa Waktu:</span> <span id="countdown-timer">01:45:00</span>
                    </div>
                </div>

                <div id="courier-status-box" class="hidden bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800">
                    <h4 class="font-bold text-sm mb-5 text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-2" data-i18n="circ-courier-status">
                        <i class="fa-solid fa-truck text-blue-500"></i> Status Pengiriman Kurir
                    </h4>
                    <div class="relative ml-2">
                        <div class="absolute left-2.5 top-2 bottom-4 w-0.5 bg-slate-200 dark:bg-slate-800"></div>

                        <div class="relative flex items-start gap-4 mb-6">
                            <div class="z-10 w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center border-4 border-white dark:border-slate-900 flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-blue-600 dark:text-blue-400" data-i18n="circ-status-1-title">Diantar Kurir</h5>
                                <p id="courier-status-1-desc" class="text-xs text-slate-400 mt-0.5">Kurir sedang menuju ke Fakultas Ilmu Komputer. Estimasi 10 menit.</p>
                            </div>
                        </div>
                        <div class="relative flex items-start gap-4 mb-6">
                            <div class="z-10 w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center border-4 border-white dark:border-slate-900 flex-shrink-0">
                                <i class="fa-solid fa-check text-[8px] text-white"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-slate-600 dark:text-slate-400" data-i18n="circ-status-2-title">Buku Selesai Dikemas</h5>
                                <p class="text-xs text-slate-400 mt-0.5" data-i18n="circ-status-2-desc">Buku telah diserahkan ke kurir internal.</p>
                            </div>
                        </div>
                        <div class="relative flex items-start gap-4">
                            <div class="z-10 w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center border-4 border-white dark:border-slate-900 flex-shrink-0">
                                <i class="fa-solid fa-check text-[8px] text-white"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-slate-600 dark:text-slate-400" data-i18n="circ-status-3-title">Booking Dikonfirmasi</h5>
                                <p class="text-xs text-slate-400 mt-0.5" data-i18n="circ-status-3-desc">Permintaan disetujui oleh sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>

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

                    <!-- Formulir Donasi Buku -->
                    <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fa-solid fa-hand-holding-heart text-blue-500 text-sm"></i>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white" data-i18n="donate-form-title">Formulir Donasi Buku</h4>
                        </div>
                        <p class="text-xs text-slate-400 leading-normal mb-4" data-i18n="donate-form-desc">Bantu perluas literasi kampus dengan mendonasikan buku layak bacamu ke koleksi Sistem Libra.</p>
                        
                        <form id="donation-form" onsubmit="event.preventDefault(); submitDonation();" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1" data-i18n="donate-title">Judul Buku</label>
                                    <input type="text" id="donate-book-title" required class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1" data-i18n="donate-author">Penulis / Pengarang</label>
                                    <input type="text" id="donate-book-author" required class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white transition-colors">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1" data-i18n="donate-category-label">Kategori</label>
                                    <select id="donate-book-category" class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white">
                                        <option value="Science" data-i18n="donate-cat-science">Sains & Teknologi</option>
                                        <option value="Romance" data-i18n="donate-cat-romance">Fiksi / Novel</option>
                                        <option value="Action" data-i18n="donate-cat-action">Komik / Petualangan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1" data-i18n="donate-condition-label">Kondisi Buku</label>
                                    <select id="donate-book-condition" class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white">
                                        <option value="Sangat Baik" data-i18n="donate-cond-verygood">Sangat Baik (Seperti Baru)</option>
                                        <option value="Baik" data-i18n="donate-cond-good">Baik (Ada Sedikit Lecet)</option>
                                        <option value="Cukup" data-i18n="donate-cond-fair">Cukup Layak</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1" data-i18n="donate-notes-label">Catatan Tambahan (Opsional)</label>
                                <textarea id="donate-book-note" rows="2" class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white resize-none"></textarea>
                            </div>
                            <button type="submit" data-i18n="donate-btn-submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-md transition-colors">
                                Ajukan Donasi Buku
                            </button>
                        </form>

                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <h5 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" data-i18n="donate-history-title">Riwayat Donasi Kamu</h5>
                            <div id="donation-history-container" class="space-y-3"></div>
                        </div>
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
                <p id="fine-amount-display" class="text-xs font-bold">Rp 10.000</p>
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
                            <input type="radio" name="pickup-option" value="Diantar Kurir" class="accent-blue-500">
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
        let activeTabId = 'beranda';
        let activeBookId = null;
        let userFine = 10000;
        let wishlistBooks = JSON.parse(localStorage.getItem('libra-wishlist') || '[]');
        let userDonations = JSON.parse(localStorage.getItem('libra-donations') || '[]');

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
                'continue-reading': 'Lanjutkan Membaca', 'my-wishlist': 'Wishlist Saya', 'recommended': 'Direkomendasikan Untukmu',
                'dark-mode': 'Tema Gelap', 'language': 'Bahasa', 'fine-status': 'Status Informasi Denda', 
                'logout': 'Keluar Sesi', 'show-action': 'Tampilkan',
                'wishlist-empty': 'Belum ada buku di wishlist kamu.',
                'donate-form-title': 'Formulir Donasi Buku', 'donate-form-desc': 'Bantu perluas literasi kampus dengan mendonasikan buku layak bacamu ke koleksi Sistem Libra.',
                'donate-title': 'Judul Buku', 'donate-author': 'Penulis / Pengarang', 'donate-empty': 'Belum ada riwayat pengajuan donasi.',
                'feedback-title': 'Kirim Masukan / Feedback', 
                'feedback-desc': 'Bantu kami meningkatkan Sistem Libra dengan memberikan saran atau laporan kendala Anda.',
                'feedback-label-cat': 'Kategori Masukan',
                'feedback-opt-saran': 'Saran & Fitur Baru', 'feedback-opt-bug': 'Laporan Bug / Error', 
                'feedback-opt-pelayanan': 'Fasilitas Perpustakaan', 'feedback-opt-lainnya': 'Lainnya',
                'feedback-label-msg': 'Pesan Anda', 'feedback-btn-submit': 'Kirim Feedback',
                'donate-category-label': 'Kategori',
                'donate-cat-science': 'Sains & Teknologi', 'donate-cat-romance': 'Fiksi / Novel', 'donate-cat-action': 'Komik / Petualangan',
                'donate-condition-label': 'Kondisi Buku',
                'donate-cond-verygood': 'Sangat Baik (Seperti Baru)', 'donate-cond-good': 'Baik (Ada Sedikit Lecet)', 'donate-cond-fair': 'Cukup Layak',
                'donate-notes-label': 'Catatan Tambahan (Opsional)', 'donate-btn-submit': 'Ajukan Donasi Buku', 'donate-history-title': 'Riwayat Donasi Kamu',
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
                'continue-reading': 'Continue Reading', 'my-wishlist': 'My Wishlist', 'recommended': 'Recommended for You',
                'dark-mode': 'Dark Mode', 'language': 'Language', 'fine-status': 'Fine Information Status', 
                'logout': 'Logout', 'show-action': 'Show',
                'wishlist-empty': 'No books in your wishlist yet.',
                'donate-form-title': 'Book Donation Form', 'donate-form-desc': 'Help expand campus literacy by donating your readable books to the Libra System collection.',
                'donate-title': 'Book Title', 'donate-author': 'Author / Writer', 'donate-empty': 'No donation application history yet.',
                'feedback-title': 'Submit Feedback',
                'feedback-desc': 'Help us improve the Libra System by providing your suggestions or reporting your issues.',
                'feedback-label-cat': 'Feedback Category',
                'feedback-opt-saran': 'Suggestions & New Features', 'feedback-opt-bug': 'Bug Report / Error',
                'feedback-opt-pelayanan': 'Library Facilities', 'feedback-opt-lainnya': 'Others',
                'feedback-label-msg': 'Your Message', 'feedback-btn-submit': 'Submit Feedback',
                'donate-category-label': 'Category',
                'donate-cat-science': 'Science & Technology', 'donate-cat-romance': 'Fiction / Novel', 'donate-cat-action': 'Comic / Adventure',
                'donate-condition-label': 'Book Condition',
                'donate-cond-verygood': 'Very Good (Like New)', 'donate-cond-good': 'Good (Minor Scratches)', 'donate-cond-fair': 'Fair / Readable',
                'donate-notes-label': 'Additional Notes (Optional)', 'donate-btn-submit': 'Submit Book Donation', 'donate-history-title': 'Your Donation History',
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
                'profil': { title: 'Profil Pengguna', sub: 'Pengaturan akun, preferensi tema, donasi, dan masukan' }
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
            if (tabId === 'profil') {
                loadUserDonations();
            }
            if (tabId === 'beranda') {
                renderWishlist();
                renderRecommendedBooks();
            }
            if (tabId === 'sirkulasi') {
                loadUserLoans();
            }
        }

        function toggleNotif(event) { 
            if(event) event.stopPropagation();
            document.getElementById('notif-panel').classList.toggle('hidden'); 
        }

        function clearNotifications() { 
            document.getElementById('notif-panel').classList.add('hidden');
            const badge = document.getElementById('notif-badge');
            if (badge) badge.classList.add('hidden');
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
        // FUNGSI BAHASA
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
        function toggleWishlist() {
            const book = booksData.find(b => b.id === activeBookId);
            if(!book) return;

            if (book.status !== 'Dipinjam') {
                showToast(currentLang === 'id' ? 'Info' : 'Info', 
                    currentLang === 'id' ? 'Wishlist hanya untuk buku yang sedang dipinjam.' : 'Wishlist is only for borrowed books.');
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
        function renderReadingProgress() {
            const container = document.getElementById('reading-progress');
            if (!container) return;
            
            const progressData = [
                { title: "Metodologi Penelitian Bisnis", author: "Prof. Dr. Sugiyono", progress: 75, cover: "https://images.unsplash.com/photo-1512820790803-83ca734da794?w=150&auto=format&fit=crop&q=60" },
                { title: "Artificial Intelligence: Modern Approach", author: "Stuart Russell", progress: 30, cover: "https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=150&auto=format&fit=crop&q=60" }
            ];

            container.innerHTML = progressData.map(item => `
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 flex gap-4 items-center">
                    <div class="w-14 h-20 rounded-lg bg-blue-100 overflow-hidden flex-shrink-0">
                        <img src="${item.cover}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm line-clamp-1">${item.title}</h4>
                        <p class="text-xs text-slate-400 mt-0.5 mb-2">${item.author}</p>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                            <div class="bg-blue-500 h-1.5 rounded-full progress-bar" style="width: ${item.progress}%"></div>
                        </div>
                        <span class="text-[9px] text-slate-400 block text-right mt-1">${item.progress}% Selesai</span>
                    </div>
                </div>
            `).join('');
        }

        // ============================================================
        // FUNGSI SIRKULASI
        // ============================================================
        function updateCourierStatusVisibility() {
            const box = document.getElementById('courier-status-box');
            const deadlineBox = document.getElementById('circ-deadline-box');
            const isActive = localStorage.getItem('libra-courier-active') === 'true';

            if (box) {
                box.classList.toggle('hidden', !isActive);
                if (isActive) {
                    const location = localStorage.getItem('libra-courier-location') || 'current';
                    const descEl = document.getElementById('courier-status-1-desc');
                    const dict = langDictionary[currentLang];
                    if (descEl) descEl.innerText = dict[`circ-status-1-desc-${location}`] || dict['circ-status-1-desc'];
                }
            }

            if (deadlineBox) {
                deadlineBox.classList.toggle('hidden', isActive);
                if (!isActive) {
                    startCountdown();
                }
            }
        }

        function startCountdown() {
            let time = 6300;
            const timerElement = document.getElementById('countdown-timer');
            if (!timerElement) return;

            const interval = setInterval(() => {
                const hours = Math.floor(time / 3600);
                const minutes = Math.floor((time % 3600) / 60);
                const seconds = time % 60;
                timerElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                
                if (time <= 0) {
                    clearInterval(interval);
                    timerElement.textContent = '00:00:00';
                }
                time--;
            }, 1000);
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
        // FUNGSI DONASI
        // ============================================================
        async function loadUserDonations() {
            const userStr = localStorage.getItem('libra_user');
            if (!userStr) return;
            const user = JSON.parse(userStr);

            try {
                const response = await fetch(`/api/donations?user_id=${encodeURIComponent(user.id)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (response.ok) {
                    userDonations = await response.json();
                    renderDonationHistory();
                }
            } catch (err) {
                console.error("Gagal memuat riwayat donasi:", err);
            }
        }

        async function submitDonation() {
            const userStr = localStorage.getItem('libra_user');
            if (!userStr) {
                alert('Sesi habis. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }
            const user = JSON.parse(userStr);

            const titleInp = document.getElementById('donate-book-title');
            const authorInp = document.getElementById('donate-book-author');
            const categoryInp = document.getElementById('donate-book-category');
            const conditionInp = document.getElementById('donate-book-condition');
            const noteInp = document.getElementById('donate-book-note');

            if (!titleInp.value.trim() || !authorInp.value.trim()) {
                const isId = currentLang === 'id';
                alert(isId ? 'Harap isi judul dan penulis buku!' : 'Please fill in the book title and author!');
                return;
            }

            const submitBtn = document.querySelector('#donation-form button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = currentLang === 'id' ? 'Mengirim...' : 'Sending...';
            }

            const now = new Date();
            const formattedDate = now.toISOString().slice(0, 10); // Format YYYY-MM-DD

            try {
                const response = await fetch('/api/donations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: user.id,
                        title: titleInp.value.trim(),
                        author: authorInp.value.trim(),
                        category: categoryInp.value,
                        condition: conditionInp.value,
                        note: noteInp.value.trim(),
                        tanggal_donasi: formattedDate
                    })
                });

                if (!response.ok) {
                    const errData = await response.json();
                    throw new Error(errData.message || 'Gagal mengirim donasi');
                }

                titleInp.value = '';
                authorInp.value = '';
                noteInp.value = '';
                categoryInp.selectedIndex = 0;
                conditionInp.selectedIndex = 0;

                await loadUserDonations();

                const isId = currentLang === 'id';
                showToast(isId ? "Donasi Diajukan!" : "Donation Submitted!", 
                        isId ? "Terima kasih! Pengajuan donasi Anda berhasil dikirim ke admin perpustakaan." 
                        : "Thank you! Your donation request has been sent successfully.");
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

        function renderDonationHistory() {
            const container = document.getElementById('donation-history-container');
            if (!container) return;
            
            if (userDonations.length === 0) {
                const emptyText = currentLang === 'id' ? "Belum ada riwayat donasi buku." : "No book donation history yet.";
                container.innerHTML = `<p class="text-xs text-slate-400 py-4 text-center italic">${emptyText}</p>`;
                return;
            }

            container.innerHTML = '';
            userDonations.forEach(donasi => {
                const rawDate = donasi.tanggal_donasi || donasi.date || '';
                let formattedDate = rawDate;
                if (rawDate) {
                    try {
                        const d = new Date(rawDate);
                        formattedDate = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    } catch(e) {}
                }
                const statusText = donasi.status === "Menunggu Verifikasi" ? (currentLang === 'id' ? "Menunggu Verifikasi" : "Pending Verification") : donasi.status;
                
                container.insertAdjacentHTML('beforeend', `
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/50 dark:border-slate-800 flex justify-between items-center">
                        <div>
                            <h6 class="text-xs font-bold text-slate-800 dark:text-white line-clamp-1">${donasi.title}</h6>
                            <p class="text-[10px] text-slate-400 mt-0.5">${donasi.author} • ${formattedDate}</p>
                        </div>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-500 dark:bg-amber-950/40 border border-amber-200/50 dark:border-amber-900/40">${statusText}</span>
                    </div>
                `);
            });
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
            
            // Tampilkan form peminjaman
            document.getElementById('booking-form-section').classList.remove('hidden');
        }

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
                    throw new Error(data.message || 'Gagal menyimpan peminjaman');
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

            if (activeLoans.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center py-8 text-slate-400 italic">
                            Belum ada peminjaman aktif.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = activeLoans.map(loan => {
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

        function updateCourierStatusVisibility() {
            const box = document.getElementById('courier-status-box');
            const deadlineBox = document.getElementById('circ-deadline-box');

            const hasDelivery = activeLoans.some(l => l.status === 'Diantar Kurir');
            const hasPickup = activeLoans.some(l => l.status === 'Booking');

            if (box) {
                box.classList.toggle('hidden', !hasDelivery);
            }

            if (deadlineBox) {
                deadlineBox.classList.toggle('hidden', !hasPickup);
                if (hasPickup) {
                    startCountdown();
                }
            }
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
            loadUserDonations();
            renderWishlist();
            renderRecommendedBooks();
            renderReadingProgress();
            loadUserLoans();
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