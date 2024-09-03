@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('../management_karyawan/karyawan/filter_karyawan')
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
                                <a href="javascript:;" class="dropdown-item" onclick="bulkImportModal()">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Bulk Import
                                </a>
                            </li>
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
                    <th>Nama Lengkap</th>
                    <th>Nama Panggilan</th>
                    <th>Branch</th>
                    <th>Email</th>
                    <th>No HP</th>
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
                <form class="modal-body grid grid-cols-12 gap-4 gap-y-3" id="form-data">
                    <div class="col-span-12 parent">
                        <h3 class="font-bold text-3xl">Biodata</h3>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label>Foto KTP</label>
                        <input type="file" class="dropify text-sm" id="file_ktp" name="file_ktp"
                            data-allowed-file-extensions="jpeg png jpg">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="nik" class="form-label">NIK KTP</label>
                        <input id="nik" name="nik" type="text" class="form-control"
                            placeholder="Masukan NIK KTP" maxlength="16">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="name" class="form-label">Nama Lengkap {{ dot() }}</label>
                        <input id="name" name="name" type="text" class="form-control required"
                            placeholder="Masukan Nama Lengkap Karyawan">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="nama_panggilan" class="form-label">Nama Panggilan {{ dot() }}</label>
                        <input id="nama_panggilan" name="nama_panggilan" type="text" class="form-control required"
                            placeholder="Masukan Nama Panggilan">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <input id="tanggal_lahir" name="tanggal_lahir" type="text"
                                class="form-control datepicker" placeholder="yyyy-mm-dd"
                                placeholder="yyyy-mm-dd" data-single-mode="true">
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input id="tempat_lahir" name="tempat_lahir" type="text" class="form-control"
                            placeholder="Masukan Tempat Lahir">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="branch_id" class="form-label">Branch {{ dot() }}</label>
                        <select name="branch_id" id="branch_id" class="select2 form-control required">
                            <option value="">Pilih Branch</option>
                            @foreach (\App\Models\Branch::where('status', true)->get() as $item)
                                <option value="{{ $item->id }}">{{ $item->lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="divisi_id" class="form-label">Divisi {{ dot() }}</label>
                        <select name="divisi_id" id="divisi_id" class="select2 form-control required">
                            <option value="">Pilih Divisi</option>
                            @foreach (\App\Models\Divisi::where('status', true)->get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="bagian_id" class="form-label">Bagian {{ dot() }}</label>
                        <select name="bagian_id" id="bagian_id" class="select2 form-control required">
                            <option value="">Pilih Bagian</option>
                            @foreach (\App\Models\Bagian::where('status', true)->get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="jabatan_id" class="form-label">Jabatan {{ dot() }}</label>
                        <select name="jabatan_id" id="jabatan_id" class="select2 form-control required">
                            <option value="">Pilih Jabatan</option>
                            @foreach (\App\Models\Jabatan::where('status', true)->get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin {{ dot() }}</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="select2 form-control required">
                            <option value="">Pilih Jenis Kelamin</option>
                            @foreach (\App\Models\Karyawan::$enumJenisKelamin as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="status_pernikahan" class="form-label">Status Pernikahan</label>
                        <select name="status_pernikahan" id="status_pernikahan" class="select2 form-control">
                            <option value="">Pilih Status Pernikahan</option>
                            @foreach (\App\Models\Karyawan::$enumStatusPernikahan as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="jumlah_anak" class="form-label">Jumlah Anak</label>
                        <input id="jumlah_anak" name="jumlah_anak" type="number" class="form-control"
                            placeholder="Masukan Jumlah Anak">
                    </div>
                    <div class="col-span-12 parent">
                        <hr>
                        <h3 class="font-bold text-3xl my-2">Informasi Yang Dapat Dihubungi</h3>
                        <hr>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input id="email" name="email" type="text" class="form-control"
                                placeholder="Masukan Email">
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="telpon" class="form-label">No Telepon</label>
                        <div class="input-group parent">
                            {{-- <div class="input-group-text">+62</div> --}}
                            <input id="telpon" name="telpon" type="text" class="form-control"
                                placeholder="Masukan No Telp" maxlength="13">
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-3 parent">
                        <label for="province_id" class="form-label">Provinsi</label>
                        <select name="province_id" id="province_id" class="select2 form-control">
                            <option value="">Pilih Provinsi</option>
                            @foreach (\App\Models\Provinsi::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-3 parent">
                        <label for="city_id" class="form-label">Kota</label>
                        <select name="city_id" id="city_id" class="select2 form-control">
                            <option value="">Pilih Kota</option>
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-3 parent">
                        <label for="district_id" class="form-label">Kecamatan</label>
                        <select name="district_id" id="district_id" class="select2 form-control">
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-3 parent">
                        <label for="village_id" class="form-label">Kelurahan / Desa</label>
                        <select name="village_id" id="village_id" class="select2 form-control">
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="rt" class="form-label">RT</label>
                        <input type="number" id="rt" name="rt" type="text"
                            class="form-control" placeholder="Masukan RT">
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="rw" class="form-label">RW</label>
                        <input type="number" id="rw" name="rw" type="text"
                            class="form-control" placeholder="Masukan RW">
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="kode_pos" class="form-label">Kode Pos</label>
                        <input type="number" id="kode_pos" name="kode_pos" type="text"
                            class="form-control" placeholder="Masukan Kode Pos">
                    </div>
                    <div class="col-span-12 parent">
                        <hr>
                        <h3 class="font-bold text-3xl my-2">Data Perusahaan</h3>
                        <hr>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="tanggal_join" class="form-label">Tanggal Join</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <input id="tanggal_join" name="tanggal_join" type="text" class="form-control"
                                placeholder="yyyy-mm-dd" placeholder="yyyy-mm-dd" data-single-mode="true">
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="bpjs" class="form-label">NO BPJS</label>
                        <input id="bpjs" name="bpjs" type="text" class="form-control"
                            placeholder="Masukan NO BPJS">
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="npwp" class="form-label">NPWP</label>
                        <input id="npwp" name="npwp" type="text" class="form-control"
                            placeholder="Masukan NPWP">
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
                        url: "{{ route('datatableKaryawan') }}",
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
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'nama_panggilan',
                        name: 'nama_panggilan'
                    }, {
                        data: 'branch',
                        name: 'branch'
                    }, {
                        data: 'email',
                        name: 'email'
                    }, {
                        data: 'telpon',
                        name: 'telpon',
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

            $('#province_id').on('select2:select', function(event) {
                $('#city_id').val(null).trigger('change.select2');
            })

            $("#city_id").select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                width: '100%',
                ajax: {
                    url: "{{ route('select2Karyawan') }}?param=city_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            province_id() {
                                return $('#province_id').val();
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
                placeholder: 'Pilih Provinsi Dahulu',
                minimumInputLength: 0,
                templateResult: formatRepoNormal,
                templateSelection: formatRepoNormalSelection
            });

            $('#city_id').on('select2:select', function(event) {
                $('#district_id').val(null).trigger('change.select2');
            })

            $("#district_id").select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                width: '100%',
                ajax: {
                    url: "{{ route('select2Karyawan') }}?param=district_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            city_id() {
                                return $('#city_id').val();
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
                placeholder: 'Pilih Kota Dahulu',
                minimumInputLength: 0,
                templateResult: formatRepoNormal,
                templateSelection: formatRepoNormalSelection
            });

            $('#district_id').on('select2:select', function(event) {
                $('#village_id').val(null).trigger('change.select2');
            })

            $("#village_id").select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                width: '100%',
                ajax: {
                    url: "{{ route('select2Karyawan') }}?param=village_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            district_id() {
                                return $('#district_id').val();
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
                placeholder: 'Pilih Kecamatan Dahulu',
                minimumInputLength: 0,
                templateResult: formatRepoNormal,
                templateSelection: formatRepoNormalSelection
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
                $('.dropify-clear').click();
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();

            })

            tomGenerator('.tomSelect');
        })()

        function bulkImportModal() {
            const el = document.querySelector("#modal-import-bulk");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
            // $("#spreadsheet").html('');
        }

        $(function() {
            $("#selectExcel").change(handleFileSelect);
        });

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
                url: '{{ route('editKaryawan') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    refreshState('#modal-tambah-data')
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

                    if (data.data.provinsi != null) {
                        var newOption = new Option(data.data.provinsi.name,
                            data.data.provinsi.id,
                            true,
                            true
                        );

                        $('#province_id').append(newOption).trigger('change');
                    }

                    if (data.data.kota != null) {
                        var newOption = new Option(data.data.kota.name,
                            data.data.kota.id,
                            true,
                            true
                        );

                        $('#city_id').append(newOption).trigger('change');
                    }

                    if (data.data.kecamatan != null) {
                        var newOption = new Option(data.data.kecamatan.name,
                            data.data.kecamatan.id,
                            true,
                            true
                        );

                        $('#district_id').append(newOption).trigger('change');
                    }

                    if (data.data.kelurahan != null) {
                        var newOption = new Option(data.data.kelurahan.name,
                            data.data.kelurahan.id,
                            true,
                            true
                        );

                        $('#village_id').append(newOption).trigger('change');
                    }

                    var url = "{{ url('/') }}" + '/' + data.data.file_ktp;
                    var imagenUrl = url;
                    var drEvent = $('#file_ktp').dropify({
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
                url: '{{ route('editKaryawan') }}',
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
                    $('.parent').addClass('disabled');
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        $('#' + key).val(temp_value[key]);
                    }

                    if (data.data.provinsi != null) {
                        var newOption = new Option(data.data.provinsi.name,
                            data.data.provinsi.id,
                            true,
                            true
                        );

                        $('#province_id').append(newOption).trigger('change');
                    }

                    if (data.data.kota != null) {
                        var newOption = new Option(data.data.kota.name,
                            data.data.kota.id,
                            true,
                            true
                        );

                        $('#city_id').append(newOption).trigger('change');
                    }

                    if (data.data.kecamatan != null) {
                        var newOption = new Option(data.data.kecamatan.name,
                            data.data.kecamatan.id,
                            true,
                            true
                        );

                        $('#district_id').append(newOption).trigger('change');
                    }

                    if (data.data.kelurahan != null) {
                        var newOption = new Option(data.data.kelurahan.name,
                            data.data.kelurahan.id,
                            true,
                            true
                        );

                        $('#village_id').append(newOption).trigger('change');
                    }
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

            var input = document.getElementById("file_ktp");
            if (input != null) {
                file = input.files[0];
                formData.append("file_ktp", file);
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
                        url: '{{ route('storeKaryawan') }}',
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
                        url: '{{ route('deleteKaryawan') }}',
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
                                // html: data.responseJSON.message == undefined ? html : data
                                //     .responseJSON.message,
                                html: 'Silakan hubungi Admin !!!',
                                icon: "error",
                            });
                        }
                    });
                }
            })
        }

        function gantiStatus(param, id) {
            $.ajax({
                url: "{{ route('statusKaryawan') }}",
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

        $("#tanggal_join").each(function() {
            let options = {
                autoApply: false,
                singleMode: false,
                numberOfColumns: 2,
                numberOfMonths: 2,
                showWeekNumbers: true,
                postition: 'top',
                format: "YYYY-MM-DD",
                dropdowns: {
                    minYear: 1990,
                    maxYear: 2050,
                    months: true,
                    years: true,
                },
            };

            console.log($(this).data("single-mode"))
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
            });
        });

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
            window.open('{{ url('/') }}' + '/storage/template_karyawan.csv');
            window.open('{{ route('branchExcel') }}');
            window.open('{{ route('divisiExcel') }}');
            window.open('{{ route('bagianExcel') }}');
            window.open('{{ route('jabatanExcel') }}');
            window.open('{{ route('provinsiExcel') }}');
            window.open('{{ route('kotaExcel') }}');
            window.open('{{ route('kecamatanExcel') }}');
            window.open('{{ route('kelurahanExcel') }}');
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
                        url: '{{ route('bulkImportKaryawan') }}',
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
                                    text: data.message,
                                    title: 'Ada Kesalahan !!!',
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
