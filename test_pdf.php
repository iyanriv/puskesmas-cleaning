<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PermintaanBarang;
use Barryvdh\DomPDF\Facade\Pdf;

$p = PermintaanBarang::with(['pengguna.peran', 'pengguna.area', 'barang'])->find(6);
$semuaBarang = $p->itemsSatuTransaksi();

echo "ID Permintaan: {$p->id}\n";
echo "Kode Transaksi: {$p->kode_transaksi}\n";
echo "Pemohon: {$p->pengguna->name}\n";
echo "Jumlah barang dalam transaksi: " . count($semuaBarang) . "\n";
foreach ($semuaBarang as $b) {
    echo "- {$b->barang->nama_barang} ({$b->jumlah} {$b->barang->satuan}) | Status: {$b->status_request}\n";
}

$pdf = Pdf::loadView('barang.bukti', ['permintaan' => $p, 'semuaBarang' => $semuaBarang])
    ->setPaper('a4', 'portrait');

$content = $pdf->output();
echo "PDF berhasil digenerate! Ukuran: " . strlen($content) . " bytes\n";
file_put_contents(__DIR__ . '/public/test_bukti_transaksi.pdf', $content);
echo "File tersimpan di public/test_bukti_transaksi.pdf\n";
