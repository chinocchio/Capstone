<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Session;

class DuplicatesExport implements FromCollection, WithHeadings
{
    protected $duplicateRows;

    public function __construct(Collection $duplicateRows)
    {
        $this->duplicateRows = $duplicateRows;
    }

    /**
     * Return the collection of duplicated rows.
     */
    public function collection()
    {
        return $this->duplicateRows;
    }

    /**
     * Dynamically return the headings for the Excel sheet based on the import.
     */
    public function headings(): array
    {
        // Retrieve the headers from the session
        return Session::get('import_headers', ['student_number', 'name', 'email', 'section']); // Fallback if headers not found
    }
}
