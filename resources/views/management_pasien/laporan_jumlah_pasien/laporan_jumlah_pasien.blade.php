@extends('../layout/' . $layout)
@section('content_filter')
    @include('../management_pasien/laporan_jumlah_pasien/filter_laporan_jumlah_pasien')
@endsection
@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
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
        <div class="col-span-12 ">
            <h5><b>Filter</b></h5>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi">
            <div class="flex justify-between">
                <h5 class="font-bold text-2xl">Laporan Jumlah Pasien Berdasarkan Pasien Masuk/Keluar</h5>
                <div class="col-span-12 text-right mb-3">
                    <button class="btn btn-primary shadow-md mr-2" onclick="excel()">Export Excel</button>
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
                    <th>Branch</th>
                    <th>Total Pasien Daftar</th>
                    <th>Total Pasien Batal</th>
                    <th>Total Pasien Periksa</th>
                    <th>Total Rawat Jalan</th>
                    <th>Total Rawat Inap</th>
                    <th>Pasien Meninggal</th>
                    <th>Pasien Pulang</th>
                    {{-- <th>Total</th> --}}
                </thead>
                <tbody>
                    @php
                        $pasienDaftar = 0;
                        $pasienBatal = 0;
                        $pasienPeriksa = 0;
                        $pasienRawatJalan = 0;
                        $pasienRawatInap = 0;
                        $pasienMeninggal = 0;
                        $pasienPulang = 0;
                    @endphp
                    @foreach ($branch as $item)
                        @php
                            $pasienDaftar += $item->total_pasien_daftar;
                            $pasienBatal += $item->total_pasien_batal;
                            $pasienPeriksa += $item->total_pasien_periksa;
                            $pasienRawatJalan += $item->total_rawat_jalan;
                            $pasienRawatInap += $item->total_rawat_inap;
                            $pasienMeninggal += $item->total_pasien_meninggal;
                            $pasienPulang += $item->total_pasien_pulang;
                        @endphp
                        <tr>
                            <td>{{ $item->kode }} - {{ $item->alamat }}</td>
                            <td class="text-right">{{ number_format($item->total_pasien_daftar) }}</td>
                            <td class="text-right">{{ number_format($item->total_pasien_batal) }}</td>
                            <td class="text-right">{{ number_format($item->total_pasien_periksa) }}</td>
                            <td class="text-right">{{ number_format($item->total_rawat_jalan) }}</td>
                            <td class="text-right">{{ number_format($item->total_rawat_inap) }}</td>
                            <td class="text-right">{{ number_format($item->total_pasien_meninggal) }}</td>
                            <td class="text-right">{{ number_format($item->total_pasien_pulang) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right"><b><i>Total</i></b></td>
                        <td class="text-right">{{ number_format($pasienDaftar) }}</td>
                        <td class="text-right">{{ number_format($pasienBatal) }}</td>
                        <td class="text-right">{{ number_format($pasienPeriksa) }}</td>
                        <td class="text-right">{{ number_format($pasienRawatJalan) }}</td>
                        <td class="text-right">{{ number_format($pasienRawatInap) }}</td>
                        <td class="text-right">{{ number_format($pasienMeninggal) }}</td>
                        <td class="text-right">{{ number_format($pasienPulang) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- END: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi1">
            <div class="flex justify-between">
                <h5 class="font-bold text-2xl">Laporan Jumlah Pasien Berdasarkan Poli</h5>
                <div class="col-span-12 text-right  mb-3">
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
                <th>Hewan</th>
                <th>Rawat Jalan</th>
                <th>Rawat Inap</th>
                <th>Titip Sehat</th>
                <th>Vaksin</th>
                <th>Grooming</th>
                <th>Bedah</th>
                </thead>
                <tbody>
                    @php
                        $rawatJalan = 0;
                        $rawatInap = 0;
                        $titipSehat = 0;
                        $vaksin = 0;
                        $grooming = 0;
                        $bedah = 0;
                    @endphp
                    @foreach ($hewan as $item)
                        @php
                            $rawatJalan += $item->rawat_jalan;
                            $rawatInap += $item->rawat_inap;
                            $titipSehat += $item->titip_sehat;
                            $vaksin += $item->vaksin;
                            $grooming += $item->grooming;
                            $bedah += $item->bedah;
                        @endphp
                        <tr>
                            <td>{{ $item->name }}</td>

                            <td class="text-center">{{ number_format($item->rawat_jalan) }}</td>
                            <td class="text-center">{{ number_format($item->rawat_inap) }}</td>
                            <td class="text-center">{{ number_format($item->titip_sehat) }}</td>
                            <td class="text-center">{{ number_format($item->vaksin) }}</td>
                            <td class="text-center">{{ number_format($item->grooming) }}</td>
                            <td class="text-center">{{ number_format($item->bedah) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right"><b><i>Total</i></b></td>
                        <td class="text-center">{{ number_format($rawatJalan) }}</td>
                        <td class="text-center">{{ number_format($rawatInap) }}</td>
                        <td class="text-center">{{ number_format($titipSehat) }}</td>
                        <td class="text-center">{{ number_format($vaksin) }}</td>
                        <td class="text-center">{{ number_format($grooming) }}</td>
                        <td class="text-center">{{ number_format($bedah) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi2">
            <div class="flex justify-between">
                <h5 class="font-bold text-2xl">Laporan Jumlah Pasien Berdasarkan Tindakan</h5>
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
                    <th>Tindakan</th>
                    @foreach (binatang() as $item)
                        <th>{{ $item->name }}</th>
                    @endforeach
                    <th>Total</th>
                </thead>
                <tbody>
                    @php
                        $jumlahTotal = 0;
                        $count = [];
                        foreach (binatang() as $item1) {
                            $count[$item1->id] = 0;
                        }
                    @endphp
                    @foreach ($tindakan as $key => $item)
                        @php
                            $jumlah = 0;
                        @endphp
                        <tr>
                            <td>{{ $item->name }}</td>
                            @foreach (binatang() as $key1 => $item1)
                                @php
                                    $jumlah += $item['tindakan_' . $item1->id] + $item['bedah_' . $item1->id];
                                    $jumlahTotal += $item['tindakan_' . $item1->id] + $item['bedah_' . $item1->id];
                                    $count[$item1->id] += $item['tindakan_' . $item1->id] + $item['bedah_' . $item1->id];
                                @endphp
                                <td>
                                    {{ number_format($item['tindakan_' . $item1->id] + $item['bedah_' . $item1->id]) }}
                                </td>
                            @endforeach
                            <td class="text-center">{{ number_format($jumlah) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right" colspan="2"><b><i>Total</i></b></td>
                        @foreach (binatang() as $item)
                            <td class="text-right">{{ $count[$item->id] }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div id="xlsDownload" style="display: none"></div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.select2filter').select2({
                dropdownParent: $("#slide-over-filter .modal-body"),
            })
        })

        function filter(params) {
            @if (Auth::user()->akses('global'))
                location.href = '{{ route('laporan-jumlah-pasien') }}?tanggal_awal=' + $('#tanggal_awal').val() +
                    '&tanggal_akhir=' + $('#tanggal_akhir').val() + '&branch_id=' + $(
                        '#branch_id').val() + '&binatang_id=' + $('#binatang_id').val() + '&tindakan_id=' + $(
                        '#tindakan_id').val();
            @else
                location.href = '{{ route('laporan-jumlah-pasien') }}?tanggal_awal=' + $('#tanggal_awal').val() +
                    '&tanggal_akhir=' + $('#tanggal_akhir').val() + '&binatang_id=' + $('#binatang_id').val() +
                    '&tindakan_id=' + $('#tindakan_id').val();
            @endif
        }

        function openFilter() {
            slideOver.toggle();
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
