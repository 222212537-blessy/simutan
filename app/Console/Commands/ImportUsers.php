<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportUsers extends Command
{
    protected $signature = 'import:users';
    protected $description = 'Import users from a CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $csvFile = base_path('app/Console/Commands/pegawai.csv');
        
        // Read the CSV file
        $reader = Reader::createFromPath($csvFile, 'r');
        $reader->setHeaderOffset(0); // Set the header offset
        $records = (new Statement())->process($reader);

        // Iterate over the records and insert them into the database
        foreach ($records as $record) {
            DB::table('users')->updateOrInsert(
                ['id' => $record['id']], // Assume 'id' is the primary key
                [
                    'name' => $record['name'],
                    'panggilan' => $record['panggilan'],
                    'role' => $record['role'],
                    'username' => $record['username'],
                    'password' => bcrypt($record['password']),
                    'ttd' => $record['ttd'],
                    'foto' => $record['foto']
                ]
            );
        }

        $this->info('Users imported successfully!');
    }
}
