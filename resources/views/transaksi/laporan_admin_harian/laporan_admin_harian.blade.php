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

        @media (min-width: 768px) {
            .md\:col-span-4 {
                grid-column: span 4 / span 4;
            }
        }
    </style>
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="col-span-12  mb-3">
            <h5><b>Filter</b></h5>
        </div>
        @if (Auth::user()->akses('global'))
            <div class="col-span-12  md:col-span-3 mb-3">
                <label for="branch_id" class="form-label">Branch{{ dot() }}</label>
                <select name="branch_id" id="branch_id" class="select2filter form-control required">
                    <option value="">Pilih Branch</option>
                    @foreach (\App\Models\Branch::get() as $item)
                        <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
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
                    value="{{ Carbon\carbon::parse($req->tanggal_awal)->format('Y-m-d') }}" data-single-mode="true">
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
        <div class="col-span-12  md:col-span-3">
            <label for="branch_id" class="form-label block">&nbsp;</label>
            <button class="btn btn-primary shadow-md " onclick="filter()"><i class="fas fa-search"></i>&nbsp;Search</button>
        </div>
        <!-- BEGIN: Data List -->
        <div class="col-span-12" id="append-data">

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
            filter();
        })()


        function filter() {
            $.ajax({
                url: "{{ route('append-data-laporan-admin-harian') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id').val();
                    },
                    tanggal_awal() {
                        return $('#tanggal_awal').val();
                    },
                    tanggal_akhir() {
                        return $('#tanggal_akhir').val();
                    },
                },
                success: function(data) {
                    $('#append-data').html(data);
                },
                error: function(data) {
                    generateKode();
                }
            });
        }

        function printBuktiPembayaran(kasir_id, id) {
            window.open('{{ route('printBuktiPembayaranCicilan') }}?kasir_id=' + kasir_id + '&id=' + id);
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
