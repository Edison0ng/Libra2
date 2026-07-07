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
            <button onclick="switchTab('beranda')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-semibold bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 transition-all" data-target="beranda">
                <i class="fa-solid fa-house w-5 text-lg"></i> Beranda
            </button>
            <button onclick="switchTab('buku')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" data-target="buku">
                <i class="fa-solid fa-magnifying-glass w-5 text-lg"></i> Pustaka
            </button>
            <button onclick="switchTab('sirkulasi')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" data-target="sirkulasi">
                <i class="fa-solid fa-arrows-spin w-5 text-lg"></i> Sirkulasi
            </button>
            <button onclick="switchTab('profil')" class="nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" data-target="profil">
                <i class="fa-regular fa-user w-5 text-lg"></i> Profil
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
            
            <button onclick="toggleNotif()" class="focus:outline-none relative p-2 bg-slate-800 dark:bg-slate-900 rounded-xl hover:bg-slate-700 transition-colors">
                <i class="fa-regular fa-bell text-lg"></i>
                <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                </span>
            </button>

            <div id="notif-panel" class="hidden absolute right-6 top-16 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-800 z-50 overflow-hidden text-slate-800 dark:text-slate-100">
                <div class="flex justify-between items-center p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="font-bold text-sm">Notifikasi Terbaru</h3>
                    <button class="text-xs text-blue-500 font-semibold" onclick="alert('Semua notifikasi ditandai dibaca')">Tandai dibaca</button>
                </div>
                <div class="p-4 space-y-3.5 max-h-72 overflow-y-auto hide-scrollbar">
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

        <!-- KONTEN HALAMAN -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8 pb-24 md:pb-8">
            
            <!-- TAB 1: BERANDA -->
            <div id="beranda" class="tab-content active max-w-5xl mx-auto space-y-6">
                <h3 class="font-bold text-slate-800 dark:text-white text-base md:text-lg flex items-center gap-2">
                    <i class="fa-solid fa-hourglass-half text-blue-500"></i> Lanjutkan Membaca
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 flex gap-4 items-center">
                        <div class="w-14 h-20 rounded-lg bg-blue-100 overflow-hidden flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?w=150&auto=format&fit=crop&q=60" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm line-clamp-1">Metodologi Penelitian Bisnis</h4>
                            <p class="text-xs text-slate-400 mt-0.5 mb-2">Prof. Dr. Sugiyono</p>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: 75%"></div>
                            </div>
                            <span class="text-[9px] text-slate-400 block text-right mt-1">75% Selesai</span>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 flex gap-4 items-center">
                        <div class="w-14 h-20 rounded-lg bg-purple-100 overflow-hidden flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=150&auto=format&fit=crop&q=60" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm line-clamp-1">Artificial Intelligence: Modern Approach</h4>
                            <p class="text-xs text-slate-400 mt-0.5 mb-2">Stuart Russell</p>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: 30%"></div>
                            </div>
                            <span class="text-[9px] text-slate-400 block text-right mt-1">30% Selesai</span>
                        </div>
                    </div>
                </div>

                <h3 class="font-bold text-slate-800 dark:text-white text-base md:text-lg flex items-center gap-2 pt-2">
                    <i class="fa-solid fa-star text-amber-500"></i> Direkomendasikan Untukmu
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3">
                        <img src="https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=300&auto=format&fit=crop&q=60" class="w-full aspect-[4/5] object-cover">
                        <div class="p-3">
                            <h4 class="font-bold text-xs md:text-sm text-slate-800 dark:text-white line-clamp-1">Data Science for Beginners</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Andrew Ng</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3">
                        <img src="https://images.unsplash.com/photo-1614849963640-9cc74b2a826f?w=300&auto=format&fit=crop&q=60" class="w-full aspect-[4/5] object-cover">
                        <div class="p-3">
                            <h4 class="font-bold text-xs md:text-sm text-slate-800 dark:text-white line-clamp-1">The Pragmatic Programmer</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Andy Hunt</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ TAB 2: PUSTAKA DIGITAL (INI YANG BENAR - MENAMPILKAN BUKU) -->
            <div id="buku" class="tab-content max-w-5xl mx-auto space-y-6">
                <!-- Search Bar -->
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="search-input" placeholder="Cari judul buku secara live..." class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 focus:outline-none focus:border-blue-500 dark:focus:border-blue-500 shadow-sm text-sm transition-colors text-slate-800 dark:text-white">
                </div>
                
                <!-- Genre Pills -->
                <div class="flex gap-2 overflow-x-auto hide-scrollbar snap-x py-1">
                    <button onclick="filterGenre('Semua', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-blue-600 text-white text-xs font-semibold rounded-full shadow-sm shadow-blue-500/20 transition-all">Semua</button>
                    <button onclick="filterGenre('Romance', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Romance</button>
                    <button onclick="filterGenre('Action', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Action</button>
                    <button onclick="filterGenre('Science', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Science</button>
                    <button onclick="filterGenre('Fiction', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Fiction</button>
                    <button onclick="filterGenre('Non-Fiction', this)" class="genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Non-Fiction</button>
                </div>

                <!-- 📚 GRID BUKU - KONTEN YANG BENAR UNTUK PUSTAKA -->
                <div id="live-library-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($books as $book)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden pb-3 hover:shadow-md transition-shadow cursor-pointer" 
                        onclick="openBookDetail('{{ $book->title }}', '{{ $book->author }}', '{{ $book->image_url }}')">
                        
                        <img src="{{ $book->image_url }}" alt="{{ $book->title }}" class="w-full aspect-[4/5] object-cover">
                     
                        <div class="p-3">
                            <h4 class="font-bold text-xs md:text-sm text-slate-800 dark:text-white line-clamp-1">{{ $book->title }}</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $book->author }}</p>
                            <p class="text-[10px] text-slate-400">Tahun: {{ $book->year }}</p>
                            
                            @if($book->status == 'Tersedia')
                                <span class="inline-block mt-2 px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[9px] font-semibold rounded-full">Tersedia</span>
                            @else
                                <span class="inline-block mt-2 px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-[9px] font-semibold rounded-full">{{ $book->status }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination sederhana -->
                <div class="flex justify-center gap-2 pt-4">
                    <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Sebelumnya</button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">1</button>
                    <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">2</button>
                    <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">3</button>
                    <button class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Selanjutnya</button>
                </div>
            </div>

            <!-- TAB 3: SIRKULASI (KONTEN STATUS PENGIRIMAN) -->
            <div id="sirkulasi" class="tab-content max-w-3xl mx-auto space-y-4">
                <!-- Batas Waktu Booking -->
                <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-400 p-2.5 rounded-xl">
                            <i class="fa-regular fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300">Batas Pengambilan Resv.</h4>
                            <p class="text-xs text-amber-600 dark:text-amber-400/80">Ambil ke meja sirkulasi atau panggil kurir antar.</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 px-4 py-2 rounded-xl border border-amber-200 dark:border-amber-800 font-mono font-bold text-amber-600 dark:text-amber-400 text-sm w-max self-end sm:self-auto">
                        Sisa Waktu: 01:45:00
                    </div>
                </div>

                <!-- Timeline Pengiriman -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800">
                    <h4 class="font-bold text-sm mb-5 text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-truck text-blue-500"></i> Status Pengiriman Kurir
                    </h4>
                    <div class="relative ml-2">
                        <div class="absolute left-2.5 top-2 bottom-4 w-0.5 bg-slate-200 dark:bg-slate-800"></div>

                        <div class="relative flex items-start gap-4 mb-6">
                            <div class="z-10 w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center border-4 border-white dark:border-slate-900 flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-blue-600 dark:text-blue-400">Diantar Kurir</h5>
                                <p class="text-xs text-slate-400 mt-0.5">Kurir sedang menuju ke Fakultas Ilmu Komputer. Estimasi 10 menit.</p>
                            </div>
                        </div>
                        <div class="relative flex items-start gap-4 mb-6">
                            <div class="z-10 w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center border-4 border-white dark:border-slate-900 flex-shrink-0">
                                <i class="fa-solid fa-check text-[8px] text-white"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Buku Selesai Dikemas</h5>
                                <p class="text-xs text-slate-400 mt-0.5">Buku telah diserahkan ke kurir internal.</p>
                            </div>
                        </div>
                        <div class="relative flex items-start gap-4">
                            <div class="z-10 w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center border-4 border-white dark:border-slate-900 flex-shrink-0">
                                <i class="fa-solid fa-check text-[8px] text-white"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Booking Dikonfirmasi</h5>
                                <p class="text-xs text-slate-400 mt-0.5">Permintaan disetujui oleh sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: PROFIL (DENGAN FITUR DONASI & FEEDBACK) -->
            <div id="profil" class="tab-content max-w-xl mx-auto space-y-6">
                <!-- Profil Mahasiswa -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-950 dark:from-slate-900 dark:to-black p-6 rounded-3xl text-center text-white shadow-md relative overflow-hidden">
                    <div class="w-20 h-20 bg-slate-700 rounded-full mx-auto mb-3 border-4 border-slate-600 overflow-hidden">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Ahmad" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-lg">Ahmad Fauzi</h3>
                    <p class="text-xs text-slate-400">NIM: 220194850</p>
                    <span class="inline-block bg-white/10 text-[10px] font-medium px-3 py-1 rounded-full mt-3 border border-white/10">Fakultas Ilmu Komputer</span>
                </div>

                <!-- Pengaturan & Fitur -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800 overflow-hidden text-sm">
                    <!-- Dark Mode Toggle -->
                    <div class="flex justify-between items-center p-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3 font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-regular fa-moon text-lg text-indigo-500"></i> Tema Gelap
                        </div>
                        <button onclick="toggleDarkMode()" id="dark-mode-toggle" class="w-11 h-6 bg-slate-200 dark:bg-blue-600 rounded-full relative p-1 transition-colors duration-200">
                            <div class="w-4 h-4 bg-white rounded-full absolute top-1 left-1 dark:left-6 transition-all duration-200 shadow-sm"></div>
                        </button>
                    </div>

                    <!-- Bahasa -->
                    <div class="flex justify-between items-center p-4 border-b border-slate-100 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <div class="flex items-center gap-3 font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-globe text-lg text-emerald-500"></i> Bahasa
                        </div>
                        <span class="text-xs text-slate-400 flex items-center gap-1.5">Indonesia (ID) <i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                    </div>

                    <!-- ✨ FORMULIR DONASI BUKU (BARU) -->
                    <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fa-solid fa-hand-holding-heart text-blue-500 text-sm"></i>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white">Formulir Donasi Buku</h4>
                        </div>
                        <p class="text-xs text-slate-400 leading-normal mb-4">Bantu perluas literasi kampus dengan mendonasikan buku layak bacamu ke koleksi Sistem Libra.</p>
                        
                        <form id="donation-form" onsubmit="event.preventDefault(); submitDonation();" class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Judul Buku</label>
                                    <input type="text" id="donate-book-title" required class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Penulis / Pengarang</label>
                                    <input type="text" id="donate-book-author" required class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white transition-colors">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kategori</label>
                                    <select id="donate-book-category" class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white">
                                        <option value="Science">Sains & Teknologi</option>
                                        <option value="Romance">Fiksi / Novel</option>
                                        <option value="Action">Komik / Petualangan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kondisi Buku</label>
                                    <select id="donate-book-condition" class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white">
                                        <option value="Sangat Baik">Sangat Baik (Seperti Baru)</option>
                                        <option value="Baik">Baik (Ada Sedikit Lecet)</option>
                                        <option value="Cukup">Cukup Layak</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Catatan Tambahan (Opsional)</label>
                                <textarea id="donate-book-note" rows="2" class="w-full px-3 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white resize-none"></textarea>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-md transition-colors">
                                Ajukan Donasi Buku
                            </button>
                        </form>

                        <!-- Riwayat Donasi -->
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <h5 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Riwayat Donasi Kamu</h5>
                            <div id="donation-history-container" class="space-y-3"></div>
                        </div>
                    </div>

                    <!-- ✨ FORMULIR FEEDBACK (BARU) -->
                    <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fa-solid fa-comment-dots text-blue-500 text-sm"></i>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white">Kirim Masukan / Feedback</h4>
                        </div>
                        <p class="text-xs text-slate-400 mb-4">Bantu kami meningkatkan Sistem Libra dengan memberikan saran atau laporan kendala Anda.</p>
                        
                        <form id="feedback-form" onsubmit="event.preventDefault(); submitFeedback();" class="space-y-3">
                            <div>
                                <label for="feedback-category" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kategori Masukan</label>
                                <select id="feedback-category" class="w-full text-xs px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white">
                                    <option value="Saran">Saran & Fitur Baru</option>
                                    <option value="Bug">Laporan Bug / Error</option>
                                    <option value="Pelayanan">Fasilitas Perpustakaan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label for="feedback-message" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pesan Anda</label>
                                <textarea id="feedback-message" rows="3" required class="w-full text-xs px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white resize-none" placeholder="Tuliskan saran atau masukan Anda di sini..."></textarea>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-blue-600 text-white text-xs font-semibold rounded-xl shadow-sm hover:bg-blue-700 transition-colors">
                                Kirim Feedback
                            </button>
                        </form>
                    </div>

                    <!-- Logout -->
                    <button onclick="logoutSesi()" class="w-full p-4 text-center font-bold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors">
                        Keluar Sesi
                    </button>
                </div>
            </div>
        </div>

        <!-- FLOATING BUTTON DENDA -->
        <div class="absolute bottom-20 md:bottom-6 left-6 bg-rose-600 text-white py-3 px-4 rounded-xl shadow-lg shadow-rose-600/20 flex items-center gap-4 cursor-pointer hover:bg-rose-700 active:scale-95 transition-all z-30" onclick="alert('Buka Modal Pembayaran Rp 18.000')">
            <i class="fa-solid fa-triangle-exclamation text-base animate-pulse"></i>
            <div class="text-left font-sans">
                <p class="text-[9px] font-bold uppercase tracking-wider opacity-80">Total Denda</p>
                <p class="text-xs font-bold">Rp 18.000</p>
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
    <div id="detail-modal" class="hidden fixed inset-0 z-50 flex justify-center items-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl p-6 shadow-2xl border border-slate-100 dark:border-slate-800 relative animate-[fadeIn_0.2s_ease-out]">
            <button onclick="closeDetailModal()" class="absolute top-4 right-4 w-8 h-8 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-full flex items-center justify-center hover:bg-slate-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="flex gap-4 mt-2">
                <div class="w-24 h-36 bg-slate-200 rounded-xl overflow-hidden flex-shrink-0 shadow">
                    <img id="modal-cover" src="" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col justify-center">
                    <span class="text-[10px] font-bold text-blue-500 tracking-wider uppercase">Detail Buku</span>
                    <h4 id="modal-title" class="font-bold text-slate-800 dark:text-white text-base leading-tight mt-0.5 mb-1"></h4>
                    <p id="modal-author" class="text-xs text-slate-400 mb-3"></p>
                    <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] font-semibold rounded-full">Tersedia</span>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 dark:border-slate-800 pt-4">
                <h5 class="font-bold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Rekomendasi Buku Serupa</h5>
                <div id="modal-rekomendasi-container" class="flex gap-3 overflow-x-auto hide-scrollbar pb-1 snap-x">
                    <div class="w-16 flex-shrink-0 text-center">
                        <img src="https://images.unsplash.com/photo-1614849963640-9cc74b2a826f?w=150&auto=format&fit=crop&q=60" class="w-full aspect-[3/4] object-cover rounded-lg">
                        <p class="text-[9px] text-slate-400 mt-1 truncate">Clean Code</p>
                    </div>
                    <div class="w-16 flex-shrink-0 text-center">
                        <img src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=150&auto=format&fit=crop&q=60" class="w-full aspect-[3/4] object-cover rounded-lg">
                        <p class="text-[9px] text-slate-400 mt-1 truncate">AI Modern</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button onclick="triggerToast()" class="w-full bg-slate-900 dark:bg-blue-600 text-white text-sm font-semibold py-3 rounded-xl shadow-md hover:bg-slate-800 active:scale-[0.98] transition-all">
                    <i class="fa-solid fa-book-open mr-2"></i> Booking & Antar ke Rumah
                </button>
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
        // DATA DONASI & FEEDBACK (Local Storage)
        // ============================================================
        let userDonations = JSON.parse(localStorage.getItem('libra-donations') || '[]');
        let userFeedbacks = JSON.parse(localStorage.getItem('libra-feedbacks') || '[]');

        // ============================================================
        // FUNGSI SWITCH TAB
        // ============================================================
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            const target = document.getElementById(tabId);
            if (target) target.classList.add('active');

            const titles = {
                'beranda': 'Beranda',
                'buku': 'Pustaka Digital',
                'sirkulasi': 'Sirkulasi',
                'profil': 'Profil Mahasiswa'
            };
            document.getElementById('header-title').textContent = titles[tabId] || tabId;

            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.className = 'nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all';
                if (btn.dataset.target === tabId) {
                    btn.className = 'nav-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left font-semibold bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 transition-all';
                }
            });

            document.querySelectorAll('.nav-btn-mobile').forEach(btn => {
                btn.className = 'nav-btn-mobile flex flex-col items-center p-2 text-slate-400';
                if (btn.dataset.target === tabId) {
                    btn.className = 'nav-btn-mobile flex flex-col items-center p-2 text-blue-600 dark:text-blue-400';
                }
            });

            // Refresh donasi jika tab profil
            if (tabId === 'profil') {
                renderDonationHistory();
            }
        }

        // ============================================================
        // FUNGSI LAINNYA
        // ============================================================

        function toggleNotif() {
            document.getElementById('notif-panel').classList.toggle('hidden');
        }

        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            const toggle = document.getElementById('dark-mode-toggle');
            toggle.classList.toggle('bg-slate-200');
            toggle.classList.toggle('dark:bg-blue-600');
            const circle = toggle.querySelector('div');
            circle.classList.toggle('dark:left-6');
        }

        function filterGenre(genre, button) {
            document.querySelectorAll('.genre-pill').forEach(pill => {
                pill.className = 'genre-pill snap-start whitespace-nowrap px-5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all';
            });
            if (button) {
                button.className = 'genre-pill snap-start whitespace-nowrap px-5 py-2 bg-blue-600 text-white text-xs font-semibold rounded-full shadow-sm shadow-blue-500/20 transition-all';
            }
            console.log('Filter genre:', genre);
        }

        function openBookDetail(title, author, image) {
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-author').textContent = author;
            document.getElementById('modal-cover').src = image;
            document.getElementById('detail-modal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
        }

        function triggerToast() {
            const toast = document.getElementById('success-toast');
            document.getElementById('toast-title').textContent = 'Pemesanan Berhasil!';
            document.getElementById('toast-desc').textContent = 'Terima kasih telah meminjam! Buku sedang diantarkan oleh kurir ke alamatmu.';
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 5000);
            closeDetailModal();
        }

        function logoutSesi() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                alert('Anda telah keluar dari sesi.');
            }
        }

        // ============================================================
        // FUNGSI DONASI BUKU
        // ============================================================
        function submitDonation() {
            const titleInp = document.getElementById('donate-book-title');
            const authorInp = document.getElementById('donate-book-author');
            const categoryInp = document.getElementById('donate-book-category');
            const conditionInp = document.getElementById('donate-book-condition');
            const noteInp = document.getElementById('donate-book-note');

            if (!titleInp.value.trim() || !authorInp.value.trim()) {
                alert('Harap isi judul dan penulis buku!');
                return;
            }

            const now = new Date();
            const formattedDate = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

            const newDonation = {
                title: titleInp.value.trim(),
                author: authorInp.value.trim(),
                category: categoryInp.value,
                condition: conditionInp.value,
                note: noteInp.value.trim(),
                date: formattedDate,
                status: "Menunggu Verifikasi"
            };

            userDonations.unshift(newDonation);
            localStorage.setItem('libra-donations', JSON.stringify(userDonations));
            renderDonationHistory();

            // Reset form
            titleInp.value = '';
            authorInp.value = '';
            noteInp.value = '';
            categoryInp.selectedIndex = 0;
            conditionInp.selectedIndex = 0;

            // Tampilkan toast
            document.getElementById('toast-title').textContent = 'Donasi Diajukan!';
            document.getElementById('toast-desc').textContent = 'Terima kasih! Pengajuan donasi Anda berhasil dikirim ke admin perpustakaan.';
            const toast = document.getElementById('success-toast');
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }

        function renderDonationHistory() {
            const container = document.getElementById('donation-history-container');
            if (!container) return;
            
            if (userDonations.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-400 py-4 text-center italic">Belum ada riwayat pengajuan donasi.</p>`;
                return;
            }

            container.innerHTML = '';
            userDonations.forEach(donasi => {
                container.insertAdjacentHTML('beforeend', `
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/50 dark:border-slate-800 flex justify-between items-center">
                        <div>
                            <h6 class="text-xs font-bold text-slate-800 dark:text-white line-clamp-1">${donasi.title}</h6>
                            <p class="text-[10px] text-slate-400 mt-0.5">${donasi.author} • ${donasi.date}</p>
                        </div>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-500 dark:bg-amber-950/40 border border-amber-200/50 dark:border-amber-900/40">${donasi.status}</span>
                    </div>
                `);
            });
        }

        // ============================================================
        // FUNGSI FEEDBACK
        // ============================================================
        function submitFeedback() {
            const category = document.getElementById('feedback-category').value;
            const message = document.getElementById('feedback-message').value.trim();
            
            if (!message) {
                alert('Harap isi pesan feedback Anda!');
                return;
            }
            
            const feedbacks = JSON.parse(localStorage.getItem('libra-feedbacks') || '[]');
            feedbacks.push({ 
                category: category, 
                message: message, 
                date: new Date().toISOString() 
            });
            localStorage.setItem('libra-feedbacks', JSON.stringify(feedbacks));
            
            document.getElementById('feedback-form').reset();
            
            document.getElementById('toast-title').textContent = 'Feedback Terkirim!';
            document.getElementById('toast-desc').textContent = 'Terima kasih atas masukan Anda untuk peningkatan Sistem Libra.';
            const toast = document.getElementById('success-toast');
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }

        // ============================================================
        // EVENT LISTENER
        // ============================================================
        // Tutup notifikasi saat klik di luar
        document.addEventListener('click', function(event) {
            const panel = document.getElementById('notif-panel');
            if (!panel.classList.contains('hidden')) {
                const isClickInside = panel.contains(event.target);
                const isClickOnBell = event.target.closest('button')?.querySelector('.fa-bell');
                if (!isClickInside && !isClickOnBell) {
                    panel.classList.add('hidden');
                }
            }
        });

        // Render donasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            renderDonationHistory();
        });

        console.log('📚 Sistem Libra siap digunakan!');
    </script>
</body>
</html>