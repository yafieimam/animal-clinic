<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class OwnerExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.excel_owner', [
            'data' => $this->data,
            'tgl' => Carbon::now()->format('d/m/Y H:i')
        ]);
    }
}
