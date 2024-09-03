<?php

namespace App\Exports;

use App\Models\Binatang;
use App\Models\Branch;
use App\Models\TypeObat;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BranchExport implements FromQuery, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    public function headings(): array
    {
        return [
            'id',
            'kode',
            'lokasi',
            'alamat',
            'lat',
            'long',
            'telpon',
            'open_time',
            'close_time',
            'open_holiday_time',
            'close_holiday_time',
            'branch_supervisor',
            'hari_libur',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ];
    }

    public function query()
    {
        return Branch::query();
    }
}
