@extends('../layout/' . $layout)

@section('subhead')
    <title>CRUD Form - Rubick - Tailwind HTML Admin Template</title>
@endsection

@section('style')
    <style>
        .tab-content .tab-pane {
            position: relative !important;
            top: 0 !important;
            left: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }


        .btn-partai {
            width: 30px;
            height: 30px;
            transition: all 0.3s ease;
            display: inline-block;
            cursor: pointer;
        }

        .btn-partai:hover {
            width: 100px;
        }

        .btn-partai span {
            transition: all 0.5s ease;
            opacity: 0;
            width: 0px;
        }

        .btn-partai:hover span {
            opacity: 1;
            width: 50px;
        }

        .h-fullscreen {
            height: 80vh !important;
        }
    </style>
@endsection

@section('subcontent')
    <div class="intro-y md:flex md:justify-between items-center mt-8">
        <span class="text-lg font-medium">{{ convertSlug($global['title']) }}</span>
        <div class="parent {{ Auth::user()->akses('global') ? '' : 'hidden' }}">
            <label for="name" class="form-label">Branch{{ dot() }}</label>
            <select name="branch_id" id="branch_id" class="select2 form-control required">
                <option value="">Pilih Branch</option>
                @foreach (\App\Models\Branch::get() as $item)
                    <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                        {{ $item->kode }} - {{ $item->alamat }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-6">
        <!-- BEGIN: Form Layout -->
        {{-- <div class="col-span-12 xl:col-span-3">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">List Dokter Jaga</h2>
            </div>
            <div id="list-dokter">
            </div>
        </div> --}}
        <div class="col-span-12 grid grid-cols-12 gap-6">
            <div class="col-span-12">
                <h2 class="text-lg font-medium truncate mr-5">Daftar Antrean</h2>
            </div>
            <div class="box col-span-12" id="antrian">
                <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                    <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-center bg-primary rounded-t-lg"
                        style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                        <span id="timer" class="font-medium text-white text-xl" hidden style="background:moccasin"></span>
                        <a href="javascript:;" class="font-medium text-white w-full text-xl">MONITORING ANTREAN OBAT</a>
                        <i class="fa-solid fa-expand text-white" onclick="openFullscreen()"></i>
                    </div>
                    <div class="px-4 py-3 grid grid-cols-2 gap-4" id="refresh-otomatis">
                        <div class="border-2">
                            <div class="py-4 border-b border-slate-200/60 dark:border-darkmode-400 text-center bg-primary rounded-t-lg">
                                <div class="text-white w-full text-lg">Sedang Proses</div>
                            </div>
                            <div class="2xl:h-56 flex items-center justify-center h-fullscreen" id="nomor-antrian">
                                <h5 class="font-bold text-5xl text-primary" id="antrian-sekarang">Tidak Ada Antrian</h5>
                            </div>
                        </div>
                        <div class="border-2">
                            <div class="py-4 border-b border-slate-200/60 dark:border-darkmode-400 text-center bg-primary rounded-t-lg">
                                <div class="text-white w-full text-lg">Obat Dapat Diambil</div>
                            </div>
                            <div class="2xl:h-56 flex items-center justify-center h-fullscreen" id="nomor-antrian">
                                <h5 class="font-bold text-5xl text-primary" id="antrian-success">Tidak Ada Antrian</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <table class="table mt-2 stripe hover" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Kode Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Nama Owner</th>
                    <th>Jenis Hewan</th>
                    <th>Obat</th>
                </thead>

                <tbody>

                </tbody>
            </table>
        </div>
        <!-- END: Form Layout -->
    </div>
    </div>
@endsection

@section('script')
    <script>
        var table;
        var xhr = [];
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
                        url: "{{ route('datatableMonitoringAntrianObat') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center'
                    }, {
                        data: 'kode_pendaftaran',
                        name: 'kode_pendaftaran'
                    }, {
                        data: 'nama_pasien',
                        name: 'nama_pasien'
                    }, {
                        data: 'nama_owner',
                        name: 'nama_owner',
                    }, {
                        data: 'jenis_hewan',
                        name: 'jenis_hewan',
                    }, {
                        data: 'obat',
                        name: 'obat',
                    }, ]
                })
                .columns.adjust()
                .responsive.recalc();

            $('.select2').select2({
                width: '100%',
            })
            // setInterval(() => {
            //     getAntrianPeriksa();
            // }, 1000);


            getDokter();
        })()

        channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
            getDokter();
            table.ajax.reload()
        });

        function getAntrianPeriksa() {
            $.ajax({
                url: "{{ route('getAntrianMonitoringAntrian') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id').val();
                    },
                    poli_id: 6,
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {

                    $("#periksa").html(data);

                },
                error: function(data) {}
            });
        }

        function getAntrianSteril() {
            $.ajax({
                url: "{{ route('getAntrianMonitoringAntrian') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id').val();
                    },
                    poli_id: 7,
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {

                    $("#steril").html(data);

                },
                error: function(data) {}
            });
        }

        function getAntrianGrooming() {
            $.ajax({
                url: "{{ route('getAntrianMonitoringAntrian') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id').val();
                    },
                    poli_id: 5,
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    $("#grooming").html(data);
                },
                error: function(data) {}
            });
        }

        function getDokter() {
            $.ajax({
                url: "{{ route('getPasienMonitoringAntrianObat') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id').val();
                    }
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    if (data.antrian) {
                        $('#antrian-sekarang').html(data.antrian.pendaftaran.kode_pendaftaran);
                    } else {
                        $('#antrian-sekarang').html('Tidak ada antrian');
                    }

                    // $('#sisa-antrian').html(data.sisa);
                    // $('#total-antrian').html(data.total);
                },
                error: function(data) {
                    getDokter();
                }
            });
        }

        function hapus(id) {
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
                        url: '{{ route('deleteMonitoringAntrian') }}',
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
                                getAntrianGrooming()
                                getAntrianPeriksa()
                                getAntrianSteril()
                                getDokter()
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

        document.addEventListener("fullscreenChange", function() {
            if (fullscreenElement != null) {
                console.info("Went full screen");
            } else {
                console.info("Exited full screen");
            }
        });

        function openFullscreen() {
            var elem = document.getElementById("antrian");
            if (elem.requestFullscreen) {
                $('#nomor-antrian').addClass('h-fullscreen')
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE11 */
                elem.msRequestFullscreen();
            }
        }
    </script>
@endsection
