@props([
    'title' => 'Stat Metric',
    'subtitle' => 'Dataset info',
    'value' => '0',
    'unit' => '',
    'icon' => 'analytics',
    'iconBg' => 'bg-secondary-fixed text-primary',
    'badgeText' => '',
    'badgeClass' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
    'badgeIcon' => 'arrow_upward',
    'badgeNote' => null,
])

<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-surface-container-high/60 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
    <div class="flex items-start justify-between gap-2">
        <div class="flex flex-col gap-1">
            <span class="text-xs text-on-surface-variant font-medium">{{ $title }}</span>
            <span class="text-[11px] text-outline">{{ $subtitle }}</span>
        </div>
        <div class="w-10 h-10 rounded-full {{ $iconBg }} flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
        </div>
    </div>
    
    <div class="my-space-md">
        <div class="flex items-baseline gap-1.5">
            <span class="font-headline text-2xl font-bold text-on-surface tracking-tight">{{ $value }}</span>
            @if($unit)
                <span class="text-xs text-on-surface-variant font-medium">{{ $unit }}</span>
            @endif
        </div>
    </div>

    @if($badgeText)
        <div class="flex items-center gap-1.5 pt-space-xs flex-wrap">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full {{ $badgeClass }} text-[11px] font-semibold">
                @if($badgeIcon)
                    <span class="material-symbols-outlined text-[14px]">{{ $badgeIcon }}</span>
                @endif
                {{ $badgeText }}
            </span>
            @if(!is_null($badgeNote))
                @if($badgeNote !== '')
                    <span class="text-[11px] text-outline">{{ $badgeNote }}</span>
                @endif
            @else
                <span class="text-[11px] text-outline">vs periode sebelumnya</span>
            @endif
        </div>
    @endif
</div>
