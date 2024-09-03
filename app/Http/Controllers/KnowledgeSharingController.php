<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KnowledgeSharingController extends Controller
{
    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function index()
    {
        Auth::user()->akses('view', null, true);
        return view('setting/knowledge_sharing/knowledge_sharing');
    }

    public function store(Request $req)
    {
        return DB::transaction(function () use ($req) {

            if(isset($req->id)){
                $this->model->knowledgeSharing()
                    ->where('id', $req->id)
                    ->update([
                        'title' => $req->title,
                        'description' => $req->description,
                        'updated_by'    => me()
                    ]);

                if($req->hasFile('file_data')){
                    foreach ($req->file('file_data') as $index => $file) {
                        $dataKnowledgeSharing = $this->model->knowledgeSharingFile()
                            ->where('knowledge_sharing_id', $req->id)
                            ->where('id', $req->seq_data[$index])->first();

                        if($dataKnowledgeSharing){
                            if (is_file($dataKnowledgeSharing->file)) {
                                unlink($dataKnowledgeSharing->file);
                            }

                            if ($file) {
                                $path = 'image/knowledge_sharing';
                                $id =  Str::uuid($req->id)->toString();
                                $name = $id . '.' . $file->getClientOriginalExtension();
                                $foto = $path . '/' . $name;
                                if (is_file($foto)) {
                                    unlink($foto);
                                }
        
                                if (!file_exists($path)) {
                                    $oldmask = umask(0);
                                    mkdir($path, 0777, true);
                                    umask($oldmask);
                                }
        
                                Storage::disk('public_uploads')->put($foto, file_get_contents($file));
                            }

                            $this->model->knowledgeSharingFile()
                                ->where('knowledge_sharing_id', $req->id)
                                ->where('id', $req->seq_data[$index])
                                ->update([
                                    'file'  => $foto
                                ]);
                        }else{
                            if ($file) {
                                $path = 'image/knowledge_sharing';
                                $id =  Str::uuid($req->id)->toString();
                                $name = $id . '.' . $file->getClientOriginalExtension();
                                $foto = $path . '/' . $name;
                                if (is_file($foto)) {
                                    unlink($foto);
                                }
        
                                if (!file_exists($path)) {
                                    $oldmask = umask(0);
                                    mkdir($path, 0777, true);
                                    umask($oldmask);
                                }
        
                                Storage::disk('public_uploads')->put($foto, file_get_contents($file));
                            }
        
                            $this->model->knowledgeSharingFile()
                                ->create([
                                    'knowledge_sharing_id' => $req->id,
                                    'id'    => $req->seq_data[$index],
                                    'file'  => $foto,
                                    'created_by' => me(),
                                    'updated_by' => me()
                                ]);
                        }
                    }
                }
            }else{
                $idData = $this->model->knowledgeSharing()->max('id') + 1;

                $newData = $this->model->knowledgeSharing()
                    ->create([
                        'id' => $idData,
                        'title' => $req->title,
                        'description' => $req->description,
                        'created_by'    => me(),
                        'updated_by'    => me()
                    ]);

                if($req->hasFile('file_data')){
                    foreach ($req->file('file_data') as $index => $file) {
                        if ($file) {
                            $path = 'image/knowledge_sharing';
                            $id =  Str::uuid($idData)->toString();
                            $name = $id . '.' . $file->getClientOriginalExtension();
                            $foto = $path . '/' . $name;
                            if (is_file($foto)) {
                                unlink($foto);
                            }

                            if (!file_exists($path)) {
                                $oldmask = umask(0);
                                mkdir($path, 0777, true);
                                umask($oldmask);
                            }

                            Storage::disk('public_uploads')->put($foto, file_get_contents($file));
                        }

                        $this->model->knowledgeSharingFile()
                            ->create([
                                'knowledge_sharing_id' => $idData,
                                'id'    => $req->seq_data[$index],
                                'file'  => $foto,
                                'created_by' => me(),
                                'updated_by' => me()
                            ]);
                    }
                }
            }

            
            
            return Response()->json(['status' => 1, 'message' => 'Berhasil menambahkan data']);
        });
    }

    public function get()
    {
        $data = $this->model->knowledgeSharing()
            ->with(['knowledgeSharingFile'])->get();

        return Response()->json(['status' => 1, 'data' => $data, 'role' => Auth::user()->role_id]);
    }

    public function edit(Request $req)
    {
        $data = $this->model->knowledgeSharing()
            ->with(['knowledgeSharingFile'])
            ->where('id', $req->knowledgeId)->get();

        return Response()->json(['status' => 1, 'data' => $data]);
    }

    public function delete(Request $req)
    {
        return DB::transaction(function () use ($req) {
            Auth::user()->akses('delete', null, true);

            $this->model->knowledgeSharing()
                ->where('id', $req->knowledgeId)
                ->delete();

            $data = $this->model->knowledgeSharingFile()
                ->where('knowledge_sharing_id', $req->knowledgeId)->get();

            foreach ($data as $index => $file) {
                if (is_file($file['file'])) {
                    unlink($file['file']);
                }
            }

            $this->model->knowledgeSharingFile()
                ->where('knowledge_sharing_id', $req->knowledgeId)
                ->delete();

            return Response()->json(['status' => 1, 'message' => 'Data berhasil dihapus']);
        });
    }
}
