<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Libra - Dashboard Pengguna</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght=300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col md:flex-row h-screen overflow-hidden transition-colors duration-200">

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

    <main class="flex-1 flex flex-col h-full relative overflow-hidden">
        
        <header class="bg-slate-900 dark:bg-slate-950 text-white px-6 py-4 flex items-center justify-between shadow-md z-20">
            <div>
                <h2 id="header-title" class="font-bold text-lg md:text-xl tracking-wide">Beranda</h2>
                <p id="header-subtitle" class="text-xs text-slate-400">Selamat datang kembali, Mahasiswa!</p>
            </div>
            
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
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8">
            
            <div id="beranda" class="tab-content active max-w-5xl mx-auto space-y-6">
                <h3 class="font-bold text-slate-800 dark:text-white text-base md:text-lg flex items-center gap-2 pt-2">
                    <i class="fa-solid fa-heart text-rose-500"></i> <span data-i18n="my-wishlist">Wishlist Saya</span>
                </h3>
                <div id="wishlist-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <p id="wishlist-empty" data-i18n="wishlist-empty" class="col-span-full text-xs text-slate-400 py-6 bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 text-center">Belum ada buku di wishlist kamu.</p>
                </div>

                <h3 class="font-bold text-slate-800 dark:text-white text-base md:text-lg flex items-center gap-2 pt-2">
                    <i class="fa-solid fa-star text-amber-500"></i> <span data-i18n="recommended">Direkomendasikan Untukmu</span>
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 cursor-pointer" onclick="openBookModal(1)">
                        <img src="https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=300&auto=format&fit=crop&q=60" class="w-full aspect-[4/5] object-cover">
                        <div class="p-3">
                            <h4 class="font-bold text-xs md:text-sm text-slate-800 dark:text-white line-clamp-1 tracking-tight">Data Science for Beginners</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Andrew Ng</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 cursor-pointer" onclick="openBookModal(2)">
                        <img src="https://images.unsplash.com/photo-1614849963640-9cc74b2a826f?w=300&auto=format&fit=crop&q=60" class="w-full aspect-[4/5] object-cover">
                        <div class="p-3">
                            <h4 class="font-bold text-xs md:text-sm text-slate-800 dark:text-white line-clamp-1 tracking-tight">The Pragmatic Programmer</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Andy Hunt</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="buku" class="tab-content max-w-5xl mx-auto space-y-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="search-input" oninput="liveSearch()" placeholder="Cari judul buku secara live..." class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 focus:outline-none focus:border-blue-500 dark:focus:border-blue-500 shadow-sm text-sm transition-colors text-slate-800 dark:text-white">
                    </div>
                    <div class="flex items-center gap-2 bg-white dark:bg-slate-900 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm sm:w-48">
                        <i class="fa-solid fa-star text-amber-500 text-sm"></i>
                        <select id="rating-filter-select" onchange="filterRating(this.value)" class="text-xs bg-transparent text-slate-600 dark:text-slate-300 font-semibold focus:outline-none cursor-pointer w-full border-none outline-none appearance-none">
                            <option value="0" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-white">Semua Rating</option>
                            <option value="4.5" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-white">4.5+ Bintang</option>
                            <option value="4.0" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-white">4.0+ Bintang</option>
                            <option value="3.5" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-white">3.5+ Bintang</option>
                        </select>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 pointer-events-none ml-auto"></i>
                    </div>
                </div>
                
                <div class="flex gap-2 overflow-x-auto hide-scrollbar snap-x py-1">
                    <button onclick="filterGenre('Semua', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-blue-600 text-white text-xs font-semibold rounded-full shadow-sm shadow-blue-500/20 transition-all" data-i18n="all-genre">Semua</button>
                    <button onclick="filterGenre('Romance', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Romance</button>
                    <button onclick="filterGenre('Action', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Action</button>
                    <button onclick="filterGenre('Science', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Science</button>
                </div>

                <div id="live-library-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"></div>
            </div>

            <div id="sirkulasi" class="tab-content max-w-3xl mx-auto space-y-6">
                <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-400 p-2.5 rounded-xl">
                            <i class="fa-regular fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300" data-i18n="circ-deadline-title">Batas Pengambilan Resv.</h4>
                            <p class="text-xs text-amber-600 dark:text-amber-400/80" data-i18n="circ-deadline-desc">Ambil ke meja sirkulasi atau panggil kurir antar.</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 px-4 py-2 rounded-xl border border-amber-200 dark:border-amber-800 font-mono font-bold text-amber-600 dark:text-amber-400 text-sm w-max self-end sm:self-auto">
                        <span data-i18n="circ-remaining-time">Sisa Waktu:</span> 01:45:00
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800">
                    <h4 class="font-bold text-sm mb-5 text-slate-700 dark:text-slate-300 uppercase tracking-wider" data-i18n="circ-courier-status">Status Pengiriman Kurir</h4>
                    <div class="relative ml-2">
                        <div class="absolute left-2.5 top-2 bottom-4 w-0.5 bg-slate-200 dark:bg-slate-800"></div>

                        <div class="relative flex items-start gap-4 mb-6">
                            <div class="z-10 w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center border-4 border-white dark:border-slate-900 flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-blue-600 dark:text-blue-400" data-i18n="circ-status-1-title">Diantar Kurir</h5>
                                <p class="text-xs text-slate-400 mt-0.5" data-i18n="circ-status-1-desc">Kurir sedang menuju ke Fakultas Ilmu Komputer. Estimasi 10 menit.</p>
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
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-300">
                                <tr>
                                    <td class="py-3.5 font-semibold text-slate-800 dark:text-white">Clean Code: Handbook of Agile Software Craftsmanship</td>
                                    <td class="py-3.5" data-i18n="circ-loan-date-1">22 Juni 2026</td>
                                    <td class="py-3.5 text-rose-500 font-semibold" data-i18n="circ-due-date-1">29 Juni 2026 (Hari Ini)</td>
                                </tr>
                                <tr>
                                    <td class="py-3.5 font-semibold text-slate-800 dark:text-white">Introduction to Algorithms</td>
                                    <td class="py-3.5" data-i18n="circ-loan-date-2">15 Juni 2026</td>
                                    <td class="py-3.5 text-slate-400" data-i18n="circ-due-date-2">05 Juli 2026</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800">
                    <h4 class="font-bold text-sm mb-4 text-slate-700 dark:text-slate-300 uppercase tracking-wider" data-i18n="circ-fine-breakdown">Rincian Informasi Denda Berjalan</h4>
                    <div class="space-y-3">
                        <div class="p-3.5 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 rounded-xl flex justify-between items-center text-xs">
                            <div>
                                <h5 class="font-bold text-slate-800 dark:text-white" data-i18n="circ-fine-item-title">Keterlambatan Pengembalian: "Sistem Basis Data"</h5>
                                <p class="text-slate-400 mt-0.5" data-i18n="circ-fine-item-desc">Terlambat 5 hari × Rp 5.000 / hari</p>
                            </div>
                            <span class="font-mono font-bold text-rose-500 text-sm">Rp 25.000</span>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-normal italic" data-i18n="circ-fine-note">*Silakan lakukan pembayaran denda langsung di meja loket sirkulasi perpustakaan pusat untuk mengaktifkan kembali hak peminjaman penuh Anda.</p>
                    </div>
                </div>
            </div>

            <div id="profil" class="tab-content max-w-xl mx-auto space-y-6">
                <div class="bg-gradient-to-br from-slate-800 to-slate-950 dark:from-slate-900 dark:to-black p-6 rounded-3xl text-center text-white shadow-md relative overflow-hidden">
                    <div class="w-20 h-20 bg-slate-700 rounded-full mx-auto mb-3 border-4 border-slate-600 overflow-hidden">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Ahmad" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-lg">Ahmad Fauzi</h3>
                    <p class="text-xs text-slate-400">NIM: 220194850</p>
                    <span class="inline-block bg-white/10 text-[10px] font-medium px-3 py-1 rounded-full mt-3 border border-white/10">Fakultas Ilmu Komputer</span>
                </div>

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
                    <button onclick="logoutSesi()" class="w-full p-4 text-center font-bold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors" data-i18n="logout">
                        Keluar Sesi
                    </button>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-comment-dots text-blue-500 text-sm"></i>
                        <h4 data-i18n="feedback-title" class="font-bold text-sm md:text-base text-slate-800 dark:text-white">Kirim Masukan / Feedback</h4>
                    </div>
                    <p data-i18n="feedback-desc" class="text-xs text-slate-400 mb-4">Bantu kami meningkatkan Sistem Libra dengan memberikan saran atau laporan kendala Anda.</p>
                    
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

                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 space-y-4">
                    <h4 class="font-bold text-base text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-hand-holding-heart text-blue-500"></i> <span data-i18n="donate-form-title">Formulir Donasi Buku</span>
                    </h4>
                    <p class="text-xs text-slate-400 leading-normal" data-i18n="donate-form-desc">Bantu perluas literasi kampus dengan mendonasikan buku layak bacamu ke koleksi Sistem Libra.</p>
                    
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

                    <div class="mt-6 border-t border-slate-100 dark:border-slate-800 pt-4">
                        <h5 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" data-i18n="donate-history-title">Riwayat Donasi Kamu</h5>
                        <div id="donation-history-container" class="space-y-3"></div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <div id="fine-widget" class="hidden fixed bottom-6 left-6 md:left-72 z-40 bg-white dark:bg-slate-900 border border-rose-100 dark:border-rose-950 p-4 rounded-2xl shadow-2xl flex items-center gap-4 max-w-xs md:max-w-sm animate-fadeIn">
        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center text-lg flex-shrink-0">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="flex-1 pr-1">
            <h5 class="font-bold text-xs md:text-sm text-slate-800 dark:text-white" data-i18n="fine-widget-title">Tagihan Denda!</h5>
            <p class="text-[11px] text-slate-400 mt-0.5 leading-normal">
                <span data-i18n="fine-widget-desc">Total denda kamu saat ini:</span> 
                <span id="fine-widget-amount" class="font-bold text-rose-500">Rp 0</span>
            </p>
        </div>
        <button onclick="hideFineWidget()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 text-sm p-1 flex-shrink-0 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div id="book-modal" onclick="if(event.target === this) closeBookModal()" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fadeIn">
        <div class="bg-white dark:bg-slate-900 max-w-2xl w-full rounded-3xl overflow-hidden shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col md:flex-row relative">
            <button onclick="closeBookModal()" class="absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-slate-900/40 text-white flex items-center justify-center hover:bg-slate-900/60 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="w-full md:w-2/5 bg-slate-100 dark:bg-slate-950">
                <img id="modal-cover" class="w-full h-full object-cover aspect-[4/5] md:aspect-auto md:h-[420px]" src="">
            </div>
            <div class="p-6 md:p-8 flex-1 flex flex-col justify-between">
                <div>
                    <span id="modal-genre" class="inline-block px-2.5 py-1 bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 font-semibold text-[10px] rounded-md tracking-wide uppercase"></span>
                    <h3 id="modal-title" class="font-bold text-lg md:text-xl text-slate-800 dark:text-white mt-2 tracking-tight"></h3>
                    <p id="modal-author" class="text-xs text-slate-400 mt-0.5"></p>
                    
                    <div class="flex items-center gap-4 mt-4 text-xs font-medium text-slate-500 border-y border-slate-100 dark:border-slate-800 py-2.5">
                        <div class="flex items-center gap-1"><i class="fa-solid fa-star text-amber-500"></i> <span id="modal-rating" class="font-bold text-slate-700 dark:text-white"></span></div>
                        <div class="w-px h-3 bg-slate-200 dark:bg-slate-800"></div>
                        <div>Status: <span id="modal-status-badge" class="font-bold"></span></div>
                    </div>
                    <p id="modal-desc" class="text-xs text-slate-400 mt-4 leading-relaxed line-clamp-4"></p>

                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300" data-i18n="modal-method-label">Metode Pengambilan</label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <label class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-3 py-2.5 rounded-xl text-xs text-slate-700 dark:text-slate-300 cursor-pointer flex-1 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">
                                <input type="radio" name="pickup-method" value="self" checked class="text-blue-600 focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700">
                                <span data-i18n="modal-method-self">Ambil Sendiri</span>
                            </label>
                            <label class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-3 py-2.5 rounded-xl text-xs text-slate-700 dark:text-slate-300 cursor-pointer flex-1 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">
                                <input type="radio" name="pickup-method" value="delivery" class="text-blue-600 focus:ring-blue-500 dark:bg-slate-900 dark:border-slate-700">
                                <span data-i18n="modal-method-delivery">Diantar Kurir</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 mt-6">
                    <button onclick="toggleWishlist()" id="modal-wishlist-btn" class="w-full text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-2 border"></button>
                    <button onclick="executeBooking()" id="modal-booking-btn" class="w-full text-white text-xs font-bold py-3 rounded-xl shadow-sm transition-colors" data-i18n="modal-btn-book"></button>
                </div>
            </div>
        </div>
    </div>

    <div id="success-toast" class="hidden fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-4 rounded-2xl shadow-2xl border border-slate-800 flex items-center gap-3.5 max-w-sm animate-fadeIn">
        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg flex-shrink-0">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <h5 id="toast-title" class="font-bold text-xs md:text-sm text-white">Aksi Berhasil!</h5>
            <p id="toast-desc" class="text-[11px] text-slate-400 mt-0.5 leading-normal"></p>
        </div>
    </div>

    <script>
        const booksData = [
            { id: 1, title: "Data Science for Beginners", author: "Andrew Ng", genre: "Science", rating: 4.8, status: "Tersedia", desc: "Panduan lengkap langkah demi langkah bagi pemula untuk menguasai konsep dasar sains data, analitik statistik, dan visualisasi data interaktif menggunakan ekosistem Python modern.", cover: "https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=500&auto=format&fit=crop&q=60" },
            { id: 2, title: "The Pragmatic Programmer", author: "Andy Hunt", genre: "Science", rating: 4.9, status: "Tersedia", desc: "Kitab wajib bagi para software engineer untuk mengasah pola pikir komputasional, efisiensi penulisan struktur kode, dan arsitektur pengembangan perangkat lunak pragmatis.", cover: "https://images.unsplash.com/photo-1614849963640-9cc74b2a826f?w=500&auto=format&fit=crop&q=60" },
            { id: 3, title: "Love under the Stars", author: "Jane Doe", genre: "Romance", rating: 4.2, status: "Dipinjam", desc: "Kisah romansa fiksi ilmiah manis yang mempertemukan dua astronom muda di tengah misi observasi rasi bintang kutub utara yang dipenuhi kejutan emosional.", cover: "https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=500&auto=format&fit=crop&q=60" },
            { id: 4, title: "Cyber-Strike Evolution", author: "John Mauric", genre: "Action", rating: 4.6, status: "Tersedia", desc: "Petualangan mendebarkan sekelompok peretas etis dalam menggagalkan konspirasi global penyebaran malware kecerdasan buatan super masif.", cover: "https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=500&auto=format&fit=crop&q=60" }
        ];

        let currentLang = localStorage.getItem('libra-lang') || 'id';
        let activeTabId = 'beranda';
        let currentSelectedGenre = 'Semua';
        let activeBookId = null;
        
        let userFine = 25000; 

        let wishlistBooks = JSON.parse(localStorage.getItem('libra-wishlist') || '[]');
        let userDonations = JSON.parse(localStorage.getItem('libra-donations') || '[]');

        // KAMUS TRANSLASI BAHASA INTERNASIONALISASI
        const langDictionary = {
            id: {
                'nav-beranda': 'Beranda', 'nav-pustaka': 'Pustaka', 'nav-sirkulasi': 'Sirkulasi', 'nav-profil': 'Profil',
                'my-wishlist': 'Wishlist Saya', 'recommended': 'Direkomendasikan Untukmu', 'dark-mode': 'Tema Gelap',
                'language': 'Bahasa', 'fine-status': 'Status Informasi Denda', 'logout': 'Keluar Sesi', 'show-action': 'Tampilkan',
                'wishlist-empty': 'Belum ada buku di wishlist kamu.', 'all-genre': 'Semua', 'available': 'Tersedia', 'borrowed': 'Dipinjam',
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
                'fine-widget-title': 'Tagihan Denda!', 'fine-widget-desc': 'Total denda kamu saat ini:',
                'circ-active-loans': 'Peminjaman Aktif Saya', 'circ-fine-breakdown': 'Rincian Informasi Denda Berjalan',
                
                'circ-deadline-title': 'Batas Pengambilan Resv.',
                'circ-deadline-desc': 'Ambil ke meja sirkulasi atau panggil kurir antar.',
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
                'circ-fine-item-desc': 'Terlambat 5 hari × Rp 5.000 / hari',
                'circ-fine-note': '*Silakan lakukan pembayaran denda langsung di meja loket sirkulasi perpustakaan pusat untuk mengaktifkan kembali hak peminjaman penuh Anda.',

                'modal-method-label': 'Metode Pengambilan',
                'modal-method-self': 'Ambil Sendiri',
                'modal-method-delivery': 'Diantar Kurir',
                'modal-btn-book': 'Ambil / Booking Buku',
                
                'modal-wishlist-disabled': 'Wishlist Hanya untuk Buku Dipinjam',
                'modal-wishlist-add': 'Simpan ke Wishlist',
                'modal-wishlist-remove': 'Batalkan Wishlist',
                'modal-book-disabled': 'Buku Sedang Dipinjam'
            },
            en: {
                'nav-beranda': 'Home', 'nav-pustaka': 'Library', 'nav-sirkulasi': 'Circulation', 'nav-profil': 'Profile',
                'my-wishlist': 'My Wishlist', 'recommended': 'Recommended for You', 'dark-mode': 'Dark Mode',
                'language': 'Language', 'fine-status': 'Fine Information Status', 'logout': 'Logout', 'show-action': 'Show',
                'wishlist-empty': 'No books in your wishlist yet.', 'all-genre': 'All', 'available': 'Available', 'borrowed': 'On Loan',
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
                'fine-widget-title': 'Fine Outstanding!', 'fine-widget-desc': 'Your current total fine:',
                'circ-active-loans': 'My Active Loans', 'circ-fine-breakdown': 'Current Fine Breakdown',
                
                'circ-deadline-title': 'Reservation Pickup Deadline',
                'circ-deadline-desc': 'Pick up at the circulation desk or request an internal courier.',
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
                'circ-fine-item-desc': '5 days overdue × Rp 5,000 / day',
                'circ-fine-note': '*Please complete your fine payment at the central library circulation desk to restore full borrowing privileges.',

                'modal-method-label': 'Pickup Method',
                'modal-method-self': 'Self Pickup',
                'modal-method-delivery': 'Courier Delivery',
                'modal-btn-book': 'Book / Reserve Book',
                
                'modal-wishlist-disabled': 'Wishlist Only for Borrowed Books',
                'modal-wishlist-add': 'Add to Wishlist',
                'modal-wishlist-remove': 'Remove from Wishlist',
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

            const headers = tabHeaderDictionary[currentLang][tabId];
            document.getElementById('header-title').innerText = headers.title;
            document.getElementById('header-subtitle').innerText = headers.sub;
        }

        function changeLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('libra-lang', lang);
            applyTranslations();
            switchTab(activeTabId);
            fetchLibraryData(currentSelectedGenre);
            renderDonationHistory();
            renderWishlist();
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

        function showFineWidget() {
            const widget = document.getElementById('fine-widget');
            const amountEl = document.getElementById('fine-widget-amount');
            if (!widget || !amountEl) return;

            if (userFine > 0) {
                amountEl.innerText = `Rp ${userFine.toLocaleString('id-ID')}`;
                widget.classList.remove('hidden');
            } else {
                alert(currentLang === 'id' ? "Akun Anda bersih dari denda!" : "Your account is free of fines!");
            }
        }

        function hideFineWidget() {
            const widget = document.getElementById('fine-widget');
            if (widget) widget.classList.add('hidden');
        }

        function fetchLibraryData(genre) {
            const container = document.getElementById('live-library-container');
            if(!container) return;
            container.innerHTML = '';

            let filtered = booksData;
            if(genre !== 'Semua') {
                filtered = booksData.filter(b => b.genre === genre);
            }

            filtered.forEach(book => {
                const statusTxt = book.status === 'Tersedia' ? (currentLang === 'id' ? 'Tersedia' : 'Available') : (currentLang === 'id' ? 'Dipinjam' : 'On Loan');
                const badgeColor = book.status === 'Tersedia' ? 'bg-green-50 text-green-600 dark:bg-green-950/30 dark:text-green-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400';

                container.insertAdjacentHTML('beforeend', `
                    <div onclick="openBookModal(${book.id})" class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 cursor-pointer group hover:shadow-md transition-all">
                        <img src="${book.cover}" class="w-full aspect-[4/5] object-cover group-hover:scale-102 transition-transform duration-200">
                        <div class="p-3">
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-md ${badgeColor}">${statusTxt}</span>
                            <h4 class="font-bold text-xs md:text-sm text-slate-800 dark:text-white line-clamp-1 tracking-tight mt-2">${book.title}</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">${book.author}</p>
                        </div>
                    </div>
                `);
            });
        }

        function filterGenre(genre, element) {
            currentSelectedGenre = genre;
            document.querySelectorAll('.genre-pill').forEach(btn => {
                btn.className = "genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all";
            });
            element.className = "genre-pill snap-start whitespace-nowrap px-5 py-2 bg-blue-600 text-white text-xs font-semibold rounded-full shadow-sm shadow-blue-500/20 transition-all";
            fetchLibraryData(genre);
        }

        function executeBooking() {
            const method = document.querySelector('input[name="pickup-method"]:checked').value;
            const book = booksData.find(b => b.id === activeBookId);
            const isId = currentLang === 'id';
            
            closeBookModal();

            if (method === 'self') {
                document.getElementById('toast-title').innerText = isId ? "Booking Berhasil!" : "Booking Successful!";
                document.getElementById('toast-desc').innerText = isId 
                    ? `Buku "${book.title}" siap. Silakan ambil di meja sirkulasi utama perpustakaan.` 
                    : `Book "${book.title}" is ready. Please pick it up at the main circulation desk.`;
            } else {
                document.getElementById('toast-title').innerText = isId ? "Pesanan Diproses!" : "Order Processed!";
                document.getElementById('toast-desc').innerText = isId 
                    ? `Buku "${book.title}" akan segera diantarkan oleh kurir internal ke lokasi Anda.` 
                    : `Book "${book.title}" will be delivered shortly to your location by our internal courier.`;
            }

            const toast = document.getElementById('success-toast');
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 4000);
        }

        function liveSearch() {
            const query = document.getElementById('search-input').value.toLowerCase();
            document.querySelectorAll('#live-library-container > div').forEach((card, idx) => {
                const book = booksData[idx];
                if(book) {
                    const match = book.title.toLowerCase().includes(query) || book.author.toLowerCase().includes(query);
                    card.style.display = match ? 'block' : 'none';
                }
            });
        }

        function filterRating(val) {
            const minRating = parseFloat(val);
            document.querySelectorAll('#live-library-container > div').forEach((card, idx) => {
                const book = booksData[idx];
                if(book) {
                    card.style.display = book.rating >= minRating ? 'block' : 'none';
                }
            });
        }

        function openBookModal(id) {
            const book = booksData.find(b => b.id === id);
            if(!book) return;
            activeBookId = id;

            document.getElementById('modal-cover').src = book.cover;
            document.getElementById('modal-title').innerText = book.title;
            document.getElementById('modal-author').innerText = book.author;
            document.getElementById('modal-genre').innerText = book.genre;
            document.getElementById('modal-rating').innerText = book.rating;
            document.getElementById('modal-desc').innerText = book.desc;

            document.querySelector('input[name="pickup-method"][value="self"]').checked = true;

            const badge = document.getElementById('modal-status-badge');
            badge.innerText = book.status === 'Tersedia' ? (currentLang === 'id' ? 'Tersedia' : 'Available') : (currentLang === 'id' ? 'Dipinjam' : 'On Loan');
            badge.className = book.status === 'Tersedia' ? 'text-green-500 font-bold' : 'text-rose-500 font-bold';

            updateModalButtons();
            document.getElementById('book-modal').classList.remove('hidden');
        }

        function closeBookModal() { document.getElementById('book-modal').classList.add('hidden'); }

        function toggleWishlist() {
            const book = booksData.find(b => b.id === activeBookId);
            if(!book) return;

            if (book.status !== 'Dipinjam') return;

            const idx = wishlistBooks.findIndex(id => id === activeBookId);
            const isId = currentLang === 'id';
            
            if(idx > -1) {
                wishlistBooks.splice(idx, 1);
                document.getElementById('toast-title').innerText = isId ? "Dihapus dari Wishlist" : "Removed from Wishlist";
                document.getElementById('toast-desc').innerText = isId ? `Buku "${book.title}" berhasil dilepas.` : `Book "${book.title}" has been removed.`;
            } else {
                wishlistBooks.push(activeBookId);
                document.getElementById('toast-title').innerText = isId ? "Ditambahkan ke Wishlist" : "Added to Wishlist";
                document.getElementById('toast-desc').innerText = isId ? `Buku "${book.title}" berhasil disimpan.` : `Book "${book.title}" has been added to your wishlist.`;
            }

            localStorage.setItem('libra-wishlist', JSON.stringify(wishlistBooks));
            updateModalButtons();
            renderWishlist();

            const toast = document.getElementById('success-toast');
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 4000);
        }

        function updateModalButtons() {
            const wishlistBtn = document.getElementById('modal-wishlist-btn');
            const bookingBtn = document.getElementById('modal-booking-btn');
            const book = booksData.find(b => b.id === activeBookId);
            if (!book || !wishlistBtn || !bookingBtn) return;

            if (book.status !== 'Dipinjam') {
                wishlistBtn.innerText = langDictionary[currentLang]['modal-wishlist-disabled'];
                wishlistBtn.className = "w-full bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-800 cursor-not-allowed";
                wishlistBtn.disabled = true;

                bookingBtn.innerText = langDictionary[currentLang]['modal-btn-book'];
                bookingBtn.className = "w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-3 rounded-xl shadow-sm transition-colors";
                bookingBtn.disabled = false;
                return;
            }

            wishlistBtn.disabled = false;
            const hasBook = wishlistBooks.includes(activeBookId);
            if(hasBook) {
                wishlistBtn.innerText = langDictionary[currentLang]['modal-wishlist-remove'];
                wishlistBtn.className = "w-full bg-slate-100 dark:bg-slate-800 text-rose-500 text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 transition-colors";
            } else {
                wishlistBtn.innerText = langDictionary[currentLang]['modal-wishlist-add'];
                wishlistBtn.className = "w-full bg-rose-50 dark:bg-rose-950/30 text-rose-600 text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-2 border border-rose-200/50 hover:bg-rose-100 dark:hover:bg-rose-950/50 transition-colors";
            }

            bookingBtn.innerText = langDictionary[currentLang]['modal-book-disabled'];
            bookingBtn.className = "w-full bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 text-xs font-bold py-3 rounded-xl flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-800 cursor-not-allowed";
            bookingBtn.disabled = true;
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
                        <div onclick="openBookModal(${book.id})" class="wishlist-card-item bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 relative cursor-pointer">
                            <button onclick="removeFromWishlist(${index}, event)" class="absolute top-2 right-2 bg-white/80 backdrop-blur-sm dark:bg-slate-900/80 w-6 h-6 rounded-full text-rose-500 flex items-center justify-center shadow-sm text-xs hover:bg-rose-50">
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

        function submitDonation() {
            const titleInp = document.getElementById('donate-book-title');
            const authorInp = document.getElementById('donate-book-author');
            const categoryInp = document.getElementById('donate-book-category');
            const conditionInp = document.getElementById('donate-book-condition');

            const now = new Date();
            const formattedDate = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

            const newDonation = {
                title: titleInp.value.trim(),
                author: authorInp.value.trim(),
                category: categoryInp.value,
                condition: conditionInp.value,
                date: formattedDate,
                status: "Menunggu Verifikasi"
            };

            userDonations.unshift(newDonation);
            localStorage.setItem('libra-donations', JSON.stringify(userDonations));
            renderDonationHistory();

            titleInp.value = ''; authorInp.value = ''; 
            document.getElementById('donate-book-note').value = '';
            categoryInp.selectedIndex = 0; conditionInp.selectedIndex = 0;

            const isId = currentLang === 'id';
            document.getElementById('toast-title').innerText = isId ? "Donasi Diajukan!" : "Donation Submitted!";
            document.getElementById('toast-desc').innerText = isId ? "Terima kasih! Pengajuan donasi Anda berhasil dikirim ke admin perpustakaan." : "Thank you! Your donation request has been sent successfully.";

            const toast = document.getElementById('success-toast');
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 4000);
        }

        function renderDonationHistory() {
            const container = document.getElementById('donation-history-container');
            if(!container) return;
            
            if (userDonations.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-400 py-4 text-center italic" data-i18n="donate-empty">${langDictionary[currentLang]['donate-empty']}</p>`;
                return;
            }
            container.innerHTML = '';
            userDonations.forEach(donasi => {
                const statusText = donasi.status === "Menunggu Verifikasi" ? (currentLang === 'id' ? "Menunggu Verifikasi" : "Pending Verification") : donasi.status;
                container.insertAdjacentHTML('beforeend', `
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/50 dark:border-slate-800 flex justify-between items-center">
                        <div>
                            <h6 class="text-xs font-bold text-slate-800 dark:text-white line-clamp-1">${donasi.title}</h6>
                            <p class="text-[10px] text-slate-400 mt-0.5">${donasi.author} • ${donasi.date}</p>
                        </div>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-500 dark:bg-amber-950/40 border border-amber-200/50 dark:border-amber-900/40">${statusText}</span>
                    </div>
                `);
            });
        }

        function submitFeedback() {
            const category = document.getElementById('feedback-category').value;
            const message = document.getElementById('feedback-message').value.trim();
            
            if (!message) return;
            
            const feedbacks = JSON.parse(localStorage.getItem('libra-feedbacks') || '[]');
            feedbacks.push({ category: category, message: message, date: new Date().toISOString() });
            localStorage.setItem('libra-feedbacks', JSON.stringify(feedbacks));
            
            document.getElementById('feedback-form').reset();
            
            const isId = currentLang === 'id';
            document.getElementById('toast-title').innerText = isId ? "Feedback Terkirim!" : "Feedback Sent!";
            document.getElementById('toast-desc').innerText = isId ? "Terima kasih atas masukan Anda untuk peningkatan Sistem Libra." : "Thank you for your feedback to help enhance the Libra System.";
            
            const toast = document.getElementById('success-toast');
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 4000);
        }

        function toggleNotif(event) { 
            if(event) event.stopPropagation();
            document.getElementById('notif-panel').classList.toggle('hidden'); 
        }
        function clearNotifications() { document.getElementById('notif-panel').classList.add('hidden'); }

        function toggleDarkMode() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.setItem('libra-theme', html.classList.contains('dark') ? 'dark' : 'light');
        }

        function logoutSesi() { if(confirm(currentLang === 'id' ? "Keluar dari sesi?" : "Log out?")) { window.location.reload(); } }

        document.addEventListener('click', (e) => {
            const notifPanel = document.getElementById('notif-panel');
            if (notifPanel && !notifPanel.contains(e.target)) {
                notifPanel.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('libra-theme') === 'dark') document.documentElement.classList.add('dark');
            applyTranslations();
            switchTab(activeTabId);
            fetchLibraryData('Semua');
            renderDonationHistory(); 
            renderWishlist();

            if (userFine > 0) {
                showFineWidget();
            }
        });
    </script>
</body>
</html>