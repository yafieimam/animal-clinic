<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\RekamMedisPasien;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function resetPendaftaran(Request $req)
    {
        if ($req->pass != 'Biawak12345') return false;

        RekamMedisPasien::where('id', '!=', 0)->delete();
        Pendaftaran::where('id', '!=', 0)->delete();

        return 'Success';
    }
}
