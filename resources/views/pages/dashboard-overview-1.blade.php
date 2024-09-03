@extends('../layout/' . $layout)

@section('subhead')
    <title>Dashboard - Rubick - Tailwind HTML Admin Template</title>
@endsection
@section('style')
    <style>
        .circle-progress {
            display: flex;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#991b1b var(--progress), gray 0deg);
            font-size: 0;
        }

        .circle-progress::after {
            content: attr(data-progress) '%';
            display: flex;
            justify-content: center;
            flex-direction: column;
            width: 100%;
            margin: 10px;
            border-radius: 50%;
            background: white;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
        }


        @keyframes turn_in {
            5% {
                background: conic-gradient(red calc(var(--progress) * .95), gray 0deg);
            }

            10% {
                background: conic-gradient(red calc(var(--progress) * .9), gray 0deg);
            }

            15% {
                background: conic-gradient(red calc(var(--progress) * .85), gray 0deg);
            }

            20% {
                background: conic-gradient(red calc(var(--progress) * .8), gray 0deg);
            }

            25% {
                background: conic-gradient(red calc(var(--progress) * .75), gray 0deg);
            }

            30% {
                background: conic-gradient(red calc(var(--progress) * .7), gray 0deg);
            }

            35% {
                background: conic-gradient(red calc(var(--progress) * .65), gray 0deg);
            }

            40% {
                background: conic-gradient(red calc(var(--progress) * .6), gray 0deg);
            }

            45% {
                background: conic-gradient(red calc(var(--progress) * .55), gray 0deg);
            }

            50% {
                background: conic-gradient(red calc(var(--progress) * .5), gray 0deg);
            }

            55% {
                background: conic-gradient(red calc(var(--progress) * .45), gray 0deg);
            }

            60% {
                background: conic-gradient(red calc(var(--progress) * .4), gray 0deg);
            }

            65% {
                background: conic-gradient(red calc(var(--progress) * .35), gray 0deg);
            }

            70% {
                background: conic-gradient(red calc(var(--progress) * 0.3), gray 0deg);
            }

            75% {
                background: conic-gradient(red calc(var(--progress) * 0.25), gray 0deg);
            }

            80% {
                background: conic-gradient(red calc(var(--progress) * .2), gray 0deg);
            }

            85% {
                background: conic-gradient(red calc(var(--progress) * .15), gray 0deg);
            }

            90% {
                background: conic-gradient(red calc(var(--progress) * .1), gray 0deg);
            }

            95% {
                background: conic-gradient(red calc(var(--progress) * .05), gray 0deg);
            }

            100% {
                background: conic-gradient(gray 0deg);
            }

        }

        .scrollbar::-webkit-scrollbar {
            width: 10px;
            height: 20px;
        }

        .scrollbar::-webkit-scrollbar-track {
            border-radius: 100vh;
            background: #f7f4ed;
        }

        .scrollbar::-webkit-scrollbar-thumb {
            background: #e0cbcb;
            border-radius: 100vh;
            border: 3px solid #f6f7ed;
        }

        .scrollbar::-webkit-scrollbar-thumb:hover {
            background: #c0a0b9;
        }

        .tombol {
            margin-bottom: 5px;
        }
    </style>
@endsection
@section('subcontent')
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: General Report -->
                @php
                    $pasienHariIni = pasienHariIni();
                    $pasienKemaren = pasienHariIni(
                        carbon\carbon::now()
                            ->subDay(1)
                            ->format('Y-m-d'),
                    );
                    if ($pasienHariIni > $pasienKemaren) {
                        # code...
                        if ($pasienKemaren != 0) {
                            $persentasePasienHariIni = (($pasienHariIni - $pasienKemaren) / $pasienKemaren) * 100;
                        } else {
                            $persentasePasienHariIni = 100;
                        }
                    } else {
                        if ($pasienHariIni != 0) {
                            $persentasePasienHariIni = (($pasienHariIni - $pasienKemaren) / $pasienKemaren) * 100;
                        } else {
                            if ($pasienHariIni == $pasienKemaren) {
                                $persentasePasienHariIni = 0;
                            } else {
                                $persentasePasienHariIni = -100;
                            }
                        }
                    }

                    $pasienBulanIni = pasienBulanIni();
                    $pasienBulanKemaren = pasienBulanIni(
                        carbon\carbon::now()
                            ->subMonth(1)
                            ->format('Y-m-d'),
                    );
                    if ($pasienBulanIni > $pasienBulanKemaren) {
                        if ($pasienBulanKemaren != 0) {
                            $persentasePasienBulanIni = (($pasienBulanIni - $pasienBulanKemaren) / $pasienBulanKemaren) * 100;
                        } else {
                            $persentasePasienBulanIni = 100;
                        }
                    } else {
                        if ($pasienBulanIni != 0) {
                            $persentasePasienBulanIni = (($pasienBulanIni - $pasienBulanKemaren) / $pasienBulanKemaren) * 100;
                        } else {
                            if ($pasienBulanIni == $pasienBulanKemaren) {
                                $persentasePasienBulanIni = 0;
                            } else {
                                $persentasePasienBulanIni = -100;
                            }
                        }
                    }

                @endphp
                <div class="col-span-12 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Data Klinik</h2>
                        <a href="" class="ml-auto flex items-center text-primary">
                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-3"></i> Reload Data
                        </a>
                    </div>
                    <div class="grid grid-cols-12 gap-6 mt-5">
                        <div class="col-span-12 sm:col-span-6 md:col-span-4 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i class="fa-solid fa-hospital-user report-box__icon text-primary"></i>
                                        <div class="ml-auto">
                                            @if ($persentasePasienHariIni == 0)
                                                <div class="report-box__indicator  tooltip cursor-pointer bg-secondary"
                                                    title="Tidak ada perubahan bulan ini dengan hari kemaren">
                                                    {{ round($persentasePasienHariIni, 2) }}% <i class="w-4 h-4 ml-0.5"></i>
                                                </div>
                                            @elseif ($persentasePasienHariIni > 0)
                                                <div class="report-box__indicator bg-success tooltip cursor-pointer"
                                                    title="{{ round($persentasePasienHariIni, 2) }}% lebih tinggi dari hari kemaren">
                                                    {{ round($persentasePasienHariIni, 2) }}% <i data-lucide="chevron-up"
                                                        class="w-4 h-4 ml-0.5"></i>
                                                </div>
                                            @elseif ($persentasePasienHariIni < 0)
                                                <div class="report-box__indicator bg-danger tooltip cursor-pointer"
                                                    title="{{ round($persentasePasienHariIni, 2) }}% lebih rendah dari hari kemaren">
                                                    {{ round($persentasePasienHariIni, 2) }}% <i data-lucide="chevron-down"
                                                        class="w-4 h-4 ml-0.5"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($pasienHariIni) }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Pasien Per Hari</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 md:col-span-4 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i class="fa-solid fa-hospital-user report-box__icon text-primary"></i>
                                        <div class="ml-auto">
                                            @if ($persentasePasienBulanIni == 0)
                                                <div class="report-box__indicator  tooltip cursor-pointer bg-secondary"
                                                    title="Tidak ada perubahan bulan ini dengan bulan kemaren">
                                                    {{ round($persentasePasienBulanIni, 2) }}% <i
                                                        class="w-4 h-4 ml-0.5"></i>
                                                </div>
                                            @elseif ($persentasePasienBulanIni > 0)
                                                <div class="report-box__indicator bg-success tooltip cursor-pointer"
                                                    title="{{ round($persentasePasienBulanIni, 2) }}% lebih tinggi dari bulan kemaren">
                                                    {{ round($persentasePasienBulanIni, 2) }}% <i data-lucide="chevron-up"
                                                        class="w-4 h-4 ml-0.5"></i>
                                                </div>
                                            @elseif ($persentasePasienBulanIni < 0)
                                                <div class="report-box__indicator bg-danger tooltip cursor-pointer"
                                                    title="{{ round($persentasePasienBulanIni, 2) }}% lebih rendah dari bulan kemaren">
                                                    {{ round($persentasePasienBulanIni, 2) }}% <i
                                                        data-lucide="chevron-down" class="w-4 h-4 ml-0.5"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($pasienBulanIni) }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Pasien Per Bulan</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 md:col-span-4 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i class="report-box__icon text-warning fa-solid fa-bed-pulse"></i>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ sedangDirawat() }}</div>
                                    <div class="text-base text-slate-500 mt-1">Pasien Dirawat</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 md:col-span-4 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="user" class="report-box__icon text-primary"></i>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ \App\Models\Pasien::count() }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Pasien Terdaftar</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 md:col-span-4 intro-y"
                            onclick="location.href='{{ route('bedah') }}' ">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i class="fa-solid fa-bed-pulse report-box__icon text-success"></i>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">
                                        {{ pasienBedahHariIni() }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Daftar Pasien Selesai Bedah</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 md:col-span-4 intro-y"
                            onclick="location.href='{{ route('bedah') }}'">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i class="fa-solid fa-notes-medical report-box__icon text-warning"></i>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">
                                        {{ pasienWaitingListBedahHariIni() }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Daftar Tunggu Bedah Hari Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: General Report -->
                <!-- BEGIN: Sales Report -->
                <div class="col-span-12 xl:col-span-6 mt-6">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Ketersediaan Ruangan Rawat Inap</h2>
                    </div>
                    <div class="box intro-y p-8 flex justify-between">
                        <div id="list-kamar" class="scrollbar" style="max-height: 350px;overflow-y: auto;width: 60%">

                        </div>
                        <div>
                            <div class="flex flex-col gap-1">
                                <div>
                                    <i class="fa fa-square" style="color: #991b1b" aria-hidden="true"></i> Ruangan Terisi
                                </div>
                                <div>
                                    <i class="fa fa-square" style="color: gray" aria-hidden="true"></i> Ruangan Kosong
                                </div>
                            </div>
                            <div class="mt-5">
                                <div data-progress="{{ round(persentaseKamar(), 2) }}" class="circle-progress"
                                    style="--progress: {{ round(persentaseKamar(), 2) }}%;">
                                    {{ round(persentaseKamar(), 2) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Sales Report -->
                <!-- BEGIN: Weekly Best Sellers -->
                <div class="col-span-12 xl:col-span-6 mt-6">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Jadwal Dokter</h2>
                    </div>
                    <div id="list-dokter">
                    </div>
                </div>
                <!-- END: Weekly Best Sellers -->
                <div class="col-span-12 mt-4">
                    <div class="intro-y block sm:flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Grafik Rekap Data Pasien</h2>
                        <div class="sm:ml-auto mt-3 sm:mt-0 relative text-slate-500 flex">
                            <select name="bulan_grafik" id="bulan_grafik" class="select2 form-control">
                                @foreach (bulan() as $i => $item)
                                    <option
                                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) == CarbonParse(dateStore(), 'm') ? 'selected' : '' }}
                                        value="{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}">{{ $item }}
                                    </option>
                                @endforeach
                            </select>
                            @php
                                $year = Carbon\Carbon::now()
                                    ->subYear(-10)
                                    ->format('Y');
                            @endphp
                            <select name="tahun_grafik" id="tahun_grafik" class="select2 form-control">
                                @for ($i = 2018; $i < $year; $i++)
                                    <option {{ $i == CarbonParse(now(), 'Y') ? 'selected' : '' }}
                                        value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="intro-y box p-5  mt-4 text-center">
                        <div class="loader-traffic-pasien">
                            <i class="fa fa-spinner loader-traffic-pasien fa-spin"></i><br><span
                                class="loader-traffic-pasien">Loading Diagram...</span>
                        </div>
                        <div id="traffic-pasien" class="hidden" style="max-height: 500px"></div>
                    </div>
                </div>
                <div class="col-span-12">
                    <div class="intro-y box p-5 text-center">
                        <div class="loader-column-pasien-per-bulan">
                            <i class="fa fa-spinner loader-column-pasien-per-bulan fa-spin"></i><br><span
                                class="loader-column-pasien-per-bulan">Loading Diagram...</span>
                        </div>
                        <div id="column-pasien-per-bulan" style="max-height: 500px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        (function() {
            getDokter();
            getKamar();
            initBarChart();
            initTrafficPasien();
            $('.select2').select2({
                width: '100%',
            })

            var $ppc = $('.progress-pie-chart'),
                percent = parseInt($ppc.data('percent')),
                deg = 360 * percent / 100;
            if (percent > 50) {
                $ppc.addClass('gt-50');
            }
            $('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');
            $('.ppc-percents span').html(percent + '%');
        })()

        $('#bulan_grafik').change(function() {
            initTrafficPasien();
            initBarChart();
        })

        $('#tahun_grafik').change(function() {
            initTrafficPasien();
            initBarChart();
        })

        function getDokter() {
            $.ajax({
                url: "{{ route('getDokterDashboard') }}",
                type: 'get',
                success: function(data) {
                    var html = '';
                    data.data.forEach((d, i) => {
                        if (d.status == "masuk") {
                        var status =
                            '<div' +
                            ' class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium text-center" Style="width: 70px;">' +
                            'Tersedia</div>';
                        }
                        if (d.status == "") {
                        var status =
                            '<div' +
                            ' class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium text-center" Style="width: 70px;">' +
                            'Tersedia</div>';
                        }
                        if (d.status == null) {
                        var status =
                            '<div' +
                            ' class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium text-center" Style="width: 70px;">' +
                            'Tersedia</div>';
                        }
                        if (d.status == "izin") {
                        var status =
                            '<div' +
                            ' class="py-1 px-2 rounded-full text-xs bg-warning text-dark cursor-pointer font-medium text-center" Style="width: 70px; background-color: #ffc107 !important">' +
                            'Izin</div>';
                        }
                        if (d.status == "sakit") {
                        var status =
                            '<div' +
                            ' class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium text-center" Style="width: 70px;">' +
                            'Sakit</div>';
                        }
                        if (d.status == "cuti") {
                        var status =
                            '<div' +
                            ' class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium text-center" Style="width: 70px;">' +
                            'Cuti</div>';
                        }
                        if (d.pengganti != null) {
                            if (d.pengganti.pendaftaran.length != 0) {
                                var status =
                                    '<div' +
                                    ' class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium text-center" Style="width: 70px;">' +
                                    'Sibuk</div>';
                            }

                            if (d.pengganti.image == null) {
                                image = d.pengganti.image ? '{{ route('dashboard') }}/' + d
                                    .pengganti.image : '{{ asset('dist/images/amore.png') }}'
                            }

                            html +=
                                '<div class="intro-y">' +
                                '<div class="box px-4 py-4 mb-3 flex items-center zoom-in">' +
                                '<div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">' +
                                '<img alt="Amore Animal Clinic"' +
                                'src="' + image + '">' +
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
                                    ' class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium text-center" Style="width: 70px;">' +
                                    'Sibuk</div>';
                            }


                            image = d.data_dokter.image ? '{{ route('dashboard') }}/' + d
                                .data_dokter.image : '{{ asset('dist/images/amore.png') }}'

                            html +=
                                '<div class="intro-y">' +
                                '<div class="box px-4 py-4 mb-3 flex items-center zoom-in">' +
                                '<div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">' +
                                '<img alt="Amore Animal Clinic"' +
                                'src="' + image + '">' +
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
                            '<a href="javascript:;" class="intro-x w-full block text-center rounded-md py-3 border border-dotted border-slate-400 dark:border-darkmode-300 text-slate-500">Tidak Ada Jadwal Dokter</a>';
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

        function getKamar() {
            $.ajax({
                url: "{{ route('getKamarDashboard') }}",
                type: 'get',
                success: function(data) {
                    var html = '';
                    data.data.forEach((d, i) => {
                        html +=
                            '<div class="intro-y">' +
                            '<div class="box px-4 py-4 mb-3 flex items-center">' +
                            '<div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">' +
                            '<i class="fas fa-bed w-full h-full text-primary"></i>' +
                            '</div>' +
                            '<div class="ml-4 mr-auto">' +
                            '<div class="font-medium">' + d.name + ' </div>' +
                            '<div class="text-slate-500 text-xs mt-0.5">Kapasitas</div>' +
                            '<div class="text-slate-500 text-xs mt-0.5">' +
                            '<span>' + d.terpakai + '/' + d.kapasitas + '</span>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';

                    });
                    $("#list-kamar").html(html);
                },
                error: function(data) {
                    getDokter();
                }
            });
        }

        function initBarChart() {
            $('.loader-column-pasien-per-bulan').removeClass('hidden');
            $('#column-pasien-per-bulan').addClass('hidden');
            $.ajax({
                url: '{{ route('columnPasienPerBulan') }}?_token={{ csrf_token() }}',
                data: {
                    bulan: $('#bulan_grafik').val(),
                    tahun: $('#tahun_grafik').val(),
                },
                type: 'get',
                success: function(res) {
                    Highcharts.chart('column-pasien-per-bulan', res);

                    $('.loader-column-pasien-per-bulan').addClass('hidden');
                    $('#column-pasien-per-bulan').removeClass('hidden');
                },
                error: function(data) {
                    overlay(false, 'Sedang Menyimpan Data');
                    Swal.fire({
                        title: "Terjadi Kesalahan",
                        text: data.responseJSON.message,
                        type: 'error',
                        showConfirmButton: true
                    });
                }
            });
        }

        function initTrafficPasien() {
            $('.loader-traffic-pasien').removeClass('hidden');
            $('#traffic-pasien').addClass('hidden');
            $.ajax({
                url: '{{ route('trafficPasien') }}?_token={{ csrf_token() }}',
                type: 'get',
                data: {
                    bulan: $('#bulan_grafik').val(),
                    tahun: $('#tahun_grafik').val(),
                },
                success: function(res) {
                    console.log(res)

                    Highcharts.chart('traffic-pasien', res);
                    $('.loader-traffic-pasien').addClass('hidden');
                    $('#traffic-pasien').removeClass('hidden');
                },
                error: function(data) {
                    overlay(false, 'Sedang Menyimpan Data');
                    Swal.fire({
                        title: "Terjadi Kesalahan",
                        text: data.responseJSON.message,
                        type: 'error',
                        showConfirmButton: true
                    });
                }
            });
        }

        function bedCapacity(params) {
            if ($("#report-donut-chart").length) {
                let ctx = $("#report-donut-chart")[0].getContext("2d");
                let myDoughnutChart = new chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: [
                            "31 - 50 Years old",
                            ">= 50 Years old",
                            "17 - 30 Years old",
                        ],
                        datasets: [{
                            data: [15, 10, 65],
                            backgroundColor: [
                                colors.pending(0.9),
                                colors.warning(0.9),
                                colors.primary(0.9),
                            ],
                            hoverBackgroundColor: [
                                colors.pending(0.9),
                                colors.warning(0.9),
                                colors.primary(0.9),
                            ],
                            borderWidth: 5,
                            borderColor: $("html").hasClass("dark") ?
                                colors.darkmode[700]() : colors.white,
                        }, ],
                    },
                    options: {
                        legend: {
                            display: false,
                        },
                        cutoutPercentage: 80,
                    },
                });
            }
        }
    </script>
@endsection
