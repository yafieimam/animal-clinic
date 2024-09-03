@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection
{{-- @section('content_filter')
    @include('../management_karyawan/karyawan/filter_karyawan')
@endsection --}}
@section('style')
    <style>
        .member-data {
            width: 300px;
            height: 100%;
        }

        .member-img {
            width: 300px;
        }

        .member-container {
            color: black;
            position: relative;
            color: black;
            font-family: 'Montserrat';
            font-weight: 700;
        }
    </style>
@endsection
@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
                <button class="btn btn-primary shadow-md mr-2" id="tambah-data"
                    onclick="refreshState('#modal-tambah-data')">Tambah Data</button>
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
        <div class="col-span-12 md:col-span-4 mb-3">
            <label for="branch_id_filter" class="form-label">Filter Branch</label>
            <select name="branch_id_filter" id="branch_id_filter" class="select2filter form-control w-full">
                <option value="">Semua Branch</option>
                @foreach (\App\Models\Branch::get() as $item)
                    <option value="{{ $item->id }}">{{ $item->kode }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-12 md:col-span-4">
            <label class="form-label block">&nbsp;</label>
            <button class="btn btn-primary shadow-md mr-2" onclick="filter()"><i
                    class="fas fa-search"></i>&nbsp;Search</button>
        </div>
        <div class="col-span-12 md:col-span-4">
            <form action="{{route('OwnerExport')}}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="branch_id" id="branch_id_export" >
                <button type="submit" class="btn btn-primary mr-2 float-right" >Export Excel</button>
            </form>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <table class="table mt-2 stripe hover" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Opsi</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Catatan</th>
                    <th>Branch</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Alamat Lengkap</th>
                    <th>Komunitas</th>
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
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 parent">
                        <label for="kode" class="form-label">Kode {{ dot() }}</label>
                        <input id="kode" name="kode" type="text" class="form-control uppercase required" readonly
                            placeholder="">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="name" class="form-label">Nama {{ dot() }}</label>
                        <input id="name" name="name" type="text" class="form-control required"
                            placeholder="Masukan Nama Owner">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 md:col-span-6 parent {{ Auth::user()->akses('global') ? '' : 'disabled' }}">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select name="branch_id" id="branch_id" class="select2 form-control required"
                            onchange="generateKode()">
                            <option value="">Pilih Branch</option>
                            @foreach (\App\Models\Branch::get() as $item)
                                <option value="{{ $item->id }}">{{ $item->lokasi }}</option>
                            @endforeach
                        </select>
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
                        <label for="telpon" class="form-label">No Telpon</label>
                        <div class="input-group parent">
                            {{-- <div class="input-group-text">+62</div> --}}
                            <input id="telpon" name="telpon" type="text" class="form-control required"
                                placeholder="Masukan No Telp">
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <label for="komunitas" class="form-label">Komunitas</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input id="komunitas" name="komunitas" type="text" class="form-control"
                                placeholder="Masukan Komunitas">
                        </div>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" type="text" class="form-control required"
                            placeholder="Masukan Alamat Lengkap"></textarea>
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
    <div id="modal-tambah-catatan" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto" id="title-modal-tambah-catatan"></h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 parent">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea id="catatan" name="catatan" type="text" class="form-control required"
                            placeholder="Masukan Catatan"></textarea>
                        <input type="hidden" id="id_catatan" name="id_catatan">
                        {{ csrf_field() }}
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary w-20" id="simpan-catatan" onclick="storeCatatan()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
    <!-- END: Delete Confirmation Modal -->
    <div id="modal-member" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Kartu Member</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 lg:col-span-6 parent flex justify-center">
                        <div class="member-container" id="id-depan">
                            <img alt="npwp" src="{{ asset('dist/images/1.png') }}" class="member-img">
                            <div class="absolute top-0 member-data flex justify-end flex-col"
                                style="padding-bottom: 0.8rem;">
                                <div class="w-full text-black" style="font-size:14px;padding-left:1rem">
                                    <span class="nama-member"></span>
                                </div>
                                <div class="w-full text-black" style="font-size:10px;padding-left:1rem">
                                    <span class="alamat-member"></span>
                                </div>
                                <div class="w-full h-8 py-2  parent-text text-black text-center"
                                    style="font-size:12px;padding-left:6rem">
                                    <span class="kode-member">AMORE-BKS-01042022-0001</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6 parent flex justify-center">
                        <div class="member-container" id="id-belakang">
                            <img alt="npwp" src="{{ asset('dist/images/2.png') }}" class="member-img">
                            <div class="absolute top-0 member-data flex justify-end flex-col"
                                style="padding-bottom:1.1rem">
                                <div class="w-full h-8 py-2 px-1 text-black text-center parent-text"
                                    style="padding-right:0.5rem;font-size:16px">
                                    <span class="telpon-cs"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary" id="simpan" onclick="downloadKartu()"><i
                            class="fa-solid fa-id-card mr-2"></i>Download</button>
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
        var kodeMember;
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
                        url: "{{ route('datatableOwner') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center',
                        orderable: false,
                    }, {
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'kode',
                        name: 'kode_index',
                        orderable: false,
                    }, {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                    }, {
                        data: 'catatan',
                        name: 'catatan',
                        orderable: false,
                    }, {
                        data: 'branch',
                        name: 'branch',
                        orderable: false,
                    }, {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                    }, {
                        data: 'telpon',
                        name: 'telpon',
                        orderable: false,
                    }, {
                        data: 'alamat',
                        orderable: false,
                    }, {
                        data: 'komunitas',
                        class: 'text-left',
                        orderable: false,
                    }, {
                        data: 'status',
                        class: 'text-center',
                        orderable: false,
                    }, {
                        data: 'created_by',
                        name: 'created_by',
                        orderable: false,
                    }, {
                        data: 'updated_by',
                        name: 'updated_by',
                        orderable: false,
                    } ]
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
            table.ajax.reload();
        }


        function generateKode() {
            if ($('#id').val() == '' && $('#branch_id').val() != '') {
                $.ajax({
                    url: "{{ route('generateKodeOwner') }}",
                    type: 'get',
                    data: {
                        branch_id() {
                            return $('#branch_id').val();
                        }
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
            refreshState('#modal-tambah-data');
            $.ajax({
                url: '{{ route('editOwner') }}',
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

        function tambahCatatan(id) {
            refreshState('#modal-tambah-catatan');
            $.ajax({
                url: '{{ route('editOwner') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    $('#title-modal-tambah-catatan').html('Tambah Catatan Owner ' + data.data.name);
                    $('#catatan').val(data.data.catatan);
                    $('#id_catatan').val(data.data.id);

                    $('#simpan-data').removeClass('hidden');
                    $('.parent').removeClass('disabled');

                    const el = document.querySelector("#modal-tambah-catatan");
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
            refreshState('#modal-tambah-data');
            $.ajax({
                url: '{{ route('editOwner') }}',
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

                    const el = document.querySelector("#modal-tambah-data");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    $('#modal-tambah-data .select2').trigger('change.select2');

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
                        url: '{{ route('storeOwner') }}',
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

        function storeCatatan() {
            var validation = 0;

            $('#modal-tambah-catatan .required').each(function() {
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
                    console.log($('#modal-tambah-catatan :input').serialize());
                    $.ajax({
                        url: '{{ route('storeCatatanOwner') }}',
                        data: $('#modal-tambah-catatan :input').serialize(),
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });
                                clear();
                                const el = document.querySelector("#modal-tambah-catatan");
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
                        url: '{{ route('deleteOwner') }}',
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
                url: "{{ route('statusOwner') }}",
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

        function lihatKartu(kode, telpon, nama, alamat) {
            kodeMember = kode;
            $('.kode-member').html(kode);
            $('.nama-member').html(nama);
            $('.alamat-member').html(alamat);
            $('.telpon-cs').html('Call Center : +62 ' + telpon);
            const el = document.querySelector("#modal-member");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
            $('#emailmember .select2').trigger('change.select2');
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


        function downloadKartu() {
            const nodeDepan = document.getElementById('id-depan');
            $('.parent-text').removeClass('py-2');
            html2canvas(nodeDepan, {
                scale: 5,
            }).then(canvas => {
                // document.body.appendChild(canvas)
                // var img    = canvas.toDataURL("image/png");
                // document.write('<img src="'+img+'"/>');
                var link = document.createElement('a');
                link.download = 'member_depan_' + kodeMember + '.png';
                link.href = canvas.toDataURL()
                link.click();
            });

            const nodeBelakang = document.getElementById('id-belakang');

            html2canvas(nodeBelakang, {
                scale: 2,
            }).then(canvas => {
                // document.body.appendChild(canvas)
                // var img    = canvas.toDataURL("image/png");
                // document.write('<img src="'+img+'"/>');
                var link = document.createElement('a');
                link.download = 'member_belakang_' + kodeMember + '.png';
                link.href = canvas.toDataURL()
                link.click();
            });
            $('.parent-text').addClass('py-2');
        }
    </script>
@endsection
