<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportBarang extends Command
{
    protected $signature = 'import:barang';
    protected $description = 'Import barang data from a CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $csvFile = base_path('app/Console/Commands/barang.csv');

        // Baca file CSV
        $reader = Reader::createFromPath($csvFile, 'r');
        $reader->setHeaderOffset(0); // Set header offset
        
        // Pilih data dari CSV
        $records = (new Statement())->process($reader);

        // Iterate over the records and insert them into the database
        foreach ($records as $record) {
            // Validasi data sebelum insert
            if (!empty($record['id']) && !empty($record['kode']) && !empty($record['nama'])) {
                DB::table('barangs')->updateOrInsert(
                    ['id' => $record['id']], // Asumsi 'id' adalah primary key
                    [
                        'kode' => $record['kode'],
                        'nama' => $record['nama'],
                        'kelompok_id' => !empty($record['kelompok_id']) ? (int)$record['kelompok_id'] : null, // Handle optional fields
                        'qty_item' => !empty($record['qty_item']) ? (int)$record['qty_item'] : 0, // Default to 0 if empty
                        'satuan' => $record['satuan'] ?? 'Unknown', // Default to 'Unknown' if empty
                        'foto_barang' => $record['foto_barang'], // Handle optional fields, use 'default.png' if empty
                        'created_at' => now(),
                        'updated_at' => now(),
                        'harga_total' => !empty($record['harga_total']) ? (int)$record['qty_item'] : 0
                    ]
                );
            } else {
                $this->error("Data invalid or missing ID for record: " . json_encode($record));
            }
        }

        $this->info('Barang data imported successfully!');
    }
}
