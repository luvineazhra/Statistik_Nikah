<x-layouts.app title="Kelola Provinsi • NikahInfo">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md mb-6">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-fixed text-on-primary-fixed">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Master Wilayah Indonesia
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">
                    <span class="material-symbols-outlined text-[14px] text-tertiary-container">map</span>
                    Satu Data BPS & Kemenag RI
                </span>
            </div>
            <h1 class="font-headline text-2xl md:text-3xl font-bold text-on-surface tracking-tight mt-1">
                Kelola Provinsi
            </h1>
            <p class="text-xs md:text-sm text-on-surface-variant">
                Kelola data wilayah provinsi yang digunakan dalam statistik pernikahan, perceraian, dan peta persebaran Indonesia
            </p>
        </div>

        <div class="flex items-center gap-space-sm self-start lg:self-center shrink-0">
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-on-primary shadow-md shadow-blue-900/15 transition-all text-xs font-bold cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Tambah Provinsi</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts (Success, Error, Validation Errors) -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs md:text-sm flex items-center justify-between shadow-sm">
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
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs md:text-sm flex items-center justify-between shadow-sm">
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
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs md:text-sm shadow-sm">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <span class="material-symbols-outlined text-[18px] text-red-600">warning</span>
                <span>Terdapat kesalahan pada input form:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-6 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4 Summary KPI Stat Cards Grid (Semua Berasal dari Database & GeoJSON Sebenarnya) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Total Provinsi -->
        <x-kpi-card 
            title="Total Provinsi"
            subtitle="Database Master Wilayah"
            value="{{ $totalProvinsi }}"
            unit="Provinsi"
            icon="public"
            iconBg="bg-primary-fixed text-primary"
            badgeText="Tersimpan di MySQL"
            badgeClass="bg-primary-fixed text-on-primary-fixed"
            badgeIcon="database"
            badgeNote="BPS & Kemenag RI"
        />

        <!-- 2. Jumlah Provinsi Aktif -->
        <x-kpi-card 
            title="Provinsi Aktif"
            subtitle="Wilayah Beroperasi Aktif"
            value="{{ $totalAktif }}"
            unit="Provinsi"
            icon="verified"
            iconBg="bg-emerald-100 text-emerald-800"
            badgeText="{{ $totalProvinsi > 0 ? round(($totalAktif / $totalProvinsi) * 100) : 0 }}% Aktif"
            badgeClass="bg-emerald-50 text-emerald-800 border border-emerald-200"
            badgeIcon="check_circle"
            badgeNote="{{ $totalProvinsi - $totalAktif }} Nonaktif"
        />

        <!-- 3. Terhubung dengan Peta -->
        <x-kpi-card 
            title="Terhubung dengan Peta"
            subtitle="Poligon GeoJSON Terpetakan"
            value="{{ $totalTerhubungPeta }}"
            unit="Provinsi"
            icon="map"
            iconBg="bg-secondary-fixed text-secondary"
            badgeText="Sinkron GeoJSON"
            badgeClass="bg-tertiary-fixed text-on-tertiary-fixed-variant"
            badgeIcon="link"
            badgeNote="Batas wilayah valid"
        />

        <!-- 4. Perlu Diperiksa Pemetaannya -->
        <x-kpi-card 
            title="Perlu Diperiksa Pemetaan"
            subtitle="Belum Memiliki Poligon Peta"
            value="{{ $totalPerluPeriksa }}"
            unit="Provinsi"
            icon="{{ $totalPerluPeriksa > 0 ? 'warning' : 'task_alt' }}"
            iconBg="{{ $totalPerluPeriksa > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}"
            badgeText="{{ $totalPerluPeriksa > 0 ? 'Perlu Geometri' : 'Semua Terhubung' }}"
            badgeClass="{{ $totalPerluPeriksa > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}"
            badgeIcon="{{ $totalPerluPeriksa > 0 ? 'sync_problem' : 'done_all' }}"
            badgeNote="{{ $totalPerluPeriksa > 0 ? 'Cek file GeoJSON' : 'Valid 100%' }}"
        />
    </div>

    <!-- Filter Bar Container -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('provinsi') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Filter Status Provinsi -->
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterStatus">
                    Status Provinsi
                </label>
                <select name="status" id="filterStatus" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                    <option value="">-- Semua Status --</option>
                    <option value="Aktif" {{ $filterStatus === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ $filterStatus === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Filter Status Pemetaan Peta -->
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterPemetaan">
                    Status Pemetaan
                </label>
                <select name="pemetaan" id="filterPemetaan" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                    <option value="">-- Semua Pemetaan --</option>
                    <option value="terhubung" {{ $filterPemetaan === 'terhubung' ? 'selected' : '' }}>Terhubung Peta</option>
                    <option value="belum_terhubung" {{ $filterPemetaan === 'belum_terhubung' ? 'selected' : '' }}>Belum Terhubung</option>
                </select>
            </div>

            <!-- Pengurutan Data -->
            <div class="lg:col-span-4">
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterSort">
                    Pengurutan
                </label>
                <div class="flex items-center gap-1.5">
                    <select name="sort_by" id="filterSort" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                        <option value="code" {{ $sortBy === 'code' ? 'selected' : '' }}>Kode Wilayah</option>
                        <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Nama Provinsi</option>
                        <option value="updated_at" {{ $sortBy === 'updated_at' ? 'selected' : '' }}>Terakhir Diperbarui</option>
                        <option value="status" {{ $sortBy === 'status' ? 'selected' : '' }}>Status</option>
                    </select>
                    <select name="sort_order" class="w-20 px-2 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>A-Z</option>
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Z-A</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Aksi Filter -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-primary-container hover:bg-primary text-on-primary font-headline font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('provinsi') }}" class="py-2 px-3 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs flex items-center justify-center gap-1 border border-outline-variant/40 transition" title="Reset Semua Filter">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-surface-container-high flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-primary">format_list_bulleted</span>
                <h2 class="font-headline font-bold text-sm text-on-surface">
                    Daftar Wilayah Provinsi Indonesia ({{ $provinces->total() }} Data Ditemukan)
                </h2>
            </div>
            @if($provinces->total() > 0)
                <span class="text-xs text-on-surface-variant font-mono">
                    Menampilkan baris {{ $provinces->firstItem() }}–{{ $provinces->lastItem() }} dari {{ $provinces->total() }}
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-semibold uppercase tracking-wider border-b border-surface-container-high">
                        <th class="py-3 px-4 w-12 text-center">Nomor</th>
                        <th class="py-3 px-4 w-28">Kode Provinsi</th>
                        <th class="py-3 px-4">Nama Provinsi</th>
                        <th class="py-3 px-4 text-center w-32">Status Provinsi</th>
                        <th class="py-3 px-4 text-center w-40">Status Pemetaan</th>
                        <th class="py-3 px-4 text-center w-36">Terakhir Diperbarui</th>
                        <th class="py-3 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-high">
                    @forelse ($provinces as $index => $item)
                        @php
                            $isAktif = in_array($item->status, ['Aktif', 'Terverifikasi']);
                            $isMapped = $item->geo_mapping['is_mapped'] ?? false;
                            $hasRelations = $item->statistical_relations['has_relations'] ?? false;
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition">
                            <!-- Nomor -->
                            <td class="py-3.5 px-4 text-center font-mono text-on-surface-variant">
                                {{ $provinces->firstItem() + $index }}
                            </td>

                            <!-- Kode Provinsi -->
                            <td class="py-3.5 px-4 font-mono">
                                <span class="px-2.5 py-1 rounded-lg bg-surface-container-high text-on-surface font-bold text-xs inline-flex items-center gap-1 border border-outline-variant/30">
                                    <span class="text-[10px] text-outline font-normal">KD</span>
                                    <span>{{ $item->code }}</span>
                                </span>
                            </td>

                            <!-- Nama Provinsi & Wilayah Kepulauan -->
                            <td class="py-3.5 px-4">
                                <div class="flex flex-col">
                                    <span class="font-headline font-bold text-sm text-on-surface">
                                        {{ $item->name }}
                                    </span>
                                    <span class="text-[11px] text-on-surface-variant flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[13px] text-outline">travel_explore</span>
                                        <span>Wilayah: {{ $item->island_group ?: 'Indonesia' }}</span>
                                        @if($hasRelations)
                                            <span class="ml-1 px-1.5 py-0.2 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200" title="Terhubung dengan statistik historis 2023–2025">
                                                Historis 2023–2025
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <!-- Status Provinsi: Aktif atau Nonaktif -->
                            <td class="py-3.5 px-4 text-center">
                                @if ($isAktif)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Status Pemetaan: Terhubung atau Belum Terhubung -->
                            <td class="py-3.5 px-4 text-center">
                                @if ($isMapped)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-teal-50 text-teal-800 border border-teal-200" title="{{ $item->geo_mapping['note'] ?? 'Terhubung ke GeoJSON' }}">
                                        <span class="material-symbols-outlined text-[14px] text-teal-600">check_circle</span>
                                        <span>Terhubung</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-300" title="{{ $item->geo_mapping['note'] ?? 'Belum ada geometri GeoJSON' }}">
                                        <span class="material-symbols-outlined text-[14px] text-amber-600">warning</span>
                                        <span>Belum Terhubung</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Terakhir Diperbarui -->
                            <td class="py-3.5 px-4 text-center text-[11px] text-on-surface-variant font-mono">
                                {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-' }}
                            </td>

                            <!-- Aksi (Detail, Edit, Nonaktifkan, Hapus) -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Tombol Detail -->
                                    <button 
                                        type="button" 
                                        onclick="openDetailModal({{ $item->id }})" 
                                        class="p-1.5 rounded-lg bg-surface-container-high text-on-surface-variant hover:bg-surface-dim hover:text-primary transition" 
                                        title="Lihat Detail Provinsi"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                                    </button>

                                    <!-- Tombol Edit -->
                                    <button 
                                        type="button" 
                                        onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->code) }}', '{{ addslashes($item->name) }}', '{{ addslashes($item->island_group ?: '') }}', '{{ $item->status }}', {{ $hasRelations ? 'true' : 'false' }})" 
                                        class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition" 
                                        title="Edit Data Provinsi"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </button>

                                    <!-- Tombol Nonaktifkan / Aktifkan -->
                                    <button 
                                        type="button" 
                                        onclick="openToggleModal({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $isAktif ? 'Aktif' : 'Nonaktif' }}')" 
                                        class="p-1.5 rounded-lg {{ $isAktif ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition" 
                                        title="{{ $isAktif ? 'Nonaktifkan Provinsi' : 'Aktifkan Provinsi' }}"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">{{ $isAktif ? 'power_settings_new' : 'check' }}</span>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button 
                                        type="button" 
                                        onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ addslashes($item->code) }}', {{ $hasRelations ? 'true' : 'false' }}, {{ $item->statistical_relations['nikah_count'] ?? 0 }}, {{ $item->statistical_relations['perceraian_count'] ?? 0 }})" 
                                        class="p-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" 
                                        title="Hapus Data Provinsi"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-on-surface-variant">
                                <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                                    <div class="w-12 h-12 rounded-full bg-surface-container-high flex items-center justify-center text-outline">
                                        <span class="material-symbols-outlined text-[28px]">search_off</span>
                                    </div>
                                    <span class="font-headline font-bold text-sm text-on-surface">Data Provinsi Tidak Ditemukan</span>
                                    <span class="text-xs text-on-surface-variant">Tidak ada hasil yang sesuai dengan kriteria pencarian atau filter yang dipilih. Silakan reset filter untuk melihat semua data.</span>
                                    <a href="{{ route('provinsi') }}" class="mt-2 px-3 py-1.5 rounded-lg bg-primary-container text-on-primary text-xs font-semibold hover:bg-primary transition">
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
                Halaman {{ $provinces->currentPage() }} dari {{ max($provinces->lastPage(), 1) }} (Total {{ $provinces->total() }} Provinsi)
            </span>
            <div>
                {{ $provinces->links() }}
            </div>
        </div>
    </div>

    <!-- 1. MODAL TAMBAH PROVINSI -->
    <div id="addModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full border border-surface-container-high shadow-2xl p-6 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px] text-primary">add_circle</span>
                    <div>
                        <h3 class="font-headline font-bold text-base text-on-surface">Tambah Data Provinsi Baru</h3>
                        <p class="text-[11px] text-on-surface-variant">Tambahkan data master wilayah baru ke dalam sistem</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddModal()" class="text-outline hover:text-on-surface p-1 rounded-lg">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form action="{{ route('provinsi.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Kode Provinsi -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addCode">
                            Kode Provinsi <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="code" 
                            id="addCode" 
                            placeholder="Contoh: 93" 
                            required 
                            maxlength="10"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-mono"
                        >
                        <span class="text-[10px] text-outline mt-1 block">Kode BPS/Kemendagri unik (2-4 digit)</span>
                    </div>

                    <!-- Status Provinsi (Default: Aktif) -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addStatus">
                            Status Provinsi <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="addStatus" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-semibold" required>
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                        <span class="text-[10px] text-outline mt-1 block">Nilai awal standar: Aktif</span>
                    </div>
                </div>

                <!-- Nama Provinsi -->
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addName">
                        Nama Provinsi <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="addName" 
                        placeholder="Contoh: Papua Barat Daya" 
                        required 
                        maxlength="100"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary"
                    >
                </div>

                <!-- Kelompok Pulau / Wilayah -->
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addIslandGroup">
                        Wilayah / Kelompok Pulau
                    </label>
                    <select name="island_group" id="addIslandGroup" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                        <option value="">-- Pilih Wilayah Pulau --</option>
                        @foreach ($islandGroups as $island)
                            <option value="{{ $island }}">{{ $island }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Catatan Integrasi Peta GeoJSON -->
                <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-200 text-blue-900 text-xs flex items-start gap-2">
                    <span class="material-symbols-outlined text-[18px] text-blue-700 shrink-0 mt-0.5">info</span>
                    <div>
                        <span class="font-semibold block">Integrasi Peta Otomatis:</span>
                        <span class="text-[11px] text-blue-800">Sistem akan secara otomatis mencocokkan kode provinsi dengan geometri pada file GeoJSON Indonesia. Jika kode cocok, status pemetaan akan otomatis berubah menjadi "Terhubung".</span>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-surface-container-high">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs border border-outline-variant/40">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-on-primary font-headline font-bold text-xs shadow-md">
                        Simpan Provinsi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. MODAL EDIT PROVINSI -->
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full border border-surface-container-high shadow-2xl p-6 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px] text-secondary">edit_square</span>
                    <div>
                        <h3 class="font-headline font-bold text-base text-on-surface">Edit Data Provinsi</h3>
                        <p class="text-[11px] text-on-surface-variant">Perbarui identitas dan status operasional wilayah</p>
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
                    <!-- Kode Provinsi -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider" for="editCode">
                                Kode Provinsi <span class="text-red-500">*</span>
                            </label>
                            <span id="codeLockedBadge" class="hidden text-[10px] text-amber-700 font-bold bg-amber-50 px-1.5 py-0.2 rounded border border-amber-300">
                                Terkunci
                            </span>
                        </div>
                        <input 
                            type="text" 
                            name="code" 
                            id="editCode" 
                            required 
                            maxlength="10"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-mono"
                        >
                        <span id="editCodeHelper" class="text-[10px] text-outline mt-1 block">Kode identitas wilayah provinsi</span>
                    </div>

                    <!-- Status Provinsi -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editStatus">
                            Status Operasional <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="editStatus" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary font-semibold" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                        <span class="text-[10px] text-outline mt-1 block">Nonaktifkan untuk membatasi pembaruan baru</span>
                    </div>
                </div>

                <!-- Nama Provinsi -->
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editName">
                        Nama Provinsi <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="editName" 
                        required 
                        maxlength="100"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary"
                    >
                </div>

                <!-- Kelompok Pulau / Wilayah -->
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editIslandGroup">
                        Wilayah / Kelompok Pulau
                    </label>
                    <select name="island_group" id="editIslandGroup" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                        <option value="">-- Pilih Wilayah Pulau --</option>
                        @foreach ($islandGroups as $island)
                            <option value="{{ $island }}">{{ $island }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="codeLockNotice" class="hidden p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-700 shrink-0 mt-0.5">lock</span>
                    <div>
                        <span class="font-semibold block">Integritas Relasi Historis Terjaga:</span>
                        <span class="text-[11px] text-amber-800">Kode wilayah tidak dapat diubah karena telah digunakan sebagai kunci relasi statistik pernikahan dan perceraian tahun 2023–2025. Mengubah nama atau status tidak akan mengubah agregat data historis.</span>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-surface-container-high">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs border border-outline-variant/40">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-secondary hover:bg-primary text-on-secondary font-headline font-bold text-xs shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. MODAL DETAIL PROVINSI (Lengkap dengan data relasi & GeoJSON) -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full border border-surface-container-high shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[24px] text-primary">analytics</span>
                    <div>
                        <h3 class="font-headline font-bold text-base text-on-surface" id="detailModalTitle">Detail Informasi Provinsi</h3>
                        <p class="text-[11px] text-on-surface-variant">Ringkasan master wilayah & keterhubungan data statistik</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-outline hover:text-on-surface p-1 rounded-lg">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div id="detailLoading" class="py-8 text-center text-on-surface-variant">
                <div class="flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[32px] text-primary animate-spin">progress_activity</span>
                    <span class="text-xs font-medium">Memuat data rincian provinsi...</span>
                </div>
            </div>

            <div id="detailContent" class="space-y-4 hidden">
                <!-- Info Identitas Utama -->
                <div class="p-4 rounded-xl bg-surface-container-low border border-surface-container-high flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-outline block">Nama Provinsi</span>
                            <h4 class="font-headline text-lg font-bold text-on-surface" id="detailName">-</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-outline block">Kode Wilayah</span>
                            <span class="font-mono text-base font-bold text-primary px-2.5 py-0.5 rounded-lg bg-surface-container-lowest border border-outline-variant/30" id="detailCode">-</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-2 border-t border-outline-variant/20 flex-wrap">
                        <span class="text-xs text-on-surface-variant">Wilayah: <strong id="detailIsland">-</strong></span>
                        <span class="text-outline">•</span>
                        <span id="detailStatusBadge" class="text-xs font-semibold px-2 py-0.5 rounded-full">-</span>
                    </div>
                </div>

                <!-- Status Pemetaan GeoJSON -->
                <div class="p-4 rounded-xl border border-surface-container-high bg-surface-container-lowest">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-headline font-bold text-xs uppercase tracking-wider text-on-surface flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-primary">map</span>
                            <span>Integrasi Peta GeoJSON</span>
                        </span>
                        <span id="detailMappingBadge" class="text-xs font-semibold px-2.5 py-0.5 rounded-full">-</span>
                    </div>
                    <div class="space-y-1.5 text-xs text-on-surface-variant">
                        <div class="flex items-center justify-between">
                            <span>Metode Pencocokan:</span>
                            <span class="font-semibold text-on-surface" id="detailMappingMethod">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Keterangan Peta:</span>
                            <span class="text-[11px] text-on-surface" id="detailMappingNote">-</span>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Statistik Relasi Pernikahan -->
                <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-headline font-bold text-xs uppercase tracking-wider text-blue-900 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-blue-700">volunteer_activism</span>
                            <span>Statistik Pernikahan (Kemenag & BPS)</span>
                        </span>
                        <span class="text-[10px] font-mono text-blue-700 bg-blue-100 px-2 py-0.5 rounded">2023–2025</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-white/80 p-2.5 rounded-lg border border-blue-100">
                            <span class="text-[10px] text-outline block">Total Akta Terdaftar:</span>
                            <span class="font-headline font-bold text-sm text-primary" id="detailNikahTotal">-</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-lg border border-blue-100">
                            <span class="text-[10px] text-outline block">Jumlah Baris Observasi:</span>
                            <span class="font-headline font-bold text-sm text-on-surface" id="detailNikahCount">-</span>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Statistik Relasi Perceraian -->
                <div class="p-4 rounded-xl border border-red-200 bg-red-50/50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-headline font-bold text-xs uppercase tracking-wider text-red-900 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-red-700">heart_broken</span>
                            <span>Statistik Perceraian & Faktor (BPS)</span>
                        </span>
                        <span class="text-[10px] font-mono text-red-700 bg-red-100 px-2 py-0.5 rounded">BPS 2023–2025</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-white/80 p-2.5 rounded-lg border border-red-100">
                            <span class="text-[10px] text-outline block">Total Kasus Perkara:</span>
                            <span class="font-headline font-bold text-sm text-red-700" id="detailPerceraianTotal">-</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-lg border border-red-100">
                            <span class="text-[10px] text-outline block">Faktor Dominan:</span>
                            <span class="font-headline font-bold text-xs text-on-surface truncate block" id="detailTopFaktor">-</span>
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="pt-2 border-t border-outline-variant/30 flex items-center justify-between text-[11px] text-outline">
                    <span>Dibuat: <strong class="font-mono text-on-surface-variant" id="detailCreatedAt">-</strong></span>
                    <span>Diperbarui: <strong class="font-mono text-on-surface-variant" id="detailUpdatedAt">-</strong></span>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-surface-container-high mt-4">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs border border-outline-variant/40">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- 4. MODAL TOGGLE STATUS (NONAKTIFKAN / AKTIFKAN) -->
    <div id="toggleModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-sm w-full border border-surface-container-high shadow-2xl p-6 text-center animate-in fade-in zoom-in-95 duration-150">
            <div id="toggleIconContainer" class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[24px]">power_settings_new</span>
            </div>
            <h3 class="font-headline font-bold text-base text-on-surface mb-1" id="toggleModalTitle">
                Ubah Status Provinsi
            </h3>
            <p class="text-xs text-on-surface-variant mb-4" id="toggleConfirmText">
                Apakah Anda yakin ingin mengubah status wilayah ini?
            </p>

            <form id="toggleForm" method="POST" class="flex items-center justify-center gap-2">
                @csrf
                @method('PATCH')
                <button type="button" onclick="closeToggleModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs border border-outline-variant/40">
                    Batal
                </button>
                <button type="submit" id="toggleSubmitBtn" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-headline font-bold text-xs shadow-md">
                    Ya, Ubah Status
                </button>
            </form>
        </div>
    </div>

    <!-- 5. MODAL HAPUS PROVINSI (DENGAN PROTEKSI DATA RELASI) -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full border border-surface-container-high shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-150">
            <div class="text-center mb-4">
                <div id="deleteIconBox" class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-[26px]">warning</span>
                </div>
                <h3 class="font-headline font-bold text-base text-on-surface mb-1">
                    Konfirmasi Tindakan Wilayah
                </h3>
                <p class="text-xs text-on-surface-variant" id="deleteProvName">
                    Provinsi yang dipilih
                </p>
            </div>

            <!-- Pesan jika memiliki relasi data (Penghapusan Diblokir) -->
            <div id="deleteBlockedNotice" class="hidden space-y-3 mb-4">
                <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-900 text-xs text-left">
                    <div class="flex items-center gap-1.5 font-bold mb-1 text-red-700">
                        <span class="material-symbols-outlined text-[16px]">block</span>
                        <span>Penghapusan Permanen Ditolak</span>
                    </div>
                    <p class="leading-relaxed" id="deleteBlockedReason">
                        Provinsi ini masih terhubung dengan data statistik pernikahan & perceraian historis. Menghapus wilayah ini akan merusak relasi dan grafik statistik 2023–2025.
                    </p>
                </div>
                <p class="text-xs text-on-surface-variant text-center font-medium">
                    Sistem merekomendasikan untuk menonaktifkan status wilayah ini saja:
                </p>
            </div>

            <!-- Pesan jika tidak memiliki relasi (Aman dihapus) -->
            <div id="deleteAllowedNotice" class="hidden mb-4">
                <p class="text-xs text-on-surface-variant text-center leading-relaxed">
                    Provinsi ini belum memiliki catatan statistik historis. Apakah Anda yakin ingin menghapus data master provinsi ini secara permanen? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="flex items-center justify-center gap-2 pt-2 border-t border-surface-container-high">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs border border-outline-variant/40">
                    Batal
                </button>

                <!-- Tombol aksi hapus jika diizinkan -->
                <form id="deleteForm" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="deleteConfirmBtn" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-headline font-bold text-xs shadow-md">
                        Hapus Permanen
                    </button>
                </form>

                <!-- Tombol alternatif nonaktifkan jika diblokir -->
                <button type="button" id="deleteSwitchToDeactivateBtn" onclick="switchToDeactivate()" class="hidden px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-headline font-bold text-xs shadow-md">
                    Nonaktifkan Wilayah Saja
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC UNTUK INTERAKSI MODAL & DATA -->
    <script>
        // Modal Tambah
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Modal Edit
        function openEditModal(id, code, name, islandGroup, status, hasRelations) {
            const form = document.getElementById('editForm');
            form.action = '/admin/provinsi/' + id;

            const codeInput = document.getElementById('editCode');
            codeInput.value = code;

            const codeLockedBadge = document.getElementById('codeLockedBadge');
            const codeLockNotice = document.getElementById('codeLockNotice');
            const editCodeHelper = document.getElementById('editCodeHelper');

            if (hasRelations) {
                codeInput.setAttribute('readonly', 'readonly');
                codeInput.classList.add('bg-slate-100', 'cursor-not-allowed', 'text-slate-600');
                codeLockedBadge.classList.remove('hidden');
                codeLockNotice.classList.remove('hidden');
                editCodeHelper.innerText = 'Kode terkunci untuk menjaga konsistensi relasi historis';
            } else {
                codeInput.removeAttribute('readonly');
                codeInput.classList.remove('bg-slate-100', 'cursor-not-allowed', 'text-slate-600');
                codeLockedBadge.classList.add('hidden');
                codeLockNotice.classList.add('hidden');
                editCodeHelper.innerText = 'Kode dapat diubah karena belum ada relasi statistik';
            }

            document.getElementById('editName').value = name;
            document.getElementById('editIslandGroup').value = islandGroup;
            document.getElementById('editStatus').value = (status === 'Terverifikasi' || status === 'Aktif') ? 'Aktif' : 'Nonaktif';

            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Modal Detail (Fetch data via AJAX API endpoint)
        let activeDetailProvinceId = null;
        function openDetailModal(id) {
            activeDetailProvinceId = id;
            document.getElementById('detailLoading').classList.remove('hidden');
            document.getElementById('detailContent').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('hidden');

            fetch('/admin/provinsi/api/' + id + '/detail')
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        const d = res.data;
                        document.getElementById('detailName').innerText = d.name;
                        document.getElementById('detailCode').innerText = d.code;
                        document.getElementById('detailIsland').innerText = d.island_group || '-';

                        const isAktif = (d.status === 'Aktif' || d.status === 'Terverifikasi');
                        const statusBadge = document.getElementById('detailStatusBadge');
                        statusBadge.innerText = isAktif ? 'Aktif' : 'Nonaktif';
                        statusBadge.className = isAktif 
                            ? 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200' 
                            : 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-300';

                        // GeoJSON Mapping Info
                        const mapBadge = document.getElementById('detailMappingBadge');
                        if (d.geo_mapping.is_mapped) {
                            mapBadge.innerText = 'Terhubung';
                            mapBadge.className = 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-800 border border-teal-200';
                            document.getElementById('detailMappingMethod').innerText = d.geo_mapping.method || 'Kode Wilayah';
                        } else {
                            mapBadge.innerText = 'Belum Terhubung';
                            mapBadge.className = 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-300';
                            document.getElementById('detailMappingMethod').innerText = 'Tidak Ditemukan di GeoJSON';
                        }
                        document.getElementById('detailMappingNote').innerText = d.geo_mapping.note || '-';

                        // Statistik Nikah
                        document.getElementById('detailNikahTotal').innerText = d.nikah.total_formatted + ' Akta';
                        document.getElementById('detailNikahCount').innerText = d.nikah.count + ' Baris Record';

                        // Statistik Perceraian
                        document.getElementById('detailPerceraianTotal').innerText = d.perceraian.total_formatted + ' Kasus';
                        document.getElementById('detailTopFaktor').innerText = d.perceraian.top_faktor || '-';

                        // Timestamps
                        document.getElementById('detailCreatedAt').innerText = d.created_at;
                        document.getElementById('detailUpdatedAt').innerText = d.updated_at;

                        document.getElementById('detailLoading').classList.add('hidden');
                        document.getElementById('detailContent').classList.remove('hidden');
                    }
                })
                .catch(err => {
                    alert('Gagal memuat detail provinsi.');
                    closeDetailModal();
                });
        }
        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Modal Toggle Status (Nonaktifkan / Aktifkan)
        function openToggleModal(id, name, currentStatus) {
            const form = document.getElementById('toggleForm');
            form.action = '/admin/provinsi/' + id + '/toggle-status';

            const willDeactivate = (currentStatus === 'Aktif' || currentStatus === 'Terverifikasi');
            const titleEl = document.getElementById('toggleModalTitle');
            const textEl = document.getElementById('toggleConfirmText');
            const submitBtn = document.getElementById('toggleSubmitBtn');
            const iconBox = document.getElementById('toggleIconContainer');

            if (willDeactivate) {
                titleEl.innerText = 'Nonaktifkan Provinsi';
                textEl.innerText = `Apakah Anda yakin ingin menonaktifkan wilayah "${name}"? Data historis pernikahan & perceraian 2023–2025 tetap aman tersimpan di database.`;
                submitBtn.innerText = 'Ya, Nonaktifkan';
                submitBtn.className = 'px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-headline font-bold text-xs shadow-md';
                iconBox.className = 'w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3';
            } else {
                titleEl.innerText = 'Aktifkan Kembali Provinsi';
                textEl.innerText = `Aktifkan kembali wilayah "${name}" untuk operasional pengelolaan data?`;
                submitBtn.innerText = 'Ya, Aktifkan';
                submitBtn.className = 'px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-headline font-bold text-xs shadow-md';
                iconBox.className = 'w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-3';
            }

            document.getElementById('toggleModal').classList.remove('hidden');
        }
        function closeToggleModal() {
            document.getElementById('toggleModal').classList.add('hidden');
        }

        // Modal Hapus Data (Proteksi Relasi Historis)
        let pendingDeactivateProvId = null;
        let pendingDeactivateProvName = null;
        function openDeleteModal(id, name, code, hasRelations, nikahCount, perceraianCount) {
            const form = document.getElementById('deleteForm');
            form.action = '/admin/provinsi/' + id;

            document.getElementById('deleteProvName').innerText = `Wilayah: ${name} (Kode ${code})`;

            const blockedNotice = document.getElementById('deleteBlockedNotice');
            const allowedNotice = document.getElementById('deleteAllowedNotice');
            const confirmBtn = document.getElementById('deleteConfirmBtn');
            const switchBtn = document.getElementById('deleteSwitchToDeactivateBtn');
            const blockedReason = document.getElementById('deleteBlockedReason');

            if (hasRelations) {
                pendingDeactivateProvId = id;
                pendingDeactivateProvName = name;
                blockedNotice.classList.remove('hidden');
                allowedNotice.classList.add('hidden');
                confirmBtn.classList.add('hidden');
                switchBtn.classList.remove('hidden');
                blockedReason.innerText = `Provinsi "${name}" (Kode ${code}) masih digunakan oleh ${nikahCount} catatan pernikahan dan ${perceraianCount} data kasus perceraian historis (2023–2025). Penghapusan permanen diblokir demi menjaga integritas data dan visualisasi peta.`;
            } else {
                blockedNotice.classList.add('hidden');
                allowedNotice.classList.remove('hidden');
                confirmBtn.classList.remove('hidden');
                switchBtn.classList.add('hidden');
            }

            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
        function switchToDeactivate() {
            closeDeleteModal();
            if (pendingDeactivateProvId) {
                openToggleModal(pendingDeactivateProvId, pendingDeactivateProvName, 'Aktif');
            }
        }

        // Tutup modal jika tombol ESC ditekan
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDetailModal();
                closeToggleModal();
                closeDeleteModal();
            }
        });
    </script>
</x-layouts.app>
