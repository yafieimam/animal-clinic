<?php

namespace App\Exports;

use App\Models\Binatang;
use App\Models\Branch;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Kota;
use App\Models\Provinsi;
use App\Models\TypeObat;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KotaExport implements FromQuery, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    public function headings(): array
    {
        return [
            'id',
            'province_id',
            'name',
            'meta',
            'created_at',
            'updated_at'
        ];
    }

    public function query()
    {
        return Kota::query();
    }
}
