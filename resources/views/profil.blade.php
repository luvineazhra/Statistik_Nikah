<x-layouts.app title="Profil Administrator • NikahInfo">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md mb-6">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-fixed text-on-primary-fixed">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    Pengaturan Akun & Keamanan
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-container-high text-on-surface-variant">
                    <span class="material-symbols-outlined text-[14px] text-tertiary-container">verified_user</span>
                    Level Hak Akses: Super Admin
                </span>
            </div>
            <h1 class="font-headline text-2xl md:text-3xl font-bold text-on-surface tracking-tight mt-1">
                Profil Administrator
            </h1>
            <p class="text-xs md:text-sm text-on-surface-variant">
                Kelola informasi pribadi, data kredensial, keamanan 2FA, dan riwayat aktivitas administrator NikahInfo
            </p>
        </div>

        <div class="flex items-center gap-space-sm self-start lg:self-center shrink-0">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-surface-container-lowest text-on-surface shadow-sm hover:bg-surface-container transition-colors text-xs font-medium border border-outline-variant/40">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">arrow_back</span>
                <span>Kembali ke Dashboard</span>
            </a>
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

    <!-- Profile Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Admin Card Summary -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- Main Profile Card -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-6 shadow-sm flex flex-col items-center text-center relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-24 bg-gradient-to-r from-primary-container via-secondary to-primary-container opacity-90"></div>
                
                <div class="relative z-10 mt-6 mb-3">
                    <div class="w-24 h-24 rounded-full bg-primary p-1 shadow-lg ring-4 ring-white relative mx-auto">
                        <div class="w-full h-full rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-3xl">
                            <span class="material-symbols-outlined text-[48px]">person</span>
                        </div>
                        <span class="absolute bottom-1 right-1 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full" title="Status: Online"></span>
                    </div>
                </div>

                <div class="relative z-10 w-full">
                    <h2 class="font-headline font-bold text-lg text-on-surface">
                        {{ session('user_name', 'Drs. Hendra Irawan, M.Si') }}
                    </h2>
                    <p class="text-xs text-on-surface-variant font-mono mt-0.5">
                        {{ session('user_identifier', 'admin@nikah.info') }}
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-1.5 mt-3">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-primary-fixed text-on-primary-fixed">
                            Super Administrator
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                            Terverifikasi BSSN
                        </span>
                    </div>

                    <div class="mt-6 pt-5 border-t border-surface-container-high w-full space-y-3 text-left">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-on-surface-variant flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-secondary">badge</span>
                                ID NIP Administrator
                            </span>
                            <span class="font-semibold text-on-surface font-mono">19780412 200312 1 002</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-on-surface-variant flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-secondary">shield</span>
                                Sesi Otentikasi
                            </span>
                            <span class="font-medium text-emerald-600">TLS 1.3 Active</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-on-surface-variant flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-secondary">schedule</span>
                                Login Terakhir
                            </span>
                            <span class="font-medium text-on-surface">Hari Ini, 15:35 WIB</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access System Security Badge Card -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-tertiary-container/10 text-tertiary-teal flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">security</span>
                    </div>
                    <div>
                        <h3 class="font-headline font-bold text-xs uppercase tracking-wider text-on-surface">Keamanan Otentikasi</h3>
                        <p class="text-[11px] text-on-surface-variant">Status Proteksi Akun Administrator</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="p-2.5 rounded-lg bg-surface-container-low flex items-center justify-between">
                        <span class="text-on-surface font-medium">Otentikasi 2FA</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-surface-container-low flex items-center justify-between">
                        <span class="text-on-surface font-medium">Password Enkripsi</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">Bcrypt / Argon2</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-surface-container-low flex items-center justify-between">
                        <span class="text-on-surface font-medium">IP WAF Whitelist</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 font-mono">127.0.0.1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Edit Profile Forms & Logs -->
        <div class="lg:col-span-8 flex flex-col gap-6">

            <!-- Card 1: Data Profil & Identitas Admin -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-surface-container-high pb-4 mb-5">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[22px] text-primary">manage_accounts</span>
                        <h2 class="font-headline font-bold text-base text-on-surface">Perbarui Informasi Administrator</h2>
                    </div>
                    <span class="text-xs text-on-surface-variant">Semua data disimpan secara aman</span>
                </div>

                <form action="{{ route('profil.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5" for="nameInput">
                                Nama Lengkap Admin
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 text-[18px] text-outline">person</span>
                                <input 
                                    type="text" 
                                    id="nameInput" 
                                    name="name" 
                                    value="{{ session('user_name', 'Drs. Hendra Irawan, M.Si') }}" 
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-sm focus:border-secondary focus:ring-1 focus:ring-secondary font-medium" 
                                    required 
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5" for="identifierInput">
                                Username / Email Official
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 text-[18px] text-outline">mail</span>
                                <input 
                                    type="text" 
                                    id="identifierInput" 
                                    name="identifier" 
                                    value="{{ session('user_identifier', 'admin@nikah.info') }}" 
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-sm focus:border-secondary focus:ring-1 focus:ring-secondary font-medium" 
                                    required 
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5" for="phoneInput">
                                No. Telepon / WhatsApp Kerja
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 text-[18px] text-outline">call</span>
                                <input 
                                    type="text" 
                                    id="phoneInput" 
                                    value="+62 812-9876-5432" 
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-sm focus:border-secondary focus:ring-1 focus:ring-secondary font-medium" 
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5">
                                Peran & Level Akses System
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 text-[18px] text-outline">admin_panel_settings</span>
                                <input 
                                    type="text" 
                                    value="Super Administrator (Full System Control)" 
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-outline-variant/40 bg-surface-container-low text-on-surface-variant text-sm font-medium cursor-not-allowed" 
                                    disabled 
                                />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-on-primary font-headline text-xs font-bold shadow-md shadow-blue-900/10 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Ubah Kata Sandi -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-surface-container-high pb-4 mb-5">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[22px] text-primary">lock_reset</span>
                        <h2 class="font-headline font-bold text-base text-on-surface">Ubah Kata Sandi</h2>
                    </div>
                    <span class="text-xs text-on-surface-variant">Standar Keamanan Min. 8 Karakter</span>
                </div>

                <form onsubmit="event.preventDefault(); alert('Kata sandi administrator berhasil diperbarui.');" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5" for="oldPassword">
                            Kata Sandi Saat Ini
                        </label>
                        <input 
                            type="password" 
                            id="oldPassword" 
                            placeholder="••••••••" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-sm focus:border-secondary focus:ring-1 focus:ring-secondary" 
                            required 
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5" for="newPassword">
                                Kata Sandi Baru
                            </label>
                            <input 
                                type="password" 
                                id="newPassword" 
                                placeholder="••••••••" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-sm focus:border-secondary focus:ring-1 focus:ring-secondary" 
                                required 
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface uppercase tracking-wider mb-1.5" for="confirmPassword">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input 
                                type="password" 
                                id="confirmPassword" 
                                placeholder="••••••••" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant/60 bg-surface-container-lowest text-on-surface text-sm focus:border-secondary focus:ring-1 focus:ring-secondary" 
                                required 
                            />
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-surface-container-high hover:bg-surface-dim text-on-surface font-headline text-xs font-bold border border-outline-variant/40 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            <span>Update Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 3: Log Aktivitas Admin Terbaru -->
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-surface-container-high pb-4 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[22px] text-primary">history</span>
                        <h2 class="font-headline font-bold text-base text-on-surface">Riwayat Aktivitas Sistem Terbaru</h2>
                    </div>
                    <span class="text-xs text-on-surface-variant font-mono">Audit Digital Log</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-surface-container-high text-on-surface-variant font-semibold uppercase tracking-wider">
                                <th class="py-2.5 px-3">Waktu</th>
                                <th class="py-2.5 px-3">Aktivitas</th>
                                <th class="py-2.5 px-3">IP Address</th>
                                <th class="py-2.5 px-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-high">
                            <tr>
                                <td class="py-3 px-3 text-on-surface font-mono">10-10-2026 15:35:10</td>
                                <td class="py-3 px-3 font-medium text-on-surface">Login Berhasil ke Admin Panel</td>
                                <td class="py-3 px-3 font-mono text-on-surface-variant">127.0.0.1 (Localhost)</td>
                                <td class="py-3 px-3 text-right">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Sukses</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-3 text-on-surface font-mono">10-10-2026 14:20:45</td>
                                <td class="py-3 px-3 font-medium text-on-surface">Sinkronisasi Data Agregat Pernikahan 2025</td>
                                <td class="py-3 px-3 font-mono text-on-surface-variant">127.0.0.1 (Localhost)</td>
                                <td class="py-3 px-3 text-right">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Sukses</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-3 text-on-surface font-mono">10-10-2026 11:15:22</td>
                                <td class="py-3 px-3 font-medium text-on-surface">Export Laporan Visualisasi PDF Provinsi Jawa Barat</td>
                                <td class="py-3 px-3 font-mono text-on-surface-variant">127.0.0.1 (Localhost)</td>
                                <td class="py-3 px-3 text-right">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">Selesai</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
