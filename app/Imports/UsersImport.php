<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'id' => $row['id'],
            'name' => $row['name'],
            'role' => $row['role'],
            'username' => $row['username'],
            'ttd' => $row['ttd'],
            'foto' => $row['foto'],
            'password' => Hash::make($row['password']),

        ]);
    }
}
