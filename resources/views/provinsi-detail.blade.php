<x-layouts.app title="Detail Statistik Pernikahan - {{ $matchedProvinsi }} - NikahInfo">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-xs flex-wrap">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Dashboard</span>
                </a>
                <span class="text-xs text-outline">•</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-fixed text-on-primary-fixed">
                    Kode Wilayah: {{ $matchedKode }}
                </span>
            </div>
            <h1 class="font-headline text-2xl md:text-3xl font-bold text-on-surface tracking-tight mt-1">
                Statistik Pernikahan {{ $matchedProvinsi }}
            </h1>
            <p class="text-xs md:text-sm text-on-surface-variant">
                Laporan detail observasi dan tren pernikahan periode 2023–2025
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard', ['provinsi' => $matchedProvinsi]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-container text-on-primary shadow-sm hover:bg-primary transition-colors text-xs font-medium">
                <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                <span>Filter di Dashboard</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-kpi-card 
            title="Total Pernikahan (2023-2025)"
            subtitle="Akumulasi 3 Tahun"
            value="{{ number_format($totalPernikahan, 0, ',', '.') }}"
            unit="Akta"
            icon="groups"
            iconBg="bg-primary-fixed text-primary"
            badgeText="Akumulasi Data"
            badgeClass="bg-tertiary-fixed text-on-tertiary-fixed-variant"
            badgeIcon="verified"
        />

        <x-kpi-card 
            title="Pertumbuhan YoY (2023-2025)"
            subtitle="Perbandingan 2023 vs 2025"
            value="{{ ($growthYoY >= 0 ? '+' : '') . $growthYoY }}%"
            unit=""
            icon="trending_up"
            iconBg="bg-secondary-fixed text-secondary"
            badgeText="{{ $growthYoY >= 0 ? 'Tren Positif' : 'Tren Menurun' }}"
            badgeClass="{{ $growthYoY >= 0 ? 'bg-tertiary-fixed text-on-tertiary-fixed-variant' : 'bg-red-100 text-red-700' }}"
            badgeIcon="{{ $growthYoY >= 0 ? 'arrow_upward' : 'arrow_downward' }}"
        />

        <x-kpi-card 
            title="Bulan Puncak (2025)"
            subtitle="Volume Tertinggi"
            value="{{ $bulanTertinggi }}"
            unit=""
            icon="event_available"
            iconBg="bg-surface-container text-tertiary-container"
            badgeText="Puncak Musiman"
            badgeClass="bg-surface-container-high text-on-surface-variant border border-outline-variant/50"
            badgeIcon="grade"
        />

        <x-kpi-card 
            title="Bulan Terendah (2025)"
            subtitle="Volume Terendah"
            value="{{ $bulanTerendah }}"
            unit=""
            icon="event_busy"
            iconBg="bg-surface-container text-outline"
            badgeText="Terendah"
            badgeClass="bg-surface-container-high text-on-surface-variant border border-outline-variant/50"
            badgeIcon="info"
        />
    </div>

    <!-- Monthly Trend Chart -->
    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-surface-container-high/60 flex flex-col gap-space-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm pb-space-xs border-b border-surface-container-high/60">
            <div>
                <h2 class="font-headline text-lg font-bold text-on-surface">Tren Pernikahan Bulanan Tahun 2025</h2>
                <p class="text-xs text-on-surface-variant">Distribusi bulanan pernikahan di {{ $matchedProvinsi }}</p>
            </div>
            <span class="px-3 py-1 rounded-lg bg-surface-container-low text-on-surface-variant text-xs font-mono font-semibold border border-outline-variant/40">
                Total 2025: {{ number_format($total2025, 0, ',', '.') }} Akta
            </span>
        </div>

        @php
            $maxTrendVal = !empty($monthlyTrend) ? max(array_values($monthlyTrend)) : 1;
            if ($maxTrendVal <= 0) $maxTrendVal = 1;
        @endphp

        <div class="h-64 flex items-end justify-between gap-2 px-2 pt-4 border-b border-outline-variant/40">
            @foreach($monthlyTrend as $bln => $val)
                @php $hPct = round(($val / $maxTrendVal) * 100); @endphp
                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group relative">
                    <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-on-surface text-surface text-[10px] py-1 px-2 rounded font-mono pointer-events-none whitespace-nowrap z-10 shadow-md">
                        {{ $bln }}: {{ number_format($val, 0, ',', '.') }}
                    </div>
                    <div class="w-full max-w-[40px] bg-primary/20 hover:bg-primary transition-all rounded-t-md relative flex flex-col justify-end overflow-hidden" style="height: {{ max($hPct, 6) }}%;">
                        <div class="w-full bg-primary" style="height: 100%;"></div>
                    </div>
                    <span class="text-[10px] sm:text-xs text-on-surface-variant font-medium group-hover:text-primary transition-colors truncate">
                        {{ substr($bln, 0, 3) }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Annual Comparison & 2024 Special Rule Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 2023 -->
        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-surface-container-high/60 flex flex-col justify-between gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Tahun 2023</span>
                <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface text-[10px] font-bold">Lengkap 12 Bulan</span>
            </div>
            <div class="text-2xl font-bold font-mono text-primary">
                {{ number_format($total2023, 0, ',', '.') }} <span class="text-xs font-normal text-on-surface-variant">Akta</span>
            </div>
            <p class="text-[11px] text-outline">Data registri pernikahan lengkap Januari–Desember 2023</p>
        </div>

        <!-- Card 2024 -->
        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-secondary/30 flex flex-col justify-between gap-3 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-secondary uppercase tracking-wider">Tahun 2024</span>
                <span class="px-2 py-0.5 rounded bg-secondary-fixed text-on-secondary-fixed text-[10px] font-bold">Jan – Sept</span>
            </div>
            <div class="text-2xl font-bold font-mono text-secondary">
                {{ number_format($total2024, 0, ',', '.') }} <span class="text-xs font-normal text-on-surface-variant">Akta</span>
            </div>
            <div class="p-2 rounded bg-amber-50 text-amber-800 border border-amber-200 text-[11px] flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px]">warning</span>
                <span>Data 2024 tersedia sampai September. Oktober–Desember: <strong>Tidak tersedia</strong>.</span>
            </div>
        </div>

        <!-- Card 2025 -->
        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-surface-container-high/60 flex flex-col justify-between gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Tahun 2025</span>
                <span class="px-2 py-0.5 rounded bg-tertiary-fixed text-on-tertiary-fixed-variant text-[10px] font-bold">Lengkap 12 Bulan</span>
            </div>
            <div class="text-2xl font-bold font-mono text-primary">
                {{ number_format($total2025, 0, ',', '.') }} <span class="text-xs font-normal text-on-surface-variant">Akta</span>
            </div>
            <p class="text-[11px] text-outline">Data konsolidasi terbaru 2025</p>
        </div>
    </div>

    <!-- Faktor Penyebab Perceraian BPS di Provinsi Ini -->
    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-surface-container-high/60 flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-surface-container-high/60">
            <div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[22px] text-red-600">heart_broken</span>
                    <h3 class="font-headline text-base font-bold text-on-surface">Faktor Penyebab Perceraian (Data BPS {{ $matchedProvinsi }})</h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">Tahun 2025</span>
                </div>
                <p class="text-xs text-on-surface-variant">Rincian perkara perceraian dan faktor pemicu di {{ $matchedProvinsi }}</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] text-outline block">Total Perkara Perceraian:</span>
                <span class="font-mono font-bold text-base text-red-700">{{ number_format($totalPerceraianProvinsi, 0, ',', '.') }} Perkara</span>
            </div>
        </div>

        @if($topFaktorProvinsi)
            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-600 text-[18px]">workspace_premium</span>
                    <span>Faktor pemicu tertinggi #1: <strong>{{ $topFaktorProvinsi->faktor_penyebab }}</strong></span>
                </div>
                <span class="font-mono font-bold text-amber-900">{{ number_format($topFaktorProvinsi->jumlah_kasus, 0, ',', '.') }} Kasus</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @php
                $detailColors = ['#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6', '#F97316', '#EC4899', '#6366F1', '#14B8A6', '#64748B'];
            @endphp
            @forelse($faktorPerceraianProvinsi as $idx => $fItem)
                @php
                    $fPct = $totalPerceraianProvinsi > 0 ? round(($fItem->jumlah_kasus / $totalPerceraianProvinsi) * 100, 1) : 0;
                    $color = $detailColors[$idx % count($detailColors)];
                @endphp
                <div class="p-3 rounded-xl bg-surface-container-low/60 border border-outline-variant/30 flex flex-col gap-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <span class="w-5 h-5 rounded-md font-mono font-bold text-[10px] flex items-center justify-center shrink-0" style="background-color: {{ $color }}20; color: {{ $color }};">
                                {{ $idx + 1 }}
                            </span>
                            <span class="font-semibold text-on-surface truncate">{{ $fItem->faktor_penyebab }}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="font-mono font-bold text-on-surface text-[11px]">{{ number_format($fItem->jumlah_kasus, 0, ',', '.') }} Kasus</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold" style="background-color: {{ $color }}20; color: {{ $color }};">
                                {{ $fPct }}%
                            </span>
                        </div>
                    </div>
                    <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="width: {{ max($fPct, 2) }}%; background-color: {{ $color }};"></div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-4 text-xs text-outline">Data faktor perceraian belum tersedia.</div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
