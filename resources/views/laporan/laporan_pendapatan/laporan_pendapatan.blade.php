<!DOCTYPE html>
<html>

<head>
    {{ convertSlug($global['title']) }}
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/dboard/logo/faveicon.png') }}" />
    {{-- <link href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="{{ asset('assets/css/chosen/chosen.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css"
        integrity="sha512-rxThY3LYIfYsVCWPCW9dB0k+e3RZB39f23ylUYTEuZMDrN/vRqLdaCBo/FbvVT6uC2r0ObfPzotsfKF9Qc5W5g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="{{ asset('assets/images/amore.png') }}" type="image/x-icon"> <!-- Favicon-->

    <style type="text/css">
        body {
            font-family: arial;
            font-size: 13px;
        }

        .height {
            background: white;
            height: 100%;
        }

        .pt-2 {
            padding-top: 20px;
        }

        .pl-2 {
            padding-top: 20px;
        }

        .pr-2 {
            padding-right: 20px !important;
        }

        .width-10 {
            width: 10%;
        }

        .width-20 {
            width: 20%;
        }

        .border-black {
            border: 1px solid #9999;
        }

        .box-git {
            width: 100%;
            height: 133px;
        }

        .nopadding-right {
            padding-right: 0 !important;
            margin-right: 0 !important;
        }

        .nopadding-left {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }

        .mt-1 {
            margin-top: 10px !important;
        }

        .mt-2 {
            margin-top: 20px !important;
        }

        .mb-1 {
            margin-bottom: 10px !important;
        }

        .mb-2 {
            margin-bottom: 20px !important;
        }

        .mr-1 {
            margin-right: 10px !important;
        }

        .mr-2 {
            margin-right: 20px !important;
        }

        .ml-1 {
            margin-left: 10px !important;
        }

        .ml-2 {
            margin-left: 20px !important;
        }

        .grey {
            color: grey;
        }

        .width-100 {
            width: 100%;
        }

        .none {
            text-decoration: none;
            list-style-type: none;
        }

        .d-inline-block {
            display: inline-block;
            vertical-align: middle;
        }

        .d-inline {
            display: inline;
            vertical-align: middle;
        }

        .d-inline li {
            display: inline;
        }

        .m-auto {
            margin: auto;
        }

        .nav-tabs li a {
            padding-left: 0 !important;
            padding-right: 0 !important;
            text-align: center !important;
        }

        .font-small {
            font-size: 12px;
        }

        .middle {
            height: 47px;
        }

        .black {
            color: black;
        }

        .head {
            background: grey !important;
            color: white;
            width: 100%;
            height: 100%;
            vertical-align: middle;
        }

        .mt-5 {
            margin-top: 50px
        }

        .head_awal {
            background-color: black !important;
            color: white;
        }

        .head_awal1 {
            background-color: black !important;
            color: white;
        }

        .head_awal2 {
            background-color: black !important;
            color: white;
        }

        .hide {
            display: none;
        }

        .disabled {
            pointer-events: none;
        }

        .tree tr {
            border: hidden;
        }

        .tree_1 tr {
            border: hidden;
        }

        hr {
            border-top: 1px solid black;
            margin-top: 2px;
            margin-bottom: 0px;
        }


        .border-right-none {
            border-right: none !important;
        }

        .border-none {
            border: none !important;
        }

        .table-border td {
            border: 1px solid black !important;
            padding: 1px;
        }

        .table-margin {
            margin-top: 70px;
            background: white;
            font-size: 10px;
            padding: 5px;
        }

        @media print {

            header,
            header * {
                display: none !important;
            }

            .table thead tr td,
            .table tbody tr td {
                border-width: 1px !important;
                border-style: solid !important;
                border-color: black !important;
                background-color: red;
                -webkit-print-color-adjust: exact;
            }

            body {
                background-color: white !important;
            }

            .table-margin {
                margin-top: 0px;
            }
        }

        .ttd {
            height: 70px;
            width: 20%;
        }

        .dotted {
            border-bottom: 2px dotted gray;
            width: 100%;
            height: 1px;
            margin-bottom: 5px;
            margin-top: 10px;
            position: relative;
        }

        .fa-scissors {
            position: absolute;
            top: -10px;
            font-size: 20px;
            font-weight: 800
        }

        fieldset.scheduler-border {
            border: 1px groove black !important;
            padding: 0 56em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
        }

        legend {
            width: auto;
        }

        .table-item th {
            padding: 4px;
        }

        .table-item td {
            padding: 4px;
        }


        .table-total th {
            padding: 4px 0px;
        }

        .table-total td {
            padding: 4px 0px;
        }

        .table-item th {
            background: #cccc;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>
    <!-- datepicker  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/treegrid/js/jquery.cookie.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/treegrid/js/jquery.treegrid.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendors/treegrid/css/jquery.treegrid.css') }}">
</head>

<body style="background: grey">
    <header id="navigation"
        style="padding: 0px 0px;height: 60px;vertical-align: middle;background: rgba(0, 0, 0, 0.8); box-shadow: 0px 2px 5px #444; z-index:2;width: 100%;position: fixed;top: 0px;">
        <div class="container">
            <div class="row">
                <nav class="navbar navbar-light" style="width: 100%">
                    <div class="col-md-4">
                        <a class="navbar-brand" href="{{ url('/') }}" style="color: white !important">
                            AMORE ANIMAL CLINIC
                        </a>
                    </div>
                    <div class="col-md-4 d-flex btn-group">
                        <button class="btn btn-info" id="tambah-data" style="float: right;">
                            <i class="fa fa-cogs"></i> Filter
                        </button>
                        <button class="btn btn-warning" onclick="excel()" style="float: right;">
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button>
                        <button class="btn btn-success" onclick="print()" style="float: right;">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <div id="isi" class="container">
        <div class="row table-margin">
            <div class="col-sm-12">
                <table style="width: 100%">
                    <tr style="vertical-align: top">
                        <td style="width: 25%;padding-top: 1rem">
                            <img src="https://pjt-acm.amoreanimalclinic.com/dist/images/amoretext.png" style="width: 10rem">
                        </td>
                        <td style="width: 50%; text-align: center;vertical-align: top;padding-top: 1rem">
                            <h2 style="margin-bottom: 0px;padding: 0px ;margin: 0px"><b>AMORE ANIMAL CLINIC</b></h2>
                            <span style="font-size: 15px;text-align: center">
                                Jl. Pejaten Raya Blok A. No. 21. Pejaten Barat.<br>
                                Villa Pejaten Mas, Pasar Minggu<br>
                                Jakarta Selatan<br>
                            </span>
                        </td>
                        <td style="width: 25%;padding-top: 1rem">

                        </td>
                    </tr>
                </table>
                <hr>
                <br>
                <table style="width:100%;font-size:14px">
                    <tr>
                        <td>
                            <center>
                                Laporan Transaksi Tanggal <b>{{ CarbonParse($tanggal_awal, 'd-F-Y') }}</b> s/d
                                <b> {{ CarbonParse($tanggal_akhir, 'd-F-Y') }}</b>
                            </center>
                        </td>
                        <td>:</td>
                    </tr>
                </table>
                <br>
                <table class="table-item" style="width: 100%" border="1">
                    <thead align="center">

                        <th>NO</th>
                        <th>KODE</th>
                        <th>REF</th>
                        <th>TANGGAL TRANSAKSI</th>
                        <th>DESKRIPSI</th>
                        <th>CABANG</th>
                        <th>METODE PEMBAYARAN</th>
                        <th>PEMASUKAN</th>
                        <th>PENGELUARAN</th>
                    </thead>
                    <tbody>
                        @php
                            $debet = 0;
                            $kredit = 0;
                        @endphp
                        @foreach ($data as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $item->kode }}</td>
                                <td>{{ $item->ref }}</td>
                                <td align="center">{{ CarbonParse($item->tanggal, 'd-M-Y') }}</td>
                                <td>{{ $item->description }}</td>
                                <td align="center">{{ $item->Branch->kode }}</td>
                                <td align="center">{{ $item->metode_pembayaran }}</td>
                                <td class="text-right">
                                    @if ($item->dk == 'DEBET')
                                        <div style="float:left;width:10%;">
                                            Rp.
                                        </div>
                                        <div style="float:right;width:50%;">
                                            {{ number_format($item->nominal) }}
                                        </div>
                                        @php
                                            $debet += $item->nominal;
                                        @endphp
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if ($item->dk == 'KREDIT')
                                        <div style="float:left;width:10%;">
                                            Rp.
                                        </div>
                                        <div style="float:right;width:50%;">
                                            {{ number_format($item->nominal) }}
                                        </div>
                                        @php
                                            $kredit += $item->nominal;
                                        @endphp
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-right"><b>TOTAL</b></td>
                            <td colspan="1" class="text-right" style="width: 150px">
                                <div style="float:left;width:10%;">
                                    Rp.
                                </div>
                                <div style="float:right;width:50%;">
                                    <b>{{ number_format($debet) }}</b>
                                </div>
                            </td>
                            <td colspan="1" class="text-right" style="width: 150px">
                                <div style="float:left;width:10%;">
                                    Rp.
                                </div>
                                <div style="float:right;width:50%;">
                                    <b>{{ number_format($kredit) }}</b>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-right"><b>TOTAL PENDAPATAN</b></td>
                            <td colspan="2" class="text-right">
                                <div style="float:left;width:0%;">
                                    Rp.
                                </div>
                                <div style="float:right;width:50%;">
                                    <b>{{ number_format($debet - $kredit) }}</b>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <table style="width: 100%;margin-top: 20px">
                    <tr>
                        <td style="text-align: left;height: 100px;vertical-align: middle" colspan="2">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
<div id="xlsDownload" style="display: none"></div>

<div id="modal-tambah-data" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="form-data" action="{{ route('laporan-pendapatan') }}">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 mb-3">
                        <div class="form-group c_form_group">
                            <label>Branch <span style="color:red;font-weight:bold" class="important">
                                    *</span></label>
                            <select name="branch_id" id="branch_id" class="form-control branch_id select2 required">
                                <option value="">Pilih Branch</option>
                                @foreach (\App\Models\Branch::where('status', true)->get() as $item)
                                    @if (Auth::user()->akses('global'))
                                        <option value="{{ $item->id }}">{{ $item->kode }} {{ $item->lokasi }}
                                        </option>
                                    @else
                                        <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }}
                                            value="{{ $item->id }}">{{ $item->kode }} {{ $item->lokasi }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            {{ csrf_field() }}
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="form-group c_form_group">
                            <label>Tanggal Awal <span style="color:red;font-weight:bold" class="important">
                                    *</span></label>
                            <input id="tanggal_awal" type="text" class="form-control required datepicker"
                                name="tanggal_awal" value="{{ $tanggal_awal }}" placeholder="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="form-group c_form_group">
                            <label>Tanggal Akhir <span style="color:red;font-weight:bold" class="important">
                                    *</span></label>
                            <input id="tanggal_akhir" type="text" class="form-control required datepicker"
                                name="tanggal_akhir" value="{{ $tanggal_akhir }}" placeholder="yyyy-mm-dd">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="submit" type="button" class="btn btn-primary" id="simpan">Cari</button>
            </div>
        </form>
    </div>
</div>
<script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
<script type="text/javascript">
    $('#tambah-data').click(function() {
        $('.c_form_group').not('.readonly').removeClass('disabled');
        $('.c_form_group').find('input').not('.readonly').prop('readonly', false);
        $('.not-editable').not('.readonly').removeClass('disabled');
        $('.not-editable').find('input').not('.readonly').prop('readonly', false);
        $('#modal-tambah-data').modal('toggle');
    })

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
    }).on('changeDate', function() {});

    function excel(argument) {
        var blob = b64toBlob(btoa($('div[id=isi]').html().replace(/[\u00A0-\u2666]/g, function(c) {
            return '&#' + c.charCodeAt(0) + ';';
        })), "application/vnd.ms-excel");
        var blobUrl = URL.createObjectURL(blob);
        var dd = new Date()
        var ss = '' + dd.getFullYear() + "-" +
            (dd.getMonth() + 1) + "-" +
            (dd.getDate()) +
            "_" +
            dd.getHours() +
            dd.getMinutes() +
            dd.getSeconds()

        $("#xlsDownload").html("<a href=\"" + blobUrl + "\" download=\"Download_Laporan_Amore\_" + ss +
            "\.xls\" id=\"xlsFile\">Download</a>");
        $("#xlsFile").get(0).click();

        function b64toBlob(b64Data, contentType, sliceSize) {
            contentType = contentType || '';
            sliceSize = sliceSize || 512;

            var byteCharacters = atob(b64Data);
            var byteArrays = [];


            for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                var slice = byteCharacters.slice(offset, offset + sliceSize);

                var byteNumbers = new Array(slice.length);
                for (var i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                var byteArray = new Uint8Array(byteNumbers);

                byteArrays.push(byteArray);
            }

            var blob = new Blob(byteArrays, {
                type: contentType
            });
            return blob;
        }
    }
</script>

</html>
