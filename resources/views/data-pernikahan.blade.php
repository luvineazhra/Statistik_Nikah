<x-layouts.app title="Kelola Data Pernikahan • NikahInfo">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md mb-6">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-fixed text-on-primary-fixed">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Statistik Pernikahan Indonesia
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">
                    <span class="material-symbols-outlined text-[14px] text-tertiary-container">database</span>
                    Kementerian Agama & BPS RI (2023–2025)
                </span>
            </div>
            <h1 class="font-headline text-2xl md:text-3xl font-bold text-on-surface tracking-tight mt-1">
                Kelola Data Pernikahan
            </h1>
            <p class="text-xs md:text-sm text-on-surface-variant">
                Kelola, periksa, dan pantau data statistik pernikahan berdasarkan tahun dan provinsi di Indonesia.
            </p>
        </div>

        <div class="flex items-center gap-space-sm self-start lg:self-center shrink-0 flex-wrap">
            <!-- Tombol Ekspor Data CSV / Excel -->
            <a 
                href="{{ route('data-pernikahan.export', request()->all()) }}" 
                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-surface-container-lowest hover:bg-surface-container text-on-surface text-xs font-semibold border border-outline-variant/60 shadow-xs transition-colors"
                title="Unduh data sesuai filter aktif dalam format CSV / Excel"
            >
                <span class="material-symbols-outlined text-[18px] text-emerald-600">file_download</span>
                <span>Ekspor CSV / Excel</span>
            </a>

            <!-- Tombol Tambah Data Pernikahan -->
            <button 
                type="button" 
                onclick="openAddModal()" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-on-primary shadow-md shadow-blue-900/15 transition-all text-xs font-bold cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Tambah Data Pernikahan</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi Feedback (Success, Error, Validation Errors) -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs md:text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs md:text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[20px] text-red-600">error</span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs md:text-sm shadow-xs">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <span class="material-symbols-outlined text-[18px] text-red-600">warning</span>
                <span>Terdapat kendala pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-6 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4 Kartu Ringkasan Statistik (Dihitung Dinamis dari MySQL) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Total Data -->
        <x-kpi-card 
            title="Total Data"
            subtitle="Catatan Statistik Aktif"
            value="{{ number_format($totalData, 0, ',', '.') }}"
            unit="Baris"
            icon="table_chart"
            iconBg="bg-primary-fixed text-primary"
            badgeText="Tersimpan di MySQL"
            badgeClass="bg-primary-fixed text-on-primary-fixed"
            badgeIcon="database"
            badgeNote="Kemenag & BPS"
        />

        <!-- 2. Total Pernikahan -->
        <x-kpi-card 
            title="Total Pernikahan"
            subtitle="Akumulasi Periode Terpilih"
            value="{{ number_format($totalPernikahan, 0, ',', '.') }}"
            unit="Akta"
            icon="volunteer_activism"
            iconBg="bg-emerald-100 text-emerald-800"
            badgeText="{{ $totalData > 0 ? number_format(round($totalPernikahan / max($totalData, 1)), 0, ',', '.') . ' /bln' : '0' }}"
            badgeClass="bg-emerald-50 text-emerald-800 border border-emerald-200"
            badgeIcon="analytics"
            badgeNote="Rata-rata bulanan"
        />

        <!-- 3. Tahun Tersedia -->
        <x-kpi-card 
            title="Tahun Tersedia"
            subtitle="Rentang Observasi Data"
            value="{{ $tahunTersediaLabel }}"
            unit=""
            icon="calendar_month"
            iconBg="bg-secondary-fixed text-secondary"
            badgeText="{{ $selectedTahun ? 'Filter Aktif' : 'Semua Tahun' }}"
            badgeClass="bg-secondary-fixed text-on-secondary-fixed"
            badgeIcon="date_range"
            badgeNote="{{ (string)$selectedTahun === '2024' ? 's.d. Sep 2024' : 'Lengkap' }}"
        />

        <!-- 4. Provinsi dengan Data -->
        <x-kpi-card 
            title="Provinsi dengan Data"
            subtitle="Cakupan Wilayah Terdaftar"
            value="{{ $provinsiDenganData }}"
            unit="dari {{ $totalMasterProvinsi }} Provinsi"
            icon="location_on"
            iconBg="bg-tertiary-fixed text-tertiary-container"
            badgeText="{{ $totalMasterProvinsi > 0 ? round(($provinsiDenganData / $totalMasterProvinsi) * 100) : 0 }}% Cakupan"
            badgeClass="bg-tertiary-fixed text-on-tertiary-fixed-variant"
            badgeIcon="verified"
            badgeNote="Master Provinsi"
        />
    </div>

    <!-- Catatan Penanganan Data Tahun Belum Lengkap (Misal: 2024 s.d. September) -->
    @if((string)$selectedTahun === '2024')
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs md:text-sm flex items-start gap-3 shadow-xs">
            <span class="material-symbols-outlined text-[22px] text-amber-600 mt-0.5 shrink-0">info</span>
            <div>
                <p class="font-bold text-amber-950 mb-0.5">Catatan Kelengkapan Data Tahun 2024:</p>
                <p class="text-amber-800 leading-relaxed text-xs">
                    Publikasi statistik resmi Kementerian Agama RI untuk tahun 2024 saat ini tersedia untuk periode <strong>Januari hingga September</strong>. Bulan Oktober, November, dan Desember <strong>belum dirilis</strong> secara resmi sehingga tidak diisi dengan angka nol palsu agar kalkulasi analitik tetap objektif.
                </p>
            </div>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-4 mb-6 shadow-xs">
        <form method="GET" action="{{ route('data-pernikahan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Filter Tahun -->
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterTahun">
                    Tahun Observasi
                </label>
                <select name="tahun" id="filterTahun" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary cursor-pointer">
                    <option value="">Semua Tahun (2023–2025)</option>
                    @foreach($daftarTahun as $thn)
                        <option value="{{ $thn }}" {{ (string)$selectedTahun === (string)$thn ? 'selected' : '' }}>
                            Tahun {{ $thn }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Provinsi (Dari Master Table `provinces`) -->
            <div class="lg:col-span-4">
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterProvinsi">
                    Provinsi (Master)
                </label>
                <select name="provinsi" id="filterProvinsi" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary cursor-pointer">
                    <option value="">Semua Provinsi ({{ count($masterProvinces) }})</option>
                    @foreach($masterProvinces as $prov)
                        <option value="{{ $prov->name }}" {{ $selectedProvinsi === $prov->name ? 'selected' : '' }}>
                            {{ $prov->code ? $prov->code . ' - ' : '' }}{{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Bulan -->
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterBulan">
                    Bulan
                </label>
                <select name="bulan" id="filterBulan" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary cursor-pointer">
                    <option value="">Semua Bulan (Jan–Des)</option>
                    @foreach($daftarBulan as $bln)
                        <option value="{{ $bln }}" {{ $selectedBulan === $bln ? 'selected' : '' }}>
                            {{ $bln }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Terapkan & Reset Filter -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-primary-container hover:bg-primary text-on-primary font-headline font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('data-pernikahan') }}" class="py-2 px-3 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs flex items-center justify-center gap-1 border border-outline-variant/40 transition" title="Reset Semua Filter">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                </a>
            </div>
        </form>

        <!-- Indikator Filter Aktif -->
        @if($selectedTahun || $selectedProvinsi || $selectedBulan)
            <div class="mt-3 pt-3 border-t border-surface-container-high/60 flex items-center gap-2 flex-wrap text-xs">
                <span class="text-outline font-medium">Filter Aktif:</span>
                @if($selectedTahun)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-primary-fixed text-on-primary-fixed text-[11px] font-medium">
                        Tahun: {{ $selectedTahun }}
                    </span>
                @endif
                @if($selectedProvinsi)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-secondary-fixed text-on-secondary-fixed text-[11px] font-medium">
                        Provinsi: {{ $selectedProvinsi }}
                    </span>
                @endif
                @if($selectedBulan)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-tertiary-fixed text-on-tertiary-fixed-variant text-[11px] font-medium">
                        Bulan: {{ $selectedBulan }}
                    </span>
                @endif
                <a href="{{ route('data-pernikahan') }}" class="text-red-600 hover:underline text-[11px] font-medium ml-1">
                    Hapus Semua Filter
                </a>
            </div>
        @endif
    </div>

    <!-- Tabel Data Pernikahan -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high shadow-xs overflow-hidden mb-6">
        <!-- Table Header Toolbar -->
        <div class="px-5 py-4 border-b border-surface-container-high flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-primary">analytics</span>
                <h2 class="font-headline font-bold text-sm text-on-surface">
                    Daftar Data Pernikahan Berdasarkan Periode & Wilayah
                </h2>
                <span class="text-xs px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-medium">
                    {{ number_format($pernikahanList->total(), 0, ',', '.') }} Data
                </span>
            </div>
            @if($pernikahanList->total() > 0)
                <span class="text-xs text-on-surface-variant font-mono">
                    Menampilkan baris {{ $pernikahanList->firstItem() }}–{{ $pernikahanList->lastItem() }} dari {{ number_format($pernikahanList->total(), 0, ',', '.') }}
                </span>
            @endif
        </div>

        <!-- Tabel Canvas -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-semibold uppercase tracking-wider border-b border-surface-container-high">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 w-20 text-center">Tahun</th>
                        <th class="py-3 px-4 w-24">Kode</th>
                        <th class="py-3 px-4">Nama Provinsi</th>
                        <th class="py-3 px-4 w-28">Bulan</th>
                        <th class="py-3 px-4 text-right w-36">Jumlah Pernikahan</th>
                        <th class="py-3 px-4 text-center w-36">Sumber Data</th>
                        <th class="py-3 px-4 text-center w-32">Status Catatan</th>
                        <th class="py-3 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-high">
                    @forelse ($pernikahanList as $index => $item)
                        <tr class="hover:bg-surface-container-low/50 transition">
                            <!-- Nomor -->
                            <td class="py-3.5 px-4 text-center font-mono text-on-surface-variant">
                                {{ $pernikahanList->firstItem() + $index }}
                            </td>

                            <!-- Tahun -->
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-on-surface">
                                <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface text-xs inline-block">
                                    {{ $item->tahun }}
                                </span>
                            </td>

                            <!-- Kode Wilayah -->
                            <td class="py-3.5 px-4 font-mono">
                                <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface font-semibold text-[11px] border border-outline-variant/30">
                                    {{ $item->kode_wilayah ?: '-' }}
                                </span>
                            </td>

                            <!-- Nama Provinsi -->
                            <td class="py-3.5 px-4 font-semibold text-on-surface">
                                <a href="{{ route('data-pernikahan', ['provinsi' => $item->provinsi, 'tahun' => $item->tahun]) }}" class="hover:text-primary transition-colors hover:underline" title="Filter provinsi ini">
                                    {{ $item->provinsi }}
                                </a>
                            </td>

                            <!-- Bulan -->
                            <td class="py-3.5 px-4 font-medium text-on-surface">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-800 text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    {{ $item->bulan }}
                                </span>
                            </td>

                            <!-- Jumlah Pernikahan -->
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-primary text-sm">
                                {{ number_format($item->jumlah_pernikahan, 0, ',', '.') }}
                                <span class="text-[10px] text-outline font-normal">Akta</span>
                            </td>

                            <!-- Sumber Data -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] text-on-surface-variant font-mono bg-surface-container px-2 py-0.5 rounded">
                                    <span class="material-symbols-outlined text-[13px] text-outline">verified</span>
                                    Kementerian Agama
                                </span>
                            </td>

                            <!-- Status Catatan -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-semibold border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Terverifikasi
                                </span>
                            </td>

                            <!-- Aksi (Detail, Edit, Hapus) -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Tombol Lihat Detail -->
                                    <button 
                                        type="button" 
                                        onclick="openDetailModal({{ $item->id }})" 
                                        class="p-1.5 rounded-lg bg-surface-container-high text-on-surface-variant hover:bg-surface-dim hover:text-primary transition cursor-pointer" 
                                        title="Lihat Rincian Statistik Tahunan & Bulanan"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    </button>

                                    <!-- Tombol Edit -->
                                    <button 
                                        type="button" 
                                        onclick="openEditModal({{ $item->id }}, '{{ $item->tahun }}', '{{ addslashes($item->provinsi) }}', '{{ $item->bulan }}', {{ (int) $item->jumlah_pernikahan }})" 
                                        class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition cursor-pointer" 
                                        title="Edit Data Pernikahan"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button 
                                        type="button" 
                                        onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->provinsi) }}', '{{ $item->tahun }}', '{{ $item->bulan }}', '{{ number_format($item->jumlah_pernikahan, 0, ',', '.') }}')" 
                                        class="p-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition cursor-pointer" 
                                        title="Hapus Catatan Data Pernikahan"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-on-surface-variant">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="w-12 h-12 rounded-full bg-surface-container-high flex items-center justify-center text-outline">
                                        <span class="material-symbols-outlined text-[28px]">search_off</span>
                                    </div>
                                    <span class="font-headline font-bold text-sm text-on-surface">Data Pernikahan Tidak Ditemukan</span>
                                    <span class="text-xs text-on-surface-variant">Tidak ada catatan data statistik yang sesuai dengan filter atau kata kunci pencarian.</span>
                                    <a href="{{ route('data-pernikahan') }}" class="mt-2 px-3 py-1.5 rounded-lg bg-primary-container text-on-primary text-xs font-semibold hover:bg-primary transition">
                                        Reset Filter Pencarian
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div class="px-5 py-4 border-t border-surface-container-high bg-surface-container-lowest flex flex-col sm:flex-row items-center justify-between gap-3">
            <span class="text-xs text-on-surface-variant font-medium">
                Halaman {{ $pernikahanList->currentPage() }} dari {{ max($pernikahanList->lastPage(), 1) }} (Total {{ number_format($pernikahanList->total(), 0, ',', '.') }} Catatan)
            </span>
            <div>
                {{ $pernikahanList->links() }}
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 1. MODAL TAMBAH DATA PERNIKAHAN            -->
    <!-- ========================================== -->
    <div id="addModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full border border-surface-container-high shadow-2xl p-6 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px] text-primary">add_circle</span>
                    <div>
                        <h3 class="font-headline font-bold text-base text-on-surface">Tambah Data Pernikahan Baru</h3>
                        <p class="text-[11px] text-on-surface-variant">Input catatan registrasi pernikahan resmi menurut provinsi dan bulan</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddModal()" class="text-outline hover:text-on-surface p-1 rounded-lg">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form action="{{ route('data-pernikahan.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Tahun -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addTahun">
                            Tahun Observasi <span class="text-red-500">*</span>
                        </label>
                        <select name="tahun" id="addTahun" required class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-mono font-bold">
                            @foreach($daftarTahun as $thn)
                                <option value="{{ $thn }}">{{ $thn }}</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-outline mt-1 block">Periode tahun pencatatan</span>
                    </div>

                    <!-- Bulan -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addBulan">
                            Bulan <span class="text-red-500">*</span>
                        </label>
                        <select name="bulan" id="addBulan" required class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-semibold">
                            @foreach($daftarBulan as $bln)
                                <option value="{{ $bln }}">{{ $bln }}</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-outline mt-1 block">Januari – Desember</span>
                    </div>
                </div>

                <!-- Provinsi (Dari Master Provinces) -->
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addProvinsi">
                        Provinsi <span class="text-red-500">*</span>
                    </label>
                    <select name="provinsi" id="addProvinsi" required class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-semibold">
                        <option value="">-- Pilih Provinsi Master --</option>
                        @foreach($masterProvinces as $prov)
                            <option value="{{ $prov->name }}">{{ $prov->name }} (Kode: {{ $prov->code ?: '-' }})</option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-outline mt-1 block">Data provinsi diambil langsung dari tabel master provinsi</span>
                </div>

                <!-- Jumlah Pernikahan -->
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addJumlah">
                        Jumlah Pernikahan (Akta) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3 text-[18px] text-outline">volunteer_activism</span>
                        <input 
                            type="number" 
                            name="jumlah_pernikahan" 
                            id="addJumlah" 
                            min="0" 
                            placeholder="Contoh: 1450" 
                            required 
                            class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-mono font-bold"
                        >
                    </div>
                    <span class="text-[10px] text-outline mt-1 block">Bilangan bulat positif non-negatif. Kosongkan jangan disamakan dengan nol.</span>
                </div>

                <!-- Sumber Data Info -->
                <div class="p-3 rounded-xl bg-surface-container-high/60 border border-outline-variant/40 text-xs">
                    <div class="flex items-center gap-1.5 font-semibold text-on-surface mb-0.5">
                        <span class="material-symbols-outlined text-[16px] text-primary">shield_person</span>
                        <span>Sumber Data Resmi:</span>
                    </div>
                    <p class="text-[11px] text-on-surface-variant">
                        Ditjen Bimas Islam Kementerian Agama RI & Badan Pusat Statistik (BPS).
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-container-high">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary-container hover:bg-primary text-on-primary text-xs font-bold shadow-sm transition">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. MODAL EDIT DATA PERNIKAHAN             -->
    <!-- ========================================== -->
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full border border-surface-container-high shadow-2xl p-6 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px] text-blue-600">edit_square</span>
                    <div>
                        <h3 class="font-headline font-bold text-base text-on-surface">Edit Data Pernikahan</h3>
                        <p class="text-[11px] text-on-surface-variant">Perbarui angka statistik registrasi pernikahan</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-outline hover:text-on-surface p-1 rounded-lg">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Tahun -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editTahun">
                            Tahun Observasi <span class="text-red-500">*</span>
                        </label>
                        <select name="tahun" id="editTahun" required class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-mono font-bold">
                            @foreach($daftarTahun as $thn)
                                <option value="{{ $thn }}">{{ $thn }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bulan -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editBulan">
                            Bulan <span class="text-red-500">*</span>
                        </label>
                        <select name="bulan" id="editBulan" required class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-semibold">
                            @foreach($daftarBulan as $bln)
                                <option value="{{ $bln }}">{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Provinsi (Dari Master Provinces) -->
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editProvinsi">
                        Provinsi <span class="text-red-500">*</span>
                    </label>
                    <select name="provinsi" id="editProvinsi" required class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-semibold">
                        @foreach($masterProvinces as $prov)
                            <option value="{{ $prov->name }}">{{ $prov->name }} (Kode: {{ $prov->code ?: '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Jumlah Pernikahan -->
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editJumlah">
                        Jumlah Pernikahan (Akta) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3 text-[18px] text-outline">volunteer_activism</span>
                        <input 
                            type="number" 
                            name="jumlah_pernikahan" 
                            id="editJumlah" 
                            min="0" 
                            required 
                            class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-mono font-bold"
                        >
                    </div>
                    <span class="text-[10px] text-outline mt-1 block">Angka terbaru yang diverifikasi</span>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-surface-container-high">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. MODAL DETAIL RINCIAN STATISTIK         -->
    <!-- ========================================== -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full border border-surface-container-high shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px] text-primary">insights</span>
                    <div>
                        <h3 class="font-headline font-bold text-base text-on-surface" id="detailProvinsiTitle">Rincian Data Pernikahan</h3>
                        <p class="text-[11px] text-on-surface-variant" id="detailSubtitle">Laporan distribusi bulanan dan konsolidasi tahunan</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-outline hover:text-on-surface p-1 rounded-lg">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Loader State -->
            <div id="detailLoading" class="py-12 flex flex-col items-center justify-center gap-3">
                <div class="w-8 h-8 border-3 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                <span class="text-xs text-on-surface-variant font-medium">Memuat data statistik dari database...</span>
            </div>

            <!-- Detail Content -->
            <div id="detailContent" class="hidden space-y-4">
                <!-- Highlight Ringkasan Info Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 rounded-xl bg-surface-container-low border border-surface-container-high">
                        <span class="text-[10px] text-outline uppercase font-semibold block">Tahun Observasi</span>
                        <span class="font-headline font-bold text-base text-on-surface" id="detailYear">-</span>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low border border-surface-container-high">
                        <span class="text-[10px] text-outline uppercase font-semibold block">Kode Wilayah</span>
                        <span class="font-mono font-bold text-base text-on-surface" id="detailKodeWilayah">-</span>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low border border-surface-container-high">
                        <span class="text-[10px] text-outline uppercase font-semibold block">Akta Periode Ini</span>
                        <span class="font-headline font-bold text-base text-primary" id="detailJumlahBulan">-</span>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low border border-surface-container-high">
                        <span class="text-[10px] text-outline uppercase font-semibold block">Total Tahunan</span>
                        <span class="font-headline font-bold text-base text-emerald-700" id="detailTotalTahunan">-</span>
                    </div>
                </div>

                <!-- KPI Mini Rincian -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-blue-50/60 border border-blue-200 flex items-center justify-between">
                        <span class="text-blue-900 font-medium">Rata-rata Bulanan:</span>
                        <span class="font-bold text-blue-950 font-mono" id="detailRataRata">-</span>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200 flex items-center justify-between">
                        <span class="text-amber-900 font-medium">Bulan Puncak Tertinggi:</span>
                        <span class="font-bold text-amber-950" id="detailBulanPuncak">-</span>
                    </div>
                </div>

                <!-- Breakdown 12 Bulan (Januari s.d. Desember) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-headline font-bold text-xs text-on-surface uppercase tracking-wider">
                            Rincian Distribusi Bulanan (Januari – Desember)
                        </h4>
                        <span class="text-[11px] text-outline" id="detailStatusKelengkapan">-</span>
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="monthlyBreakdownGrid">
                        <!-- Populated via JavaScript -->
                    </div>
                </div>

                <!-- Catatan Kelengkapan & Metrik Resmi -->
                <div class="p-3.5 rounded-xl bg-surface-container-high/60 border border-outline-variant/40 space-y-1.5 text-xs">
                    <div class="flex items-center gap-1.5 font-semibold text-on-surface">
                        <span class="material-symbols-outlined text-[16px] text-primary">info</span>
                        <span>Catatan Integritas Data & Sumber:</span>
                    </div>
                    <p class="text-[11px] text-on-surface-variant leading-relaxed" id="detailCatatanKelengkapan">
                        -
                    </p>
                    <div class="pt-1 flex items-center gap-3 text-[10px] text-outline">
                        <span>Sumber: Kementerian Agama RI & BPS RI</span>
                        <span>•</span>
                        <span>Status: Terverifikasi di MySQL</span>
                    </div>
                </div>

                <!-- Footer Modal Detail -->
                <div class="flex items-center justify-end pt-2 border-t border-surface-container-high">
                    <button type="button" onclick="closeDetailModal()" class="px-5 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface text-xs font-semibold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. MODAL HAPUS DATA PERNIKAHAN            -->
    <!-- ========================================== -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full border border-surface-container-high shadow-2xl p-6 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-700 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">warning</span>
                </div>
                <div>
                    <h3 class="font-headline font-bold text-base text-on-surface">Konfirmasi Hapus Data</h3>
                    <p class="text-[11px] text-on-surface-variant">Tindakan ini akan menghapus catatan statistik dari database</p>
                </div>
            </div>

            <p class="text-xs text-on-surface-variant leading-relaxed mb-4">
                Apakah Anda yakin ingin menghapus catatan statistik pernikahan untuk:
            </p>

            <div class="p-3.5 rounded-xl bg-red-50/70 border border-red-200 mb-4 text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-outline">Provinsi:</span>
                    <strong class="text-red-950 font-semibold" id="deleteProvinsiText">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-outline">Periode:</span>
                    <strong class="text-red-950 font-semibold" id="deletePeriodeText">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-outline">Jumlah Akta:</span>
                    <strong class="text-red-950 font-mono font-bold" id="deleteJumlahText">-</strong>
                </div>
            </div>

            <p class="text-[11px] text-red-600 mb-5 leading-normal">
                Peringatan: Penghapusan akan memicu penghitungan ulang otomatis pada grafik dan peta statistik pernikahan.
            </p>

            <form id="deleteForm" method="POST" class="flex items-center justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm transition">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC INTERAKTIF UNTUK MODAL & API DETAIL -->
    <script>
        // Modal Tambah
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Modal Edit
        function openEditModal(id, tahun, provinsi, bulan, jumlah) {
            const form = document.getElementById('editForm');
            form.action = `/admin/data-pernikahan/${id}`;
            document.getElementById('editTahun').value = tahun;
            document.getElementById('editProvinsi').value = provinsi;
            document.getElementById('editBulan').value = bulan;
            document.getElementById('editJumlah').value = jumlah;
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Modal Detail (Fetch dari Endpoint API JSON)
        async function openDetailModal(id) {
            const modal = document.getElementById('detailModal');
            const loading = document.getElementById('detailLoading');
            const content = document.getElementById('detailContent');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');

            try {
                const response = await fetch(`/admin/data-pernikahan/api/${id}/detail`);
                if (!response.ok) throw new Error('Gagal memuat rincian data.');
                const res = await response.json();
                const d = res.data;

                document.getElementById('detailProvinsiTitle').innerText = `Rincian Pernikahan ${d.provinsi}`;
                document.getElementById('detailSubtitle').innerText = `Tahun ${d.tahun} • Periode ${d.bulan}`;
                document.getElementById('detailYear').innerText = d.tahun;
                document.getElementById('detailKodeWilayah').innerText = d.kode_wilayah;
                document.getElementById('detailJumlahBulan').innerText = `${d.bulan}: ${d.jumlah_formatted}`;
                document.getElementById('detailTotalTahunan').innerText = d.total_tahunan_format;
                document.getElementById('detailRataRata').innerText = d.rata_rata_bulanan;
                document.getElementById('detailBulanPuncak').innerText = d.bulan_puncak;
                document.getElementById('detailStatusKelengkapan').innerText = `${d.bulan_tersedia_count} dari 12 Bulan Tersedia`;
                document.getElementById('detailCatatanKelengkapan').innerText = d.catatan_kelengkapan;

                // Render 12 months breakdown
                const grid = document.getElementById('monthlyBreakdownGrid');
                grid.innerHTML = '';

                for (const [bulan, info] of Object.entries(d.monthly_breakdown)) {
                    const isCurrent = (bulan === d.bulan);
                    const isAvailable = (info.status === 'tersedia');
                    
                    let bgClass = 'bg-surface-container-low border-surface-container-high text-on-surface';
                    let badgeClass = 'text-outline';
                    
                    if (isCurrent) {
                        bgClass = 'bg-primary-fixed/30 border-primary text-primary';
                        badgeClass = 'text-primary font-bold';
                    } else if (!isAvailable) {
                        bgClass = 'bg-slate-50 border-slate-200 text-slate-400 opacity-75';
                        badgeClass = 'text-slate-400 italic text-[10px]';
                    }

                    const card = document.createElement('div');
                    card.className = `p-2.5 rounded-xl border flex flex-col justify-between ${bgClass}`;
                    card.innerHTML = `
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-xs">${bulan}</span>
                            ${isCurrent ? '<span class="text-[9px] bg-primary text-on-primary px-1.5 py-0.2 rounded font-bold">Aktif</span>' : ''}
                        </div>
                        <div class="text-xs font-mono font-bold ${badgeClass}">
                            ${info.jumlah_formatted}${isAvailable ? ' Akta' : ''}
                        </div>
                    `;
                    grid.appendChild(card);
                }

                loading.classList.add('hidden');
                content.classList.remove('hidden');
            } catch (err) {
                loading.innerHTML = `
                    <div class="text-center text-red-600 p-4">
                        <span class="material-symbols-outlined text-[28px]">error</span>
                        <p class="text-xs font-bold mt-1">Gagal memuat rincian data pernikahan.</p>
                        <button type="button" onclick="closeDetailModal()" class="mt-3 px-3 py-1.5 rounded-lg bg-surface-container-high text-xs font-semibold">Tutup</button>
                    </div>
                `;
            }
        }
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Modal Hapus
        function openDeleteModal(id, provinsi, tahun, bulan, jumlah) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/data-pernikahan/${id}`;
            document.getElementById('deleteProvinsiText').innerText = provinsi;
            document.getElementById('deletePeriodeText').innerText = `${bulan} ${tahun}`;
            document.getElementById('deleteJumlahText').innerText = `${jumlah} Akta`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Esc key closes any open modal
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDetailModal();
                closeDeleteModal();
            }
        });
    </script>
</x-layouts.app>
