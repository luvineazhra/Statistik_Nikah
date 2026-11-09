@props([
    'id' => 'modal',
    'title' => 'Dialog Box'
])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-on-surface/50 backdrop-blur-sm p-4">
    <div class="bg-surface-container-lowest rounded-2xl shadow-2xl border border-surface-container-high w-full max-w-lg overflow-hidden flex flex-col transform transition-all">
        <!-- Modal Header -->
        <div class="px-space-lg py-space-md border-b border-surface-container-high/60 flex items-center justify-between">
            <h3 class="font-headline text-base font-bold text-on-surface">{{ $title }}</h3>
            <button type="button" onclick="closeModal('{{ $id }}')" class="p-1 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-space-lg">
            {{ $body }}
        </div>

        <!-- Modal Footer -->
        @if(isset($footer))
            <div class="px-space-lg py-space-md bg-surface-container-low/50 border-t border-surface-container-high/60 flex items-center justify-end gap-2">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
