@props([
    'selectedProvinsi' => '',
    'selectedTahun' => '',
])

@php
    $defaultTahun = $selectedTahun ?: '2025';
@endphp

<div id="persebaran-peta-section" 
     data-selected-provinsi="{{ $selectedProvinsi }}" 
     data-selected-tahun="{{ $defaultTahun }}" 
     class="bg-surface-container-lowest rounded-2xl p-4 sm:p-6 shadow-sm border border-surface-container-high/60 flex flex-col gap-6">
    
    <!-- Header Section (Judul, Subjudul & Filter Interaktif Peta) -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-surface-container-high/60">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[28px] text-primary">map</span>
                <h2 class="font-headline text-xl sm:text-2xl font-bold text-on-surface tracking-tight">
                    Persebaran Pernikahan di Indonesia
                </h2>
            </div>
            <p class="text-xs sm:text-sm text-on-surface-variant">
                Peta choropleth interaktif geografis Indonesia (Aceh hingga Papua) berdasarkan data registrasi pernikahan BPS & Kementerian Agama.
            </p>
        </div>

        <!-- Filter Controls (Tahun, Bulan, & Tombol Reset Tampilan) -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Filter Tahun -->
            <div class="flex items-center gap-1.5 bg-surface-container-low px-3 py-1.5 rounded-xl border border-outline-variant/40 shadow-xs">
                <label for="map-filter-tahun" class="text-xs font-semibold text-on-surface-variant shrink-0 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                    <span>Tahun:</span>
                </label>
                <select id="map-filter-tahun" class="bg-transparent border-0 text-on-surface text-xs font-bold focus:outline-none cursor-pointer pr-2">
                    <option value="2025" {{ $defaultTahun === '2025' ? 'selected' : '' }}>2025</option>
                    <option value="2024" {{ $defaultTahun === '2024' ? 'selected' : '' }}>2024</option>
                    <option value="2023" {{ $defaultTahun === '2023' ? 'selected' : '' }}>2023</option>
                </select>
            </div>

            <!-- Filter Bulan -->
            <div class="flex items-center gap-1.5 bg-surface-container-low px-3 py-1.5 rounded-xl border border-outline-variant/40 shadow-xs">
                <label for="map-filter-bulan" class="text-xs font-semibold text-on-surface-variant shrink-0 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">date_range</span>
                    <span>Bulan:</span>
                </label>
                <select id="map-filter-bulan" class="bg-transparent border-0 text-on-surface text-xs font-bold focus:outline-none cursor-pointer pr-2">
                    <option value="Semua Bulan" selected>Semua Bulan</option>
                    <option value="Januari">Januari</option>
                    <option value="Februari">Februari</option>
                    <option value="Maret">Maret</option>
                    <option value="April">April</option>
                    <option value="Mei">Mei</option>
                    <option value="Juni">Juni</option>
                    <option value="Juli">Juli</option>
                    <option value="Agustus">Agustus</option>
                    <option value="September">September</option>
                    <option value="Oktober">Oktober</option>
                    <option value="November">November</option>
                    <option value="Desember">Desember</option>
                </select>
            </div>

            <!-- Tombol Reset Tampilan Peta -->
            <button type="button" id="map-btn-reset-zoom" title="Kembalikan zoom ke seluruh Indonesia" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold text-xs border border-outline-variant/40 transition shadow-xs">
                <span class="material-symbols-outlined text-[16px] text-primary">zoom_out_map</span>
                <span class="hidden sm:inline">Seluruh Indonesia</span>
            </button>
        </div>
    </div>

    <!-- Informasi Periode & Status Seleksi Aktif -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface-container-low/70 p-3 rounded-xl border border-outline-variant/30">
        <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-on-surface">
            <span class="inline-flex items-center gap-1 text-primary font-bold">
                <span class="material-symbols-outlined text-[17px]">insights</span>
                <span id="map-periode-info">{{ $defaultTahun }} • Semua Bulan</span>
            </span>

            @if($selectedProvinsi)
                <span class="text-outline">•</span>
                <span id="map-active-prov-badge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-semibold text-[11px]">
                    <span class="material-symbols-outlined text-[13px] text-amber-700">place</span>
                    <span>Menyorot: {{ $selectedProvinsi }}</span>
                    <button type="button" id="map-btn-clear-prov-select" class="ml-1 hover:text-red-700" title="Batal sorot provinsi">&times;</button>
                </span>
            @endif
        </div>

        <div id="map-warning-banner" class="hidden text-xs px-3 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-medium flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">warning</span>
            <span id="map-warning-text">Data 2024 tersedia sampai September.</span>
        </div>
    </div>

    <!-- 3 Card Ringkasan Statistik Data Peta -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Total Nasional -->
        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/40 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-200">
                <span class="material-symbols-outlined text-[22px]">analytics</span>
            </div>
            <div>
                <span class="text-[11px] font-semibold uppercase text-outline block tracking-wider">Total Nasional</span>
                <span id="map-stat-total" class="text-lg font-bold font-mono text-primary">Memuat...</span>
            </div>
        </div>

        <!-- Card 2: Provinsi Tertinggi -->
        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/40 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200">
                <span class="material-symbols-outlined text-[22px]">workspace_premium</span>
            </div>
            <div class="overflow-hidden">
                <span class="text-[11px] font-semibold uppercase text-outline block tracking-wider">Pernikahan Tertinggi</span>
                <span id="map-stat-tertinggi" class="text-sm font-bold text-on-surface truncate block">--</span>
                <span id="map-stat-tertinggi-val" class="text-xs font-mono text-primary font-semibold block">--</span>
            </div>
        </div>

        <!-- Card 3: Provinsi Terendah -->
        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/40 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 border border-slate-300">
                <span class="material-symbols-outlined text-[22px]">trending_down</span>
            </div>
            <div class="overflow-hidden">
                <span class="text-[11px] font-semibold uppercase text-outline block tracking-wider">Pernikahan Terendah</span>
                <span id="map-stat-terendah" class="text-sm font-bold text-on-surface truncate block">--</span>
                <span id="map-stat-terendah-val" class="text-xs font-mono text-slate-600 font-semibold block">--</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid: Leaflet Map (Left) & Ranking Panel (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Leaflet Map Container -->
        <div class="lg:col-span-8 relative rounded-2xl overflow-hidden border border-outline-variant/40 shadow-xs bg-[#F4F8FC] min-h-[520px] lg:min-h-[580px] flex flex-col">
            <!-- Canvas Leaflet Map -->
            <div id="indonesia-leaflet-map" class="w-full flex-1 min-h-[520px] lg:min-h-[580px] z-10"></div>

            <!-- Floating Map Navigation Control / Instructions (Top Right) -->
            <div class="absolute top-3.5 right-3.5 z-[400] flex flex-col gap-2 items-end">
                <div class="bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-lg border border-outline-variant/30 shadow-md text-[11px] text-slate-700 flex items-center gap-1.5 font-medium">
                    <span class="material-symbols-outlined text-[15px] text-primary">touch_app</span>
                    <span>Klik provinsi untuk rincian data</span>
                </div>
            </div>

            <!-- Legenda Gradasi Warna (Bottom Right) -->
            <div class="absolute bottom-4 right-4 z-[400] bg-white/95 backdrop-blur-md p-3.5 rounded-xl border border-outline-variant/30 shadow-lg text-xs font-sans max-w-[300px] w-full">
                <div class="flex items-center justify-between font-bold text-on-surface mb-2">
                    <span class="text-xs font-bold text-slate-800">Jumlah Pernikahan</span>
                    <span class="material-symbols-outlined text-sm text-outline">legend_toggle</span>
                </div>

                <!-- 5-Step Gradient Bar -->
                <div class="grid grid-cols-5 h-3 rounded-md overflow-hidden border border-slate-300 shadow-inner mb-1.5">
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

        <!-- Ranking Panel (Top 10 Provinsi) -->
        <div class="lg:col-span-4 bg-surface-container-low/70 rounded-2xl p-4 sm:p-5 border border-outline-variant/40 flex flex-col justify-between gap-4">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-outline-variant/30 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">format_list_numbered</span>
                        <h3 class="font-headline text-sm font-bold text-on-surface uppercase tracking-wider">
                            Top 10 Provinsi
                        </h3>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-primary-container text-on-primary">
                        Volume Tertinggi
                    </span>
                </div>

                <p class="text-[11px] text-on-surface-variant mb-3">
                    Provinsi dengan jumlah pernikahan tertinggi pada periode terfilter. Klik baris provinsi untuk menyorot di peta.
                </p>

                <!-- List Top 10 -->
                <div id="top-10-ranking-list" class="flex flex-col gap-2 max-h-[420px] overflow-y-auto pr-1">
                    <div class="text-center py-8 text-xs text-outline">Memuat data ranking...</div>
                </div>
            </div>

            <div class="pt-3 border-t border-outline-variant/30 text-center">
                <span class="text-[11px] text-outline flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">sync</span>
                    <span>Terhubung langsung dengan database BPS & Kementerian Agama</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Modal Panel Detail Provinsi (Saat Provinsi Diklik) -->
    <div id="modal-provinsi-faktor" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full border border-surface-container-high shadow-2xl p-6 relative max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center shrink-0 border border-blue-200">
                        <span class="material-symbols-outlined text-[24px]">location_on</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="modal-prov-nama" class="font-headline font-bold text-base text-on-surface">--</h3>
                            <span id="modal-prov-kode" class="px-2 py-0.5 rounded font-mono text-[10px] font-bold bg-surface-container text-on-surface-variant">Kode: --</span>
                        </div>
                        <p class="text-xs text-on-surface-variant">
                            Statistik Komparatif Pernikahan & Perceraian (<span id="modal-prov-tahun">2025</span>) • BPS RI
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeProvinsiFaktorModal()" class="text-outline hover:text-on-surface p-1 rounded-lg hover:bg-surface-container">
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
                        <span class="text-[10px] text-blue-600 block mt-0.5">Kementerian Agama</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-rose-50/80 border border-rose-200 text-xs shadow-xs">
                        <span class="text-[10px] text-rose-700 font-semibold uppercase tracking-wider block">Total Perceraian</span>
                        <span id="modal-stat-cerai" class="font-mono font-bold text-base text-rose-900 block mt-0.5">-- Perkara</span>
                        <span class="text-[10px] text-rose-600 block mt-0.5">Sensus BPS RI</span>
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
                    <div id="modal-top-nama" class="font-headline font-bold text-base text-on-surface mt-1">
                        --
                    </div>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-amber-200/60 text-xs">
                        <span class="text-on-surface-variant">Jumlah Perkara:</span>
                        <span id="modal-top-kasus" class="font-mono font-bold text-amber-900">-- Kasus</span>
                    </div>
                </div>

                <!-- Breakdown Seluruh Faktor Penyebab Perceraian -->
                <div>
                    <h4 class="font-headline font-bold text-xs uppercase tracking-wider text-on-surface mb-2.5 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-primary">analytics</span>
                        <span>Rincian Faktor Penyebab Perceraian (BPS)</span>
                    </h4>
                    <div id="modal-factors-list" class="space-y-2">
                        <!-- Loaded dynamically via JS -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-surface-container-high flex flex-wrap items-center justify-between gap-2 shrink-0">
                <a id="modal-btn-filter" href="#" class="px-3.5 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-semibold text-xs flex items-center gap-1.5 border border-outline-variant/40 transition">
                    <span class="material-symbols-outlined text-[16px] text-primary">filter_alt</span>
                    <span>Filter Dashboard ke Wilayah Ini</span>
                </a>
                <div class="flex items-center gap-2">
                    <a id="modal-btn-detail" href="#" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-on-primary font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                        <span>Halaman Detail</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                    <button type="button" onclick="closeProvinsiFaktorModal()" class="px-3.5 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-semibold text-xs transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Custom Tooltip Leaflet */
    .leaflet-tooltip-indonesia {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .leaflet-tooltip-indonesia::before {
        display: none !important;
    }

    /* Custom Leaflet Popup Styling */
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
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rootSection = document.getElementById('persebaran-peta-section');
        const initialSelectedProvinsi = rootSection ? rootSection.getAttribute('data-selected-provinsi') : '';
        const initialSelectedTahun = rootSection ? rootSection.getAttribute('data-selected-tahun') : '2025';

        // Filter Elements
        const tahunSelect = document.getElementById('map-filter-tahun');
        const bulanSelect = document.getElementById('map-filter-bulan');
        const btnResetZoom = document.getElementById('map-btn-reset-zoom');
        const btnClearProvSelect = document.getElementById('map-btn-clear-prov-select');

        // Display Info Elements
        const periodeInfo = document.getElementById('map-periode-info');
        const warningBanner = document.getElementById('map-warning-banner');
        const warningText = document.getElementById('map-warning-text');

        // Ringkasan Cards Elements
        const statTotal = document.getElementById('map-stat-total');
        const statTertinggi = document.getElementById('map-stat-tertinggi');
        const statTertinggiVal = document.getElementById('map-stat-tertinggi-val');
        const statTerendah = document.getElementById('map-stat-terendah');
        const statTerendahVal = document.getElementById('map-stat-terendah-val');

        // Legend Elements
        const legendMin = document.getElementById('legend-min');
        const legendMid = document.getElementById('legend-mid');
        const legendMax = document.getElementById('legend-max');
        const top10Container = document.getElementById('top-10-ranking-list');

        let leafletMap = null;
        let geojsonLayer = null;
        let geojsonData = null;
        let currentApiData = null;
        let activeSelectedLayer = null;
        let dkiJakartaLayer = null;

        // Palet Gradasi 5 Tingkat Biru Sesuai Standar Choropleth (Biru Sangat Muda -> Biru Paling Gelap)
        const COLOR_SCALE = [
            '#DBEAFE', // 1. Biru Sangat Muda (Jumlah Pernikahan Sangat Rendah)
            '#93C5FD', // 2. Biru Muda (Rendah)
            '#3B82F6', // 3. Biru Sedang
            '#1D4ED8', // 4. Biru Tua (Tinggi)
            '#0B2559'  // 5. Biru Paling Gelap (Tertinggi)
        ];
        const COLOR_NO_DATA = '#CBD5E1'; // Netral soft-slate untuk wilayah tanpa data

        // Inisialisasi Peta Leaflet
        function initMap() {
            // Titik tengah geografis kepulauan Indonesia
            const centerLat = -1.5;
            const centerLng = 118.0;

            leafletMap = L.map('indonesia-leaflet-map', {
                center: [centerLat, centerLng],
                zoom: 5,
                minZoom: 4,
                maxZoom: 18,
                zoomControl: false,
                attributionControl: false
            });

            // Zoom Control rapi di kiri atas
            L.control.zoom({ position: 'topleft' }).addTo(leafletMap);

            // Tambahkan Custom Reset View Control di bawah Zoom Control
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
            leafletMap.addControl(new ResetControl());

            // Peta Dasar OpenStreetMap (100% Gratis, Bebas & TIDAK Memerlukan API Key Apapun)
            const basemap = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                opacity: 0.65
            });
            basemap.addTo(leafletMap);

            // Fetch GeoJSON batas administratif Indonesia & data statistik dari MySQL
            const cacheBuster = '_v=' + Date.now();
            const tahunAwal = tahunSelect.value || initialSelectedTahun || '2025';
            const bulanAwal = bulanSelect.value || 'Semua Bulan';

            Promise.all([
                fetch('/geojson/indonesia-provinces.json?' + cacheBuster).then(res => res.json()),
                fetchMapData(tahunAwal, bulanAwal)
            ]).then(([geoJson, apiData]) => {
                geojsonData = geoJson;
                currentApiData = apiData;

                renderGeoJsonLayer();
                updateUI(apiData);

                // Sinkronkan provinsi terpilih jika ada filter dari dashboard
                if (initialSelectedProvinsi) {
                    highlightAndZoomToProvince(initialSelectedProvinsi);
                } else if (geojsonLayer) {
                    leafletMap.fitBounds(geojsonLayer.getBounds(), { padding: [20, 20] });
                }
            }).catch(err => {
                console.error('Gagal memuat data peta Indonesia:', err);
            });
        }

        // Fetch Data Peta & Faktor Perceraian dari Endpoint Laravel
        function fetchMapData(tahun, bulan) {
            const cacheBuster = '_v=' + Date.now();
            const url = `/api/map-data?tahun=${encodeURIComponent(tahun)}&bulan=${encodeURIComponent(bulan)}&${cacheBuster}`;
            return fetch(url).then(res => res.json());
        }

        // Hitung Warna Choropleth Berdasarkan Rentang Nilai
        function getChoroplethColor(value, minVal, maxVal, hasData) {
            if (!hasData || value === null || value === undefined) {
                return COLOR_NO_DATA;
            }
            if (maxVal <= minVal || maxVal === 0) {
                return COLOR_SCALE[2];
            }

            const ratio = (value - minVal) / (maxVal - minVal);
            if (ratio <= 0.20) return COLOR_SCALE[0]; // Sangat Rendah
            if (ratio <= 0.40) return COLOR_SCALE[1]; // Rendah
            if (ratio <= 0.60) return COLOR_SCALE[2]; // Sedang
            if (ratio <= 0.80) return COLOR_SCALE[3]; // Tinggi
            return COLOR_SCALE[4];                    // Tertinggi
        }

        // Render Layer GeoJSON Batas Administratif Provinsi
        function renderGeoJsonLayer() {
            if (!geojsonData) return;

            if (geojsonLayer) {
                leafletMap.removeLayer(geojsonLayer);
            }

            dkiJakartaLayer = null;

            geojsonLayer = L.geoJSON(geojsonData, {
                style: function (feature) {
                    const kode = feature.properties.kode_wilayah || feature.properties.kode;
                    const name = feature.properties.name || feature.properties.NAME_1;

                    const provItem = currentApiData && currentApiData.data ? 
                        (currentApiData.data[kode] || currentApiData.data[name]) : null;

                    const isAvailable = currentApiData ? currentApiData.is_data_available : false;
                    const hasData = isAvailable && provItem && provItem.has_data;
                    const val = hasData ? provItem.jumlah : null;

                    const color = getChoroplethColor(
                        val, 
                        currentApiData ? currentApiData.min_val : 0, 
                        currentApiData ? currentApiData.max_val : 0, 
                        hasData
                    );

                    const isSelected = initialSelectedProvinsi && 
                        (name === initialSelectedProvinsi || kode === initialSelectedProvinsi);

                    return {
                        fillColor: color,
                        weight: isSelected ? 3.0 : 1.2,
                        opacity: 1,
                        color: isSelected ? '#F59E0B' : '#FFFFFF', // Batas tipis, putih, kontras tinggi
                        dashArray: '',
                        fillOpacity: isSelected ? 0.95 : 0.85
                    };
                },
                onEachFeature: function (feature, layer) {
                    const kode = feature.properties.kode_wilayah || feature.properties.kode;
                    const name = feature.properties.name || feature.properties.NAME_1;
                    const provItem = currentApiData && currentApiData.data ? 
                        (currentApiData.data[kode] || currentApiData.data[name]) : null;
                    const isAvailable = currentApiData ? currentApiData.is_data_available : false;
                    const tahunAktif = tahunSelect.value || initialSelectedTahun || '2025';

                    // Catat referensi DKI Jakarta agar posisinya selalu di atas polygon Jawa Barat
                    if (name === 'DKI Jakarta' || kode === '31') {
                        dkiJakartaLayer = layer;
                    }

                    // Interaksi Hover & Klik
                    layer.on({
                        mouseover: function (e) {
                            const l = e.target;
                            l.setStyle({
                                weight: 2.5,
                                color: '#0A2540',
                                fillOpacity: 0.95
                            });
                            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                                l.bringToFront();
                            }
                        },
                        mouseout: function (e) {
                            if (activeSelectedLayer === e.target) {
                                // Pertahankan style seleksi jika provinsi ini sedang dipilih
                                e.target.setStyle({
                                    weight: 3.5,
                                    color: '#F59E0B',
                                    fillOpacity: 0.95
                                });
                            } else {
                                geojsonLayer.resetStyle(e.target);
                            }
                            // Kembalikan DKI Jakarta ke front agar tidak tertutup
                            ensureDkiOnTop();
                        },
                        click: function (e) {
                            selectProvince(layer, name, kode, provItem);
                        }
                    });

                    // Pembuatan Tooltip Interaktif Hover
                    let tooltipContent = '';
                    if (isAvailable && provItem) {
                        tooltipContent = `
                            <div class="p-3 bg-slate-900/95 backdrop-blur-md text-white rounded-xl shadow-2xl border border-slate-700 text-xs min-w-[210px] max-w-[270px] font-sans">
                                <div class="font-bold text-sm text-white pb-1.5 border-b border-slate-700/80 flex items-center justify-between">
                                    <span class="truncate">${provItem.provinsi}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-500/30 text-blue-300 font-mono font-normal">Kode: ${provItem.kode_wilayah}</span>
                                </div>
                                <div class="space-y-1.5 mt-2">
                                    <div class="flex justify-between items-baseline">
                                        <span class="text-slate-400 text-[11px]">Tahun:</span>
                                        <span class="font-bold text-white font-mono">${tahunAktif}</span>
                                    </div>
                                    <div class="flex justify-between items-baseline">
                                        <span class="text-slate-400 text-[11px]">Jumlah Pernikahan:</span>
                                        <span class="font-bold font-mono text-blue-400 text-sm">${provItem.jumlah_formatted} <span class="text-[10px] font-normal text-slate-400">Akta</span></span>
                                    </div>
                                    <div class="flex justify-between items-baseline pt-1 border-t border-slate-800 text-[11px]">
                                        <span class="text-slate-400">Porsi Nasional:</span>
                                        <span class="font-bold font-mono text-amber-400">${provItem.persentase}%</span>
                                    </div>
                                    <div class="text-[10px] text-blue-300 font-medium italic pt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">touch_app</span>
                                        <span>Klik untuk rincian perceraian</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        tooltipContent = `
                            <div class="p-3 bg-slate-900/95 backdrop-blur-md text-white rounded-xl shadow-2xl border border-slate-700 text-xs min-w-[200px] font-sans">
                                <div class="font-bold text-sm text-white mb-1.5 pb-1 border-b border-slate-700">
                                    ${name}
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
            }).addTo(leafletMap);

            // Selalu bawa DKI Jakarta ke depan setelah rendering
            ensureDkiOnTop();
        }

        // Fungsi Memastikan DKI Jakarta Selalu Tampil Teratas
        function ensureDkiOnTop() {
            if (dkiJakartaLayer && typeof dkiJakartaLayer.bringToFront === 'function') {
                dkiJakartaLayer.bringToFront();
            }
        }

        // Aksi Saat Provinsi Dipilih (Klik)
        function selectProvince(layer, name, kode, provItem) {
            // Reset style layer yang sebelumnya aktif
            if (activeSelectedLayer && activeSelectedLayer !== layer) {
                geojsonLayer.resetStyle(activeSelectedLayer);
            }

            activeSelectedLayer = layer;
            layer.setStyle({
                weight: 3.5,
                color: '#F59E0B',
                fillOpacity: 0.95
            });
            if (layer.bringToFront) layer.bringToFront();

            // Zoom smooth ke batas wilayah provinsi
            if (layer.getBounds) {
                leafletMap.fitBounds(layer.getBounds(), { padding: [40, 40], maxZoom: 8 });
            }

            // Tampilkan Popup Leaflet Cepat dengan ringkasan lengkap
            const tahunAktif = tahunSelect.value || initialSelectedTahun || '2025';
            const jumlahNikah = provItem ? `${provItem.jumlah_formatted} Akta` : 'Data tidak tersedia';
            const fp = provItem ? provItem.faktor_perceraian : null;
            const jumlahCerai = (fp && fp.total_kasus_formatted) ? `${fp.total_kasus_formatted} Perkara` : 'Data tidak tersedia';
            const topFaktorNama = (fp && fp.top_faktor) ? fp.top_faktor.nama : 'Data tidak tersedia';
            const topFaktorKasus = (fp && fp.top_faktor) ? `${fp.top_faktor.kasus_formatted || fp.top_faktor.kasus} Kasus (${fp.top_faktor.persentase}%)` : '';

            const popupHtml = `
                <div class="p-3.5 bg-white text-slate-900 rounded-xl font-sans min-w-[240px] text-xs">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-200">
                        <div>
                            <span class="font-bold text-sm text-slate-900 block">${name}</span>
                            <span class="text-[10px] text-slate-500 font-mono">Kode: ${kode || '-'} • Tahun ${tahunAktif}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600 font-medium">Jumlah Pernikahan:</span>
                            <span class="font-bold font-mono text-blue-700">${jumlahNikah}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600 font-medium">Jumlah Perceraian:</span>
                            <span class="font-bold font-mono text-rose-700">${jumlahCerai}</span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-100">
                            <span class="text-[10px] font-semibold text-amber-700 uppercase block">Faktor Perceraian Tertinggi:</span>
                            <span class="font-bold text-slate-800 text-[11px] block truncate">${topFaktorNama}</span>
                            ${topFaktorKasus ? `<span class="text-[10px] text-slate-500 font-mono block">${topFaktorKasus}</span>` : ''}
                        </div>
                    </div>
                    <div class="pt-2.5 mt-2.5 border-t border-slate-200 flex items-center justify-between gap-2">
                        <button type="button" onclick="showProvinsiFaktorModal('${name}', '${kode}', currentApiData && currentApiData.data ? currentApiData.data['${kode}'] || currentApiData.data['${name}'] : null)" class="px-2.5 py-1 rounded-lg bg-blue-600 text-white font-semibold text-[11px] hover:bg-blue-700 transition">
                            Rincian Lengkap
                        </button>
                        <a href="/?provinsi=${encodeURIComponent(name)}&tahun=${encodeURIComponent(tahunAktif)}" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 hover:bg-slate-200 font-semibold text-[11px] border border-slate-300 transition">
                            Filter Dashboard
                        </a>
                    </div>
                </div>
            `;

            layer.bindPopup(popupHtml, { offset: [0, -10] }).openPopup();
        }

        // Sorot dan Zoom ke Provinsi Berdasarkan Nama/Kode
        function highlightAndZoomToProvince(provTarget) {
            if (!geojsonLayer) return;

            let targetLayer = null;
            geojsonLayer.eachLayer(l => {
                const props = l.feature && l.feature.properties ? l.feature.properties : {};
                const name = props.name || props.NAME_1;
                const kode = props.kode_wilayah || props.kode;
                if (name === provTarget || kode === provTarget) {
                    targetLayer = l;
                }
            });

            if (targetLayer) {
                const props = targetLayer.feature.properties;
                const name = props.name || props.NAME_1;
                const kode = props.kode_wilayah || props.kode;
                const provItem = currentApiData && currentApiData.data ? 
                    (currentApiData.data[kode] || currentApiData.data[name]) : null;
                selectProvince(targetLayer, name, kode, provItem);
            }
        }

        // Reset Tampilan Peta Kembali ke Seluruh Indonesia
        function resetMapView() {
            if (activeSelectedLayer && geojsonLayer) {
                geojsonLayer.resetStyle(activeSelectedLayer);
                activeSelectedLayer = null;
            }
            if (geojsonLayer && leafletMap) {
                leafletMap.closePopup();
                leafletMap.fitBounds(geojsonLayer.getBounds(), { padding: [20, 20] });
            }
            ensureDkiOnTop();
        }

        // Perbarui Tampilan UI (Statistik Ringkasan, Legenda Skala, Top 10)
        function updateUI(apiData) {
            // Informasi Label Periode
            periodeInfo.textContent = `${apiData.tahun} • ${apiData.bulan}`;

            // Banner Peringatan 2024 / Data Belum Tersedia
            if (apiData.note) {
                warningBanner.classList.remove('hidden');
                warningText.textContent = apiData.note;
            } else if (!apiData.is_data_available) {
                warningBanner.classList.remove('hidden');
                warningText.textContent = 'Data tidak tersedia untuk periode yang dipilih.';
            } else {
                warningBanner.classList.add('hidden');
            }

            // Stat Cards Ringkasan
            if (apiData.is_data_available) {
                statTotal.textContent = `${apiData.total_nasional_formatted} Akta`;
                statTertinggi.textContent = apiData.provinsi_tertinggi.name;
                statTertinggiVal.textContent = apiData.provinsi_tertinggi.jumlah_formatted ? `${apiData.provinsi_tertinggi.jumlah_formatted} Akta` : '-';
                statTerendah.textContent = apiData.provinsi_terendah.name;
                statTerendahVal.textContent = apiData.provinsi_terendah.jumlah_formatted ? `${apiData.provinsi_terendah.jumlah_formatted} Akta` : '-';
            } else {
                statTotal.textContent = 'Data tidak tersedia';
                statTertinggi.textContent = '-';
                statTertinggiVal.textContent = '-';
                statTerendah.textContent = '-';
                statTerendahVal.textContent = '-';
            }

            // Label Angka Skala Legenda
            if (apiData.is_data_available && apiData.max_val > 0) {
                const minFmt = apiData.min_val.toLocaleString('id-ID');
                const maxFmt = apiData.max_val.toLocaleString('id-ID');
                const midFmt = Math.round((apiData.min_val + apiData.max_val) / 2).toLocaleString('id-ID');

                legendMin.textContent = minFmt;
                legendMid.textContent = midFmt;
                legendMax.textContent = maxFmt;
            } else {
                legendMin.textContent = '0';
                legendMid.textContent = '-';
                legendMax.textContent = '0';
            }

            // Render Panel Top 10 Provinsi
            if (apiData.is_data_available && apiData.top_10 && apiData.top_10.length > 0) {
                top10Container.innerHTML = apiData.top_10.map(item => `
                    <div onclick="window.highlightProvinceFromList('${item.provinsi}')" class="group cursor-pointer flex items-center justify-between p-2.5 rounded-xl bg-surface-container-lowest border border-outline-variant/30 hover:border-primary hover:shadow-xs transition-all text-xs">
                        <div class="flex items-center gap-2.5 overflow-hidden">
                            <span class="w-6 h-6 rounded-lg bg-surface-container font-mono font-bold text-primary flex items-center justify-center text-[11px] shrink-0 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                                ${item.rank}
                            </span>
                            <span class="font-medium text-on-surface truncate group-hover:text-primary transition-colors">
                                ${item.provinsi}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 ml-2">
                            <span class="font-mono font-bold text-blue-700 text-[11px]">
                                ${item.jumlah_formatted}
                            </span>
                            <span class="material-symbols-outlined text-[14px] text-outline group-hover:text-primary transition-colors">place</span>
                        </div>
                    </div>
                `).join('');
            } else {
                top10Container.innerHTML = `
                    <div class="text-center py-6 px-3 bg-surface-container-lowest rounded-xl border border-outline-variant/30 text-xs text-amber-700 flex flex-col items-center gap-1">
                        <span class="material-symbols-outlined text-amber-500 text-lg">info</span>
                        <span>Data ranking tidak tersedia untuk periode ini.</span>
                    </div>
                `;
            }
        }

        // Expose Helper untuk sorot dari panel ranking
        window.highlightProvinceFromList = function (provName) {
            highlightAndZoomToProvince(provName);
        };

        // Event Listener Perubahan Filter Tahun & Bulan
        function handleFilterChange() {
            const tahun = tahunSelect.value;
            const bulan = bulanSelect.value;

            fetchMapData(tahun, bulan).then(data => {
                currentApiData = data;
                renderGeoJsonLayer();
                updateUI(data);
            }).catch(err => {
                console.error('Error memperbarui filter peta:', err);
            });
        }

        tahunSelect.addEventListener('change', handleFilterChange);
        bulanSelect.addEventListener('change', handleFilterChange);

        if (btnResetZoom) {
            btnResetZoom.addEventListener('click', resetMapView);
        }

        if (btnClearProvSelect) {
            btnClearProvSelect.addEventListener('click', function () {
                const badge = document.getElementById('map-active-prov-badge');
                if (badge) badge.remove();
                resetMapView();
            });
        }

        // Jalankan Inisialisasi Peta
        initMap();
    });

    // Global Modal Functions untuk Rincian Faktor Perceraian
    const factorColors = [
        '#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6',
        '#F97316', '#EC4899', '#6366F1', '#14B8A6', '#64748B'
    ];

    function showProvinsiFaktorModal(name, kode, provItem) {
        const modal = document.getElementById('modal-provinsi-faktor');
        if (!modal) return;

        const tahunSelect = document.getElementById('map-filter-tahun');
        const tahun = tahunSelect ? tahunSelect.value : '2025';
        const provName = provItem ? provItem.provinsi : name;
        const provKode = kode || (provItem ? provItem.kode_wilayah : '-');
        const slug = provName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

        document.getElementById('modal-prov-nama').textContent = provName;
        document.getElementById('modal-prov-kode').textContent = 'Kode: ' + provKode;
        document.getElementById('modal-prov-tahun').textContent = tahun;
        document.getElementById('modal-stat-nikah').textContent = provItem && provItem.jumlah_formatted ? `${provItem.jumlah_formatted} Akta` : 'Data tidak tersedia';

        document.getElementById('modal-btn-filter').href = `/?provinsi=${encodeURIComponent(provName)}&tahun=${encodeURIComponent(tahun)}`;
        document.getElementById('modal-btn-detail').href = provItem && provItem.url ? provItem.url : `/statistik/provinsi/${slug}`;

        function renderFactors(totalKasusFormatted, topFaktor, breakdown) {
            document.getElementById('modal-stat-cerai').textContent = totalKasusFormatted ? `${totalKasusFormatted} Perkara` : 'Data tidak tersedia';

            if (topFaktor) {
                document.getElementById('modal-top-nama').textContent = topFaktor.nama;
                document.getElementById('modal-top-kasus').textContent = `${topFaktor.kasus_formatted || topFaktor.total_formatted || topFaktor.kasus} Kasus`;
                document.getElementById('modal-top-pct').textContent = `${topFaktor.persentase}%`;
            } else {
                document.getElementById('modal-top-nama').textContent = 'Data tidak tersedia';
                document.getElementById('modal-top-kasus').textContent = '-';
                document.getElementById('modal-top-pct').textContent = '-';
            }

            const listContainer = document.getElementById('modal-factors-list');
            if (!breakdown || breakdown.length === 0) {
                listContainer.innerHTML = '<div class="text-center py-4 text-xs text-outline">Data rincian tidak ditemukan.</div>';
                return;
            }

            listContainer.innerHTML = breakdown.map((item, idx) => {
                const color = factorColors[idx % factorColors.length];
                const kasusNum = item.kasus_formatted || item.total_formatted || item.kasus || item.total;
                return `
                    <div class="p-2.5 rounded-xl bg-surface-container-lowest border border-outline-variant/30 flex flex-col gap-1 text-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <span class="w-5 h-5 rounded-md font-mono font-bold text-[10px] flex items-center justify-center shrink-0" style="background-color: ${color}20; color: ${color};">
                                    ${idx + 1}
                                </span>
                                <span class="font-semibold text-on-surface truncate">${item.faktor}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="font-mono font-bold text-on-surface text-[11px]">${kasusNum} Kasus</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold" style="background-color: ${color}20; color: ${color};">
                                    ${item.persentase}%
                                </span>
                            </div>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full rounded-full transition-all" style="width: ${Math.max(item.persentase, 2)}%; background-color: ${color};"></div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        if (provItem && provItem.faktor_perceraian && provItem.faktor_perceraian.breakdown && provItem.faktor_perceraian.breakdown.length > 0) {
            const fp = provItem.faktor_perceraian;
            renderFactors(fp.total_kasus_formatted, fp.top_faktor, fp.breakdown);
        } else {
            document.getElementById('modal-factors-list').innerHTML = '<div class="text-center py-4 text-xs text-outline">Memuat data faktor perceraian BPS...</div>';
            fetch(`/api/faktor-perceraian?provinsi=${encodeURIComponent(provName)}&tahun=${encodeURIComponent(tahun)}`)
                .then(res => res.json())
                .then(data => {
                    renderFactors(data.total_kasus_formatted, data.top_faktor, data.breakdown);
                })
                .catch(err => {
                    console.error('Gagal mengambil data faktor perceraian:', err);
                    document.getElementById('modal-factors-list').innerHTML = '<div class="text-center py-4 text-xs text-rose-600">Gagal memuat rincian faktor.</div>';
                });
        }

        modal.classList.remove('hidden');
    }

    function closeProvinsiFaktorModal() {
        const modal = document.getElementById('modal-provinsi-faktor');
        if (modal) modal.classList.add('hidden');
    }
</script>
