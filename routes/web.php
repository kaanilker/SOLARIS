<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolarDataController;
use App\Http\Controllers\LLMController;

// 1. Canlı Veri - Ana Sayfa
Route::get('/', function () {
    return view('pages.canli-veri');
})->name('home');

// 2. Hesaplama Sayfası
Route::get('/hesaplama', function () {
    return view('pages.hesaplama');
})->name('hesaplama');

// 3. Geçmiş - Tarihi Fırtınalar
Route::get('/gecmis', function () {
    return view('pages.gecmis');
})->name('gecmis');

// 4. Hakkımızda Sayfası
Route::get('/hakkimizda', function () {
    return view('pages.hakkimizda');
})->name('hakkimizda');

// ═══════════════════════════════════════════════════════════════════════════
// API ROUTES - Solar Data Proxy
// ═══════════════════════════════════════════════════════════════════════════

// Fırtına verileri (statik JSON)
Route::get('/api/firtinalar', function () {
    $candidatePaths = [
        public_path('data/firtinalar.json'),
        base_path('public/data/firtinalar.json'),
    ];

    foreach ($candidatePaths as $path) {
        if (file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded)) {
                return response()->json($decoded);
            }
        }
    }

    return response()->json(['error' => 'Veri bulunamadı'], 404);
});

// NOAA / SWPC - Anlık akışlar (JSON proxy)
Route::get('/api/solar/plasma', [SolarDataController::class, 'getPlasma']);
Route::get('/api/solar/mag', [SolarDataController::class, 'getMag']);
Route::get('/api/solar/xray', [SolarDataController::class, 'getXray']);
Route::get('/api/solar/protons', [SolarDataController::class, 'getProtons']);
Route::get('/api/solar/k-index', [SolarDataController::class, 'getKIndex']);
Route::get('/api/solar/f107', [SolarDataController::class, 'getF107']);

// Kyoto WDC - Dst İndeksi (HTML parse)
Route::get('/api/solar/dst', [SolarDataController::class, 'getDst']);

// Kyoto WDC - SYM-H, ASY-H, AE İndeksleri (HTML parse)
Route::get('/api/solar/ae-indices', [SolarDataController::class, 'getAeIndices']);

// GFZ Potsdam - Kp nowcast (JSON proxy)
Route::get('/api/solar/kp-nowcast', [SolarDataController::class, 'getKpNowcast']);

// NOAA SWPC - Geoelectric 1D (JSON proxy)
Route::get('/api/solar/geoelectric', [SolarDataController::class, 'getGeoelectric']);

// DTU Space - PC İndeksi (FTP parse)
Route::get('/api/solar/pc', [SolarDataController::class, 'getPcIndex']);

// NASA CDDIS - TEC Verileri (IONEX parse)
Route::get('/api/solar/tec', [SolarDataController::class, 'getTec']);

// Tüm backend verileri tek seferde (Dashboard için)
Route::get('/api/solar/all', [SolarDataController::class, 'getAllData']);

// AWS Bedrock Analiz API (LLM Operasyonları)
Route::post('/api/analyze-storm', [LLMController::class, 'analyze'])->name('analyze.storm');

