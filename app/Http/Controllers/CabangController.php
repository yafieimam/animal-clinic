<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Cabang;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CabangController extends Controller
{
    // Require Login
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $count = Cabang::count();
        $cabang = Cabang::all();
        return view('master.cabang.index', compact('cabang', 'count'));
    }

    public function create()
    {
        return view('master.cabang.create');
    }

    public function store(Request $request)
    {

        // dd($request->all());

        $this->validate(
            $request,
            [

                'cabang_code'       => 'required|unique:cabang|min:2|max:3',
                'cabang_name'       => 'required|unique:cabang',
                'cabang_notelp'     => 'required',
                'cabang_alamat'     => 'required',
                'cabang_jambuka'     => 'required',
                'cabang_jamtutup'     => 'required',
            ],
            [
                'cabang_code.required'      => 'Kode Cabang Wajib Di isi',
                'cabang_code.unique'        => 'Kode sudah ada',
                'cabang_code.min'           => 'Kode Cabang Minimal 2 Karakter',
                'cabang_code.max'           => 'Kode Cabang Maksimal 3 Karakter',
                'cabang_name.required'      => 'Lokasi Cabang Wajib Di isi',
                'cabang_alamat.required'    => 'Alamat Lengkap Wajib Di isi',
                'cabang_name.unique'        => 'Nama Cabang sudah ada',
                'cabang_notelp.required'    => 'No Telp Wajib Di isi',
                'cabang_jambuka.required'    => 'Jam Buka Wajib Di isi',
                'cabang_jamtutup.required'    => 'Jam Tutup Wajib Di isi',
            ]
        );

        $increment = DB::table('cabang')->max('cabang_id')+1;

        $tambah = new Cabang();
        $tambah->cabang_code = $request->get('cabang_code');
        $tambah->cabang_name = $request->get('cabang_name');
        $tambah->cabang_alamat = $request->get('cabang_alamat');
        $tambah->cabang_notelp = $request->get('cabang_notelp');
        $tambah->cabang_jambuka = $request->get('cabang_jambuka');
        $tambah->cabang_jamtutup = $request->get('cabang_jamtutup');
        $tambah->created_by = Auth::user()->name;
        $tambah->updated_by = Auth::user()->name;
        $tambah->save();

        return redirect()->route('cabang_index')->with('info', 'Cabang Created Successfully.');
    }

    public function show(cabang $cabang, $cabang_code)
    {
        $decryptID = Crypt::decryptString($cabang_code);
        $cabang = Cabang::where('cabang_code', $decryptID)->first();
        return view('master.cabang.show')->with('cabang', $cabang);
    }

    public function edit(cabang $cabang, $cabang_id)
    {
        $decryptID = Crypt::decryptString($cabang_id);
        $cabang = Cabang::where('cabang_id', $decryptID)->first();
        return view('master.cabang.edit', ['cabang'=> $cabang]);
    }

    public function update(Request $request, $cabang_id)
    {

         $this->validate(
            $request,
            [

                'cabang_code'       => 'required|min:2|max:3',
                'cabang_name'       => 'required',
                'cabang_notelp'     => 'required',
                'cabang_alamat'     => 'required',
            ],
            [
                'cabang_code.required'      => 'Kode Cabang Wajib Di isi',
                'cabang_code.min'           => 'Kode Cabang Minimal 2 Karakter',
                'cabang_code.max'           => 'Kode Cabang Maksimal 3 Karakter',
                'cabang_name.required'      => 'Lokasi Cabang Wajib Di isi',
                'cabang_alamat.required'    => 'Alamat Lengkap Wajib Di isi',
                'cabang_notelp.required'    => 'No Telp Wajib Di isi',
            ]
        );

        $cabang = cabang::find($cabang_id);
        $cabang->cabang_code = $request->get('cabang_code');
        $cabang->cabang_name = $request->get('cabang_name');
        $cabang->cabang_alamat = $request->get('cabang_alamat');
        $cabang->cabang_notelp = $request->get('cabang_notelp');
        $cabang->cabang_jambuka = $request->get('cabang_jambuka');
        $cabang->cabang_jamtutup = $request->get('cabang_jamtutup');
        $cabang->updated_by = Auth::user()->name;
        $cabang->update();

        return redirect()->route('cabang_index')->with('update', 'Update');
    }

    public function destroy($cabang_id)
    {
        $cabang = Cabang::find($cabang_id);
        $cabang->delete();
        $cabang->deleted_by = Auth::user()->name;
        $cabang->save();
        return redirect()->route('cabang_index')->with('hapus', 'Cabang Deleted Successfully.');
    }

    public function trash()
    {
            $cabang = Cabang::onlyTrashed()->get();
            return view('master.cabang.trash', ['cabang' => $cabang]);
    }
}
