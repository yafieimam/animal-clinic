@extends('../layout/' . $layout)
@section('content_filter')
    @include('../transaksi/rekap_invoice/filter_rekap_invoice')
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="col-span-12 sm:col-span-6 md:col-span-12 intro-y">
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="col-span-12 sm:col-span-6 md:col-span-6 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i class="fa-solid fa fa-credit-card-alt report-box__icon text-success"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6" id="total-lunas">
                                <?php
                                // $total_lunas = \App\Models\Kasir::where('type_kasir', '=', 'Normal')
                                //     ->where('created_at', '>=', date('Y-m-d'))
                                //     ->where('langsung_lunas', '=', 't')
                                //     ->sum('pembayaran');
                                
                                // $totallunas = \App\Models\KasirPembayaran::where('created_at', '>=', date('Y-m-d'))
                                // ->where('keterangan', 'NOT LIKE', '%Rescue%')
                                // ->sum('nilai_pembayaran');
                                ?>
                                Rp 0.00
                                {{-- IDR {{ number_format($totallunas) }} --}}

                            </div>
                            <div class="text-base text-slate-500 mt-1" style="font-size:25px;">Total Lunas</div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 md:col-span-6 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i class="fa-solid fa-money report-box__icon text-warning"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6" id="total-hutang">
                                <?php
                                // $totalhutang = \App\Models\Kasir::where('type_kasir', '=', 'Normal')
                                //     ->where('created_at', '>=', date('Y-m-d'))
                                //     ->sum('sisa_pelunasan');
                                ?>
                                {{-- IDR {{ number_format($totalhutang) }} --}}
                                Rp 0.00
                            </div>
                            <div class="text-base text-slate-500 mt-1" style="font-size:25px;">Total Belum Lunas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
                <div class="dropdown inline">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center">
                            <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40 ">
                        <ul class="dropdown-content">
                            <li>
                                <a href="javascript:;" class="dropdown-item" onclick="openFilter()">
                                    <i class="w-4 h-4 mr-2 fa-solid fa-filter"></i>
                                    Filter
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" id="myInputTextField" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>


        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <table class="table mt-2 stripe hover" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Opsi</th>
                    <th>No. INV</th>
                    <th>Tanggal Invoice</th>
                    <th>Tanggal Update</th>
                    <th>Branch</th>
                    <th>Type Kasir</th>
                    <th>Nama Customer</th>
                    <th>Total Pembayaran</th>
                    <th>Pembayaran</th>
                    <th>Sisa Tagihan</th>
                    <th>Catatan</th>
                    <th>Bukti Transfer</th>
                    <th>Status Pembayaran</th>
                    <th>Status Transfer</th>
                    <th>Metode Pembayaran</th>
                    <th>Dokter</th>
                    <th>Cashier</th>
                </thead>

                <tbody>

                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
    </div>
    <!-- BEGIN: Delete Confirmation Modal -->
    <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>This process cannot
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
    <!-- BEGIN: Modal Content -->
    <div id="modal-tambah-data" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Data</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <form class="modal-body grid grid-cols-12 gap-4 gap-y-3 " id="form-tambah-data">
                    <div class="col-span-12 parent disabled">
                        <label for="name" class="form-label">Kode Invoice {{ dot() }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control kode" readonly name="kode" id="kode"
                                value="">
                        </div>
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent disabled">
                        <label for="name" class="form-label">Nama Owner {{ dot() }}</label>
                        <input type="text" id="nama_owner" class="form-control" name="nama_owner" readonly>
                    </div>
                    <div class="col-span-12 parent disabled">
                        <label for="nilai_deposit" class="form-label">Sisa Pelunasan {{ dot() }}</label>
                        <input id="sisa_pelunasan" name="sisa_pelunasan" readonly type="text"
                            class="form-control text-right mask" placeholder="Masukan sisa pelunasan">
                    </div>
                    <div class="col-span-12 parent disabled">
                        <label for="pembayaran" class="form-label">Nilai Pembayaran {{ dot() }}</label>
                        <input id="pembayaran" name="pembayaran" type="text"
                            class="form-control text-right mask" readonly placeholder="Masukan nilai pembayaran">
                    </div>
                    <div class="col-span-12">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran {{ dot() }}</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control select2 required">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>
                    <div class="col-span-4 parent non-tunai hidden">
                        <label for="nama_bank" class="form-label">Nama Bank {{ dot() }}</label>
                        <input id="nama_bank" name="nama_bank" type="text"
                            class="form-control uppercase required" placeholder="Masukan nama bank">
                    </div>
                    <div class="col-span-4 parent non-tunai hidden">
                        <label for="nomor_kartu" class="form-label">No. Rekening {{ dot() }}</label>
                        <input type="text" name="nomor_kartu" id="nomor_kartu" placeholder="xxxxxxxxxx"
                            class="form-control required">
                    </div>
                    <div class="col-span-4 parent non-tunai hidden">
                        <label for="nomor_transaksi" class="form-label">No. Transaksi {{ dot() }}</label>
                        <input type="text" name="nomor_transaksi" id="nomor_transaksi" placeholder="xxxxxxxxxx"
                            class="form-control required">
                    </div>
                    <div class="col-span-12 parent disabled">
                        <label for="catatan_kasir" class="form-label">Keterangan</label>
                        <textarea id="catatan_kasir" name="catatan_kasir" type="text" class="form-control" readonly placeholder="Masukan Keterangan"></textarea>
                    </div>
                </form>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary w-20" onclick="store()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>

    <div id="modal-pembayaran" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Historis Pembayaran</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3 " id="historis-pembayaran">

                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>

    <div id="modal-upload-bukti" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Upload Bukti Transfer</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <form class="modal-body grid grid-cols-12 gap-4 gap-y-3 " id="form-data">
                    <div class="col-span-12 parent disabled">
                        <label for="owner_id_bukti" class="form-label">Owner {{ dot() }}</label>
                        <select name="owner_id_bukti" id="owner_id_bukti" class="select2bukti form-control required">
                            <option value="">Pilih Owner</option>
                            @foreach (\App\Models\Owner::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="id_bukti" name="id_bukti">
                        {{ csrf_field() }}
                    </div>
                    {{-- <div class="col-span-12 parent">
                        <label for="sisa_pelunasan_bukti" class="form-label">Tagihan {{ dot() }}
                        </label>
                        <input id="sisa_pelunasan_bukti" readonly name="sisa_pelunasan_bukti" type="text"
                            class="form-control text-right required">
                    </div> --}}
                    {{-- <div class="col-span-12 parent">
                        <label for="nominal_transfer_bukti" class="form-label">Nominal Transfer {{ dot() }}
                        </label>
                        <input id="nominal_transfer_bukti" name="nominal_transfer_bukti" type="text"
                            class="form-control text-right mask required">
                    </div> --}}
                    <div class="col-span-12 parent disabled">
                        <label for="metode_pembayaran_bukti" class="form-label">Metode Pembayaran {{ dot() }}</label>
                        <select name="metode_pembayaran_bukti" id="metode_pembayaran_bukti" class="form-control required">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nama_bank_bukti" class="form-label">Nama Bank</label>
                        <input id="nama_bank_bukti" name="nama_bank_bukti" type="text"
                            class="form-control text-left uppercase" readonly placeholder="Masukan nama bank">
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nomor_kartu_bukti" class="form-label">No. Rekening</label>
                        <input type="text" name="nomor_kartu_bukti" id="nomor_kartu_bukti" readonly placeholder="xxxxxxxxxx"
                            class="form-control">
                    </div>
                    <div class="col-span-12 parent non-tunai hidden">
                        <label for="atas_nama_bukti" class="form-label">Atas Nama</label>
                        <input type="text" name="atas_nama_bukti" id="atas_nama_bukti" readonly
                            placeholder="Masukan Nama di Rekening" class="form-control">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="keterangan" class="form-label">Bukti Transfer {{ dot() }}</label>
                        <input type="file" class="dropify" name="bukti_transfer" id="bukti_transfer" data-max-file-size="5M"
                            data-allowed-file-extensions="jpeg png jpg">
                    </div>
                </form>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary w-20" id="upload" onclick="upload()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>

    <div id="modal-upload-bukti-transfer" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Upload Bukti Transfer</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <form class="modal-body grid grid-cols-12 gap-4 gap-y-3 " id="form-data-bukti-transfer">
                    <div class="col-span-12 parent">
                        <label for="kode_bukti_transfer" class="form-label">Kode Invoice {{ dot() }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control kode" readonly name="kode_bukti_transfer" id="kode_bukti_transfer"
                                value="">
                        </div>
                        <input type="hidden" id="kasir_id_bukti_transfer" name="kasir_id_bukti_transfer">
                        <input type="hidden" id="id_bukti_transfer" name="id_bukti_transfer">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nama_owner_bukti_transfer" class="form-label">Nama Owner {{ dot() }}</label>
                        <input type="text" id="nama_owner_bukti_transfer" class="form-control" name="nama_owner_bukti_transfer" readonly>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nilai_pembayaran_bukti_transfer" class="form-label">Nilai Pembayaran {{ dot() }}</label>
                        <input id="nilai_pembayaran_bukti_transfer" name="nilai_pembayaran_bukti_transfer" type="text"
                            class="form-control text-right mask required" readonly>
                    </div>
                    <div class="col-span-12 parent disabled">
                        <label for="metode_pembayaran_bukti_transfer" class="form-label">Metode Pembayaran {{ dot() }}</label>
                        <select name="metode_pembayaran_bukti_transfer" id="metode_pembayaran_bukti_transfer" class="form-control required">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nama_bank_bukti_transfer" class="form-label">Nama Bank</label>
                        <input id="nama_bank_bukti_transfer" name="nama_bank_bukti_transfer" type="text"
                            class="form-control text-left uppercase" readonly placeholder="Masukan nama bank">
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nomor_kartu_bukti_transfer" class="form-label">No. Rekening</label>
                        <input type="text" name="nomor_kartu_bukti_transfer" id="nomor_kartu_bukti_transfer" readonly placeholder="xxxxxxxxxx"
                            class="form-control">
                    </div>
                    <div class="col-span-12 parent non-tunai hidden">
                        <label for="atas_nama_bukti_transfer" class="form-label">Atas Nama</label>
                        <input type="text" name="atas_nama_bukti_transfer" id="atas_nama_bukti_transfer" readonly
                            placeholder="Masukan Nama di Rekening" class="form-control">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="bukti_transfer_bukti" class="form-label">Bukti Transfer {{ dot() }}</label>
                        <input type="file" class="dropify" name="bukti_transfer_bukti" id="bukti_transfer_bukti" data-max-file-size="5M"
                            data-allowed-file-extensions="jpeg png jpg">
                    </div>
                </form>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary w-20" id="upload_bukti_transfer" onclick="prosesUploadBuktiTransfer()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
    <script>
        var table;
        (function() {
            table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                "sDom": "ltipr",
                "order": [
                    [3, 'desc'],
                ],
                buttons: [
                    $.extend(true, {}, {
                        extend: 'pageLength',
                        className: 'btn btn-primary'
                    }),
                ],
                ajax: {
                    url: "{{ route('datatableRekapInvoice') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tanggal_awal() {
                            return $('#tanggal_awal').val();
                        },
                        tanggal_akhir() {
                            return $('#tanggal_akhir').val();
                        },
                        branch_id() {
                            return $('#branch_id_filter').val();
                        },
                        owner_id() {
                            return $('#owner_id_filter').val();
                        },
                        sisa_pelunasan() {
                            return $('#sisa_pelunasan_filter').val();
                        },
                    }
                },
                responsive: {
                    details: {
                        renderer: function(api, rowIdx, columns) {
                            var data = $.map(columns, function(col, i) {
                                return col.hidden ?
                                    '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' +
                                    col.columnIndex + '">' +
                                    '<td>' + col.title + '</td> ' +
                                    '<td>' + col.data + '</td>' +
                                    '</tr>' :
                                    '';
                            }).join('');

                            return data ? $('<table style="width:100%"/>').append(data) : false;
                        }
                    }
                },
                drawCallback: function() {
                    var api = this.api();
                    var total_lunas = 0;
                    var total_hutang = 0;
                    var data = api.rows().data();
                    if (data.length > 0) {
                        total_lunas = data[0].total_lunas;
                        total_hutang = data[0].total_hutang;
                    }
                    $('#total-lunas').html(total_lunas.toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }));
                    $('#total-hutang').html(total_hutang.toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }));
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    class: 'text-center',
                    orderable: false,
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    class: 'text-center',
                    orderable: false,
                }, {
                    data: 'kode',
                    name: 'kode'
                }, {
                    data: 'tanggal_buat',
                    name: 'tanggal_buat'
                }, {
                    data: 'updated_at',
                    name: 'updated_at'
                }, {
                    data: 'branch',
                    name: 'branch'
                }, {
                    data: 'type_kasir',
                    name: 'type_kasir'
                }, {
                    data: 'nama_owner',
                    name: 'nama_owner',
                }, {
                    data: 'total_bayar',
                    name: 'total_bayar',
                    class: 'text-right',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp '),
                }, {
                    data: 'pembayaran',
                    name: 'pembayaran',
                    class: 'text-right',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp '),
                }, {
                    data: 'sisa_pelunasan',
                    name: 'sisa_pelunasan',
                    class: 'text-center',
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp '),
                }, {
                    data: 'catatan_kasir',
                    name: 'catatan_kasir',
                    class: 'text-center',
                }, {
                    data: 'bukti_transfer',
                    name: 'bukti_transfer',
                    class: 'text-center',
                }, {
                    data: 'status_pembayaran_custom',
                    name: 'status_pembayaran_custom',
                    class: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        if (data == 0) {
                            return '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Belum Lunas</div>';
                        } else {
                            return '<div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Lunas</div>';
                        }
                    }
                }, {
                    data: 'status_transfer',
                    name: 'status_transfer',
                    class: 'text-center'
                }, {
                    data: 'metode_pembayaran',
                    name: 'metode_pembayaran',
                    class: 'text-center'
                }, {
                    data: 'dokter',
                    name: 'dokter',
                    class: 'text-center'
                }, {
                    data: 'created_by',
                    name: 'created_by'
                },]
            });





            $('#myInputTextField').keyup(debounce(function() {
                table.search($(this).val()).draw();
            }, 500));

            $('.mask').maskMoney({
                precision: 0,
                thousands: ','
            })


            $('.select2').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                // theme: 'bootstrap4',
            })

            $('.select2bukti').select2({
                dropdownParent: $("#modal-upload-bukti .modal-body"),
                // theme: 'bootstrap4',
            })

            $('.select2filter').select2({
                dropdownParent: $("#slide-over-filter .modal-body"),
                width: '100%',
            })
            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");
            $('.dropify').dropify();

            $('#tambah-data').click(function() {
                clear();
                $('.not-editable').removeClass('disabled');
                $('.not-editable').prop('readonly', false);

                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();
                generateKode();
            })

            tomGenerator('.tomSelect');
        })()

        function generateKode() {
            if ($('#id').val() == '') {
                $.ajax({
                    url: "{{ route('generateKodeCicilan') }}",
                    type: 'get',
                    success: function(data) {
                        $('#kode').val(data.kode);
                    },
                    error: function(data) {
                        generateKode();
                    }
                });
            }
        }

        function filter() {
            slideOver.toggle();
            table.ajax.reload();
        }

        function upload() {
            var validation = 0;

            $('#modal-upload-bukti .required').each(function() {
                var par = $(this).parents('.parent');
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).addClass('is-invalid');
                    $(par).find('.select2-container').addClass('is-invalid');
                    validation++
                }
            })

            if (validation != 0) {
                ToastNotification('warning', 'Semua data harus diisi');
                return false;
            }

            var formData = new FormData();

            var input = document.getElementById("bukti_transfer");
            if (input != null) {
                file = input.files[0];
                if (file == undefined || file == null) {
                    ToastNotification('warning', 'Bukti Transfer Harus Diisi');
                    return false;
                } else {
                    formData.append("bukti_transfer", file);
                }
            }

            var data = $('#form-data').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            var previousWindowKeyDown = window.onkeydown;

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('uploadBuktiTransferRekapInvoice') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                clear();
                                const el = document.querySelector("#modal-upload-bukti");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
                                table.ajax.reload(null, false);
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            } else {
                                Swal.fire({
                                    title: 'Ada Kesalahan !!!',
                                    text: data,
                                    icon: "warning",
                                    html: true,
                                });
                            }
                        },
                        error: function(data) {
                            var html = '';
                            Object.keys(data.responseJSON).forEach(element => {
                                html += data.responseJSON[element][0] + '<br>';
                            });
                            Swal.fire({
                                title: 'Ada Kesalahan !!!',
                                html: data.responseJSON.message == undefined ? html : data
                                    .responseJSON.message,
                                icon: "error",
                            });
                        }
                    });
                }
            })
        }

        function edit(id) {
            $.ajax({
                url: '{{ route('editCicilan') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        if(temp_key[i] == 'bukti_transfer'){
                            continue;
                        }
                        var key = temp_key[i];
                        $('#' + key).val(temp_value[key]);
                    }
                    // $('.parent').addClass('disabled');
                    $('.not-editable').prop('readonly', true);
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    $('#simpan').removeClass('hidden');
                    $('#sisa_pelunasan').val(accounting.formatNumber(data.data.sisa_pelunasan, {
                        precision: 0
                    }));
                    $('#metode_pembayaran').val(data.data.metode_pembayaran).trigger('change');
                    $('#nama_bank').val(data.data.nama_bank);
                    $('#nomor_kartu').val(data.data.nomor_kartu);
                    $('#nomor_transaksi').val(data.data.nomor_transaksi);
                    $('#pembayaran').val(accounting.formatNumber(data.data.pembayaran, {
                        precision: 0
                    }));
                    const el = document.querySelector("#modal-tambah-data");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                },
                error: function(data) {
                    var html = '';
                    Object.keys(data.responseJSON).forEach(element => {
                        html += data.responseJSON[element][0] + '<br>';
                    });
                    swal({
                        title: 'Ada Kesalahan !!!',
                        html: data.responseJSON.message == undefined ? html : data
                            .responseJSON.message,
                        icon: "error",
                    });
                }
            });
        }

        function openFilter() {
            slideOver.toggle();
        }

        function lihatPembayaran(id) {
            $.ajax({
                url: "{{ route('getHistorisPembayaran') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    $('#historis-pembayaran').html(data);
                    const el = document.querySelector("#modal-pembayaran");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                },
                error: function(data) {
                    generateKode();
                }
            });
        }

        function uploadBukti(id) {
            $.ajax({
                url: '{{ route('editRekapInvoice') }}',
                data: {
                    id,
                },
                type: 'get',
                success: function(data) {
                    $('#owner_id_bukti').val(data.data.owner_id).trigger('change');
                    $('#sisa_pelunasan_bukti').val(accounting.formatNumber(data.data.sisa_pelunasan, {
                        precision: 0,
                    }));

                    $('#metode_pembayaran_bukti').val(data.data.metode_pembayaran);
                    $('#metode_pembayaran_bukti').change();
                    $('#nama_bank_bukti').val(data.data.nama_bank);
                    $('#nomor_kartu_bukti').val(data.data.nomor_kartu);
                    $('#atas_nama_bukti').val(data.data.nama_owner);
                    $('#id_bukti').val(data.data.id);
                    const el = document.querySelector("#modal-upload-bukti");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                },
                error: function(data) {
                    var html = '';
                    Object.keys(data.responseJSON).forEach(element => {
                        html += data.responseJSON[element][0] + '<br>';
                    });
                    swal({
                        title: 'Ada Kesalahan !!!',
                        html: data.responseJSON.message == undefined ? html : data
                            .responseJSON.message,
                        icon: "error",
                    });
                }
            });  
        }

        function uploadBuktiTransfer(kasir_id, id) {
            $.ajax({
                url: '{{ route('editCicilanPembayaran') }}',
                data: {
                    kasir_id,
                    id,
                },
                type: 'get',
                success: function(data) {
                    $('#kode_bukti_transfer').val(data.data.kode);
                    $('#nama_owner_bukti_transfer').val(data.data.nama_owner);
                    $('#nilai_pembayaran_bukti_transfer').val(accounting.formatNumber(data.data.kasir_pembayaran[0].nilai_pembayaran, {
                        precision: 0,
                    }));

                    $('#metode_pembayaran_bukti_transfer').val(data.data.kasir_pembayaran[0].jenis_pembayaran);
                    $('#metode_pembayaran_bukti_transfer').change();
                    $('#nama_bank_bukti_transfer').val(data.data.kasir_pembayaran[0].nama_bank);
                    $('#nomor_kartu_bukti_transfer').val(data.data.kasir_pembayaran[0].nomor_kartu);
                    $('#atas_nama_bukti_transfer').val(data.data.kasir_pembayaran[0].nama_owner);
                    $('#kasir_id_bukti_transfer').val(data.data.kasir_pembayaran[0].kasir_id);
                    $('#id_bukti_transfer').val(data.data.kasir_pembayaran[0].id);
                    const el = document.querySelector("#modal-upload-bukti-transfer");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();

                    const eltwo = document.querySelector("#modal-pembayaran");
                    const modaltwo = tailwind.Modal.getOrCreateInstance(eltwo);
                    modaltwo.toggle();

                },
                error: function(data) {
                    var html = '';
                    Object.keys(data.responseJSON).forEach(element => {
                        html += data.responseJSON[element][0] + '<br>';
                    });
                    swal({
                        title: 'Ada Kesalahan !!!',
                        html: data.responseJSON.message == undefined ? html : data
                            .responseJSON.message,
                        icon: "error",
                    });
                }
            });     
        }

        function store() {
            var validation = 0;

            $('#form-tambah-data .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null) {
                        $(this).addClass('is-invalid');
                        $(this).addClass('error');
                        $(par).find('.select2-container').addClass('is-invalid');
                        validation++
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).removeClass('error');
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#form-tambah-data').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    overlay(true)
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('updateRekapInvoice') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                const el = document.querySelector("#modal-tambah-data");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Perhatian',
                                    text: data.message,
                                    icon: "warning",
                                });
                            } else if (data.status == 3) {
                                Swal.fire({
                                    title: 'Perhatian',
                                    text: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false)
                        },
                        error: function(data) {
                            overlay(false)
                            var html = '';
                            Object.keys(data.responseJSON).forEach(element => {
                                html += data.responseJSON[element][0] + '<br>';
                            });
                            Swal.fire({
                                title: 'Ada Kesalahan !!!',
                                html: data.responseJSON.message == undefined ? html : data
                                    .responseJSON.message,
                                icon: "error",
                            });
                        }
                    });
                }
            })
        }

        function prosesUploadBuktiTransfer() {
            var validation = 0;

            $('#modal-upload-bukti-transfer .required').each(function() {
                var par = $(this).parents('.parent');
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).addClass('is-invalid');
                    $(par).find('.select2-container').addClass('is-invalid');
                    validation++
                }
            })

            if (validation != 0) {
                ToastNotification('warning', 'Semua data harus diisi');
                return false;
            }

            var formData = new FormData();

            var input = document.getElementById("bukti_transfer_bukti");
            if (input != null) {
                file = input.files[0];
                if (file == undefined || file == null) {
                    ToastNotification('warning', 'Bukti Transfer Harus Diisi');
                    return false;
                } else {
                    formData.append("bukti_transfer_bukti", file);
                }
            }

            var data = $('#form-data-bukti-transfer').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            var previousWindowKeyDown = window.onkeydown;

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('uploadCicilanPembayaran') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                // clear();
                                const el = document.querySelector("#modal-upload-bukti-transfer");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            } else {
                                Swal.fire({
                                    title: 'Ada Kesalahan !!!',
                                    text: data,
                                    icon: "warning",
                                    html: true,
                                });
                            }
                        },
                        error: function(data) {
                            var html = '';
                            Object.keys(data.responseJSON).forEach(element => {
                                html += data.responseJSON[element][0] + '<br>';
                            });
                            Swal.fire({
                                title: 'Ada Kesalahan !!!',
                                html: data.responseJSON.message == undefined ? html : data
                                    .responseJSON.message,
                                icon: "error",
                            });
                        }
                    });
                }
            })
        }

        $(document).on('change', '#metode_pembayaran_bukti_transfer', function() {
            if ($(this).val() == 'TUNAI') {
                $('.non-tunai').addClass('hidden');
            } else {
                $('.non-tunai').removeClass('hidden');
            }
            $('.non-tunai').find('input').val('');
        })

        $(document).on('change', '#metode_pembayaran_bukti', function() {
            if ($(this).val() == 'TUNAI') {
                $('.non-tunai').addClass('hidden');
            } else {
                $('.non-tunai').removeClass('hidden');
            }
            $('.non-tunai').find('input').val('');
        })

        function hapus(id, param) {
            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Hapus Data",
                text: "Data yang telah dihapus tidak bisa dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('deleteCicilan') }}',
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}"
                        },
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            table.ajax.reload(null, false);
                        },
                        error: function(data) {
                            var html = '';
                            Object.keys(data.responseJSON).forEach(element => {
                                html += data.responseJSON[element][0] + '<br>';
                            });
                            Swal.fire({
                                title: 'Ada Kesalahan !!!',
                                html: data.responseJSON.message == undefined ? html : data
                                    .responseJSON.message,
                                icon: "error",
                            });
                        }
                    });
                }
            })
        }

        function gantiStatus(param, id) {
            $.ajax({
                url: "{{ route('statusCicilan') }}",
                data: {
                    id,
                    param
                },
                type: 'get',
                success: function(data) {
                    table.ajax.reload(null, false);
                    ToastNotification('success', data.message);

                },
                error: function(data) {
                    var html = '';
                    Object.keys(data.responseJSON).forEach(element => {
                        html += data.responseJSON[element][0] + '<br>';
                    });
                    Swal.fire({
                        title: 'Ada Kesalahan !!!',
                        html: data.responseJSON.message == undefined ? html : data
                            .responseJSON.message,
                        icon: "error",
                    });
                }
            });
        }

        $('#nilai_pembayaran').keyup(function() {
            var sisa_pelunasan = $('#sisa_pelunasan').val().replace(/[^0-9\-]+/g, "") * 1;
            var nilai_pembayaran = $(this).val().replace(/[^0-9\-]+/g, "") * 1;
            if (sisa_pelunasan < nilai_pembayaran) {
                $('#nilai_pembayaran').val(accounting.formatNumber(sisa_pelunasan, {
                    precision: 0
                }));
            }
        })

        $(document).on('change', '#metode_pembayaran', function() {
            if ($(this).val() == 'TUNAI') {
                $('.non-tunai').addClass('hidden');
            } else {
                $('.non-tunai').removeClass('hidden');
            }
            $('.non-tunai').find('input').val('');
        })

        function printInvoice(id) {
            window.open('{{ route('printPembayaran') }}?id=' + id);
        }

        function excel(params) {
            window.open('{{ route('rekapInvoiceExcel') }}?tanggal_awal=' + $('#tanggal_awal').val() + '&tanggal_akhir=' +
                $('#tanggal_akhir').val() + '&branch_id_filter=' + $('#branch_id_filter').val() +
                '&sisa_pelunasan_filter=' + $('#sisa_pelunasan_filter').val() +
                '&owner_id_filter=' + $('#owner_id_filter').val());
        }

        function printBuktiPembayaran(kasir_id, id) {
            window.open('{{ route('printBuktiPembayaranCicilan') }}?kasir_id=' + kasir_id + '&id=' + id);
        }
    </script>
@endsection
