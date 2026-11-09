<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'NikahInfo - Satu Data BPS & Kemenag RI' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    <!-- Material Symbols & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Leaflet.js CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js for Donut & Bar Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "tertiary-fixed": "#89f5e7",
              "error-container": "#ffdad6",
              "on-tertiary-fixed-variant": "#005049",
              "outline": "#757682",
              "surface-variant": "#d3e4fe",
              "primary-fixed-dim": "#b6c4ff",
              "surface-tint": "#4059aa",
              "error": "#ba1a1a",
              "secondary-fixed-dim": "#adc6ff",
              "inverse-surface": "#213145",
              "surface-dim": "#cbdbf5",
              "surface": "#f8f9ff",
              "on-primary-container": "#90a8ff",
              "tertiary-container": "#004942",
              "on-surface": "#0b1c30",
              "on-error": "#ffffff",
              "inverse-primary": "#b6c4ff",
              "secondary-container": "#2170e4",
              "on-primary-fixed": "#00164e",
              "surface-container-low": "#eff4ff",
              "on-surface-variant": "#444651",
              "background": "#f8f9ff",
              "on-secondary-fixed-variant": "#004395",
              "tertiary-fixed-dim": "#6bd8cb",
              "on-tertiary-container": "#4ebdb0",
              "on-primary-fixed-variant": "#264191",
              "on-primary": "#ffffff",
              "primary-container": "#1e3a8a",
              "secondary": "#0058be",
              "on-secondary-fixed": "#001a42",
              "surface-container-lowest": "#ffffff",
              "surface-container-highest": "#d3e4fe",
              "on-error-container": "#93000a",
              "primary-fixed": "#dce1ff",
              "inverse-on-surface": "#eaf1ff",
              "tertiary": "#00312c",
              "on-tertiary": "#ffffff",
              "on-background": "#0b1c30",
              "surface-container": "#e5eeff",
              "surface-bright": "#f8f9ff",
              "on-secondary-container": "#fefcff",
              "surface-container-high": "#dce9ff",
              "on-secondary": "#ffffff",
              "secondary-fixed": "#d8e2ff",
              "on-tertiary-fixed": "#00201d",
              "primary": "#00236f",
              "outline-variant": "#c5c5d3"
            },
            borderRadius: {
              "DEFAULT": "0.25rem",
              "lg": "0.5rem",
              "xl": "0.75rem",
              "full": "9999px"
            },
            spacing: {
              "space-xl": "2rem",
              "gutter-sm": "1rem",
              "margin-sm": "1rem",
              "space-xs": "0.25rem",
              "margin": "2rem",
              "space-sm": "0.5rem",
              "gutter": "1.5rem",
              "space-lg": "1.5rem",
              "space-md": "1rem"
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
              headline: ["Plus Jakarta Sans", "sans-serif"]
            }
          }
        }
      }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-surface font-sans text-on-surface antialiased selection:bg-primary-fixed selection:text-primary">
    <!-- Sidebar Component -->
    <x-sidebar />

    <!-- Main Shell Area -->
    <div class="pl-64 min-h-screen flex flex-col justify-between">
        <!-- Navbar Component -->
        <x-navbar />

        <!-- Main Page Content -->
        <main class="relative pt-16 w-full bg-background min-h-screen">
            <div class="p-space-md md:p-space-xl flex flex-col gap-space-lg">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer Component -->
        <x-footer />
    </div>

    <!-- Modals Container -->
    <x-modal id="addRecordModal" title="Tambah Registrasi Pernikahan Baru">
        <x-slot:body>
            <form id="recordForm" class="flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Provinsi</label>
                    <select class="w-full px-3 py-2 rounded-lg bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:ring-1 focus:ring-secondary">
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach(config('provinces.list', []) as $prov)
                            <option value="{{ $prov['code'] }}">{{ $prov['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1">Tahun Observasi</label>
                        <input type="number" value="2025" class="w-full px-3 py-2 rounded-lg bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:ring-1 focus:ring-secondary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1">Bulan</label>
                        <select class="w-full px-3 py-2 rounded-lg bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:ring-1 focus:ring-secondary">
                            <option>Januari</option>
                            <option>Februari</option>
                            <option>Maret</option>
                            <option>Juli</option>
                            <option>Agustus</option>
                            <option>September</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Jumlah Akta Terdaftar</label>
                    <input type="number" placeholder="Contoh: 18450" class="w-full px-3 py-2 rounded-lg bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:ring-1 focus:ring-secondary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Sumber Integrasi</label>
                    <input type="text" value="Kementerian Agama & Sensus BPS" readonly class="w-full px-3 py-2 rounded-lg bg-surface-container border border-outline-variant text-on-surface-variant cursor-not-allowed">
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button onclick="closeModal('addRecordModal')" type="button" class="px-4 py-2 rounded-lg text-on-surface-variant hover:bg-surface-container font-medium text-sm transition-colors">Batal</button>
            <button onclick="saveRecord()" type="button" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary hover:bg-primary font-medium text-sm transition-colors">Simpan Data</button>
        </x-slot:footer>
    </x-modal>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('hidden');
        }
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('hidden');
        }
        function saveRecord() {
            alert('Data pernikahan berhasil disimpan ke database!');
            closeModal('addRecordModal');
        }
    </script>
</body>
</html>
