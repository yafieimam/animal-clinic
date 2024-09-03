@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('../management_karyawan/jadwal_dokter/filter_jadwal_dokter')
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
                    <th>No</th>
                    <th>Opsi</th>
                    <th>Branch</th>
                    <th>Poli</th>
                    <th>Hari</th>
                    <th>Waktu Jaga</th>
                    <th>Nama Dokter Jaga</th>
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
                    <div class="col-span-12 md:col-span-6  parent">
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
                    <div class="col-span-12 md:col-span-6  parent">
                        <label class="form-label">Poli {{ dot() }}</label>
                        <select name="poli_id" id="poli_id" class="select2 form-control required">
                            <option value="">Pilih Poli</option>
                            @foreach (\App\Models\Poli::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12  mb-3 parent">
                        <label for="hari" class="form-label">Hari {{ dot() }}</label>
                        <select name="hari" id="hari" class="select2 form-control w-full required">
                            <option value="">Pilih Hari</option>
                            @foreach (hari() as $item)
                                <option value="{{ $item }}">
                                    {{ ucwords($item) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 mb-3 parent">
                        <label for="jam_pertama_id" class="form-label">Jam Ke {{ dot() }}</label>
                        <select name="jam_pertama_id" id="jam_pertama_id" class="select2 form-control w-full required">
                            <option value="">Pilih Jam</option>
                            @foreach (\App\Models\JamKerja::all() as $i => $d)
                                <option data-sequence="{{ $d->sequence }}" value="{{ $d->id }}">
                                    {{ $d->jam_awal }} : {{ $d->menit_awal }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-6 mb-3 parent">
                        <label for="jam_terakhir_id" class="form-label">Sampai Ke {{ dot() }}</label>
                        <select class="form-control select2 required" id="jam_terakhir_id" name="jam_terakhir_id">
                        </select>
                    </div>
                    <div class="col-span-12 text-right">
                        <hr class="mb-3">
                        <button type="button" class="btn btn-primary button-data simpan" onclick="addDokterJaga()"><i
                                class="fa fa-user-circle mr-2"></i> Tambah dokter
                            jaga</button>
                    </div>
                    <div class="col-span-12">
                        <div class="grid grid-cols-12 gap-4 gap-y-3" id="appendDokter">

                        </div>
                    </div>
                </form>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary w-20 simpan" id="simpan"
                        onclick="store()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>

    <div id="modal-dokter" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">List Dokter</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <table class="table mt-2 stripe hover" id="tableData" style="width: 100%">
                            <thead>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Opsi</th>
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
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="processDokterJaga()">Tambahkan</button>
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
        var tableData;
        var selectedDokter = [0];
        var jam = JSON.parse('{!! json_encode(\App\Models\JamKerja::all()) !!}');
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
                        url: "{{ route('datatableDataJadwalDokter') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                            poli_id() {
                                return $('#poli_id_filter').val();
                            },
                            hari() {
                                return $('#hari_filter').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center',
                    }, {
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, {
                        data: 'branch',
                        name: 'branch',
                        class: 'text-center',
                    }, {
                        data: 'poli',
                        name: 'poli',
                        class: 'text-center',
                    }, {
                        data: 'hari',
                        name: 'hari',
                        class: 'text-center',
                    }, {
                        data: 'waktuJaga',
                        name: 'waktuJaga',
                        class: 'text-center',
                    }, {
                        data: 'dokter',
                        name: 'dokter',
                        class: 'text-center',
                    }, {
                        data: 'status',
                        name: 'status',
                        class: 'text-center',
                        width: '20%',
                    }]
                })
                .columns.adjust()
                .responsive.recalc();

            tableData = $('#tableData').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: "{{ route('datatableAddDokterJadwalDokter') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        selectedDokter: function() {
                            return selectedDokter
                        },
                        branch_id() {
                            return $('#branch_id').val()
                        },
                        poli_id: function() {
                            return $('#poli_id').val()
                        },
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    class: 'text-center'
                }, {
                    data: 'image',
                    name: 'image',
                    class: 'text-center'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'email',
                    name: 'email'
                }, {
                    data: 'role',
                    name: 'role'
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    class: 'text-center',
                }]
            });

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

            $('#tambah-data').click(function() {
                clear();
                $('.not-editable').removeClass('disabled');
                $('.not-editable').prop('readonly', false);
                $('.parent').removeClass('disabled');
                $('#simpan').removeClass('hidden');
                $('.simpan').removeClass('hidden');
                selectedDokter = [0];
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                $('#appendDokter').html('');
                modal.toggle();
            })

            tomGenerator('.tomSelect');
        })()

        $('#jam_pertama_id').change(function() {
            var html = '';
            var validate = 0;
            html += `<option value="">Pilih Jam Terakhir</option>`;
            jam.forEach((d, i) => {
                if (d.sequence >= $(this).find('option:selected').data('sequence')) {
                    html += `<option data-sequence="` + d.sequence + `" value="` + d.id + `">` + d
                        .jam_awal + ' : ' + d.menit_awal + `</option>`;
                    validate++;
                }
            })

            $('#jam_terakhir_id').html(html);
            if (validate != 0) {
                $('#jam_terakhir_id').prop('disabled', false);
            } else {
                $('#jam_terakhir_id').prop('disabled', true);
                $('#jam_terakhir_id').html(`<option value="">Tidak Ada Data</option>`);
            }
            $('#jam_terakhir_id').trigger('change.select2');
        });

        $('body').on('change', '.status_dokter', function() {
            var status = $(this).val();
            var jadwal = $(this).data('jadwal');
            var dokter = $(this).data('dokter');
            console.log(status, jadwal, dokter);

            gantiStatus(status, jadwal, dokter);
        });

        function addDokterJaga() {
            tableData.ajax.reload();
            const el = document.querySelector("#modal-dokter");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
        }

        function processDokterJaga() {
            var selectingDokter = [];
            tableData.$('.checkDokter').each(function() {
                if ($(this).is(':checked')) {
                    selectingDokter.push($(this).data('id'));
                }
            });

            if (selectingDokter.length != 0) {
                $.ajax({
                    url: '{{ route('addDokterJadwalDokter') }}',
                    data: {
                        selectingDokter: selectingDokter,
                    },
                    type: 'get',
                    success: function(data) {

                        data.data.forEach((d, i) => {
                            console.log(d);
                            selectedDokter.push(d.id);
                            // var html =
                            //     '<div class="col-span-6 md:col-span-3 mb-3 parentCard">' +
                            //     '<div class="box" >' +
                            //     '<img style="min-height: 100px;object-fit: cover" class="box-img-top" src="{{ url('/') }}' +
                            //     "/" +
                            //     d.image + '" alt="Card image cap">' +
                            //     '<div class="box-body">' +
                            //     '<h5 class="box-title">' + d.name +
                            //     '<input type="hidden" name="dokter[]" class="dokter" value="' + d.id +
                            //     '">' +
                            //     '</h5>' +
                            //     '<p class="box-text">' + d.role.name + '</p>' +
                            //     '<button type="button" class="btn btn-danger"  onclick="deleteDokter(this)"><i class="fa fa-trash"></i> Hapus</button>' +
                            //     '</div>' +
                            //     '</div>' +
                            //     '</div>';

                            var html =
                                '<div class="col-span-12 md:col-span-3 parentCard">' +
                                '<div class="box intro-y p-3">' +
                                '<img alt="Amore Animal Clinic" class="rounded-md w-full h-36 object-fill"' +
                                'src="{{ url('/') . '/' }}' + d.image + '"' +
                                'style="width: 100% !important;height: 150px !important;object-fit: cover !important">' +
                                '<h5 class="box-title text-xl font-extrabold mt-2">' +
                                d.name +
                                '<input type="hidden" name="dokter[]" class="dokter" value="' + d.id +
                                '">' +
                                '</h5>' +
                                '<p class="mb-2">' + d.role.name + '</p>' +
                                '<div class="w-full text-right">' +
                                '<button class="btn btn-primary shadow-md mr-2"  onclick="deleteDokter(this)"><i class="fas fa-trash mr-2"></i> Hapus</button>' +
                                '</div>' +
                                '</div>' +
                                '</div>';

                            $('#appendDokter').append(html);
                        });

                        const el = document.querySelector("#modal-dokter");
                        const modal = tailwind.Modal.getOrCreateInstance(el);
                        modal.toggle();
                    },
                    error: function(data) {
                        processDokterJaga();
                    }
                });
            }

        }

        function deleteDokter(child) {
            var par = $(child).parents('.parentCard');
            var id = $(par).find('.dokter').val();
            console.log(id);
            console.log(par);
            var index = selectedDokter.indexOf(id * 1);
            selectedDokter.splice(index, 1);
            $(par).remove();
        }

        function openFilter() {
            slideOver.toggle();
        }

        function filter() {
            print
            slideOver.toggle();
            table.ajax.reload();
        }

        function edit(id) {
            $('.parentCard').remove();
            $.ajax({
                url: '{{ route('editJadwalDokter') }}',
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
                            $('#' + key).val(temp_value[key]);
                        }
                    }
                    var url = "{{ url('/') }}" + '/' + data.data.image;
                    var imagenUrl = url;
                    var drEvent = $('.dropify').dropify({
                        defaultFile: imagenUrl,
                    });

                    $('#jam_pertama_id').change();
                    $('#jam_terakhir_id').val(data.data.jam_terakhir_id);

                    var selectingDokter = [];

                    data.data.jadwal_dokter_detail.forEach((d, i) => {
                        selectingDokter.push(d.dokter);
                    });

                    if (selectingDokter.length != 0) {
                        $.ajax({
                            url: '{{ route('addDokterJadwalDokter') }}',
                            data: {
                                selectingDokter: selectingDokter,
                            },
                            type: 'get',
                            success: function(data) {
                                data.data.forEach((d, i) => {
                                    console.log(d);
                                    selectedDokter.push(d.id);
                                    var html =
                                        '<div class="col-span-12 md:col-span-3 parentCard">' +
                                        '<div class="box intro-y p-3">' +
                                        '<img alt="Amore Animal Clinic" class="rounded-md w-full h-36 object-fill"' +
                                        'src="{{ url('/') . '/' }}' + d.image + '"' +
                                        'style="width: 100% !important;height: 150px !important;object-fit: cover !important">' +
                                        '<h5 class="box-title text-xl font-extrabold mt-2">' +
                                        d.name +
                                        '<input type="hidden" name="dokter[]" class="dokter" value="' +
                                        d.id +
                                        '">' +
                                        '</h5>' +
                                        '<p class="mb-2">' + d.role.name + '</p>' +
                                        '<div class="w-full text-right">' +
                                        '<button class="btn btn-primary shadow-md mr-2"  onclick="deleteDokter(this)"><i class="fas fa-trash mr-2"></i> Hapus</button>' +
                                        '</div>' +
                                        '</div>' +
                                        '</div>';

                                    $('#appendDokter').append(html);
                                });
                            },
                            error: function(data) {
                                ToastNotification('warning', 'Terjadi kesahalan server');
                            }
                        });
                    }
                    $('#branch_id').val(data.data.branch_id);
                    $('#poli_id').val(data.data.poli_id);
                    $('#hari').val(data.data.hari);

                    $('.not-editable').prop('readonly', true);
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    $('#simpan').removeClass('hidden');
                    $('.simpan').removeClass('hidden');
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
            $('.parentCard').remove();
            $.ajax({
                url: '{{ route('editJadwalDokter') }}',
                data: {
                    id,
                    param: 'lihat',
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    $('.parent').addClass('disabled');
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        if ($('#' + key).length != 0) {
                            $('#' + key).val(temp_value[key]);
                        }
                    }

                    var url = "{{ url('/') }}" + '/' + data.data.image;
                    var imagenUrl = url;
                    var drEvent = $('.dropify').dropify({
                        defaultFile: imagenUrl,
                    });

                    $('#jam_pertama_id').change();
                    $('#jam_terakhir_id').val(data.data.jam_terakhir_id);

                    var selectingDokter = [];

                    data.data.jadwal_dokter_detail.forEach((d, i) => {
                        selectingDokter.push(d.dokter);
                    });

                    if (selectingDokter.length != 0) {
                        $.ajax({
                            url: '{{ route('addDokterJadwalDokter') }}',
                            data: {
                                selectingDokter: selectingDokter,
                            },
                            type: 'get',
                            success: function(data) {

                                data.data.forEach((d, i) => {
                                    console.log(d);
                                    selectedDokter.push(d.id);
                                    var html =
                                        '<div class="col-span-12 md:col-span-3 parentCard">' +
                                        '<div class="box intro-y p-3">' +
                                        '<img alt="Amore Animal Clinic" class="rounded-md w-full h-36 object-fill"' +
                                        'src="{{ url('/') . '/' }}' + d.image + '"' +
                                        'style="width: 100% !important;height: 150px !important;object-fit: cover !important">' +
                                        '<h5 class="box-title text-xl font-extrabold mt-2">' +
                                        d.name +
                                        '<input type="hidden" name="dokter[]" class="dokter" value="' +
                                        d.id +
                                        '">' +
                                        '</h5>' +
                                        '<p class="mb-2">' + d.role.name + '</p>' +
                                        '</div>' +
                                        '</div>';

                                    $('#appendDokter').append(html);
                                });
                            },
                            error: function(data) {
                                ToastNotification('warning', 'Terjadi kesahalan server');
                            }
                        });
                    }

                    $('#branch_id').val(data.data.branch_id);
                    $('#poli_id').val(data.data.poli_id);
                    $('#hari').val(data.data.hari);

                    const el = document.querySelector("#modal-tambah-data");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                    $('#simpan').addClass('hidden');
                    $('.simpan').addClass('hidden');
                    $('#modal-tambah-data .select2').trigger('change.select2');
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
                        url: '{{ route('storeJadwalDokter') }}',
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
                                $('.parentCard').remove();
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
                        url: '{{ route('deleteJadwalDokter') }}',
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

        function gantiStatus(status, jadwal, dokter) {
            $.ajax({
                url: "{{ route('statusJadwalDokter') }}",
                data: {
                    status,
                    jadwal,
                    dokter
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
    </script>
@endsection
