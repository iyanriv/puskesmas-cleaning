<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

function testRoute($route_name, $user_id, $params = []) {
    try {
        $user = User::find($user_id);
        auth()->login($user);
        $url = route($route_name, $params);
        $request = Illuminate\Http\Request::create($url, 'GET');
        $response = app()->handle($request);
        if ($response->getStatusCode() == 200) {
            echo "✅ $route_name (User: {$user->peran->nama_peran}) - OK\n";
        } else {
            echo "❌ $route_name (User: {$user->peran->nama_peran}) - Failed with status {$response->getStatusCode()}\n";
        }
    } catch (\Exception $e) {
        echo "❌ $route_name (User: {$user->peran->nama_peran}) - Error: " . $e->getMessage() . "\n";
    }
}

echo "Mulai testing rute...\n\n";

$admin = 1;
$spv = 2;
$cs = 3;
$gdg = 4;

// Dasbor
testRoute('dasbor.cs', $cs);
testRoute('dasbor.supervisor', $spv);
testRoute('dasbor.gudang', $gdg);
testRoute('dasbor.admin', $admin);

// Modul 5: Penilaian
testRoute('penilaian.index', $spv);
testRoute('penilaian.buat', $spv, ['id' => $cs]);
testRoute('penilaian.detail', $spv, ['id' => $cs]);
testRoute('penilaian.rekap', $spv);

// Modul 6: Laporan
testRoute('laporan.index', $spv);

// Modul 4: Sampah
testRoute('sampah.buat', $cs);
testRoute('sampah.rekapan', $spv);

// Modul 3: Barang
testRoute('barang.katalog', $cs);
testRoute('barang.gudang', $gdg);

echo "\nSelesai testing.\n";
