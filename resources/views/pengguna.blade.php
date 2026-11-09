<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="Portal Statistik Pernikahan dan Perceraian Indonesia periode 2023–2025. Eksplorasi data resmi Kemenag RI dan BPS melalui visualisasi grafik, tabel data, dan peta choropleth interaktif."/>
    <meta name="author" content="Statistik Pernikahan & Perceraian Indonesia"/>
    <title>Statistik Pernikahan dan Perceraian Indonesia (2023–2025) • Portal Publik</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        },
                        secondary: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
                        }
                    },
                    fontFamily: {
                        headline: ["Plus Jakarta Sans", "sans-serif"],
                        body: ["Inter", "sans-serif"]
                    }
                }
            }
        };
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Leaflet JS CDN -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Map container styling */
        #map-container {
            width: 100%;
            height: 580px;
            border-radius: 1rem;
            z-index: 10;
        }
        @media (max-width: 640px) {
            #map-container { height: 440px; }
        }

        /* Styling Custom Tooltip Leaflet Persis Admin */
        .leaflet-tooltip-indonesia {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .leaflet-tooltip-indonesia::before {
            display: none !important;
        }

        /* Leaflet custom popup persis admin */
        .leaflet-popup-content-wrapper {
            border-radius: 1rem !important;
            padding: 0 !important;
            overflow: hidden !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
        }
        .leaflet-popup-content {
            margin: 0 !important;
            line-height: normal !important;
        }
        .leaflet-container {
            font-family: inherit !important;
            background: #F4F8FC;
        }

        /* Pulsing indicator */
        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .animate-pulse-subtle {
            animation: pulse-subtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Masking Gradasi Halus Siluet Pengantin Adat Nusantara */
        .couple-mask-left {
            -webkit-mask-image: linear-gradient(to top, transparent 0%, rgba(0,0,0,0.95) 15%, rgba(0,0,0,1) 85%, transparent 100%), linear-gradient(to right, rgba(0,0,0,1) 68%, transparent 100%);
            mask-image: linear-gradient(to top, transparent 0%, rgba(0,0,0,0.95) 15%, rgba(0,0,0,1) 85%, transparent 100%), linear-gradient(to right, rgba(0,0,0,1) 68%, transparent 100%);
        }
        .couple-mask-right {
            -webkit-mask-image: linear-gradient(to top, transparent 0%, rgba(0,0,0,0.95) 15%, rgba(0,0,0,1) 85%, transparent 100%), linear-gradient(to left, rgba(0,0,0,1) 68%, transparent 100%);
            mask-image: linear-gradient(to top, transparent 0%, rgba(0,0,0,0.95) 15%, rgba(0,0,0,1) 85%, transparent 100%), linear-gradient(to left, rgba(0,0,0,1) 68%, transparent 100%);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex flex-col min-h-screen">

    <!-- ========================================== -->
    <!-- 1. NAVBAR PUBLIK                          -->
    <!-- ========================================== -->
    <!-- ========================================== -->
    <!-- 1. NAVBAR PUBLIK ELEGAN & TERTATA RAPI     -->
    <!-- ========================================== -->
    <nav class="w-full bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.04)] transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Identitas Brand & Logo Kementerian Agama -->
                <a href="#hero" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded-xl p-1 transition shrink-0">
                    <!-- Icon Emblem Mewah -->
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-700 via-teal-800 to-slate-900 text-white flex items-center justify-center shadow-md shadow-emerald-950/20 ring-1 ring-emerald-500/30 group-hover:scale-105 transition-transform duration-300 shrink-0">
                        <span class="material-symbols-outlined text-[22px] text-amber-300">account_balance</span>
                    </div>
                    <!-- Teks Brand -->
                    <div class="flex flex-col text-left">
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-extrabold text-base sm:text-lg text-slate-900 tracking-tight leading-tight group-hover:text-emerald-800 transition-colors">
                                Statistik Pernikahan
                            </span>
                            <span class="hidden sm:inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-mono">
                                RI
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500 font-medium tracking-wide flex items-center gap-1.5 leading-none mt-1">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Kementerian Agama &amp; BPS (2023–2025)</span>
                        </span>
                    </div>
                </a>

                <!-- Desktop Nav Links (Clean Segmented Pills) -->
                <div class="hidden xl:flex items-center gap-1 text-[13px] font-semibold text-slate-600 bg-slate-100/70 p-1.5 rounded-2xl border border-slate-200/70 shadow-2xs">
                    <a href="#hero" class="px-3 py-1.5 rounded-xl hover:text-emerald-800 hover:bg-white hover:shadow-2xs transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">home</span>
                        <span>Beranda</span>
                    </a>
                    <a href="#dashboard-ringkasan" class="px-3 py-1.5 rounded-xl hover:text-emerald-800 hover:bg-white hover:shadow-2xs transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">insights</span>
                        <span>Ringkasan</span>
                    </a>
                    <a href="#peta-persebaran" class="px-3 py-1.5 rounded-xl hover:text-emerald-800 hover:bg-white hover:shadow-2xs transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">map</span>
                        <span>Peta</span>
                    </a>
                    <a href="#data-pernikahan" class="px-3 py-1.5 rounded-xl hover:text-emerald-800 hover:bg-white hover:shadow-2xs transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">volunteer_activism</span>
                        <span>Pernikahan</span>
                    </a>
                    <a href="#data-perceraian" class="px-3 py-1.5 rounded-xl hover:text-emerald-800 hover:bg-white hover:shadow-2xs transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">heart_broken</span>
                        <span>Perceraian</span>
                    </a>
                    <a href="#faktor-perceraian" class="px-3 py-1.5 rounded-xl hover:text-emerald-800 hover:bg-white hover:shadow-2xs transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">pie_chart</span>
                        <span>Faktor</span>
                    </a>
                    <a href="#tentang-data" class="px-3 py-1.5 rounded-xl hover:text-emerald-800 hover:bg-white hover:shadow-2xs transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">info</span>
                        <span>Tentang</span>
                    </a>
                </div>

                <!-- Nav Links for Intermediate Screens (lg to xl) -->
                <div class="hidden lg:flex xl:hidden items-center gap-1 text-xs font-semibold text-slate-600">
                    <a href="#hero" class="px-2.5 py-1.5 rounded-lg hover:text-emerald-700 hover:bg-slate-100 transition">Beranda</a>
                    <a href="#dashboard-ringkasan" class="px-2.5 py-1.5 rounded-lg hover:text-emerald-700 hover:bg-slate-100 transition">Ringkasan</a>
                    <a href="#peta-persebaran" class="px-2.5 py-1.5 rounded-lg hover:text-emerald-700 hover:bg-slate-100 transition">Peta</a>
                    <a href="#data-pernikahan" class="px-2.5 py-1.5 rounded-lg hover:text-emerald-700 hover:bg-slate-100 transition">Data</a>
                    <a href="#faktor-perceraian" class="px-2.5 py-1.5 rounded-lg hover:text-emerald-700 hover:bg-slate-100 transition">Faktor</a>
                </div>

                <!-- Right Actions: CTA Buttons -->
                <div class="hidden sm:flex items-center gap-2.5 shrink-0">
                    <a href="#peta-persebaran" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition border border-emerald-200/80 shadow-2xs">
                        <span class="material-symbols-outlined text-[17px] text-emerald-700">explore</span>
                        <span>Eksplorasi Peta</span>
                    </a>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="lg:hidden flex items-center gap-2">
                    <button type="button" id="mobile-menu-btn" class="p-2.5 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" aria-label="Buka Menu Navigasi">
                        <span class="material-symbols-outlined text-2xl" id="menu-icon">menu</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-md px-4 pt-4 pb-6 shadow-2xl space-y-2 text-sm font-semibold text-slate-700 transition-all duration-300">
            <!-- Mobile Navigation Links with Icons -->
            <div class="grid grid-cols-1 gap-1">
                <a href="#hero" class="mobile-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-emerald-50 hover:text-emerald-800 transition">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">home</span>
                    <span>Beranda Utama</span>
                </a>
                <a href="#dashboard-ringkasan" class="mobile-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-emerald-50 hover:text-emerald-800 transition">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">insights</span>
                    <span>Ringkasan Statistik</span>
                </a>
                <a href="#peta-persebaran" class="mobile-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-emerald-50 hover:text-emerald-800 transition">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">map</span>
                    <span>Peta Persebaran Wilayah</span>
                </a>
                <a href="#data-pernikahan" class="mobile-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-emerald-50 hover:text-emerald-800 transition">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">volunteer_activism</span>
                    <span>Data Pernikahan</span>
                </a>
                <a href="#data-perceraian" class="mobile-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-emerald-50 hover:text-emerald-800 transition">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">heart_broken</span>
                    <span>Data Perceraian</span>
                </a>
                <a href="#faktor-perceraian" class="mobile-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-emerald-50 hover:text-emerald-800 transition">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">pie_chart</span>
                    <span>Faktor Perceraian</span>
                </a>
                <a href="#tentang-data" class="mobile-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-emerald-50 hover:text-emerald-800 transition">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">info</span>
                    <span>Tentang Data &amp; Regulasi</span>
                </a>
            </div>

            <!-- Mobile CTA Buttons -->
            <div class="pt-3 border-t border-slate-200/80 flex flex-col gap-2">
                <a href="#peta-persebaran" class="mobile-nav-link w-full py-2.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold text-center border border-emerald-200 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">explore</span>
                    <span>Eksplorasi Peta Interaktif</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Public Content -->
    <main class="flex-grow">

        <!-- ========================================== -->
        <!-- 2. HERO SECTION BERANDA                    -->
        <!-- ========================================== -->
        <!-- ========================================== -->
        <!-- 2. HERO SECTION BERANDA (KEMENAG & BATIK NUSANTARA) -->
        <!-- ========================================== -->
        <section id="hero" class="relative text-white py-20 sm:py-28 lg:py-32 px-4 sm:px-6 lg:px-8 overflow-hidden" 
                 style="background: radial-gradient(circle at 50% 18%, rgba(16, 85, 62, 0.45) 0%, rgba(6, 49, 36, 0.25) 50%, transparent 80%), 
                            radial-gradient(circle at 10% 85%, rgba(217, 119, 6, 0.18) 0%, transparent 45%), 
                            radial-gradient(circle at 90% 85%, rgba(13, 148, 136, 0.20) 0%, transparent 45%), 
                            linear-gradient(135deg, #021a13 0%, #063124 30%, #082837 70%, #03141f 100%);">

            <!-- 1. WATERMARK MOTIF BATIK KAWUNG & TENUN SONGKET NUSANTARA -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
                <svg class="absolute inset-0 w-full h-full opacity-[0.09] mix-blend-overlay" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                    <defs>
                        <pattern id="batik-kawung-songket" width="100" height="100" patternUnits="userSpaceOnUse">
                            <!-- Songket Diamond Grid lines -->
                            <path d="M 50 0 L 100 50 L 50 100 L 0 50 Z" fill="none" stroke="#FDE68A" stroke-width="0.8" opacity="0.6" stroke-dasharray="3,2"/>
                            <path d="M 50 12 L 88 50 L 50 88 L 12 50 Z" fill="none" stroke="#FDE68A" stroke-width="0.5" opacity="0.4"/>
                            
                            <!-- Classical Batik Kawung 4-lobe Floral Petals -->
                            <ellipse cx="50" cy="30" rx="11" ry="16" fill="none" stroke="#FDE68A" stroke-width="1.2" opacity="0.85"/>
                            <ellipse cx="50" cy="30" rx="5" ry="9" fill="none" stroke="#FDE68A" stroke-width="0.6" opacity="0.6"/>
                            
                            <ellipse cx="50" cy="70" rx="11" ry="16" fill="none" stroke="#FDE68A" stroke-width="1.2" opacity="0.85"/>
                            <ellipse cx="50" cy="70" rx="5" ry="9" fill="none" stroke="#FDE68A" stroke-width="0.6" opacity="0.6"/>
                            
                            <ellipse cx="30" cy="50" rx="16" ry="11" fill="none" stroke="#FDE68A" stroke-width="1.2" opacity="0.85"/>
                            <ellipse cx="30" cy="50" rx="9" ry="5" fill="none" stroke="#FDE68A" stroke-width="0.6" opacity="0.6"/>
                            
                            <ellipse cx="70" cy="50" rx="16" ry="11" fill="none" stroke="#FDE68A" stroke-width="1.2" opacity="0.85"/>
                            <ellipse cx="70" cy="50" rx="9" ry="5" fill="none" stroke="#FDE68A" stroke-width="0.6" opacity="0.6"/>
                            
                            <!-- Center Rosette Core -->
                            <circle cx="50" cy="50" r="5" fill="#FDE68A" opacity="0.8"/>
                            <circle cx="50" cy="50" r="2" fill="#041f17"/>
                            
                            <!-- Seamless Corner Petals -->
                            <ellipse cx="0" cy="0" rx="14" ry="14" fill="none" stroke="#FDE68A" stroke-width="1" opacity="0.7"/>
                            <ellipse cx="100" cy="0" rx="14" ry="14" fill="none" stroke="#FDE68A" stroke-width="1" opacity="0.7"/>
                            <ellipse cx="0" cy="100" rx="14" ry="14" fill="none" stroke="#FDE68A" stroke-width="1" opacity="0.7"/>
                            <ellipse cx="100" cy="100" rx="14" ry="14" fill="none" stroke="#FDE68A" stroke-width="1" opacity="0.7"/>
                            <circle cx="0" cy="0" r="4" fill="#FDE68A" opacity="0.7"/>
                            <circle cx="100" cy="0" r="4" fill="#FDE68A" opacity="0.7"/>
                            <circle cx="0" cy="100" r="4" fill="#FDE68A" opacity="0.7"/>
                            <circle cx="100" cy="100" r="4" fill="#FDE68A" opacity="0.7"/>

                            <!-- Songket cross-stitches at edges -->
                            <path d="M 47 0 L 53 0 M 50 -3 L 50 3" stroke="#FDE68A" stroke-width="0.8" opacity="0.6"/>
                            <path d="M 47 100 L 53 100 M 50 97 L 50 103" stroke="#FDE68A" stroke-width="0.8" opacity="0.6"/>
                            <path d="M 0 47 L 0 53 M -3 50 L 3 50" stroke="#FDE68A" stroke-width="0.8" opacity="0.6"/>
                            <path d="M 100 47 L 100 53 M 97 50 L 103 50" stroke="#FDE68A" stroke-width="0.8" opacity="0.6"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#batik-kawung-songket)"/>
                </svg>

                <!-- Subtle Center Radial Mask for Text Legibility -->
                <div class="absolute inset-0 bg-radial from-transparent via-transparent to-[#021a13]/80"></div>
                <!-- Top & Bottom Decorative Golden Trim Border -->
                <div class="absolute top-0 inset-x-0 h-[2px] bg-gradient-to-r from-transparent via-amber-400/30 to-transparent"></div>
                <div class="absolute bottom-0 inset-x-0 h-[2px] bg-gradient-to-r from-transparent via-emerald-400/20 to-transparent"></div>
            </div>

            <!-- 2. ILUSTRASI SILUET PENGANTIN SISI KIRI BAWAH (ADAT JAWA & SUMATERA) -->
            <div class="absolute bottom-0 left-0 lg:left-2 xl:left-6 z-10 pointer-events-none select-none flex flex-col items-center">
                <div class="relative w-48 sm:w-60 md:w-72 lg:w-84 xl:w-[410px] max-w-[36vw] transition-transform duration-700 hover:scale-105">
                    <img src="{{ asset('images/pengantin_jawa_sumatera.png') }}" 
                         alt="Siluet Pasangan Pengantin Adat Jawa dan Sumatera (Minangkabau)" 
                         class="w-full h-auto object-contain couple-mask-left opacity-35 sm:opacity-50 lg:opacity-85 xl:opacity-95 filter drop-shadow-[0_10px_25px_rgba(217,119,6,0.3)]"
                         loading="lazy" />
                    <!-- Bottom Fade Gradient Overlay -->
                    <div class="absolute bottom-0 inset-x-0 h-16 bg-gradient-to-t from-[#021a13] to-transparent"></div>
                </div>
                <!-- Badge Penjelas Budaya Adat Nusantara -->
                <div class="hidden xl:inline-flex items-center gap-1.5 px-3 py-1 -mt-4 mb-2 rounded-full bg-emerald-950/80 border border-amber-400/40 text-amber-200 text-[11px] font-semibold backdrop-blur-md shadow-lg shadow-black/50 z-20">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Adat Jawa &amp; Sumatera (Minang)</span>
                </div>
            </div>

            <!-- 3. ILUSTRASI SILUET PENGANTIN SISI KANAN BAWAH (ADAT SULAWESI & PAPUA) -->
            <div class="absolute bottom-0 right-0 lg:right-2 xl:right-6 z-10 pointer-events-none select-none flex flex-col items-center">
                <div class="relative w-48 sm:w-60 md:w-72 lg:w-84 xl:w-[410px] max-w-[36vw] transition-transform duration-700 hover:scale-105">
                    <img src="{{ asset('images/pengantin_sulawesi_papua.png') }}" 
                         alt="Siluet Pasangan Pengantin Adat Sulawesi (Bugis) dan Papua" 
                         class="w-full h-auto object-contain couple-mask-right opacity-35 sm:opacity-50 lg:opacity-85 xl:opacity-95 filter drop-shadow-[0_10px_25px_rgba(13,148,136,0.3)]"
                         loading="lazy" />
                    <!-- Bottom Fade Gradient Overlay -->
                    <div class="absolute bottom-0 inset-x-0 h-16 bg-gradient-to-t from-[#03141f] to-transparent"></div>
                </div>
                <!-- Badge Penjelas Budaya Adat Nusantara -->
                <div class="hidden xl:inline-flex items-center gap-1.5 px-3 py-1 -mt-4 mb-2 rounded-full bg-emerald-950/80 border border-teal-400/40 text-teal-200 text-[11px] font-semibold backdrop-blur-md shadow-lg shadow-black/50 z-20">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                    <span>Adat Sulawesi (Bugis) &amp; Papua</span>
                </div>
            </div>

            <!-- 4. KONTEN UTAMA HERO (TEKS DENGAN KONTRAS TINGGI & MUDAH DIBACA) -->
            <div class="max-w-4xl mx-auto text-center relative z-20">
                <!-- Badges Kemenag & Nusantara -->
                <div class="inline-flex flex-wrap items-center justify-center gap-2 px-4 py-1.5 rounded-full bg-emerald-950/85 backdrop-blur-md border border-emerald-500/40 text-xs font-semibold text-emerald-200 mb-6 shadow-xl shadow-black/30">
                    <span class="material-symbols-outlined text-[16px] text-amber-300">verified</span>
                    <span class="text-white">Dataset Resmi: Kementerian Agama RI &amp; BPS Indonesia</span>
                    <span class="text-emerald-400/40">•</span>
                    <span class="text-amber-200 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px] text-amber-300">diversity_3</span>
                        <span>Harmoni Adat Pernikahan Nusantara</span>
                    </span>
                    <span class="hidden sm:inline text-emerald-400/40">•</span>
                    <span class="hidden sm:inline text-emerald-300">Akses Terbuka Bebas Login</span>
                </div>

                <!-- Main Headline -->
                <h1 class="font-heading font-extrabold text-3xl sm:text-5xl lg:text-6xl tracking-tight leading-tight sm:leading-none mb-6 drop-shadow-md text-white">
                    Statistik Pernikahan dan Perceraian Indonesia
                </h1>

                <p class="text-emerald-50/90 text-sm sm:text-base lg:text-lg max-w-3xl mx-auto leading-relaxed mb-10 font-normal drop-shadow-sm">
                    Jelajahi perkembangan data pernikahan dan perceraian berdasarkan provinsi di Indonesia periode 2023–2025 melalui visualisasi statistik interaktif resmi Kementerian Agama RI dan BPS.
                </p>

                <!-- Hero Action Buttons -->
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="#dashboard-ringkasan" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-800 hover:from-emerald-500 hover:to-teal-700 text-white font-heading font-bold text-sm shadow-xl shadow-emerald-950/60 border border-emerald-400/40 transition transform hover:-translate-y-0.5">
                        <span class="material-symbols-outlined text-[20px]">insights</span>
                        <span>Jelajahi Data</span>
                    </a>
                    <a href="#peta-persebaran" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-heading font-semibold text-sm border border-emerald-400/30 hover:border-amber-300/50 backdrop-blur-md transition transform hover:-translate-y-0.5 shadow-lg shadow-black/20">
                        <span class="material-symbols-outlined text-[20px]">explore</span>
                        <span>Lihat Peta Persebaran</span>
                    </a>
                </div>

                <!-- Quick Metadata Row -->
                <div class="mt-12 pt-8 border-t border-emerald-500/20 grid grid-cols-2 sm:grid-cols-4 gap-4 text-left max-w-4xl mx-auto">
                    <div class="p-3.5 rounded-xl bg-emerald-950/50 border border-emerald-500/30 backdrop-blur-md hover:border-amber-400/40 transition">
                        <span class="text-[11px] text-emerald-300/80 font-medium block">Rentang Periode</span>
                        <span class="text-base font-bold text-white font-heading">2023 – 2025</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-emerald-950/50 border border-emerald-500/30 backdrop-blur-md hover:border-amber-400/40 transition">
                        <span class="text-[11px] text-emerald-300/80 font-medium block">Cakupan Wilayah</span>
                        <span class="text-base font-bold text-white font-heading">38 Provinsi</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-emerald-950/50 border border-emerald-500/30 backdrop-blur-md hover:border-amber-400/40 transition">
                        <span class="text-[11px] text-emerald-300/80 font-medium block">Dataset Pernikahan</span>
                        <span class="text-base font-bold text-amber-300 font-heading">Kementerian Agama</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-emerald-950/50 border border-emerald-500/30 backdrop-blur-md hover:border-amber-400/40 transition">
                        <span class="text-[11px] text-emerald-300/80 font-medium block">Dataset Perceraian</span>
                        <span class="text-base font-bold text-teal-300 font-heading">BPS &amp; Badilag</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- C. FILTER INTERAKTIF GLOBAL               -->
        <!-- ========================================== -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-7 relative z-30">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-5 sm:p-6 transition-all duration-300">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-[20px]">tune</span>
                        </div>
                        <div>
                            <h2 class="font-heading font-bold text-slate-900 text-sm sm:text-base">Filter Statistik Interaktif</h2>
                            <p class="text-xs text-slate-500">Perubahan filter langsung memperbarui seluruh kartu, grafik, peta, dan tabel secara dinamis.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 self-start lg:self-center">
                        <span id="filter-loading-badge" class="hidden inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-200">
                            <svg class="animate-spin h-3.5 w-3.5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memuat data aktual...</span>
                        </span>

                        <button type="button" id="btn-reset-filter" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                            <span>Reset Filter</span>
                        </button>
                    </div>
                </div>

                <!-- Form Filter Controls -->
                <form id="global-filter-form" class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                    <!-- 1. Filter Tahun -->
                    <div>
                        <label for="filter-tahun" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-blue-600">calendar_month</span>
                            <span>Tahun Analisis</span>
                        </label>
                        <select id="filter-tahun" name="tahun" class="w-full bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-slate-800 text-xs sm:text-sm font-semibold rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-2.5 px-3 transition cursor-pointer">
                            <option value="2025" {{ $selectedTahun == '2025' ? 'selected' : '' }}>Tahun 2025 (Terbaru)</option>
                            <option value="2024" {{ $selectedTahun == '2024' ? 'selected' : '' }}>Tahun 2024 (s.d. September)</option>
                            <option value="2023" {{ $selectedTahun == '2023' ? 'selected' : '' }}>Tahun 2023 (Penuh)</option>
                            <option value="Semua Tahun" {{ $selectedTahun == 'Semua Tahun' ? 'selected' : '' }}>Semua Tahun (2023–2025)</option>
                        </select>
                    </div>

                    <!-- 2. Filter Jenis Statistik -->
                    <div>
                        <label for="filter-jenis" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-blue-600">analytics</span>
                            <span>Jenis Statistik</span>
                        </label>
                        <select id="filter-jenis" name="jenis_statistik" class="w-full bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-slate-800 text-xs sm:text-sm font-semibold rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-2.5 px-3 transition cursor-pointer">
                            <option value="pernikahan" {{ $selectedJenis == 'pernikahan' ? 'selected' : '' }}>Pernikahan (Kementerian Agama)</option>
                            <option value="perceraian" {{ $selectedJenis == 'perceraian' ? 'selected' : '' }}>Perceraian (BPS / Peradilan)</option>
                        </select>
                    </div>

                    <!-- 3. Filter Provinsi -->
                    <div>
                        <label for="filter-provinsi" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-blue-600">location_on</span>
                            <span>Provinsi / Wilayah</span>
                        </label>
                        <select id="filter-provinsi" name="provinsi" class="w-full bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-slate-800 text-xs sm:text-sm font-semibold rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 py-2.5 px-3 transition cursor-pointer">
                            <option value="">Seluruh Indonesia (Nasional)</option>
                            @foreach($masterProvinces as $prov)
                                <option value="{{ $prov->name }}" {{ $selectedProvinsi == $prov->name ? 'selected' : '' }}>
                                    {{ $prov->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <!-- Peringatan Kelengkapan Data 2024 jika aktif -->
                <div id="notice-2024" class="{{ $selectedTahun == '2024' ? '' : 'hidden' }} mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">warning</span>
                    <div>
                        <span class="font-bold block">Pemberitahuan Kelengkapan Data Tahun 2024:</span>
                        <span>Dataset pernikahan resmi tahun 2024 tercatat dari bulan Januari hingga September. Bulan Oktober–Desember 2024 belum dipublikasikan oleh Kementerian Agama dan tidak diasumsikan bernilai nol.</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- D. DASHBOARD PUBLIK / BERANDA             -->
        <!-- ========================================== -->
        <section id="dashboard-ringkasan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            
            <div class="mb-8">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Ringkasan Statistik</span>
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Dashboard Data Terkini Indonesia
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Informasi agregat dihitung secara aktual dari basis data berdasarkan kriteria filter yang sedang aktif.
                </p>
            </div>

            <!-- D.1. KARTU STATISTIK (KPI) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
                
                <!-- Kartu 1: Total Pernikahan -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pernikahan</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">favorite</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <span id="kpi-total-nikah" class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight block">
                            {{ number_format($kpi['total_nikah'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 leading-tight border-t border-slate-100 pt-2.5">
                        <span class="font-semibold text-slate-700" id="kpi-nikah-scope">{{ $kpi['tahun_label'] }} • {{ $kpi['provinsi_label'] }}</span>
                        <p class="text-blue-600 text-[10px] mt-0.5" id="kpi-nikah-note">{{ $kpi['catatan_nikah'] }}</p>
                    </div>
                </div>

                <!-- Kartu 2: Total Perceraian -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Perceraian</span>
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">heart_broken</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <span id="kpi-total-cerai" class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight block">
                            {{ number_format($kpi['total_cerai'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 leading-tight border-t border-slate-100 pt-2.5">
                        <span class="font-semibold text-slate-700" id="kpi-cerai-scope">{{ $kpi['tahun_label'] }} • {{ $kpi['provinsi_label'] }}</span>
                        <p class="text-rose-600 text-[10px] mt-0.5" id="kpi-cerai-note">{{ $kpi['catatan_cerai'] }}</p>
                    </div>
                </div>

                <!-- Kartu 3: Faktor Tertinggi -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Faktor Tertinggi</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">gavel</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <span id="kpi-top-faktor" class="font-heading font-bold text-base text-slate-900 tracking-tight line-clamp-1 block" title="{{ $kpi['top_faktor_nama'] }}">
                            {{ $kpi['top_faktor_nama'] }}
                        </span>
                        <span id="kpi-top-kasus" class="text-xs font-semibold text-amber-700 block mt-0.5">
                            {{ number_format($kpi['top_faktor_kasus'] ?? 0, 0, ',', '.') }} Perkara Tercatat
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 leading-tight border-t border-slate-100 pt-2.5">
                        <span class="text-[10px] text-slate-500 block leading-tight">
                            *Satu perkara dapat memiliki >1 faktor (non-mutual exclusive).
                        </span>
                    </div>
                </div>

                <!-- Kartu 4: Provinsi Berdata -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Provinsi Berdata</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">public</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <span id="kpi-provinsi-tercover" class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight block">
                            {{ $kpi['provinsi_tercover'] }} Provinsi
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 leading-tight border-t border-slate-100 pt-2.5">
                        <span class="text-emerald-700 font-semibold block text-[11px]">38 Entitas Wilayah Nasional</span>
                        <span class="text-[10px] text-slate-500 block">Termasuk 4 provinsi pemekaran baru di Papua.</span>
                    </div>
                </div>

            </div>

            <!-- D.2. GRAFIK TREN PERNIKAHAN & PERCERAIAN -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                
                <!-- Grafik 1: Tren Pernikahan -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="font-heading font-bold text-slate-900 text-base">Tren Jumlah Pernikahan (2023–2025)</h3>
                            <span class="text-xs text-slate-500 block" id="tren-nikah-sublabel">Cakupan: {{ $selectedProvinsi ?: 'Seluruh Indonesia' }}</span>
                        </div>
                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-start">
                            <button type="button" class="btn-chart-type px-2.5 py-1 text-xs font-bold rounded-lg bg-white shadow-sm text-blue-600" data-chart="nikah" data-type="bar">Batang</button>
                            <button type="button" class="btn-chart-type px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-600 hover:text-slate-900" data-chart="nikah" data-type="line">Garis</button>
                        </div>
                    </div>
                    <div class="relative h-72">
                        <canvas id="canvas-tren-nikah"></canvas>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                        <span>Sumber: Ditjen Bimas Islam Kementerian Agama RI</span>
                        <span class="text-amber-600 font-medium">*2024: s.d. September</span>
                    </div>
                </div>

                <!-- Grafik 2: Tren Perceraian -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="font-heading font-bold text-slate-900 text-base">Tren Jumlah Perceraian (2023–2025)</h3>
                            <span class="text-xs text-slate-500 block" id="tren-cerai-sublabel">Cakupan: {{ $selectedProvinsi ?: 'Seluruh Indonesia' }}</span>
                        </div>
                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-start">
                            <button type="button" class="btn-chart-type px-2.5 py-1 text-xs font-bold rounded-lg bg-white shadow-sm text-rose-600" data-chart="cerai" data-type="bar">Batang</button>
                            <button type="button" class="btn-chart-type px-2.5 py-1 text-xs font-semibold rounded-lg text-slate-600 hover:text-slate-900" data-chart="cerai" data-type="line">Garis</button>
                        </div>
                    </div>
                    <div class="relative h-72">
                        <canvas id="canvas-tren-cerai"></canvas>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                        <span>Sumber: Badan Pusat Statistik (BPS) & Mahkamah Agung RI</span>
                        <span class="text-emerald-600 font-medium">Data Lengkap Tahunan</span>
                    </div>
                </div>

            </div>

            <!-- D.3. PERINGKAT PROVINSI TERTINGGI -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Peringkat Pernikahan -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600 text-[22px]">leaderboard</span>
                            <div>
                                <h3 class="font-heading font-bold text-slate-900 text-base">Provinsi Pernikahan Tertinggi</h3>
                                <span class="text-xs text-slate-500 block">Peringkat 10 besar berdasarkan tahun aktif</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700" id="badge-tahun-rank-nikah">
                            Tahun {{ $selectedTahun }}
                        </span>
                    </div>

                    <div class="space-y-3" id="list-ranking-nikah">
                        @php $maxNikah = !empty($rankingNikah) ? ($rankingNikah[0]['total'] ?? 1) : 1; @endphp
                        @foreach($rankingNikah as $idx => $rn)
                            @php $pct = $maxNikah > 0 ? round(($rn['total'] / $maxNikah) * 100) : 0; @endphp
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-xs font-extrabold {{ $idx < 3 ? 'text-blue-600' : 'text-slate-400' }} text-center">
                                    #{{ $idx + 1 }}
                                </span>
                                <div class="flex-grow">
                                    <div class="flex justify-between text-xs font-semibold text-slate-800 mb-1">
                                        <span>{{ $rn['provinsi'] }}</span>
                                        <span class="font-bold text-slate-900">{{ number_format($rn['total'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Peringkat Perceraian -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-rose-600 text-[22px]">leaderboard</span>
                            <div>
                                <h3 class="font-heading font-bold text-slate-900 text-base">Provinsi Perceraian Tertinggi</h3>
                                <span class="text-xs text-slate-500 block">Peringkat 10 besar berdasarkan tahun aktif</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700" id="badge-tahun-rank-cerai">
                            Tahun {{ $selectedTahun }}
                        </span>
                    </div>

                    <div class="space-y-3" id="list-ranking-cerai">
                        @php $maxCerai = !empty($rankingCerai) ? ($rankingCerai[0]['total'] ?? 1) : 1; @endphp
                        @foreach($rankingCerai as $idx => $rc)
                            @php $pct = $maxCerai > 0 ? round(($rc['total'] / $maxCerai) * 100) : 0; @endphp
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-xs font-extrabold {{ $idx < 3 ? 'text-rose-600' : 'text-slate-400' }} text-center">
                                    #{{ $idx + 1 }}
                                </span>
                                <div class="flex-grow">
                                    <div class="flex justify-between text-xs font-semibold text-slate-800 mb-1">
                                        <span>{{ $rc['provinsi'] }}</span>
                                        <span class="font-bold text-slate-900">{{ number_format($rc['total'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-rose-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </section>

        <!-- ========================================== -->
        <!-- E. PETA PERSEBARAN INDONESIA (CHOROPLETH)  -->
        <!-- ========================================== -->
        <section id="peta-persebaran" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div id="persebaran-peta-section" class="bg-white rounded-3xl p-5 sm:p-7 shadow-sm border border-slate-200/80 flex flex-col gap-6">
                
                <!-- Header Section (Judul, Subjudul & Filter Interaktif Peta) -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[28px] text-blue-600">map</span>
                            <h2 class="font-heading text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                                Persebaran Pernikahan & Perceraian di Indonesia
                            </h2>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Peta choropleth interaktif geografis Indonesia (Aceh hingga Papua) berdasarkan data resmi BPS & Kementerian Agama RI.
                        </p>
                    </div>

                    <!-- Filter Controls (Tahun, Mode Statistik, & Tombol Reset Tampilan) -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Filter Tahun Peta -->
                        <div class="flex items-center gap-1.5 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200 shadow-xs">
                            <label for="map-filter-tahun" class="text-xs font-semibold text-slate-600 shrink-0 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                                <span>Tahun:</span>
                            </label>
                            <select id="map-filter-tahun" class="bg-transparent border-0 text-slate-900 text-xs font-bold focus:outline-none cursor-pointer pr-2">
                                <option value="2025" {{ $selectedTahun === '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ $selectedTahun === '2024' ? 'selected' : '' }}>2024</option>
                                <option value="2023" {{ $selectedTahun === '2023' ? 'selected' : '' }}>2023</option>
                            </select>
                        </div>

                        <!-- Toggle Mode: Pernikahan vs Perceraian -->
                        <div class="flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200">
                            <button type="button" id="map-mode-nikah" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-blue-600 text-white shadow-xs">
                                <span class="material-symbols-outlined text-[16px]">favorite</span>
                                <span>Pernikahan</span>
                            </button>
                            <button type="button" id="map-mode-cerai" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900">
                                <span class="material-symbols-outlined text-[16px]">heart_broken</span>
                                <span>Perceraian</span>
                            </button>
                        </div>

                        <!-- Tombol Reset Tampilan Peta -->
                        <button type="button" id="btn-reset-map-view" title="Kembalikan zoom ke seluruh Indonesia" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs border border-slate-200 transition shadow-xs">
                            <span class="material-symbols-outlined text-[16px] text-blue-600">zoom_out_map</span>
                            <span class="hidden sm:inline">Seluruh Indonesia</span>
                        </button>
                    </div>
                </div>

                <!-- Informasi Periode & Status Seleksi Aktif -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-800">
                        <span class="inline-flex items-center gap-1 text-blue-700 font-bold">
                            <span class="material-symbols-outlined text-[17px]">insights</span>
                            <span id="map-periode-info">Tahun {{ $selectedTahun }} • Mode Pernikahan</span>
                        </span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-600 font-normal">Cakupan 38 Provinsi Nasional</span>
                    </div>

                    <div id="map-warning-banner" class="{{ $selectedTahun === '2024' ? '' : 'hidden' }} text-xs px-3 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-medium flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-amber-600">warning</span>
                        <span id="map-warning-text">Data pernikahan 2024 tersedia sampai September.</span>
                    </div>
                </div>

                <!-- 3 Card Ringkasan Statistik Data Peta (Persis Admin) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Card 1: Total Nasional -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-200">
                            <span class="material-symbols-outlined text-[22px]">analytics</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold uppercase text-slate-500 block tracking-wider">Total Nasional</span>
                            <span id="map-stat-total" class="text-lg font-bold font-mono text-blue-800">Memuat...</span>
                        </div>
                    </div>

                    <!-- Card 2: Provinsi Tertinggi -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200">
                            <span class="material-symbols-outlined text-[22px]">workspace_premium</span>
                        </div>
                        <div class="overflow-hidden">
                            <span class="text-[11px] font-semibold uppercase text-slate-500 block tracking-wider" id="map-stat-tertinggi-label">Pernikahan Tertinggi</span>
                            <span id="map-stat-tertinggi" class="text-sm font-bold text-slate-900 truncate block">--</span>
                            <span id="map-stat-tertinggi-val" class="text-xs font-mono text-blue-700 font-semibold block">--</span>
                        </div>
                    </div>

                    <!-- Card 3: Provinsi Terendah -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 border border-slate-300">
                            <span class="material-symbols-outlined text-[22px]">trending_down</span>
                        </div>
                        <div class="overflow-hidden">
                            <span class="text-[11px] font-semibold uppercase text-slate-500 block tracking-wider" id="map-stat-terendah-label">Pernikahan Terendah</span>
                            <span id="map-stat-terendah" class="text-sm font-bold text-slate-900 truncate block">--</span>
                            <span id="map-stat-terendah-val" class="text-xs font-mono text-slate-600 font-semibold block">--</span>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid: Leaflet Map (Left) & Ranking Panel (Right) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    <!-- Leaflet Map Container -->
                    <div class="lg:col-span-8 relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-[#F4F8FC] min-h-[520px] lg:min-h-[580px] flex flex-col">
                        <!-- Canvas Leaflet Map -->
                        <div id="map-container" class="w-full flex-1 min-h-[520px] lg:min-h-[580px] z-10"></div>

                        <!-- Floating Map Navigation Control / Instructions (Top Right) -->
                        <div class="absolute top-3.5 right-3.5 z-[400] flex flex-col gap-2 items-end">
                            <div class="bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-lg border border-slate-200 shadow-md text-[11px] text-slate-700 flex items-center gap-1.5 font-medium">
                                <span class="material-symbols-outlined text-[15px] text-blue-600">touch_app</span>
                                <span>Klik provinsi untuk rincian data</span>
                            </div>
                        </div>

                        <!-- Legenda Gradasi Warna (Bottom Right - Persis Admin) -->
                        <div class="absolute bottom-4 right-4 z-[400] bg-white/95 backdrop-blur-md p-3.5 rounded-xl border border-slate-200 shadow-lg text-xs font-sans max-w-[300px] w-full">
                            <div class="flex items-center justify-between font-bold text-slate-900 mb-2">
                                <span class="text-xs font-bold text-slate-800" id="legend-title">Jumlah Kasus</span>
                                <span class="material-symbols-outlined text-sm text-slate-500">legend_toggle</span>
                            </div>

                            <!-- 5-Step Gradient Bar -->
                            <div class="grid grid-cols-5 h-3 rounded-md overflow-hidden border border-slate-300 shadow-inner mb-1.5" id="legend-gradient-bar">
                                <div class="bg-[#DBEAFE]" title="Sangat Rendah"></div>
                                <div class="bg-[#93C5FD]" title="Rendah"></div>
                                <div class="bg-[#3B82F6]" title="Sedang"></div>
                                <div class="bg-[#1D4ED8]" title="Tinggi"></div>
                                <div class="bg-[#0B2559]" title="Tertinggi"></div>
                            </div>

                            <!-- Label Angka Skala Min, Mid, Max -->
                            <div class="flex justify-between items-center text-[10px] font-mono text-slate-700 font-semibold">
                                <span id="legend-min">0</span>
                                <span id="legend-mid">Mid</span>
                                <span id="legend-max">Max</span>
                            </div>

                            <div class="flex justify-between items-center text-[10px] text-slate-500 font-medium mt-0.5 pb-2 border-b border-slate-200">
                                <span>Sangat Rendah</span>
                                <span>Tertinggi</span>
                            </div>

                            <!-- Indikator Warna Khusus "Data Tidak Tersedia" -->
                            <div class="flex items-center justify-between text-[11px] text-slate-600 pt-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3.5 h-3 rounded bg-[#CBD5E1] border border-slate-400 inline-block"></span>
                                    <span>Data tidak tersedia</span>
                                </div>
                                <span class="text-[10px] font-mono text-slate-500 font-semibold">(Netral)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ranking Panel (Top 10 Provinsi - Persis Admin) -->
                    <div class="lg:col-span-4 bg-slate-50/80 rounded-2xl p-4 sm:p-5 border border-slate-200 flex flex-col justify-between gap-4">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-blue-600 text-[20px]">format_list_numbered</span>
                                    <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider">
                                        Top 10 Provinsi
                                    </h3>
                                </div>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-800" id="panel-ranking-mode-badge">
                                    Pernikahan
                                </span>
                            </div>

                            <p class="text-[11px] text-slate-500 mb-3">
                                Provinsi dengan jumlah kasus tertinggi pada periode terfilter. Klik baris provinsi untuk menyorot di peta.
                            </p>

                            <!-- List Top 10 -->
                            <div id="top-10-ranking-list" class="flex flex-col gap-2 max-h-[420px] overflow-y-auto pr-1">
                                <div class="text-center py-8 text-xs text-slate-400">Memuat data ranking...</div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-200 text-center">
                            <span class="text-[11px] text-slate-500 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[13px] text-emerald-600">sync</span>
                                <span>Terhubung langsung dengan database BPS & Kementerian Agama</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Modal Panel Detail Provinsi (Saat Tombol Rincian Lengkap Diklik - Persis Admin) -->
                <div id="modal-provinsi-faktor-pengguna" class="fixed inset-0 z-[1000] hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl p-6 relative max-h-[90vh] flex flex-col">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4 shrink-0">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center shrink-0 border border-blue-200">
                                    <span class="material-symbols-outlined text-[24px]">location_on</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 id="modal-prov-nama" class="font-heading font-bold text-base text-slate-900">--</h3>
                                        <span id="modal-prov-kode" class="px-2 py-0.5 rounded font-mono text-[10px] font-bold bg-slate-100 text-slate-700">Kode: --</span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        Statistik Komparatif Pernikahan & Perceraian (<span id="modal-prov-tahun">2025</span>) • BPS RI & Kemenag
                                    </p>
                                </div>
                            </div>
                            <button type="button" onclick="closeProvinsiFaktorModalPengguna()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition">
                                <span class="material-symbols-outlined text-[22px]">close</span>
                            </button>
                        </div>

                        <!-- Modal Body Scrollable -->
                        <div class="overflow-y-auto space-y-4 pr-1 flex-1">
                            <!-- 2 Mini Summary Cards (Pernikahan vs Perceraian) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200 text-xs shadow-xs">
                                    <span class="text-[10px] text-blue-700 font-semibold uppercase tracking-wider block">Total Pernikahan</span>
                                    <span id="modal-stat-nikah" class="font-mono font-bold text-base text-blue-900 block mt-0.5">-- Akta</span>
                                    <span class="text-[10px] text-blue-600 block mt-0.5">Kementerian Agama RI</span>
                                </div>
                                <div class="p-3.5 rounded-xl bg-rose-50/80 border border-rose-200 text-xs shadow-xs">
                                    <span class="text-[10px] text-rose-700 font-semibold uppercase tracking-wider block">Total Perceraian</span>
                                    <span id="modal-stat-cerai" class="font-mono font-bold text-base text-rose-900 block mt-0.5">-- Perkara</span>
                                    <span class="text-[10px] text-rose-600 block mt-0.5">Publikasi BPS RI</span>
                                </div>
                            </div>

                            <!-- Highlight Card Faktor Perceraian Tertinggi -->
                            <div class="p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 shadow-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-amber-600">workspace_premium</span>
                                        <span>Faktor Perceraian Tertinggi</span>
                                    </span>
                                    <span id="modal-top-pct" class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-mono font-bold text-xs">--%</span>
                                </div>
                                <div id="modal-top-nama" class="font-heading font-bold text-base text-slate-900 mt-1">
                                    --
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-amber-200/60 text-xs">
                                    <span class="text-slate-600">Jumlah Perkara:</span>
                                    <span id="modal-top-kasus" class="font-mono font-bold text-amber-900">-- Kasus</span>
                                </div>
                            </div>

                            <!-- Breakdown Seluruh Faktor Penyebab Perceraian -->
                            <div>
                                <h4 class="font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-2.5 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-blue-600">analytics</span>
                                    <span>Rincian Faktor Penyebab Perceraian (BPS)</span>
                                </h4>
                                <div id="modal-factors-list" class="space-y-2">
                                    <!-- Loaded dynamically via JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="pt-4 border-t border-slate-200 flex items-center justify-end shrink-0">
                            <button type="button" onclick="closeProvinsiFaktorModalPengguna()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ========================================== -->
        <!-- F. HALAMAN / TAB DATA PERNIKAHAN          -->
        <!-- ========================================== -->
        <section id="data-pernikahan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-slate-200">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Dataset Resmi Kemenag RI</span>
                    <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                        Eksplorasi Data Pernikahan Indonesia
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-3xl">
                        Data statistik peristiwa pernikahan resmi yang tercatat di Kantor Urusan Agama (KUA) seluruh Indonesia. Disimpan pada MySQL dan disinkronkan dari Kementerian Agama RI.
                    </p>
                </div>

                <!-- Export CSV Button -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('pengguna.export-pernikahan', ['tahun' => $selectedTahun, 'provinsi' => $selectedProvinsi]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-bold text-xs shadow-md shadow-emerald-600/20 transition">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Ekspor CSV Pernikahan</span>
                    </a>
                </div>
            </div>

            <!-- Rincian Bulanan Mini-Bar (Jika ada pada tahun terpilih) -->
            @if(count($bulananNikah) > 0)
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-heading font-bold text-sm text-slate-800">
                            Distribusi Bulanan Pernikahan (Tahun {{ $selectedTahun }} • {{ $selectedProvinsi ?: 'Nasional' }})
                        </h4>
                        <span class="text-[11px] text-slate-500">Tersedia {{ count($bulananNikah) }} Bulan Pencatatan</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-2 text-center">
                        @foreach($bulananNikah as $b)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 hover:bg-blue-50 hover:border-blue-200 transition">
                                <span class="text-[11px] text-slate-500 font-semibold block">{{ $b['bulan'] }}</span>
                                <span class="text-xs font-bold text-slate-900 block mt-1">{{ number_format($b['total'], 0, ',', '.') }}</span>
                                <span class="text-[10px] text-blue-600 font-medium block">{{ $b['persen'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tabel Data Pernikahan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[20px]">table_chart</span>
                        <h4 class="font-heading font-bold text-sm text-slate-900">Daftar Rekapitulasi Pernikahan Terverifikasi</h4>
                    </div>
                    
                    <div class="text-xs text-slate-500 font-medium">
                        Total Data: {{ number_format($pernikahanTable->total(), 0, ',', '.') }} Baris
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[11px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4 text-center w-14">No</th>
                                <th class="py-3.5 px-4">Provinsi</th>
                                <th class="py-3.5 px-4 text-center">Tahun</th>
                                <th class="py-3.5 px-4 text-center">Bulan</th>
                                <th class="py-3.5 px-4 text-right">Jumlah Peristiwa</th>
                                <th class="py-3.5 px-4">Sumber Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($pernikahanTable as $idx => $item)
                                <tr class="hover:bg-blue-50/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono text-xs">
                                        {{ $pernikahanTable->firstItem() + $idx }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-900">
                                        {{ $item->provinsi }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold text-blue-700">
                                        {{ $item->tahun }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-medium">
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs">
                                            {{ $item->bulan ?? 'Tahunan' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-900 font-mono">
                                        {{ number_format($item->jumlah_peristiwa ?? $item->jumlah_pernikahan ?? $item->jumlah_nikah ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-[11px] text-slate-500">
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span>{{ $item->sumber_data ?? 'Kementerian Agama RI' }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500 text-xs">
                                        Tidak ada data pernikahan yang sesuai dengan filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pernikahanTable->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        {{ $pernikahanTable->fragment('data-pernikahan')->links() }}
                    </div>
                @endif
            </div>
        </section>

        <!-- ========================================== -->
        <!-- G. HALAMAN / TAB DATA PERCERAIAN          -->
        <!-- ========================================== -->
        <section id="data-perceraian" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-slate-200">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-widest block mb-1">Dataset Resmi BPS & Peradilan</span>
                    <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                        Statistik Perkara Perceraian Indonesia
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-3xl">
                        Data statistik perkara perceraian yang diputuskan oleh badan peradilan dan dipublikasikan resmi oleh Badan Pusat Statistik (BPS).
                    </p>
                </div>

                <!-- Export CSV Button -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('pengguna.export-perceraian', ['tahun' => $selectedTahun, 'provinsi' => $selectedProvinsi]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-heading font-bold text-xs shadow-md shadow-rose-600/20 transition">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Ekspor CSV Perceraian</span>
                    </a>
                </div>
            </div>

            <!-- Tabel Data Perceraian -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-600 text-[20px]">table_chart</span>
                        <h4 class="font-heading font-bold text-sm text-slate-900">Daftar Perkara Perceraian Berdasarkan Wilayah</h4>
                    </div>
                    
                    <div class="text-xs text-slate-500 font-medium">
                        Total Data: {{ number_format($perceraianTable->total(), 0, ',', '.') }} Baris
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[11px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4 text-center w-14">No</th>
                                <th class="py-3.5 px-4">Provinsi</th>
                                <th class="py-3.5 px-4 text-center">Tahun</th>
                                <th class="py-3.5 px-4 text-right">Jumlah Perkara Perceraian</th>
                                <th class="py-3.5 px-4">Sumber Data & Atribusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($perceraianTable as $idx => $item)
                                <tr class="hover:bg-rose-50/40 transition">
                                    <td class="py-3 px-4 text-center text-slate-400 font-mono text-xs">
                                        {{ $perceraianTable->firstItem() + $idx }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-900">
                                        {{ $item->provinsi }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold text-rose-700">
                                        {{ $item->tahun }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-slate-900 font-mono">
                                        {{ number_format($item->jumlah_perceraian, 0, ',', '.') }} Perkara
                                    </td>
                                    <td class="py-3 px-4 text-[11px] text-slate-500">
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Badan Pusat Statistik (BPS RI)</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-500 text-xs">
                                        Tidak ada data perceraian yang sesuai dengan filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($perceraianTable->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        {{ $perceraianTable->fragment('data-perceraian')->links() }}
                    </div>
                @endif
            </div>
        </section>

        <!-- ========================================== -->
        <!-- H. HALAMAN / TAB FAKTOR PERCERAIAN        -->
        <!-- ========================================== -->
        <section id="faktor-perceraian" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-b border-slate-200">
            <div class="mb-8">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest block mb-1">Analisis Yuridis & Faktor Terbanyak</span>
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Faktor Penyebab Perceraian di Indonesia
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-3xl">
                    Distribusi alasan hukum atau latar belakang gugatan perceraian yang dikabulkan dalam persidangan. Diklasifikasikan berdasarkan kategori resmi publikasi BPS.
                </p>
            </div>

            <!-- Metodologi Callout Alert -->
            <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 mb-8 flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 text-[22px] shrink-0 mt-0.5">info</span>
                <div class="text-xs text-amber-900 leading-relaxed">
                    <span class="font-bold block text-sm mb-0.5">Catatan Penting Metodologi Pencatatan Faktor:</span>
                    <span>Satu perkara perceraian dapat tercatat memiliki lebih dari satu faktor penyebab (tidak saling eksklusif / *non-mutual exclusive*). Oleh karena itu, penjumlahan seluruh kategori faktor tidak sama dengan total perkara perceraian, dan kategori faktor tidak boleh digunakan sebagai pengganti angka kasus perceraian.</span>
                </div>
            </div>

            <!-- Diagram Lingkaran: Rincian Kasus per Kategori Faktor Penyebab -->
            @php
                $faktorColors = [
                    '#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6',
                    '#F97316', '#14B8A6', '#6366F1', '#EC4899', '#06B6D4',
                    '#7C3AED', '#D946EF', '#64748B', '#71717A'
                ];
            @endphp
            <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200/80 mb-6">
                <!-- Header Card -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">pie_chart</span>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-slate-900 text-base sm:text-lg">
                                Rincian Kasus per Kategori Faktor Penyebab
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Diagram lingkaran persentase dan distribusi perkara per kategori faktor penyebab perceraian (Tahun <span id="label-tahun-faktor-chart">{{ $selectedTahun }}</span>)
                            </p>
                        </div>
                    </div>

                    <!-- Controls & Badges -->
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Dominan: <strong id="badge-top-faktor">{{ $kpi['top_faktor_nama'] }}</strong></span>
                        </span>

                        <!-- Switch Chart Type Buttons -->
                        <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200/80 text-xs">
                            <button type="button" id="btn-faktor-donut" onclick="switchFaktorChartType('doughnut')" class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 transition bg-white text-amber-700 shadow-xs">
                                <span class="material-symbols-outlined text-[16px]">donut_large</span>
                                <span>Donat</span>
                            </button>
                            <button type="button" id="btn-faktor-pie" onclick="switchFaktorChartType('pie')" class="px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5 transition text-slate-600 hover:text-slate-900">
                                <span class="material-symbols-outlined text-[16px]">pie_chart</span>
                                <span>Lingkaran</span>
                            </button>
                            <button type="button" id="btn-faktor-bar" onclick="switchFaktorChartType('bar')" class="px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5 transition text-slate-600 hover:text-slate-900">
                                <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                                <span>Batang</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Chart & Breakdown Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left: Diagram Lingkaran (5 Cols) -->
                    <div class="lg:col-span-5 flex flex-col items-center justify-center p-2 relative min-h-[340px]">
                        <div class="relative w-full max-w-[320px] aspect-square flex items-center justify-center">
                            <canvas id="canvas-faktor-chart" class="w-full h-full"></canvas>
                            <!-- Donut Center Information Overlay -->
                            <div id="faktor-donut-center" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center p-4">
                                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest block">Faktor Terbesar</span>
                                <span id="donut-top-name" class="text-xs sm:text-sm font-bold text-slate-900 line-clamp-2 max-w-[150px] leading-tight mt-0.5">
                                    {{ $kpi['top_faktor_nama'] }}
                                </span>
                                <span id="donut-top-pct" class="text-xs font-mono font-extrabold text-amber-600 mt-1">
                                    {{ $faktorBreakdown['top_faktor']['persen'] ?? 0 }}% Kasus
                                </span>
                            </div>
                        </div>
                        <span class="text-[11px] text-slate-400 mt-3 text-center block">
                            Arahkan kursor / klik bagian lingkaran untuk melihat detail jumlah perkara
                        </span>
                    </div>

                    <!-- Right: Peringkat & Proporsi Kategori Faktor (7 Cols) -->
                    <div class="lg:col-span-7 flex flex-col gap-3 bg-slate-50/70 rounded-2xl p-4 sm:p-5 border border-slate-200/80">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-600 text-[18px]">format_list_bulleted</span>
                                <h4 class="font-heading text-xs font-bold uppercase tracking-wider text-slate-800">
                                    Peringkat &amp; Proporsi Kategori Faktor
                                </h4>
                            </div>
                            <span class="text-[11px] font-medium text-slate-500">Persentase &amp; Jumlah Perkara</span>
                        </div>

                        <!-- Scrollable Factor List -->
                        <div class="flex flex-col gap-2 max-h-[340px] overflow-y-auto pr-1" id="faktor-breakdown-list">
                            @forelse($faktorBreakdown['items'] as $idx => $fItem)
                                @php
                                    $color = $faktorColors[$idx % count($faktorColors)];
                                    $pct = $fItem['persen'];
                                @endphp
                                <div class="flex flex-col gap-1 text-xs bg-white p-2.5 rounded-xl border border-slate-200/70 hover:border-amber-400/60 hover:shadow-2xs transition group">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 overflow-hidden">
                                            <span class="w-5 h-5 rounded-md font-mono font-bold text-[10px] flex items-center justify-center shrink-0" style="background-color: {{ $color }}20; color: {{ $color }};">
                                                {{ $idx + 1 }}
                                            </span>
                                            <span class="font-semibold text-slate-800 truncate group-hover:text-amber-700 transition" title="{{ $fItem['faktor'] }}">
                                                {{ $fItem['faktor'] }}
                                            </span>
                                            @if($idx === 0)
                                                <span class="hidden sm:inline-flex px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 shrink-0">
                                                    Dominan
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="font-mono font-bold text-slate-900 text-[11px]">
                                                {{ number_format($fItem['total'], 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-400">Perkara</span>
                                            </span>
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold" style="background-color: {{ $color }}20; color: {{ $color }};">
                                                {{ $pct }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500" style="width: {{ max($pct, 2) }}%; background-color: {{ $color }};"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-xs text-slate-400">
                                    Data faktor perceraian tidak tersedia untuk kombinasi filter ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Toggle View Tabular Data -->
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" onclick="const tbl = document.getElementById('wrapper-tabel-faktor'); tbl.classList.toggle('hidden'); const ic = this.querySelector('.chevron-icon'); if (ic) ic.textContent = tbl.classList.contains('hidden') ? 'expand_more' : 'expand_less';" class="text-xs font-bold text-slate-600 hover:text-amber-700 flex items-center gap-1 transition">
                        <span class="material-symbols-outlined text-[18px]">table_rows</span>
                        <span>Lihat Tabel Data Tabulasi Lengkap</span>
                        <span class="material-symbols-outlined text-[18px] chevron-icon">expand_more</span>
                    </button>
                    <span class="text-[11px] text-slate-400 font-mono">Total {{ count($faktorBreakdown['items']) }} Kategori Tercatat BPS</span>
                </div>

                <!-- Collapsible Table -->
                <div id="wrapper-tabel-faktor" class="hidden mt-4 pt-4 border-t border-slate-100 overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[11px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4 text-center w-14">Peringkat</th>
                                <th class="py-3 px-4">Kategori Faktor Penyebab</th>
                                <th class="py-3 px-4 text-right">Jumlah Perkara</th>
                                <th class="py-3 px-4 text-center">Persentase</th>
                                <th class="py-3 px-4">Sumber Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($faktorBreakdown['items'] as $idx => $fItem)
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="py-2.5 px-4 text-center font-bold text-slate-400">#{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $fItem['faktor'] }}</td>
                                    <td class="py-2.5 px-4 text-right font-bold text-slate-900 font-mono">{{ number_format($fItem['total'], 0, ',', '.') }} Perkara</td>
                                    <td class="py-2.5 px-4 text-center font-bold text-slate-700 font-mono">{{ $fItem['persen'] }}%</td>
                                    <td class="py-2.5 px-4 text-xs text-slate-500">Badan Pusat Statistik (BPS RI)</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- I. HALAMAN / TAB TENTANG DATA & SUMBER    -->
        <!-- ========================================== -->
        <section id="tentang-data" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="mb-10 text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Informasi & Metodologi</span>
                <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Tentang Data, Metodologi, dan Sumber Resmi
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">
                    Penjelasan komprehensif mengenai batasan data, perbedaan cakupan instansi penyedia, serta pedoman interpretasi statistik publik.
                </p>
            </div>

            <!-- 4 Grid Pilar Informasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                
                <!-- Pilar 1: Tujuan Website & Periode -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[24px]">flag</span>
                    </div>
                    <h3 class="font-heading font-bold text-slate-900 text-base mb-2">Tujuan Portal & Periode Analisis</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">
                        Portal ini dikembangkan untuk menyediakan akses data terbuka (*open data*) yang mudah dijangkau oleh masyarakat umum, akademisi, peneliti, dan pengambil kebijakan mengenai dinamika pernikahan dan perceraian di Indonesia pada rentang tahun <strong>2023 hingga 2025</strong>.
                    </p>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600">
                        <strong>Cakupan Waktu:</strong> Tahun 2023 (12 bulan), 2024 (Januari s.d. September), dan 2025 (12 bulan penuh).
                    </div>
                </div>

                <!-- Pilar 2: Definisi Indikator -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[24px]">menu_book</span>
                    </div>
                    <h3 class="font-heading font-bold text-slate-900 text-base mb-2">Definisi Indikator Statistik</h3>
                    <ul class="text-xs text-slate-600 space-y-2 leading-relaxed">
                        <li>
                            <strong class="text-slate-800">Pernikahan:</strong> Jumlah peristiwa akad nikah sah yang tercatat secara administratif di KUA seluruh Indonesia.
                        </li>
                        <li>
                            <strong class="text-slate-800">Perceraian:</strong> Perkara perceraian (cerai talak dan cerai gugat) yang telah diputus dan berkekuatan hukum tetap (*inkracht*) di Pengadilan Agama.
                        </li>
                        <li>
                            <strong class="text-slate-800">Faktor Penyebab:</strong> Alasan yuridis penyebab perceraian yang dibuktikan di muka persidangan.
                        </li>
                    </ul>
                </div>

                <!-- Pilar 3: Sumber Resmi Berbeda -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[24px]">account_balance</span>
                    </div>
                    <h3 class="font-heading font-bold text-slate-900 text-base mb-2">Atribusi Sumber Data Independen</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">
                        Dataset dalam portal ini bersumber dari dua instansi resmi yang berbeda dan tidak boleh dicampuradukkan:
                    </p>
                    <div class="space-y-2 text-xs">
                        <div class="p-2.5 rounded-lg bg-blue-50/60 border border-blue-100 text-blue-900">
                            <strong>Data Pernikahan:</strong> Direktorat Jenderal Bimbingan Masyarakat Islam, Kementerian Agama RI.
                        </div>
                        <div class="p-2.5 rounded-lg bg-rose-50/60 border border-rose-100 text-rose-900">
                            <strong>Data Perceraian & Faktor:</strong> Publikasi Statistik Indonesia – Badan Pusat Statistik (BPS) dan Direktorat Jenderal Badan Peradilan Agama (Badilag) Mahkamah Agung RI.
                        </div>
                    </div>
                </div>

                <!-- Pilar 4: Keterbatasan & Pemekaran Wilayah -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[24px]">report_problem</span>
                    </div>
                    <h3 class="font-heading font-bold text-slate-900 text-base mb-2">Pemekaran Wilayah & Batasan Interpretasi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-2">
                        <strong>Provinsi Baru di Papua:</strong> Berdasarkan UU tahun 2022, terbentuk 4 provinsi baru di Papua (Papua Selatan, Papua Tengah, Papua Pegunungan, dan Papua Barat Daya). Data historis sebelum operasional penuh pencatatan mandiri tetap mengacu pada wilayah induk tanpa pemecahan estimasi spekulatif.
                    </p>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        <strong>Kausalitas:</strong> Tingginya angka pada faktor perceraian tertentu mencerminkan materi alasan yang diajukan dalam gugatan, bukan kesimpulan kausalitas tunggal sosiologis.
                    </p>
                </div>

            </div>

        </section>

    </main>

    <!-- ========================================== -->
    <!-- FOOTER PUBLIK                             -->
    <!-- ========================================== -->
    <footer class="w-full bg-slate-900 text-slate-400 py-12 px-4 sm:px-6 lg:px-8 text-xs border-t border-slate-800">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8 pb-8 border-b border-slate-800">
            <!-- Kolom 1: Brand Info -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="font-heading font-extrabold text-sm sm:text-base text-white">Statistik Pernikahan Indonesia</span>
                </div>
                <p class="text-slate-400 text-xs leading-relaxed mb-3">
                    Platform informasi publik visualisasi statistik pernikahan, perceraian, dan faktor penyebab di 38 provinsi Indonesia periode 2023–2025.
                </p>
                <div class="text-[11px] text-slate-500">
                    Data Resmi: Kementerian Agama RI &amp; BPS RI.
                </div>
            </div>

            <!-- Kolom 2: Navigasi Cepat -->
            <div>
                <span class="font-heading font-bold text-white text-xs uppercase tracking-wider block mb-3">Navigasi Halaman</span>
                <ul class="space-y-2">
                    <li><a href="#hero" class="hover:text-emerald-400 transition">Beranda Utama</a></li>
                    <li><a href="#dashboard-ringkasan" class="hover:text-emerald-400 transition">Ringkasan Statistik</a></li>
                    <li><a href="#peta-persebaran" class="hover:text-emerald-400 transition">Peta Persebaran</a></li>
                    <li><a href="#data-pernikahan" class="hover:text-emerald-400 transition">Data Pernikahan</a></li>
                    <li><a href="#data-perceraian" class="hover:text-emerald-400 transition">Data Perceraian</a></li>
                    <li><a href="#faktor-perceraian" class="hover:text-emerald-400 transition">Faktor Perceraian</a></li>
                    <li><a href="#tentang-data" class="hover:text-emerald-400 transition">Tentang Data</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Layanan Publik -->
            <div>
                <span class="font-heading font-bold text-white text-xs uppercase tracking-wider block mb-3">Ketentuan Akses</span>
                <p class="text-slate-400 text-xs leading-relaxed mb-3">
                    Akses publik terbuka tanpa proses pendaftaran. Data disajikan untuk transparansi informasi publik dan edukasi masyarakat.
                </p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-950/80 text-emerald-400 border border-emerald-800/80 text-[11px]">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Layanan Terbuka Bebas Login</span>
                </div>
            </div>

            <!-- Kolom 4: Akses Khusus Petugas -->
            <div>
                <span class="font-heading font-bold text-white text-xs uppercase tracking-wider block mb-3">Akses Khusus</span>
                <p class="text-slate-400 text-xs leading-relaxed mb-3">
                    Khusus petugas Kementerian Agama RI dan administrator pengelola data statistik.
                </p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-emerald-950 text-slate-200 hover:text-white border border-slate-700 hover:border-emerald-500/50 text-xs font-semibold transition group shadow-xs">
                    <span class="material-symbols-outlined text-[17px] text-amber-400 group-hover:rotate-12 transition-transform">lock</span>
                    <span>Login Petugas</span>
                </a>
            </div>
        </div>

        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-slate-500 text-[11px]">
            <div>
                © 2026 <strong>Statistik Pernikahan &amp; Perceraian Indonesia</strong>. Seluruh hak cipta dilindungi.
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="hover:text-slate-300 transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-amber-400">admin_panel_settings</span>
                    <span>Login Petugas / Administrator</span>
                </a>
            </div>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- JAVASCRIPT LOGIC & AJAX SYNCHRONIZATION    -->
    <!-- ========================================== -->
    <script>
        // State Global Aplikasi Publik
        const appState = {
            tahun: '{{ $selectedTahun }}',
            jenis: '{{ $selectedJenis }}',
            provinsi: '{{ $selectedProvinsi }}',
            mapMode: '{{ $selectedJenis }}', // 'pernikahan' atau 'perceraian'
            chartTypeNikah: 'bar',
            chartTypeCerai: 'bar',
            mapData: null,
            geoJsonLayer: null,
            mapInstance: null,
            charts: {
                nikah: null,
                cerai: null,
                faktor: null
            }
        };

        // Data awal yang disuntikkan dari Blade Controller
        const initialTrenNikah = @json($trenPernikahan);
        const initialTrenCerai = @json($trenPerceraian);
        const initialFaktor = @json($faktorBreakdown);

        document.addEventListener('DOMContentLoaded', function () {
            initMobileMenu();
            initCharts();
            initLeafletMap();
            initFilterEvents();
            initChartTypeToggles();
        });

        // 1. Mobile Menu Toggle
        function initMobileMenu() {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            if (!btn || !menu) return;

            btn.addEventListener('click', function () {
                const isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden', isOpen);
                icon.textContent = isOpen ? 'menu' : 'close';
            });

            document.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    menu.classList.add('hidden');
                    icon.textContent = 'menu';
                });
            });
        }

        // 2. Format Angka Ribuan Indonesia
        function formatNumberId(num) {
            if (num === null || num === undefined || isNaN(num)) return 'Data tidak tersedia';
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // 3. Inisialisasi Chart.js
        function initCharts() {
            // Chart Tren Pernikahan
            const ctxNikah = document.getElementById('canvas-tren-nikah')?.getContext('2d');
            if (ctxNikah) {
                appState.charts.nikah = new Chart(ctxNikah, {
                    type: 'bar',
                    data: {
                        labels: initialTrenNikah.labels,
                        datasets: [{
                            label: 'Jumlah Pernikahan',
                            data: initialTrenNikah.data,
                            backgroundColor: '#2563eb',
                            borderColor: '#1d4ed8',
                            borderWidth: 1.5,
                            borderRadius: 8,
                            fill: false,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(c) {
                                        let val = c.raw;
                                        let extra = (c.label === '2024') ? ' (s.d. September)' : '';
                                        return ' ' + formatNumberId(val) + ' Peristiwa' + extra;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(v) { return formatNumberId(v); }
                                }
                            }
                        }
                    }
                });
            }

            // Chart Tren Perceraian
            const ctxCerai = document.getElementById('canvas-tren-cerai')?.getContext('2d');
            if (ctxCerai) {
                appState.charts.cerai = new Chart(ctxCerai, {
                    type: 'bar',
                    data: {
                        labels: initialTrenCerai.labels,
                        datasets: [{
                            label: 'Jumlah Perceraian',
                            data: initialTrenCerai.data,
                            backgroundColor: '#e11d48',
                            borderColor: '#be123c',
                            borderWidth: 1.5,
                            borderRadius: 8,
                            fill: false,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(c) {
                                        return ' ' + formatNumberId(c.raw) + ' Perkara';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(v) { return formatNumberId(v); }
                                }
                            }
                        }
                    }
                });
            }

            // Chart Faktor Perceraian (Diagram Lingkaran / Doughnut Chart)
            const ctxFaktor = document.getElementById('canvas-faktor-chart')?.getContext('2d');
            if (ctxFaktor) {
                const colors = [
                    '#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6',
                    '#F97316', '#14B8A6', '#6366F1', '#EC4899', '#06B6D4',
                    '#7C3AED', '#D946EF', '#64748B', '#71717A'
                ];
                appState.charts.faktor = new Chart(ctxFaktor, {
                    type: 'doughnut',
                    data: {
                        labels: initialFaktor.labels,
                        datasets: [{
                            label: 'Jumlah Perkara',
                            data: initialFaktor.data,
                            backgroundColor: colors.slice(0, initialFaktor.labels.length),
                            borderColor: '#FFFFFF',
                            borderWidth: 2,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '66%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 10,
                                callbacks: {
                                    label: function(c) {
                                        const dataset = c.dataset.data;
                                        const total = dataset.reduce((a, b) => a + b, 0);
                                        const val = c.raw;
                                        const pct = total > 0 ? Math.round((val / total) * 1000) / 10 : 0;
                                        return ' ' + formatNumberId(val) + ' Perkara (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Switch Tipe Diagram Faktor (Donat / Lingkaran Penuh / Batang)
        window.switchFaktorChartType = function(type) {
            if (!appState.charts.faktor) return;

            const btnDonut = document.getElementById('btn-faktor-donut');
            const btnPie = document.getElementById('btn-faktor-pie');
            const btnBar = document.getElementById('btn-faktor-bar');
            const centerText = document.getElementById('faktor-donut-center');

            [btnDonut, btnPie, btnBar].forEach(b => {
                if (b) {
                    b.classList.remove('bg-white', 'text-amber-700', 'shadow-xs');
                    b.classList.add('text-slate-600');
                }
            });

            if (type === 'doughnut') {
                if (btnDonut) {
                    btnDonut.classList.add('bg-white', 'text-amber-700', 'shadow-xs');
                    btnDonut.classList.remove('text-slate-600');
                }
                appState.charts.faktor.config.type = 'doughnut';
                appState.charts.faktor.options.cutout = '66%';
                if (centerText) centerText.style.display = 'flex';
            } else if (type === 'pie') {
                if (btnPie) {
                    btnPie.classList.add('bg-white', 'text-amber-700', 'shadow-xs');
                    btnPie.classList.remove('text-slate-600');
                }
                appState.charts.faktor.config.type = 'pie';
                appState.charts.faktor.options.cutout = 0;
                if (centerText) centerText.style.display = 'none';
            } else if (type === 'bar') {
                if (btnBar) {
                    btnBar.classList.add('bg-white', 'text-amber-700', 'shadow-xs');
                    btnBar.classList.remove('text-slate-600');
                }
                appState.charts.faktor.config.type = 'bar';
                appState.charts.faktor.options.indexAxis = 'y';
                if (centerText) centerText.style.display = 'none';
            }

            appState.charts.faktor.update();
        };

        // 4. Toggle Jenis Chart (Batang vs Garis)
        function initChartTypeToggles() {
            document.querySelectorAll('.btn-chart-type').forEach(btn => {
                btn.addEventListener('click', function () {
                    const chartKey = this.dataset.chart;
                    const type = this.dataset.type;
                    const chartInstance = appState.charts[chartKey];
                    if (!chartInstance) return;

                    // Update button styling
                    const parent = this.parentElement;
                    parent.querySelectorAll('.btn-chart-type').forEach(b => {
                        b.classList.remove('bg-white', 'shadow-sm', 'text-blue-600', 'text-rose-600');
                        b.classList.add('text-slate-600');
                    });
                    this.classList.add('bg-white', 'shadow-sm', chartKey === 'nikah' ? 'text-blue-600' : 'text-rose-600');
                    this.classList.remove('text-slate-600');

                    // Update chart
                    chartInstance.config.type = type;
                    chartInstance.update();
                });
            });
        }

        // 5. Inisialisasi Peta Choropleth Leaflet (Persis Admin)
        let activeSelectedLayer = null;
        let dkiJakartaLayer = null;

        const COLOR_SCALE_NIKAH = [
            '#DBEAFE', // Sangat Rendah
            '#93C5FD', // Rendah
            '#3B82F6', // Sedang
            '#1D4ED8', // Tinggi
            '#0B2559'  // Tertinggi
        ];

        const COLOR_SCALE_CERAI = [
            '#FECDD3', // Sangat Rendah
            '#FB7185', // Rendah
            '#E11D48', // Sedang
            '#BE123C', // Tinggi
            '#881337'  // Tertinggi
        ];

        const COLOR_NO_DATA = '#CBD5E1'; // Netral abu-abu

        const factorColors = [
            '#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6',
            '#F97316', '#EC4899', '#6366F1', '#14B8A6', '#64748B'
        ];

        function initLeafletMap() {
            const mapEl = document.getElementById('map-container');
            if (!mapEl) return;

            // Inisialisasi koordinat tengah Indonesia
            appState.mapInstance = L.map('map-container', {
                center: [-2.5489, 118.0149],
                zoom: 5,
                minZoom: 4,
                maxZoom: 18,
                zoomControl: false,
                attributionControl: false
            });

            // Zoom Control di kiri atas
            L.control.zoom({ position: 'topleft' }).addTo(appState.mapInstance);

            // Custom Reset Control (Home Button) di bawah Zoom Control (Persis Admin)
            const ResetControl = L.Control.extend({
                options: { position: 'topleft' },
                onAdd: function () {
                    const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                    const button = L.DomUtil.create('a', '', container);
                    button.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px; line-height:30px; display:block;">home</span>';
                    button.href = '#';
                    button.title = 'Tampilkan Seluruh Indonesia';
                    button.style.width = '30px';
                    button.style.height = '30px';
                    button.style.textAlign = 'center';
                    button.style.backgroundColor = '#FFFFFF';
                    button.style.cursor = 'pointer';

                    L.DomEvent.on(button, 'click', function (e) {
                        L.DomEvent.stopPropagation(e);
                        L.DomEvent.preventDefault(e);
                        resetMapView();
                    });

                    return container;
                }
            });
            appState.mapInstance.addControl(new ResetControl());

            // Peta Dasar OpenStreetMap (100% Gratis, Bebas & TIDAK Memerlukan API Key Apapun - Persis Admin)
            const basemap = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                opacity: 0.65
            });
            basemap.addTo(appState.mapInstance);

            // Fetch GeoJSON dan data peta
            loadMapChoropleth();

            // Bind Event Kontrol Peta
            document.getElementById('btn-reset-map-view')?.addEventListener('click', resetMapView);

            const mapFilterTahun = document.getElementById('map-filter-tahun');
            if (mapFilterTahun) {
                mapFilterTahun.addEventListener('change', function () {
                    appState.tahun = this.value;
                    const globalTahun = document.getElementById('filter-tahun');
                    if (globalTahun) globalTahun.value = this.value;
                    fetchUpdatedStats();
                });
            }

            const btnModeNikah = document.getElementById('map-mode-nikah');
            const btnModeCerai = document.getElementById('map-mode-cerai');

            btnModeNikah?.addEventListener('click', function () {
                setMapMode('pernikahan');
                const globalJenis = document.getElementById('filter-jenis');
                if (globalJenis) globalJenis.value = 'pernikahan';
                fetchUpdatedStats();
            });

            btnModeCerai?.addEventListener('click', function () {
                setMapMode('perceraian');
                const globalJenis = document.getElementById('filter-jenis');
                if (globalJenis) globalJenis.value = 'perceraian';
                fetchUpdatedStats();
            });
        }

        function setMapMode(mode) {
            appState.mapMode = mode;
            const btnModeNikah = document.getElementById('map-mode-nikah');
            const btnModeCerai = document.getElementById('map-mode-cerai');
            const panelBadge = document.getElementById('panel-ranking-mode-badge');
            const tertinggiLabel = document.getElementById('map-stat-tertinggi-label');
            const terendahLabel = document.getElementById('map-stat-terendah-label');
            const legendTitle = document.getElementById('legend-title');
            const periodeInfo = document.getElementById('map-periode-info');

            if (mode === 'pernikahan') {
                if (btnModeNikah) btnModeNikah.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-blue-600 text-white shadow-xs';
                if (btnModeCerai) btnModeCerai.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900';
                if (panelBadge) { panelBadge.textContent = 'Pernikahan'; panelBadge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-800'; }
                if (tertinggiLabel) tertinggiLabel.textContent = 'Pernikahan Tertinggi';
                if (terendahLabel) terendahLabel.textContent = 'Pernikahan Terendah';
                if (legendTitle) legendTitle.textContent = 'Jumlah Pernikahan';
                if (periodeInfo) periodeInfo.textContent = `Tahun ${appState.tahun} • Mode Pernikahan`;
            } else {
                if (btnModeCerai) btnModeCerai.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-rose-600 text-white shadow-xs';
                if (btnModeNikah) btnModeNikah.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900';
                if (panelBadge) { panelBadge.textContent = 'Perceraian'; panelBadge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded bg-rose-100 text-rose-800'; }
                if (tertinggiLabel) tertinggiLabel.textContent = 'Perceraian Tertinggi';
                if (terendahLabel) terendahLabel.textContent = 'Perceraian Terendah';
                if (legendTitle) legendTitle.textContent = 'Jumlah Perceraian';
                if (periodeInfo) periodeInfo.textContent = `Tahun ${appState.tahun} • Mode Perceraian`;
            }

            renderGeoJsonLayer();
        }

        async function loadMapChoropleth() {
            try {
                const cacheBuster = '_v=' + Date.now();
                const [geoJsonResponse, statsMapResponse] = await Promise.all([
                    fetch('/geojson/indonesia-provinces.json?' + cacheBuster).then(r => r.json()),
                    fetch(`/api/public-map?tahun=${encodeURIComponent(appState.tahun)}&jenis_statistik=${encodeURIComponent(appState.mapMode)}&${cacheBuster}`).then(r => r.json())
                ]);

                appState.geoJsonRaw = geoJsonResponse;
                appState.mapData = statsMapResponse;

                renderGeoJsonLayer();
            } catch (err) {
                console.error("Gagal memuat data peta:", err);
            }
        }

        function getChoroplethColor(value, minVal, maxVal, hasData, mode) {
            if (!hasData || value === null || value === undefined) {
                return COLOR_NO_DATA;
            }

            const scale = (mode === 'pernikahan') ? COLOR_SCALE_NIKAH : COLOR_SCALE_CERAI;

            if (maxVal <= minVal || maxVal === 0) {
                return scale[2];
            }

            const ratio = (value - minVal) / (maxVal - minVal);
            if (ratio <= 0.20) return scale[0]; // Sangat Rendah
            if (ratio <= 0.40) return scale[1]; // Rendah
            if (ratio <= 0.60) return scale[2]; // Sedang
            if (ratio <= 0.80) return scale[3]; // Tinggi
            return scale[4];                    // Tertinggi
        }

        function renderGeoJsonLayer() {
            if (!appState.geoJsonRaw || !appState.mapData || !appState.mapInstance) return;

            if (appState.geoJsonLayer) {
                appState.mapInstance.removeLayer(appState.geoJsonLayer);
            }

            dkiJakartaLayer = null;

            const mode = appState.mapMode;
            const dataMap = appState.mapData.data || {};
            const minVal = appState.mapData.min_val || 0;
            const maxVal = appState.mapData.max_val || 0;

            // Render GeoJSON
            appState.geoJsonLayer = L.geoJSON(appState.geoJsonRaw, {
                style: function (feature) {
                    const props = feature.properties || {};
                    const kode = props.kode_wilayah || props.kode || '';
                    const name = props.name || props.NAME_1 || props.Propinsi || '';
                    const normName = name.trim().toLowerCase();

                    const provItem = dataMap[kode] || dataMap[name] || dataMap[normName];
                    const val = provItem ? (mode === 'pernikahan' ? provItem.nikah ?? provItem.nilai : provItem.cerai ?? provItem.nilai) : null;
                    const hasData = (val !== null && val !== undefined);
                    const color = getChoroplethColor(val, minVal, maxVal, hasData, mode);

                    const isSelected = activeSelectedLayer && activeSelectedLayer.feature === feature;

                    return {
                        fillColor: color,
                        weight: isSelected ? 3.5 : 1.2,
                        opacity: 1,
                        color: isSelected ? '#F59E0B' : '#FFFFFF',
                        dashArray: '',
                        fillOpacity: isSelected ? 0.95 : 0.85
                    };
                },
                onEachFeature: function (feature, layer) {
                    const props = feature.properties || {};
                    const kode = props.kode_wilayah || props.kode || '';
                    const pName = props.name || props.NAME_1 || props.Propinsi || 'Provinsi';
                    const normName = pName.trim().toLowerCase();
                    const provItem = dataMap[kode] || dataMap[pName] || dataMap[normName];

                    // Simpan referensi DKI Jakarta agar selalu di paling depan
                    if (pName === 'DKI Jakarta' || kode === '31') {
                        dkiJakartaLayer = layer;
                    }

                    const val = provItem ? (mode === 'pernikahan' ? provItem.nikah ?? provItem.nilai : provItem.cerai ?? provItem.nilai) : null;
                    const hasData = (val !== null && val !== undefined);
                    const totalNasional = appState.mapData.total_nasional || 1;
                    const pct = (hasData && totalNasional > 0) ? roundVal((val / totalNasional) * 100, 1) : 0;

                    // Mouseover & Mouseout Event
                    layer.on('mouseover', function (e) {
                        const l = e.target;
                        l.setStyle({
                            weight: 2.5,
                            color: '#0A2540',
                            fillOpacity: 0.95
                        });
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                            l.bringToFront();
                        }
                    });

                    layer.on('mouseout', function (e) {
                        if (activeSelectedLayer === e.target) {
                            e.target.setStyle({
                                weight: 3.5,
                                color: '#F59E0B',
                                fillOpacity: 0.95
                            });
                        } else {
                            appState.geoJsonLayer.resetStyle(e.target);
                        }
                        ensureDkiOnTop();
                    });

                    // Click Event
                    layer.on('click', function (e) {
                        selectProvince(layer, pName, kode, provItem);
                    });

                    // Tooltip Interaktif Hover (Persis Admin)
                    let tooltipContent = '';
                    if (hasData) {
                        const unitLabel = mode === 'pernikahan' ? 'Akta' : 'Perkara';
                        const jenisTitle = mode === 'pernikahan' ? 'Jumlah Pernikahan:' : 'Jumlah Perceraian:';
                        const colorClass = mode === 'pernikahan' ? 'text-blue-400' : 'text-rose-400';
                        tooltipContent = `
                            <div class="p-3 bg-slate-900/95 backdrop-blur-md text-white rounded-xl shadow-2xl border border-slate-700 text-xs min-w-[210px] max-w-[270px] font-sans">
                                <div class="font-bold text-sm text-white pb-1.5 border-b border-slate-700/80 flex items-center justify-between">
                                    <span class="truncate">${pName}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-500/30 text-blue-300 font-mono font-normal">Kode: ${kode || '-'}</span>
                                </div>
                                <div class="space-y-1.5 mt-2">
                                    <div class="flex justify-between items-baseline">
                                        <span class="text-slate-400 text-[11px]">Tahun:</span>
                                        <span class="font-bold text-white font-mono">${appState.tahun}</span>
                                    </div>
                                    <div class="flex justify-between items-baseline">
                                        <span class="text-slate-400 text-[11px]">${jenisTitle}</span>
                                        <span class="font-bold font-mono ${colorClass} text-sm">${formatNumberId(val)} <span class="text-[10px] font-normal text-slate-400">${unitLabel}</span></span>
                                    </div>
                                    <div class="flex justify-between items-baseline pt-1 border-t border-slate-800 text-[11px]">
                                        <span class="text-slate-400">Porsi Nasional:</span>
                                        <span class="font-bold font-mono text-amber-400">${pct}%</span>
                                    </div>
                                    <div class="text-[10px] text-blue-300 font-medium italic pt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">touch_app</span>
                                        <span>Klik untuk rincian wilayah</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        tooltipContent = `
                            <div class="p-3 bg-slate-900/95 backdrop-blur-md text-white rounded-xl shadow-2xl border border-slate-700 text-xs min-w-[200px] font-sans">
                                <div class="font-bold text-sm text-white mb-1.5 pb-1 border-b border-slate-700">
                                    ${pName}
                                </div>
                                <div class="text-[11px] text-amber-400 font-semibold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm">warning</span>
                                    <span>Data tidak tersedia untuk periode ini</span>
                                </div>
                            </div>
                        `;
                    }

                    layer.bindTooltip(tooltipContent, {
                        sticky: true,
                        className: 'leaflet-tooltip-indonesia',
                        direction: 'top',
                        offset: [0, -10]
                    });
                }
            }).addTo(appState.mapInstance);

            ensureDkiOnTop();

            // Perbarui UI Statistik & Ranking
            updateMapUI(appState.mapData);
        }

        function ensureDkiOnTop() {
            if (dkiJakartaLayer && typeof dkiJakartaLayer.bringToFront === 'function') {
                dkiJakartaLayer.bringToFront();
            }
        }

        function selectProvince(layer, name, kode, provItem) {
            if (activeSelectedLayer && activeSelectedLayer !== layer) {
                appState.geoJsonLayer.resetStyle(activeSelectedLayer);
            }

            activeSelectedLayer = layer;
            layer.setStyle({
                weight: 3.5,
                color: '#F59E0B',
                fillOpacity: 0.95
            });
            if (layer.bringToFront) layer.bringToFront();

            if (layer.getBounds) {
                appState.mapInstance.fitBounds(layer.getBounds(), { padding: [40, 40], maxZoom: 8 });
            }

            const nikahFormatted = provItem && provItem.nikah_formatted ? provItem.nikah_formatted : 'Data tidak tersedia';
            const ceraiFormatted = provItem && provItem.cerai_formatted ? provItem.cerai_formatted : 'Data tidak tersedia';
            const topFaktorNama = (provItem && provItem.top_faktor) ? provItem.top_faktor.nama : 'Data tidak tersedia';
            const topFaktorKasus = (provItem && provItem.top_faktor) ? `${provItem.top_faktor.kasus_format || provItem.top_faktor.kasus} Kasus` : '';

            const popupHtml = `
                <div class="p-3.5 bg-white text-slate-900 rounded-xl font-sans min-w-[240px] text-xs">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-200">
                        <div>
                            <span class="font-bold text-sm text-slate-900 block">${name}</span>
                            <span class="text-[10px] text-slate-500 font-mono">Kode: ${kode || '-'} • Tahun ${appState.tahun}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600 font-medium">Jumlah Pernikahan:</span>
                            <span class="font-bold font-mono text-blue-700">${nikahFormatted}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600 font-medium">Jumlah Perceraian:</span>
                            <span class="font-bold font-mono text-rose-700">${ceraiFormatted}</span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-100">
                            <span class="text-[10px] font-semibold text-amber-700 uppercase block">Faktor Perceraian Tertinggi:</span>
                            <span class="font-bold text-slate-800 text-[11px] block truncate">${topFaktorNama}</span>
                            ${topFaktorKasus ? `<span class="text-[10px] text-slate-500 font-mono block">${topFaktorKasus}</span>` : ''}
                        </div>
                    </div>
                    <div class="pt-2.5 mt-2.5 border-t border-slate-200 flex items-center justify-between gap-2">
                        <button type="button" onclick="showProvinsiFaktorModalPengguna('${name}', '${kode}', appState.mapData.data ? appState.mapData.data['${kode}'] || appState.mapData.data['${name}'] : null)" class="w-full py-1.5 rounded-lg bg-blue-600 text-white font-semibold text-xs hover:bg-blue-700 transition flex items-center justify-center gap-1 shadow-xs">
                            <span class="material-symbols-outlined text-[15px]">info</span>
                            <span>Rincian Lengkap</span>
                        </button>
                    </div>
                </div>
            `;

            layer.bindPopup(popupHtml, { offset: [0, -10] }).openPopup();
        }

        function highlightAndZoomToProvince(provTarget) {
            if (!appState.geoJsonLayer) return;

            let targetLayer = null;
            appState.geoJsonLayer.eachLayer(l => {
                const props = l.feature && l.feature.properties ? l.feature.properties : {};
                const name = props.name || props.NAME_1 || props.Propinsi;
                const kode = props.kode_wilayah || props.kode;
                if (name === provTarget || kode === provTarget) {
                    targetLayer = l;
                }
            });

            if (targetLayer) {
                const props = targetLayer.feature.properties;
                const name = props.name || props.NAME_1 || props.Propinsi;
                const kode = props.kode_wilayah || props.kode;
                const provItem = appState.mapData && appState.mapData.data ? 
                    (appState.mapData.data[kode] || appState.mapData.data[name]) : null;
                selectProvince(targetLayer, name, kode, provItem);
            }
        }

        function resetMapView() {
            if (activeSelectedLayer && appState.geoJsonLayer) {
                appState.geoJsonLayer.resetStyle(activeSelectedLayer);
                activeSelectedLayer = null;
            }
            if (appState.geoJsonLayer && appState.mapInstance) {
                appState.mapInstance.closePopup();
                appState.mapInstance.fitBounds(appState.geoJsonLayer.getBounds(), { padding: [20, 20] });
            }
            ensureDkiOnTop();
        }

        function updateMapUI(mapData) {
            if (!mapData) return;

            const mode = appState.mapMode;
            const dataMap = mapData.data || {};
            const totalNasional = mapData.total_nasional || 0;
            const totalFormatted = mapData.total_formatted || formatNumberId(totalNasional);

            // Update 3 Cards
            const statTotal = document.getElementById('map-stat-total');
            if (statTotal) statTotal.textContent = totalFormatted + (mode === 'pernikahan' ? ' Akta' : ' Perkara');

            // Hitung Provinsi Tertinggi & Terendah dari dataset aktual
            const validList = [];
            const processedProvs = new Set();
            Object.values(dataMap).forEach(item => {
                if (item && item.provinsi && !processedProvs.has(item.provinsi)) {
                    processedProvs.add(item.provinsi);
                    const val = mode === 'pernikahan' ? item.nikah ?? item.nilai : item.cerai ?? item.nilai;
                    if (val !== null && val !== undefined) {
                        validList.push({
                            provinsi: item.provinsi,
                            kode_wilayah: item.kode_wilayah,
                            jumlah: val,
                            jumlah_formatted: formatNumberId(val)
                        });
                    }
                }
            });

            validList.sort((a, b) => b.jumlah - a.jumlah);

            const tertinggi = validList.length > 0 ? validList[0] : { provinsi: '-', jumlah_formatted: '-' };
            const terendah = validList.length > 0 ? validList[validList.length - 1] : { provinsi: '-', jumlah_formatted: '-' };

            const statTertinggi = document.getElementById('map-stat-tertinggi');
            const statTertinggiVal = document.getElementById('map-stat-tertinggi-val');
            if (statTertinggi) statTertinggi.textContent = tertinggi.provinsi;
            if (statTertinggiVal) statTertinggiVal.textContent = tertinggi.jumlah_formatted + (mode === 'pernikahan' ? ' Akta' : ' Perkara');

            const statTerendah = document.getElementById('map-stat-terendah');
            const statTerendahVal = document.getElementById('map-stat-terendah-val');
            if (statTerendah) statTerendah.textContent = terendah.provinsi;
            if (statTerendahVal) statTerendahVal.textContent = terendah.jumlah_formatted + (mode === 'pernikahan' ? ' Akta' : ' Perkara');

            // Update Legenda Angka
            const minVal = mapData.min_val || (validList.length > 0 ? validList[validList.length - 1].jumlah : 0);
            const maxVal = mapData.max_val || (validList.length > 0 ? validList[0].jumlah : 0);
            const midVal = Math.round((minVal + maxVal) / 2);

            const legMin = document.getElementById('legend-min');
            const legMid = document.getElementById('legend-mid');
            const legMax = document.getElementById('legend-max');
            if (legMin) legMin.textContent = formatNumberId(minVal);
            if (legMid) legMid.textContent = formatNumberId(midVal);
            if (legMax) legMax.textContent = formatNumberId(maxVal);

            // Update Gradient Bar Colors
            const gradBar = document.getElementById('legend-gradient-bar');
            if (gradBar) {
                const scale = (mode === 'pernikahan') ? COLOR_SCALE_NIKAH : COLOR_SCALE_CERAI;
                gradBar.innerHTML = scale.map(c => `<div style="background-color: ${c};"></div>`).join('');
            }

            // Update Top 10 Ranking Panel (Persis Admin)
            const top10Container = document.getElementById('top-10-ranking-list');
            if (top10Container) {
                const top10 = validList.slice(0, 10);
                if (top10.length === 0) {
                    top10Container.innerHTML = '<div class="text-center py-8 text-xs text-slate-400">Data tidak tersedia untuk periode ini.</div>';
                } else {
                    const maxTop = top10[0].jumlah || 1;
                    const barColor = mode === 'pernikahan' ? 'bg-blue-600' : 'bg-rose-600';
                    const unit = mode === 'pernikahan' ? 'Akta' : 'Perkara';

                    top10Container.innerHTML = top10.map((item, idx) => {
                        const pct = Math.round((item.jumlah / maxTop) * 100);
                        const rankNum = String(idx + 1).padStart(2, '0');
                        return `
                            <div onclick="highlightAndZoomToProvince('${item.provinsi}')" class="p-2.5 rounded-xl bg-white hover:bg-slate-100/80 border border-slate-200/70 transition-all cursor-pointer flex flex-col gap-1.5 shadow-2xs group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <span class="w-5 h-5 rounded-md font-mono font-bold text-[10px] flex items-center justify-center shrink-0 ${idx < 3 ? (mode === 'pernikahan' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700') : 'bg-slate-100 text-slate-500'}">
                                            ${rankNum}
                                        </span>
                                        <span class="font-semibold text-slate-800 text-xs truncate group-hover:text-blue-600 transition-colors">${item.provinsi}</span>
                                    </div>
                                    <span class="font-mono font-bold text-slate-900 text-xs shrink-0">${item.jumlah_formatted} <span class="text-[10px] font-normal text-slate-400">${unit}</span></span>
                                </div>
                                <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full transition-all ${barColor}" style="width: ${pct}%;"></div>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            }
        }

        function roundVal(num, decimals) {
            return Number(Math.round(num + 'e' + decimals) + 'e-' + decimals);
        }

        // Global Modal Functions untuk Rincian Faktor Perceraian (Persis Admin)
        window.showProvinsiFaktorModalPengguna = function(name, kode, provItem) {
            const modal = document.getElementById('modal-provinsi-faktor-pengguna');
            if (!modal) return;

            const provName = provItem ? provItem.provinsi : name;
            const provKode = kode || (provItem ? provItem.kode_wilayah : '-');

            document.getElementById('modal-prov-nama').textContent = provName;
            document.getElementById('modal-prov-kode').textContent = 'Kode: ' + provKode;
            document.getElementById('modal-prov-tahun').textContent = appState.tahun;

            const nikahText = provItem && provItem.nikah_formatted ? provItem.nikah_formatted : (provItem && provItem.nikah ? formatNumberId(provItem.nikah) + ' Akta' : 'Data tidak tersedia');
            const ceraiText = provItem && provItem.cerai_formatted ? provItem.cerai_formatted : (provItem && provItem.cerai ? formatNumberId(provItem.cerai) + ' Perkara' : 'Data tidak tersedia');

            document.getElementById('modal-stat-nikah').textContent = nikahText;
            document.getElementById('modal-stat-cerai').textContent = ceraiText;

            function renderFactors(topFaktor, breakdown) {
                if (topFaktor) {
                    document.getElementById('modal-top-nama').textContent = topFaktor.nama;
                    document.getElementById('modal-top-kasus').textContent = `${topFaktor.kasus_formatted || topFaktor.total_formatted || topFaktor.kasus || topFaktor.kasus_format} Kasus`;
                    document.getElementById('modal-top-pct').textContent = `${topFaktor.persentase || topFaktor.persen}%`;
                } else {
                    document.getElementById('modal-top-nama').textContent = 'Data tidak tersedia';
                    document.getElementById('modal-top-kasus').textContent = '-';
                    document.getElementById('modal-top-pct').textContent = '-';
                }

                const listContainer = document.getElementById('modal-factors-list');
                if (!breakdown || breakdown.length === 0) {
                    listContainer.innerHTML = '<div class="text-center py-4 text-xs text-slate-400">Data rincian faktor tidak ditemukan untuk periode ini.</div>';
                    return;
                }

                listContainer.innerHTML = breakdown.map((item, idx) => {
                    const color = factorColors[idx % factorColors.length];
                    const kasusNum = item.kasus_formatted || item.total_formatted || item.kasus || item.total || formatNumberId(item.kasus || item.total);
                    const pctVal = item.persentase || item.persen || 0;
                    return `
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col gap-1 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <span class="w-5 h-5 rounded-md font-mono font-bold text-[10px] flex items-center justify-center shrink-0" style="background-color: ${color}20; color: ${color};">
                                        ${idx + 1}
                                    </span>
                                    <span class="font-semibold text-slate-800 truncate">${item.faktor || item.faktor_penyebab}</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-mono font-bold text-slate-900 text-[11px]">${kasusNum} Kasus</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold" style="background-color: ${color}20; color: ${color};">
                                        ${pctVal}%
                                    </span>
                                </div>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full rounded-full transition-all" style="width: ${Math.max(pctVal, 2)}%; background-color: ${color};"></div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            document.getElementById('modal-factors-list').innerHTML = '<div class="text-center py-4 text-xs text-slate-400">Memuat data rincian faktor BPS...</div>';

            fetch(`/api/faktor-perceraian?provinsi=${encodeURIComponent(provName)}&tahun=${encodeURIComponent(appState.tahun)}`)
                .then(res => res.json())
                .then(data => {
                    renderFactors(data.top_faktor, data.breakdown || data.items || []);
                })
                .catch(err => {
                    console.error('Gagal mengambil data faktor perceraian:', err);
                    document.getElementById('modal-factors-list').innerHTML = '<div class="text-center py-4 text-xs text-rose-600">Gagal memuat rincian faktor.</div>';
                });

            modal.classList.remove('hidden');
        };

        window.closeProvinsiFaktorModalPengguna = function() {
            const modal = document.getElementById('modal-provinsi-faktor-pengguna');
            if (modal) modal.classList.add('hidden');
        };

        // Window function aliases for 100% parity with admin map signatures
        window.closeProvinsiFaktorModal = window.closeProvinsiFaktorModalPengguna;
        window.showProvinsiFaktorModal = window.showProvinsiFaktorModalPengguna;
        window.highlightAndZoomToProvince = highlightAndZoomToProvince;
        window.highlightProvinceFromList = highlightAndZoomToProvince;

        // 6. Sinkronisasi Filter Interaktif (AJAX Fetch ke Controller)
        function initFilterEvents() {
            const filterTahun = document.getElementById('filter-tahun');
            const filterJenis = document.getElementById('filter-jenis');
            const filterProvinsi = document.getElementById('filter-provinsi');
            const btnReset = document.getElementById('btn-reset-filter');

            const handleFilterChange = () => {
                appState.tahun = filterTahun.value;
                appState.jenis = filterJenis.value;
                appState.provinsi = filterProvinsi.value;
                appState.mapMode = filterJenis.value;

                // Sync UI notices
                const notice2024 = document.getElementById('notice-2024');
                if (notice2024) {
                    notice2024.classList.toggle('hidden', appState.tahun !== '2024');
                }

                const scopeLabel = document.getElementById('filter-active-scope-label');
                if (scopeLabel) {
                    const pText = appState.provinsi ? appState.provinsi : 'Nasional';
                    scopeLabel.textContent = `Cakupan: ${pText} (${appState.tahun})`;
                }

                // Jalankan AJAX Refresh
                fetchUpdatedStats();
            };

            filterTahun?.addEventListener('change', handleFilterChange);
            filterJenis?.addEventListener('change', handleFilterChange);
            filterProvinsi?.addEventListener('change', handleFilterChange);

            btnReset?.addEventListener('click', function () {
                if (filterTahun) filterTahun.value = '2025';
                if (filterJenis) filterJenis.value = 'pernikahan';
                if (filterProvinsi) filterProvinsi.value = '';
                handleFilterChange();
            });
        }

        async function fetchUpdatedStats() {
            const loadingBadge = document.getElementById('filter-loading-badge');
            if (loadingBadge) loadingBadge.classList.remove('hidden');

            try {
                // Fetch stats JSON & map data secara paralel
                const [statsRes, mapRes] = await Promise.all([
                    fetch(`/api/public-stats?tahun=${encodeURIComponent(appState.tahun)}&provinsi=${encodeURIComponent(appState.provinsi)}&jenis_statistik=${encodeURIComponent(appState.jenis)}`).then(r => r.json()),
                    fetch(`/api/public-map?tahun=${encodeURIComponent(appState.tahun)}&jenis_statistik=${encodeURIComponent(appState.mapMode)}`).then(r => r.json())
                ]);

                if (statsRes.success) {
                    // Update KPI Cards
                    updateKpiCards(statsRes.kpi);

                    // Update Charts
                    updateChartsData(statsRes);

                    // Update Rankings
                    updateRankingsList(statsRes.rankingNikah, statsRes.rankingCerai, appState.tahun);
                }

                // Update Map
                appState.mapData = mapRes;
                setMapMode(appState.mapMode);

            } catch (err) {
                console.error("Gagal sinkronisasi data filter:", err);
            } finally {
                if (loadingBadge) loadingBadge.classList.add('hidden');
            }
        }

        function updateKpiCards(kpi) {
            if (!kpi) return;
            const elNikah = document.getElementById('kpi-total-nikah');
            const elCerai = document.getElementById('kpi-total-cerai');
            const elTopFaktor = document.getElementById('kpi-top-faktor');
            const elTopKasus = document.getElementById('kpi-top-kasus');
            const elProv = document.getElementById('kpi-provinsi-tercover');
            const elNikahScope = document.getElementById('kpi-nikah-scope');
            const elCeraiScope = document.getElementById('kpi-cerai-scope');
            const elNikahNote = document.getElementById('kpi-nikah-note');
            const elCeraiNote = document.getElementById('kpi-cerai-note');

            if (elNikah) elNikah.textContent = formatNumberId(kpi.total_nikah);
            if (elCerai) elCerai.textContent = formatNumberId(kpi.total_cerai);
            if (elTopFaktor) {
                elTopFaktor.textContent = kpi.top_faktor_nama;
                elTopFaktor.title = kpi.top_faktor_nama;
            }
            if (elTopKasus) elTopKasus.textContent = formatNumberId(kpi.top_faktor_kasus) + ' Perkara Tercatat';
            if (elProv) elProv.textContent = kpi.provinsi_tercover + ' Provinsi';
            if (elNikahScope) elNikahScope.textContent = kpi.tahun_label + ' • ' + kpi.provinsi_label;
            if (elCeraiScope) elCeraiScope.textContent = kpi.tahun_label + ' • ' + kpi.provinsi_label;
            if (elNikahNote) elNikahNote.textContent = kpi.catatan_nikah;
            if (elCeraiNote) elCeraiNote.textContent = kpi.catatan_cerai;
        }

        function updateChartsData(statsRes) {
            // Update Chart Pernikahan
            if (appState.charts.nikah && statsRes.trenPernikahan) {
                appState.charts.nikah.data.labels = statsRes.trenPernikahan.labels;
                appState.charts.nikah.data.datasets[0].data = statsRes.trenPernikahan.data;
                appState.charts.nikah.update();

                const sublabel = document.getElementById('tren-nikah-sublabel');
                if (sublabel) sublabel.textContent = 'Cakupan: ' + (appState.provinsi || 'Seluruh Indonesia');
            }

            // Update Chart Perceraian
            if (appState.charts.cerai && statsRes.trenPerceraian) {
                appState.charts.cerai.data.labels = statsRes.trenPerceraian.labels;
                appState.charts.cerai.data.datasets[0].data = statsRes.trenPerceraian.data;
                appState.charts.cerai.update();

                const sublabel = document.getElementById('tren-cerai-sublabel');
                if (sublabel) sublabel.textContent = 'Cakupan: ' + (appState.provinsi || 'Seluruh Indonesia');
            }

            // Update Chart Faktor (Diagram Lingkaran)
            if (appState.charts.faktor && statsRes.faktorBreakdown) {
                const fData = statsRes.faktorBreakdown;
                const colors = [
                    '#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6',
                    '#F97316', '#14B8A6', '#6366F1', '#EC4899', '#06B6D4',
                    '#7C3AED', '#D946EF', '#64748B', '#71717A'
                ];
                appState.charts.faktor.data.labels = fData.labels;
                appState.charts.faktor.data.datasets[0].data = fData.data;
                appState.charts.faktor.data.datasets[0].backgroundColor = colors.slice(0, fData.labels.length);
                appState.charts.faktor.update();

                // Update center overlay text & badges
                const topFaktor = fData.top_faktor || (fData.items && fData.items[0]);
                const topName = topFaktor ? (topFaktor.faktor || topFaktor.faktor_penyebab) : '-';
                const topPct = topFaktor ? (topFaktor.persen || topFaktor.persentase || 0) : 0;

                const donutTopName = document.getElementById('donut-top-name');
                const donutTopPct = document.getElementById('donut-top-pct');
                const badgeTop = document.getElementById('badge-top-faktor');
                const labelTahun = document.getElementById('label-tahun-faktor-chart');

                if (donutTopName) donutTopName.textContent = topName;
                if (donutTopPct) donutTopPct.textContent = topPct + '% Kasus';
                if (badgeTop) badgeTop.textContent = topName;
                if (labelTahun) labelTahun.textContent = appState.tahun;

                // Update List breakdown
                updateFaktorBreakdownList(fData.items || []);
            }
        }

        function updateRankingsList(rankingNikah, rankingCerai, tahun) {
            const badgeNikah = document.getElementById('badge-tahun-rank-nikah');
            const badgeCerai = document.getElementById('badge-tahun-rank-cerai');
            if (badgeNikah) badgeNikah.textContent = 'Tahun ' + tahun;
            if (badgeCerai) badgeCerai.textContent = 'Tahun ' + tahun;

            // Render list pernikahan
            const listNikah = document.getElementById('list-ranking-nikah');
            if (listNikah && rankingNikah && rankingNikah.length > 0) {
                const maxN = rankingNikah[0].total || 1;
                listNikah.innerHTML = rankingNikah.map((rn, idx) => {
                    const pct = Math.round((rn.total / maxN) * 100);
                    const rankClass = idx < 3 ? 'text-blue-600' : 'text-slate-400';
                    return `
                        <div class="flex items-center gap-3">
                            <span class="w-6 text-xs font-extrabold ${rankClass} text-center">#${idx + 1}</span>
                            <div class="flex-grow">
                                <div class="flex justify-between text-xs font-semibold text-slate-800 mb-1">
                                    <span>${rn.provinsi}</span>
                                    <span class="font-bold text-slate-900">${formatNumberId(rn.total)}</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: ${pct}%"></div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // Render list perceraian
            const listCerai = document.getElementById('list-ranking-cerai');
            if (listCerai && rankingCerai && rankingCerai.length > 0) {
                const maxC = rankingCerai[0].total || 1;
                listCerai.innerHTML = rankingCerai.map((rc, idx) => {
                    const pct = Math.round((rc.total / maxC) * 100);
                    const rankClass = idx < 3 ? 'text-rose-600' : 'text-slate-400';
                    return `
                        <div class="flex items-center gap-3">
                            <span class="w-6 text-xs font-extrabold ${rankClass} text-center">#${idx + 1}</span>
                            <div class="flex-grow">
                                <div class="flex justify-between text-xs font-semibold text-slate-800 mb-1">
                                    <span>${rc.provinsi}</span>
                                    <span class="font-bold text-slate-900">${formatNumberId(rc.total)}</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-rose-600 h-2 rounded-full" style="width: ${pct}%"></div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }

        // Render Dinamis List Breakdown Faktor Perceraian saat Filter Berubah
        function updateFaktorBreakdownList(items) {
            const container = document.getElementById('faktor-breakdown-list');
            if (!container) return;

            const colors = [
                '#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6',
                '#F97316', '#14B8A6', '#6366F1', '#EC4899', '#06B6D4',
                '#7C3AED', '#D946EF', '#64748B', '#71717A'
            ];

            if (!items || items.length === 0) {
                container.innerHTML = '<div class="text-center py-6 text-xs text-slate-400">Data faktor perceraian tidak tersedia untuk kombinasi filter ini.</div>';
                return;
            }

            container.innerHTML = items.map((fItem, idx) => {
                const color = colors[idx % colors.length];
                const pct = fItem.persen || fItem.persentase || 0;
                const totalFormatted = fItem.total_format || formatNumberId(fItem.total);
                const isDominant = idx === 0;

                return `
                    <div class="flex flex-col gap-1 text-xs bg-white p-2.5 rounded-xl border border-slate-200/70 hover:border-amber-400/60 hover:shadow-2xs transition group">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <span class="w-5 h-5 rounded-md font-mono font-bold text-[10px] flex items-center justify-center shrink-0" style="background-color: ${color}20; color: ${color};">
                                    ${idx + 1}
                                </span>
                                <span class="font-semibold text-slate-800 truncate group-hover:text-amber-700 transition" title="${fItem.faktor}">
                                    ${fItem.faktor}
                                </span>
                                ${isDominant ? '<span class="hidden sm:inline-flex px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 shrink-0">Dominan</span>' : ''}
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="font-mono font-bold text-slate-900 text-[11px]">
                                    ${totalFormatted} <span class="text-[10px] font-normal text-slate-400">Perkara</span>
                                </span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold" style="background-color: ${color}20; color: ${color};">
                                    ${pct}%
                                </span>
                            </div>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" style="width: ${Math.max(pct, 2)}%; background-color: ${color};"></div>
                        </div>
                    </div>
                `;
            }).join('');
        }
    </script>
</body>
</html>
