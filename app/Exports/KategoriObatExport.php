<?php

namespace App\Exports;

use App\Models\KategoriObat;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KategoriObatExport implements FromQuery, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    public function headings(): array
    {
        return [
            'id',
            'name',
            'description',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ];
    }

    public function query()
    {
        return KategoriObat::query();
    }
}
