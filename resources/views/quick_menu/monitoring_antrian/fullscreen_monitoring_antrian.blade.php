@extends('../layout/main_fullscreen')

{{-- @section('subhead')
    <title>Amore Animal Clinic</title>
@endsection --}}

@section('head')
    <title>
        Amore Animal Clinic
    </title>
@endsection

@section('style')
    <style>
        body {
            padding: 0 !important;
        }

        .alert {
            border-radius: 0
        }

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

        .justify-around {
            justify-content: space-around
        }
    </style>
@endsection

@section('content')
    {{-- <div class="top-bar bg-primary mb-4">
        <!-- BEGIN: Breadcrumb -->
        <a href="javascript:;" class="intro-x flex items-center pl-5 opacity-100">
            <img alt="Amore Animal Clinic" style="height: 50px" src="{{ asset('dist/images/amore.png') }}">
        </a>
        <!-- END: Breadcrumb -->
    </div> --}}
    <div class="grid grid-cols-12 gap-6 px-4 py-4">
        <!-- BEGIN: Form Layout -->
        <input type="hidden" id="branch_id" class="branch_id" value="{{ $req->branch_id }}">
        <div class="col-span-12 xl:col-span-3">
            {{-- <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">Dokter Poli</h2>
            </div> --}}
            <div id="list-dokter">
            </div>
        </div>
        <div class="col-span-12 xl:col-span-9 grid grid-cols-12 gap-6 ">
            <div class="box col-span-12 grid grid-cols-12 gap-6" style="background: rgb(232, 232, 232)">
                <div class="col-span-3 bg-white rounded-t-lg">
                    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-2 text-center bg-primary rounded-t-lg"
                            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                            <a href="javascript:;" class="font-medium text-white w-full text-xl">EMERGENCY</a>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-center">
                                <h5 class="font-bold text-xl text-primary" id="sedang-periksa-emergency">PJT-001</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-9 bg-white rounded-t-lg">
                    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-2 text-center bg-primary rounded-t-lg"
                            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                            <a href="javascript:;" class="font-medium text-white w-full text-xl">ANTRIAN SELANJUTNYA</a>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-around" id="antrian-sekarang-emergency">
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box col-span-12 grid grid-cols-12 gap-6" style="background: rgb(232, 232, 232)">
                <div class="col-span-3 bg-white rounded-t-lg">
                    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-2 text-center bg-warning rounded-t-lg"
                            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                            <a href="javascript:;" class="font-medium text-white w-full text-xl">PERIKSA</a>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-center">
                                <h5 class="font-bold text-xl text-primary" id="sedang-periksa-periksa">PJT-001</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-9 bg-white rounded-t-lg">
                    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-2 text-center bg-warning rounded-t-lg"
                            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                            <a href="javascript:;" class="font-medium text-white w-full text-xl">ANTRIAN SELANJUTNYA</a>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-around" id="antrian-sekarang-periksa">
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box col-span-12 grid grid-cols-12 gap-6" style="background: rgb(232, 232, 232)">
                <div class="col-span-3 bg-white rounded-t-lg">
                    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-2 text-center bg-success rounded-t-lg"
                            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                            <a href="javascript:;" class="font-medium text-white w-full text-xl">STERIL</a>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-center">
                                <h5 class="font-bold text-xl text-primary" id="sedang-periksa-steril">PJT-001</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-9 bg-white rounded-t-lg">
                    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-2 text-center bg-success rounded-t-lg"
                            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                            <a href="javascript:;" class="font-medium text-white w-full text-xl">ANTRIAN SELANJUTNYA</a>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-around" id="antrian-sekarang-steril">
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box col-span-12 grid grid-cols-12 gap-6" style="background: rgb(232, 232, 232)">
                <div class="col-span-3 bg-white rounded-t-lg">
                    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-2 text-center bg-secondary rounded-t-lg"
                            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(75, 50, 216)">
                            <a href="javascript:;" class="font-medium text-white w-full text-xl">GROOMING</a>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-center">
                                <h5 class="font-bold text-xl text-primary" id="sedang-periksa-grooming">PJT-001</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-9 bg-white rounded-t-lg">
                    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
                        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-2 text-center bg-secondary rounded-t-lg"
                            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(75, 50, 216)">
                            <a href="javascript:;" class="font-medium text-white w-full text-xl">ANTRIAN SELANJUTNYA</a>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-around" id="antrian-sekarang-grooming">
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5> |
                                <h5 class="font-bold text-xl text-primary">PJT-001</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END: Form Layout -->
    </div>
    {{-- <div class="w-full bg-primary h-8" style="position: absolute;bottom: 50px">
        @if (\App\Models\PengumumanKaryawan::where('status', true)->count() != 0)
            <div class="alert alert-dark show my-2 overflow-hidden flex items-center">
                <div class='marquee w-full overflow-hidden font-bold text-md' data-duration='10000' data-gap='10'
                    data-duplicated='true'>
                    <span class="mr-6">AMORE ANIMAL CLINIC, untuk informasi tentang kami hubungi call center kami di +62 85892578936</span><span class="mr-6"></span>
                </div>
                <div class="flex ml-4 ">
                    <div class="text-md  text-center flex flex-col justify-center">
                        {{ CarbonParseISO(now(), 'dddd') }}
                        {{ CarbonParseISO(now(), 'l') }}
                    </div>
                    <div class="text-xl flex flex-col justify-center">|</div>
                    <div class="digital-clock text-xl flex flex-col justify-center ml-2">00:00</div>
                </div>
            </div>
        @endif
    </div> --}}
@endsection

@section('script')
    <script>
        var table;
        var xhr = [];
        (function() {
            // $('.marquee').marquee({
            //     //duration in milliseconds of the marquee
            //     duration: 15000,
            //     //gap in pixels between the tickers
            //     gap: 50,
            //     //time in milliseconds before the marquee will start animating
            //     delayBeforeStart: 0,
            //     //'left' or 'right'
            //     direction: 'left',
            //     //true or false - should the marquee be duplicated to show an effect of continues flow
            //     duplicated: true
            // });
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

            clockUpdate();
            setInterval(clockUpdate, 1000);
            getAntrianPeriksa();
            getDokter();
        })()

        $(document).ready(function() {

        })

        function clockUpdate() {
            var date = new Date();
            $('.digital-clock').css({
                'color': '#fff',
            });

            function addZero(x) {
                if (x < 10) {
                    return x = '0' + x;
                } else {
                    return x;
                }
            }

            function twelveHour(x) {
                if (x > 12) {
                    return x = x - 12;
                } else if (x == 0) {
                    return x = 12;
                } else {
                    return x;
                }
            }

            var h = addZero(twelveHour(date.getHours()));
            var m = addZero(date.getMinutes());
            var s = addZero(date.getSeconds());

            $('.digital-clock').text(h + ':' + m)
        }
        // channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
        //     console.log(data);
        // });
        // console.log(tes);

        channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
            getAntrianPeriksa();
            getDokter();
        });

        function getAntrianPeriksa() {
            $.ajax({
                url: "{{ route('getAntrianFullscreenMonitoringAntrian') }}",
                type: 'get',
                data: {
                    branch_id: '{{ $req->branch_id }}',
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    $('#sedang-periksa-emergency').html(data.emergency_periksa ? data.emergency_periksa
                        .kode_pendaftaran : 'Tidak ada Pasien')
                    $('#sedang-periksa-periksa').html(data.periksa_periksa ? data.periksa_periksa
                        .kode_pendaftaran : 'Tidak ada Pasien')
                    $('#sedang-periksa-steril').html(data.steril_periksa ? data.steril_periksa
                        .kode_pendaftaran : 'Tidak ada Pasien')
                    $('#sedang-periksa-grooming').html(data.grooming_periksa ? data.grooming_periksa
                        .kode_pendaftaran : 'Tidak ada Pasien')

                    var html = '';

                    data.emergency.forEach((d, i) => {

                        if (i != data.emergency.length - 1) {
                            console.log(i);
                            console.log(data.emergency.length - 1);
                            html += '<h5 class="font-bold text-xl text-primary">' + d
                                .kode_pendaftaran + '</h5> |';
                        } else {
                            html += '<h5 class="font-bold text-xl text-primary">' + d
                                .kode_pendaftaran + '</h5>';
                        }
                    });


                    if (data.emergency.length == 0) {
                        var html = '<h5 class="font-bold text-xl text-primary">Tidak ada Pasien</h5> ';
                    }


                    $('#antrian-sekarang-emergency').html(html);

                    var html = '';

                    data.periksa.forEach((d, i) => {
                        if (i != data.emergency.length - 1) {
                            html += '<h5 class="font-bold text-xl text-primary">' + d
                                .kode_pendaftaran + '</h5> |';
                        } else {
                            html += '<h5 class="font-bold text-xl text-primary">' + d
                                .kode_pendaftaran + '</h5>';
                        }
                    });

                    if (data.periksa.length == 0) {
                        var html = '<h5 class="font-bold text-xl text-primary">Tidak ada Pasien</h5> ';
                    }

                    $('#antrian-sekarang-periksa').html(html);

                    var html = '';

                    data.steril.forEach((d, i) => {
                        if (i != data.emergency.length - 1) {
                            html += '<h5 class="font-bold text-xl text-primary">' + d
                                .kode_pendaftaran + '</h5> |';
                        } else {
                            html += '<h5 class="font-bold text-xl text-primary">' + d
                                .kode_pendaftaran + '</h5>';
                        }
                    });

                    if (data.steril.length == 0) {
                        var html = '<h5 class="font-bold text-xl text-primary">Tidak ada Pasien</h5> ';
                    }

                    $('#antrian-sekarang-steril').html(html);

                    var html = '';

                    data.grooming.forEach((d, i) => {
                        if (i != data.emergency.length - 1) {
                            html += '<h5 class="font-bold text-xl text-primary">' + d
                                .kode_pendaftaran + '</h5> |';
                        } else {
                            html += '<h5 class="font-bold text-xl text-primary">' + d
                                .kode_pendaftaran + '</h5>';
                        }
                    });

                    if (data.grooming.length == 0) {
                        var html = '<h5 class="font-bold text-xl text-primary">Tidak ada Pasien</h5> ';
                    }

                    $('#antrian-sekarang-grooming').html(html);


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
                title: "Batalkan Antrean",
                text: "Klik Tombol Ya untuk membatalkan Antrean.",
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
                                getAntrianPeriksa()
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

        function openFullscreen(params) {
            window.open('{{ route('fullscreenMonitoringAntrian') }}');
        }
    </script>
@endsection
