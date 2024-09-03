@extends('../layout/' . $layout)

@section('subhead')
    <title>Amore Animal Clinic</title>
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
        <div class="col-span-12 xl:col-span-3">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">Dokter Poli</h2>
            </div>
            <div id="list-dokter">
            </div>
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
        <div class="col-span-12 xl:col-span-12 grid grid-cols-12 gap-6">
            <div class="col-span-12">
                <h2 class="text-lg font-medium truncate mr-5">Daftar Antrean</h2>
            </div>
            <div class="box col-span-12" id="antrian">
                <div class="intro-y col-span-12 md:col-span-12 xl:col-span-12">
                    <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-center bg-info rounded-t-lg"
                        style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                        <a href="javascript:;" class="font-medium text-white w-full text-xl">MONITORING ANTREAN</a>
                        <i class="fa-solid fa-expand text-white" onclick="openFullscreen()"></i>
                    </div>
                    <br><br>
                    <div class="box col-span-6">
                        <div class="intro-y col-span-9 md:col-span-6 xl:col-span-4">
                            <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-center bg-primary rounded-t-lg"
                                style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                                <a href="javascript:;" class="font-medium text-white w-full text-xl">EMERGENCY</a>
                            </div>
                            <div class="p-5">
                                <div class="h-40 2xl:h-56 flex items-center justify-center">
                                    <h5 class="font-bold text-5xl text-primary" id="antrian-sekarang">Loading...</h5>
                                </div>
                            </div>
                        </div>
                    </div><br>
                    <div class="box col-span-12 md:col-span-6">
                        <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                            <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-center bg-warning rounded-t-lg"
                                style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                                <a href="javascript:;" class="font-medium text-white w-full text-xl">PERIKSA</a>
                            </div>
                            <div class="w-full overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="border-2 px-3 py-2">No Antrean</th>
                                            <th class="border-2 px-3 py-2">Request</th>
                                        </tr>
                                    </thead>
                                    <tbody id="periksa">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><br>
                    <div class="col-span-12 md:col-span-6 grid grid-cols-12 gap-6">
                        <div class="box col-span-12">
                            <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                                <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-center bg-success rounded-t-lg"
                                    style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                                    <a href="javascript:;" class="font-medium text-white w-full text-xl">STERIL</a>
                                </div>
                                <div class="w-full overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="border-2 px-3 py-2">No Antrean</th>
                                                <th class="border-2 px-3 py-2">Request</th>
                                            </tr>
                                        </thead>
                                        <tbody id="steril">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="box col-span-12">
                            <div class="intro-y col-span-12">
                                <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-center bg-secondary rounded-t-lg"
                                    style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(75, 50, 216)">
                                    <a href="javascript:;" class="font-medium text-white w-full text-xl">GROOMING</a>
                                </div>
                                <div class="w-full">
                                    <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="border-2 px-3 py-2">No Antrean</th>
                                                <th class="border-2 px-3 py-2">Request</th>
                                            </tr>
                                        </thead>
                                        <tbody id="grooming">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>








    <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
        <table class="table mt-2 stripe hover" id="table" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
            <thead align="center">
                <th>No</th>
                <th>Opsi</th>
                <th>Kode Antrean</th>
                <th>Poli</th>
                <th>Nama Pasien</th>
                <th>Nama Owner</th>
                <th>Status Owner</th>
            </thead>

            <tbody>

            </tbody>
        </table>
    </div>
    <!-- END: Form Layout -->
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
                        url: "{{ route('datatableMonitoringAntrian') }}",
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
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, {
                        data: 'kode_pendaftaran',
                        name: 'kode_pendaftaran',
                        class: 'text-center'
                    }, {
                        data: 'poli',
                        name: 'poli',
                        class: 'text-center'
                    }, {
                        data: 'pasien',
                        name: 'pasien',
                        class: 'text-center'
                    }, {
                        data: 'owner',
                        name: 'owner',
                        class: 'text-center'
                    }, {
                        data: 'status_owner',
                        name: 'status_owner',
                        class: 'text-center'
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


            getAntrianPeriksa();
            getAntrianGrooming();
            getAntrianSteril();
            getDokter();
        })()

        // channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
        //     console.log(data);
        // });
        // console.log(tes);

        channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
            switch (data.data.poli_id) {
                case 5:
                    getAntrianGrooming();
                    table.ajax.reload()
                    break;
                case 6:
                    getAntrianPeriksa();
                    table.ajax.reload()
                    break;
                case 7:
                    getAntrianSteril();
                    table.ajax.reload()
                    break;
                case 8:
                    getDokter();
                    table.ajax.reload()
                    break;
                default:
                    break;
            }
        });

        function getAntrianPeriksa() {
            $.ajax({
                url: "{{ route('getAntrianMonitoringAntrian') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id').val();
                    },
                    poli_id: 'Periksa',
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
                    poli_id: 'Steril',
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
                    poli_id: 'Grooming',
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
                url: "{{ route('getDokterMonitoringAntrian') }}",
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
                    var html = '';
                    data.data.forEach((d, i) => {
                        var status =
                            '<div' +
                            ' class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">' +
                            'Tersedia</div>';
                        if (d.pengganti != null) {
                            if (d.pengganti.pendaftaran.length != 0) {
                                var status =
                                    '<div' +
                                    ' class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">' +
                                    'Sibuk</div>';
                            }
                            html +=
                                '<div class="intro-y">' +
                                '<div class="box px-4 py-4 mb-3 flex items-center zoom-in">' +
                                '<div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">' +
                                '<img alt="Amore Animal Clinic"' +
                                'src="{{ route('dashboard') }}' + '/' + d.pengganti.image + '">' +
                                '</div>' +
                                '<div class="ml-4 mr-auto">' +
                                '<div class="font-medium">' + d.pengganti.name + ' </div>' +
                                '<div class="text-slate-500 text-xs mt-0.5">Poli ' + d.jadwal_dokter
                                .poli.name + '</div>' +
                                '<div class="text-slate-500 text-xs mt-0.5">' +
                                '<span>' + d.jadwal_dokter.jam_pertama.jam_awal + ':' + d.jadwal_dokter
                                .jam_pertama.menit_awal + ' s/d ' + d.jadwal_dokter.jam_terakhir
                                .jam_awal +
                                ':' + d.jadwal_dokter
                                .jam_terakhir.menit_awal + ' </span>' +
                                '</div>' +
                                '</div>' +
                                status +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        } else {
                            if (d.data_dokter.pendaftaran.length != 0) {
                                var status =
                                    '<div' +
                                    ' class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">' +
                                    'Sibuk</div>';
                            }

                            html +=
                                '<div class="intro-y">' +
                                '<div class="box px-4 py-4 mb-3 flex items-center zoom-in">' +
                                '<div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">' +
                                '<img alt="Amore Animal Clinic"' +
                                'src="{{ route('dashboard') }}' + '/' + d.data_dokter.image + '">' +
                                '</div>' +
                                '<div class="ml-4 mr-auto">' +
                                '<div class="font-medium">' + d.data_dokter.name + ' </div>' +
                                '<div class="text-slate-500 text-xs mt-0.5">Poli ' + d.jadwal_dokter
                                .poli.name + '</div>' +
                                '<div class="text-slate-500 text-xs mt-0.5">' +
                                '<span>' + d.jadwal_dokter.jam_pertama.jam_awal + ':' + d.jadwal_dokter
                                .jam_pertama.menit_awal + ' s/d ' + d.jadwal_dokter.jam_terakhir
                                .jam_awal +
                                ':' + d.jadwal_dokter
                                .jam_terakhir.menit_awal + ' </span>' +
                                '</div>' +
                                '</div>' +
                                status +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        }

                    });

                    if (data.data.length == 0) {
                        html =
                            '<a href="javascript:;" class="intro-x w-full block text-center rounded-md py-3 border border-dotted border-slate-400 dark:border-darkmode-300 text-slate-500">Tidak ada Dokter</a>';
                    }

                    $("#list-dokter").html(html);
                    if (data.antrian == null) {
                        $('#antrian-sekarang').html(0);
                    } else {
                        $('#antrian-sekarang').html(data.antrian.kode_pendaftaran);
                    }
                    $('#sisa-antrian').html(data.sisa);
                    $('#total-antrian').html(data.total);
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

        function edit(id) {
            window.open('{{ route('editPendaftaran') }}?id=' + id);
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

        function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}

window.onload = timedRefresh(5000);
    </script>
@endsection
