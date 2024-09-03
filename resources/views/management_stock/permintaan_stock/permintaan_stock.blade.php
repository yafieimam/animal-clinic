@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('../management_stock/stock/filter_stock')
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
            <table class="table mt-2 stripe hover" id="table" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th><b>Opsi</b></th>
                    <th><b>Permintaan Oleh</b></th>
                    <th><b>Item Permintaan Stock</b></th>
                    <th><b>Branch</b></th>
                    <th><b>Item</b></th>
                    <th><b>Quantity</b></th>
                    <th><b>Tanggal Permintaan</b></th>
                    <th><b>Status</b></th>
                    <th><b>Tanggal</b></th>
                    <th><b>Konfirmasi Oleh</b></th>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Tambah Penerimaan</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 md:col-span-12 parent">
                        <label for="branch_id" class="form-label">Branch {{ dot() }}</label>
                        <select name="branch_id" id="branch_id" class="form-control branch_id select2 required">
                            <option value="">Pilih Branch</option>
                            @foreach (\App\Models\Branch::where('status', true)->get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->lokasi }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="item" class="form-label">Nama Item</label>
                        <input type="text" id="item" class="form-control" name="item" readonly>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="qty" class="form-label">Qty {{ dot() }}</label>
                        <input type="text" id="qty" class="form-control" placeholder="Masukan Qty PermintaanStock"
                            name="qty">
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary" id="simpan" onclick="store()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var table;
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
                        url: "{{ route('datatablePermintaanStock') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                            jenis_item() {
                                return $('#jenis_item').val();
                            },
                            item_id() {
                                return $('#item_id').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center'
                    }, {
                        data: 'created_by',
                        name: 'created_by'
                    }, {
                        data: 'jenis_stock',
                        name: 'jenis_stock',
                        class: 'text-center',
                    }, {
                        data: 'branch',
                        name: 'branch'
                    }, {
                        data: 'item',
                        name: 'item'
                    }, {
                        data: 'qty',
                        name: 'qty',
                        class: 'text-center',
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        class: 'text-center',
                    }, {
                        data: 'status',
                        name: 'status',
                        class: 'text-center',
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        class: 'text-center',
                    }, {
                        data: 'updated_by',
                        name: 'updated_by',
                        class: 'text-center',
                    }]
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

            $("#item_id").select2({
                dropdownParent: $("#slide-over-filter .modal-body"),
                width: '100%',
                ajax: {
                    url: "{{ route('select2PenerimaanStock') }}?param=item_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            jenis_item() {
                                return $('#jenis_item').val();
                            },
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 10) < data.total
                            }
                        };
                    },
                    cache: true,
                    type: 'GET',
                },
                placeholder: 'Cari Item',
                minimumInputLength: 0,
                templateResult: formatRepoNormal,
                templateSelection: formatRepoNormalSelection
            });
            tomGenerator('.tomSelect');
        })()

        function requestPermintaanStock(id, branchId, item) {
            $('#branch_id').val(branchId).trigger('change.select2');
            $('#id').val(id);
            $('#item').val(item);
            const el = document.querySelector("#modal-tambah-data");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
        }

        function openFilter() {
            slideOver.toggle();
        }

        function filter() {
            print
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
                        url: '{{ route('deletePermintaanStock') }}',
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

        function store(id, param) {
            var validation = 0;

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
                        url: '{{ route('confirmationPermintaanStock') }}',
                        data: {
                            id: id,
                            param: param,
                        },
                        type: 'get',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,

                                    icon: "success",
                                });
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            } else {
                                Swal.fire({
                                    title: 'Ada Kesalahan !!!',
                                    icon: "warning",
                                    html: data,
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
    </script>
@endsection
