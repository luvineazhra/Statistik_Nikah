@props([
    'daftarProvinsi' => [],
    'daftarTahun' => [2025, 2024, 2023],
    'selectedProvinsi' => '',
    'selectedTahun' => '',
])

<form method="GET" action="{{ route('dashboard') }}" class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-surface-container-high/60 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
    <div class="flex flex-wrap items-center gap-3">
        <!-- Year Dropdown Filter -->
        <div class="relative min-w-[150px]">
            <select name="tahun" onchange="this.form.submit()" class="w-full h-9 pl-3 pr-8 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant/50 focus:outline-none focus:ring-1 focus:ring-secondary appearance-none cursor-pointer">
                <option value="">Semua Tahun (2023–2025)</option>
                @foreach($daftarTahun as $thn)
                    <option value="{{ $thn }}" {{ (string)$selectedTahun === (string)$thn ? 'selected' : '' }}>
                        Tahun {{ $thn }}
                    </option>
                @endforeach
            </select>
            <span class="material-symbols-outlined pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-outline text-[16px]">expand_more</span>
        </div>

        <!-- Province Dropdown Filter -->
        <div class="relative min-w-[240px]">
            <select name="provinsi" onchange="this.form.submit()" class="w-full h-9 pl-3 pr-8 rounded-lg bg-surface-container-low text-on-surface text-xs border border-outline-variant/50 focus:outline-none focus:ring-1 focus:ring-secondary appearance-none cursor-pointer">
                <option value="">Semua Provinsi ({{ count($daftarProvinsi) }})</option>
                @foreach($daftarProvinsi as $prov)
                    <option value="{{ $prov }}" {{ $selectedProvinsi === $prov ? 'selected' : '' }}>
                        {{ $prov }}
                    </option>
                @endforeach
            </select>
            <span class="material-symbols-outlined pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-outline text-[16px]">expand_more</span>
        </div>

        <!-- Indicator of active filter -->
        @if($selectedProvinsi || $selectedTahun)
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary-container/20 text-primary text-xs font-medium border border-primary/20">
                <span class="material-symbols-outlined text-[14px]">filter_alt</span>
                <span>Terfilter: {{ $selectedProvinsi ?: 'Semua Provinsi' }} {{ $selectedTahun ? "($selectedTahun)" : '' }}</span>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-2">
        <button type="button" onclick="openModal('addRecordModal')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary-container text-on-primary hover:bg-primary font-medium text-xs shadow-sm transition-colors">
            <span class="material-symbols-outlined text-[16px]">add</span>
            <span>Tambah Data Baru</span>
        </button>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant font-medium text-xs border border-outline-variant/50 transition-colors">
            <span class="material-symbols-outlined text-[16px]">refresh</span>
            <span>Reset Filter</span>
        </a>
    </div>
</form>
