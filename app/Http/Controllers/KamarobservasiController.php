<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Kamarobservasi;
use App\Cabang;
use App\Kamarkategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class KamarobservasiController extends Controller
{
    // Require Login
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $count = Kamarobservasi::count();
        $kamarobservasi = DB::table('kamarobservasi')
            ->leftjoin('kamarkategori','kamarkategori.kamarkategori_id','kamarobservasi.kamarkategori_id')
            ->leftjoin('cabang','cabang.cabang_id','kamarobservasi.kamarobservasi_cabang')
            ->get();
        return view('master.kamarobservasi.index', compact('kamarobservasi','count'));
    }

    public function create()
    {

        $cabang = Cabang::all();
        $kamarkategori = Kamarkategori::all();
        return view('master.kamarobservasi.create', compact('cabang','kamarkategori'));
    }

    public function store(Request $request)
    {

        $this->validate(
            $request,
            [

                'kamarobservasi_name'       => 'required|unique:kamarobservasi',
                'kamarkategori_id'       => 'required',
                'kamarobservasi_deskripsi'       => 'required',
                'kamarobservasi_cabang'       => 'required',
                'kamarobservasi_kapasitas'       => 'required',
                'kamarobservasi_harga'       => 'required',
            ],
            [
                'kamarobservasi_name.required'      => 'Ruang Rawat Inap Wajib Di isi',
                'kamarobservasi_name.unique'        => 'Ruang Rawat Inap sudah ada',
                'kamarkategori_id.required'      => 'Kategori Ruang Rawat Inap Wajib Di isi',
                'kamarobservasi_deskripsi.required'      => 'Deskripsi Wajib Di isi',
                'kamarobservasi_cabang.required'      => 'Cabang Wajib Di isi',
                'kamarobservasi_kapasitas.required'      => 'Kapasitas Wajib Di isi',
                'kamarobservasi_harga.required'      => 'Harga Ruang Rawat Inap Wajib Di isi',

            ]
        );


        $increment = DB::table('kamarobservasi')->max('kamarobservasi_id')+1;

        $tambah = new Kamarobservasi();
        $tambah->kamarobservasi_name = $request->get('kamarobservasi_name');
        $tambah->kamarkategori_id = $request->get('kamarkategori_id');
        $tambah->kamarobservasi_deskripsi = $request->get('kamarobservasi_deskripsi');
        $tambah->kamarobservasi_cabang = $request->get('kamarobservasi_cabang');
        $tambah->kamarobservasi_kapasitas = $request->get('kamarobservasi_kapasitas');
        $tambah->kamarobservasi_harga = $request->get('kamarobservasi_harga');
        $tambah->created_by = Auth::user()->name;
        $tambah->updated_by = Auth::user()->name;
        $tambah->kamarobservasi_updated_at = date('Y-m-d H:i:s');
        $tambah->kamarobservasi_created_at = date('Y-m-d H:i:s');
        $tambah->save();

        return redirect()->route('kamarobservasi_index')->with('info', 'kamarobservasi Created Successfully.');
    }

     public function show(kamarobservasi $kamarobservasi, $kamarobservasi_id)
    {
        // $kamarobservasi = Kamarobservasi::where('kamarobservasi_id', $kamarobservasi_id)->first();
        // $decryptID = Crypt::decryptString($cabang_code);
        // $decryptID = Crypt::decryptString($kamarkategori_id);
         $kamarobservasi = DB::table('kamarobservasi')
                        ->leftjoin('kamarkategori','kamarkategori.kamarkategori_id','kamarobservasi.kamarkategori_id')
                        ->leftjoin('cabang','cabang.cabang_id','kamarobservasi.kamarobservasi_cabang')
                        ->first();

        return view('master.kamarobservasi.show')->with('kamarobservasi', $kamarobservasi);
    }

    public function edit(kamarobservasi $kamarobservasi, $kamarobservasi_id)
    {
        $decryptID = Crypt::decryptString($kamarobservasi_id);
        $kamarobservasi = Kamarobservasi::where('kamarobservasi_id', $decryptID)->first();
        $kamarkategori = Kamarkategori::all();
        $cabang = cabang::all();

        return view('master.kamarobservasi.edit',
            [
                'kamarobservasi'=> $kamarobservasi,
                'kamarkategori'=> $kamarkategori,
                'cabang'=> $cabang
            ]
        );
    }

    public function update(Request $request, $kamarobservasi_id)
    {
        $kamarobservasi = Kamarobservasi::find($kamarobservasi_id);
        $kamarobservasi->kamarobservasi_name = $request->get('kamarobservasi_name');
        $kamarobservasi->kamarkategori_id = $request->get('kamarkategori_id');
        $kamarobservasi->kamarobservasi_deskripsi = $request->get('kamarobservasi_deskripsi');
        $kamarobservasi->kamarobservasi_cabang = $request->get('kamarobservasi_cabang');
        $kamarobservasi->kamarobservasi_kapasitas = $request->get('kamarobservasi_kapasitas');
        $kamarobservasi->kamarobservasi_harga = $request->get('kamarobservasi_harga');
        $kamarobservasi->updated_by = Auth::user()->name;
        $kamarobservasi->kamarobservasi_updated_at = date('Y-m-d H:i:s');
        $kamarobservasi->update();

        return redirect()->route('kamarobservasi_index')->with('update', 'Update');
    }

    public function destroy($kamarobservasi_id)
    {
        $kamarobservasi = Kamarobservasi::find($kamarobservasi_id);
        $kamarobservasi->delete();
        $kamarobservasi->deleted_by = Auth::user()->name;
        $kamarobservasi->save();
        return redirect()->route('kamarobservasi_index')->with('hapus', 'kamarobservasi Deleted Successfully.');
    }

    public function trash()
    {
            $kamarobservasi = Kamarobservasi::onlyTrashed()->get();
            return view('master.kamarobservasi.trash', ['kamarobservasi' => $kamarobservasi]);
    }
}
