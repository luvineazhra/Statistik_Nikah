<x-layouts.app title="Kelola Faktor & Sumber Data - NikahInfo">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-fixed text-on-primary-fixed">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Hub Integrasi Analytics
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">
                    <span class="material-symbols-outlined text-[14px] text-tertiary-container">sync</span>
                    Status API: Terhubung (Realtime)
                </span>
            </div>
            <h1 class="font-headline text-2xl md:text-3xl font-bold text-on-surface tracking-tight mt-1">
                Kelola Faktor Analisis & Sumber Data Integrasi
            </h1>
            <p class="text-xs md:text-sm text-on-surface-variant">
                Konfigurasi pipeline sinkronisasi data registri Kementerian Agama, Sensus BPS, Dukcapil, & Badilag MA
            </p>
        </div>

        <div class="flex items-center gap-space-sm self-start lg:self-center shrink-0">
            <button type="button" onclick="alert('Memulai sinkronisasi manual...'); location.reload();" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-container text-on-primary shadow-sm hover:bg-primary transition-colors text-xs font-medium">
                <span class="material-symbols-outlined text-[18px]">sync</span>
                <span>Picu Sinkronisasi Manual</span>
            </button>
        </div>
    </div>

    <!-- Data Sources Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-surface-container-lowest rounded-xl p-5 border border-surface-container-high flex flex-col justify-between gap-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined">account_balance</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant text-[11px] font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Aktif
                </span>
            </div>
            <div>
                <h3 class="font-headline text-base font-bold text-on-surface">Kementerian Agama RI</h3>
                <p class="text-xs text-on-surface-variant mt-0.5">Pencatatan Pernikahan Nasional</p>
                <div class="mt-3 text-xs text-outline flex items-center justify-between pt-2 border-t border-surface-container-high">
                    <span>Frekuensi: Realtime API</span>
                    <span class="font-semibold text-primary">1.82M Records</span>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl p-5 border border-surface-container-high flex flex-col justify-between gap-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg bg-secondary-fixed text-secondary flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined">analytics</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant text-[11px] font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Aktif
                </span>
            </div>
            <div>
                <h3 class="font-headline text-base font-bold text-on-surface">Sensus Demografi BPS</h3>
                <p class="text-xs text-on-surface-variant mt-0.5">Badan Pusat Statistik RI</p>
                <div class="mt-3 text-xs text-outline flex items-center justify-between pt-2 border-t border-surface-container-high">
                    <span>Frekuensi: Bulanan</span>
                    <span class="font-semibold text-primary">34 Provinsi</span>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl p-5 border border-surface-container-high flex flex-col justify-between gap-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg bg-surface-container text-tertiary-container flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant text-[11px] font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Aktif
                </span>
            </div>
            <div>
                <h3 class="font-headline text-base font-bold text-on-surface">Validasi NIK Dukcapil</h3>
                <p class="text-xs text-on-surface-variant mt-0.5">Ditjen Dukcapil Kemendagri</p>
                <div class="mt-3 text-xs text-outline flex items-center justify-between pt-2 border-t border-surface-container-high">
                    <span>Frekuensi: On-Demand</span>
                    <span class="font-semibold text-primary">Validasi KTP</span>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl p-5 border border-surface-container-high flex flex-col justify-between gap-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg bg-surface-container-high text-on-surface-variant flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined">gavel</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-[11px] font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-outline"></span> Batch Daily
                </span>
            </div>
            <div>
                <h3 class="font-headline text-base font-bold text-on-surface">Badilag Mahkamah Agung</h3>
                <p class="text-xs text-on-surface-variant mt-0.5">Badan Peradilan Agama RI</p>
                <div class="mt-3 text-xs text-outline flex items-center justify-between pt-2 border-t border-surface-container-high">
                    <span>Frekuensi: Harian</span>
                    <span class="font-semibold text-primary">Dispensasi & Perceraian</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Factors & Variables Breakdown -->
    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-surface-container-high/60 flex flex-col gap-4">
        <div class="pb-3 border-b border-surface-container-high/60">
            <h3 class="font-headline text-base font-bold text-on-surface">Faktor Variable & Pengaruh Musiman</h3>
            <p class="text-xs text-on-surface-variant">Konfigurasi variabel observasi yang mempengaruhi tren pernikahan nasional</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-surface-container-low/60 border border-outline-variant/30">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">event_repeat</span>
                    <h4 class="font-bold text-sm text-on-surface">Siklus Bulan Syawal & Zulhijah</h4>
                </div>
                <p class="text-xs text-on-surface-variant">Puncak tren pernikahan nasional terjadi pada bulan pasca-Lebaran dan bulan Haji (lonjakan 35–45%).</p>
                <span class="mt-3 inline-block px-2 py-0.5 rounded bg-primary-fixed text-on-primary-fixed text-[11px] font-semibold">Bobot Pengaruh: 42%</span>
            </div>

            <div class="p-4 rounded-xl bg-surface-container-low/60 border border-outline-variant/30">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-secondary text-[20px]">school</span>
                    <h4 class="font-bold text-sm text-on-surface">Tingkat Pendidikan & Pekerjaan</h4>
                </div>
                <p class="text-xs text-on-surface-variant">Rata-rata usia pernikahan meningkat pada wilayah dengan tingkat pendidikan tinggi (S1/D3).</p>
                <span class="mt-3 inline-block px-2 py-0.5 rounded bg-secondary-fixed text-on-secondary-fixed text-[11px] font-semibold">Bobot Pengaruh: 28%</span>
            </div>

            <div class="p-4 rounded-xl bg-surface-container-low/60 border border-outline-variant/30">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-tertiary-container text-[20px]">policy</span>
                    <h4 class="font-bold text-sm text-on-surface">Dispensasi Usia Pernikahan</h4>
                </div>
                <p class="text-xs text-on-surface-variant">Ketentuan UU No 16/2019 usia minimal 19 tahun menurunkan dispensasi nikah hingga 1.8% secara nasional.</p>
                <span class="mt-3 inline-block px-2 py-0.5 rounded bg-tertiary-fixed text-on-tertiary-fixed-variant text-[11px] font-semibold">Bobot Pengaruh: 30%</span>
            </div>
        </div>
    </div>
</x-layouts.app>
