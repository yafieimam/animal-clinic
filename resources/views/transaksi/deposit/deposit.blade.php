@extends('../layout/' . $layout)
@section('content_filter')
    @include('../transaksi/deposit/filter_deposit')
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="col-span-12 sm:col-span-6 md:col-span-12 intro-y">
            <div class="grid grid-cols-12 gap-6 mt-5">
                <div class="col-span-12 sm:col-span-6 md:col-span-12 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i class="fa-solid fa fa-credit-card-alt report-box__icon text-success"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6" id="total-uang-masuk">
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
                                {{-- IDR {{ number_format($totaluangmasuk) }} --}}

                            </div>
                            <div class="text-base text-slate-500 mt-1" style="font-size:25px;">Total Uang Masuk</div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-span-12 sm:col-span-6 md:col-span-6 intro-y">
                    <div class="report-box zoom-in">
                        <div class="box p-5">
                            <div class="flex">
                                <i class="fa-solid fa-money report-box__icon text-warning"></i>
                            </div>
                            <div class="text-3xl font-medium leading-8 mt-6" id="total-uang-keluar">
                                <?php
                                // $totalhutang = \App\Models\Kasir::where('type_kasir', '=', 'Normal')
                                //     ->where('created_at', '>=', date('Y-m-d'))
                                //     ->sum('sisa_pelunasan');
                                ?>
                                {{-- IDR {{ number_format($totaluangkeluar) }} --}}
                                Rp 0.00
                            </div>
                            <div class="text-base text-slate-500 mt-1" style="font-size:25px;">Total Uang Keluar</div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
            <div class="dropdown inline mr-2">
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
                <button class="btn btn-primary shadow-md mr-2" id="tambah-data"
                    onclick="refreshState('#modal-tambah-data')">Tambah Data</button>
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
                    <th>Opsi</th>
                    <th style="text-align:center">No Deposit</th>
                    <th style="text-align:center">Nama Owner</th>
                    <th style="text-align:center">No Registrasi</th>
                    <th style="text-align:center">Branch</th>
                    <th style="text-align:center">Deposit</th>
                    <th style="text-align:center">Nilai Deposit</th>
                    <th style="text-align:center">Sisa Deposit</th>
                    <th style="text-align:center">Keterangan</th>
                    <th>Deposit Oleh</th>
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
                    <h2 class="font-medium text-base mr-auto">Deposit</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 parent">
                        <label for="name" class="form-label">Kode Deposit {{ dot() }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control kode" readonly name="kode" id="kode"
                                value="">
                            <div class="input-group-text">
                                <button type="button" onclick="generateKode()"><i class="fas fa-refresh"></i></button>
                            </div>
                        </div>
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent">
                        <label for="name" class="form-label">Nama Owner {{ dot() }}</label>
                        <select name="owner_id" id="owner_id" class="select2 form-control required">
                            <option value="">Pilih Nama Owner</option>
                            @foreach (\App\Models\Owner::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nilai_deposit" class="form-label">Nilai Deposit {{ dot() }}</label>
                        <input id="nilai_deposit" name="nilai_deposit" type="text"
                            class="form-control text-right required mask" placeholder="Masukan nilai deposit">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran
                            {{ dot() }}</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control select2">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nama_bank" class="form-label">Nama Bank {{ dot() }}</label>
                        <input id="nama_bank" name="nama_bank" type="text"
                            class="form-control text-left uppercase required" placeholder="Masukan nama bank">
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nomor_kartu" class="form-label">No. Rekening {{ dot() }}</label>
                        <input type="text" name="nomor_kartu" id="nomor_kartu" placeholder="xxxxxxxxxx"
                            class="form-control required">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" type="text" class="form-control" placeholder="Masukan Keterangan"></textarea>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary w-20" onclick="store()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>

    <div id="modal-histori-pemakaian" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Histori Deposit</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <table class="table mt-2 stripe hover table-bordered" id="tableHistoriPemakaian"
                            style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                            <thead align="center">
                                <th>Print</th>
                                <th>Ref</th>
                                <th>Tanggal</th>
                                <th>Nilai Deposit</th>
                                <th>Metode Deposit</th>
                                <th>Status</th>
                                <th>Nama Bank</th>
                                <th>Jenis</th>
                                <th>No. Rekening</th>
                                <th>Dokumen</th>
                                <th>Keterangan</th>
                                <th>Dibuat Oleh</th>
                            </thead>

                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>

    <div id="modal-kurang-data" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Data</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 parent">
                        <label for="owner_id_kurang" class="form-label">Owner {{ dot() }}</label>
                        <select name="owner_id" id="owner_id_kurang" class="select2 form-control required">
                            <option value="">Pilih Owner</option>
                            @foreach (\App\Models\Owner::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="id_kurang" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 md:col-span-6 parent disabled">
                        <label for="sisa_deposit_kurang" class="form-label">Sisa Deposit {{ dot() }}</label>
                        <input id="sisa_deposit_kurang" name="sisa_deposit" readonly type="text"
                            class="form-control text-right required mask" placeholder="Masukan Sisa deposit">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="pengurangan" class="form-label">Pengurangan Deposit
                            {{ dot() }}</label>
                        <input id="pengurangan" name="pengurangan" type="text"
                            class="form-control text-right required mask" placeholder="Masukan nilai pengurangan deposit">
                    </div>

                    <div class="col-span-12 parent">
                        <label for="keterangan_kurang" class="form-label">Keterangan</label>
                        <textarea id="keterangan_kurang" name="keterangan" type="text" class="form-control"
                            placeholder="Masukan Keterangan"></textarea>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="pengurangan()">Pengurangan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>

    <div id="modal-tarik-data" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Penarikan Deposit</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 parent disabled">
                        <label for="owner_id_tarik" class="form-label">Owner {{ dot() }}</label>
                        <select name="owner_id" id="owner_id_tarik" class="select2 form-control required">
                            <option value="">Pilih Owner</option>
                            @foreach (\App\Models\Owner::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="id_tarik" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 md:col-span-6 parent disabled">
                        <label for="sisa_deposit_tarik" class="form-label">Sisa Deposit {{ dot() }}</label>
                        <input id="sisa_deposit_tarik" name="sisa_deposit" readonly type="text"
                            class="form-control text-right required mask" placeholder="Masukan Sisa deposit">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="tarik" class="form-label">Jumlah Penarikan
                            {{ dot() }}</label>
                        <input id="tarik" name="tarik" type="text"
                            class="form-control text-right required mask" placeholder="Masukan nilai yang akan ditarik">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="metode_pembayaran_tarik" class="form-label">Metode Pembayaran
                            {{ dot() }}</label>
                        <select name="metode_pembayaran" id="metode_pembayaran_tarik" class="form-control">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nama_bank_tarik" class="form-label">Nama Bank {{ dot() }}</label>
                        <input id="nama_bank_tarik" name="nama_bank" type="text"
                            class="form-control text-left uppercase required" placeholder="Masukan nama bank">
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nomor_kartu_tarik" class="form-label">No. Rekening {{ dot() }}</label>
                        <input type="text" name="nomor_kartu" id="nomor_kartu_tarik" placeholder="xxxxxxxxxx"
                            class="form-control required">
                    </div>
                    <div class="col-span-12 parent non-tunai hidden">
                        <label for="atas_nama_tarik" class="form-label">Atas Nama {{ dot() }}</label>
                        <input type="text" name="atas_nama" id="atas_nama_tarik"
                            placeholder="Masukan Nama di Rekening" class="form-control required">
                    </div>
                    <div class="col-span-12 parent hidden">
                        <label for="keterangan_tarik" val=''"form-label">Keterangan</label>
                        <textarea id="keterangan_tarik" val=''"keterangan" type="text" class="form-control"
                            placeholder="Masukan Keterangan"></textarea>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="keterangan_tarik2" class="form-label">Keterangan</label>
                        <textarea id="keterangan_tarik2" name="keterangan2" type="text" class="form-control"
                            placeholder="Masukan Keterangan"></textarea>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="tarikDeposit()">Penarikan Deposit</button>
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
        var tableHistoriPemakaian;
        (function() {
            table = $('#table').DataTable({
                    // searching: false,
                    processing: true,
                    serverSide: false,
                    "sDom": "ltipr",
                    buttons: [
                        $.extend(true, {}, {
                            extend: 'pageLength',
                            className: 'btn btn-primary'
                        }),
                    ],
                    lengthMenu: [
                        [10, 50, 100, -1],
                        ['10 rows', '50 rows', '100 rows', 'Show all']
                    ],
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
                    ajax: {
                        url: "{{ route('datatableDeposit') }}",
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
                        }
                    },
                    drawCallback: function() {
                        var api = this.api();
                        var total_uang_masuk = 0;
                        var total_uang_keluar = 0;
                        var data = api.rows({ search: 'applied' }).data();
                        data.each(function(rowData) {
                            total_uang_masuk += parseInt(rowData.total_uang_masuk);
                            total_uang_keluar += parseInt(rowData.total_uang_keluar);
                        });
                        // if (data.length > 0) {
                        //     total_uang_masuk = data[0].total_uang_masuk;
                        //     total_uang_keluar = data[0].total_uang_keluar;
                        // }
                        $('#total-uang-masuk').html(total_uang_masuk.toLocaleString('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }));
                        $('#total-uang-keluar').html(total_uang_keluar.toLocaleString('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }));
                    },
                    columns: [{
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, {
                        data: 'kode',
                        name: 'kode',
                        class: 'text-left',
                    }, {
                        data: 'owner',
                        name: 'owner',
                        class: 'text-left',
                    }, {
                        data: 'kode_registrasi',
                        name: 'kode_registrasi',
                        class: 'text-left',
                    }, {
                        data: 'branch',
                        name: 'branch',
                        class: 'text-left',
                    }, {
                        data: 'nilai',
                        name: 'nilai',
                        class: 'text-right',
                    }, {
                        data: 'nilai_deposit',
                        name: 'nilai_deposit',
                        class: 'text-right',
                    }, {
                        data: 'sisa_deposit',
                        name: 'sisa_deposit',
                        class: 'text-right',
                    }, {
                        data: 'keterangan',
                        name: 'keterangan',
                        class: 'text-left',
                    }, {
                        data: 'created_by',
                        name: 'created_by',
                        class: 'text-center',
                    }, ]
                })
                .columns.adjust()
                .responsive.recalc();

            tableHistoriPemakaian = $('#tableHistoriPemakaian').DataTable({
                    // searching: false,
                    processing: true,
                    serverSide: true,
                    "sDom": "ltipr",
                    buttons: [
                        $.extend(true, {}, {
                            extend: 'pageLength',
                            className: 'btn btn-primary'
                        }),
                    ],
                    lengthMenu: [
                        [10, 50, 100, -1],
                        ['10 rows', '50 rows', '100 rows', 'Show all']
                    ],
                    order: [
                        [ 0, "desc" ]
                    ],
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
                    ajax: {
                        url: "{{ route('datatableHistoriPemakaianDeposit') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id() {
                                return $('#id').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, {
                        data: 'ref',
                        name: 'ref',
                        class: 'text-center',
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        class: 'text-left',
                    }, {
                        data: 'nilai',
                        name: 'nilai',
                        class: 'text-right',
                    }, {
                        data: 'metode_pembayaran',
                        name: 'metode_pembayaran',
                        class: 'text-left',
                    }, {
                        data: 'status_deposit',
                        name: 'status_deposit',
                        class: 'text-center',
                    }, {
                        data: 'nama_bank',
                        name: 'nama_bank',
                        class: 'text-left',
                    }, {
                        data: 'jenis_deposit',
                        name: 'jenis_deposit',
                        class: 'text-left',
                    }, {
                        data: 'nomor_kartu',
                        name: 'nomor_kartu',
                        class: 'text-left',
                    }, {
                        data: 'bukti_transfer',
                        name: 'bukti_transfer',
                        class: 'text-center',
                    }, {
                        data: 'keterangan2',
                        name: 'keterangan2',
                        class: 'text-left',
                    }, {
                        data: 'created_by',
                        name: 'created_by',
                        class: 'text-center',
                    }, ]
                })
                .columns.adjust()
                .responsive.recalc();

            $('#myInputTextField').keyup(debounce(function() {
                table.search($(this).val()).draw();
            }, 500));

            $('.mask').maskMoney({
                precision: 0,
                thousands: ','
            })

            $('#metode_pembayaran').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                // theme: 'bootstrap4',
            })

            $('.select2').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
            })

            $('#metode_pembayaran_tarik').select2({
                dropdownParent: $("#modal-tarik-data .modal-body"),
                // theme: 'bootstrap4',
            })

             $('.select2filter').select2({
            dropdownParent: $("#slide-over-filter .modal-body"),
            width: '100%',
        })

            
            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            $('#tambah-data').click(function() {
                // clear();
                $('.not-editable').removeClass('disabled');
                $('.not-editable').prop('readonly', false);
                $('#metode_pembayaran').val('TUNAI').trigger('change.select2');
                $('#metode_pembayaran').change();

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
                    url: "{{ route('generateKodeDeposit') }}",
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

        function openFilter() {
            slideOver.toggle();
        }

        $('#pengurangan').keyup(function() {
            var pengurangan = $(this).val().replace(/[^0-9\-]+/g, "") * 1;
            var limit = $('#sisa_deposit_kurang').val().replace(/[^0-9\-]+/g, "") * 1;
            if (pengurangan > limit) {
                $(this).val(accounting.formatNumber(limit, {
                    precision: 0,
                }));
            }
        })

        $('#tarik').keyup(function() {
            var tarik = $(this).val().replace(/[^0-9\-]+/g, "") * 1;
            var limit = $('#sisa_deposit_tarik').val().replace(/[^0-9\-]+/g, "") * 1;
            if (tarik > limit) {
                $(this).val(accounting.formatNumber(limit, {
                    precision: 0,
                }));
            }
        })


        function edit(id) {
            $.ajax({
                url: '{{ route('editDeposit') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        $('#' + key + '_kurang').val(temp_value[key]);
                    }
                    $('.not-editable').prop('readonly', true);
                    $('#modal-kurang-data .select2').trigger('change.select2');
                    $('.mask').maskMoney('mask')

                    const el = document.querySelector("#modal-kurang-data");
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

        function tarik(id) {
            $.ajax({
                url: '{{ route('editDeposit') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        $('#' + key + '_tarik').val(temp_value[key]);
                    }
                    $('.not-editable').prop('readonly', true);
                    $('#modal-tarik-data .select2').trigger('change.select2');
                    $('.mask').maskMoney('mask')
                    $('#metode_pembayaran_tarik').change();
                    $('#keterangan_tarik').val('');
                    $('#keterangan_tarik2').val('');

                    const el = document.querySelector("#modal-tarik-data");
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

        function lihat(id) {
            $.ajax({
                url: '{{ route('editDeposit') }}',
                data: {
                    id,
                    param: 'lihat',
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        $('#' + key).val(temp_value[key]);
                    }
                    $('.c_form_group').addClass('disabled');
                    $('.c_form_group').find('input').prop('readonly', true);
                    $('#modal-tambah-data').find('.select2').trigger('change.select2');
                    $('#modal-tambah-data').modal('toggle');
                    $('#simpan').addClass('hidden');
                },
                error: function(data) {
                    lihat(id);
                }
            });
        }

        function store() {
            var validation = 0;

            $('#modal-tambah-data .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null || $(this).val() == 0) {
                        $(this).addClass('is-invalid');
                        $(par).find('.select2-container').addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', 'Semua data harus diisi');
                return false;
            }
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
                    overlay(true)
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('storeDeposit') }}',
                        data: $('#modal-tambah-data :input').serialize(),
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                // clear();
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
                            generateKode();
                            table.ajax.reload(null, false);
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

        function tarikDeposit() {
            var validation = 0;

            $('#modal-tarik-data .required').each(function() {
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
                    overlay(true)
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('tarikDeposit') }}',
                        data: $('#modal-tarik-data :input').serialize(),
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                // clear();
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
                            generateKode();
                            table.ajax.reload(null, false);
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

        function pengurangan() {
            var validation = 0;

            $('#modal-kurang-data .required').each(function() {
                var par = $(this).parents('.parent');
                if ($(this).val() == '' || $(this).val() == null || $(this).val() == 0) {
                    $(this).addClass('is-invalid');
                    $(par).find('.select2-container').addClass('is-invalid');
                    validation++
                }
            })

            if (validation != 0) {
                ToastNotification('warning', 'Semua data harus diisi');
                return false;
            }
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
                        url: '{{ route('updateDeposit') }}',
                        data: $('#modal-kurang-data :input').serialize(),
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                // clear();
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
                            generateKode();
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

        $(document).on('change', '#metode_pembayaran', function() {
            if ($(this).val() == 'TUNAI') {
                $('.non-tunai').addClass('hidden');
            } else {
                $('.non-tunai').removeClass('hidden');
            }
            $('.non-tunai').find('input').val('');
        })

        $(document).on('change', '#metode_pembayaran_tarik', function() {
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
                        url: '{{ route('deleteDeposit') }}',
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
                                // clear();
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
                url: "{{ route('statusDeposit') }}",
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

        function printDeposit(id) {
            window.open('{{ route('printDeposit') }}?id=' + id);
        }

        function printHistoryDeposit(deposit_id, id) {
            window.open('{{ route('printHistoryDeposit') }}?id=' + id + '&deposit_id=' + deposit_id);
        }

        function historiPemakaian(id) {
            $('#id').val(id);

            console.log($('#id').val());
            const el = document.querySelector("#modal-histori-pemakaian");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();

            tableHistoriPemakaian.ajax.reload();
        }
    </script>
@endsection
