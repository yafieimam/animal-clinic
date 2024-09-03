<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Obatkategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ObatkategoriController extends Controller
{
    // Require Login
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $obatkategori = Obatkategori::all();
        $count = Obatkategori::count();
        return view('master.obatkategori.index', compact('obatkategori','count'));
    }

    public function create()
    {
        return view('master.obatkategori.create');
    }

    public function store(Request $request)
    {

        $this->validate(
            $request,
            [
                'obatkategori_name'             => 'required|unique:obatkategori',
            ],
            [
                'obatkategori_name.required'    => 'Kategori Obat Wajib Di isi',
                'obatkategori_name.unique'      => 'Kategori Obat sudah ada',
            ]
        );

        $increment = DB::table('obatkategori')->max('obatkategori_id')+1;

        $tambah = new obatkategori();
        $tambah->obatkategori_name = $request->get('obatkategori_name');
        $tambah->created_by = Auth::user()->name;
        $tambah->updated_by = Auth::user()->name;
        $tambah->save();

        return redirect()->route('obatkategori_index')->with('info', 'obatkategori Created Successfully.');
    }

     public function show(obatkategori $obatkategori, $obatkategori_id)
    {
        $decryptID = Crypt::decryptString($obatkategori_id);
        $obatkategori = Obatkategori::where('obatkategori_id', $decryptID)->first();
        return view('master.obatkategori.show')->with('obatkategori', $obatkategori);
    }

    public function edit(obatkategori $obatkategori, $obatkategori_id)
    {
        $decryptID = Crypt::decryptString($obatkategori_id);
        $obatkategori = Obatkategori::where('obatkategori_id', $decryptID)->first();
        return view('master.obatkategori.edit', ['obatkategori'=> $obatkategori]);
    }

    public function update(Request $request, obatkategori $obatkategori)
    {
        //
    }

    public function destroy($obatkategori_id)
    {
        $obatkategori = Obatkategori::find($obatkategori_id);
        $obatkategori->delete();
        $obatkategori->deleted_by = Auth::user()->name;
        $obatkategori->save();
        return redirect()->route('obatkategori_index')->with('hapus', 'obatkategori Deleted Successfully.');
    }

    public function trash()
    {
            $obatkategori = Obatkategori::onlyTrashed()->get();
            return view('master.obatkategori.trash', ['obatkategori' => $obatkategori]);
    }
}
