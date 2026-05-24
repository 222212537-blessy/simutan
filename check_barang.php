<?php
require __DIR__ . '/bootstrap/app.php';

$barang = \App\Models\Barang::where('kode', '1010301001000200')->first();

if ($barang) {
    echo "Kode: " . $barang->kode . "\n";
    echo "Nama: " . $barang->nama . "\n";
    echo "Foto Barang (DB): " . ($barang->foto_barang ?? 'NULL') . "\n";
} else {
    echo "Barang tidak ditemukan\n";
}
?>
