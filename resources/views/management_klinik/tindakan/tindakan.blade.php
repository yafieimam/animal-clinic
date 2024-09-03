@extends('../layout/' . $layout)

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
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
                                <a href="javascript:;" class="dropdown-item" onclick="bulkImportModal()">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Bulk Import
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
        <div class="col-span-12 ">
            <h5><b>Filter</b></h5>
        </div>
        {{-- <div class="col-span-12 md:col-span-4 ">
            <label for="poli_id_filter" class="form-label">Filter Poli</label>
            <select name="poli_id_filter" id="poli_id_filter" class="select2filter form-control">
                <option value="">Semua Poli</option>
                @foreach (\App\Models\Poli::get() as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div> --}}
        <div class="col-span-12 md:col-span-4 ">
            <label for="binatang_id_filter" class="form-label">Filter Hewan</label>
            <select name="binatang_id_filter" id="binatang_id_filter" class="select2filter form-control">
                <option value="">Semua Hewan</option>
                @foreach (\App\Models\Binatang::where('status', 't')->get() as $item)
                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-12 md:col-span-4">
            <label class="form-label block">&nbsp;</label>
            <button class="btn btn-primary shadow-md mr-2" onclick="filter()"><i
                    class="fas fa-search"></i>&nbsp;Search</button>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <table class="table mt-2 stripe hover" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Opsi</th>
                    <th>Nama</th>
                    {{-- <th>Poli</th> --}}
                    <th>Hewan</th>
                    <th>Diskon</th>
                    <th>Tarif</th>
                    <th>Keterangan</th>
                    <th>Status</th>
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
                        <label for="name" class="form-label">Nama Tindakan {{ dot() }}</label>
                        <input id="name" name="name" type="text" class="form-control required"
                            placeholder="Tulis Nama Tindakan">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    {{-- <div class="col-span-12 parent">
                        <label for="name" class="form-label">Poli {{ dot() }}</label>
                        <select name="poli_id" id="poli_id" class="select2 form-control required">
                            <option value="">Pilih Poli</option>
                            @foreach (\App\Models\Poli::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="col-span-12 parent">
                        <label for="name" class="form-label">Hewan {{ dot() }}</label>
                        <select name="binatang_id" id="binatang_id" class="select2 form-control required">
                            <option selected value="semua">Semua Hewan</option>
                            @foreach (\App\Models\Binatang::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="name" class="form-label">Diskon {{ dot() }}</label>
                        <select name="diskon" id="diskon" class="select2 form-control required">
                            <option selected value="">Pilih Status Diskon</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="tarif" class="form-label">Tarif Tindakan {{ dot() }}</label>
                        <input id="tarif" name="tarif" type="text" class="form-control required mask"
                            placeholder="Tulis tarif">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="description" class="form-label">Keterangan {{ dot() }}</label>
                        <textarea id="description" name="description" type="text" class="form-control required"
                            placeholder="Tulis Keterangan"></textarea>
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
    <!-- END: Delete Confirmation Modal -->


    <!-- BEGIN: Modal Content -->
    <div id="modal-import-bulk" class="modal" tabindex="-1" data-tw-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Bulk Import</h2>
                    <button class="btn btn-outline-secondary hidden sm:flex" onclick="downloadTemplate()">
                        <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Template
                    </button>
                    <div class="dropdown sm:hidden">
                        <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false"
                            data-tw-toggle="dropdown">
                            <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i>
                        </a>
                        <div class="dropdown-menu w-40">
                            <ul class="dropdown-content">
                                <li>
                                    <a href="javascript:;" class="dropdown-item">
                                        <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Docs
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <div class="form-group">
                            <label class="form-label">Upload Excel</label>
                            <div class="file-upload upl_1" style="width: 100%;">
                                <div class="file-select">
                                    <div class="file-select-button fileName">Upload</div>
                                    <div class="file-select-name noFile tag_image_1">Format file harus csv</div>
                                    <input type="file" class="chooseFile upload_pdf" id="selectExcel" name="image">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 overflow-y-auto">
                        <div id="spreadsheet" style="width: 100%"></div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="bulkImport()">Mulai Bulk Import</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
    <!-- END: Modal Content -->
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
                        url: "{{ route('datatableTindakan') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            binatang_id() {
                                return $('#binatang_id_filter').val();
                            },
                            poli_id() {
                                return $('#poli_id_filter').val();
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
                            data: 'name',
                            name: 'name'
                        },
                        // {
                        //     data: 'poli',
                        //     name: 'poli'
                        // },
                        {
                            data: 'binatang',
                            name: 'binatang'
                        }, {
                            data: 'diskon',
                            name: 'diskon',
                            class: 'text-center',
                        }, {
                            data: 'tarif',
                            name: 'tarif',
                            class: 'text-right',
                        }, {
                            data: 'description',
                            name: 'description'
                        }, {
                            data: 'status',
                            class: 'text-center',
                        },
                    ]
                })
                .columns.adjust()
                .responsive.recalc();

            $('#myInputTextField').keyup(debounce(function() {
                table.search($(this).val()).draw();
            }, 500));

            $('.mask').maskMoney({
                precision: 0,
                thousands: ',',
                allowZero: true,
                defaultZero: true,
            })

            $('.select2').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                // theme: 'bootstrap4',
            })

            $('.select2filter').select2({
                // theme: 'bootstrap4',
            })
            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            $('#tambah-data').click(function() {
                clear();
                $('.not-editable').removeClass('disabled');
                $('.not-editable').prop('readonly', false);
                $('#binatang_id').val('semua').trigger('change.select2')
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();

            })

            tomGenerator('.tomSelect');
        })()

        function filter(params) {
            table.ajax.reload();
        }

        function bulkImportModal() {
            const el = document.querySelector("#modal-import-bulk");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
            // $("#spreadsheet").html('');
        }

        $(function() {
            $("#selectExcel").change(handleFileSelect);
        });

        function edit(id) {
            $.ajax({
                url: '{{ route('editTindakan') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        $('#' + key).val(temp_value[key]);
                    }
                    $('.not-editable').prop('readonly', true);
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    $('#simpan').removeClass('hidden');

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

        function lihat(id) {
            $.ajax({
                url: '{{ route('editTindakan') }}',
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
                        url: '{{ route('storeTindakan') }}',
                        data: $('#modal-tambah-data :input').serialize(),
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                clear();
                                $('#binatang_id').val('semua').trigger('change.select2')
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
                        url: '{{ route('deleteTindakan') }}',
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
                url: "{{ route('statusTindakan') }}",
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

        function handleFileSelect(evt) {
            var file = evt.target.files[0];

            $('.noFile').text(file.name);
            $('.file-upload').addClass('active');

            Papa.parse(file, {
                skipEmptyLines: true,
                complete: function(results) {
                    var data = results.data;
                    generateExcel(data);
                }
            });
        }

        function generateExcel(data = '') {

            $('#spreadsheet').html('');

            var arr = [];
            var header = [];
            console.log(data);
            data.forEach((d, i) => {
                if (i > 1) {
                    arr.push(d);
                } else if (i == 1) {
                    d.forEach((d1, i1) => {
                        header.push({
                            title: d1
                        });
                    })
                }
            })

            console.log(arr);
            console.log(header);

            table = jexcel(document.getElementById('spreadsheet'), {
                data: arr,
                tableOverflow: true,
                lazyLoading: true,
                loadingSpin: true,
                csvHeaders: true,
                csvDelimiter: ',',
                download: true,
                minDimensions: [10, 20],
                defaultColWidth: 100,
                tableOverflow: true,
                contextMenu: false,
                csvFileName: 'BULK IMPORT ({{ carbon\carbon::now()->format('d-M-Y') }})',
                columns: header,
            });
        }

        function downloadTemplate() {
            window.open('{{ url('/') }}' + '/storage/template_tindakan.csv');
            window.open('{{ route('binatangExcel') }}');
            // window.open('{{ route('satuanObatExcel') }}');
            // window.open('{{ route('typeObatExcel') }}');
        }


        function bulkImport() {
            try {
                var jsonData = table.getJson();
                var headers = table.getHeaders();
            } catch (error) {
                return ToastNotification('warning', 'Tidak ada data CSV yang diupload.');
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
                        url: '{{ route('bulkImportTindakan') }}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            data: jsonData,
                            header: headers
                        },
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                clear();
                                table.ajax.reload();
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
