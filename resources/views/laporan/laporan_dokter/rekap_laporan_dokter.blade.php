@extends('../layout/' . $layout)

@section('subcontent')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>

    <h2 class="intro-y text-lg font-medium mt-10">Laporan Jumlah Periksa Dokter</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
            </div>

            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" id="myInputTextField" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <form id="form-filter" class="col-span-12 intro-y grid grid-cols-12 gap-6 mt-5">
            <div class="col-span-12">
                <h5><b>Filter</b></h5>
            </div>
            <div class="col-span-12 md:col-span-2">
                <label for="bagian_id" class="form-label">Bagian</label>
                <select name="bagian_id" id="bagian_id" class="select2filter form-control required">
                    <option value="">Pilih Bagian</option>
                    @foreach ($bagian as $item)
                        <option {{ request('bagian_id') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                            {{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            @if (Auth::user()->akses('global'))
                <div class="col-span-12 md:col-span-3">
                    <label for="branch_id" class="form-label">Branch{{ dot() }}</label>
                    <select name="branch_id" id="branch_id" class="select2filter form-control required">
                        <option value="">Pilih Branch</option>
                        @foreach (\App\Models\Branch::get() as $item)
                            @if (Auth::user()->akses('global'))
                                <option {{ request('branch_id') == $item->id ? 'selected' : '' }}
                                    value="{{ $item->id }}">
                                    {{ $item->kode }} - {{ $item->alamat }}</option>
                            @else
                                <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }}
                                    value="{{ $item->id }}">
                                    {{ $item->kode }} - {{ $item->alamat }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-span-12 md:col-span-2">
                <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                <div class="input-group parent">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control required datepicker"
                        placeholder="yyyy-mm-dd" value="{{ \carbon\carbon::parse($req->tanggal_awal)->format('Y-m-d') }}"
                        data-single-mode="true">
                </div>
            </div>
            <div class="col-span-12 md:col-span-2">
                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                <div class="input-group parent">
                    <div class="input-group-text">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <input id="tanggal_akhir" name="tanggal_akhir" type="text" class="form-control required datepicker"
                        placeholder="yyyy-mm-dd" value="{{ \carbon\carbon::parse($req->tanggal_akhir)->format('Y-m-d') }}"
                        data-single-mode="true">
                </div>
            </div>
            <div class="col-span-12 md:col-span-3">
                <label class="form-label block">&nbsp;</label>
                <button type="button" class="btn btn-primary shadow-md mr-2" onclick="filter()"><i
                        class="fas fa-search"></i>&nbsp;Search</button>

                <a href="{{ route('laporan-dokter') }}" class="btn btn-primary shadow-md mr-2">
                    <i class="fas fa-refresh"></i>&nbsp;Refresh
                </a>
            </div>
        </form>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi1">
            <div class="flex justify-between">
                <h5 class="font-bold text-2xl">Laporan Jumlah Pemeriksaan</h5>
                <div class="col-span-12 text-right ">
                    <button class="btn btn-primary shadow-md mr-2" onclick="excel1()">Export Excel</button>
                </div>

            </div>
            <div class="flex">
                {{ CarbonParseISO($req->tanggal_awal, 'DD-MMMM-Y') }} s/d
                {{ CarbonParseISO($req->tanggal_akhir, 'DD-MMMM-Y') }}
            </div>
            <br>
            <table class="table mt-2 stripe hover table-bordered" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Nama Dokter</th>
                    <th>Branch</th>
                    <th>Rawat Jalan</th>
                    <th>Rawat Inap</th>
                    <th>Titip Sehat</th>
                    <th>Vaksin</th>
                    <th>Grooming</th>
                    <th>Steril</th>
                    <th>Jumlah Pasien</th>
                </thead>
                <tbody>
                    @php
                        $jumlahTotal = 0;
                    @endphp
                    @foreach ($data as $i => $item)
                        @php
                            $jumlah = $item->rawat_jalan;
                            $jumlah += $item->rawat_inap;
                            $jumlah += $item->titip_sehat;
                            $jumlah += $item->vaksin;
                            $jumlah += $item->grooming;
                            $jumlah += $item->steril;
                            
                            $jumlahTotal += $jumlah;
                        @endphp
                        <tr>
                            <td class="text-center" width=20>{{ $i + 1 }}</td>
                            <td class="text-left">{{ $item->karyawan->name }}</td>
                            <td class="text-center">{{ $item->branch->kode }}</td>
                            <td class="text-center">{{ number_format($item->rawat_jalan) }}</td>
                            <td class="text-center">{{ number_format($item->rawat_inap) }}</td>
                            <td class="text-center">{{ number_format($item->titip_sehat) }}</td>
                            <td class="text-center">{{ number_format($item->vaksin) }}</td>
                            <td class="text-center">{{ number_format($item->grooming) }}</td>
                            <td class="text-center">{{ number_format($item->steril) }}</td>
                            <td class="text-center">{{ number_format($jumlah) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right" colspan="3"><b><i>Total</i></b></td>
                        <td class="text-center">{{ number_format($data->sum('rawat_jalan')) }}</td>
                        <td class="text-center">{{ number_format($data->sum('rawat_inap')) }}</td>
                        <td class="text-center">{{ number_format($data->sum('titip_sehat')) }}</td>
                        <td class="text-center">{{ number_format($data->sum('vaksin')) }}</td>
                        <td class="text-center">{{ number_format($data->sum('grooming')) }}</td>
                        <td class="text-center">{{ number_format($data->sum('steril')) }}</td>
                        <td class="text-center">
                            {{ number_format($jumlahTotal) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- END: Data List -->
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi2">
            <div class="flex justify-between">
                <h5 class="font-bold text-2xl">Laporan Jumlah Tindakan Bedah</h5>
                <div class="col-span-12 text-right  mb-3">
                    <button class="btn btn-primary shadow-md mr-2" onclick="excel2()">Export Excel</button>
                </div>

            </div>
            <div class="flex">
                {{ CarbonParseISO($req->tanggal_awal, 'DD-MMMM-Y') }} s/d
                {{ CarbonParseISO($req->tanggal_akhir, 'DD-MMMM-Y') }}
            </div>
            <br>
            <table class="table mt-2 stripe hover table-bordered" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Nama Tindakan</th>
                    @foreach ($dokter as $item)
                        <th>{{ $item->name }}</th>
                    @endforeach
                </thead>
                <tbody>
                    @php
                        $jumlahTotal = 0;
                        $count = [];
                        foreach ($dokter as $item1) {
                            $count[$item1->id] = 0;
                        }
                    @endphp
                    @foreach ($tindakan as $i => $item)
                        <tr>
                            <td class="text-center" width=20>{{ $i + 1 }}</td>
                            <td class="text-left">{{ $item->name }}</td>
                            @foreach ($dokter as $key1 => $item1)
                                @php
                                    $jumlahTotal += $item['bedah_' . $item1->id];
                                    $count[$item1->id] += $item['bedah_' . $item1->id];
                                @endphp
                                <td class="text-center">
                                    {{ number_format($item['bedah_' . $item1->id]) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right" colspan="2"><b><i>Total</i></b></td>
                        @foreach ($dokter as $item)
                            <td class="text-right">{{ $count[$item->id] }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- END: Data List -->
    </div>
    <div id="xlsDownload" style="display: none"></div>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.select2filter').select2({
                width: '100%',
            })
        })

        function filter(params) {
            var form = $('#form-filter').serialize();
            location.href = `{{ route('laporan-dokter') }}?${form}`;
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
