@extends('../layout/' . $layout)

@section('subcontent')
    <style>
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

        .border-black {
            border: 1px solid #9999;
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
    </style>
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
                {{-- <div class="dropdown inline">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center">
                            <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40 ">
                        <ul class="dropdown-content">
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div> --}}
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
        @if (Auth::user()->akses('global'))
            <div class="col-span-12  md:col-span-3 mb-3">
                <label for="branch_id" class="form-label">Branch{{ dot() }}</label>
                <select name="branch_id" id="branch_id" class="select2 form-control required">
                    <option value="">Pilih Branch</option>
                    @foreach (\App\Models\Branch::get() as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->kode }} - {{ $item->alamat }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-span-12  md:col-span-3 mb-3">
            <label for="tanggal_awal" class="form-label">Tanggal awal</label>
            <div class="input-group parent">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
                <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control required datepicker"
                    placeholder="yyyy-mm-dd" placeholder="yyyy-mm-dd"
                    value="{{ Carbon\carbon::parse(dateStore($req->tanggal_awal))->format('Y-m-d') }}"
                    data-single-mode="true">
            </div>
        </div>
        <div class="col-span-12  md:col-span-3 mb-3">
            <label for="tanggal_akhir" class="form-label">Tanggal akhir</label>
            <div class="input-group parent">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
                <input id="tanggal_akhir" name="tanggal_akhir" type="text" class="form-control required datepicker"
                    placeholder="yyyy-mm-dd" placeholder="yyyy-mm-dd"
                    value="{{ Carbon\carbon::parse($req->tanggal_akhir)->format('Y-m-d') }}" data-single-mode="true">
            </div>
        </div>
        <div class="col-span-12 md:col-span-3">
            <label class="form-label block">&nbsp;</label>
            <button class="btn btn-primary shadow-md mr-2" onclick="filter()"><i
                    class="fas fa-search"></i>&nbsp;Search</button>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi">
            <div class="col-span-12 text-right mb-3">
                <button class="btn btn-primary shadow-md mr-2" onclick="excel()">Export Excel</button>
            </div>
            <table class="table-item" id="table" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>Branch</th>
                    <th>Tunai</th>
                    <th>Debet</th>
                    <th>Transfer</th>
                    <th>Total</th>
                </thead>
                <tbody>
                    @php
                        $deposit = 0;
                        $cash = 0;
                        $debet = 0;
                        $transfer = 0;
                    @endphp
                    @foreach ($branch as $item)
                        @php
                            // $deposit += $item->kasir->sum('deposit');
                            $cash += $item->kasir->sum('cash') + $item->cash_deposit_debet - $item->cash_deposit_kredit - $item->pengeluaran_tunai;
                            $debet += $item->kasir->sum('debet') + $item->debet_deposit_debet - $item->debet_deposit_kredit - $item->pengeluaran_debet;
                            $transfer += $item->kasir->sum('transfer') + $item->transfer_deposit_debet - $item->transfer_deposit_kredit - $item->pengeluaran_transfer;
                            $total = $item->kasir->sum('cash') + $item->kasir->sum('debet') + $item->kasir->sum('transfer') + $item->cash_deposit_debet - $item->cash_deposit_kredit - $item->pengeluaran_tunai - $item->pengeluaran_debet - $item->pengeluaran_transfer + $item->debet_deposit_debet - $item->debet_deposit_kredit + $item->transfer_deposit_debet - $item->transfer_deposit_kredit;
                        @endphp
                        <tr>
                            <td>{{ $item->kode }} - {{ $item->alamat }}</td>
                            {{-- <td class="text-right">{{ number_format($item->kasir->sum('deposit')) }}</td> --}}
                            <td class="text-right">
                                {{ number_format($item->kasir->sum('cash') + $item->cash_deposit_debet - $item->cash_deposit_kredit - $item->pengeluaran_tunai * 1) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($item->kasir->sum('debet') + $item->debet_deposit_debet - $item->debet_deposit_kredit - $item->pengeluaran_debet) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($item->kasir->sum('transfer') + $item->transfer_deposit_debet - $item->transfer_deposit_kredit - $item->pengeluaran_transfer) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($total) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right"><b><i>Total</i></b></td>
                        {{-- <td class="text-right">{{ number_format($deposit) }}</td> --}}
                        <td class="text-right">{{ number_format($cash) }}</td>
                        <td class="text-right">{{ number_format($debet) }}</td>
                        <td class="text-right">{{ number_format($transfer) }}</td>
                        <td class="text-right">
                            {{ number_format($cash + $debet + $transfer) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi1">
            <div class="col-span-12 text-right  mb-3">
                <button class="btn btn-primary shadow-md mr-2" onclick="excel1()">Export Excel</button>
            </div>
            <table class="table-item" style="width: 100%" border="1">
                <thead align="center">
                    <th>NO</th>
                    <th>KODE</th>
                    <th>REF</th>
                    <th>TANGGAL TRANSAKSI</th>
                    <th>METODE PEMBAYARAN</th>
                    <th>DESKRIPSI</th>
                    <th>CABANG</th>
                    <th>PEMASUKAN</th>
                    <th>PENGELUARAN</th>
                </thead>
                <tbody>
                    @php
                        $debet = 0;
                        $kredit = 0;
                    @endphp
                    @foreach ($data->sortBy('created_at') as $i => $item)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $item->kode }}</td>
                            <td>{{ $item->ref }}</td>
                            <td align="center">{{ CarbonParse($item->tanggal, 'd-M-Y') }}</td>
                            <td align="center">{{ $item->metode_pembayaran }}</td>
                            <td>{{ $item->description }}</td>
                            <td align="center">{{ $item->Branch->kode }}</td>
                            <td class="text-right">
                                @if ($item->dk == 'DEBET')
                                    @if (Auth::user()->role->name == 'Superuser')
                                        {{ number_format($item->nominal) }}
                                    @else
                                        <div style="float:left;width:10%;">
                                            Rp.
                                        </div>
                                        <div style="float:right;width:50%;">
                                            {{ number_format($item->nominal) }}
                                        </div>
                                    @endif
                                    @php
                                        $debet += $item->nominal;
                                    @endphp
                                @else
                                    0
                                @endif
                            </td>
                            <td class="text-right">
                                @if ($item->dk == 'KREDIT')
                                    @if (Auth::user()->role->name == 'Superuser')
                                        {{ number_format($item->nominal) }}
                                    @else
                                        <div style="float:left;width:10%;">
                                            Rp.
                                        </div>
                                        <div style="float:right;width:50%;">
                                            {{ number_format($item->nominal) }}
                                        </div>
                                    @endif
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
                            <div style="float:right;">
                                <b>{{ number_format($debet) }}</b>
                            </div>
                        </td>
                        <td colspan="1" class="text-right" style="width: 150px">
                            <div style="float:left;width:10%;">
                                Rp.
                            </div>
                            <div style="float:right;">
                                <b>{{ number_format($kredit) }}</b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right"><b>TOTAL PENDAPATAN</b></td>
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
        </div>
        <div class="intro-y col-span-12 p-8 my-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi2">
            <div class="col-span-12 text-right  mb-3">
                <button class="btn btn-primary shadow-md mr-2" onclick="excel2()">Export Excel</button>
            </div>
            <table class="table-item" style="width: 100%;border-color: black" border="1">
                <thead align="center">
                    <tr>
                        <th>TANGGAL</th>
                        <th>DESKRIPSI</th>
                        <th>PEMASUKAN</th>
                        <th>PENGELUARAN</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $debet = 0;
                        $kredit = 0;
                        $debetTertinggi = 0;
                        $kreditTertinggi = 0;
                    @endphp
                    @foreach ($dates as $i => $item)
                        @php
                            $debet += $item['penerimaan'];
                            $kredit += $item['pengeluaran'];
                            
                            if ($debetTertinggi < $item['penerimaan']) {
                                $debetTertinggi = $item['penerimaan'];
                            }
                            
                            if ($kreditTertinggi < $item['pengeluaran']) {
                                $kreditTertinggi = $item['pengeluaran'];
                            }
                        @endphp
                        <tr>
                            <td>{{ $item['tanggal'] }}</td>
                            <td>PEMASUKAN KLINIK</td>
                            <td style="text-align: right">
                                <div style="float:left;width:0%;">
                                    Rp.
                                </div>
                                <div style="float:right;width:50%;">
                                    <b>{{ number_format($item['penerimaan']) }}</b>
                                </div>
                            </td>
                            <td style="text-align: right">
                                <div style="float:left;width:0%;">
                                    Rp.
                                </div>
                                <div style="float:right;width:50%;">
                                    <b>0</b>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>PENGELUARAN KLINIK</td>
                            <td style="text-align: right">
                                <div style="float:left;width:0%;">
                                    Rp.
                                </div>
                                <div style="float:right;width:50%;">
                                    <b>0</b>
                                </div>
                            </td>
                            <td style="text-align: right">
                                <div style="float:left;width:0%;">
                                    Rp.
                                </div>
                                <div style="float:right;width:50%;">
                                    <b>{{ number_format($item['pengeluaran']) }}</b>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Total</b></td>
                        <td style="text-align: right">
                            <div style="float:left;width:0%;">
                                Rp.
                            </div>
                            <div style="float:right;width:50%;">
                                <b>{{ number_format($debet) }}</b>
                            </div>

                        </td>
                        <td style="text-align: right">
                            <div style="float:left;width:0%;">
                                Rp.
                            </div>
                            <div style="float:right;width:50%;">
                                <b>{{ number_format($kredit) }}</b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Rata-rata per Hari</b></td>
                        <td style="text-align: right">

                            <div style="float:left;width:0%;">
                                Rp.
                            </div>
                            <div style="float:right;width:50%;">
                                <b>{{ number_format($debet / count($dates)) }}</b>
                            </div>
                        </td>
                        <td style="text-align: right">
                            <div style="float:left;width:0%;">
                                Rp.
                            </div>
                            <div style="float:right;width:50%;">
                                <b>{{ number_format($kredit / count($dates)) }}</b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Nilai Tertinggi</b></td>
                        <td style="text-align: right">
                            <div style="float:left;width:0%;">
                                Rp.
                            </div>
                            <div style="float:right;width:50%;">
                                <b>{{ number_format($debetTertinggi) }}</b>
                            </div>
                        </td>
                        <td style="text-align: right">
                            <div style="float:left;width:0%;">
                                Rp.
                            </div>
                            <div style="float:right;width:50%;">
                                <b>{{ number_format($kreditTertinggi) }}</b>
                            </div>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
        <!-- END: Data List -->
    </div>
    <div id="xlsDownload" style="display: none"></div>
@endsection
@section('script')
    <script>
        (function() {
            $('.select2').select2({
                width: '100%',
            })
            $('.select2filter').select2({
                width: '100%',
            })
        })()

        function filter(params) {
            location.href = '{{ route('laporan-admin-harian') }}?tanggal_awal=' + $('#tanggal_awal').val() +
                '&tanggal_akhir=' + $('#tanggal_akhir').val() +
                '&branch_id=' + $('#branch_id').val();
        }

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

        function excel1(argument) {
            var blob = b64toBlob(btoa($('div[id=isi1]').html().replace(/[\u00A0-\u2666]/g, function(c) {
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

        function excel2(argument) {
            var blob = b64toBlob(btoa($('div[id=isi2]').html().replace(/[\u00A0-\u2666]/g, function(c) {
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
@endsection
