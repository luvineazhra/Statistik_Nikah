@props([
    'chartBulananData' => [],
    'perbandinganProvinsi' => [],
    'selectedProvinsi' => '',
    'selectedTahun' => '',
    'totalPernikahan' => 0,
])

@php
    $maxBulanan = !empty($chartBulananData) ? max(array_values($chartBulananData)) : 1;
    if ($maxBulanan <= 0) $maxBulanan = 1;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chart Bulanan (2 Cols on LG) -->
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-surface-container-high/60 flex flex-col gap-space-md justify-between">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm pb-space-xs border-b border-surface-container-high/60">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[24px] text-primary">bar_chart</span>
                    <h2 class="font-headline text-lg font-bold text-on-surface">
                        Grafik Tren Pernikahan Bulanan
                    </h2>
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-primary-container text-on-primary">
                        {{ $selectedProvinsi ?: 'Nasional (Semua Provinsi)' }}
                    </span>
                </div>
                <p class="text-xs text-on-surface-variant">
                    Distribusi jumlah pernikahan per bulan {{ $selectedTahun ? "pada tahun $selectedTahun" : "(2023–2025)" }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-[11px] text-outline block">Total Terfilter</span>
                <span class="text-base font-bold font-mono text-primary">{{ number_format($totalPernikahan, 0, ',', '.') }} Akta</span>
            </div>
        </div>

        <!-- Dynamic Bar Chart Canvas -->
        <div class="relative w-full pt-4 pb-2">
            <div class="h-64 flex items-end justify-between gap-1 sm:gap-2 px-2 border-b border-outline-variant/40">
                @foreach($chartBulananData as $bulan => $total)
                    @php
                        $percent = round(($total / $maxBulanan) * 100);
                        $shortBulan = substr($bulan, 0, 3);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group relative">
                        <!-- Tooltip on Hover -->
                        <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-on-surface text-surface text-[10px] py-1 px-2 rounded font-mono pointer-events-none whitespace-nowrap z-10 shadow-md">
                            {{ $bulan }}: {{ number_format($total, 0, ',', '.') }}
                        </div>
                        <!-- Bar -->
                        <div class="w-full max-w-[36px] bg-primary/20 hover:bg-primary group-hover:shadow-lg transition-all rounded-t-md relative flex flex-col justify-end overflow-hidden" style="height: {{ max($percent, 4) }}%;">
                            <div class="w-full bg-primary transition-all" style="height: 100%;"></div>
                        </div>
                        <!-- Month Label -->
                        <span class="text-[10px] sm:text-xs text-on-surface-variant font-medium group-hover:text-primary transition-colors truncate">
                            {{ $shortBulan }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between text-xs text-outline pt-2 border-t border-surface-container-high/40">
            <span>Sumbangan terbanyak berada di bulan dengan batang tertinggi</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-primary inline-block"></span> Total Jumlah Akta</span>
        </div>
    </div>

    <!-- Perbandingan Antar Provinsi / Tahun (1 Col on LG) -->
    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-surface-container-high/60 flex flex-col gap-space-md justify-between">
        <div class="pb-space-xs border-b border-surface-container-high/60">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[24px] text-secondary">analytics</span>
                <h2 class="font-headline text-base font-bold text-on-surface">
                    @if(!$selectedProvinsi)
                        Perbandingan Antar Provinsi
                    @else
                        Perbandingan Per Tahun ({{ $selectedProvinsi }})
                    @endif
                </h2>
            </div>
            <p class="text-xs text-on-surface-variant">
                @if(!$selectedProvinsi)
                    Peringkat volume pernikahan tertinggi
                @else
                    Tren pernikahan tahunan di {{ $selectedProvinsi }}
                @endif
            </p>
        </div>

        <div class="flex flex-col gap-3 overflow-y-auto max-h-[260px] pr-1">
            @if(!$selectedProvinsi)
                @php
                    $maxProv = count($perbandinganProvinsi) > 0 ? $perbandinganProvinsi->max('total') : 1;
                    if ($maxProv <= 0) $maxProv = 1;
                @endphp
                @forelse($perbandinganProvinsi->take(7) as $idx => $item)
                    @php $pPercent = round(($item->total / $maxProv) * 100); @endphp
                    <div class="flex flex-col gap-1 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-on-surface truncate max-w-[160px]">
                                {{ $idx + 1 }}. {{ $item->provinsi }}
                            </span>
                            <span class="font-mono font-bold text-secondary text-[11px]">
                                {{ number_format($item->total, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full bg-secondary rounded-full transition-all" style="width: {{ max($pPercent, 5) }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-xs text-on-surface-variant py-4">Data perbandingan tidak tersedia.</div>
                @endforelse
            @else
                @php
                    $maxThn = count($perbandinganProvinsi) > 0 ? $perbandinganProvinsi->max('total') : 1;
                    if ($maxThn <= 0) $maxThn = 1;
                @endphp
                @forelse($perbandinganProvinsi as $item)
                    @php $tPercent = round(($item->total / $maxThn) * 100); @endphp
                    <div class="flex flex-col gap-1 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-on-surface">Tahun {{ $item->tahun }}</span>
                            <span class="font-mono font-bold text-primary text-[11px]">
                                {{ number_format($item->total, 0, ',', '.') }} Akta
                            </span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full bg-primary rounded-full transition-all" style="width: {{ max($tPercent, 5) }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-xs text-on-surface-variant py-4">Data tahun tidak tersedia.</div>
                @endforelse
            @endif
        </div>

        <div class="pt-2 border-t border-surface-container-high/40 text-center">
            @if(!$selectedProvinsi)
                <span class="text-[11px] text-outline">Menampilkan top provinsi berdasarkan volume data</span>
            @else
                <a href="{{ route('dashboard') }}" class="text-xs text-primary hover:underline font-semibold inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                    <span>Tampilkan Semua Provinsi</span>
                </a>
            @endif
        </div>
    </div>
</div>
