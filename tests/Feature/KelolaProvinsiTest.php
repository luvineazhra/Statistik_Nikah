<?php

namespace Tests\Feature;

use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KelolaProvinsiTest extends TestCase
{
    /**
     * 1. Pengguna tanpa login dialihkan ke halaman login (keamanan route)
     */
    public function test_guest_cannot_access_or_modify_provinsi(): void
    {
        $response = $this->get('/provinsi');
        $response->assertRedirect(route('login'));

        $postResponse = $this->post('/provinsi', [
            'code' => '99',
            'name' => 'Provinsi Test',
            'status' => 'Aktif',
        ]);
        $postResponse->assertRedirect(route('login'));
    }

    /**
     * 2. Admin yang login dapat membuka halaman Kelola Provinsi & data nyata ditampilkan
     */
    public function test_admin_can_view_kelola_provinsi_page_with_real_data(): void
    {
        $response = $this->withSession(['is_logged_in' => true])->get('/provinsi');

        $response->assertStatus(200);
        $response->assertSee('Kelola Provinsi');
        $response->assertSee('Master Wilayah Indonesia');
        $response->assertSee('Tambah Provinsi');

        // Pastikan nama provinsi nyata dari database muncul
        $response->assertSee('Aceh');
        $response->assertSee('Jawa Barat');
    }

    /**
     * 3. Pencarian dan filter berfungsi
     */
    public function test_search_and_filter_functionality(): void
    {
        // Cari "Jawa Barat"
        $response = $this->withSession(['is_logged_in' => true])->get('/provinsi?search=Jawa+Barat');
        $response->assertStatus(200);
        $response->assertSee('Jawa Barat');

        // Filter status Aktif
        $responseStatus = $this->withSession(['is_logged_in' => true])->get('/provinsi?status=Aktif');
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Aktif');

        // Filter pemetaan terhubung
        $responsePemetaan = $this->withSession(['is_logged_in' => true])->get('/provinsi?pemetaan=terhubung');
        $responsePemetaan->assertStatus(200);
        $responsePemetaan->assertSee('Terhubung');
    }

    /**
     * 4. Admin dapat menambah provinsi baru dengan validasi kode unik
     */
    public function test_add_province_with_unique_code_validation(): void
    {
        // Coba masukkan kode duplikat (32 sudah milik Jawa Barat)
        $dupResponse = $this->withSession(['is_logged_in' => true])->post('/provinsi', [
            'code' => '32',
            'name' => 'Jawa Barat Duplikat',
            'island_group' => 'Jawa',
            'status' => 'Aktif',
        ]);
        $dupResponse->assertSessionHasErrors('code');

        // Masukkan kode baru yang unik
        $newCode = '98';
        Province::where('code', $newCode)->delete();

        $validResponse = $this->withSession(['is_logged_in' => true])->post('/provinsi', [
            'code' => $newCode,
            'name' => 'Papua Khusus Test',
            'island_group' => 'Papua',
            'status' => 'Aktif',
        ]);

        $validResponse->assertRedirect(route('provinsi'));
        $validResponse->assertSessionHas('success');

        $this->assertDatabaseHas('provinces', [
            'code' => $newCode,
            'name' => 'Papua Khusus Test',
            'status' => 'Aktif',
        ]);

        Province::where('code', $newCode)->delete();
    }

    /**
     * 5. Admin dapat mengedit provinsi tanpa merusak relasi dan kode terkunci jika ada data historis
     */
    public function test_edit_province_preserves_relations_and_locks_code(): void
    {
        $jabar = Province::where('code', '32')->first();
        $this->assertNotNull($jabar);

        // Coba ganti kode provinsi 32 menjadi 39 padahal sudah terikat data historis
        $blockedEdit = $this->withSession(['is_logged_in' => true])->put("/provinsi/{$jabar->id}", [
            'code' => '39',
            'name' => 'Jawa Barat Baru',
            'island_group' => 'Jawa',
            'status' => 'Aktif',
        ]);
        $blockedEdit->assertSessionHasErrors('code');

        // Edit nama dan status dengan mempertahankan kode asli
        $validEdit = $this->withSession(['is_logged_in' => true])->put("/provinsi/{$jabar->id}", [
            'code' => '32',
            'name' => 'Jawa Barat Terupdate',
            'island_group' => 'Jawa',
            'status' => 'Aktif',
        ]);
        $validEdit->assertRedirect(route('provinsi'));
        $validEdit->assertSessionHas('success');

        $this->assertDatabaseHas('provinces', [
            'id' => $jabar->id,
            'code' => '32',
            'name' => 'Jawa Barat Terupdate',
        ]);

        // Kembalikan nama Jawa Barat semula
        $jabar->update(['name' => 'Jawa Barat']);
    }

    /**
     * 6. Toggle status Aktif / Nonaktif
     */
    public function test_toggle_status_province(): void
    {
        $jabar = Province::where('code', '32')->first();
        $this->assertNotNull($jabar);

        // Toggle menjadi Nonaktif
        $toggleResp = $this->withSession(['is_logged_in' => true])->patch("/provinsi/{$jabar->id}/toggle-status");
        $toggleResp->assertRedirect(route('provinsi'));
        $this->assertEquals('Nonaktif', $jabar->fresh()->status);

        // Toggle kembali menjadi Aktif
        $toggleBack = $this->withSession(['is_logged_in' => true])->patch("/provinsi/{$jabar->id}/toggle-status");
        $toggleBack->assertRedirect(route('provinsi'));
        $this->assertEquals('Aktif', $jabar->fresh()->status);
    }

    /**
     * 7. Provinsi yang terhubung statistik tidak dapat dihapus secara permanen
     */
    public function test_province_with_statistical_data_cannot_be_deleted(): void
    {
        $jabar = Province::where('code', '32')->first();
        $this->assertNotNull($jabar);

        $deleteResponse = $this->withSession(['is_logged_in' => true])->delete("/provinsi/{$jabar->id}");
        $deleteResponse->assertRedirect(route('provinsi'));
        $deleteResponse->assertSessionHas('error');

        // Pastikan Jawa Barat tidak terhapus dari tabel
        $this->assertDatabaseHas('provinces', [
            'id' => $jabar->id,
            'code' => '32',
        ]);
    }

    /**
     * 8. Provinsi tanpa relasi statistik dapat dihapus permanen
     */
    public function test_province_without_relations_can_be_deleted(): void
    {
        Province::where('code', '97')->delete();

        $tempProv = Province::create([
            'code' => '97',
            'name' => 'Wilayah Sementara',
            'island_group' => 'Papua',
            'status' => 'Aktif',
            'total_marriages' => 0,
            'dispensation_ratio' => 1.0,
        ]);

        $deleteResponse = $this->withSession(['is_logged_in' => true])->delete("/provinsi/{$tempProv->id}");
        $deleteResponse->assertRedirect(route('provinsi'));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('provinces', [
            'id' => $tempProv->id,
            'code' => '97',
        ]);
    }

    /**
     * 9. API detail JSON mengembalikan status GeoJSON dan ringkasan statistik
     */
    public function test_detail_json_api(): void
    {
        $jabar = Province::where('code', '32')->first();
        $jabar->update(['name' => 'Jawa Barat']);

        $response = $this->withSession(['is_logged_in' => true])->get("/provinsi/api/{$jabar->id}/detail");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'code' => '32',
                'name' => 'Jawa Barat',
                'geo_mapping' => [
                    'is_mapped' => true,
                ],
                'has_relations' => true,
            ]
        ]);
    }

    /**
     * 10. Dashboard utama dan API peta tetap berfungsi normal
     */
    public function test_dashboard_and_map_api_remain_intact(): void
    {
        $dashResponse = $this->withSession(['is_logged_in' => true])->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Analytics & Persebaran Data Pernikahan Indonesia');

        $mapApiResponse = $this->get('/api/map-data?tahun=2025&bulan=Semua%20Bulan');
        $mapApiResponse->assertStatus(200);
        $mapApiResponse->assertJson([
            'status' => 'success',
            'tahun' => '2025',
            'is_data_available' => true,
        ]);
    }
}
