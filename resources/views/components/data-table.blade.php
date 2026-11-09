@props([
    'tabelData' => [],
    'selectedProvinsi' => '',
    'selectedTahun' => '',
])

<div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container-high/60 overflow-hidden flex flex-col">
    <!-- Table Header Toolbar -->
    <div class="p-space-md border-b border-surface-container-high/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex flex-col gap-0.5">
            <h3 class="font-headline text-base font-bold text-on-surface">Data Registrasi Pernikahan Menurut Provinsi</h3>
            <p class="text-xs text-on-surface-variant">
                Menampilkan data statistik dari tabel <code class="bg-surface-container-high px-1 py-0.5 rounded text-[11px] font-mono">data_pernikahan_2023_2025_flat</code>
                @if($selectedProvinsi)
                    • Filter Provinsi: <strong>{{ $selectedProvinsi }}</strong>
                @endif
                @if($selectedTahun)
                    • Tahun: <strong>{{ $selectedTahun }}</strong>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="px-3 py-1.5 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant text-xs border border-outline-variant/40 font-medium">Export CSV</button>
        </div>
    </div>

    <!-- Table Canvas -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="marriagesDataTable">
            <thead>
                <tr class="bg-surface-container-low/70 text-[11px] font-semibold tracking-wider text-on-surface-variant uppercase border-b border-surface-container-high">
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Kode Wilayah</th>
                    <th class="py-3 px-4">Provinsi</th>
                    <th class="py-3 px-4 text-right">Total Akta Pernikahan</th>
                    <th class="py-3 px-4 text-center">Status Data</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-high/50 text-xs text-on-surface">
                @forelse($tabelData as $index => $row)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="py-3 px-4 text-outline">{{ $index + 1 }}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded bg-primary-fixed text-on-primary-fixed text-[11px] font-bold font-mono">{{ $row->kode_wilayah }}</span>
                    </td>
                    <td class="py-3 px-4 font-semibold text-on-surface">
                        {{ $row->provinsi }}
                    </td>
                    <td class="py-3 px-4 text-right font-mono font-bold text-primary text-sm">
                        {{ number_format($row->total_marriages, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant text-[11px] font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Terverifikasi
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="{{ route('dashboard', ['provinsi' => $row->provinsi]) }}" class="p-1 rounded text-outline hover:text-primary transition-colors inline-block" title="Filter Provinsi Ini">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-on-surface-variant italic">
                        Tidak ada data pernikahan yang sesuai dengan filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="p-space-sm bg-surface-container-low/40 border-t border-surface-container-high/60 flex items-center justify-between text-xs text-on-surface-variant">
        <span>Menampilkan {{ count($tabelData) }} Data Provinsi</span>
        <div class="text-[11px] text-outline">
            Tabel Data Konsolidasi Kementerian Agama & BPS
        </div>
    </div>
</div>

<script>
function filterTable() {
    let input = document.getElementById("tableSearchInput");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("marriagesDataTable");
    let tr = table.getElementsByTagName("tr");
    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[2];
        if (td) {
            let txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>
