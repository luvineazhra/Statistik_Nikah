<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\RekapNikahProvinsi;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DataPernikahanTest extends TestCase
{
    /**
     * 1. Pengguna tanpa login tidak dapat mengakses atau memodifikasi data pernikahan (Keamanan route)
     */
    public function test_guest_cannot_access_or_modify_data_pernikahan(): void
    {
        $response = $this->get('/data-pernikahan');
        $response->assertRedirect(route('login'));

        $postResponse = $this->post('/data-pernikahan', [
            'tahun' => 2025,
            'provinsi' => 'Jawa Barat',
            'bulan' => 'Januari',
            'jumlah_pernikahan' => 10000,
        ]);
        $postResponse->assertRedirect(route('login'));

        $exportResponse = $this->get('/data-pernikahan/export');
        $exportResponse->assertStatus(403);
    }

    /**
     * 2. Admin yang login dapat membuka halaman Kelola Data Pernikahan dengan data nyata dari MySQL
     */
    public function test_admin_can_view_kelola_data_pernikahan_with_real_mysql_data(): void
    {
        $response = $this->withSession(['is_logged_in' => true])->get('/data-pernikahan');

        $response->assertStatus(200);
        $response->assertSee('Kelola Data Pernikahan');
        $response->assertSee('Kelola, periksa, dan pantau data statistik pernikahan berdasarkan tahun dan provinsi di Indonesia.');
        $response->assertSee('Tambah Data Pernikahan');
        $response->assertSee('Ekspor CSV / Excel');

        // Pastikan 4 kartu ringkasan dinamis dari database muncul
        $response->assertSee('Total Data');
        $response->assertSee('Total Pernikahan');
        $response->assertSee('Tahun Tersedia');
        $response->assertSee('Provinsi dengan Data');

        // Pastikan data aktual dari rekap_nikah_provinsi muncul
        $firstRecord = RekapNikahProvinsi::first();
        $this->assertNotNull($firstRecord, 'Tabel rekap_nikah_provinsi harus memiliki data.');
        $response->assertSee($firstRecord->provinsi);
    }

    /**
     * 3. Fitur pencarian berdasarkan nama dan kode provinsi berfungsi
     */
    public function test_search_by_provinsi_and_code(): void
    {
        // Cari provinsi "Jawa Barat"
        $response = $this->withSession(['is_logged_in' => true])->get('/data-pernikahan?search=Jawa+Barat');
        $response->assertStatus(200);
        $response->assertSee('Jawa Barat');

        // Cari kode wilayah "32" (Jawa Barat)
        $responseCode = $this->withSession(['is_logged_in' => true])->get('/data-pernikahan?search=32');
        $responseCode->assertStatus(200);
        $responseCode->assertSee('32');
    }

    /**
     * 4. Fitur filter tahun, provinsi master, dan bulan berfungsi
     */
    public function test_filter_by_tahun_provinsi_and_bulan(): void
    {
        // Filter Tahun 2023
        $resp2023 = $this->withSession(['is_logged_in' => true])->get('/data-pernikahan?tahun=2023');
        $resp2023->assertStatus(200);
        $resp2023->assertSee('2023');

        // Filter Tahun 2024 menampilkan catatan kelengkapan (tersedia sampai September)
        $resp2024 = $this->withSession(['is_logged_in' => true])->get('/data-pernikahan?tahun=2024');
        $resp2024->assertStatus(200);
        $resp2024->assertSee('Catatan Kelengkapan Data Tahun 2024:');
        $resp2024->assertSee('Januari hingga September');

        // Filter Provinsi & Bulan
        $respFilter = $this->withSession(['is_logged_in' => true])->get('/data-pernikahan?provinsi=Jawa+Barat&bulan=Januari');
        $respFilter->assertStatus(200);
        $respFilter->assertSee('Jawa Barat');
        $respFilter->assertSee('Januari');
    }

    /**
     * 5. API JSON detail data pernikahan mengembalikan rincian lengkap 12 bulan
     */
    public function test_detail_json_api_returns_complete_breakdown(): void
    {
        $record = RekapNikahProvinsi::first();
        $this->assertNotNull($record);

        $response = $this->withSession(['is_logged_in' => true])->get("/data-pernikahan/api/{$record->id}/detail");
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $record->id,
                'provinsi' => $record->provinsi,
                'tahun' => $record->tahun,
                'bulan' => $record->bulan,
            ]
        ]);

        $json = $response->json();
        $this->assertArrayHasKey('monthly_breakdown', $json['data']);
        $this->assertArrayHasKey('total_tahunan', $json['data']);
        $this->assertArrayHasKey('bulan_puncak', $json['data']);
        $this->assertArrayHasKey('catatan_kelengkapan', $json['data']);
    }

    /**
     * 6. Validasi mencegah duplikasi kombinasi (Provinsi, Tahun, Bulan) & mencegah angka negatif
     */
    public function test_store_validation_prevents_duplicate_and_negative_value(): void
    {
        $existing = RekapNikahProvinsi::first();
        $this->assertNotNull($existing);

        // Uji coba membuat data yang sudah ada persis
        $dupResponse = $this->withSession(['is_logged_in' => true])->post('/data-pernikahan', [
            'tahun' => $existing->tahun,
            'provinsi' => $existing->provinsi,
            'bulan' => $existing->bulan,
            'jumlah_pernikahan' => 5000,
        ]);
        $dupResponse->assertSessionHasErrors(['duplicate']);

        // Uji coba angka pernikahan negatif
        $negResponse = $this->withSession(['is_logged_in' => true])->post('/data-pernikahan', [
            'tahun' => 2026,
            'provinsi' => 'Aceh',
            'bulan' => 'Januari',
            'jumlah_pernikahan' => -50,
        ]);
        $negResponse->assertSessionHasErrors(['jumlah_pernikahan']);
    }

    /**
     * 7. Siklus CRUD lengkap: Tambah, Perbarui, dan Hapus data pernikahan
     */
    public function test_crud_lifecycle_store_update_and_destroy(): void
    {
        $testTahun = 2026;
        $testProvinsi = 'Aceh';
        $testBulan = 'Januari';
        $testJumlah = 3850;

        // Bersihkan data tes jika ada sisa
        RekapNikahProvinsi::where('tahun', $testTahun)
            ->where('provinsi', $testProvinsi)
            ->where('bulan', $testBulan)
            ->delete();

        // 1. Tambah data (Store)
        $storeResponse = $this->withSession(['is_logged_in' => true])->post('/data-pernikahan', [
            'tahun' => $testTahun,
            'provinsi' => $testProvinsi,
            'bulan' => $testBulan,
            'jumlah_pernikahan' => $testJumlah,
        ]);

        $storeResponse->assertRedirect(route('data-pernikahan'));
        $storeResponse->assertSessionHas('success');

        $created = RekapNikahProvinsi::where('tahun', $testTahun)
            ->where('provinsi', $testProvinsi)
            ->where('bulan', $testBulan)
            ->first();

        $this->assertNotNull($created);
        $this->assertEquals($testJumlah, $created->jumlah_pernikahan);

        // 2. Perbarui data (Update)
        $updatedJumlah = 4500;
        $updateResponse = $this->withSession(['is_logged_in' => true])->put("/data-pernikahan/{$created->id}", [
            'tahun' => $testTahun,
            'provinsi' => $testProvinsi,
            'bulan' => $testBulan,
            'jumlah_pernikahan' => $updatedJumlah,
        ]);

        $updateResponse->assertRedirect(route('data-pernikahan'));
        $updateResponse->assertSessionHas('success');

        $created->refresh();
        $this->assertEquals($updatedJumlah, $created->jumlah_pernikahan);

        // 3. Hapus data (Destroy)
        $deleteResponse = $this->withSession(['is_logged_in' => true])->delete("/data-pernikahan/{$created->id}");
        $deleteResponse->assertRedirect(route('data-pernikahan'));
        $deleteResponse->assertSessionHas('success');

        $deleted = RekapNikahProvinsi::find($created->id);
        $this->assertNull($deleted);
    }

    /**
     * 8. Update mencegah benturan duplikasi dengan baris lain
     */
    public function test_update_prevents_duplicate_collision(): void
    {
        $records = RekapNikahProvinsi::take(2)->get();
        $this->assertCount(2, $records);

        $rowA = $records[0];
        $rowB = $records[1];

        // Coba ubah row A menjadi identitas row B
        $response = $this->withSession(['is_logged_in' => true])->put("/data-pernikahan/{$rowA->id}", [
            'tahun' => $rowB->tahun,
            'provinsi' => $rowB->provinsi,
            'bulan' => $rowB->bulan,
            'jumlah_pernikahan' => 1234,
        ]);

        $response->assertSessionHasErrors(['duplicate']);
    }

    /**
     * 9. Ekspor CSV / Excel bekerja dan mengalirkan data dengan header valid
     */
    public function test_export_csv_streams_real_data(): void
    {
        $response = $this->withSession(['is_logged_in' => true])->get('/data-pernikahan/export?tahun=2025');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('data_pernikahan_2025.csv', $response->headers->get('Content-Disposition'));
    }

    /**
     * 10. Dashboard utama dan menu lainnya tetap berfungsi dengan baik
     */
    public function test_dashboard_and_related_endpoints_remain_functional(): void
    {
        // Dashboard utama
        $dashResponse = $this->withSession(['is_logged_in' => true])->get('/');
        $dashResponse->assertStatus(200);

        // Kelola Provinsi
        $provResponse = $this->withSession(['is_logged_in' => true])->get('/provinsi');
        $provResponse->assertStatus(200);

        // Kelola Faktor Perceraian
        $faktorResponse = $this->withSession(['is_logged_in' => true])->get('/faktor-perceraian');
        $faktorResponse->assertStatus(200);

        // API Map Data
        $mapResponse = $this->get('/api/map-data?tahun=2025');
        $mapResponse->assertStatus(200);
        $mapResponse->assertJson(['status' => 'success']);
    }

    /**
     * 11. Verifikasi aturan pengurutan baru (ORDER BY) pada Data Pernikahan & Faktor Perceraian
     */
    public function test_sorting_logic_in_data_pernikahan_and_faktor_perceraian(): void
    {
        // 1. Data Pernikahan: Tahun (desc), Bulan Januari (asc), Provinsi (asc)
        $responseNikah = $this->withSession(['is_logged_in' => true])->get('/data-pernikahan');
        $responseNikah->assertStatus(200);

        $pernikahanList = $responseNikah->viewData('pernikahanList');
        $this->assertNotEmpty($pernikahanList);

        $firstItem = $pernikahanList->first();
        $this->assertEquals(2025, (int) $firstItem->tahun);
        $this->assertEquals('Januari', $firstItem->bulan);
        $this->assertEquals('Aceh', $firstItem->provinsi);

        // Item kedua harus tetap Januari 2025 dengan provinsi berikutnya secara alfabetis (Bali)
        $items = $pernikahanList->items();
        $this->assertGreaterThan(1, count($items));
        $this->assertEquals(2025, (int) $items[1]->tahun);
        $this->assertEquals('Januari', $items[1]->bulan);
        $this->assertEquals('Bali', $items[1]->provinsi);

        // 2. Faktor Perceraian: Tahun (desc), Faktor Penyebab (asc), Provinsi (asc)
        $responseFaktor = $this->withSession(['is_logged_in' => true])->get('/faktor-perceraian');
        $responseFaktor->assertStatus(200);

        $faktorList = $responseFaktor->viewData('faktorList');
        $this->assertNotEmpty($faktorList);

        $firstFaktor = $faktorList->first();
        $this->assertEquals(2025, (int) $firstFaktor->tahun);
        $this->assertEquals('Aceh', $firstFaktor->provinsi);

        // Baris kedua harus faktor penyebab yang sama dengan provinsi berikutnya secara alfabetis (Bali), bukan Aceh menumpuk
        $faktorItems = $faktorList->items();
        $this->assertGreaterThan(1, count($faktorItems));
        $this->assertEquals(2025, (int) $faktorItems[1]->tahun);
        $this->assertEquals($firstFaktor->faktor_penyebab, $faktorItems[1]->faktor_penyebab);
        $this->assertEquals('Bali', $faktorItems[1]->provinsi);
        $this->assertNotEquals($faktorItems[0]->provinsi, $faktorItems[1]->provinsi);
    }
}
