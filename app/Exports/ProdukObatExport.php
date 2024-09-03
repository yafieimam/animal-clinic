<?php

namespace App\Exports;

use App\Models\ProdukObat;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProdukObatExport implements FromQuery, WithHeadings
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
            'name',
            'dosis',
            'kategori_obat_id',
            'type_obat_id',
            'satuan_obat_id',
            'harga',
            'description',
            'diskon',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ];
    }

    public function query()
    {
        return ProdukObat::query();
    }
}
