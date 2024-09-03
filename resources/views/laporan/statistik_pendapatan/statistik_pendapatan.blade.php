@extends('../layout/' . $layout)

@section('subhead')
    {{ convertSlug($global['title']) }}
@endsection

@section('subcontent')
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: General Report -->
                <div class="col-span-12 mt-4">
                    <div class="intro-y block sm:flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Grafik Pendapatan dan Pengeluaran</h2>
                    </div>
                    <div class="intro-y box p-5 mt-4 gap-6 grid grid-cols-12">
                        <div class="col-span-3  text-left">
                            <select name="branch_id_stock" id="branch_id_stock" class="select2 form-control">
                                <option selected value="">Semua Cabang</option>
                                @foreach (\App\Models\Branch::get() as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} {{ $item->lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-3">
                            <button class="btn btn-primary" onclick="initChart()"><i class="fa fa-search"></i>
                                Search</button>
                        </div>
                        <div class="col-span-12">
                            <div id="container" class="hidden" style="max-height: 500px"></div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12">
                    <div class="intro-y box p-5 grid grid-cols-12">
                        <div class="col-span-4 flex  text-left">
                            <select name="bulan" id="bulan" class="select2 form-control">
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
                            <select name="tahun" id="tahun" class="select2 form-control">
                                @for ($i = 2018; $i < $year; $i++)
                                    <option {{ $i == CarbonParse(now(), 'Y') ? 'selected' : '' }}
                                        value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-span-3">
                            <button class="btn btn-primary" onclick="initBarChart()"><i class="fa fa-search"></i>
                                Search</button>
                        </div>

                        <div class="col-span-12">
                            <div id="container2" style="max-height: 500px"></div>
                        </div>
                        <div class="col-span-6">
                            <figure class="highcharts-figure">
                                <div id="metode_pembayaran"></div>
                                <p class="highcharts-description">

                                </p>
                            </figure>
                        </div>
                        <div class="col-span-6">
                            <figure class="highcharts-figure">
                                <div id="pembayaran"></div>
                                <p class="highcharts-description">

                                </p>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css"
        integrity="sha512-rxThY3LYIfYsVCWPCW9dB0k+e3RZB39f23ylUYTEuZMDrN/vRqLdaCBo/FbvVT6uC2r0ObfPzotsfKF9Qc5W5g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.highcharts.com/stock/highstock.js"></script>
    <script src="https://code.highcharts.com/stock/modules/data.js"></script>
    <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/stock/modules/export-data.js"></script>
    <script>
        var table;
        var cabang = [];
        var data = [];
        var omzet = [];
        (function() {
            initChart();
            initBarChart();
            initPieChart();
            $('.select2').select2({
                width: '100%',
            })

            $(".tanggal").each(function() {
                let options = {
                    autoApply: false,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    format: "YYYY-MM",
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

                new Litepicker({
                    element: this,
                    ...options,
                    setup: (picker) => {
                        picker.on('button:apply', (date1, date2) => {
                            initBarChart();
                        });
                    },
                });
            });

            $('#bulan').datepicker({
                format: 'yyyy-mm',
                autoclose: true,
                viewMode: "months",
                minViewMode: "months",
                todayHighlight: true,
            }).on('changeDate', function() {
                initBarChart();
            });

        })()

        $('#bulan_grafik').change(function() {
            initTrafficPasien();
            initBarChart();
        })

        $('#tahun_grafik').change(function() {
            initTrafficPasien();
            initBarChart();
        })

        function initChart() {
            $.ajax({
                url: '{{ route('getDataHighChartStatistikPendapatan') }}?_token={{ csrf_token() }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    branch_id() {
                        return $('#branch_id_stock').val();
                    },
                },
                type: 'get',
                success: function(res) {
                    res.data.forEach((d, i) => {
                        data[i] = [];
                        data[i].name = d.name;
                        data[i].data = [];
                        data[i].tooltip = {
                            valueDecimals: 0
                        };

                        omzet[i] = [];
                        omzet[i].name = d.name;
                        omzet[i].data = [];
                        omzet[i].tooltip = {
                            valueDecimals: 0
                        };
                        d.data.forEach((d1, i1) => {
                            data[i].data[i1] = [d1.x * 1, d1.y * 1];
                        });

                        res.omzet[i].data.forEach((d1, i1) => {
                            omzet[i].data[i1] = [d1.x * 1, d1.y * 1];
                        });
                    });


                    Highcharts.stockChart('container', {
                        // chart: {
                        //     events: {
                        //         load: function() {
                        //             // set up the updating of the chart each second
                        //             var series = this.series[0];
                        //             setInterval(function() {
                        //                 var x = (new Date()).getTime(), // current time
                        //                     y = Math.round(Math.random() * 100);
                        //                 series.addPoint([x, y], true, true);
                        //             }, 1000);
                        //         }
                        //     }
                        // },
                        rangeSelector: {
                            selected: 1
                        },
                        title: {
                            text: 'Grafik Pendapatan Dan Pengeluaran'
                        },
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom',
                            x: 0,
                            y: 0,
                            floating: false,
                            borderWidth: 1,
                            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor ||
                                '#FFFFFF',
                            shadow: true,
                            enabled: res.enable
                        },
                        series: omzet
                    });

                    $('.loader-diagram').html('');
                    $('#container').removeClass('hidden');
                },
                error: function(data) {
                    Swal.fire({
                        title: "Terjadi Kesalahan",
                        text: data.responseJSON.message,
                        type: 'error',
                        showConfirmButton: true
                    });
                }
            });
        }

        function initBarChart() {
            $('.loader-diagram-bar').removeClass('hidden');
            $('#container2').addClass('hidden');
            $.ajax({
                url: '{{ route('getDataBarHighChartStatistikPendapatan') }}?_token={{ csrf_token() }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    bulan() {
                        return $('#bulan').val();
                    },
                    tahun() {
                        return $('#tahun').val();
                    },
                },
                type: 'get',
                success: function(res) {
                    Highcharts.chart('container2', res);

                    $('.loader-diagram-bar').addClass('hidden');
                    $('#container2').removeClass('hidden');
                },
                error: function(data) {
                    Swal.fire({
                        title: "Terjadi Kesalahan",
                        text: data.responseJSON.message,
                        type: 'error',
                        showConfirmButton: true
                    });
                }
            });
        }

        function initPieChart() {
            $.ajax({
                url: '{{ route('getDataPieChartStatistikPendapatan') }}?_token={{ csrf_token() }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    bulan() {
                        return $('#bulan').val();
                    },
                    tahun() {
                        return $('#tahun').val();
                    },
                },
                type: 'get',
                success: function(data) {
                    Highcharts.chart('metode_pembayaran', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Grafik Penggunaan Metode Pembayaran Amore'
                        },
                        accessibility: {
                            point: {
                                valueSuffix: '%'
                            }
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                    style: {
                                        fontFamily: '\'Lato\', sans-serif',
                                        lineHeight: '12px',
                                        fontSize: '12px'
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Nilai',
                            colorByPoint: true,
                            data: data.metode_pembayaran.data
                        }]
                    });

                    Highcharts.chart('pembayaran', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Grafik Tingkat Pembayaran Klien Amore'
                        },
                        accessibility: {
                            point: {
                                valueSuffix: '%'
                            }
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                    style: {
                                        fontFamily: '\'Lato\', sans-serif',
                                        lineHeight: '12px',
                                        fontSize: '12px'
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Nilai',
                            colorByPoint: true,
                            data: data.pembayaran.data
                        }]
                    });

                    $('.search').removeClass('hidden');
                    $('.spin').addClass('hidden');
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

        function filter() {
            initChart();
            initBarChart();
            initPieChart();
        }
    </script>
@endsection
