<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPortalTest extends TestCase
{
    /**
     * Test bahwa halaman publik / dan /pengguna dapat diakses tanpa autentikasi (guest access)
     */
    public function test_guest_can_access_public_portal_without_login()
    {
        // 1. Akses ke route utama publik ('/')
        $responseHome = $this->get('/');
        $responseHome->assertStatus(200);
        $responseHome->assertSee('Statistik Pernikahan dan Perceraian Indonesia');
        $responseHome->assertDontSee('Login Admin');

        // 2. Akses ke /pengguna
        $response = $this->get('/pengguna');
        $response->assertStatus(200);
        $response->assertSee('Statistik Pernikahan dan Perceraian Indonesia');
        $response->assertSee('Jelajahi perkembangan data pernikahan dan perceraian berdasarkan provinsi di Indonesia periode 2023–2025');
        $response->assertSee('Jelajahi Data');
        $response->assertSee('Lihat Peta Persebaran');
        
        // Memastikan komponen navbar sesuai ketentuan
        $response->assertSee('Beranda');
        $response->assertSee('Data Pernikahan');
        $response->assertSee('Data Perceraian');
        $response->assertSee('Faktor Perceraian');
        $response->assertSee('Peta Persebaran');
        $response->assertSee('Tentang Data');

        // Memastikan tidak ada tombol Login Admin atau menu kelola admin internal di navbar publik
        $response->assertDontSee('Login Admin');
        $response->assertDontSee('Kelola Data Pernikahan');
        $response->assertDontSee('Kelola Provinsi');
        $response->assertDontSee('Kelola Faktor');
    }

    /**
     * Test filter tahun dan provinsi pada halaman publik
     */
    public function test_public_portal_filters_work()
    {
        // Filter tahun 2024 harus memunculkan catatan kelengkapan data s.d. September
        $response2024 = $this->get('/pengguna?tahun=2024');
        $response2024->assertStatus(200);
        $response204Content = $response2024->getContent();
        $this->assertStringContainsString('September', $response204Content);

        // Filter provinsi tertentu
        $responseJabar = $this->get('/pengguna?tahun=2025&provinsi=Jawa+Barat');
        $responseJabar->assertStatus(200);
        $responseJabar->assertSee('Jawa Barat');
    }

    /**
     * Test endpoint API JSON statistik publik (AJAX)
     */
    public function test_public_stats_api_returns_valid_json()
    {
        $response = $this->getJson('/api/public-stats?tahun=2025&jenis_statistik=pernikahan');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'tahun',
            'provinsi',
            'jenis_statistik',
            'kpi' => [
                'total_nikah',
                'total_cerai',
                'top_faktor_nama',
                'top_faktor_kasus',
                'provinsi_tercover',
                'tahun_label',
                'provinsi_label',
                'catatan_nikah',
                'catatan_cerai'
            ],
            'trenPernikahan' => [
                'labels',
                'data',
                'provinsi'
            ],
            'trenPerceraian' => [
                'labels',
                'data',
                'provinsi'
            ],
            'rankingNikah',
            'rankingCerai',
            'faktorBreakdown' => [
                'labels',
                'data',
                'items',
                'top_faktor'
            ]
        ]);

        $this->assertTrue($response->json('success'));
    }

    /**
     * Test endpoint API peta choropleth publik
     */
    public function test_public_map_api_returns_valid_data()
    {
        // Mode Pernikahan
        $responseNikah = $this->getJson('/api/public-map?tahun=2025&jenis_statistik=pernikahan');
        $responseNikah->assertStatus(200);
        $responseNikah->assertJsonStructure([
            'success',
            'tahun',
            'jenis_statistik',
            'data'
        ]);

        // Mode Perceraian
        $responseCerai = $this->getJson('/api/public-map?tahun=2025&jenis_statistik=perceraian');
        $responseCerai->assertStatus(200);
        $responseCerai->assertJsonStructure([
            'success',
            'tahun',
            'jenis_statistik',
            'data'
        ]);
    }

    /**
     * Test ekspor CSV publik untuk pernikahan dan perceraian
     */
    public function test_public_csv_exports()
    {
        // Export CSV Pernikahan
        $responseNikah = $this->get('/pengguna/export-pernikahan?tahun=2025');
        $responseNikah->assertStatus(200);
        $this->assertStringContainsString('text/csv', $responseNikah->headers->get('content-type'));

        // Export CSV Perceraian
        $responseCerai = $this->get('/pengguna/export-perceraian?tahun=2025');
        $responseCerai->assertStatus(200);
        $this->assertStringContainsString('text/csv', $responseCerai->headers->get('content-type'));
    }

    /**
     * Test bahwa route admin /admin/dashboard terlindungi dan login mengalihkan ke /admin/dashboard
     */
    public function test_admin_routes_remain_intact()
    {
        // Akses guest ke dashboard admin (/admin/dashboard) harus redirect ke login
        $resGuestDashboard = $this->get('/admin/dashboard');
        $resGuestDashboard->assertRedirect(route('login'));

        // Akses guest ke kelola data pernikahan admin harus redirect ke login
        $resGuest = $this->get('/admin/data-pernikahan');
        $resGuest->assertRedirect(route('login'));

        // Admin yang login dapat mengakses dashboard admin dengan sukses
        $resAdmin = $this->withSession([
            'is_logged_in' => true,
            'user_name' => 'Administrator',
            'user_identifier' => 'admin@nikah.info'
        ])->get('/admin/dashboard');
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Dashboard Analytics & Persebaran Data Pernikahan Indonesia');
    }

    /**
     * Test bahwa ketika admin berhasil login, aplikasi otomatis redirect ke /admin/dashboard
     */
    public function test_login_redirects_to_admin_dashboard()
    {
        $response = $this->post('/login', [
            'identifier' => 'admin@nikah.info',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/admin/dashboard');
    }
}
