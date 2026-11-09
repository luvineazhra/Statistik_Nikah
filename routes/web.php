<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Controllers\MapApiController;
use App\Http\Controllers\ProvinsiController;
use App\Http\Controllers\FaktorPerceraianController;
use App\Http\Controllers\DataPernikahanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 1. Route Utama Publik ('/') -> PublicController@index (Guest Access)
| 2. Grup Route Admin / Dashboard ('/admin') -> Dilindungi middleware 'auth'
| 3. Autentikasi & Pengalihan Login -> Redirect ke /admin/dashboard
|
*/

// =========================================================================
// 1. ROUTE UTAMA PUBLIK & DATA TERBUKA (GUEST ACCESS - TANPA LOGIN)
// =========================================================================

// Halaman Utama Portal Data Publik
Route::get('/', [PublicController::class, 'index'])->name('public.dashboard');
Route::get('/pengguna', [PublicController::class, 'index'])->name('pengguna');

// Endpoint API Publik (Statistik Interaktif & Peta Choropleth)
Route::get('/api/public-stats', [PublicPortalController::class, 'getStatsJson'])->name('api.public-stats');
Route::get('/api/public-map', [PublicPortalController::class, 'getMapData'])->name('api.public-map');
Route::get('/api/map-data', [MapApiController::class, 'getData'])->name('api.map-data');
Route::get('/pengguna/export-pernikahan', [PublicPortalController::class, 'exportPernikahan'])->name('pengguna.export-pernikahan');
Route::get('/pengguna/export-perceraian', [PublicPortalController::class, 'exportPerceraian'])->name('pengguna.export-perceraian');

// Detail Statistik Provinsi Publik (by Slug)
Route::get('/statistik/provinsi/{slug}', [ProvinsiController::class, 'show'])->name('provinsi.detail');
Route::get('/provinsi/{slug}', [ProvinsiController::class, 'show'])->where('slug', '^[a-zA-Z0-9\-_]+$')->name('provinsi.detail.alias');

// API Publik Faktor Perceraian
Route::get('/api/faktor-perceraian', function (Request $request) {
    $provinsi = $request->input('provinsi');
    $tahun = $request->input('tahun');

    $query = DB::table('faktor_perceraian')
        ->select('faktor_penyebab', DB::raw('SUM(jumlah_kasus) as total'));

    if ($provinsi && $provinsi !== 'Semua Provinsi' && $provinsi !== '') {
        $query->where('provinsi', $provinsi);
    }
    if ($tahun && $tahun !== 'Semua Tahun' && $tahun !== '') {
        $query->where('tahun', $tahun);
    }

    $records = $query->groupBy('faktor_penyebab')
        ->orderByDesc('total')
        ->get();

    $totalKasus = $records->sum('total');
    $topFaktor = $records->first();

    $colors = [
        '#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6',
        '#F97316', '#EC4899', '#6366F1', '#14B8A6', '#64748B'
    ];

    $items = [];
    foreach ($records as $index => $rec) {
        $pct = $totalKasus > 0 ? round(($rec->total / $totalKasus) * 100, 1) : 0;
        $items[] = [
            'faktor' => $rec->faktor_penyebab,
            'total' => (int) $rec->total,
            'total_formatted' => number_format((int) $rec->total, 0, ',', '.'),
            'persentase' => $pct,
            'color' => $colors[$index % count($colors)],
        ];
    }

    return response()->json([
        'status' => 'success',
        'provinsi' => $provinsi ?: 'Nasional',
        'tahun' => $tahun ?: '2023–2025',
        'total_kasus' => $totalKasus,
        'total_kasus_formatted' => number_format($totalKasus, 0, ',', '.'),
        'top_faktor' => $topFaktor ? [
            'nama' => $topFaktor->faktor_penyebab,
            'total' => (int) $topFaktor->total,
            'total_formatted' => number_format((int) $topFaktor->total, 0, ',', '.'),
            'persentase' => $totalKasus > 0 ? round(($topFaktor->total / $totalKasus) * 100, 1) : 0
        ] : null,
        'breakdown' => $items
    ]);
})->name('api.faktor-perceraian');

// Ekspor Data Pernikahan Legacy Route (Pemeriksaan sesi & abort 403 ditangani di DataPernikahanController)
Route::get('/data-pernikahan/export', [DataPernikahanController::class, 'export'])->name('data-pernikahan.export');


// =========================================================================
// 2. AUTENTIKASI ADMINISTRATOR & PENGALIHAN SETELAH LOGIN
// =========================================================================

// Halaman Login GET: Jika sudah login, langsung alihkan ke /admin/dashboard
Route::get('/login', function () {
    if (session()->get('is_logged_in') || Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('login');
})->name('login');

// Proses Login POST: Simpan session lalu redirect otomatis ke /admin/dashboard
Route::post('/login', function (Request $request) {
    $identifier = $request->input('identifier', 'Drs. Hendra Irawan, M.Si');
    
    // Format nama agar rapi jika input berupa email atau username
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $parts = explode('@', $identifier);
        $displayName = ucwords(str_replace(['.', '_', '-'], ' ', $parts[0]));
    } else {
        $displayName = ucwords($identifier);
    }

    session([
        'is_logged_in' => true,
        'user_name' => $displayName,
        'user_identifier' => $identifier
    ]);

    return redirect()->route('admin.dashboard');
})->name('login.post');

// Logout POST/GET
Route::any('/logout', function () {
    session()->forget(['is_logged_in', 'user_name', 'user_identifier']);
    Auth::logout();
    return redirect()->route('login');
})->name('logout');


// =========================================================================
// 3. GRUP ROUTE ADMIN / DASHBOARD ('/admin') - MIDDLEWARE AUTH
// =========================================================================

Route::middleware(['auth'])->group(function () {

    // Prefix '/admin' untuk semua menu manajemen administrator
    Route::prefix('admin')->group(function () {
        
        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Kelola Data Pernikahan (CRUD & Manajemen Data)
        Route::get('/data-pernikahan', [DataPernikahanController::class, 'index'])->name('admin.data-pernikahan.index');
        Route::post('/data-pernikahan', [DataPernikahanController::class, 'store'])->name('admin.data-pernikahan.store');
        Route::put('/data-pernikahan/{id}', [DataPernikahanController::class, 'update'])->whereNumber('id')->name('admin.data-pernikahan.update');
        Route::delete('/data-pernikahan/{id}', [DataPernikahanController::class, 'destroy'])->whereNumber('id')->name('admin.data-pernikahan.destroy');
        Route::get('/data-pernikahan/api/{id}/detail', [DataPernikahanController::class, 'detailJson'])->whereNumber('id')->name('admin.data-pernikahan.detail-json');
        Route::get('/data-pernikahan/export', [DataPernikahanController::class, 'export'])->name('admin.data-pernikahan.export');

        // Kelola Data Perceraian & Faktor (CRUD)
        Route::get('/faktor-perceraian', [FaktorPerceraianController::class, 'index'])->name('admin.faktor-perceraian.index');
        Route::post('/faktor-perceraian', [FaktorPerceraianController::class, 'store'])->name('admin.faktor-perceraian.store');
        Route::put('/faktor-perceraian/{id}', [FaktorPerceraianController::class, 'update'])->name('admin.faktor-perceraian.update');
        Route::delete('/faktor-perceraian/{id}', [FaktorPerceraianController::class, 'destroy'])->name('admin.faktor-perceraian.destroy');
        Route::get('/nikah-agama', function () {
            return redirect()->route('admin.faktor-perceraian.index');
        })->name('admin.nikah-agama.index');

        // Kelola Provinsi (CRUD & Master Wilayah)
        Route::get('/provinsi', [ProvinsiController::class, 'index'])->name('admin.provinsi.index');
        Route::post('/provinsi', [ProvinsiController::class, 'store'])->name('admin.provinsi.store');
        Route::put('/provinsi/{id}', [ProvinsiController::class, 'update'])->whereNumber('id')->name('admin.provinsi.update');
        Route::patch('/provinsi/{id}/toggle-status', [ProvinsiController::class, 'toggleStatus'])->whereNumber('id')->name('admin.provinsi.toggle-status');
        Route::delete('/provinsi/{id}', [ProvinsiController::class, 'destroy'])->whereNumber('id')->name('admin.provinsi.destroy');
        Route::get('/provinsi/api/{id}/detail', [ProvinsiController::class, 'detailJson'])->whereNumber('id')->name('admin.provinsi.detail-json');

        // Kelola Faktor & Sumber
        Route::get('/faktor-sumber', function () {
            return view('faktor-sumber');
        })->name('admin.faktor-sumber');

        // Profil Admin
        Route::get('/profil', function () {
            return view('profil');
        })->name('admin.profil');

        Route::post('/profil', function (Request $request) {
            $name = $request->input('name', session('user_name', 'Drs. Hendra Irawan, M.Si'));
            $identifier = $request->input('identifier', session('user_identifier', 'admin@nikah.info'));
            
            session([
                'user_name' => $name,
                'user_identifier' => $identifier
            ]);
            
            return redirect()->route('admin.profil')->with('success', 'Profil administrator berhasil diperbarui.');
        })->name('admin.profil.post');
    });

    // Alias routes untuk kompatibilitas tautan internal & testing
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    });

    // Kelola Data Pernikahan Alias
    Route::get('/data-pernikahan', [DataPernikahanController::class, 'index'])->name('data-pernikahan');
    Route::post('/data-pernikahan', [DataPernikahanController::class, 'store'])->name('data-pernikahan.store');
    Route::put('/data-pernikahan/{id}', [DataPernikahanController::class, 'update'])->whereNumber('id')->name('data-pernikahan.update');
    Route::delete('/data-pernikahan/{id}', [DataPernikahanController::class, 'destroy'])->whereNumber('id')->name('data-pernikahan.destroy');
    Route::get('/data-pernikahan/api/{id}/detail', [DataPernikahanController::class, 'detailJson'])->whereNumber('id')->name('data-pernikahan.detail-json');

    // Kelola Data Perceraian & Faktor Alias
    Route::get('/faktor-perceraian', [FaktorPerceraianController::class, 'index'])->name('faktor-perceraian');
    Route::post('/faktor-perceraian', [FaktorPerceraianController::class, 'store'])->name('faktor-perceraian.store');
    Route::put('/faktor-perceraian/{id}', [FaktorPerceraianController::class, 'update'])->name('faktor-perceraian.update');
    Route::delete('/faktor-perceraian/{id}', [FaktorPerceraianController::class, 'destroy'])->name('faktor-perceraian.destroy');
    Route::get('/nikah-agama', function () {
        return redirect()->route('admin.faktor-perceraian.index');
    })->name('nikah-agama');

    // Kelola Provinsi Alias
    Route::get('/provinsi', [ProvinsiController::class, 'index'])->name('provinsi');
    Route::post('/provinsi', [ProvinsiController::class, 'store'])->name('provinsi.store');
    Route::put('/provinsi/{id}', [ProvinsiController::class, 'update'])->whereNumber('id')->name('provinsi.update');
    Route::patch('/provinsi/{id}/toggle-status', [ProvinsiController::class, 'toggleStatus'])->whereNumber('id')->name('provinsi.toggle-status');
    Route::delete('/provinsi/{id}', [ProvinsiController::class, 'destroy'])->whereNumber('id')->name('provinsi.destroy');
    Route::get('/provinsi/api/{id}/detail', [ProvinsiController::class, 'detailJson'])->whereNumber('id')->name('provinsi.detail-json');

    // Kelola Faktor & Sumber Alias
    Route::get('/faktor-sumber', function () {
        return view('faktor-sumber');
    })->name('faktor-sumber');

    // Profil Admin Alias
    Route::get('/profil', function () {
        return view('profil');
    })->name('profil');

    Route::post('/profil', function (Request $request) {
        $name = $request->input('name', session('user_name', 'Drs. Hendra Irawan, M.Si'));
        $identifier = $request->input('identifier', session('user_identifier', 'admin@nikah.info'));
        
        session([
            'user_name' => $name,
            'user_identifier' => $identifier
        ]);
        
        return redirect()->route('profil')->with('success', 'Profil administrator berhasil diperbarui.');
    })->name('profil.post');
});
