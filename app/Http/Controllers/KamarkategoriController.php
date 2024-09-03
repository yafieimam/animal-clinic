<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Kamarkategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class KamarkategoriController extends Controller
{
    // Require Login
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $kamarkategori = Kamarkategori::all();
        $count = Kamarkategori::count();
        return view('master.kamarkategori.index', compact('kamarkategori','count'));
    }

    public function create()
    {
        return view('master.kamarkategori.create');
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [

                'kamarkategori_name'       => 'required|unique:kamarkategori',
            ],
            [
                'kamarkategori_name.required'      => 'Kategori Ruang Rawat Inap Wajib Di isi',
                'kamarkategori_name.unique'        => 'Kategori Ruang Rawat Inap sudah ada',
            ]
        );

        $increment = DB::table('kamarkategori')->max('kamarkategori_id')+1;

        $tambah = new Kamarkategori();
        $tambah->kamarkategori_name = $request->get('kamarkategori_name');
        $tambah->created_by = Auth::user()->name;
        $tambah->updated_by = Auth::user()->name;
        $tambah->save();

        return redirect()->route('kamarkategori_index')->with('info', 'kamarkategori Created Successfully.');
    }

     public function show(kamarkategori $kamarkategori, $kamarkategori_id)
    {
        $decryptID = Crypt::decryptString($kamarkategori_id);
        $kamarkategori = Kamarkategori::where('kamarkategori_id', $decryptID)->first();
        return view('master.kamarkategori.show')->with('kamarkategori', $kamarkategori);
    }

    public function edit(kamarkategori $kamarkategori, $kamarkategori_id)
    {
        $decryptID = Crypt::decryptString($kamarkategori_id);
        $kamarkategori = kamarkategori::where('kamarkategori_id', $decryptID)->first();
        return view('master.kamarkategori.edit', ['kamarkategori'=> $kamarkategori]);
    }

    public function update(Request $request, $kamarkategori_id)
    {
        $kamarkategori = Kamarkategori::find($kamarkategori_id);
        $kamarkategori->kamarkategori_name = $request->get('kamarkategori_name');
        $kamarkategori->updated_by = Auth::user()->name;
        $kamarkategori->update();

        return redirect()->route('kamarkategori_index')->with('update', 'Update');
    }

    public function destroy($kamarkategori_id)
    {
        $kamarkategori = Kamarkategori::find($kamarkategori_id);
        $kamarkategori->delete();
        $kamarkategori->deleted_by = Auth::user()->name;
        $kamarkategori->save();
        return redirect()->route('kamarkategori_index')->with('hapus', 'kamarkategori Deleted Successfully.');
    }

    public function trash()
    {
            $kamarkategori = Kamarkategori::onlyTrashed()->get();
            return view('master.kamarkategori.trash', ['kamarkategori' => $kamarkategori]);
    }
}
