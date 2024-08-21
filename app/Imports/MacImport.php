<?php

namespace App\Imports;

use App\Models\Mac;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MacImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            $generatedCode = mt_rand(1111111111,9999999999);

            Mac::create([
                'mac_number' => $row['mac_number'],
                'qr' => $row['qr'] ?? $generatedCode
            ]);
        }
    }
}
