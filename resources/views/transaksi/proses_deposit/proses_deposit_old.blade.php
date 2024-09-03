@extends('../layout/' . $layout)
@section('content_filter')
    @include('../transaksi/proses_deposit/filter_proses_deposit')
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">Refund</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            {{-- <div class="flex flex-wrap items-center">
                <button class="btn btn-primary shadow-md mr-2" id="tambah-data"
                    onclick="refreshState('#modal-tambah-data')">Tambah Data</button>
                <div class="dropdown inline">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center">
                            <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40 ">
                        <ul class="dropdown-content">
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div> --}}

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
            </div>

            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" id="myInputTextField" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        {{-- <div class="col-span-12 ">
            <h5><b>Filter</b></h5>
        </div>
       
        <div class="col-span-12 md:col-span-4">
            <label class="form-label block">&nbsp;</label>
            <button class="btn btn-primary shadow-md mr-2" onclick="filter()"><i
                    class="fas fa-search"></i>&nbsp;Search</button>
        </div> --}}
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <table class="table mt-2 stripe hover" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Opsi</th>
                    <th>Owner</th>
                    <th>No Registrasi</th>
                    <th>Nilai Deposit</th>
                    <th>Metode Pembayaran</th>
                    <th>Bank</th>
                    <th>No. Rekening</th>
                    <th>Atas Nama</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Bukti Transfer</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Dibuat Oleh</th>
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
                <form class="modal-body grid grid-cols-12 gap-4 gap-y-3 " id="form-data">
                    <div class="col-span-12 parent disabled">
                        <label for="owner_id" class="form-label">Owner</label>
                        <select name="owner_id" id="owner_id" class="select2 form-control required">
                            <option value="">Pilih Owner</option>
                            @foreach (\App\Models\Owner::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="deposit_id" name="deposit_id">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nominal_transfer" class="form-label">Nominal Transfer
                        </label>
                        <input id="nominal_transfer" readonly name="nominal_transfer" type="text"
                            class="form-control text-left required">
                    </div>
                    <div class="col-span-12 parent disabled">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nama_bank" class="form-label">Nama Bank</label>
                        <input id="nama_bank" name="nama_bank" type="text"
                            class="form-control text-left uppercase required" readonly placeholder="Masukan nama bank">
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nomor_kartu" class="form-label">No. Rekening</label>
                        <input type="text" name="nomor_kartu" id="nomor_kartu" readonly placeholder="xxxxxxxxxx"
                            class="form-control required">
                    </div>
                    <div class="col-span-12 parent non-tunai hidden">
                        <label for="atas_nama" class="form-label">Atas Nama</label>
                        <input type="text" name="atas_nama" id="atas_nama" readonly
                            placeholder="Masukan Nama di Rekening" class="form-control required">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="bukti_transfer" class="form-label">Bukti Transfer {{ dot() }}</label>
                        <input type="file" class="dropify" id="bukti_transfer" data-max-file-size="5M">
                    </div>
                </form>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="store()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
    <div id="modal-tambah-data" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Data 3</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <form class="modal-body grid grid-cols-12 gap-4 gap-y-3 " id="form-data">
                    <div class="col-span-12 parent disabled">
                        <label for="owner_id" class="form-label">Owner</label>
                        <select name="owner_id" id="owner_id" class="select2 form-control required">
                            <option value="">Pilih Owner</option>
                            @foreach (\App\Models\Owner::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="deposit_id" name="deposit_id">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nominal_transfer" class="form-label">Nominal Transfer
                        </label>
                        <input id="nominal_transfer" readonly name="nominal_transfer" type="text"
                            class="form-control text-left required">
                    </div>
                    <div class="col-span-12 parent disabled">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nama_bank" class="form-label">Nama Bank</label>
                        <input id="nama_bank" name="nama_bank" type="text"
                            class="form-control text-left uppercase required" readonly placeholder="Masukan nama bank">
                    </div>
                    <div class="col-span-6 parent non-tunai hidden">
                        <label for="nomor_kartu" class="form-label">No. Rekening</label>
                        <input type="text" name="nomor_kartu" id="nomor_kartu" readonly placeholder="xxxxxxxxxx"
                            class="form-control required">
                    </div>
                    <div class="col-span-12 parent non-tunai hidden">
                        <label for="atas_nama" class="form-label">Atas Nama</label>
                        <input type="text" name="atas_nama" id="atas_nama" readonly
                            placeholder="Masukan Nama di Rekening" class="form-control required">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="bukti_transfer" class="form-label">Bukti Transfer {{ dot() }}</label>
                        <input type="file" class="dropify" id="bukti_transfer" data-max-file-size="5M">
                    </div>
                </form>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="store()">Simpan</button>
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
                        url: "{{ route('datatableProsesDeposit') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            tanggal_awal() {
                                return $('#tanggal_awal').val();
                            },
                            tanggal_akhir() {
                                return $('#tanggal_akhir').val();
                            },
                            owner_id() {
                                return $('#owner_id_filter').val();
                            },
                            status() {
                                return $('#status_filter').val();
                            },
                            branch_id() {
                                return $('#branch_id_filter').val();
                            }
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center'
                    }, {
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, {
                        data: 'owner',
                        name: 'owner',
                        class: 'text-left',
                    }, {
                        data: 'kode',
                        name: 'kode',
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
                        data: 'nama_bank',
                        name: 'nama_bank',
                        class: 'text-left',
                    }, {
                        data: 'nomor_kartu',
                        name: 'nomor_kartu',
                        class: 'text-left',
                    }, {
                        data: 'atas_nama',
                        name: 'atas_nama',
                        class: 'text-left',
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        class: 'text-center',
                    }, {
                        data: 'bukti_transfer',
                        name: 'bukti_transfer',
                        class: 'text-center',
                    }, {
                        data: 'status',
                        name: 'status',
                        class: 'text-center',
                    }, {
                        data: 'keterangan2',
                        name: 'keterangan2',
                        class: 'text-center',
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
                clear();
                $('.not-editable').removeClass('disabled');
                $('.not-editable').prop('readonly', false);

                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();
                generateKode();
            })

            tomGenerator('.tomSelect');
            $('.dropify').dropify();
        })()

        function generateKode() {
            if ($('#id').val() == '') {
                $.ajax({
                    url: "{{ route('generateKodeProsesDeposit') }}",
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

        function openFilter() {
            slideOver.toggle();
        }

        function filter() {
            slideOver.toggle();
            table.ajax.reload();
        }

        $('#pengurangan').keyup(function() {
            var pengurangan = $(this).val().replace(/[^0-9\-]+/g, "") * 1;
            var limit = $('#sisa_deposit').val().replace(/[^0-9\-]+/g, "") * 1;
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


        function edit(deposit_id, id) {
            $.ajax({
                url: '{{ route('editProsesDeposit') }}',
                data: {
                    deposit_id,
                    id,
                },
                type: 'get',
                success: function(data) {
                    $('#owner_id').val(data.data.deposit.owner_id);
                    $('#nominal_transfer').val(accounting.formatNumber(data.data.nilai, {
                        precision: 0,
                    }));

                    $('#metode_pembayaran').val(data.data.metode_pembayaran);
                    $('#metode_pembayaran').change();
                    $('#nama_bank').val(data.data.nama_bank);
                    $('#nomor_kartu').val(data.data.nomor_kartu);
                    $('#atas_nama').val(data.data.atas_nama);
                    $('#deposit_id').val(data.data.deposit_id);
                    $('#id').val(data.data.id);
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

        function store() {
            var validation = 0;

            $('#modal-tambah-data .required').each(function() {
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
                        url: '{{ route('storeProsesDeposit') }}',
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
                                $('.dropify-clear').click();
                                const el = document.querySelector("#modal-tambah-data");
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

        $(document).on('change', '#metode_pembayaran_cancel', function() {
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
                        url: '{{ route('deleteProsesDeposit') }}',
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
                url: "{{ route('statusProsesDeposit') }}",
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
            window.open('{{ route('printProsesDeposit') }}?id=' + id);
        }

        function historiPemakaian(id) {
            $('#id').val(id);

            console.log($('#id').val());
            const el = document.querySelector("#modal-histori-pemakaian");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();

            tableHistoriPemakaian.ajax.reload();
        }

        function cancel(deposit_id, id) {
            $.ajax({
                url: '{{ route('editProsesDeposit') }}',
                data: {
                    deposit_id,
                    id,
                },
                type: 'get',
                success: function(data) {
                    $('#owner_id_cancel').val(data.data.deposit.owner_id);
                    $('#nominal_transfer_cancel').val(accounting.formatNumber(data.data.nilai, {
                        precision: 0,
                    }));

                    $('#metode_pembayaran_cancel').val(data.data.metode_pembayaran);
                    $('#metode_pembayaran_cancel').change();
                    $('#nama_bank_cancel').val(data.data.nama_bank);
                    $('#nomor_kartu_cancel').val(data.data.nomor_kartu);
                    $('#atas_nama_cancel').val(data.data.atas_nama);
                    $('#deposit_id_cancel').val(data.data.deposit_id);
                    $('#id_cancel').val(data.data.id);
                    const el = document.querySelector("#modal-cancel-data");
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

        function prosesCancel(deposit_id, id) {
            var validation = 0;

            $('#modal-cancel-data .required').each(function() {
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

            var data = $('#form-cancel-data').serializeArray();

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            var previousWindowKeyDown = window.onkeydown;

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Klik Tombol Ya jika ingin membatalkan.",
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
                        url: '{{ route('cancelProsesDeposit') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                clear();
                                const el = document.querySelector("#modal-cancel-data");
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
    </script>
@endsection
