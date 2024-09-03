@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('../management_karyawan/pindah_jadwal_jaga/filter_pindah_jadwal_jaga')
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
            <table class="table mt-2 stripe hover" id="table" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Opsi</th>
                    <th>Branch</th>
                    <th>Dokter Yang Mengajukan</th>
                    <th>Dokter Yang Diajukan</th>
                    <th>Tanggal Ganti</th>
                    <th>Jadwal</th>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Data</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12  parent">
                        <label class="form-label">Branch {{ dot() }}</label>
                        <select name="branch_id" id="branch_id" class="select2 form-control required">
                            <option value="">Pilih Branch</option>
                            @foreach (\App\Models\Branch::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->alamat }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-6 md:col-span-3 parent">
                        <label class="form-label">Ganti Tanggal {{ dot() }}</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>

                            <input type="text" class="form-control required" id="tanggal_awal" data-single-mode="true"
                                name="tanggal_awal">
                        </div>
                    </div>
                    <div class="col-span-6 md:col-span-3 parent">
                        <label class="form-label">Hari {{ dot() }}</label>
                        <input id="hari_awal" type="text" class="form-control" name="hari_awal" readonly value=""
                            autocomplete="hari_awal">
                    </div>
                    <div class="col-span-6 md:col-span-3 parent">
                        <label class="form-label">Ganti Tanggal {{ dot() }}</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control required" id="tanggal_tujuan" name="tanggal_tujuan"
                                data-single-mode="true">
                        </div>
                    </div>
                    <div class="col-span-6 md:col-span-3 parent">
                        <label class="form-label">Hari {{ dot() }}</label>
                        <input id="hari_tujuan" type="text" class="form-control" name="hari_tujuan" readonly value=""
                            autocomplete="hari_tujuan">
                    </div>
                    <div class="col-span-6 md:col-span-6 parent">
                        <label class="form-label">Yang Mengajukan {{ dot() }}</label>
                        <select name="dokter_peminta" id="dokter_peminta"
                            class="form-control dokter_peminta select2 required">
                            <option value="">Pilih Dokter</option>
                            @foreach (dokter(true) as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-6 md:col-span-6 parent">
                        <label class="form-label">Pindah jadwal dengan {{ dot() }}</label>
                        <select name="dokter_diminta" id="dokter_diminta"
                            class="form-control dokter_diminta select2 required">
                            <option value="">Pilih Dokter</option>
                            @foreach (dokter(true) as $item)
                                @if ($item->id != me())
                                    <option data-name="{{ $item->name }}" value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-6 md:col-span-6 parent">
                        <label class="form-label">Jadwal <span class="nama-pembuat-dokter"></span> pada hari
                            {{ dot() }}</label>
                        <select name="jadwal_dokter_awal_id" id="jadwal_dokter_awal_id"
                            class="form-control jadwal_dokter_awal_id select2 required">

                        </select>
                    </div>
                    <div class="col-span-6 md:col-span-6 parent">
                        <label class="form-label">Jadwal <span class="nama-ke-dokter"></span> pada hari <span
                                class="hari-ke-dokter"></span>
                            {{ dot() }}</label>
                        <select name="jadwal_dokter_tujuan_id" id="jadwal_dokter_tujuan_id"
                            class="form-control jadwal_dokter_tujuan_id select2 required">

                        </select>
                    </div>
                </div>
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
                        url: "{{ route('datatablePindahJadwalJaga') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                            divisi_id() {
                                return $('#divisi_id_filter').val();
                            },
                            bagian_id() {
                                return $('#bagian_id_filter').val();
                            },
                            jabatan_id() {
                                return $('#jabatan_id_filter').val();
                            },
                            jenis_kelamin() {
                                return $('#jenis_kelamin_filter').val();
                            },
                            status_pernikahan() {
                                return $('#status_pernikahan_filter').val();
                            },
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
                        data: 'branch',
                        name: 'branch'
                    }, {
                        data: 'DokterPeminta',
                        name: 'DokterPeminta'
                    }, {
                        data: 'DokterDiminta',
                        name: 'DokterDiminta',
                    }, {
                        data: 'tanggal',
                        name: 'tanggal'
                    }, {
                        data: 'jadwal',
                        name: 'jadwal',
                    }, {
                        data: 'status',
                        class: 'text-center',
                        orderable: false,
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

            $("#tanggal_awal").each(function() {
                let options = {
                    autoApply: false,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    minDate: '{{ Carbon\carbon::now()->subDay(0)->format('Y-m-d') }}',
                    format: "YYYY-MM-DD",
                    dropdowns: {
                        minYear: 1990,
                        maxYear: null,
                        months: true,
                        years: true,
                    },
                };

                if ($(this).data("single-mode")) {
                    options.singleMode = true;
                    options.numberOfColumns = 1;
                    options.numberOfMonths = 1;
                }

                if ($(this).data("format")) {
                    options.format = $(this).data("format");
                }

                if (!$(this).val()) {
                    let date = dayjs().format(options.format);
                    date += !options.singleMode ?
                        " - " + dayjs().add(1, "month").format(options.format) :
                        "";
                    $(this).val(date);
                }

                new Litepicker({
                    element: this,
                    ...options,
                    setup: (picker) => {
                        picker.on('button:apply', (date1, date2) => {
                            generateHariAwal();
                        });
                    },
                });
            });

            $("#tanggal_tujuan").each(function() {
                let options = {
                    autoApply: false,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    minDate: '{{ Carbon\carbon::now()->subDay(0)->format('Y-m-d') }}',
                    format: "YYYY-MM-DD",
                    dropdowns: {
                        minYear: 1990,
                        maxYear: null,
                        months: true,
                        years: true,
                    },
                };

                if ($(this).data("single-mode")) {
                    options.singleMode = true;
                    options.numberOfColumns = 1;
                    options.numberOfMonths = 1;
                }

                if ($(this).data("format")) {
                    options.format = $(this).data("format");
                }

                if (!$(this).val()) {
                    let date = dayjs().format(options.format);
                    date += !options.singleMode ?
                        " - " + dayjs().add(1, "month").format(options.format) :
                        "";
                    $(this).val(date);
                }

                new Litepicker({
                    element: this,
                    ...options,
                    setup: (picker) => {
                        picker.on('button:apply', (date1, date2) => {
                            generateHariTujuan();
                        });
                    },
                });
            });

            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            $('#tambah-data').click(function() {
                clear();
                $('.not-editable').removeClass('disabled');
                $('.not-editable').prop('readonly', false);
                $('.parent').removeClass('disabled');
                $('#simpan').removeClass('hidden');

                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();

            })

            $("#jadwal_dokter_awal_id").select2({
                width: '100%',
                dropdownParent: $("#modal-tambah-data .modal-body"),
                ajax: {
                    url: "{{ route('select2PindahJadwalJaga') }}?param=jadwal_dokter_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            hari() {
                                return $('#hari_awal').val();
                            },
                            branch_id() {
                                return $('#branch_id').val();
                            },
                            dokter() {
                                return $('#dokter_peminta').val();
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
                placeholder: 'Pilih Dokter Yang Mengajukan Dahulu',
                minimumInputLength: 0,
                templateResult: formatRepoJadwalDokter,
                templateSelection: formatRepoJadwalDokterSelection
            });

            $("#jadwal_dokter_tujuan_id").select2({
                width: '100%',
                dropdownParent: $("#modal-tambah-data .modal-body"),
                ajax: {
                    url: "{{ route('select2PindahJadwalJaga') }}?param=jadwal_dokter_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            hari() {
                                return $('#hari_tujuan').val();
                            },
                            branch_id() {
                                return $('#branch_id').val();
                            },
                            dokter() {
                                return $('#dokter_diminta').val();
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
                placeholder: 'Pilih Dokter Yang Diajukan Dahulu',
                minimumInputLength: 0,
                templateResult: formatRepoJadwalDokter,
                templateSelection: formatRepoJadwalDokterSelection
            });

            tomGenerator('.tomSelect');
        })()

        function openFilter() {
            slideOver.toggle();
        }

        function filter() {
            print
            slideOver.toggle();
            table.ajax.reload();
        }

        function edit(id) {
            $.ajax({
                url: '{{ route('editPindahJadwalJaga') }}',
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

                    if (data.data.dokter_peminta != null) {
                        var newOption = new Option(data.data.dokter_peminta.name,
                            data.data.dokter_peminta.id,
                            true,
                            true
                        );

                        $('#dokter_peminta').append(newOption).trigger('change');
                    }

                    if (data.data.dokter_diminta != null) {
                        var newOption = new Option(data.data.dokter_diminta.name,
                            data.data.dokter_diminta.id,
                            true,
                            true
                        );

                        $('#dokter_diminta').append(newOption).trigger('change');
                    }

                    if (data.data.branch != null) {
                        var newOption = new Option(data.data.branch.kode,
                            data.data.branch.id,
                            true,
                            true
                        );

                        $('#branch_id').append(newOption).trigger('change');
                    }
                    if (data.data.jadwal_dokter_awal != null) {
                        var newOption = new Option(data.data.jadwal_dokter_awal.hari + ' ' + data.data
                            .jadwal_dokter_awal
                            .jam_pertama.jam_awal + ':' + data.data.jadwal_dokter_awal.jam_pertama
                            .menit_awal +
                            ' s/d ' + data.data
                            .jadwal_dokter_awal
                            .jam_terakhir.jam_awal + ':' + data.data.jadwal_dokter_awal.jam_terakhir
                            .menit_awal,
                            data.data.jadwal_dokter_awal.id,
                            true,
                            true
                        );

                        $('#jadwal_dokter_awal_id').append(newOption).trigger('change');
                        $('#hari_awal').val(data.data.jadwal_dokter_awal.hari);
                    }

                    if (data.data.jadwal_dokter_tujuan != null) {
                        var newOption = new Option(data.data.jadwal_dokter_tujuan.hari + ' ' + data.data
                            .jadwal_dokter_tujuan
                            .jam_pertama.jam_awal + ':' + data.data.jadwal_dokter_tujuan.jam_pertama
                            .menit_awal +
                            ' s/d ' + data.data
                            .jadwal_dokter_tujuan
                            .jam_terakhir.jam_awal + ':' + data.data.jadwal_dokter_tujuan.jam_terakhir
                            .menit_awal,
                            data.data.jadwal_dokter_tujuan.id,
                            true,
                            true
                        );

                        $('#jadwal_dokter_tujuan_id').append(newOption).trigger('change');
                        $('#hari_tujuan').val(data.data.jadwal_dokter_tujuan.hari);
                    }

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
                url: '{{ route('editPindahJadwalJaga') }}',
                data: {
                    id,
                    param: 'lihat',
                },
                type: 'get',
                success: function(data) {
                    $('.parent').addClass('disabled');
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        $('#' + key).val(temp_value[key]);
                    }

                    if (data.data.dokter_peminta != null) {
                        var newOption = new Option(data.data.dokter_peminta.name,
                            data.data.dokter_peminta.id,
                            true,
                            true
                        );

                        $('#dokter_peminta').append(newOption).trigger('change');
                    }

                    if (data.data.dokter_diminta != null) {
                        var newOption = new Option(data.data.dokter_diminta.name,
                            data.data.dokter_diminta.id,
                            true,
                            true
                        );

                        $('#dokter_diminta').append(newOption).trigger('change');
                    }

                    if (data.data.branch != null) {
                        var newOption = new Option(data.data.branch.kode,
                            data.data.branch.id,
                            true,
                            true
                        );

                        $('#branch_id').append(newOption).trigger('change');
                    }
                    if (data.data.jadwal_dokter_awal != null) {
                        var newOption = new Option(data.data.jadwal_dokter_awal.hari + ' ' + data.data
                            .jadwal_dokter_awal
                            .jam_pertama.jam_awal + ':' + data.data.jadwal_dokter_awal.jam_pertama
                            .menit_awal +
                            ' s/d ' + data.data
                            .jadwal_dokter_awal
                            .jam_terakhir.jam_awal + ':' + data.data.jadwal_dokter_awal.jam_terakhir
                            .menit_awal,
                            data.data.jadwal_dokter_awal.id,
                            true,
                            true
                        );

                        $('#jadwal_dokter_awal_id').append(newOption).trigger('change');
                        $('#hari_awal').val(data.data.jadwal_dokter_awal.hari);
                    }

                    if (data.data.jadwal_dokter_tujuan != null) {
                        var newOption = new Option(data.data.jadwal_dokter_tujuan.hari + ' ' + data.data
                            .jadwal_dokter_tujuan
                            .jam_pertama.jam_awal + ':' + data.data.jadwal_dokter_tujuan.jam_pertama
                            .menit_awal +
                            ' s/d ' + data.data
                            .jadwal_dokter_tujuan
                            .jam_terakhir.jam_awal + ':' + data.data.jadwal_dokter_tujuan.jam_terakhir
                            .menit_awal,
                            data.data.jadwal_dokter_tujuan.id,
                            true,
                            true
                        );

                        $('#jadwal_dokter_tujuan_id').append(newOption).trigger('change');
                        $('#hari_tujuan').val(data.data.jadwal_dokter_tujuan.hari);
                    }
                    const el = document.querySelector("#modal-tambah-data");
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
                        url: '{{ route('storePindahJadwalJaga') }}',
                        data: $('#modal-tambah-data :input').serialize(),
                        type: 'post',
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
                        url: '{{ route('deletePindahJadwalJaga') }}',
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
                url: "{{ route('statusPindahJadwalJaga') }}",
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

        function generateHariAwal() {
            $.ajax({
                url: "{{ route('generateHariPindahJadwalJaga') }}",
                data: {
                    tanggal() {
                        return $('#tanggal_awal').val();
                    }
                },
                type: 'get',
                success: function(data) {
                    $('#hari_awal').val(data.data);
                    $('.hari-pembuat-dokter').html(data.data);
                    $('#jadwal_dokter_awal_id').val(null).trigger('change.select2');
                },
                error: function(data) {
                    generateHariAwal();
                }
            });
        }

        function generateHariTujuan() {
            $.ajax({
                url: "{{ route('generateHariPindahJadwalJaga') }}",
                data: {
                    tanggal() {
                        return $('#tanggal_tujuan').val();
                    }
                },
                type: 'get',
                success: function(data) {
                    $('#hari_tujuan').val(data.data);

                    $('.hari-ke-dokter').html(data.data);
                    $('#jadwal_dokter_tujuan_id').val(null).trigger('change.select2');
                },
                error: function(data) {
                    generateHariTujuan();
                }
            });
        }

        function formatRepoJadwalDokter(repo) {
            if (repo.loading) {
                return repo.text;
            }

            var markup;
            if (repo.jam_pertama != undefined) {
                var markup = $('<span value=' + repo.id + '>' + repo.hari + ' ' + repo.jam_pertama
                    .jam_awal + ':' + repo
                    .jam_pertama
                    .menit_awal + ' s/d ' + repo.jam_terakhir.jam_awal + ':' + repo.jam_terakhir.menit_awal + '</span>');
            }
            // scrolling can be used

            return markup;
        }

        function formatRepoJadwalDokterSelection(repo) {
            if (repo.jam_pertama != undefined) {
                return repo.hari + ' ' + repo.jam_pertama.jam_awal + ':' + repo.jam_pertama.menit_awal +
                    ' s/d ' + repo.jam_terakhir.jam_awal + ':' + repo.jam_terakhir.menit_awal;
            } else {
                return repo.text;
            }

        }
    </script>
@endsection
