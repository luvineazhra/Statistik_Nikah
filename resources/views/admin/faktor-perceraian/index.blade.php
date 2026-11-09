<x-layouts.app title="Kelola Data Perceraian & Faktor • NikahInfo">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md mb-6">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                    Statistik Perkara BPS & Badilag MA
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">
                    <span class="material-symbols-outlined text-[14px] text-tertiary-container">analytics</span>
                    Faktor Penyebab Perceraian 2023–2025
                </span>
            </div>
            <h1 class="font-headline text-2xl md:text-3xl font-bold text-on-surface tracking-tight mt-1">
                Kelola Data Perceraian & Faktor Penyebab
            </h1>
            <p class="text-xs md:text-sm text-on-surface-variant">
                Manajemen data perkara perceraian dan klasifikasi faktor pemicu (Perselisihan, Ekonomi, KDRT, Judi Online, dll.) di 38 Provinsi Indonesia
            </p>
        </div>

        <div class="flex items-center gap-space-sm self-start lg:self-center shrink-0">
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-on-primary shadow-md shadow-blue-900/15 transition-all text-xs font-bold cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Tambah Data Perkara</span>
            </button>
        </div>
    </div>

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

    <!-- 4 KPI Summary Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-kpi-card 
            title="Total Perkara Perceraian"
            subtitle="{{ $selectedProvinsi ? 'Provinsi: ' . $selectedProvinsi : 'Agregat Nasional Terfilter' }}"
            value="{{ number_format($totalKasus, 0, ',', '.') }}"
            unit="Perkara"
            icon="heart_broken"
            iconBg="bg-red-100 text-red-700"
            badgeText="{{ $selectedProvinsi ? 'Filter Aktif' : 'Nasional' }}"
            badgeClass="bg-tertiary-fixed text-on-tertiary-fixed-variant"
            badgeIcon="verified"
        />

        <x-kpi-card 
            title="Jumlah Data Baris"
            subtitle="Total Record Dataset BPS"
            value="{{ number_format($totalRecords, 0, ',', '.') }}"
            unit="Baris"
            icon="table_rows"
            iconBg="bg-surface-container text-secondary"
            badgeText="Tahun 2023 - 2025"
            badgeClass="bg-secondary-fixed text-on-secondary-fixed"
            badgeIcon="calendar_today"
        />

        <x-kpi-card 
            title="Faktor Dominan (#1)"
            subtitle="{{ Str::limit($faktorTerbanyakNama, 28) }}"
            value="{{ number_format($faktorTerbanyakTotal, 0, ',', '.') }}"
            unit="Kasus"
            icon="trending_up"
            iconBg="bg-amber-100 text-amber-800"
            badgeText="Pemicu Tertinggi"
            badgeClass="bg-amber-100 text-amber-800"
            badgeIcon="warning"
        />

        <x-kpi-card 
            title="Provinsi Tertinggi"
            subtitle="Wilayah Terbanyak Kasus"
            value="{{ $provinsiTertinggi }}"
            unit=""
            icon="location_city"
            iconBg="bg-surface-container text-tertiary-container"
            badgeText="Data BPS RI"
            badgeClass="bg-surface-container-high text-on-surface-variant border border-outline-variant/50"
            badgeIcon="info"
        />
    </div>

    <!-- Visual Divorce Factors Distribution Pills -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-5 mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-headline font-bold text-xs uppercase tracking-wider text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-primary">pie_chart</span>
                <span>Rekapitulasi Faktor Penyebab Perceraian Terbanyak (BPS 2023–2025)</span>
            </h3>
            <span class="text-[11px] text-outline">Sumber: Sensus & Peradilan Agama</span>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @php
                $factorStyles = [
                    'Perselisihan dan Pertengkaran Terus Menerus' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-900', 'icon' => 'forum'],
                    'Ekonomi' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-900', 'icon' => 'payments'],
                    'Meninggalkan Salah Satu Pihak' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-900', 'icon' => 'person_off'],
                    'Kekerasan Dalam Rumah Tangga (KDRT)' => ['bg' => 'bg-red-50 border-red-200 text-red-900', 'icon' => 'personal_injury'],
                    'Judi' => ['bg' => 'bg-purple-50 border-purple-200 text-purple-900', 'icon' => 'casino'],
                    'Mabuk' => ['bg' => 'bg-orange-50 border-orange-200 text-orange-900', 'icon' => 'liquor'],
                    'Madat' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-900', 'icon' => 'medication'],
                    'Zina' => ['bg' => 'bg-pink-50 border-pink-200 text-pink-900', 'icon' => 'heart_broken'],
                    'Poligami' => ['bg' => 'bg-indigo-50 border-indigo-200 text-indigo-900', 'icon' => 'diversity_1'],
                    'Dihukum Penjara' => ['bg' => 'bg-slate-100 border-slate-300 text-slate-800', 'icon' => 'lock'],
                    'Cacat Badan' => ['bg' => 'bg-teal-50 border-teal-200 text-teal-900', 'icon' => 'accessible'],
                    'Kawin Paksa' => ['bg' => 'bg-yellow-50 border-yellow-200 text-yellow-900', 'icon' => 'link_off'],
                    'Murtad' => ['bg' => 'bg-cyan-50 border-cyan-200 text-cyan-900', 'icon' => 'change_circle'],
                    'Lain-lain' => ['bg' => 'bg-gray-100 border-gray-300 text-gray-800', 'icon' => 'more_horiz'],
                ];
            @endphp

            @foreach ($rekapPerFaktor as $faktorName => $subTotal)
                @php
                    $style = $factorStyles[$faktorName] ?? ['bg' => 'bg-slate-50 border-slate-200 text-slate-900', 'icon' => 'category'];
                    $pct = $totalKasus > 0 ? round(($subTotal / $totalKasus) * 100, 1) : 0;
                @endphp
                <div class="p-3 rounded-xl border {{ $style['bg'] }} flex flex-col justify-between transition hover:shadow-sm">
                    <div class="flex items-center justify-between mb-1 gap-1">
                        <span class="text-xs font-bold font-headline truncate" title="{{ $faktorName }}">{{ $faktorName }}</span>
                        <span class="material-symbols-outlined text-[16px] opacity-75 shrink-0">{{ $style['icon'] }}</span>
                    </div>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="font-headline font-bold text-base leading-tight">{{ number_format($subTotal, 0, ',', '.') }}</span>
                        <span class="text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded bg-black/5">{{ $pct }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Filter Bar Component -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('faktor-perceraian') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Filter Provinsi -->
            <div>
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterProvinsi">
                    Filter Provinsi
                </label>
                <select name="provinsi" id="filterProvinsi" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                    <option value="">-- Semua Provinsi --</option>
                    @foreach ($listProvinsiNama as $prov)
                        <option value="{{ $prov }}" {{ $selectedProvinsi === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tahun -->
            <div>
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterTahun">
                    Filter Tahun
                </label>
                <select name="tahun" id="filterTahun" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                    <option value="">-- Semua Tahun --</option>
                    @foreach ($daftarTahun as $th)
                        <option value="{{ $th }}" {{ (string)$selectedTahun === (string)$th ? 'selected' : '' }}>Tahun {{ $th }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Faktor Penyebab -->
            <div>
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1" for="filterFaktor">
                    Faktor Penyebab
                </label>
                <select name="faktor_penyebab" id="filterFaktor" class="w-full px-3 py-2 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary">
                    <option value="">-- Semua Faktor --</option>
                    @foreach ($daftarFaktor as $fk)
                        <option value="{{ $fk }}" {{ $selectedFaktor === $fk ? 'selected' : '' }}>{{ $fk }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-primary-container hover:bg-primary text-on-primary font-headline font-bold text-xs flex items-center justify-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Filter</span>
                </button>
                <a href="{{ route('faktor-perceraian') }}" class="py-2 px-3 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs flex items-center justify-center gap-1 border border-outline-variant/40 transition" title="Reset Filter">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-surface-container-high flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-primary">table_chart</span>
                <h2 class="font-headline font-bold text-sm text-on-surface">
                    Daftar Faktor Penyebab Perceraian BPS ({{ $faktorList->total() }} Data)
                </h2>
            </div>
            <span class="text-xs text-on-surface-variant">Halaman {{ $faktorList->currentPage() }} dari {{ $faktorList->lastPage() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-semibold uppercase tracking-wider border-b border-surface-container-high">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Provinsi</th>
                        <th class="py-3 px-4 text-center">Tahun</th>
                        <th class="py-3 px-4">Faktor Penyebab Perceraian</th>
                        <th class="py-3 px-4 text-right">Jumlah Perkara / Kasus</th>
                        <th class="py-3 px-4 text-center">Terakhir Diperbarui</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-high">
                    @forelse ($faktorList as $index => $item)
                        <tr class="hover:bg-surface-container-low/50 transition">
                            <td class="py-3.5 px-4 text-center font-mono text-on-surface-variant">
                                {{ $faktorList->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-on-surface">
                                {{ $item->provinsi }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-mono text-[11px] font-bold bg-surface-container-high text-on-surface">
                                    {{ $item->tahun }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $factorBadge = match ($item->faktor_penyebab) {
                                        'Perselisihan', 'Perselisihan dan Pertengkaran Terus Menerus' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'Ekonomi' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'Meninggalkan Salah Satu Pihak' => 'bg-blue-50 text-blue-800 border-blue-200',
                                        'KDRT', 'Kekerasan Dalam Rumah Tangga (KDRT)' => 'bg-red-50 text-red-800 border-red-200',
                                        'Judi', 'Judi (Termasuk Judi Online)' => 'bg-purple-50 text-purple-800 border-purple-200',
                                        'Mabuk', 'Mabuk / Minuman Keras' => 'bg-orange-50 text-orange-800 border-orange-200',
                                        'Madat', 'Madat / Narkoba' => 'bg-rose-50 text-rose-800 border-rose-200',
                                        'Zina', 'Zina / Perselingkuhan' => 'bg-pink-50 text-pink-800 border-pink-200',
                                        'Poligami' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                        'Dihukum Penjara' => 'bg-gray-100 text-gray-800 border-gray-300',
                                        'Cacat Badan' => 'bg-teal-50 text-teal-800 border-teal-200',
                                        'Kawin Paksa' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                                        'Murtad' => 'bg-violet-50 text-violet-800 border-violet-200',
                                        'Lain-lain' => 'bg-stone-100 text-stone-700 border-stone-200',
                                        default => 'bg-slate-100 text-slate-800 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold border inline-flex items-center gap-1.5 {{ $factorBadge }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $item->faktor_penyebab }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-headline font-bold text-sm text-primary">
                                {{ number_format($item->jumlah_kasus, 0, ',', '.') }} <span class="text-xs font-normal text-on-surface-variant">Kasus</span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-[11px] text-on-surface-variant font-mono">
                                {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button 
                                        type="button" 
                                        onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->provinsi) }}', {{ $item->tahun }}, '{{ addslashes($item->faktor_penyebab) }}', {{ $item->jumlah_kasus }})" 
                                        class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition" 
                                        title="Edit Data"
                                    >
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->provinsi) }}', '{{ addslashes($item->faktor_penyebab) }}', {{ $item->tahun }})" 
                                        class="p-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" 
                                        title="Hapus Data"
                                    >
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-on-surface-variant">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-[36px] text-outline">search_off</span>
                                    <span class="text-sm font-medium">Tidak ada data faktor perceraian yang sesuai dengan filter.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div class="px-5 py-4 border-t border-surface-container-high bg-surface-container-lowest">
            {{ $faktorList->links() }}
        </div>
    </div>

    <!-- Modal Tambah Data Faktor Perceraian -->
    <div id="addModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full border border-surface-container-high shadow-2xl p-6 relative">
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-primary">add_circle</span>
                    <h3 class="font-headline font-bold text-base text-on-surface">Tambah Data Faktor Perceraian</h3>
                </div>
                <button type="button" onclick="closeAddModal()" class="text-outline hover:text-on-surface">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form action="{{ route('faktor-perceraian.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addProvinsi">Provinsi</label>
                    <select name="provinsi" id="addProvinsi" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary" required>
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach ($listProvinsiNama as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addTahun">Tahun</label>
                        <select name="tahun" id="addTahun" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary" required>
                            @foreach ($daftarTahun as $th)
                                <option value="{{ $th }}">Tahun {{ $th }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addFaktor">Faktor Penyebab</label>
                        <select name="faktor_penyebab" id="addFaktor" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary" required>
                            @foreach ($daftarFaktor as $fk)
                                <option value="{{ $fk }}">{{ $fk }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="addJumlah">Jumlah Kasus / Perkara</label>
                    <input type="number" min="0" name="jumlah_kasus" id="addJumlah" placeholder="Contoh: 14500" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary" required />
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs border border-outline-variant/40">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-on-primary font-headline font-bold text-xs shadow-md">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Data Faktor Perceraian -->
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full border border-surface-container-high shadow-2xl p-6 relative">
            <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-secondary">edit_square</span>
                    <h3 class="font-headline font-bold text-base text-on-surface">Edit Data Faktor Perceraian</h3>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-outline hover:text-on-surface">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editProvinsi">Provinsi</label>
                    <select name="provinsi" id="editProvinsi" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary" required>
                        @foreach ($listProvinsiNama as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editTahun">Tahun</label>
                        <select name="tahun" id="editTahun" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary" required>
                            @foreach ($daftarTahun as $th)
                                <option value="{{ $th }}">Tahun {{ $th }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editFaktor">Faktor Penyebab</label>
                        <select name="faktor_penyebab" id="editFaktor" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary" required>
                            @foreach ($daftarFaktor as $fk)
                                <option value="{{ $fk }}">{{ $fk }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1" for="editJumlah">Jumlah Kasus / Perkara</label>
                    <input type="number" min="0" name="jumlah_kasus" id="editJumlah" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-xs focus:border-secondary focus:ring-1 focus:ring-secondary" required />
                </div>

                <div class="pt-3 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs border border-outline-variant/40">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-secondary hover:bg-primary text-on-secondary font-headline font-bold text-xs shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus Confirmation -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-2xl max-w-sm w-full border border-surface-container-high shadow-2xl p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[24px]">warning</span>
            </div>
            <h3 class="font-headline font-bold text-base text-on-surface mb-1">Konfirmasi Hapus Data</h3>
            <p class="text-xs text-on-surface-variant mb-4" id="deleteConfirmText">
                Apakah Anda yakin ingin menghapus data faktor perceraian ini?
            </p>

            <form id="deleteForm" method="POST" class="flex items-center justify-center gap-2">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline font-semibold text-xs border border-outline-variant/40">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-headline font-bold text-xs shadow-md">Ya, Hapus Data</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(id, provinsi, tahun, faktor, jumlah) {
            const form = document.getElementById('editForm');
            form.action = '/admin/faktor-perceraian/' + id;
            document.getElementById('editProvinsi').value = provinsi;
            document.getElementById('editTahun').value = tahun;
            document.getElementById('editFaktor').value = faktor;
            document.getElementById('editJumlah').value = jumlah;
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openDeleteModal(id, provinsi, faktor, tahun) {
            const form = document.getElementById('deleteForm');
            form.action = '/admin/faktor-perceraian/' + id;
            document.getElementById('deleteConfirmText').innerText = `Hapus record "${faktor}" - ${provinsi} (${tahun})?`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
