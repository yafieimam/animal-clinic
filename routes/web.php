<?php

use App\Http\Controllers\AnamnesaController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApotekController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\BedahController;
use App\Http\Controllers\BinatangController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BuktiKasKeluarController;
use App\Http\Controllers\CicilanController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\DarkModeController;
use App\Http\Controllers\ColorSchemeController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemNonObatController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JadwalDokterController;
use App\Http\Controllers\JamKerjaController;
use App\Http\Controllers\KamarRawatInapDanBedahController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriKamarController;
use App\Http\Controllers\KategoriObatController;
use App\Http\Controllers\LaporanAdminHarianController;
use App\Http\Controllers\LaporanDokterController;
use App\Http\Controllers\LaporanJumlahPasienController;
use App\Http\Controllers\LaporanPendapatanController;
use App\Http\Controllers\LaporanPendaftaranController;
use App\Http\Controllers\MasterAkunTransaksiController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MonitoringAntrianController;
use App\Http\Controllers\MonitoringAntrianObatController;
use App\Http\Controllers\MutasiStockController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PasienRawatInapController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemeriksaanPasienController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PenerimaanPasienController;
use App\Http\Controllers\PenerimaanStockController;
use App\Http\Controllers\PengeluaranStockController;
use App\Http\Controllers\PengumumanKaryawanController;
use App\Http\Controllers\PermintaanStockController;
use App\Http\Controllers\PermintaanStockControllere;
use App\Http\Controllers\PindahJadwalJagaController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\ProdukObatController;
use App\Http\Controllers\ProsesDepositController;
use App\Http\Controllers\RasController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\RekapInvoiceController;
use App\Http\Controllers\HibahController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\ReimbursementApprovalController;
use App\Http\Controllers\ReimbursementApprovedController;
use App\Http\Controllers\RekapPasienController;
use App\Http\Controllers\RekapPasienMeninggalController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\KnowledgeSharingController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\RuleResepRacikanController;
use App\Http\Controllers\SatuanNonObatController;
use App\Http\Controllers\SatuanObatController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatistikPendapatanController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TagihanSementaraController;
use App\Http\Controllers\TindakanController;
use App\Http\Controllers\TitleMenuController;
use App\Http\Controllers\TransaksiKasCabangController;
use App\Http\Controllers\TransaksiKasController;
use App\Http\Controllers\TypeObatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch'])->name('color-scheme-switcher');
Route::get('reset-pendaftaran', [SettingController::class, 'resetPendaftaran'])->name('resetPendaftaran');
Route::get('broadcast-request-obat',  [NotifyController::class, 'broadcastingRequestObat'])->name('broadcastingRequestObat');
Route::get('local/temp/{path}', function (string $path){
    return Storage::disk('local')->download($path);
})->name('local.temp');

Route::controller(AuthController::class)->middleware('loggedin')->group(function () {
    Route::get('login', 'loginView')->name('login.index');
    Route::post('login', 'login')->name('login.check');
    Route::get('register', 'registerView')->name('register.index');
    Route::post('register', 'register')->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(ApiController::class)->group(function () {
        Route::get('generateTanggalKamar', 'generateTanggalKamar')->name('generateTanggalKamar');
        Route::get('deleteJournal', 'deleteJournal')->name('deleteJournal');
        
    });
    Route::controller(NotifyController::class)->group(function () {
        Route::group(['prefix' => 'broadcasting'], function () {
            Route::post('auth', 'authenticate')->name('broadcastingAuth');
            Route::post('open-notification', 'openNotification')->name('openNotification');
            Route::get('see-all-notification', 'seeAllNotification')->name('seeAllNotification');
            Route::post('validasi-notifikasi', 'validasiNotifikasi')->name('validasiNotifikasi');
            Route::get('parse-tanggal-notification', 'parseTanggalNotification')->name('parseTanggalNotification');
            Route::get('broadcast-pendaftaran', 'monitoringPendaftaran')->name('monitoringPendaftaran');
            Route::get('mark-as-read', 'markAsRead')->name('markAsRead');
        });
    });
    Route::controller(PageController::class)->group(function () {
        Route::get('/', 'dashboardOverview1')->name('dashboard-overview-1');
        Route::get('dashboard-overview-2-page', 'dashboardOverview2')->name('dashboard-overview-2');
        Route::get('dashboard-overview-3-page', 'dashboardOverview3')->name('dashboard-overview-3');
        Route::get('inbox-page', 'inbox')->name('inbox');
        Route::get('file-manager-page', 'fileManager')->name('file-manager');
        Route::get('point-of-sale-page', 'pointOfSale')->name('point-of-sale');
        Route::get('chat-page', 'chat')->name('chat');
        Route::get('post-page', 'post')->name('post');
        Route::get('calendar-page', 'calendar')->name('calendar');
        Route::get('crud-data-list-page', 'crudDataList')->name('crud-data-list');
        Route::get('crud-form-page', 'crudForm')->name('crud-form');
        Route::get('users-layout-1-page', 'usersLayout1')->name('users-layout-1');
        Route::get('users-layout-2-page', 'usersLayout2')->name('users-layout-2');
        Route::get('users-layout-3-page', 'usersLayout3')->name('users-layout-3');
        Route::get('profile-overview-1-page', 'profileOverview1')->name('profile-overview-1');
        Route::get('profile-overview-2-page', 'profileOverview2')->name('profile-overview-2');
        Route::get('profile-overview-3-page', 'profileOverview3')->name('profile-overview-3');
        Route::get('wizard-layout-1-page', 'wizardLayout1')->name('wizard-layout-1');
        Route::get('wizard-layout-2-page', 'wizardLayout2')->name('wizard-layout-2');
        Route::get('wizard-layout-3-page', 'wizardLayout3')->name('wizard-layout-3');
        Route::get('blog-layout-1-page', 'blogLayout1')->name('blog-layout-1');
        Route::get('blog-layout-2-page', 'blogLayout2')->name('blog-layout-2');
        Route::get('blog-layout-3-page', 'blogLayout3')->name('blog-layout-3');
        Route::get('pricing-layout-1-page', 'pricingLayout1')->name('pricing-layout-1');
        Route::get('pricing-layout-2-page', 'pricingLayout2')->name('pricing-layout-2');
        Route::get('invoice-layout-1-page', 'invoiceLayout1')->name('invoice-layout-1');
        Route::get('invoice-layout-2-page', 'invoiceLayout2')->name('invoice-layout-2');
        Route::get('faq-layout-1-page', 'faqLayout1')->name('faq-layout-1');
        Route::get('faq-layout-2-page', 'faqLayout2')->name('faq-layout-2');
        Route::get('faq-layout-3-page', 'faqLayout3')->name('faq-layout-3');
        Route::get('login-page', 'login')->name('login');
        Route::get('register-page', 'register')->name('register');
        Route::get('error-page-page', 'errorPage')->name('error-page');
        Route::get('update-profile-page', 'updateProfile')->name('update-profile');
        Route::get('change-password-page', 'changePassword')->name('change-password');
        Route::get('regular-table-page', 'regularTable')->name('regular-table');
        Route::get('tabulator-page', 'tabulator')->name('tabulator');
        Route::get('modal-page', 'modal')->name('modal');
        Route::get('slide-over-page', 'slideOver')->name('slide-over');
        Route::get('notification-page', 'notification')->name('notification');
        Route::get('tab-page', 'tab')->name('tab');
        Route::get('accordion-page', 'accordion')->name('accordion');
        Route::get('button-page', 'button')->name('button');
        Route::get('alert-page', 'alert')->name('alert');
        Route::get('progress-bar-page', 'progressBar')->name('progress-bar');
        Route::get('tooltip-page', 'tooltip')->name('tooltip');
        Route::get('dropdown-page', 'dropdown')->name('dropdown');
        Route::get('typography-page', 'typography')->name('typography');
        Route::get('icon-page', 'icon')->name('icon');
        Route::get('loading-icon-page', 'loadingIcon')->name('loading-icon');
        Route::get('regular-form-page', 'regularForm')->name('regular-form');
        Route::get('datepicker-page', 'datepicker')->name('datepicker');
        Route::get('tom-select-page', 'tomSelect')->name('tom-select');
        Route::get('file-upload-page', 'fileUpload')->name('file-upload');
        Route::get('wysiwyg-editor-classic', 'wysiwygEditorClassic')->name('wysiwyg-editor-classic');
        Route::get('wysiwyg-editor-inline', 'wysiwygEditorInline')->name('wysiwyg-editor-inline');
        Route::get('wysiwyg-editor-balloon', 'wysiwygEditorBalloon')->name('wysiwyg-editor-balloon');
        Route::get('wysiwyg-editor-balloon-block', 'wysiwygEditorBalloonBlock')->name('wysiwyg-editor-balloon-block');
        Route::get('wysiwyg-editor-document', 'wysiwygEditorDocument')->name('wysiwyg-editor-document');
        Route::get('validation-page', 'validation')->name('validation');
        Route::get('chart-page', 'chart')->name('chart');
        Route::get('slider-page', 'slider')->name('slider');
        Route::get('image-zoom-page', 'imageZoom')->name('image-zoom');
    });

    Route::controller(HomeController::class)->group(function () {
        Route::group(['prefix' => ''], function () {
            Route::get('/', 'index')->name('dashboard');
            Route::get('/search-menu', 'searchMenu')->name('searchMenu');
            Route::get('/get-dokter', 'getDokter')->name('getDokterDashboard');
            Route::get('/get-kamar', 'getKamar')->name('getKamarDashboard');
            Route::get('/column-pasien-per-bulan', 'columnPasienPerBulan')->name('columnPasienPerBulan');
            Route::get('/traffic-pasien', 'trafficPasien')->name('trafficPasien');
            Route::get('/changeBranchOfDepositMutasi', 'changeBranchOfDepositMutasi')->name('changeBranchOfDepositMutasi');
        });
    });

    Route::group(['prefix' => 'quick-menu'], function () {
        Route::controller(PendaftaranController::class)->group(function () {
            Route::group(['prefix' => 'pendaftaran'], function () {
                Route::get('/index', 'index')->name('pendaftaran');
                Route::get('/datatable', 'datatable')->name('datatablePendaftaran');
                Route::get('/generate-kode', 'generateKode')->name('generateKodePendaftaran');
                Route::get('/generate-age', 'generateAge')->name('generateAgePendaftaran');
                Route::get('/select2', 'select2')->name('select2Pendaftaran');
                Route::get('/status', 'status')->name('statusPendaftaran');
                Route::get('/ganti-anamnesa', 'gantiAnamnesa')->name('gantiAnamnesaPendaftaran');
                Route::get('/get-hewan', 'getHewan')->name('getHewanPendaftaran');
                Route::get('/edit', 'edit')->name('editPendaftaran');
                Route::get('/print', 'print')->name('printPendaftaran');
                Route::get('/sequence', 'sequence')->name('sequencePendaftaran');
                Route::post('/store', 'store')->name('storePendaftaran');
                Route::post('/update', 'update')->name('updatePendaftaran');
                Route::post('/delete', 'delete')->name('deletePendaftaran');
            });
        });

        Route::controller(MonitoringAntrianController::class)->group(function () {
            Route::group(['prefix' => 'monitoring-antrian'], function () {
                Route::get('/index', 'index')->name('monitoring-antrian');
                Route::get('/datatable', 'datatable')->name('datatableMonitoringAntrian');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeMonitoringAntrian');
                Route::get('/generate-age', 'generateAge')->name('generateAgeMonitoringAntrian');
                Route::get('/get-dokter', 'getDokter')->name('getDokterMonitoringAntrian');
                Route::get('/get-antrian', 'getAntrian')->name('getAntrianMonitoringAntrian');
                Route::get('/fullscreen', 'fullscreen')->name('fullscreenMonitoringAntrian');
                Route::get('/get-antrian-fullscreen', 'getAntrianFullscreen')->name('getAntrianFullscreenMonitoringAntrian');
                Route::get('/select2', 'select2')->name('select2MonitoringAntrian');
                Route::get('/status', 'status')->name('statusMonitoringAntrian');
                Route::get('/edit', 'edit')->name('editMonitoringAntrian');
                Route::get('/sequence', 'sequence')->name('sequenceMonitoringAntrian');
                Route::post('/store', 'store')->name('storeMonitoringAntrian');
                Route::post('/delete', 'delete')->name('deleteMonitoringAntrian');
            });
        });

        Route::controller(MonitoringAntrianObatController::class)->group(function () {
            Route::group(['prefix' => 'monitoring-antrian-obat'], function () {
                Route::get('/index', 'index')->name('monitoring-antrian-obat');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeMonitoringAntrianObat');
                Route::get('/generate-age', 'generateAge')->name('generateAgeMonitoringAntrianObat');
                Route::get('/get-pasien', 'getPasien')->name('getPasienMonitoringAntrianObat');
                Route::get('/datatable', 'datatable')->name('datatableMonitoringAntrianObat');
                Route::get('/select2', 'select2')->name('select2MonitoringAntrianObat');
                Route::get('/status', 'status')->name('statusMonitoringAntrianObat');
                Route::get('/edit', 'edit')->name('editMonitoringAntrianObat');
                Route::get('/sequence', 'sequence')->name('sequenceMonitoringAntrianObat');
                Route::post('/store', 'store')->name('storeMonitoringAntrianObat');
                Route::post('/delete', 'delete')->name('deleteMonitoringAntrianObat');
            });
        });

        Route::controller(PasienRawatInapController::class)->group(function () {
            Route::group(['prefix' => 'pasien-rawat-inap'], function () {
                Route::get('/index', 'index')->name('pasien-rawat-inap');
            });
        });

        Route::controller(KnowledgeSharingController::class)->group(function () {
            Route::group(['prefix' => 'knowledge-sharing'], function () {
                Route::get('/index', 'index')->name('knowledge-sharing');
                Route::get('/get', 'get')->name('getKnowledgeSharing');
                Route::get('/edit', 'edit')->name('editKnowledgeSharing');
                Route::post('/store', 'store')->name('storeKnowledgeSharing');
                Route::post('/update', 'update')->name('updateKnowledgeSharing');
                Route::post('/delete', 'delete')->name('deleteKnowledgeSharing');
            });
        });
    });


    Route::group(['prefix' => 'management-kamar'], function () {
        Route::controller(KategoriKamarController::class)->group(function () {
            Route::group(['prefix' => 'kategori-kamar'], function () {
                Route::get('/index', 'index')->name('kategori-kamar');
                Route::get('/datatable', 'datatable')->name('datatableKategoriKamar');
                Route::get('/status', 'status')->name('statusKategoriKamar');
                Route::get('/edit', 'edit')->name('editKategoriKamar');
                Route::get('/sequence', 'sequence')->name('sequenceKategoriKamar');
                Route::post('/store', 'store')->name('storeKategoriKamar');
                Route::post('/delete', 'delete')->name('deleteKategoriKamar');
            });
        });

        Route::controller(KamarRawatInapDanBedahController::class)->group(function () {
            Route::group(['prefix' => 'kamar-rawat-inap-dan-bedah'], function () {
                Route::get('/index', 'index')->name('kamar-rawat-inap-dan-bedah');
                Route::get('/datatable', 'datatable')->name('datatableKamarRawatInapDanBedah');
                Route::get('/status', 'status')->name('statusKamarRawatInapDanBedah');
                Route::get('/edit', 'edit')->name('editKamarRawatInapDanBedah');
                Route::get('/sequence', 'sequence')->name('sequenceKamarRawatInapDanBedah');
                Route::post('/store', 'store')->name('storeKamarRawatInapDanBedah');
                Route::post('/delete', 'delete')->name('deleteKamarRawatInapDanBedah');
            });
        });
    });


    Route::group(['prefix' => 'poli'], function () {
        Route::controller(PenerimaanPasienController::class)->group(function () {
            Route::group(['prefix' => 'penerimaan-pasien'], function () {
                Route::get('/index', 'index')->name('penerimaan-pasien');
                Route::get('/get-antrian', 'getAntrian')->name('getAntrianPenerimaanPasien');
                Route::get('/history', 'history')->name('historyPenerimaanPasien');
                Route::post('/store', 'store')->name('storePenerimaanPasien');
                Route::get('/get-dokter', 'getDokter')->name('getDokterPenerimaanPasien');
            });
        });

        Route::controller(PemeriksaanPasienController::class)->group(function () {
            Route::group(['prefix' => 'pemeriksaan-pasien'], function () {
                Route::get('/index', 'index')->name('pemeriksaan-pasien');
                Route::get('/datatable', 'datatable')->name('datatablePemeriksaanPasien');
                Route::get('/generate-kode', 'generateKode')->name('generateKodePemeriksaanPasien');
                Route::get('/generate-age', 'generateAge')->name('generateAgePemeriksaanPasien');
                Route::get('/get-pasien', 'getPasien')->name('getPasienPemeriksaanPasien');
                Route::get('/get-rekam-medis', 'getRekamMedis')->name('getRekamMedisPemeriksaanPasien');
                Route::get('/get-list-rekam-medis', 'getListRekamMedis')->name('getListRekamMedisPemeriksaanPasien');
                Route::get('/tambah-resep', 'tambahResep')->name('tambahResepPemeriksaanPasien');
                Route::get('/tambah-racikan-child', 'tambahRacikanChild')->name('tambahRacikanChildPemeriksaanPasien');
                Route::get('/select2', 'select2')->name('select2PemeriksaanPasien');
                Route::get('/status', 'status')->name('statusPemeriksaanPasien');
                Route::get('/edit', 'edit')->name('editPemeriksaanPasien');
                Route::get('/print/{id}', 'print')->name('printPemeriksaanPasien');
                Route::get('/sequence', 'sequence')->name('sequencePemeriksaanPasien');
                Route::post('/store', 'store')->name('storePemeriksaanPasien');
                Route::post('/delete', 'delete')->name('deletePemeriksaanPasien');
            });
        });
    });


    Route::group(['prefix' => 'management-klinik'], function () {
        Route::controller(BranchController::class)->group(function () {
            Route::group(['prefix' => 'branch'], function () {
                Route::get('/index', 'index')->name('branch');
                Route::get('/datatable', 'datatable')->name('datatableBranch');
                Route::get('/edit', 'edit')->name('editBranch');
                Route::get('/status', 'status')->name('statusBranch');
                Route::post('/store', 'store')->name('storeBranch');
                Route::post('/delete', 'delete')->name('deleteBranch');
            });
        });

        Route::controller(BinatangController::class)->group(function () {
            Route::group(['prefix' => 'hewan'], function () {
                Route::get('/index', 'index')->name('hewan');
                Route::get('/datatable', 'datatable')->name('datatableBinatang');
                Route::get('/status', 'status')->name('statusBinatang');
                Route::get('/edit', 'edit')->name('editBinatang');
                Route::get('/sequence', 'sequence')->name('sequenceBinatang');
                Route::post('/store', 'store')->name('storeBinatang');
                Route::post('/delete', 'delete')->name('deleteBinatang');
            });
        });

        Route::controller(RasController::class)->group(function () {
            Route::group(['prefix' => 'ras'], function () {
                Route::get('/index', 'index')->name('ras');
                Route::get('/datatable', 'datatable')->name('datatableRas');
                Route::get('/status', 'status')->name('statusRas');
                Route::get('/edit', 'edit')->name('editRas');
                Route::get('/sequence', 'sequence')->name('sequenceRas');
                Route::post('/store', 'store')->name('storeRas');
                Route::post('/delete', 'delete')->name('deleteRas');
            });
        });

        Route::controller(PoliController::class)->group(function () {
            Route::group(['prefix' => 'poli'], function () {
                Route::get('/index', 'index')->name('poli');
                Route::get('/datatable', 'datatable')->name('datatablePoli');
                Route::get('/status', 'status')->name('statusPoli');
                Route::get('/edit', 'edit')->name('editPoli');
                Route::get('/sequence', 'sequence')->name('sequencePoli');
                Route::post('/store', 'store')->name('storePoli');
                Route::post('/delete', 'delete')->name('deletePoli');
            });
        });

        Route::controller(TindakanController::class)->group(function () {
            Route::group(['prefix' => 'tindakan'], function () {
                Route::get('/index', 'index')->name('tindakan');
                Route::get('/datatable', 'datatable')->name('datatableTindakan');
                Route::get('/status', 'status')->name('statusTindakan');
                Route::get('/edit', 'edit')->name('editTindakan');
                Route::get('/sequence', 'sequence')->name('sequenceTindakan');
                Route::post('/store', 'store')->name('storeTindakan');
                Route::post('/delete', 'delete')->name('deleteTindakan');
                Route::post('/bulk-import', 'bulkImport')->name('bulkImportTindakan');
                Route::get('/binatang-excel', 'binatangExcel')->name('binatangExcel');
            });
        });

        Route::controller(DivisiController::class)->group(function () {
            Route::group(['prefix' => 'divisi'], function () {
                Route::get('/index', 'index')->name('divisi');
                Route::get('/datatable', 'datatable')->name('datatableDivisi');
                Route::get('/status', 'status')->name('statusDivisi');
                Route::get('/edit', 'edit')->name('editDivisi');
                Route::get('/sequence', 'sequence')->name('sequenceDivisi');
                Route::post('/store', 'store')->name('storeDivisi');
                Route::post('/delete', 'delete')->name('deleteDivisi');
            });
        });

        Route::controller(BagianController::class)->group(function () {
            Route::group(['prefix' => 'bagian'], function () {
                Route::get('/index', 'index')->name('bagian');
                Route::get('/datatable', 'datatable')->name('datatableBagian');
                Route::get('/status', 'status')->name('statusBagian');
                Route::get('/edit', 'edit')->name('editBagian');
                Route::get('/sequence', 'sequence')->name('sequenceBagian');
                Route::post('/store', 'store')->name('storeBagian');
                Route::post('/delete', 'delete')->name('deleteBagian');
            });
        });

        Route::controller(JamKerjaController::class)->group(function () {
            Route::group(['prefix' => 'jam-kerja'], function () {
                Route::get('/index', 'index')->name('jam-kerja');
                Route::get('/datatable', 'datatable')->name('datatableJamKerja');
                Route::get('/status', 'status')->name('statusJamKerja');
                Route::get('/edit', 'edit')->name('editJamKerja');
                Route::get('/sequence', 'sequence')->name('sequenceJamKerja');
                Route::post('/store', 'store')->name('storeJamKerja');
                Route::post('/delete', 'delete')->name('deleteJamKerja');
            });
        });

        Route::controller(JabatanController::class)->group(function () {
            Route::group(['prefix' => 'jabatan'], function () {
                Route::get('/index', 'index')->name('jabatan');
                Route::get('/datatable', 'datatable')->name('datatableJabatan');
                Route::get('/status', 'status')->name('statusJabatan');
                Route::get('/edit', 'edit')->name('editJabatan');
                Route::get('/sequence', 'sequence')->name('sequenceJabatan');
                Route::post('/store', 'store')->name('storeJabatan');
                Route::post('/delete', 'delete')->name('deleteJabatan');
            });
        });

        Route::controller(HakAksesController::class)->group(function () {
            Route::group(['prefix' => 'hak-akses'], function () {
                Route::get('/index', 'index')->name('hak-akses');
                Route::get('/datatable', 'datatable')->name('datatableHakAkses');
                Route::get('/status', 'status')->name('statusHakAkses');
                Route::get('/edit', 'edit')->name('editHakAkses');
                Route::get('/update', 'update')->name('updateHakAkses');
                Route::post('/store', 'store')->name('storeHakAkses');
                Route::post('/delete', 'delete')->name('deleteHakAkses');
            });
        });

        Route::controller(AnamnesaController::class)->group(function () {
            Route::group(['prefix' => 'anamnesa'], function () {
                Route::get('/index', 'index')->name('anamnesa');
                Route::get('/datatable', 'datatable')->name('datatableAnamnesa');
                Route::get('/status', 'status')->name('statusAnamnesa');
                Route::get('/edit', 'edit')->name('editAnamnesa');
                Route::get('/sequence', 'sequence')->name('sequenceAnamnesa');
                Route::post('/store', 'store')->name('storeAnamnesa');
                Route::post('/delete', 'delete')->name('deleteAnamnesa');
            });
        });

        Route::controller(RekeningController::class)->group(function () {
            Route::group(['prefix' => 'rekening'], function () {
                Route::get('/index', 'index')->name('rekening');
                Route::get('/datatable', 'datatable')->name('datatableRekening');
                Route::get('/edit', 'edit')->name('editRekening');
                Route::get('/status', 'status')->name('statusRekening');
                Route::post('/store', 'store')->name('storeRekening');
                Route::post('/delete', 'delete')->name('deleteRekening');
            });
        });
    });

    Route::group(['prefix' => 'management-obat'], function () {
        Route::controller(KategoriObatController::class)->group(function () {
            Route::group(['prefix' => 'kategori-obat'], function () {
                Route::get('/index', 'index')->name('kategori-obat');
                Route::get('/datatable', 'datatable')->name('datatableKategoriObat');
                Route::get('/status', 'status')->name('statusKategoriObat');
                Route::get('/edit', 'edit')->name('editKategoriObat');
                Route::get('/sequence', 'sequence')->name('sequenceKategoriObat');
                Route::post('/store', 'store')->name('storeKategoriObat');
                Route::post('/delete', 'delete')->name('deleteKategoriObat');
            });
        });

        Route::controller(TypeObatController::class)->group(function () {
            Route::group(['prefix' => 'type-obat'], function () {
                Route::get('/index', 'index')->name('type-obat');
                Route::get('/datatable', 'datatable')->name('datatableTypeObat');
                Route::get('/status', 'status')->name('statusTypeObat');
                Route::get('/edit', 'edit')->name('editTypeObat');
                Route::get('/sequence', 'sequence')->name('sequenceTypeObat');
                Route::post('/store', 'store')->name('storeTypeObat');
                Route::post('/delete', 'delete')->name('deleteTypeObat');
            });
        });

        Route::controller(SatuanObatController::class)->group(function () {
            Route::group(['prefix' => 'satuan-obat'], function () {
                Route::get('/index', 'index')->name('satuan-obat');
                Route::get('/datatable', 'datatable')->name('datatableSatuanObat');
                Route::get('/status', 'status')->name('statusSatuanObat');
                Route::get('/edit', 'edit')->name('editSatuanObat');
                Route::get('/sequence', 'sequence')->name('sequenceSatuanObat');
                Route::post('/store', 'store')->name('storeSatuanObat');
                Route::post('/delete', 'delete')->name('deleteSatuanObat');
            });
        });

        Route::controller(ProdukObatController::class)->group(function () {
            Route::group(['prefix' => 'produk-obat'], function () {
                Route::get('/index', 'index')->name('produk-obat');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeProdukObat');
                Route::get('/datatable', 'datatable')->name('datatableProdukObat');
                Route::get('/status', 'status')->name('statusProdukObat');
                Route::get('/edit', 'edit')->name('editProdukObat');
                Route::get('/sequence', 'sequence')->name('sequenceProdukObat');
                Route::get('/kategori-obat-excel', 'kategoriObatExcel')->name('kategoriObatExcel');
                Route::get('/satuan-obat-excel', 'satuanObatExcel')->name('satuanObatExcel');
                Route::get('/type-obat-excel', 'typeObatExcel')->name('typeObatExcel');
                Route::get('/produk-obat-excel', 'produkObatExcel')->name('produkObatExcel');
                Route::post('/store', 'store')->name('storeProdukObat');
                Route::post('/bulk-import', 'bulkImport')->name('bulkImportProdukObat');
                Route::post('/delete', 'delete')->name('deleteProdukObat');
            });
        });

        Route::controller(RuleResepRacikanController::class)->group(function () {
            Route::group(['prefix' => 'rule-resep-racikan'], function () {
                Route::get('/index', 'index')->name('rule-resep-racikan');
                Route::get('/datatable', 'datatable')->name('datatableRuleResepRacikan');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeRuleResepRacikan');
                Route::get('/generate-age', 'generateAge')->name('generateAgeRuleResepRacikan');
                Route::get('/get-dokter', 'getDokter')->name('getDokterRuleResepRacikan');
                Route::get('/select2', 'select2')->name('select2RuleResepRacikan');
                Route::get('/status', 'status')->name('statusRuleResepRacikan');
                Route::get('/edit', 'edit')->name('editRuleResepRacikan');
                Route::get('/sequence', 'sequence')->name('sequenceRuleResepRacikan');
                Route::post('/store', 'store')->name('storeRuleResepRacikan');
                Route::post('/delete', 'delete')->name('deleteRuleResepRacikan');
            });
        });
    });

    Route::group(['prefix' => 'laporan'], function () {
        Route::controller(LaporanPendapatanController::class)->group(function () {
            Route::group(['prefix' => 'laporan-pendapatan'], function () {
                Route::get('/index', 'index')->name('laporan-pendapatan');
            });
        });

        Route::controller(StatistikPendapatanController::class)->group(function () {
            Route::group(['prefix' => 'statistik-pendapatan'], function () {
                Route::get('/index', 'index')->name('statistik-pendapatan');
                Route::get('/getDataHighChartStatistikPendapatan', 'getDataHighChart')->name('getDataHighChartStatistikPendapatan');
                Route::get('/getDataBarHighChartStatistikPendapatan', 'getDataBarHighChart')->name('getDataBarHighChartStatistikPendapatan');
                Route::get('/getDataPieChartStatistikPendapatan', 'getDataPieChart')->name('getDataPieChartStatistikPendapatan');
            });
        });

        Route::controller(RekapPasienMeninggalController::class)->group(function () {
            Route::group(['prefix' => 'rekap-pasien-meninggal'], function () {
                Route::get('/index', 'index')->name('rekap-pasien-meninggal');
                Route::get('/datatable', 'datatable')->name('datatableRekapPasienMeninggal');
                Route::get('/rekap-pasien-meninggal-excel', 'rekapPasienMeninggalExcel')->name('rekapPasienMeninggalExcel');
            });
        });

        Route::controller(LaporanDokterController::class)->group(function () {
            Route::group(['prefix' => 'laporan-dokter'], function () {
                Route::get('/index', 'index')->name('laporan-dokter');
                Route::get('/append-data', 'appendData')->name('append-data-laporan-dokter');
            });
        });

        Route::controller(LaporanPendaftaranController::class)->group(function () {
            Route::group(['prefix' => 'laporan-pendaftaran'], function () {
                Route::get('/index', 'index')->name('laporan-pendaftaran');
                Route::get('/datatable', 'datatable')->name('datatableLaporanPendaftaran');
                Route::get('/append-data', 'appendData')->name('append-data-laporan-pendaftaran');
            });
        });

        Route::controller(LaporanJumlahPasienController::class)->group(function () {
            Route::group(['prefix' => 'laporan-jumlah-pasien'], function () {
                Route::get('/index', 'index')->name('laporan-jumlah-pasien');
            });
        });

        Route::controller(RekapPasienController::class)->group(function () {
            Route::group(['prefix' => 'rekap-pasien'], function () {
                Route::get('/index', 'index')->name('rekap-pasien');
                Route::get('/datatable', 'datatable')->name('datatableRekapPasien');
                Route::get('/get-rekam-medis', 'getRekamMedis')->name('getRekamMedisPasienRekapPasien');
                Route::get('/get-list-ruangan', 'getListRekapPasien')->name('getListRekapPasienRekamMedis');
                Route::get('/tambah-resep', 'tambahResep')->name('tambahResepRekapPasien');
                Route::get('/tambah-racikan-child', 'tambahRacikanChild')->name('tambahRacikanChildRekapPasien');
                Route::get('/status', 'status')->name('statusRekapPasien');
                Route::get('/edit', 'edit')->name('editRekapPasien');
                Route::get('/print', 'print')->name('printRekapPasien');
                Route::get('/sequence', 'sequence')->name('sequenceRekapPasien');
                Route::get('/select2', 'select2')->name('select2RekapPasien');
                Route::post('/store', 'store')->name('storeRekapPasien');
                Route::post('/delete', 'delete')->name('deleteRekapPasien');
            });
        });
    });

    Route::group(['prefix' => 'management-karyawan'], function () {
        Route::controller(KaryawanController::class)->group(function () {
            Route::group(['prefix' => 'karyawan'], function () {
                Route::get('/index', 'index')->name('karyawan');
                Route::get('/datatable', 'datatable')->name('datatableKaryawan');
                Route::get('/status', 'status')->name('statusKaryawan');
                Route::get('/edit', 'edit')->name('editKaryawan');
                Route::get('/sequence', 'sequence')->name('sequenceKaryawan');
                Route::get('/select2', 'select2')->name('select2Karyawan');
                Route::post('/store', 'store')->name('storeKaryawan');
                Route::post('/delete', 'delete')->name('deleteKaryawan');
                Route::post('/bulk-import', 'bulkImport')->name('bulkImportKaryawan');
                Route::get('/branch-excel', 'branchExcel')->name('branchExcel');
                Route::get('/divisi-excel', 'divisiExcel')->name('divisiExcel');
                Route::get('/bagian-excel', 'bagianExcel')->name('bagianExcel');
                Route::get('/jabatan-excel', 'jabatanExcel')->name('jabatanExcel');
                Route::get('/provinsi-excel', 'provinsiExcel')->name('provinsiExcel');
                Route::get('/kota-excel', 'kotaExcel')->name('kotaExcel');
                Route::get('/kecamatan-excel', 'kecamatanExcel')->name('kecamatanExcel');
                Route::get('/kelurahan-excel', 'kelurahanExcel')->name('kelurahanExcel');
            });
        });

        Route::controller(UserController::class)->group(function () {
            Route::group(['prefix' => 'user'], function () {
                Route::get('/index', 'index')->name('user');
                Route::get('/datatable', 'datatable')->name('datatableUser');
                Route::get('/status', 'status')->name('statusUser');
                Route::get('/select2', 'select2')->name('select2User');
                Route::get('/edit', 'edit')->name('editUser');
                Route::get('/sequence', 'sequence')->name('sequenceUser');
                Route::post('/store', 'store')->name('storeUser');
                Route::post('/delete', 'delete')->name('deleteUser');
                Route::get('/profile', function () {
                    return view('management_karyawan/user/profile');
                })->name('profile');
                Route::post('/profile/update', 'updateProfile')->name('updateProfile');
                Route::post('/profile/update-image', 'updateImageProfile')->name('updateImageProfile');
                Route::post('/profile/update-password', 'updatePassword')->name('updatePassword');
                Route::get('/edit-profile', 'editProfile')->name('editProfile');
                Route::post('/store-profile', 'storeProfile')->name('storeProfile');
            });
        });

        Route::controller(JadwalDokterController::class)->group(function () {
            Route::group(['prefix' => 'jadwal-dokter'], function () {
                Route::get('/index', 'index')->name('jadwal-dokter');
                Route::get('/datatable', 'datatable')->name('datatableJadwalDokter');
                Route::get('/datatable-data', 'datatableData')->name('datatableDataJadwalDokter');
                Route::get('/datatable-add-dokter', 'datatableAddDokter')->name('datatableAddDokterJadwalDokter');
                Route::get('/status', 'status')->name('statusJadwalDokter');
                Route::get('/add-dokter', 'addDokter')->name('addDokterJadwalDokter');
                Route::get('/edit', 'edit')->name('editJadwalDokter');
                Route::get('/sequence', 'sequence')->name('sequenceJadwalDokter');
                Route::post('/store', 'store')->name('storeJadwalDokter');
                Route::post('/delete', 'delete')->name('deleteJadwalDokter');
            });
        });

        Route::controller(PindahJadwalJagaController::class)->group(function () {
            Route::group(['prefix' => 'pindah-jadwal-jaga'], function () {
                Route::get('/index', 'index')->name('pindah-jadwal-jaga');
                Route::get('/datatable', 'datatable')->name('datatablePindahJadwalJaga');
                Route::get('/datatable-data', 'datatableData')->name('datatableDataPindahJadwalJaga');
                Route::get('/select2', 'select2')->name('select2PindahJadwalJaga');
                Route::get('/generate-hari', 'generateHari')->name('generateHariPindahJadwalJaga');
                Route::get('/status', 'status')->name('statusPindahJadwalJaga');
                Route::get('/edit', 'edit')->name('editPindahJadwalJaga');
                Route::get('/sequence', 'sequence')->name('sequencePindahJadwalJaga');
                Route::post('/store', 'store')->name('storePindahJadwalJaga');
                Route::post('/delete', 'delete')->name('deletePindahJadwalJaga');
            });
        });

        Route::controller(PengumumanKaryawanController::class)->group(function () {
            Route::group(['prefix' => 'pengumuman-karyawan'], function () {
                Route::get('/index', 'index')->name('pengumuman-karyawan');
                Route::get('/datatable', 'datatable')->name('datatablePengumumanKaryawan');
                Route::get('/status', 'status')->name('statusPengumumanKaryawan');
                Route::get('/edit', 'edit')->name('editPengumumanKaryawan');
                Route::get('/sequence', 'sequence')->name('sequencePengumumanKaryawan');
                Route::post('/store', 'store')->name('storePengumumanKaryawan');
                Route::post('/delete', 'delete')->name('deletePengumumanKaryawan');
            });
        });
    });

    Route::group(['prefix' => 'management-pasien'], function () {
        Route::controller(OwnerController::class)->group(function () {
            Route::group(['prefix' => 'owner'], function () {
                Route::get('/index', 'index')->name('owner');
                Route::post('/owner-export', 'OwnerExport')->name('OwnerExport');
                Route::get('/datatable', 'datatable')->name('datatableOwner');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeOwner');
                Route::get('/status', 'status')->name('statusOwner');
                Route::get('/edit', 'edit')->name('editOwner');
                Route::get('/sequence', 'sequence')->name('sequenceOwner');
                Route::post('/store', 'store')->name('storeOwner');
                Route::post('/store-catatan', 'storeCatatan')->name('storeCatatanOwner');
                Route::post('/delete', 'delete')->name('deleteOwner');
                Route::get('/regenerate-code', 'regenerateCode')->name('regenerateCodeOwner');
            });
        });

        Route::controller(PasienController::class)->group(function () {
            Route::group(['prefix' => 'pasien'], function () {
                Route::get('/index', 'index')->name('pasien');
                Route::get('/generate-kode', 'generateKode')->name('generateKodePasien');
                Route::get('/get-rekam-medis', 'getRekamMedis')->name('getRekamMedisPasien');
                Route::get('/select2', 'select2')->name('select2Pasien');
                Route::get('/datatable', 'datatable')->name('datatablePasien');
                Route::get('/status', 'status')->name('statusPasien');
                Route::get('/edit', 'edit')->name('editPasien');
                Route::get('/sequence', 'sequence')->name('sequencePasien');
                Route::post('/store', 'store')->name('storePasien');
                Route::post('/delete', 'delete')->name('deletePasien');
            });
        });

        Route::controller(PasienController::class)->group(function () {
            Route::group(['prefix' => 'purchase-order'], function () {
                Route::get('/index', 'purchaseOrder')->name('purchase-order');
            });

            Route::group(['prefix' => 'delivery-order'], function () {
                Route::get('/index', 'deliveryOrder')->name('delivery-order');
            });
        });

        Route::controller(RekamMedisController::class)->group(function () {
            Route::group(['prefix' => 'rekam-medis'], function () {
                Route::get('/index', 'index')->name('rekam-medis');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeRekamMedis');
                Route::get('/get-rekam-medis', 'getRekamMedis')->name('getRekamMedisRekamMedis');
                Route::get('/select2', 'select2')->name('select2RekamMedis');
                Route::get('/datatable', 'datatable')->name('datatableRekamMedis');
                Route::get('/status', 'status')->name('statusRekamMedis');
                Route::get('/edit', 'edit')->name('editRekamMedis');
                Route::get('/sequence', 'sequence')->name('sequenceRekamMedis');
                Route::post('/store', 'store')->name('storeRekamMedis');
                Route::post('/delete', 'delete')->name('deleteRekamMedis');
                Route::get('/checkIfRmNoPasien', 'checkIfRmNoPasien')->name('checkIfRmNoPasien');
            });
        });
    });

    Route::group(['prefix' => 'setting'], function () {
        Route::controller(TitleMenuController::class)->group(function () {
            Route::group(['prefix' => 'title-menu'], function () {
                Route::get('/index', 'index')->name('title-menu');
                Route::get('/datatable', 'datatable')->name('datatableTitleMenu');
                Route::get('/status', 'status')->name('statusTitleMenu');
                Route::get('/edit', 'edit')->name('editTitleMenu');
                Route::get('/sequence', 'sequence')->name('sequenceTitleMenu');
                Route::post('/store', 'store')->name('storeTitleMenu');
                Route::post('/delete', 'delete')->name('deleteTitleMenu');
            });
        });

        Route::controller(GroupMenuController::class)->group(function () {
            Route::group(['prefix' => 'group-menu'], function () {
                Route::get('/index', 'index')->name('group-menu');
                Route::get('/datatable', 'datatable')->name('datatableGroupMenu');
                Route::get('/edit', 'edit')->name('editGroupMenu');
                Route::get('/status', 'status')->name('statusGroupMenu');
                Route::post('/store', 'store')->name('storeGroupMenu');
                Route::post('/delete', 'delete')->name('deleteGroupMenu');
                Route::get('/sequence', 'sequence')->name('sequenceGroupMenu');
            });
        });

        Route::controller(MenuController::class)->group(function () {
            Route::group(['prefix' => 'menu'], function () {
                Route::get('/index', 'index')->name('menu');
                Route::get('/datatable', 'datatable')->name('datatableMenu');
                Route::get('/status', 'status')->name('statusMenu');
                Route::get('/edit', 'edit')->name('editMenu');
                Route::get('/sequence', 'sequence')->name('sequenceMenu');
                Route::post('/store', 'store')->name('storeMenu');
                Route::post('/delete', 'delete')->name('deleteMenu');
            });
        });

        Route::controller(RoleController::class)->group(function () {
            Route::group(['prefix' => 'role'], function () {
                Route::get('/index', 'index')->name('role');
                Route::get('/datatable', 'datatable')->name('datatableRole');
                Route::get('/status', 'status')->name('statusRole');
                Route::get('/edit', 'edit')->name('editRole');
                Route::post('/store', 'store')->name('storeRole');
                Route::post('/delete', 'delete')->name('deleteRole');
            });
        });
    });


    Route::group(['prefix' => 'rawat-inap'], function () {
        Route::controller(RuanganController::class)->group(function () {
            Route::group(['prefix' => 'ruangan'], function () {
                Route::get('/index', 'index')->name('ruangan');
                Route::get('/datatable', 'datatable')->name('datatableRuangan');
                Route::get('/get-rekam-medis', 'getRekamMedis')->name('getRekamMedisPasienRuangan');
                Route::get('/get-list-ruangan', 'getListRuangan')->name('getListRuanganRekamMedis');
                Route::get('/tambah-resep', 'tambahResep')->name('tambahResepRuangan');
                Route::get('/tambah-racikan-child', 'tambahRacikanChild')->name('tambahRacikanChildRuangan');
                Route::get('/status', 'status')->name('statusRuangan');

                Route::get('/edit', 'edit')->name('editRuangan');
                Route::get('/edit-form-persetujuan', 'editFormPersetujuan')->name('editFormPersetujuan');
                Route::get('/edit-pulang-paksa', 'editPulangPaksa')->name('editPulangPaksa');
                Route::get('/print', 'print')->name('printRuangan');
                Route::get('/print-pulang-paksa', 'printPulangPaksa')->name('printPulangPaksaRuangan');
                Route::get('/sequence', 'sequence')->name('sequenceRuangan');
                Route::get('/select2', 'select2')->name('select2Ruangan');
                Route::post('/store', 'store')->name('storeRuangan');
                Route::post('/store-temp-form-persetujuan', 'storeTempFormPersetujuan')->name('storeTempFormPersetujuan');
                Route::post('/delete', 'delete')->name('deleteRuangan');
                Route::get('/delete-form-persetujuan', 'deleteFormPersetujuan')->name('deleteFormPersetujuan');
            });
        });

        Route::controller(ApotekController::class)->group(function () {
            Route::group(['prefix' => 'apotek'], function () {
                Route::get('/index', 'index')->name('apotek');
                Route::get('/history', 'history')->name('historyApotek');
                Route::get('/datatable', 'datatable')->name('datatableApotek');
                Route::get('/select2', 'select2')->name('select2Apotek');
                Route::get('/tambah-resep', 'tambahResep')->name('tambahResepApotek');
                Route::get('/generate-on-queue', 'generateOnQueue')->name('generateOnQueue');
                Route::get('/get-apotek', 'getApotek')->name('getApotek');
                Route::get('/tambah-racikan-child', 'tambahRacikanChild')->name('tambahRacikanChildApotek');
                Route::get('/status', 'status')->name('statusApotek');
                Route::get('/edit', 'edit')->name('editApotek');
                Route::get('/sequence', 'sequence')->name('sequenceApotek');
                Route::post('/store', 'store')->name('storeApotek');
                Route::post('/delete', 'delete')->name('deleteApotek');
                Route::post('/status-apoteker', 'changeStatusApoteker')->name('statusApoteker');
                Route::post('/save', 'saveResep')->name('saveResep');
                Route::get('/check-log-deleted', 'checkLogDeleted')->name('checkLogDeletedApotek');
            });
        });

        Route::controller(BedahController::class)->group(function () {
            Route::group(['prefix' => 'bedah'], function () {
                Route::get('/index', 'index')->name('bedah');
                Route::get('/datatable', 'datatable')->name('datatableBedah');
                Route::get('/get-rekam-medis', 'getRekamMedis')->name('getRekamMedisPasienBedah');
                Route::get('/tambah-resep', 'tambahResep')->name('tambahResepBedah');
                Route::get('/tambah-racikan-child', 'tambahRacikanChild')->name('tambahRacikanChildBedah');
                Route::get('/status', 'status')->name('statusBedah');
                Route::get('/edit', 'edit')->name('editBedah');
                Route::get('/bedah-excel', 'bedahExcel')->name('bedahExcel');
                Route::get('/sequence', 'sequence')->name('sequenceBedah');
                Route::get('/select2', 'select2')->name('select2Bedah');
                Route::post('/store', 'store')->name('storeBedah');
                Route::post('/delete', 'delete')->name('deleteBedah');
            });
        });
    });


    Route::group(['prefix' => 'management-stock'], function () {
        Route::controller(SatuanNonObatController::class)->group(function () {
            Route::group(['prefix' => 'satuan-non-obat'], function () {
                Route::get('/index', 'index')->name('satuan-non-obat');
                Route::get('/datatable', 'datatable')->name('datatableSatuanNonObat');
                Route::get('/status', 'status')->name('statusSatuanNonObat');
                Route::get('/edit', 'edit')->name('editSatuanNonObat');
                Route::get('/sequence', 'sequence')->name('sequenceSatuanNonObat');
                Route::post('/store', 'store')->name('storeSatuanNonObat');
                Route::post('/delete', 'delete')->name('deleteSatuanNonObat');
            });
        });

        Route::controller(ItemNonObatController::class)->group(function () {
            Route::group(['prefix' => 'item-non-obat'], function () {
                Route::get('/index', 'index')->name('item-non-obat');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeItemNonObat');
                Route::get('/datatable', 'datatable')->name('datatableItemNonObat');
                Route::get('/status', 'status')->name('statusItemNonObat');
                Route::get('/edit', 'edit')->name('editItemNonObat');
                Route::get('/sequence', 'sequence')->name('sequenceItemNonObat');
                Route::post('/store', 'store')->name('storeItemNonObat');
                Route::post('/delete', 'delete')->name('deleteItemNonObat');
            });
        });

        Route::controller(SupplierController::class)->group(function () {
            Route::group(['prefix' => 'supplier'], function () {
                Route::get('/index', 'index')->name('supplier');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeSupplier');
                Route::get('/datatable', 'datatable')->name('datatableSupplier');
                Route::get('/status', 'status')->name('statusSupplier');
                Route::get('/edit', 'edit')->name('editSupplier');
                Route::get('/sequence', 'sequence')->name('sequenceSupplier');
                Route::post('/store', 'store')->name('storeSupplier');
                Route::post('/delete', 'delete')->name('deleteSupplier');
            });
        });

        Route::controller(PenerimaanStockController::class)->group(function () {
            Route::group(['prefix' => 'penerimaan-stock'], function () {
                Route::get('/index', 'index')->name('penerimaan-stock');
                Route::get('/create', 'create')->name('createPenerimaanStock');
                Route::get('/generate-kode', 'generateKode')->name('generateKodePenerimaanStock');
                Route::get('/select2', 'select2')->name('select2PenerimaanStock');
                Route::get('/datatable', 'datatable')->name('datatablePenerimaanStock');
                Route::get('/status', 'status')->name('statusPenerimaanStock');
                Route::get('/edit/{id}', 'edit')->name('editPenerimaanStock');
                Route::get('/lihat/{id}', 'lihat')->name('lihatPenerimaanStock');
                Route::get('/terima/{id}', 'terima')->name('terimaPenerimaanStock');
                Route::get('/sequence', 'sequence')->name('sequencePenerimaanStock');
                Route::post('/store', 'store')->name('storePenerimaanStock');
                Route::post('/update', 'update')->name('updatePenerimaanStock');
                Route::post('/delete', 'delete')->name('deletePenerimaanStock');
            });
        });

        Route::controller(PengeluaranStockController::class)->group(function () {
            Route::group(['prefix' => 'pengeluaran-stock'], function () {
                Route::get('/index', 'index')->name('pengeluaran-stock');
                Route::get('/create', 'create')->name('createPengeluaranStock');
                Route::get('/generate-kode', 'generateKode')->name('generateKodePengeluaranStock');
                Route::get('/select2', 'select2')->name('select2PengeluaranStock');
                Route::get('/datatable', 'datatable')->name('datatablePengeluaranStock');
                Route::get('/status', 'status')->name('statusPengeluaranStock');
                Route::get('/edit/{id}', 'edit')->name('editPengeluaranStock');
                Route::get('/lihat/{id}', 'lihat')->name('lihatPengeluaranStock');
                Route::get('/print/{id}', 'print')->name('printPengeluaranStock');
                Route::get('/sequence', 'sequence')->name('sequencePengeluaranStock');
                Route::post('/store', 'store')->name('storePengeluaranStock');
                Route::post('/update', 'update')->name('updatePengeluaranStock');
                Route::post('/delete', 'delete')->name('deletePengeluaranStock');
            });
        });

        Route::controller(StockController::class)->group(function () {
            Route::group(['prefix' => 'stock'], function () {
                Route::get('/index', 'index')->name('stock');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeStock');
                Route::get('/datatable', 'datatable')->name('datatableStock');
                Route::get('/status', 'status')->name('statusStock');
                Route::get('/edit', 'edit')->name('editStock');
                Route::get('/sequence', 'sequence')->name('sequenceStock');
                Route::post('/store', 'store')->name('storeStock');
                Route::post('/delete', 'delete')->name('deleteStock');
                Route::get('/rekon-stock', 'rekonStock')->name('rekonStock');
            });
        });

        Route::controller(MutasiStockController::class)->group(function () {
            Route::group(['prefix' => 'mutasi-stock'], function () {
                Route::get('/index', 'index')->name('mutasi-stock');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeMutasiStock');
                Route::get('/datatable', 'datatable')->name('datatableMutasiStock');
                Route::get('/status', 'status')->name('statusMutasiStock');
                Route::get('/edit', 'edit')->name('editMutasiStock');
                Route::get('/sequence', 'sequence')->name('sequenceMutasiStock');
                Route::post('/store', 'store')->name('storeMutasiStock');
                Route::post('/delete', 'delete')->name('deleteMutasiStock');
                Route::get('/mutasi-stock-excel', 'mutasiStockExcel')->name('mutasiStockExcel');
            });
        });

        Route::controller(PermintaanStockController::class)->group(function () {
            Route::group(['prefix' => 'permintaan-stock'], function () {
                Route::get('/index', 'index')->name('permintaan-stock');
                Route::get('/generate-kode', 'generateKode')->name('generateKodePermintaanStock');
                Route::get('/datatable', 'datatable')->name('datatablePermintaanStock');
                Route::get('/status', 'status')->name('statusPermintaanStock');
                Route::get('/edit', 'edit')->name('editPermintaanStock');
                Route::get('/sequence', 'sequence')->name('sequencePermintaanStock');
                Route::get('/confirmation', 'confirmation')->name('confirmationPermintaanStock');
                Route::post('/delete', 'delete')->name('deletePermintaanStock');
            });
        });
    });

    Route::group(['prefix' => 'products'], function () {
        Route::controller(ListProductController::class)->group(function () {
            Route::group(['prefix' => 'list-product'], function () {
                Route::get('/index', 'index')->name('list-product');
            });
        });

        Route::controller(MerkController::class)->group(function () {
            Route::group(['prefix' => 'merk'], function () {
                Route::get('/index', 'index')->name('merk');
            });
        });

        Route::controller(PrintBarcodeController::class)->group(function () {
            Route::group(['prefix' => 'print-barcode'], function () {
                Route::get('/index', 'index')->name('print-barcode');
            });
        });

        Route::controller(PenyesuaianKuantitasController::class)->group(function () {
            Route::group(['prefix' => 'penyesuaian-kuantitas'], function () {
                Route::get('/index', 'index')->name('penyesuaian-kuantitas');
            });
        });
    });

    Route::group(['prefix' => 'penerimaan'], function () {
        Route::controller(ListPenerimaanController::class)->group(function () {
            Route::group(['prefix' => 'list-penerimaan'], function () {
                Route::get('/index', 'index')->name('list-penerimaan');
            });
        });
    });

    Route::group(['prefix' => 'pembelian'], function () {
        Route::controller(ListPembelianController::class)->group(function () {
            Route::group(['prefix' => 'list-pembelian'], function () {
                Route::get('/index', 'index')->name('list-pembelian');
            });
        });
    });

    Route::group(['prefix' => 'penjualan'], function () {
        Route::controller(ListPenjualanController::class)->group(function () {
            Route::group(['prefix' => 'list-penjualan-penjualan'], function () {
                Route::get('/index', 'index')->name('list-penjualan-penjualan');
            });
        });

        Route::controller(PengirimanController::class)->group(function () {
            Route::group(['prefix' => 'pengiriman-penjualan'], function () {
                Route::get('/index', 'index')->name('pengiriman-penjualan');
            });
        });

        Route::controller(ListPenjualanController::class)->group(function () {
            Route::group(['prefix' => 'list-penjualan'], function () {
                Route::get('/index', 'index')->name('list-penjualan');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeListPenjualan');
                Route::get('/datatable', 'datatable')->name('datatableListPenjualan');
                Route::get('/status', 'status')->name('statusListPenjualan');
                Route::get('/edit', 'edit')->name('editListPenjualan');
                Route::get('/sequence', 'sequence')->name('sequenceListPenjualan');
                Route::get('/confirmation', 'confirmation')->name('confirmationListPenjualan');
                Route::get('/check-stock', 'checkStock')->name('checkStockListPenjualan');
                Route::post('/store-deliver', 'storeDeliver')->name('storeDeliverListPenjualan');
                Route::post('/delete', 'delete')->name('deleteListPenjualan');
            });
        });

        Route::controller(PengirimanController::class)->group(function () {
            Route::group(['prefix' => 'pengiriman'], function () {
                Route::get('/index', 'index')->name('pengiriman');
                Route::get('/generate-kode', 'generateKode')->name('generateKodePengiriman');
                Route::get('/datatable', 'datatable')->name('datatablePengiriman');
                Route::get('/status', 'status')->name('statusPengiriman');
                Route::get('/edit', 'edit')->name('editPengiriman');
                Route::get('/sequence', 'sequence')->name('sequencePengiriman');
                Route::get('/confirmation', 'confirmation')->name('confirmationPengiriman');
                Route::get('/print', 'print')->name('printPengiriman');
                Route::post('/delete', 'delete')->name('deletePengiriman');
            });
        });
    });

    Route::group(['prefix' => 'pembayaran'], function () {
        Route::controller(ListPenjualanController::class)->group(function () {
            Route::group(['prefix' => 'list-penjualan-pembayaran'], function () {
                Route::get('/index', 'index')->name('list-penjualan-pembayaran');
            });
        });

        Route::controller(PengirimanController::class)->group(function () {
            Route::group(['prefix' => 'pengiriman-pembayaran'], function () {
                Route::get('/index', 'index')->name('pengiriman-pembayaran');
            });
        });
    });

    Route::group(['prefix' => 'permintaan'], function () {
        Route::controller(ListPermintaanController::class)->group(function () {
            Route::group(['prefix' => 'list-permintaan'], function () {
                Route::get('/index', 'index')->name('list-permintaan');
            });
        });
    });

    Route::group(['prefix' => 'daftar-persetujuan'], function () {
        Route::controller(DaftarPersetujuanController::class)->group(function () {
            Route::group(['prefix' => 'list-persetujuan'], function () {
                Route::get('/index', 'index')->name('list-persetujuan');
            });
        });
    });

    Route::group(['prefix' => 'retur'], function () {
        Route::controller(DaftarReturController::class)->group(function () {
            Route::group(['prefix' => 'daftar-retur'], function () {
                Route::get('/index', 'index')->name('daftar-retur');
            });
        });
    });

    Route::group(['prefix' => 'laporan'], function () {
        Route::controller(LaporanPenjualanController::class)->group(function () {
            Route::group(['prefix' => 'laporan-penjualan'], function () {
                Route::get('/index', 'index')->name('laporan-penjualan');
            });
        });

        Route::controller(LaporanProdukController::class)->group(function () {
            Route::group(['prefix' => 'laporan-produk'], function () {
                Route::get('/index', 'index')->name('laporan-produk');
            });
        });

        Route::controller(LaporanPenyesuaianController::class)->group(function () {
            Route::group(['prefix' => 'laporan-penyesuaian'], function () {
                Route::get('/index', 'index')->name('laporan-penyesuaian');
            });
        });

        Route::controller(LaporanMerkController::class)->group(function () {
            Route::group(['prefix' => 'laporan-merk'], function () {
                Route::get('/index', 'index')->name('laporan-merk');
            });
        });

        Route::controller(LaporanPembayaranController::class)->group(function () {
            Route::group(['prefix' => 'laporan-pembayaran'], function () {
                Route::get('/index', 'index')->name('laporan-pembayaran');
            });
        });

        Route::controller(LaporanPajakController::class)->group(function () {
            Route::group(['prefix' => 'laporan-pajak'], function () {
                Route::get('/index', 'index')->name('laporan-pajak');
            });
        });

        Route::controller(LaporanPembelianController::class)->group(function () {
            Route::group(['prefix' => 'laporan-pembelian'], function () {
                Route::get('/index', 'index')->name('laporan-pembelian');
            });
        });

        Route::controller(LaporanSupplierController::class)->group(function () {
            Route::group(['prefix' => 'laporan-supplier'], function () {
                Route::get('/index', 'index')->name('laporan-supplier');
            });
        });
    });

    Route::group(['prefix' => 'karyawan'], function () {
        Route::controller(DaftarUserController::class)->group(function () {
            Route::group(['prefix' => 'daftar-user'], function () {
                Route::get('/index', 'index')->name('daftar-user');
            });
        });

        Route::controller(SupplierController::class)->group(function () {
            Route::group(['prefix' => 'supplier'], function () {
                Route::get('/index', 'index')->name('supplier');
            });
        });

        Route::controller(SupplierController::class)->group(function () {
            Route::group(['prefix' => 'supplier-warehouse'], function () {
                Route::get('/index', 'index')->name('supplier-warehouse');
            });
        });

        Route::controller(PemberitahuanController::class)->group(function () {
            Route::group(['prefix' => 'pemberitahuan'], function () {
                Route::get('/index', 'index')->name('pemberitahuan');
            });
        });

        Route::controller(KalenderController::class)->group(function () {
            Route::group(['prefix' => 'kalender'], function () {
                Route::get('/index', 'index')->name('kalender');
            });
        });

        Route::controller(GudangController::class)->group(function () {
            Route::group(['prefix' => 'gudang'], function () {
                Route::get('/index', 'index')->name('gudang');
            });
        });

        Route::controller(BranchController::class)->group(function () {
            Route::group(['prefix' => 'branch-karyawan'], function () {
                Route::get('/index', 'index')->name('branch-karyawan');
            });
        });
    });

    Route::group(['prefix' => 'transaksi'], function () {
        Route::controller(TransaksiKasCabangController::class)->group(function () {
            Route::group(['prefix' => 'transaksi-kas-cabang'], function () {
                Route::get('/index', 'index')->name('transaksi-kas-cabang');
            });
        });

        Route::controller(MasterAkunTransaksiController::class)->group(function () {
            Route::group(['prefix' => 'master-akun-transaksi'], function () {
                Route::get('/index', 'index')->name('master-akun-transaksi');
            });
        });

        Route::controller(BuktiKasKeluarController::class)->group(function () {
            Route::group(['prefix' => 'bukti-kas-keluar'], function () {
                Route::get('/index', 'index')->name('bukti-kas-keluar');
                Route::get('/create', 'create')->name('createBuktiKasKeluar');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeBuktiKasKeluar');
                Route::get('/select2', 'select2')->name('select2BuktiKasKeluar');
                Route::get('/datatable', 'datatable')->name('datatableBuktiKasKeluar');
                Route::get('/datatable-detail', 'datatableDetail')->name('datatableDetailBuktiKasKeluar');
                Route::get('/status', 'status')->name('statusBuktiKasKeluar');
                Route::get('/edit/{id}', 'edit')->name('editBuktiKasKeluar');
                Route::get('/lihat/{id}', 'lihat')->name('lihatBuktiKasKeluar');
                Route::get('/print/{id}', 'print')->name('printBuktiKasKeluar');
                Route::get('/sequence', 'sequence')->name('sequenceBuktiKasKeluar');
                Route::post('/store', 'store')->name('storeBuktiKasKeluar');
                Route::post('/update', 'update')->name('updateBuktiKasKeluar');
                Route::post('/delete', 'delete')->name('deleteBuktiKasKeluar');
            });
        });

        Route::controller(TagihanSementaraController::class)->group(function () {
            Route::group(['prefix' => 'tagihan-sementara'], function () {
                Route::get('/index', 'index')->name('tagihan-sementara');
                Route::get('/datatable', 'datatable')->name('datatableTagihanSementara');
                Route::get('/get-tagihan-sementara', 'getTagihanSementara')->name('getTagihanSementara');
                Route::get('/print', 'print')->name('printTagihanSementara');
            });
        });

        Route::controller(PembayaranController::class)->group(function () {
            Route::group(['prefix' => 'pembayaran'], function () {
                Route::get('/index', 'index')->name('pembayaran');
                Route::get('/datatable', 'datatable')->name('datatablePembayaran');
                Route::get('/datatable-deposit', 'datatableDeposit')->name('datatableDepositPembayaran');
                Route::get('/pilihDeposit', 'pilihDeposit')->name('pilihDepositPembayaran');
                Route::get('/get-pembayaran', 'getPembayaran')->name('getPembayaran');
                Route::get('/get-terbilang', 'getTerbilang')->name('getTerbilang');
                Route::get('/get-pasien-pembayaran', 'getPasienPembayaran')->name('getPasienPembayaran');

                Route::get('/get-list-ruangan', 'getListPembayaran')->name('getListPembayaranRekamMedis');
                Route::get('/tambah-resep', 'tambahResep')->name('tambahResepPembayaran');
                Route::get('/tambah-racikan-child', 'tambahRacikanChild')->name('tambahRacikanChildPembayaran');
                Route::get('/status', 'status')->name('statusPembayaran');
                Route::get('/generate-item-kasir', 'generateItem')->name('generateItemKasir');
                Route::get('/edit', 'edit')->name('editPembayaran');
                Route::get('/print', 'print')->name('printPembayaran');
                Route::get('/send-invoice', 'sendInvoice')->name('sendInvoicePembayaran');
                Route::get('/kwitansi', 'printKwitansi')->name('printKwitansiPembayaran');
                Route::get('/sequence', 'sequence')->name('sequencePembayaran');
                Route::get('/select2', 'select2')->name('select2Pembayaran');
                Route::post('/store', 'store')->name('storePembayaran');
                Route::post('/delete', 'delete')->name('deletePembayaran');
                Route::post('/backToApotek', 'backToApotek')->name('backToApotek');
                Route::post('/backToRanap', 'backToRanap')->name('backToRanap');
                Route::get('/refresh-data-stock', 'refreshDataStock')->name('refreshDataStock');
            });
        });

        Route::controller(TransaksiKasController::class)->group(function () {
            Route::group(['prefix' => 'transaksi-kas'], function () {
                Route::get('/index', 'index')->name('transaksi-kas');
            });
        });

        Route::controller(DepositController::class)->group(function () {
            Route::group(['prefix' => 'deposit'], function () {
                Route::get('/index', 'index')->name('deposit');
                Route::get('/datatable', 'datatable')->name('datatableDeposit');
                Route::get('/datatable-histori-pemakaian', 'datatableHistoriPemakaian')->name('datatableHistoriPemakaianDeposit');
                Route::get('/status', 'status')->name('statusDeposit');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeDeposit');
                Route::get('/edit', 'edit')->name('editDeposit');
                Route::get('/print', 'print')->name('printDeposit');
                Route::get('/print-history', 'printHistory')->name('printHistoryDeposit');
                Route::get('/sequence', 'sequence')->name('sequenceDeposit');
                Route::post('/store', 'store')->name('storeDeposit');
                Route::post('/delete', 'delete')->name('deleteDeposit');
                Route::post('/update', 'update')->name('updateDeposit');
                Route::post('/tarik', 'tarik')->name('tarikDeposit');
            });
        });

        Route::controller(CicilanController::class)->group(function () {
            Route::group(['prefix' => 'cicilan'], function () {
                Route::get('/index', 'index')->name('cicilan');
                Route::get('/datatable', 'datatable')->name('datatableCicilan');
                Route::get('/status', 'status')->name('statusCicilan');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeCicilan');
                Route::get('/get-historis-pembayaran', 'getHistorisPembayaran')->name('getHistorisPembayaran');
                Route::get('/edit', 'edit')->name('editCicilan');
                Route::get('/edit-cicilan-pembayaran', 'editCicilanPembayaran')->name('editCicilanPembayaran');
                Route::get('/sequence', 'sequence')->name('sequenceCicilan');
                Route::post('/store', 'store')->name('storeCicilan');
                Route::post('/delete', 'delete')->name('deleteCicilan');
                Route::get('/print-bukti-pembayaran', 'printBuktiPembayaran')->name('printBuktiPembayaranCicilan');
                Route::get('/cicilan-excel', 'CicilanExcel')->name('CicilanExcel');
                Route::post('/upload-cicilan-pembayaran', 'uploadCicilanPembayaran')->name('uploadCicilanPembayaran');
            });
        });

        Route::controller(RekapInvoiceController::class)->group(function () {
            Route::group(['prefix' => 'rekap-invoice'], function () {
                Route::get('/index', 'index')->name('rekap-invoice');
                Route::get('/datatable', 'datatable')->name('datatableRekapInvoice');
                Route::get('/status', 'status')->name('statusRekapInvoice');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeRekapInvoice');
                Route::get('/rekap-invoice-excel', 'rekapInvoiceExcel')->name('rekapInvoiceExcel');
                Route::get('/edit', 'edit')->name('editRekapInvoice');
                Route::get('/sequence', 'sequence')->name('sequenceRekapInvoice');
                Route::post('/store', 'store')->name('storeRekapInvoice');
                Route::post('/update', 'update')->name('updateRekapInvoice');
                Route::post('/delete', 'delete')->name('deleteRekapInvoice');
                Route::post('/upload-bukti-transfer', 'uploadBuktiTransfer')->name('uploadBuktiTransferRekapInvoice');
            });
        });

        Route::controller(HibahController::class)->group(function () {
            Route::group(['prefix' => 'hibah'], function () {
                Route::get('/index', 'index')->name('hibah');
                Route::get('/datatable', 'datatable')->name('datatableHibah');
                Route::get('/status', 'status')->name('statusHibah');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeHibah');
                Route::get('/hibah-excel', 'HibahExcel')->name('HibahExcel');
                Route::get('/edit', 'edit')->name('editHibah');
                Route::get('/sequence', 'sequence')->name('sequenceHibah');
                Route::post('/store', 'store')->name('storeHibah');
                Route::post('/delete', 'delete')->name('deleteHibah');
            });
        });

        Route::controller(LaporanAdminHarianController::class)->group(function () {
            Route::group(['prefix' => 'laporan-admin-harian'], function () {
                Route::get('/index', 'index')->name('laporan-admin-harian');
                Route::get('/append-data', 'appendData')->name('append-data-laporan-admin-harian');
            });
        });

        Route::controller(ProsesDepositController::class)->group(function () {
            Route::group(['prefix' => 'proses-deposit'], function () {
                Route::get('/index', 'index')->name('proses-deposit');
                Route::get('/datatable', 'datatable')->name('datatableProsesDeposit');
                Route::get('/datatable-histori-pemakaian', 'datatableHistoriPemakaian')->name('datatableHistoriPemakaianProsesDeposit');
                Route::get('/status', 'status')->name('statusProsesDeposit');
                Route::get('/generate-kode', 'generateKode')->name('generateKodeProsesDeposit');
                Route::get('/edit', 'edit')->name('editProsesDeposit');
                Route::get('/print', 'print')->name('printProsesDeposit');
                Route::get('/sequence', 'sequence')->name('sequenceProsesDeposit');
                Route::post('/store', 'store')->name('storeProsesDeposit');
                Route::post('/delete', 'delete')->name('deleteProsesDeposit');
                Route::post('/update', 'update')->name('updateProsesDeposit');
                Route::post('/tarik', 'tarik')->name('tarikProsesDeposit');
                Route::post('/cancel', 'cancel')->name('cancelProsesDeposit');
            });
        });

        Route::controller(ReimbursementController::class)->group(function () {
            Route::group(['prefix' => 'reimbursement'], function () {
                Route::get('/index', 'index')->name('reimbursement');
            });
        });

        Route::controller(ReimbursementApprovalController::class)->group(function () {
            Route::group(['prefix' => 'reimbursement-approval'], function () {
                Route::get('/index', 'index')->name('reimbursement-approval');
                Route::get('/datatable', 'datatable')->name('datatableReimbursementApproval');
                Route::get('/edit', 'edit')->name('editReimbursementApproval');
                Route::post('/store', 'store')->name('storeReimbursementApproval');
                Route::post('/submit', 'submit')->name('submitReimbursementApproval');
                Route::post('/delete', 'delete')->name('deleteReimbursementApproval');
                Route::get('/approve', 'approve')->name('approveReimbursementApproval');
                Route::post('/reject', 'reject')->name('rejectReimbursementApproval');
            });
        });

        Route::controller(ReimbursementApprovedController::class)->group(function () {
            Route::group(['prefix' => 'reimbursement-approved'], function () {
                Route::get('/index', 'index')->name('reimbursement-approved');
                Route::get('/datatable', 'datatable')->name('datatableReimbursementApproved');
                Route::get('/edit', 'edit')->name('editReimbursementApproved');
                Route::post('/store', 'store')->name('storeReimbursementApproved');
                Route::post('/submit', 'submit')->name('submitReimbursementApproved');
                Route::post('/delete', 'delete')->name('deleteReimbursementApproved');
                Route::post('/approve', 'approve')->name('approveReimbursementApproved');
                Route::post('/reject', 'reject')->name('rejectReimbursementApproved');
                Route::get('/print', 'print')->name('printReimbursement');
            });
        });
    });



    // Route::controller(PemeriksaanPasienController::class)->group(function () {
    //     Route::group(['prefix' => 'laporan'], function () {
    //         Route::group(['prefix' => 'rekap-pasien'], function () {
    //             Route::get('/index', 'index')->name('rekap-pasien');
    //         });
    //     });
    // });
});


// Route::get('/debug-sentry', function () {
//     throw new Exception('My first Sentry error!');
// });
