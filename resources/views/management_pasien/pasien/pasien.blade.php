@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('../management_pasien/pasien/filter_pasien')
@endsection

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
                    <th class="no-sort">No</th>
                    <th class="no-sort">Aksi</th>
                    <th class="no-sort">Image</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Branch</th>
                    <th>Hewan</th>
                    <th>Ras</th>
                    <th>Jenis Kelamin</th>
                    <th>Owner</th>
                    <th>Ciri Khas</th>
                    <th>Tanggal Lahir</th>
                    <th>Berat</th>
                    <th>Tanggal Awal Periksa</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Updated By</th>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Data</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <form class="modal-body grid grid-cols-12 gap-4 gap-y-3" id="form-data">
                    <div class="col-span-12">
                        <label>Foto</label>
                        <input type="file" class="dropify text-sm" id="image" name="image">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent">
                        <label for="kode" class="form-label">Kode {{ dot() }}</label>

                        <input id="kode" name="kode" type="text" class="form-control uppercase required" readonly
                            placeholder="">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent {{ Auth::user()->akses('global') ? '' : 'disabled' }}">
                        <label for="name" class="form-label">Branch{{ dot() }}</label>
                        <select name="branch_id" id="branch_id" class="select2 form-control required">
                            <option value="">Pilih Branch</option>
                            @foreach (\App\Models\Branch::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->alamat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="name" class="form-label">Nama Hewan{{ dot() }}</label>
                        <input id="name" name="name" type="text" class="form-control required"
                            placeholder="">
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="binatang_id" class="form-label">Jenis Hewan {{ dot() }}</label>
                        <select name="binatang_id" id="binatang_id" class="form-control binatang_id select2 required"
                            onchange="generateKode()">
                            <option value="">Pilih Hewan</option>
                            @foreach (\App\Models\Binatang::where('status', true)->get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="ras_id" class="form-label">Ras {{ dot() }}</label>
                        <select name="ras_id" id="ras_id" class="form-control select2">
                            <option value="">Pilih Ras</option>
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="sex" class="form-label">Jenis Kelamin {{ dot() }}</label>
                        <select name="sex" id="sex" class="form-control required select2">
                            <option value="">Pilih Jenis Kelamin</option>
                            @foreach (\App\Models\Pasien::$enumJenisKelamin as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="owner_id" class="form-label">Owner{{ dot() }}</label>
                        <select name="owner_id" id="owner_id" class="form-control select2 required">
                            <option value="">Pilih Owner</option>
                            @foreach (\App\Models\Owner::where('status', true)->get() as $item)
                                <option data-branch="{{ $item->branch_id }}" value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="date_of_birth" class="form-label">Tanggal Lahir {{ dot() }}</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <input id="date_of_birth" name="date_of_birth" type="text"
                                class="form-control required datepicker" placeholder="yyyy-mm-dd"
                                placeholder="yyyy-mm-dd" data-single-mode="true">
                        </div>
                    </div>
                     <div class="col-span-12 md:col-span-4 parent">
                        <label for="berat" class="form-label">Berat Hewan{{ dot() }}</label>
                        <input id="berat" name="berat" type="text" class="form-control required"
                            placeholder="">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="ciri_khas" class="form-label">Ciri Khas {{ dot() }}</label>
                        <textarea id="ciri_khas" name="ciri_khas" type="text" class="form-control required"></textarea>
                    </div>
                </form>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary w-20" id="simpan" onclick="store()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>

    <div id="modal-rekam-medis" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width: 80% !important">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Rekam Medis</h2>
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
                        url: "{{ route('datatablePasien') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                            owner_id() {
                                return $('#owner_id_filter').val();
                            },
                            binatang_id() {
                                return $('#binatang_id_filter').val();
                            },
                            ras_id() {
                                return $('#ras_id_filter').val();
                            }
                        }
                    },
                    columnDefs: [{
                        'orderable': false,
                        'targets': 'no-sort'
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center'
                    }, {
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, {
                        data: 'image',
                        kode: 'name',
                        class: 'text-center',
                    }, {
                        data: 'kode',
                        kode: 'name'
                    }, {
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'branch',
                        name: 'branch'
                    }, {
                        data: 'binatang',
                        name: 'binatang'
                    }, {
                        data: 'ras',
                        name: 'mk_ras'
                    }, {
                        data: 'sex',
                        name: 'sex'
                    }, {
                        data: 'owner',
                        name: 'owner'
                    }, {
                        data: 'ciri_khas',
                        name: 'ciri_khas',
                    }, {
                        data: 'date_of_birth',
                        name: 'date_of_birth',
                    }, {
                        data: 'berat',
                        name: 'berat',
                    }, {
                        data: 'tanggal_awal_periksa',
                        name: 'tanggal_awal_periksa',
                    }, {
                        data: 'status',
                        class: 'text-center',
                    }, {
                        data: 'created_by',
                        name: 'created_by',
                    }, {
                        data: 'updated_by',
                        name: 'updated_by',
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

            $("#ras_id").select2({
                width: '100%',
                dropdownParent: $("#modal-tambah-data .modal-body"),
                ajax: {
                    url: "{{ route('select2Pasien') }}?param=ras_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            binatang_id() {
                                return $('#binatang_id').val();
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
                placeholder: 'Pilih Binatang Dahulu',
                minimumInputLength: 0,
                templateResult: formatRepoStatus,
                templateSelection: formatRepoStatusSelection
            });

            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");
            $('.dropify').dropify();

            $('#tambah-data').click(function() {
                clear();
                $('.not-editable').removeClass('disabled');
                $('.not-editable').prop('readonly', false);
                $('.parent').removeClass('disabled');
                $('#simpan').removeClass('hidden');
                $('#branch_id').val('{{ Auth::user()->branch_id }}').trigger('change.select2');
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();

            })

            tomGenerator('.tomSelect');
        })()

        function openFilter() {
            slideOver.toggle();
        }

        function filter() {
            slideOver.toggle();
            table.ajax.reload();
        }

        function generateKode() {
            if ($('#id').val() == '' && $('#binatang_id').val() != '' && $('#binatang_id').val() != '') {
                $.ajax({
                    url: "{{ route('generateKodePasien') }}",
                    type: 'get',
                    data: {
                        binatang_id() {
                            return $('#binatang_id').val();
                        },
                        branch_id() {
                            return $('#branch_id').val();
                        },
                    },
                    success: function(data) {
                        $('#kode').val(data.kode);
                    },
                    error: function(data) {
                        generateKode();
                    }
                });
            }
        }

        function edit(id) {
            $.ajax({
                url: '{{ route('editPasien') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        if ($('#' + key).length != 0) {
                            if (!$('#' + key).hasClass('dropify')) {
                                $('#' + key).val(temp_value[key]);
                            }
                        }
                    }

                    if (data.data.ras != null) {
                        var newOption = new Option(data.data.ras.name,
                            data.data.ras.id,
                            true,
                            true
                        );

                        $('#ras_id').append(newOption).trigger('change');
                    }
                    var url = "{{ url('/') }}" + '/' + data.data.image;
                    var imagenUrl = url;
                    var drEvent = $('.dropify').dropify({
                        defaultFile: imagenUrl,
                    });

                    drEvent = drEvent.data('dropify');
                    drEvent.resetPreview();
                    drEvent.clearElement();
                    drEvent.settings.defaultFile = imagenUrl;
                    drEvent.destroy();
                    drEvent.init();
                    $('.not-editable').prop('readonly', true);
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    $('#simpan').removeClass('hidden');
                    $('.parent').removeClass('disabled');

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
                url: '{{ route('editPasien') }}',
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
                        if ($('#' + key).length != 0) {
                            if (!$('#' + key).hasClass('dropify')) {
                                $('#' + key).val(temp_value[key]);
                            }
                        }
                    }

                    $('.parent').addClass('disabled');

                    if (data.data.ras != null) {
                        var newOption = new Option(data.data.ras.name,
                            data.data.ras.id,
                            true,
                            true
                        );

                        $('#ras_id').append(newOption).trigger('change');
                    }
                    var url = "{{ url('/') }}" + '/' + data.data.image;
                    var imagenUrl = url;
                    var drEvent = $('.dropify').dropify({
                        defaultFile: imagenUrl,
                    });

                    drEvent = drEvent.data('dropify');
                    drEvent.resetPreview();
                    drEvent.clearElement();
                    drEvent.settings.defaultFile = imagenUrl;
                    drEvent.destroy();
                    drEvent.init();
                    const el = document.querySelector("#modal-tambah-data");
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
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

            var formData = new FormData();

            var input = document.getElementById("image");
            if (input != null) {
                file = input.files[0];
                formData.append("image", file);
            }

            var data = $('#form-data').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            console.log(data);
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
                        url: '{{ route('storePasien') }}',
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
                        url: '{{ route('deletePasien') }}',
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

        function lihatRekamMedis(id) {
            $.ajax({
                url: "{{ route('getRekamMedisPasien') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    idPasien = id;
                    $('#append-rekam-medis').html(data);
                    const el = document.querySelector("#modal-rekam-medis");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                },
                error: function(data) {
                    lihatRekamMedis(id);
                }
            });
        }

        function gantiStatus(param, id) {
            $.ajax({
                url: "{{ route('statusPasien') }}",
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

        function formatRepoStatus(repo) {
            if (repo.loading) {
                return repo.text;
            }
            // scrolling can be used
            var markup = $('<span value=' + repo.id + '>' + repo.text + '</span>');
            return markup;
        }

        function formatRepoStatusSelection(repo) {
            return repo.text || repo.text;
        }
    </script>
@endsection
