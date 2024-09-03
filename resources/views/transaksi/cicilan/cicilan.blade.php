@extends('../layout/' . $layout)
@section('content_filter')
    @include('../transaksi/cicilan/filter_cicilan')
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">

        {{-- <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
            </div>

           
        </div> --}}
        {{-- <div class="col-span-12 ">
            <h5><b>Filter</b></h5>
        </div>
        <div class="col-span-12 md:col-span-4 ">
            <label for="owner_id_filter" class="form-label">Filter Owner</label>
            <select name="owner_id_filter" id="owner_id_filter" class="select2filter form-control">
                <option value="">Semua Owner</option>
                @foreach (\App\Models\Owner::get() as $item)
                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-12 md:col-span-4 ">
            <label for="branch_id_filter" class="form-label">Filter Cabang</label>
            <select name="branch_id_filter" id="branch_id_filter" class="select2filter form-control">
                @if (Auth::user()->akses('global'))
                    <option value="">Semua Branch</option>
                @endif
                @foreach (cabangFixed() as $item)
                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->lokasi }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-12 md:col-span-4">
            <label class="form-label block">&nbsp;</label>
            <button class="btn btn-primary shadow-md mr-2" onclick="filter()"><i
                    class="fas fa-search"></i>&nbsp;Search</button>
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

            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" id="myInputTextField" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white" id="isi">
            <table class="table mt-2 stripe hover" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Opsi</th>
                    <th>No Invoice</th>
                    <th>Branch</th>
                    <th>Tanggal Invoice</th>
                    <th>Tanggal Update</th>
                    <th>Nama Owner</th>
                    <th>No. Registrasi</th>
                    <th>No Telp</th>
                    <th>Tagihan</th>
                    <th>Kurang Bayar</th>
                    <th><center>Catatan</center></th>
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
                    <h2 class="font-medium text-base mr-auto">Form Cicilan</h2>
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
                        <label for="name" class="form-label">Nama Owner {{ dot() }}</label>
                        <input type="text" id="nama_owner" class="form-control" name="nama_owner" readonly>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nilai_deposit" class="form-label">Sisa Tagihan {{ dot() }}</label>
                        <input id="sisa_pelunasan" name="sisa_pelunasan" readonly type="text"
                            class="form-control text-right required" placeholder="Masukan sisa pelunasan">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="kategori_pembayaran" class="form-label">Kategori Pembayaran {{ dot() }}</label>
                        <select name="kategori_pembayaran" id="kategori_pembayaran" class="form-control select2">
                            <option value="Cicilan">CICILAN</option>
                            <option value="Diskon Cicilan">DISKON CICILAN</option>
                            <option value="Langsung Lunas">PELUNASAN</option>
                            <option value="Hibah">HIBAH</option>
                        </select>
                    </div>
                    <div class="col-span-12 parent diskon-cicilan hidden">
                        <label for="diskon_cicilan" class="form-label">Diskon Cicilan {{ dot() }}</label>
                        <input id="diskon_cicilan" name="diskon_cicilan" type="text"
                            class="form-control text-right mask required" placeholder="Masukan diskon cicilan">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="nilai_pembayaran" class="form-label">Nilai Pembayaran {{ dot() }}</label>
                        <input id="nilai_pembayaran" name="nilai_pembayaran" type="text"
                            class="form-control text-right mask required" placeholder="Masukan nilai pembayaran">
                    </div>
                    <div class="col-span-12 parent non-hibah">
                        <label for="jenis_pembayaran" class="form-label">Metode Pembayaran {{ dot() }}</label>
                        <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-control select2">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
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
                    <div class="col-span-12 parent non-tunai hidden">
                        <label for="keterangan" class="form-label">Bukti Transfer {{ dot() }}</label>
                        <input type="file" class="dropify" name="bukti_transfer" id="bukti_transfer" data-max-file-size="5M"
                            data-allowed-file-extensions="jpeg png jpg">
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
                    <h2 class="font-medium text-base mr-auto">Histori Pembayaran</h2>
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

    <div id="xlsDownload" style="display: none"></div>

    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
<script>
    var table;
    var filterTanggalAwal = null;
    var filterTanggalAkhir = null;
    var filterOwnerId = null;
    var filterBranchId = null;
    var filterTypeKasir = null;

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
                // lengthMenu: [
                //     [-1],
                //     ['Show all']
                // ],
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
                    url: "{{ route('datatableCicilan') }}",
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
                        branch_id() {
                            return $('#branch_id_filter').val();
                        },
                        type_kasir() {
                            return $('#type_kasir_filter').val();
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
                    data: 'kode',
                    name: 'kode'
                }, {
                    data: 'branch',
                    name: 'branch'
                }, {
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'updated_at',
                    name: 'updated_at'
                }, {
                    data: 'nama_owner',
                    name: 'nama_owner'
                }, {
                    data: 'no_registrasi',
                    name: 'no_registrasi'
                }, {
                    data: 'telpon',
                    name: 'telpon'
                }, {
                    data: 'pembayaran',
                    name: 'pembayaran',
                    class: 'text-right',
                }, {
                    data: 'sisa_pelunasan',
                    name: 'sisa_pelunasan',
                    class: 'text-right',
                }, {
                    data: 'catatan_kasir',
                    name: 'catatan_kasir',
                    class: 'text-left',
                }, {
                    data: 'created_by',
                    name: 'created_by',
                    class: 'text-left',
                },]
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

    function filter(params) {
        table.ajax.reload();
    }

    function edit(id) {
        $.ajax({
            url: '{{ route('editCicilan') }}',
            data: {
                id
            },
            type: 'get',
            success: function(data) {
                // clear();
                var temp_key = Object.keys(data.data);
                var temp_value = data.data;
                for (var i = 0; i < temp_key.length; i++) {
                    if(temp_key[i] == 'bukti_transfer'){
                        continue;
                    }
                    var key = temp_key[i];
                    $('#' + key).val(temp_value[key]);
                }
                $('.not-editable').prop('readonly', true);
                $('#modal-tambah-data .select2').trigger('change.select2');
                $('#simpan').removeClass('hidden');
                $('#sisa_pelunasan').val(accounting.formatNumber(data.data.sisa_pelunasan, {
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

    function openFilter() {
        slideOver.toggle();
    }

    function filter() {
        slideOver.toggle();
        table.ajax.reload();
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

        var formData = new FormData();

        var input = document.getElementById("bukti_transfer");
        if (input != null) {
            file = input.files[0];
            jenis = $('#jenis_pembayaran').val();
            if ((jenis == 'DEBET' || jenis == 'TRANSFER') && (file == undefined || file == null)) {
                ToastNotification('warning', 'Bukti Transfer Harus Diisi');
                return false;
            } else {
                formData.append("bukti_transfer", file);
            }
        }

        var data = $('#modal-tambah-data :input').serializeArray();


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
                overlay(true)
                window.onkeydown = previousWindowKeyDown;
                $.ajax({
                    url: '{{ route('storeCicilan') }}',
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
                            // $("#modal-tambah-data :input").val("");
                            // $("#modal-tambah-data :textarea").val("");
                            // $("#modal-tambah-data select").prop("selectedIndex", 0);

                            printBuktiPembayaran(data.kasir_id, data.id);
                            // clear();
                        } else if (data.status == 2) {
                            Swal.fire({
                                title: data.message,
                                icon: "warning",
                            });
                        } else if (data.status == 3) {
                            Swal.fire({
                                title: data.message,
                                icon: "warning",
                            });

                            $('#nilai_pembayaran').val(accounting.formatNumber(data.nilai, {
                                precision: 0
                            }));


                            $('#sisa_pelunasan').val(accounting.formatNumber(data.sisa, {
                                precision: 0
                            }));
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
                        overlay(false)
                    }
                });
            }
        })
    }

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
                overlay(true)
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
                        overlay(false)
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
                        overlay(false)
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
                overlay(true)
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
                        overlay(false)
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
                        overlay(false)
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

    $('#diskon_cicilan').keyup(function() {
        var sisa_pelunasan = $('#sisa_pelunasan').val().replace(/[^0-9\-]+/g, "") * 1;
        var diskon_cicilan = $(this).val().replace(/[^0-9\-]+/g, "") * 1;
        if (sisa_pelunasan < diskon_cicilan) {
            $('#diskon_cicilan').val(accounting.formatNumber(sisa_pelunasan, {
                precision: 0
            }));
        }else{
            $('#nilai_pembayaran').val(accounting.formatNumber(sisa_pelunasan - diskon_cicilan, {
                precision: 0
            }));
        }
    })

    $('#nilai_pembayaran').keyup(function() {
        var sisa_pelunasan = $('#sisa_pelunasan').val().replace(/[^0-9\-]+/g, "") * 1;
        var nilai_pembayaran = $(this).val().replace(/[^0-9\-]+/g, "") * 1;
        if (sisa_pelunasan < nilai_pembayaran) {
            $('#nilai_pembayaran').val(accounting.formatNumber(sisa_pelunasan, {
                precision: 0
            }));
        }
    })

    $(document).on('change', '#kategori_pembayaran', function() {
        $('#nilai_pembayaran').val('');
        $('#jenis_pembayaran').val('TUNAI').trigger('change');

        if ($(this).val() == 'Diskon Cicilan') {
            $('.diskon-cicilan').removeClass('hidden');
            $('#nilai_pembayaran').prop('readonly', true);

            var sisa_pelunasan = $('#sisa_pelunasan').val().replace(/[^0-9\-]+/g, "") * 1;
            $('#nilai_pembayaran').val(accounting.formatNumber(sisa_pelunasan, {
                precision: 0
            }));

            $('.non-hibah').removeClass('hidden');
        } else if ($(this).val() == 'Hibah') {
            $('.diskon-cicilan').addClass('hidden');
            $('#nilai_pembayaran').prop('readonly', true);

            var sisa_pelunasan = $('#sisa_pelunasan').val().replace(/[^0-9\-]+/g, "") * 1;
            $('#nilai_pembayaran').val(accounting.formatNumber(sisa_pelunasan, {
                precision: 0
            }));

            $('.non-hibah').addClass('hidden');
            $('.non-tunai').addClass('hidden');
        } else if ($(this).val() == 'Langsung Lunas') {
            $('.diskon-cicilan').addClass('hidden');
            $('#nilai_pembayaran').prop('readonly', true);

            var sisa_pelunasan = $('#sisa_pelunasan').val().replace(/[^0-9\-]+/g, "") * 1;
            $('#nilai_pembayaran').val(accounting.formatNumber(sisa_pelunasan, {
                precision: 0
            }));

            $('.non-hibah').removeClass('hidden');
        } else {
            $('.diskon-cicilan').addClass('hidden');
            $('#nilai_pembayaran').prop('readonly', false);

            $('.non-hibah').removeClass('hidden');
        }
        $('.diskon-cicilan').find('input').val('');
    })

    $(document).on('change', '#jenis_pembayaran', function() {
        if ($(this).val() == 'TUNAI') {
            $('.non-tunai').addClass('hidden');
        } else {
            $('.non-tunai').removeClass('hidden');
        }
        $('.non-tunai').find('input').val('');
    })

    function printBuktiPembayaran(kasir_id, id) {
        window.open('{{ route('printBuktiPembayaranCicilan') }}?kasir_id=' + kasir_id + '&id=' + id);
    }

    function excel(params) {
        window.open('{{ route('CicilanExcel') }}?tanggal_awal=' + $('#tanggal_awal').val() + '&tanggal_akhir=' +
            $('#tanggal_akhir').val() + '&type_kasir_filter=' + $('#type_kasir_filter').val() + '&branch_id_filter=' +
            $('#branch_id_filter').val() + '&owner_id_filter=' + $('#owner_id_filter').val());
    }
</script>
@endsection
