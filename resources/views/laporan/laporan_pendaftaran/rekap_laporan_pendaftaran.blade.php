@extends('../layout/' . $layout)
@section('content_filter')
    @include('laporan.laporan_pendaftaran.filter_laporan_pendaftaran')
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
        <div class="intro-y col-span-12 p-8 my-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
            id="isi2">
            <div class="col-span-12 text-right  mb-3">
                <button class="btn btn-primary shadow-md mr-2" onclick="excel2()">Export Excel</button>
            </div>
            <table class="table mt-2 stripe hover table-bordered" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Kode Antrean</th>
                    <th>Nama Pendaftar</th>
                    <th>Nama Owner</th>
                    <th>Nama Pasien</th>
                    <th>Nama Dokter</th>
                    <th>Poli</th>
                    <th>Branch</th>
                    <th>Status Owner</th>
                    <th>Status</th>
                    <th>Tanggal & Jam Daftar</th>
                    <th>Tanggal & Jam Pick Up Pasien</th>
                    <th>Tanggal & Jam Selesai Periksa</th>
                    <th>Batal By</th>
                </thead>

            </table>
        </div>
        <div id="xlsDownload" style="display: none"></div>
        <!-- END: Data List -->
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
                        url: "{{ route('datatableLaporanPendaftaran') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                            tanggal_periksa_awal() {
                                return $('#tanggal_periksa_awal').val();
                            },
                            tanggal_periksa_akhir() {
                                return $('#tanggal_periksa_akhir').val();
                            },
                            jam_pickup() {
                                return $('#jam_pickup').val();
                            },
                            dokter_id() {
                                return $('#dokter_id').val();
                            },
                            binatang_id() {
                                return $('#binatang_id_filter').val();
                            },
                            owner_id() {
                                return $('#owner_id_filter').val();
                            },
                            poli_id() {
                                return $('#poli_id_filter').val();
                            }
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center'
                    }, {
                        data: 'kode_pendaftaran',
                        name: 'kode_pendaftaran',
                        class: 'text-center'
                    }, {
                        data: 'created_by',
                        name: 'created_by',
                        class: 'text-center'
                    }, {
                        data: 'owner',
                        name: 'owner',
                        class: 'text-center'
                    }, {
                        data: 'pasien',
                        name: 'pasien',
                        class: 'text-center'
                    }, {
                        data: 'dokter_periksa',
                        name: 'dokter_periksa',
                        class: 'text-center'
                    }, {
                        data: 'poli',
                        name: 'poli',
                        class: 'text-center'
                    }, {
                        data: 'branch',
                        name: 'branch',
                        class: 'text-center'
                    }, {
                        data: 'status_owner',
                        name: 'status_owner',
                        class: 'text-center'
                    }, {
                        data: 'status',
                        name: 'status',
                        class: 'text-center'
                    }, {
                        
                        data: 'created_at',
                        name: 'created_at',
                        class: 'text-center'
                    }, {
                        data: 'jam_pickup',
                        name: 'jam_pickup',
                        class: 'text-center'
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        class: 'text-center'
                    }, {
                        data: 'updated_by',
                        name: 'updated_by',
                        class: 'text-center'
                    },]
                })
                .columns.adjust()
                .responsive.recalc();

            $('.select2').select2({
                width: '100%',
            })
        })()

        $('#myInputTextField').keyup(debounce(function() {
            table.search($(this).val()).draw();
        }, 500));

        function openFilter(params) {
            slideOver.toggle();
        }

        function filter(params) {
            slideOver.toggle();
            table.ajax.reload();
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

        $(document).ready(function() {
            $('.select2filter').select2({
                dropdownParent: $("#slide-over-filter .modal-body"),
                width: '100%',
            })
        })

        // function filter(params) {
        //     table.ajax.reload()
        // }
    </script>
@endsection
