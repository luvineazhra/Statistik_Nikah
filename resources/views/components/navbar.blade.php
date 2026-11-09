<header class="fixed top-0 left-64 right-0 h-16 bg-surface-container-lowest/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 border-b border-surface-container-high px-space-lg flex items-center justify-between">
    <!-- Left Section: Breadcrumbs -->
    <div class="flex items-center gap-space-md">
        <div class="flex items-center gap-1.5 text-xs">
            <a href="{{ route('admin.dashboard') }}" class="text-on-surface-variant font-medium hover:text-primary transition-colors">Portal</a>
            <span class="text-outline-variant">/</span>
            <span class="font-semibold text-on-surface">
                @if(request()->routeIs('data-pernikahan'))
                    Kelola Data Pernikahan
                @elseif(request()->routeIs('faktor-perceraian*') || request()->routeIs('admin.faktor-perceraian*'))
                    Kelola Data Perceraian & Faktor
                @elseif(request()->routeIs('provinsi*') || request()->routeIs('admin.provinsi*'))
                    Kelola Provinsi
                @elseif(request()->routeIs('faktor-sumber'))
                    Kelola Faktor & Sumber
                @elseif(request()->routeIs('pengguna'))
                    Kelola Pengguna
                @elseif(request()->routeIs('profil'))
                    Profil Saya
                @else
                    Ringkasan Analitik Nasional
                @endif
            </span>
        </div>
    </div>

    <!-- Right Section: User Profile & Shortcut -->
    <div class="flex items-center gap-space-md">
        <!-- Quick Link ke Portal Publik -->
        <a href="{{ route('pengguna') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-xs font-medium text-on-surface transition-colors border border-outline-variant/40" title="Buka Portal Publik Bebas Login">
            <span class="material-symbols-outlined text-[16px] text-secondary">public</span>
            <span>Portal Publik</span>
        </a>

        <!-- User Profile Link -->
        <a href="{{ route('profil') }}" class="flex items-center gap-space-sm cursor-pointer group">
            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-on-primary font-semibold text-sm">
                <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
            </div>
            <div class="hidden md:flex flex-col text-left">
                <span class="text-xs font-semibold text-on-surface leading-tight group-hover:text-primary transition-colors">{{ session('user_name', 'Drs. Hendra Irawan, M.Si') }}</span>
                <span class="text-[11px] text-on-surface-variant leading-tight">Analis Demografi • {{ session('user_identifier', 'Kemenag RI') }}</span>
            </div>
            <span class="material-symbols-outlined text-[18px] text-outline group-hover:text-on-surface transition-colors">expand_more</span>
        </a>
    </div>
</header>
