<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modeler;
use App\Models\User;
use App\Notifications\AntrianApotek;
use App\Notifications\AntrianKasir;
use App\Notifications\NotifyDeposit;
use App\Notifications\ObatSelesai;
use App\Notifications\PendaftaranNotification;
use App\Notifications\RawatInap;
use App\Notifications\RequestStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class NotifyController extends Controller
{

    public $model;
    public function __construct()
    {
        $this->model  = new Modeler();
    }

    public function authenticate(Request $req)
    {
        $pusher = new Pusher(
            '9cd8b5ebac6374f2d2db',
            '98e84f10c2de0311e7b2',
            '1371569'
        );

        return $pusher->socket_auth($req->channel_name, $req->socket_id);
    }

    public function openNotification(Request $req)
    {
        DB::table('notifications')
            ->where('id', $req->id)
            ->update([
                'read_at' => Carbon::now()
            ]);
        $notif = Auth::user()->unreadNotifications;

        return Response()->json(['status' => 1, 'message' => 'Success read all notification', 'unreadNotification' => $notif->count()]);
    }

    public function parseTanggalNotification(Request $req)
    {
        $tanggal = CarbonParse($req->tanggal, 'd/m/Y H:i');
        $notif = Auth::user()->unreadNotifications;
        return Response()->json(['status' => 1, 'tanggal' => $tanggal, 'unreadNotification' => $notif->count()]);
    }

    public function broadcastingRequestStock(Request $req)
    {
        $data = $this->model->stock()->find($req->id);

        if ($data->jenis_stock == 'OBAT') {
            $item = $data->ProdukObat;
        } else {
            $item = $data->ItemNonObat;
        }

        $message = 'Cabang ' . $data->Branch->kode . ' meminta request stock ' . $item->name . ' sebanyak ' . $req->qty . ' ' . $item->Satuan->name;

        $user = [];
        $user = $this->model->user()->whereHas('role', function ($q) use ($req) {
            $q->whereIn('name', ['Owner', 'Superuser']);
        })->get();

        foreach ($user as $item) {
            $item->notify(new RequestStock($item, $message, $data));
        }
        return Response()->json(['status' => 1, 'message' => 'Success blasting notification to APOTEKER']);
    }

    public function broadcastingAntrianApotek(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->with([
                'Pasien',
                'Pendaftaran'
            ])
            ->find($req->id);
        if ($data->jenis_stock == 'OBAT') {
            $item = $data->ProdukObat;
        } else {
            $item = $data->ItemNonObat;
        }

        $message = 'Antrian ' . $data->Pendaftaran->kode_pendaftaran . ' atas pasien ' . $data->Pasien->name;
        $user = [];
        $user = User::whereHas('role', function ($q) use ($req) {
            $q->where('type_role', 'APOTEKER');
            $q->orWhere('name', 'Superuser');
        })->where('branch_id', $data->Pendaftaran->branch_id)->get();

        foreach ($user as $item) {
            $item->notify(new AntrianApotek($item, $message, $data));
        }

        return Response()->json(['status' => 1, 'message' => 'Success blasting notification to APOTEKER']);
    }

    public function broadcastingAntrianApotekDariPembayaran($id)
    {
        $data = $this->model->rekamMedisPasien()
            ->with([
                'Pasien',
                'Pendaftaran'
            ])
            ->find($id);
        if ($data->jenis_stock == 'OBAT') {
            $item = $data->ProdukObat;
        } else {
            $item = $data->ItemNonObat;
        }

        $message = 'Antrian ' . $data->Pendaftaran->kode_pendaftaran . ' atas pasien ' . $data->Pasien->name;
        $user = [];
        $user = User::whereHas('role', function ($q) {
            $q->where('type_role', 'APOTEKER');
            $q->orWhere('name', 'Superuser');
        })->where('branch_id', $data->Pendaftaran->branch_id)->get();

        foreach ($user as $item) {
            $item->notify(new AntrianApotek($item, $message, $data));
        }

        return Response()->json(['status' => 1, 'message' => 'Success blasting notification to APOTEKER']);
    }

    public function broadcastingAntrianPembayaran(Request $req)
    {
        $data = $this->model->rekamMedisPasien()
            ->with([
                'Pasien',
                'Pendaftaran'
            ])
            ->find($req->id);
        if ($data->jenis_stock == 'OBAT') {
            $item = $data->ProdukObat;
        } else {
            $item = $data->ItemNonObat;
        }

        $message = 'Antrian ' . $data->Pendaftaran->kode_pendaftaran . ' atas pasien ' . $data->Pasien->name;
        $user = [];
        $user = User::whereHas('role', function ($q) use ($req) {
            $q->where('id', '3');
            $q->orWhere('name', 'Superuser');
        })->where('branch_id', $data->Pendaftaran->branch_id)->get();

        foreach ($user as $item) {
            $item->notify(new AntrianKasir($item, $message, $data));
        }

        return Response()->json(['status' => 1, 'message' => 'Success blasting notification to APOTEKER']);
    }

    public function broadcastingRequestObat(Request $req)
    {

        $data = $this->model->rekamMedisPasien()
            ->with([
                'Pasien',
                'Pendaftaran'
            ])
            ->find($req->id);

        if ($data->jenis_stock == 'OBAT') {
            $item = $data->ProdukObat;
        } else {
            $item = $data->ItemNonObat;
        }

        $message = 'Terdapat request obat untuk pasien dengan kode ' . $data->kode . ' atas nama pasien ' . $data->Pasien->name;
        $user = [];
        $user = User::whereHas('role', function ($q) use ($req) {
            $q->where('type_role', 'APOTEKER');
            $q->orWhere('name', 'Superuser');
        })->where('branch_id', $data->Pendaftaran->branch_id)->get();

        foreach ($user as $item) {
            $item->notify(new AntrianApotek($item, $message, $data));
        }

        return Response()->json(['status' => 1, 'message' => 'Success blasting notification to APOTEKER']);
    }


    public function broadcastingRawatInap($id)
    {
        $data = $this->model->rekamMedisPasien()
            ->with([
                'Pasien',
                'Pendaftaran'
            ])
            ->find($id);
        $message = 'Terdapat pasien rawat inap di <b>' . $data->KamarRawatInapDanBedahDetailFirst->KamarRawatInapDanBedah->name . '</b> atas nama <b>' . $data->Pasien->name . '</b> jenis binatang <b>' . $data->Pasien->binatang->name . '</b>';
        $user = [];
        $user = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['Dokter Bedah', 'Dokter Rawat Inap']);
        })->where('branch_id', $data->Pendaftaran->branch_id)->get();

        foreach ($user as $item) {
            $item->notify(new RawatInap($item, $message, $data));
        }
        return Response()->json(['status' => 1, 'message' => 'Success blasting notification to APOTEKER']);
    }

    public function broadcastingObatSelesai($idRekamMedis, $namaObat)
    {
        $data = $this->model->rekamMedisPasien()
            ->with([
                'Pasien',
                'Pendaftaran'
            ])
            ->find($idRekamMedis);
        if ($data->jenis_stock == 'OBAT') {
            $item = $data->ProdukObat;
        } else {
            $item = $data->ItemNonObat;
        }

        $message = 'Obat ' . $namaObat . ' telah selesai dibuat untuk pasien ' . $data->Pasien->name;
        $user = [];
        $user = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['Dokter Rawat Inap', 'Paramedis']);
        })->where('branch_id', $data->Pendaftaran->branch_id)->get();

        foreach ($user as $item) {
            $item->notify(new ObatSelesai($item, $message, $data));
        }

        return Response()->json(['status' => 1, 'message' => 'Success blasting notification to Paramedis and Dokter Rawat Inap']);
    }

    public function seeAllNotification()
    {
        $notification = Auth::user()->unreadNotifications;
        $notification = $this->model->notifications()
            ->distinct()
            ->selectRaw('date(created_at) as tanggal, notifications.*')
            ->where('notifiable_id', Auth::user()->id)
            ->orderBy('created_at', 'DESC')
            ->take(1000)
            ->get();

        return view('layout/see_all_notification', compact('notification'));
    }

    public function validasiNotifikasi(Request $req)
    {
        return DB::transaction(function () use ($req) {
            $this->model->notifications()
                ->find($req->id)
                ->update([
                    'status' => $req->param,
                    'approved_by' => me(),
                ]);
            return Response()->json(['status' => 1, 'message' => 'Data berhasil diubah']);
        });
    }


    public function generateMonitoringPendaftaran($idPendaftaran, $branchId)
    {
        $user = User::where('branch_id', $branchId)
            ->get();

        $data = $this->model->pendaftaran()->find($idPendaftaran);
        foreach ($user as $item) {
            $message = 'Terdapat pendaftaran pasien baru dengan kode ' . $data->kode_pendaftaran;
            $item->notify(new PendaftaranNotification($item, $message, $data));
        }
    }

    public function monitoringPendaftaran(Request $req)
    {
        $user = User::where('branch_id', $req->branch_id)
            ->get();

        $data = $this->model->pendaftaran()->find($req->pendaftaran_id);
        foreach ($user as $item) {
            $message = 'Terdapat pendaftaran pasien baru dengan kode ' . $data->kode_pendaftaran;
            $item->notify(new PendaftaranNotification($item, $message, $data));
        }


        return Response()->json(['status' => 1, 'message' => 'Berhasil blast pendaftaran']);
    }


    public function broadCastPenerimaanPasien(Request $req)
    {
        $data = $this->model->pendaftaran()->find($req->id);

        $user = User::where(function ($q) use ($data) {
            $q->where('branch_id', $data->branch_id);
            $q->orWhere(function ($q) {
                $q->whereHas('role', function ($q) {
                    $q->orWhere('name', 'Superuser');
                });
            });
        })->get();

        foreach ($user as $item) {
            $message = 'Pasien dengan nomor pendaftaran ' . $data->kode_pendaftaran . ' sedang ditangani oleh ' . Auth::user()->name;
            $item->notify(new PendaftaranNotification($item, $message, $data));
        }
        return Response()->json(['status' => 1, 'message' => 'Berhasil blast penerimaan pasien']);
    }

    public function notifyDeposit($id)
    {
        $data = $this->model->deposit()
            ->find($id);

        $message = 'Terdapat deposit yang harus segera di proses dengan nama owner ' . $data->Owner->name;
        $user = [];
        $user = User::whereHas('role', function ($q) {
            $q->where('type_role', 'DOKTER');
            $q->orWhere('name', 'Superuser');
        })->get();

        foreach ($user as $item) {
            if ($item->aksesMenu('edit', 'proses-deposit')) {
                $item->notify(new NotifyDeposit($item, $message, $data));
            }
        }

        return Response()->json(['status' => 1, 'message' => 'Success blasting notification']);
    }

    public function markAsRead(Request $req)
    {
        $user = User::find(Auth::user()->id);
        $user->unreadNotifications->markAsRead();
        return back();
    }
}
