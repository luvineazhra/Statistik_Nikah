@props([
    'faktorPerceraianData' => collect(),
    'totalKasusPerceraian' => 0,
    'topFaktorPerceraian' => null,
    'selectedProvinsi' => '',
    'selectedTahun' => '',
])

@php
    $colors = [
        '#F59E0B', // Amber - Perselisihan
        '#10B981', // Emerald - Ekonomi
        '#3B82F6', // Blue - Meninggalkan Pihak
        '#EF4444', // Red - KDRT
        '#8B5CF6', // Purple - Judi
        '#F97316', // Orange - Miras
        '#EC4899', // Pink - Narkoba
        '#6366F1', // Indigo - Zina
        '#14B8A6', // Teal - Poligami
        '#64748B', // Slate - Penjara
    ];

    $chartLabels = [];
    $chartValues = [];
    $chartColors = [];

    foreach ($faktorPerceraianData as $idx => $item) {
        $chartLabels[] = $item->faktor_penyebab;
        $chartValues[] = (int) $item->total;
        $chartColors[] = $colors[$idx % count($colors)];
    }

    $topPct = ($totalKasusPerceraian > 0 && $topFaktorPerceraian) ? round(($topFaktorPerceraian->total / $totalKasusPerceraian) * 100, 1) : 0;
@endphp

<div id="faktor-perceraian-section" class="bg-surface-container-lowest rounded-2xl p-5 sm:p-6 shadow-sm border border-surface-container-high/60 flex flex-col gap-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-surface-container-high/60">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="material-symbols-outlined text-[26px] text-red-600">pie_chart</span>
                <h2 class="font-headline text-lg sm:text-xl font-bold text-on-surface">
                    Faktor Utama Penyebab Perceraian
                </h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                    Data BPS 2023–2025
                </span>
                @if($selectedProvinsi)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-fixed text-on-primary-fixed">
                        {{ $selectedProvinsi }}
                    </span>
                @endif
                @if($selectedTahun)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-surface-container-high text-on-surface-variant">
                        Tahun {{ $selectedTahun }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-on-surface-variant">
                Visualisasi faktor pemicu perkara perceraian resmi peradilan agama & sensus demografi BPS di {{ $selectedProvinsi ?: 'seluruh wilayah Indonesia' }}
            </p>
        </div>

        <!-- View Controls & Total Badge -->
        <div class="flex items-center gap-3 self-start sm:self-center shrink-0">
            <div class="text-right sm:block hidden">
                <span class="text-[10px] uppercase font-bold text-outline block">Total Perkara Terdata</span>
                <span class="text-sm sm:text-base font-bold font-mono text-red-700">
                    {{ number_format($totalKasusPerceraian, 0, ',', '.') }} Perkara
                </span>
            </div>
            <div class="inline-flex rounded-xl p-1 bg-surface-container border border-outline-variant/30 text-xs">
                <button type="button" id="btn-chart-donut" onclick="switchChartType('doughnut')" class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 transition bg-surface-container-lowest text-primary shadow-xs">
                    <span class="material-symbols-outlined text-[16px]">donut_large</span>
                    <span>Donut</span>
                </button>
                <button type="button" id="btn-chart-bar" onclick="switchChartType('bar')" class="px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5 transition text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                    <span>Bar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Chart & Breakdown Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
        <!-- Chart Canvas Container (5 cols on LG) -->
        <div class="lg:col-span-5 flex flex-col items-center justify-center p-3 relative min-h-[300px]">
            <div class="relative w-full max-w-[320px] aspect-square flex items-center justify-center">
                <canvas id="divorceFactorsChart" class="w-full h-full"></canvas>
            </div>
            <div class="mt-3 flex items-center gap-4 text-xs text-outline justify-center">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                    <span>Perselisihan</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                    <span>Ekonomi</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>
                    <span>KDRT</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span>
                    <span>Lainnya</span>
                </span>
            </div>
        </div>

        <!-- Breakdown List & Percentages (7 cols on LG) -->
        <div class="lg:col-span-7 flex flex-col justify-between gap-3 bg-surface-container-low/60 rounded-xl p-4 sm:p-5 border border-outline-variant/30">
            <div class="flex items-center justify-between pb-2 border-b border-outline-variant/30">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px]">format_list_bulleted</span>
                    <h3 class="font-headline text-xs font-bold uppercase tracking-wider text-on-surface">
                        Peringkat Faktor Penyebab Perceraian
                    </h3>
                </div>
                <span class="text-[11px] font-mono text-outline">Persentase Kasus</span>
            </div>

            <!-- List items -->
            <div class="flex flex-col gap-2.5 max-h-[320px] overflow-y-auto pr-1">
                @forelse($faktorPerceraianData as $index => $item)
                    @php
                        $pct = $totalKasusPerceraian > 0 ? round(($item->total / $totalKasusPerceraian) * 100, 1) : 0;
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div class="flex flex-col gap-1 text-xs bg-surface-container-lowest p-2.5 rounded-xl border border-outline-variant/30 hover:border-primary/50 transition">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <span class="w-5 h-5 rounded-md text-[10px] font-mono font-bold flex items-center justify-center shrink-0" style="background-color: {{ $color }}20; color: {{ $color }};">
                                    {{ $index + 1 }}
                                </span>
                                <span class="font-medium text-on-surface truncate" title="{{ $item->faktor_penyebab }}">
                                    {{ $item->faktor_penyebab }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="font-mono font-bold text-on-surface text-[11px]">
                                    {{ number_format($item->total, 0, ',', '.') }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold" style="background-color: {{ $color }}20; color: {{ $color }};">
                                    {{ $pct }}%
                                </span>
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ max($pct, 2) }}%; background-color: {{ $color }};"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-xs text-outline">
                        Data faktor perceraian tidak tersedia untuk kombinasi filter ini.
                    </div>
                @endforelse
            </div>

            <!-- Bottom Highlights Banner -->
            @if($topFaktorPerceraian)
                <div class="pt-3 border-t border-outline-variant/30 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-1.5 text-amber-800">
                        <span class="material-symbols-outlined text-[16px] text-amber-600">info</span>
                        <span>Faktor dominan #1: <strong>{{ $topFaktorPerceraian->faktor_penyebab }}</strong> ({{ $topPct }}%)</span>
                    </div>
                    <a href="{{ route('faktor-perceraian') }}" class="text-primary font-semibold hover:underline inline-flex items-center gap-1">
                        <span>Kelola Master Data Faktor</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    let divorceChartInstance = null;
    let currentChartType = 'doughnut';

    const rawChartData = {
        labels: {!! json_encode($chartLabels) !!},
        values: {!! json_encode($chartValues) !!},
        colors: {!! json_encode($chartColors) !!}
    };

    function initDivorceChart(type = 'doughnut') {
        const ctx = document.getElementById('divorceFactorsChart');
        if (!ctx) return;

        if (divorceChartInstance) {
            divorceChartInstance.destroy();
        }

        const isDonut = (type === 'doughnut');

        divorceChartInstance = new Chart(ctx, {
            type: type,
            data: {
                labels: rawChartData.labels,
                datasets: [{
                    label: 'Jumlah Perkara',
                    data: rawChartData.values,
                    backgroundColor: rawChartData.colors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6,
                    borderRadius: isDonut ? 4 : 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleColor: '#F8FAFC',
                        bodyColor: '#93C5FD',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const val = context.raw || 0;
                                const total = rawChartData.values.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${val.toLocaleString('id-ID')} Perkara (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: isDonut ? '68%' : 0,
                scales: isDonut ? {} : {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 10 },
                            callback: function(v) { return v.toLocaleString('id-ID'); }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 45,
                            minRotation: 20
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function switchChartType(type) {
        currentChartType = type;
        const btnDonut = document.getElementById('btn-chart-donut');
        const btnBar = document.getElementById('btn-chart-bar');

        if (type === 'doughnut') {
            btnDonut.className = 'px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 transition bg-surface-container-lowest text-primary shadow-xs';
            btnBar.className = 'px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5 transition text-on-surface-variant hover:text-on-surface';
        } else {
            btnBar.className = 'px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 transition bg-surface-container-lowest text-primary shadow-xs';
            btnDonut.className = 'px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5 transition text-on-surface-variant hover:text-on-surface';
        }

        initDivorceChart(type);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDivorceChart('doughnut');
    });
</script>
