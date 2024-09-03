@extends('../layout/' . $layout)
@section('header_filter')
    Filter Pengeluaran
@endsection

@section('content_filter')
    @include('../transaksi/bukti_kas_keluar/filter_bukti_kas_keluar')
@endsection

@section('subcontent')
    {{-- <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2> --}}
    <h2 class="intro-y text-lg font-medium mt-10">Pengeluaran</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
                @if (Auth::user()->akses('create'))
                    <a class="btn btn-primary shadow-md mr-2" id="tambah-data"
                        href="{{ route('createBuktiKasKeluar') }}">Tambah Data</a>
                @endif
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
                    <th>Kode</th>
                    <th>Branch</th>
                    {{-- <th>Perihal</th> --}}
                    <th>Tanggal</th>
                    <th>Metode Pembayaran</th>
                    <th>Jumlah Item</th>
                    <th>Total</th>
                    <th>Perihal</th>
                    <th>Dibuat Oleh</th>
                </thead>

                <tbody>

                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
    </div>
    <!-- BEGIN: Delete Confirmation Modal -->
    <div id="modal-detail" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
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
                        <input type="hidden" id="id">
                        <table class="table mt-2 stripe hover table-bordered" id="tableDetail"
                            style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                            <thead align="center">
                                <th>Master Akun Transaksi</th>
                                <th>Item</th>
                                <th>Nominal</th>
                                <th>Proofment</th>
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
@endsection
@section('script')
    <script>
        var table;
        var tableDetail;
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
                        url: "{{ route('datatableBuktiKasKeluar') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                            tanggal_awal() {
                                return $('#tanggal_awal').val();
                            },
                            tanggal_akhir() {
                                return $('#tanggal_akhir').val();
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
                        data: 'tanggal',
                        name: 'tanggal'
                    }, {
                        data: 'metode_pembayaran',
                        name: 'metode_pembayaran'
                    }, {
                        data: 'jumlah_item',
                        class: 'text-center',
                    }, {
                        data: 'nominal',
                        class: 'text-right',
                    }, {
                        data: 'description',
                        class: 'text-center',
                    }, {
                        data: 'updated_by',
                        class: 'text-center',
                    }, ]
                })
                .columns.adjust()
                .responsive.recalc();


            tableDetail = $('#tableDetail').DataTable({
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
                        url: "{{ route('datatableDetailBuktiKasKeluar') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id() {
                                return $('#id').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'master_akun_transaksi',
                        name: 'master_akun_transaksi',
                        class: 'text-left',
                    }, {
                        data: 'redaksi',
                        name: 'redaksi',
                        class: 'text-left',
                    }, {
                        data: 'nominal',
                        name: 'nominal',
                        class: 'text-right',
                    }, {
                        data: 'proofment',
                        name: 'proofment',
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

            $('.select2').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                width: '100%',
            })

            $('.select2filter').select2({
                dropdownParent: $("#slide-over-filter .modal-body"),
                width: '100%',
            })

            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            tomGenerator('.tomSelect');
        })()

        function openFilter() {
            slideOver.toggle();
        }

        function filter() {
            slideOver.toggle();
            table.ajax.reload();
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
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('deleteBuktiKasKeluar') }}',
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

        function formatRepoNormalSelection(repo) {
            return repo.text || repo.text;
        }

        function formatRepoNormal(repo) {
            if (repo.loading) {
                return repo.text;
            }
            // scrolling can be used
            var markup = $('<span  data-name=' + repo.name + ' value=' + repo.id + '>' + repo.text + '</span>');
            return markup;
        }

        function lihatDetail(id) {
            $('#id').val(id);

            console.log($('#id').val());
            const el = document.querySelector("#modal-detail");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();

            tableDetail.ajax.reload();
        }
    </script>
@endsection
