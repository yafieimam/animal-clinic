<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modeler extends Model
{

    public function user()
    {
        return new \App\Models\User();
    }

    public function titleMenu()
    {
        return new \App\Models\TitleMenu();
    }

    public function groupMenu()
    {
        return new \App\Models\GroupMenu();
    }

    public function menu()
    {
        return new \App\Models\Menu();
    }

    public function role()
    {
        return new \App\Models\Role();
    }

    public function hakAkses()
    {
        return new \App\Models\HakAkses();
    }

    public function branch()
    {
        return new \App\Models\Branch();
    }

    public function poli()
    {
        return new \App\Models\Poli();
    }

    public function bagian()
    {
        return new \App\Models\Bagian();
    }

    public function binatang()
    {
        return new \App\Models\Binatang();
    }

    public function ras()
    {
        return new \App\Models\Ras();
    }

    public function tindakan()
    {
        return new \App\Models\Tindakan();
    }

    public function kategoriKamar()
    {
        return new \App\Models\KategoriKamar();
    }

    public function kamarRawatInapDanBedah()
    {
        return new \App\Models\KamarRawatInapDanBedah();
    }

    public function kamarRawatInapDanBedahDetail()
    {
        return new \App\Models\KamarRawatInapDanBedahDetail();
    }

    public function kategoriObat()
    {
        return new \App\Models\KategoriObat();
    }

    public function satuanObat()
    {
        return new \App\Models\SatuanObat();
    }

    public function produkObat()
    {
        return new \App\Models\ProdukObat();
    }


    public function provinsi()
    {
        return new \App\Models\Provinsi();
    }

    public function kota()
    {
        return new \App\Models\Kota();
    }

    public function kecamatan()
    {
        return new \App\Models\Kecamatan();
    }

    public function kelurahan()
    {
        return new \App\Models\Kelurahan();
    }

    public function karyawan()
    {
        return new \App\Models\Karyawan();
    }

    public function owner()
    {
        return new \App\Models\Owner();
    }

    public function pasien()
    {
        return new \App\Models\Pasien();
    }

    public function rekamMedisPasien()
    {
        return new \App\Models\RekamMedisPasien();
    }

    public function rekamMedisCatatan()
    {
        return new \App\Models\RekamMedisCatatan();
    }

    public function pendaftaran()
    {
        return new \App\Models\Pendaftaran();
    }

    public function jamKerja()
    {
        return new \App\Models\JamKerja();
    }

    public function jadwalDokter()
    {
        return new \App\Models\JadwalDokter();
    }

    public function jadwalDokterDetail()
    {
        return new \App\Models\JadwalDokterDetail();
    }

    public function rekamMedisDiagnosa()
    {
        return new \App\Models\RekamMedisDiagnosa();
    }

    public function rekamMedisHasilLab()
    {
        return new \App\Models\RekamMedisHasilLab();
    }

    public function rekamMedisResep()
    {
        return new \App\Models\RekamMedisResep();
    }

    public function rekamMedisKondisiHarian()
    {
        return new \App\Models\RekamMedisKondisiHarian();
    }

    public function rekamMedisResepRacikan()
    {
        return new \App\Models\RekamMedisResepRacikan();
    }

    public function rekamMedisTreatment()
    {
        return new \App\Models\RekamMedisTreatment();
    }

    public function rekamMedisTindakan()
    {
        return new \App\Models\RekamMedisTindakan();
    }

    public function rekamMedisPakan()
    {
        return new \App\Models\RekamMedisPakan();
    }

    public function rekamMedisNonObat()
    {
        return new \App\Models\RekamMedisNonObat();
    }
    public function rekamMedisRekomendasiTindakanBedah()
    {
        return new \App\Models\RekamMedisRekomendasiTindakanBedah();
    }

    public function rekamMedisLogHistory()
    {
        return new \App\Models\RekamMedisLogHistory();
    }

    public function rekamMedisPasienMutasiStock()
    {
        return new \App\Models\RekamMedisPasienMutasiStock();
    }

    public function rekamMedisPasienUploadFormPersetujuan()
    {
        return new \App\Models\RekamMedisPasienUploadFormPersetujuan();
    }

    public function rekamMedisPasienUploadPulangPaksa()
    {
        return new \App\Models\RekamMedisPasienUploadPulangPaksa();
    }

    public function ruleResepRacikan()
    {
        return new \App\Models\RuleResepRacikan();
    }

    public function pindahJadwalJaga()
    {
        return new \App\Models\PindahJadwalJaga();
    }

    public function supplier()
    {
        return new \App\Models\Supplier();
    }

    public function satuanNonObat()
    {
        return new \App\Models\SatuanNonObat();
    }

    public function itemNonObat()
    {
        return new \App\Models\ItemNonObat();
    }

    public function penerimaanStock()
    {
        return new \App\Models\PenerimaanStock();
    }

    public function penerimaanStockDetail()
    {
        return new \App\Models\PenerimaanStockDetail();
    }

    public function pengeluaranStock()
    {
        return new \App\Models\PengeluaranStock();
    }

    public function pengeluaranStockDetail()
    {
        return new \App\Models\PengeluaranStockDetail();
    }

    public function pengeluaranStockDetailMutasi()
    {
        return new \App\Models\PengeluaranStockDetailMutasi();
    }

    public function stock()
    {
        return new \App\Models\Stock();
    }

    public function mutasiStock()
    {
        return new \App\Models\MutasiStock();
    }

    public function permintaanStock()
    {
        return new \App\Models\PermintaanStock();
    }

    public function kasir()
    {
        return new \App\Models\Kasir();
    }

    public function kasirDetail()
    {
        return new \App\Models\KasirDetail();
    }

    public function jurnal()
    {
        return new \App\Models\Jurnal();
    }

    public function jurnalDetail()
    {
        return new \App\Models\JurnalDetail();
    }

    public function masterAkunTransaksi()
    {
        return new \App\Models\MasterAkunTransaksi();
    }

    public function notifications()
    {
        return new \App\Models\Notifications();
    }

    public function jabatan()
    {
        return new \App\Models\Jabatan();
    }

    public function divisi()
    {
        return new \App\Models\Divisi();
    }

    public function typeObat()
    {
        return new \App\Models\TypeObat();
    }

    public function anamnesa()
    {
        return new \App\Models\Anamnesa();
    }

    public function pengumuman_karyawan()
    {
        return new \App\Models\PengumumanKaryawan();
    }

    public function pendaftaran_pasien()
    {
        return new \App\Models\PendaftaranPasien();
    }

    public function pendaftaran_pasien_anamnesa()
    {
        return new \App\Models\PendaftaranPasienAnamnesa();
    }

    public function deposit()
    {
        return new \App\Models\Deposit();
    }

    public function deposit_mutasi()
    {
        return new \App\Models\DepositMutasi();
    }

    public function KasirPembayaran()
    {
        return new \App\Models\KasirPembayaran();
    }

    public function pasien_meninggal()
    {
        return new \App\Models\PasienMeninggal();
    }

    public function rekening()
    {
        return new \App\Models\Rekening();
    }

    public function knowledgeSharing()
    {
        return new \App\Models\KnowledgeSharing();
    }

    public function knowledgeSharingFile()
    {
        return new \App\Models\KnowledgeSharingFile();
    }

    public function reimbursement()
    {
        return new \App\Models\Reimbursement();
    }
    
    public function reimbursementFileApproval()
    {
        return new \App\Models\ReimbursementFileApproval();
    }

    public function reimbursementFileKlaim()
    {
        return new \App\Models\ReimbursementFileKlaim();
    }
}
