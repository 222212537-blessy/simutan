<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $filePath = base_path('app/Console/Commands/pegawai.csv');

        // Baca file, abaikan baris baru dan lewati baris kosong secara otomatis
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (count($lines) < 2) {
            $this->command->info('CSV file is empty or only contains a header.');
            return; // Keluar jika file kosong atau hanya berisi header
        }

        $header = str_getcsv(array_shift($lines)); // Ambil dan parse header

        foreach ($lines as $line) {
            $row = str_getcsv($line);

            // PENTING: Cek apakah jumlah kolom cocok sebelum combine
            if (count($header) !== count($row)) {
                $this->command->warn('Skipping malformed row: ' . $line);
                continue; // Lewati baris ini dan lanjut ke baris berikutnya
            }

            $rowData = array_combine($header, $row);

            User::create([
                'name' => $rowData['name'],
                'panggilan' => $rowData['panggilan'],
                'role' => $rowData['role'],
                'username' => $rowData['username'],
                'password' => Hash::make($rowData['password']), // Hash password
                'ttd' => $rowData['ttd'],
                'foto' => $rowData['foto'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}