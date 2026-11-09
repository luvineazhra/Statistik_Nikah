<aside class="fixed left-0 top-0 h-full w-64 bg-primary-container z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.08)]">
    <div class="flex flex-col">
        <!-- Brand Header -->
        <a href="{{ route('admin.dashboard') }}" class="px-space-md py-space-md border-b border-primary/40 block hover:bg-primary/30 transition-colors">
            <div class="flex items-center gap-space-sm">
                <div class="w-9 h-9 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">account_balance</span>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-1.5">
                        <span class="font-headline text-lg text-on-primary tracking-tight font-bold">NikahInfo</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-variant text-on-primary-fixed">PORTAL</span>
                    </div>
                    <span class="text-[11px] text-primary-fixed-dim">Satu Data BPS & Kemenag</span>
                </div>
            </div>
        </a>

        <!-- Menu Section Label -->
        <div class="px-space-md pt-space-md pb-space-xs">
            <span class="text-[11px] text-on-primary-container tracking-wider uppercase font-semibold">Menu Navigasi</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex flex-col px-space-sm gap-1">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-space-sm px-space-md py-space-sm rounded-lg transition-all {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'bg-primary text-on-primary font-semibold shadow-sm border-l-4 border-tertiary-fixed' : 'text-primary-fixed-dim hover:bg-primary/50 hover:text-on-primary' }}"
               {!! request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'aria-current="page"' : '' !!}>
                <span class="material-symbols-outlined text-[20px]">grid_view</span>
                <span class="text-sm">Dashboard Utama</span>
            </a>
            <a href="{{ route('data-pernikahan') }}" 
               class="flex items-center gap-space-sm px-space-md py-space-sm rounded-lg transition-all {{ request()->routeIs('data-pernikahan') ? 'bg-primary text-on-primary font-semibold shadow-sm border-l-4 border-tertiary-fixed' : 'text-primary-fixed-dim hover:bg-primary/50 hover:text-on-primary' }}"
               {!! request()->routeIs('data-pernikahan') ? 'aria-current="page"' : '' !!}>
                <span class="material-symbols-outlined text-[20px]">table_chart</span>
                <span class="text-sm">Kelola Data Pernikahan</span>
            </a>
            <a href="{{ route('faktor-perceraian') }}" 
               class="flex items-center gap-space-sm px-space-md py-space-sm rounded-lg transition-all {{ request()->routeIs('faktor-perceraian*') || request()->routeIs('admin.faktor-perceraian*') ? 'bg-primary text-on-primary font-semibold shadow-sm border-l-4 border-tertiary-fixed' : 'text-primary-fixed-dim hover:bg-primary/50 hover:text-on-primary' }}"
               {!! request()->routeIs('faktor-perceraian*') ? 'aria-current="page"' : '' !!}>
                <span class="material-symbols-outlined text-[20px]">heart_broken</span>
                <span class="text-sm">Kelola Data Perceraian & Faktor</span>
            </a>
            <a href="{{ route('provinsi') }}" 
               class="flex items-center gap-space-sm px-space-md py-space-sm rounded-lg transition-all {{ request()->routeIs('provinsi*') || request()->routeIs('admin.provinsi*') ? 'bg-primary text-on-primary font-semibold shadow-sm border-l-4 border-tertiary-fixed' : 'text-primary-fixed-dim hover:bg-primary/50 hover:text-on-primary' }}"
               {!! request()->routeIs('provinsi*') || request()->routeIs('admin.provinsi*') ? 'aria-current="page"' : '' !!}>
                <span class="material-symbols-outlined text-[20px]">map</span>
                <span class="text-sm">Kelola Provinsi</span>
            </a>
            <a href="{{ route('faktor-sumber') }}" 
               class="flex items-center gap-space-sm px-space-md py-space-sm rounded-lg transition-all {{ request()->routeIs('faktor-sumber') ? 'bg-primary text-on-primary font-semibold shadow-sm border-l-4 border-tertiary-fixed' : 'text-primary-fixed-dim hover:bg-primary/50 hover:text-on-primary' }}"
               {!! request()->routeIs('faktor-sumber') ? 'aria-current="page"' : '' !!}>
                <span class="material-symbols-outlined text-[20px]">analytics</span>
                <span class="text-sm">Kelola Faktor & Sumber</span>
            </a>
            <a href="{{ route('profil') }}" 
               class="flex items-center gap-space-sm px-space-md py-space-sm rounded-lg transition-all {{ request()->routeIs('profil') ? 'bg-primary text-on-primary font-semibold shadow-sm border-l-4 border-tertiary-fixed' : 'text-primary-fixed-dim hover:bg-primary/50 hover:text-on-primary' }}"
               {!! request()->routeIs('profil') ? 'aria-current="page"' : '' !!}>
                <span class="material-symbols-outlined text-[20px]">person_outline</span>
                <span class="text-sm">Profil Saya</span>
            </a>

            <!-- Shortcut ke Website Publik -->
            <div class="my-1.5 border-t border-primary/40 mx-2"></div>
            <a href="{{ route('public.dashboard') }}" target="_blank"
               class="flex items-center gap-space-sm px-space-md py-space-sm rounded-lg text-primary-fixed-dim hover:bg-primary/50 hover:text-on-primary transition-all group">
                <span class="material-symbols-outlined text-[20px] text-tertiary-fixed">public</span>
                <span class="text-sm">Lihat Website Publik</span>
                <span class="material-symbols-outlined text-[15px] ml-auto opacity-70 group-hover:opacity-100">open_in_new</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Status & Logout -->
    <div class="p-space-md border-t border-primary/40 flex flex-col gap-space-sm">
        <div class="flex items-center justify-between px-space-sm py-1.5 rounded-lg bg-primary/40">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-tertiary-fixed opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-tertiary-fixed"></span>
                </span>
                <span class="text-xs text-on-primary">Data Sinkron Realtime</span>
            </div>
            <span class="material-symbols-outlined text-[14px] text-tertiary-fixed">sync</span>
        </div>
        <a href="{{ route('logout') }}" class="flex items-center gap-space-sm px-space-sm py-space-sm rounded-lg text-primary-fixed-dim hover:bg-error/20 hover:text-error-container transition-all">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="text-sm">Keluar Sistem</span>
        </a>
    </div>
</aside>
