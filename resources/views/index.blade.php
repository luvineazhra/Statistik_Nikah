<x-layouts.app title="Dashboard Analytics & Persebaran Data Pernikahan Indonesia - NikahInfo">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-fixed text-on-primary-fixed">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Data Konsolidasi Nasional
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">
                    <span class="material-symbols-outlined text-[14px] text-tertiary-container">database</span>
                    Sumber: <code class="font-mono text-[11px]">rekap_nikah_provinsi (Kemenag) & BPS RI</code>
                </span>
            </div>
            <h1 class="font-headline text-2xl md:text-3xl font-bold text-on-surface tracking-tight mt-1">
                Dashboard Analytics & Persebaran Data Pernikahan Indonesia
            </h1>
            <p class="text-xs md:text-sm text-on-surface-variant">
                Periode Analisis Data 2023–2025 • Visualisasi & Analisis Lintas Wilayah Indonesia
            </p>
        </div>

        <div class="flex items-center gap-space-sm self-start lg:self-center shrink-0">
            <button type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-surface-container-lowest text-on-surface shadow-sm hover:bg-surface-container transition-colors text-xs font-medium border border-outline-variant/40">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">picture_as_pdf</span>
                <span>Export PDF/Excel</span>
            </button>
            <button type="button" onclick="openModal('addRecordModal')" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-container text-on-primary shadow-sm hover:bg-primary transition-colors text-xs font-medium">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Input Registrasi Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar Component -->
    <x-filter-bar 
        :daftarProvinsi="$daftarProvinsi" 
        :daftarTahun="$daftarTahun" 
        :selectedProvinsi="$selectedProvinsi" 
        :selectedTahun="$selectedTahun" 
    />

    <!-- 4 KPI Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-kpi-card 
            title="Total Pernikahan"
            subtitle="{{ $selectedProvinsi ? 'Provinsi: ' . $selectedProvinsi : 'Registri Kementerian Agama & BPS' }}"
            value="{{ number_format($totalPernikahan, 0, ',', '.') }}"
            unit="Akta"
            icon="groups"
            iconBg="bg-secondary-fixed text-primary"
            badgeText="{{ $selectedProvinsi ? 'Filter Aktif' : 'Nasional' }}"
            badgeClass="bg-tertiary-fixed text-on-tertiary-fixed-variant"
            badgeIcon="verified"
        />

        <x-kpi-card 
            title="Total Provinsi Tercover"
            subtitle="Cakupan Wilayah Filter"
            value="{{ $totalProvinsi }}"
            unit="Provinsi"
            icon="location_on"
            iconBg="bg-surface-container text-secondary"
            badgeText="{{ $totalProvinsi === 1 ? '1 Wilayah' : 'Data Wilayah BPS' }}"
            badgeClass="bg-secondary-fixed text-on-secondary-fixed"
            badgeIcon="map"
        />

        <x-kpi-card 
            title="Periode Dataset"
            subtitle="Rentang Waktu Observasi"
            value="{{ $periodeDataset }}"
            unit=""
            icon="calendar_today"
            iconBg="bg-surface-container text-tertiary-container"
            badgeText="Tahun 2023 - 2025"
            badgeClass="bg-surface-container-high text-on-surface-variant border border-outline-variant/50"
            badgeIcon="info"
        />

        <x-kpi-card 
            title="Bulan Puncak Pernikahan"
            subtitle="Volume Tertinggi"
            value="{{ $bulanPuncak }}"
            unit=""
            icon="trending_up"
            iconBg="bg-secondary-fixed text-secondary-container"
            badgeText="Musim Puncak"
            badgeClass="bg-tertiary-fixed text-on-tertiary-fixed-variant"
            badgeIcon="event_repeat"
        />
    </div>

    <!-- Monthly Charts & Provincial Comparison Section -->
    <x-interactive-map 
        :chartBulananData="$chartBulananData" 
        :perbandinganProvinsi="$perbandinganProvinsi" 
        :selectedProvinsi="$selectedProvinsi" 
        :selectedTahun="$selectedTahun" 
        :totalPernikahan="$totalPernikahan" 
    />

    <!-- Donut / Bar Chart: Faktor Utama Penyebab Perceraian (Data BPS 2023–2025) -->
    <x-faktor-perceraian-chart 
        :faktorPerceraianData="$faktorPerceraianData"
        :totalKasusPerceraian="$totalKasusPerceraian"
        :topFaktorPerceraian="$topFaktorPerceraian"
        :selectedProvinsi="$selectedProvinsi"
        :selectedTahun="$selectedTahun"
    />

    <!-- Interactive Indonesia Choropleth Map Section (Peta Persebaran Data Pernikahan Indonesia) -->
    <x-indonesia-choropleth-map 
        :selectedProvinsi="$selectedProvinsi" 
        :selectedTahun="$selectedTahun" 
    />

    {{-- 
    <!-- Data Table Section (Dinonaktifkan: Data Registrasi Pernikahan Menurut Provinsi dari data_pernikahan_2023_2025_flat) -->
    <x-data-table 
        :tabelData="$tabelData ?? []" 
        :selectedProvinsi="$selectedProvinsi" 
        :selectedTahun="$selectedTahun" 
    />
    --}}
</x-layouts.app>
