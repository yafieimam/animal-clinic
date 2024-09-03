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
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
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
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
    <form id="modal-pindah-ruangan" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
        aria-hidden="true">
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
                            <label for="">Pindah Kamar Ke</label>
                            <select name="kamar_rawat_inap_dan_bedah_id" class="form-control"
                                id="kamar_rawat_inap_dan_bedah_id">
                            </select>
                            <input type="hidden" name="jenis" value="kamar">
                        </div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="pindahKamar()">Pindah Kamar</button>
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
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
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
                            <label for="" class="form-label">Isi Diagnosa</label>
                            <textarea name="diagnosa" placeholder="Masukan Diagnosa" class="form-control diagnosa required" cols="2"
                                rows="2"></textarea>
                            <input type="hidden" name="jenis" value="diagnosa">
                        </div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
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
                    <div class="col-span-12 mb-3 parent-tindakan">
                        <div class="mb-3">
                            <label for="" class="form-label">Isi Hasil Lab</label>
                            <input type="file" class="dropify hasil_lab mb-2 required" id="dropify"
                                name="hasil_lab[]" data-allowed-file-extensions="pdf jpeg jpg">
                            <input type="hidden" name="jenis" value="hasil lab">
                        </div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
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
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="tambahTindakan()">Tambahkan Data
                        Tindakan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </form>

    <form id="modal-tambah-resep" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
        aria-hidden="true">
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
                        <span for="" class="flex justify-between align-middle mb-3">
                            <b>Obat</b>
                            <button type="button" class="btn btn-primary btn-xs" onclick="appendResep()"
                                id="add-resep"><i class="fa fa-plus"></i>
                                Tambah Obat</button>
                        </span>
                    </div>
                    <div class="grid grid-cols-12 gap-6 col-span-12 clearfix" id="append-resep">
                    </div>
                    <input type="hidden" name="jenis" value="obat_bedah">
                    <div class="loading-resep col-span-12 text-center hidden">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
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
                            <label for="" class="form-label">Berikan Catatan selama proses Bedah</label>
                            <textarea name="catatan" placeholder="Tulis Catatan" class="form-control catatan required" cols="2"
                                rows="2"></textarea>
                            <input type="hidden" name="jenis" value="catatan">
                        </div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="tambahCatatan()">Tambahkan Data
                        Catatan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </form>

    <form id="modal-tambah-pakan" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
        aria-hidden="true">
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
                            <label for="" class="form-label">Isi Pakan</label>
                            <select name="pakan" id="pakan" class="form-control select2pakan required">
                                <option value="">Pilih pakan</option>
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
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
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
                            <label for="" class="form-label">Isi Item Non Obat</label>
                            <select name="item_non_obat_id" id="item_non_obat_id"
                                class="form-control select2itemNonObat required">
                                <option value="">Pilih Item Non Obat</option>
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
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="tambahItemNonObat()">Tambahkan Data
                        Non Obat</button>
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
                    <h2 class="font-medium text-base mr-auto">Tambah Tindakan Bedah</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 mb-3 parent-tindakan-bedah">
                        <label for="" class="form-label">Jenis Tindakan Bedah</label>
                        <select name="rekomendasi_tindakan_bedah" id="rekomendasi_tindakan_bedah"
                            class="form-control select2rekomendasiTindakanBedah required">
                            <option value="">Pilih Tindakan</option>
                            @foreach ($rekomendasiTindakanBedah as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="jenis" value="tindakan_bedah">
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary"
                        onclick="tambahRekomendasiTindakanBedah()">Tambahkan
                        Data
                        Tindakan Bedah</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </form>

    <form id="modal-form-persetujuan" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
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
                    <div class="col-span-12">
                        <input type="file" class="dropify" name="form_persetujuan"
                        data-allowed-file-extensions="pdf jpeg jpg png" id="form_persetujuan">
                        <input type="hidden" id="form_persetujuan_id" name="rekam_medis_pasien_id">
                        <input type="hidden" id="rekam_medis_rekomendasi_tindakan_bedah_id"
                            name="rekam_medis_rekomendasi_tindakan_bedah_id">
                        <input type="hidden" name="jenis" value="upload_form_persetujuan">
                    </div>
                    <div class="col-span-12">
                        <div
                            class="py-1 px-2 rounded-full w-40 text-xs bg-success text-white cursor-pointer font-medium sudah-upload hidden text-center">
                            Sudah upload</div>
                        <div
                            class="py-1 px-2 rounded-full w-40 text-xs bg-danger text-white cursor-pointer font-medium belum-upload text-center">
                            Belum upload</div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn mr-1 btn-warning float-left" onclick="printFormPersetujuan()">
                        Lihat Form Persetujuan
                    </button>
                    <button type="button" onclick="uploadFormPersetujuan()"
                        class="btn btn-primary mr-1">Perbarui</button>
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </form>

    <form id="modal-di-tolak-bedah" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Penolakan Bedah</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label for="form-label">Alasan Menolak Bedah {{ dot() }}</label>
                        <textarea class="form-control required" name="keterangan" id="alasan_menolak"></textarea>
                        <input type="hidden" name="jenis" value="di_tolak_bedah">
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" onclick="ditolakBedah()" class="btn btn-primary mr-1">Tolak Bedah</button>
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </form>

    <form id="modal-edit-catatan" class="modal modal-data" data-tw-backdrop="static" tabindex="-1"
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
                            <label for="" class="form-label">Rubah catatan pemeriksaan</label>
                            <textarea name="catatan" placeholder="Tulis Catatan" class="form-control catatan required" cols="2"
                                rows="2"></textarea>
                            <input type="hidden" name="jenis" value="catatan-pemeriksaan">
                        </div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="editCatatan()">Tambahkan Data
                        Catatan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </form>
