@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('../management_pasien/rekap_pasien_meninggal/filter_pasien_meninggal')
@endsection
@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
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
                            <li>
                                <a href="javascript:;" class="dropdown-item" onclick="exportExcel()">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
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
                    <th>Nama Pasien</th>
                    <th>Branch</th>
                    <th>Jenis Hewan</th>
                    <th>Owner</th>
                    <th>Dokter Poli</th>
                    <th>Kamar Terakhir</th>
                    <th>Tanggal Daftar Pasien</th>
                    <th>Tanggal Meninggal Pasien</th>
                    <th>Meninggal Saat</th>
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
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 parent">
                        <label for="name" class="form-label">Kode Invoice {{ dot() }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control kode" readonly name="kode" id="kode"
                                value="">
                        </div>
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent">
                        <label for="name" class="form-label">Nama Customer {{ dot() }}</label>
                        <input type="text" id="nama_owner" class="form-control" name="nama_owner" readonly>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nilai_deposit" class="form-label">Sisa Hutang {{ dot() }}</label>
                        <input id="sisa_pelunasan" name="sisa_pelunasan" readonly type="text"
                            class="form-control text-right required" placeholder="Masukan sisa pelunasan">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nilai_pembayaran" class="form-label">Nilai Pembayaran {{ dot() }}</label>
                        <input id="nilai_pembayaran" name="nilai_pembayaran" type="text"
                            class="form-control text-right mask required" placeholder="Masukan nilai pembayaran">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="jenis_pembayaran" class="form-label">Metode Pembayaran {{ dot() }}</label>
                        <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-control select2">
                            <option value="TUNAI">TUNAI</option>
                            <option value="NON TUNAI">NON TUNAI</option>
                        </select>
                    </div>
                    <div class="col-span-4 parent non-tunai hidden">
                        <label for="nama_bank" class="form-label">Nama Bank {{ dot() }}</label>
                        <input id="nama_bank" name="nama_bank" type="text"
                            class="form-control text-right uppercase required" placeholder="Masukan nilai pembayaran">
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
                    <div class="col-span-12 parent">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" type="text" class="form-control" placeholder="Masukan Keterangan"></textarea>
                    </div>
                </div>
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
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
    <script>
        var table;
        (function() {
            table = $('#table').DataTable({
                    // searching: false,
                    processing: true,
                    serverSide: true,
                    selected: null,
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
                        url: "{{ route('datatableRekapPasienMeninggal') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            tanggal_awal() {
                                return $('#tanggal_awal').val();
                            },
                            tanggal_akhir() {
                                return $('#tanggal_akhir').val();
                            },
                            jenis() {
                                return $('#jenis').val();
                            },
                            value() {
                                return $('#value').val();
                            },
                            binatang_id() {
                                return $('#binatang_id_filter').val();
                            },
                            owner_id() {
                                return $('#owner_id_filter').val();
                            },
                            dokter_poli() {
                                return $('#dokter_poli').val();
                            },
                            kamar_rawat_inap_dan_bedah_id() {
                                return $('#kamar_rawat_inap_dan_bedah_id').val();
                            },
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center'
                    }, {
                        data: 'pasien.name',
                        name: 'pasien.name'
                    }, {
                        data: 'branch',
                        name: 'branch'
                    }, {
                        data: 'binatang',
                        name: 'binatang'
                    }, {
                        data: 'owner',
                        name: 'owner'
                    }, {
                        data: 'dokter_poli',
                        name: 'dokter_poli'
                    }, {
                        data: 'kamar_rawat_inap_dan_bedah',
                        name: 'kamar_rawat_inap_dan_bedah'
                    }, {
                        data: 'created_at',
                        name: 'created_at'
                    }, {
                        data: 'pasien_meninggal.created_at',
                        name: 'pasien_meninggal.created_at',
                        class: 'text-left',
                    }, {
                        data: 'pasien_meninggal.meninggal_saat',
                        name: 'pasien_meninggal.meninggal_saat',
                        class: 'text-left',
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

            $('.select2').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
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

        // function filter(params) {
        //     table.ajax.reload();
        // }

        // function openFilter() {
        //     slideOver.toggle();
        // }

        function openFilter() {
            slideOver.toggle();
        }

        function filter(params) {
            slideOver.toggle();
            table.ajax.reload();
        }

        function exportExcel() {
            var data = $('#data-filter').serialize();
            window.open('{{ route('rekapPasienMeninggalExcel') }}?' + data);
        }
    </script>
@endsection
