<?php

namespace App\Models\Traits;

use App\Models\LogResep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

trait ResepTrait
{
    /**
     * Boot the versionable trait for a model.
     *
     * @return void
     */
    protected static function bootResepTrait()
    {
        static::created(function (Model $model) {
            $model->beforeSave();
        });

        static::deleted(function (Model $model) {
            $model->deleteResep();
        });
    }

    public function beforeSave()
    {
        try {
            DB::beginTransaction();
            $data = LogResep::create([
                'rekam_medis_resep_id' => $this->id,
                'rekam_medis_pasien_id' => $this->rekam_medis_pasien_id,
                'url' => request()->url(),
                'jenis' => 'save',
                'user_id' => Auth::user()->id,
            ]);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
        }
    }

    public function deleteResep()
    {
        try {
            DB::beginTransaction();
            $data = LogResep::create([
                'rekam_medis_resep_id' => $this->id,
                'rekam_medis_pasien_id' => $this->rekam_medis_pasien_id,
                'url' => request()->url(),
                'jenis' => 'save',
                'user_id' => Auth::user()->id,
            ]);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new \Exception('Data belum bisa di proses, silakan Klik Simpan Kembali.');
        }
    }
}
