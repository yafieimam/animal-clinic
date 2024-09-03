<!-- BEGIN: Delete Confirmation Modal -->
<div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                    <div class="text-3xl mt-5">Are you sure?</div>
                    <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>This process
                        cannot
                        be undone.</div>
                </div>
                <div class="px-5 pb-8 text-center">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                    <button type="button" class="btn btn-danger w-24">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-rekam-medis" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="width: 90% !important;">
        <div class="modal-content" style="background: #f1f5f9 !important">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto no-rekam-medis">Rekam Medis</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3" id="append-rekam-medis">

            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                {{-- <button type="button" class="btn btn-primary" id="simpan" onclick="store()">Simpan</button> --}}
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</div>

<div id="modal-rekam-medis-history" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog" style="width: 80% !important">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Rekam Medis</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3" id="append-rekam-medis-history">

            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</div>
<form id="modal-pindah-ruangan" class="modal modal-data" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Pindah Ruangan</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mb-3 parent-pindah-kamar">
                    <div class="mb-3">
                        <label for="">Pilih Ruangan Rawat Inap</label>
                        <select name="kamar_rawat_inap_dan_bedah_id" class="form-control required"
                            id="kamar_rawat_inap_dan_bedah_id">
                        </select>
                        <input type="hidden" name="jenis" value="kamar">
                    </div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="pindahKamar()">Pindah Ruang Rawat
                    Inap</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-tambah-kondisi-harian" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Kondisi Harian</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-4 parent-kondisi-harian">
                    <label for="" class="form-label font-bold">Suhu {{ dot() }}</label>
                    <div class="input-group parent">
                        <input type="text" class="form-control required maskdec" name="suhu"
                            placeholder="Isi suhu hewan" value="">
                        <input type="hidden" name="jenis" value="kondisi_harian">
                        <div class="input-group-text">
                            C
                        </div>
                    </div>
                </div>
                <div class="col-span-4 parent-kondisi-harian">
                    <label for="" class="form-label font-bold">Makan {{ dot() }}</label>
                    <select name="makan" id="makan" class="form-control required">
                        <option value="">Pilih Status Makan</option>
                        <option value="YA">YA</option>
                        <option value="TIDAK">TIDAK</option>
                    </select>
                </div>
                <div class="col-span-4 parent-kondisi-harian">
                    <label for="" class="form-label font-bold">Minum {{ dot() }}</label>
                    <select name="minum" id="minum" class="form-control required">
                        <option value="">Pilih Status Minum</option>
                        <option value="YA">YA</option>
                        <option value="TIDAK">TIDAK</option>
                    </select>
                </div>
                <div class="col-span-6 parent-kondisi-harian">
                    <label for="" class="form-label font-bold">Urin {{ dot() }}</label>
                    <select name="urin" id="urin" class="form-control required">
                        <option value="">Pilih Status Urin</option>
                        <option value="YA">YA</option>
                        <option value="TIDAK">TIDAK</option>
                    </select>
                </div>
                <div class="col-span-6 parent-kondisi-harian">
                    <label for="" class="form-label font-bold">Feses {{ dot() }}</label>
                    <select name="feses" id="feses" class="form-control required">
                        <option value="">Pilih Status Feses</option>
                        <option value="YA">YA</option>
                        <option value="TIDAK">TIDAK</option>
                    </select>
                </div>
                <div class="col-span-12 parent-kondisi-harian">
                    <label for="" class="form-label font-bold">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" placeholder="Masukan keterangan"></textarea>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="tambahKondisiHarian()">Tambahkan Data
                    Kondisi Harian</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-tambah-diagnosa" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Diagnosa</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mb-3 parent-diagnosa">
                    <div class="mb-3">
                        <label for="" class="form-label">Berikan Diagnosa di rawat inap</label>

                        <textarea id="diagnosa" name="diagnosa" placeholder="Tulis Diagnosa" class="form-control diagnosa required"
                            cols="2" rows="2"></textarea>
                        <input type="hidden" name="jenis" value="diagnosa">
                    </div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="tambahDiagnosa()">Tambahkan Data
                    Diagnosa</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-tambah-hasil-lab" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Hasil Lab</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mb-3 parent-hasil-lab">
                    <div class="mb-3">
                        <label for="" class="form-label">Tambah Hasil Lab</label>
                        <input type="file" class="dropify hasil_lab mb-2 required" id="dropify"
                            name="hasil_lab[]" data-allowed-file-extensions="pdf jpeg jpg png">
                        <input type="hidden" name="jenis" value="hasil lab">
                    </div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="tambahHasilLab()">Tambahkan Data
                    Tindakan</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-tambah-tindakan" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Tindakan</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mb-3 parent-tindakan">
                    <div class="mb-3">
                        <label for="" class="form-label">Jenis Tindakan</label>
                        <select name="tindakan_id" id="tindakan_id"
                            class="form-control tindakan_id select2 required">
                        </select>
                        <input type="hidden" name="jenis" value="tindakan">
                    </div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="tambahTindakan()">Tambahkan Data
                    Tindakan</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-tambah-resep" class="modal modal-data" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Obat</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">

                <div class="col-span-12 parent-tambah-resep">
                    <div class="mb-3">
                        <label for="">Penggunaan Obat</label>
                        <select name="status_resep" class="form-control select2resep" id="status_resep">
                            <option value="Langsung">Obat Rawat Inap</option>
                            <option value="Antrian">Obat Pulang</option>
                        </select>
                    </div>
                </div>
                <div class="col-span-12 parent-tambah-resep">
                    <span for="" class="flex justify-between align-middle mb-3">
                        <b>Obat</b>
                        <button type="button" class="btn btn-primary btn-xs" onclick="appendResep()"
                            id="add-resep"><i class="fa fa-plus"></i>
                            Tambah Obat</button>
                    </span>
                </div>
                <div class="grid grid-cols-12 gap-6 col-span-12 clearfix" id="append-resep">
                </div>
                <input type="hidden" name="jenis" value="resep">
                <div class="loading-resep col-span-12 text-center hidden">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="tambahResep()">Tambahkan Data
                    Obat</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-tambah-catatan" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Catatan</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mb-3 parent-catatan">
                    <div class="mb-3">
                        <label for="" class="form-label">Berikan Catatan Rawat Inap</label>
                        <textarea name="catatan" id="catatan" placeholder="Tulis Catatan" class="form-control catatan required"
                            cols="2" rows="2"></textarea>
                        <input type="hidden" name="jenis" value="catatan">
                    </div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="tambahCatatan()">Tambahkan Data
                    Catatan</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-tambah-pakan" class="modal modal-data" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Pakan</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mb-3 parent-pakan">
                    <div class="mb-3">
                        <label for="" class="form-label">Jenis Pakan</label>
                        <select name="pakan" id="pakan" class="form-control select2pakan required">
                            <option value="">Pilih Jenis Pakan</option>
                            @foreach ($pakan as $item)
                                <option
                                    {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                                    value="{{ $item->id }}">{{ $item->name }}
                                    {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stock Kosong)' : '') : '(Stock Kosong)' }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="jenis" value="pakan">
                    </div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="tambahPakan()">Tambahkan Data
                    Pakan</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-item-non-obat" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Item Non Obat</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mb-3 parent-item-non-obat">
                    <div class="mb-3">
                        <label for="" class="form-label">Jenis Item</label>
                        <select name="item_non_obat_id" id="item_non_obat_id"
                            class="form-control select2itemNonObat required">
                            <option value="">Pilih Jenis Item</option>
                            @foreach ($itemNonObat as $item)
                                <option
                                    {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                                    value="{{ $item->id }}">{{ $item->name }}
                                    {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stock Kosong)' : '') : '(Stock Kosong)' }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="jenis" value="item_non_obat">
                    </div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="tambahItemNonObat()">Tambahkan Data Non
                    Obat</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-rekomendasi-tindakan-bedah" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Rekomendasi Tindakan Bedah</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 parent-rekomendasi-tindakan-bedah parent">
                    <label for="" class="form-label">Jenis Tindakan Bedah
                        {{ dot() }}</label>
                    <select name="rekomendasi_tindakan_bedah" id="rekomendasi_tindakan_bedah"
                        class="form-control select2rekomendasiTindakanBedah required">
                    </select>
                    <input type="hidden" name="jenis" value="rekomendasi_tindakan_bedah">
                </div>
                <div class="col-span-12 parent-rekomendasi-tindakan-bedah">
                    <label for="rekomendasi_tanggal_bedah" class="form-label">Rekomendasi Tanggal Bedah
                        {{ dot() }}</label>
                    <div class="input-group parent">
                        <div class="input-group-text">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <input id="rekomendasi_tanggal_bedah" name="rekomendasi_tanggal_bedah" type="text"
                            class="form-control required  rekomendasi_tanggal_bedah" placeholder="yyyy-mm-dd"
                            placeholder="yyyy-mm-dd" data-single-mode="true">
                    </div>
                </div>
                <div class="col-span-12 parent-rekomendasi-tindakan-bedah">
                    <label for="keterangan_rekomendasi_tindakan_bedah" class="form-label">Keterangan
                    </label>

                    <textarea id="keterangan_rekomendasi_tindakan_bedah" name="keterangan"
                        class="form-control  keterangan_rekomendasi_tindakan_bedah"></textarea>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn mr-1 btn-warning float-left" onclick="printTindakanBedah()">
                    Print Persetujuan
                </button>
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">Cancel</button>

                <button type="button" class="btn btn-primary" onclick="tambahRekomendasiTindakanBedah()">Proses
                    Data</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-form-persetujuan" class="modal modal-data" enctype="multipart/form-data" data-tw-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Form Persetujuan</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-9">
                    <!-- <input type="file" class="dropify" name="form_persetujuan"
                        data-allowed-file-extensions="pdf jpeg jpg png" id="form_persetujuan"> -->
                    <input type="hidden" id="form_persetujuan_id" name="form_persetujuan_id">
                    <input type="hidden" name="jenis" value="upload_form_persetujuan">
                    <input type="hidden" id="form_persetujuan_function" name="form_persetujuan_function" value="upload_form_persetujuan">
                </div>
                <div class="col-span-3">
                    <button type="button" class="btn btn-primary" id="addFileButtonFormPersetujuan">Add More File</button>
                </div>
                <div class="col-span-12 file-container-form-persetujuan" id="fileContainerFormPersetujuan">
                </div>
                <div class="col-span-12">
                    <div
                        class="py-1 px-2 rounded-full w-40 text-xs bg-success text-white cursor-pointer font-medium sudah-upload hidden text-center">
                        Sudah Upload</div>
                    <div
                        class="py-1 px-2 rounded-full w-40 text-xs bg-danger text-white cursor-pointer font-medium belum-upload text-center">
                        Belum Upload</div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <!-- <button type="button" class="btn mr-1 btn-warning float-left sudah-upload"
                    onclick="printFormPersetujuan()">
                    Lihat Form Persetujuan
                </button>

                <button type="button" class="btn mr-1 btn-warning float-left disabled belum-upload"
                    onclick="printFormPersetujuan()">
                    Lihat Form Persetujuan
                </button> -->
                <button type="button" id="reset-form-persetujuan"
                    class="btn btn-primary mr-1">Reset</button>
                <button type="button" id="upload-form-persetujuan"
                    onclick="uploadFormPersetujuan()" class="btn btn-primary mr-1">Upload</button>
                <button type="button" data-tw-dismiss="modal" 
                    class="btn btn-outline-secondary mr-1">Cancel</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>

<form id="modal-pulang-paksa" class="modal modal-data" enctype="multipart/form-data" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Persetujuan Pulang Paksa</h2>
                <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
            </div>
            <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-9">&nbsp;</div>
                <div class="col-span-3">
                    <button type="button" class="btn btn-primary" id="addFileButtonPulangPaksa">Add More File</button>
                </div>
                <div class="col-span-12 parent-pulang-paksa">
                    <label for="alasan_pulang_paksa" class="form-label">
                        Alasan Pulang Paksa
                    </label>
                    <textarea id="alasan_pulang_paksa" readonly name="alasan_pulang_paksa"
                        class="form-control alasan_pulang_paksa required"></textarea>
                    <input type="hidden" id="pulang_paksa_id" name="pulang_paksa_id">
                    <input type="hidden" name="jenis" value="pulang_paksa">
                    <input type="hidden" id="pulang_paksa_function" name="pulang_paksa_function" value="pulang_paksa">
                </div>
                <div class="col-span-12 file-container-pulang-paksa" id="fileContainerPulangPaksa">
                </div>
                <!-- <div class="col-span-12 parent-pulang-paksa">
                    <label for="alasan_pulang_paksa" class="form-label">
                        Upload Print Persetujuan
                    </label>
                    <input type="file" class="dropify upload_pulang_paksa mb-2" id="upload_pulang_paksa"
                        name="upload_pulang_paksa" data-allowed-file-extensions="pdf jpeg jpg png">
                    
                </div> -->
                <div class="col-span-12">
                    <div
                        class="py-1 px-2 rounded-full w-40 text-xs bg-success text-white cursor-pointer font-medium sudah-upload-pulang-paksa hidden text-center">
                        Sudah Upload</div>
                    <div
                        class="py-1 px-2 rounded-full w-40 text-xs bg-danger text-white cursor-pointer font-medium belum-upload-pulang-paksa text-center">
                        Belum Upload</div>
                </div>
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer">
                <!-- <button type="button" class="btn mr-1 btn-warning float-left" onclick="printPulangPaksa()">
                    Lihat Persetujuan
                </button> -->
                <button type="button" id="reset-pulang-paksa"
                    class="btn btn-primary mr-1">Reset</button>
                <button type="button" id="upload-pulang-paksa" 
                    class="btn btn-primary" onclick="pulangPaksa()">Proses Data</button>
                <button type="button" data-tw-dismiss="modal" 
                    class="btn btn-outline-secondary mr-1">Cancel</button>
            </div>
            <!-- END: Modal Footer -->
        </div>
    </div>
</form>
