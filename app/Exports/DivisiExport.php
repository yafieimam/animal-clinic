<?php

namespace App\Exports;

use App\Models\Binatang;
use App\Models\Branch;
use App\Models\Divisi;
use App\Models\TypeObat;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DivisiExport implements FromQuery, WithHeadings
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
        return Divisi::query();
    }
}
