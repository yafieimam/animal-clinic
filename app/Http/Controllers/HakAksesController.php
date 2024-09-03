<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;

use Datatables;
use DB;
use Hash;
use Illuminate\Support\Facades\Auth;
use Response;

class HakAksesController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view',null,true);
        return view('setting/hak_akses/hak_akses');
    }

    public function edit(Request $req)
    {
        if (!isset($req->param)) {
            Auth::user()->akses('edit', null, true);
        }

        $groupMenu = $this->model->groupMenu()
            ->where('status', true)
            ->orderBy('sequence', 'ASC')
            ->get();
        return view('setting/hak_akses/table_hak_akses', compact('groupMenu', 'req'));
    }

    public function update(Request $req)
    {
        return DB::transaction(function () use ($req) {
            if ($req->jenis == 'GLOBAL') {
                $groupItem = $this->model->GroupMenu()->findOrFail($req->groupItemId);

                foreach ($groupItem->Menu as $i => $d) {
                    $check = $this->model->HakAkses()
                        ->where('role_id', $req->roleId)
                        ->where('menu_id', $d->id)
                        ->first();
                    if (is_null($check)) {
                        $id = $this->model->HakAkses()->max('id') + 1;
                        $this->model->HakAkses()
                            ->create([
                                'id' => $id,
                                'role_id' => $req->roleId,
                                'menu_id' => $d->id,
                                $req->column => $req->param,
                                'created_by' => me(),
                                'updated_by' => me(),
                            ]);
                    } else {
                        $this->model->HakAkses()
                            ->where('role_id', $req->roleId)
                            ->where('menu_id', $d->id)
                            ->update([
                                $req->column => $req->param,
                                'updated_by' => me(),
                            ]);
                    }
                }
            } else if ($req->jenis == 'MENU') {
                $hakAkses = $this->model->HakAkses()
                    ->where('role_id', $req->roleId)
                    ->where('menu_id', $req->menuId)
                    ->first();
                if (is_null($hakAkses)) {
                    $id = $this->model->HakAkses()->max('id') + 1;
                    $this->model->HakAkses()
                        ->create([
                            'id' => $id,
                            'role_id' => $req->roleId,
                            'menu_id' => $req->menuId,
                            $req->column => $req->param,
                            'created_by' => me(),
                            'updated_by' => me(),
                        ]);
                } else {
                    $this->model->HakAkses()
                        ->where('role_id', $req->roleId)
                        ->where('menu_id', $req->menuId)
                        ->update([
                            $req->column => $req->param,
                            'updated_by' => me(),
                        ]);
                }
            }
            return Response()->json(['status' => 1, 'message' => 'Data Berhasil Diubah']);
        });
    }
}
