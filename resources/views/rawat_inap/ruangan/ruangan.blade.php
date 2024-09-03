@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('../rawat_inap/ruangan/filter_ruangan')
@endsection

@section('style')
    <style>
        .col-span-9 {
            grid-column: span 9/span 9 !important;
        }

        .list-group-item:first-child {
            border-top-left-radius: inherit;
            border-top-right-radius: inherit;
        }

        .list-group-item {
            background-color: var(--card-color);
            border-color: var(--border-color);
        }

        .list-group-item {
            position: relative;
            display: block;
            padding: 0.75rem 1.25rem;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, .125);
        }

        .list-group-item-action {
            width: 100%;
            color: #495057;
            text-align: inherit;
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


        #list-pasien .active {
            background: lightgrey !important;
        }

        .select-racikan {
            color: hsl(240, 1%, 68%);
        }

        .select-racikan.active {
            color: #c70039 !important;
        }
        
        input[type='file'].multiple-file { 
            width:100px; color:transparent; 
        }

        @media (min-width: 768px) {
            .md\:col-span-9 {
                grid-column: span 9 / span 9;
            }
        }
    </style>
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">Ruangan Rawat Inap Pasien</h2>
    <div class="grid grid-cols-12 gap-2 mt-5">
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
        <div
            class="intro-y col-span-12 md:col-span-3 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <div class="flex items-center col-span-12 border-b border-slate-200/60 dark:border-darkmode-400 px-3 py-2 text-left bg-warning rounded-t-lg"
                style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(247, 194, 21);height: 50px;">
                <a href="javascript:;" class="font-medium text-white w-full text-xl">Nama Ruang Rawat Inap</a>
            </div>
            <div class="col-span-12 pt-2">
                <div id="html1">

                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div
            class="intro-y col-span-12 md:col-span-9 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <div class="flex items-center col-span-12  border-b border-slate-200/60 dark:border-darkmode-400 px-3 py-2 text-left rounded-t-lg bg-primary"
                style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;height: 50px;">
                <a href="javascript:;" class="font-medium text-white w-full text-xl">List Pasien Rawat Inap</a>
            </div>
            <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
                <input type="hidden" id="jenis" name="jenis">
                <input type="hidden" id="value" name="value">
                <table class="table mt-2 stripe hover" id="table"
                    style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                    <thead align="center">
                        <th>Opsi</th>
                        <th>No Rekam Medis Rawat Inap</th>
                        <th>Nama Pasien</th>
                        <th>Nama Owner</th>
                        <th>Diagnosa</th>
                        <th>Nama Dokter</th>
                        <th>Status Form Persetujuan</th>
                    </thead>

                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <!-- END: Data List -->
    </div>
    @include('../rawat_inap/ruangan/modal')
@endsection
@section('script')
    <script>
        var xhr = [];
        var table;
        var indexRacikan = 1;
        var indexFileFormPersetujuan = 1;
        var indexFilePulangPaksa = 1;

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
                        url: "{{ route('datatableRuangan') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            jenis() {
                                return $('#jenis').val();
                            },
                            value() {
                                return $('#value').val();
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
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, {
                        data: 'kode',
                        name: 'kode'
                    }, {
                        data: 'pasien',
                        name: 'pasien'
                    }, {
                        data: 'owner',
                        name: 'owner'
                    }, {
                        data: 'diagnosa',
                        name: 'diagnosa'
                    }, {
                        data: 'dokter',
                        name: 'dokter',
                        class: 'text-left',
                    }, {
                        data: 'form_persetujuan',
                        name: 'form_persetujuan'
                    }, ]
                })
                .columns.adjust()
                .responsive.recalc();

            $('#myInputTextField').keyup(debounce(function() {
                table.search($(this).val()).draw();
            }, 500));

            $('.mask').maskMoney({
                precision: 0,
                thousands: ','
            })

            $('.maskdec').maskMoney({
                precision: 2,
                thousands: '',
                decimals: '.',
                allowZero: true,
            })


            // $('.select2').select2({
            //     dropdownParent: $("#modal-tambah-data .modal-body"),
            //     width: '100%',
            // })

            $('.select2filter').select2({
                dropdownParent: $("#slide-over-filter .modal-body"),
                width: '100%',
            })

            $('.select2resep').select2({
                dropdownParent: $("#modal-tambah-resep .modal-body"),
                width: '100%',
            })

            $('.select2pakan').select2({
                dropdownParent: $("#modal-tambah-pakan .modal-body"),
                width: '100%',
            })

            $('.select2itemNonObat').select2({
                dropdownParent: $("#modal-item-non-obat .modal-body"),
                width: '100%',
            })

            $('#status_urgent').select2({
                dropdownParent: $("#modal-rekomendasi-tindakan-bedah .modal-body"),
                width: '100%',
            })

            $(".rekomendasi_tanggal_bedah").each(function() {
                let options = {
                    autoApply: false,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
                    minDate: '{{ Carbon\carbon::now()->subDay(0)->format('Y-m-d') }}',
                    format: "YYYY-MM-DD",
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
                            generateAge($(this));
                        });
                    },
                });
            });

            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            tomGenerator('.tomSelect');

            $("#kamar_rawat_inap_dan_bedah_id").select2({
                width: '100%',
                dropdownParent: $("#modal-pindah-ruangan .modal-body .parent-pindah-kamar"),
                ajax: {
                    url: "{{ route('select2Ruangan') }}?param=kamar_rawat_inap_dan_bedah_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            rekam_medis_pasien_id: $('#rekam_medis_pasien_id').val(),
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 10) < data.total
                            }
                        };
                    },
                    cache: true,
                    type: 'GET',
                },
                placeholder: 'Pilih Ruangan',
                minimumInputLength: 0,
                templateResult: formatRepoKamar,
                templateSelection: formatRepoKamarSelection
            });

            $("#tindakan_id").select2({
                dropdownParent: $("#modal-tambah-tindakan .modal-body .parent-tindakan"),
                width: '100%',
                ajax: {
                    url: "{{ route('select2Ruangan') }}?param=tindakan_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            id: $('#rekam_medis_pasien_id').val(),
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 10) < data.total
                            }
                        };
                    },
                    cache: true,
                    type: 'GET',
                },
                placeholder: 'Pilih Tindakan',
                minimumInputLength: 0,
                templateResult: formatRepoStatus,
                templateSelection: formatRepoStatusSelection
            });

            $("#rekomendasi_tindakan_bedah").select2({
                dropdownParent: $("#modal-rekomendasi-tindakan-bedah .modal-body"),
                width: '100%',
                ajax: {
                    url: "{{ route('select2Ruangan') }}?param=tindakan_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            id: $('#rekam_medis_pasien_id').val(),
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 10) < data.total
                            }
                        };
                    },
                    cache: true,
                    type: 'GET',
                },
                placeholder: 'Pilih Jenis Tindakan Bedah',
                minimumInputLength: 0,
                templateResult: formatRepoStatus,
                templateSelection: formatRepoStatusSelection
            });

            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Drag and drop or click to replace',
                    'remove': 'Remove',
                    'error': 'Ooops, something wrong happended.'
                }
            });

            $("#addFileButtonFormPersetujuan").click(function() {
                const fileInput = $("<div>", {
                    class: "col-span-12 mt-2 parent"
                });
                const fileLabel = $("<label>", {
                    class: "form-label",
                    text: "File " + indexFileFormPersetujuan
                });
                const inputFile = $("<input>", {
                    type: "file",
                    class: "form-control mb-2 multiple-file file-input-field",
                    name: "form_persetujuan_file[]",
                    multiple: true,
                    "data-id": indexFileFormPersetujuan,
                    style: "border-radius: unset;"
                });
                const labelFile = $("<label>", {
                    id: "label-filename" + indexFileFormPersetujuan,
                    text: 'No File Choosen'
                });
                const inputSeq = $("<input>", {
                    type: "hidden",
                    class: "form-control seq-input-field",
                    name: "form_persetujuan_seq[]"
                });
                const previewLink = $("<a>", {
                    href: "javascript:void(0);",
                    class: "preview-link",
                    text: "Preview File " + indexFileFormPersetujuan,
                    style: "text-decoration: underline;"
                });

                fileInput.append(fileLabel, '<br>', inputFile, labelFile, '<br>', inputSeq, previewLink);
                $("#fileContainerFormPersetujuan").append(fileInput);

                indexFileFormPersetujuan++;
            });

            $("#fileContainerFormPersetujuan").on("click", ".preview-link", function(e) {
                e.preventDefault();
                const fileInput = $(this).siblings(".file-input-field");
                if (fileInput[0].files.length > 0) {
                    const file = fileInput[0].files[0];
                    const url = URL.createObjectURL(file);
                    window.open(url);
                }else{
                    if($(this).attr("href") != "javascript:void(0);"){
                        window.open($(this).attr("href"));
                    }
                }
            });

            $("#fileContainerFormPersetujuan").on("change", ".file-input-field", function() {
                const previewLink = $(this).siblings(".preview-link");
                const seqData = $(this).siblings(".seq-input-field");
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    $('#label-filename' + $(this).data('id')).html(fileName);
                    seqData.val($(this).data('id'));
                    previewLink.attr("href", "javascript:void(0);");
                }
            });

            $("#reset-form-persetujuan").click(function() {
                $('#fileContainerFormPersetujuan').empty();
                indexFileFormPersetujuan = 1;

                const fileInput = $("<div>", {
                    class: "col-span-12 mt-2 parent"
                });
                const fileLabel = $("<label>", {
                    class: "form-label",
                    text: "File " + indexFileFormPersetujuan
                });
                const inputFile = $("<input>", {
                    type: "file",
                    class: "form-control mb-2 multiple-file file-input-field",
                    name: "form_persetujuan_file[]",
                    multiple: true,
                    "data-id": indexFileFormPersetujuan,
                    style: "border-radius: unset;"
                });
                const labelFile = $("<label>", {
                    id: "label-filename" + indexFileFormPersetujuan,
                    text: 'No File Choosen'
                });
                const inputSeq = $("<input>", {
                    type: "hidden",
                    class: "form-control seq-input-field",
                    name: "form_persetujuan_seq[]"
                });
                const previewLink = $("<a>", {
                    href: "javascript:void(0);",
                    class: "preview-link",
                    text: "Preview File " + indexFileFormPersetujuan,
                    style: "text-decoration: underline;"
                });

                fileInput.append(fileLabel, '<br>', inputFile, labelFile, '<br>', inputSeq, previewLink);
                $("#fileContainerFormPersetujuan").append(fileInput);

                indexFileFormPersetujuan++;

                $('#form_persetujuan_function').val('New');
            });

            $("#addFileButtonPulangPaksa").click(function() {
                const fileInputPP = $("<div>", {
                    class: "col-span-12 mt-2 parent-pulang-paksa"
                });
                const fileLabelPP = $("<label>", {
                    class: "form-label",
                    text: "File " + indexFilePulangPaksa
                });
                const inputFilePP = $("<input>", {
                    type: "file",
                    class: "form-control mb-2 multiple-file file-input-field-pp",
                    name: "pulang_paksa_file[]",
                    multiple: true,
                    "data-id": indexFilePulangPaksa,
                    style: "border-radius: unset;"
                });
                const labelFilePP = $("<label>", {
                    id: "label-filename-pp" + indexFilePulangPaksa,
                    text: 'No File Choosen'
                });
                const inputSeqPP = $("<input>", {
                    type: "hidden",
                    class: "form-control seq-input-field-pp",
                    name: "pulang_paksa_seq[]"
                });
                const previewLinkPP = $("<a>", {
                    href: "javascript:void(0);",
                    class: "preview-link-pp",
                    text: "Preview File " + indexFilePulangPaksa,
                    style: "text-decoration: underline;"
                });

                fileInputPP.append(fileLabelPP, '<br>', inputFilePP, labelFilePP, '<br>', inputSeqPP, previewLinkPP);
                $("#fileContainerPulangPaksa").append(fileInputPP);

                indexFilePulangPaksa++;
            });

            $("#fileContainerPulangPaksa").on("click", ".preview-link-pp", function(e) {
                e.preventDefault();
                const fileInput = $(this).siblings(".file-input-field-pp");
                if (fileInput[0].files.length > 0) {
                    const file = fileInput[0].files[0];
                    const url = URL.createObjectURL(file);
                    window.open(url);
                }else{
                    if($(this).attr("href") != "javascript:void(0);"){
                        window.open($(this).attr("href"));
                    }
                }
            });

            $("#fileContainerPulangPaksa").on("change", ".file-input-field-pp", function() {
                const previewLinkPP = $(this).siblings(".preview-link-pp");
                const seqDataPP = $(this).siblings(".seq-input-field-pp");
                if (this.files.length > 0) {
                    const fileNamePP = this.files[0].name;
                    $('#label-filename-pp' + $(this).data('id')).html(fileNamePP);
                    seqDataPP.val($(this).data('id'));
                    previewLinkPP.attr("href", "javascript:void(0);");
                }
            });

            $("#reset-pulang-paksa").click(function() {
                $('#fileContainerPulangPaksa').empty();
                indexFilePulangPaksa = 1;

                const fileInputPP = $("<div>", {
                    class: "col-span-12 mt-2 parent-pulang-paksa"
                });
                const fileLabelPP = $("<label>", {
                    class: "form-label",
                    text: "File " + indexFilePulangPaksa
                });
                const inputFilePP = $("<input>", {
                    type: "file",
                    class: "form-control mb-2 multiple-file file-input-field-pp",
                    name: "pulang_paksa_file[]",
                    multiple: true,
                    "data-id": indexFilePulangPaksa,
                    style: "border-radius: unset;"
                });
                const labelFilePP = $("<label>", {
                    id: "label-filename-pp" + indexFilePulangPaksa,
                    text: 'No File Choosen'
                });
                const inputSeqPP = $("<input>", {
                    type: "hidden",
                    class: "form-control seq-input-field-pp",
                    name: "pulang_paksa_seq[]"
                });
                const previewLinkPP = $("<a>", {
                    href: "javascript:void(0);",
                    class: "preview-link-pp",
                    text: "Preview File " + indexFilePulangPaksa,
                    style: "text-decoration: underline;"
                });

                fileInputPP.append(fileLabelPP, '<br>', inputFilePP, labelFilePP, '<br>', inputSeqPP, previewLinkPP);
                $("#fileContainerPulangPaksa").append(fileInputPP);

                indexFilePulangPaksa++;
            });

            getListRuangan();
        })()

        function openFilePreview(file) {
            console.log(file);
            var reader = new FileReader();
            reader.onload = function (event) {
                console.log(event);
                var mimeType = file.type;
                if (mimeType.startsWith('image/')) {
                    // Image file: Open image in new tab
                    var img = new Image();
                    img.src = event.target.result;
                    var win = window.open('', '_blank');
                    win.document.write(img.outerHTML);
                } else if (mimeType === 'application/pdf') {
                    // PDF file: Open PDF in new tab using PDF.js
                    var pdfData = event.target.result;
                    var pdfBlob = new Blob([pdfData], { type: 'application/pdf' });
                    var pdfUrl = URL.createObjectURL(pdfBlob);

                    var win = window.open(pdfUrl, '_blank');
                    if (win) {
                        win.focus();
                    } else {
                        alert('Please allow pop-ups for this website to view the PDF.');
                    }
                } else {
                    // Unsupported file type: Show an alert
                    alert('Unsupported file type. Unable to preview the file.');
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function getListRuangan() {
            $.ajax({
                url: "{{ route('getListRuanganRekamMedis') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id_filter').val();
                    },
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    $("#html1").jstree("destroy");
                    $("#html1").html(data);
                    $('#html1').jstree();
                    $('#html1').jstree("open_all");
                    $('#html1').on('changed.jstree', function(e, data) {
                        $('#jenis').val(data.node.data.jenis);
                        $('#value').val(data.node.data.value);
                        table.ajax.reload();
                    }).jstree();
                },
                error: function(data) {
                    getListRuangan();
                }
            });
        }


        function getListRekamMedis(pasienActive) {
            $.ajax({
                url: "{{ route('getListRekamMedisPemeriksaanPasien') }}",
                type: 'get',
                data: {
                    id: pasienActive,
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    $("#list-rekam-medis").html(data);
                },
                error: function(data) {}
            });
        }

        function lihatRekamMedis(id) {
            $.ajax({
                url: "{{ route('getRekamMedisPasien') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    $('#append-rekam-medis-history').html(data);
                    const el = document.querySelector("#modal-rekam-medis-history");
                    const modalRekamMedis = tailwind.Modal.getOrCreateInstance(el);
                    modalRekamMedis.toggle();
                },
                error: function(data) {
                    // lihatRekamMedis(id);
                }
            });
        }

        function openFilter() {
            slideOver.toggle();
        }

        function filter() {
            slideOver.toggle();
            table.ajax.reload();
            getListRuangan();
        }

        function formatRepoNormalSelection(repo) {
            return repo.text || repo.text;
        }

        function formatRepoNormal(repo) {
            if (repo.loading) {
                return repo.text;
            }
            // scrolling can be used
            var markup = $('<span  data-name=' + repo.name + ' value=' + repo.id + '>' + repo.text + '</span>');
            return markup;
        }

        function refreshingData(id) {
            overlay(true);
            $.ajax({
                url: "{{ route('getRekamMedisPasienRuangan') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    idPasien = id;
                    $('#append-rekam-medis').html(data);
                    getListRekamMedis(id);
                    overlay(false);
                },
                error: function(data) {
                    overlay(false);
                    refreshingData(id);
                }
            });
        }

        function openModal(id) {
            $.ajax({
                url: "{{ route('getRekamMedisPasienRuangan') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    idPasien = id;
                    $('#append-rekam-medis').html(data);
                    const el = document.querySelector("#modal-rekam-medis");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    getListRekamMedis(id);
                    modal.toggle();
                },
                error: function(data) {
                    openModal(id);
                }
            });
        }

        function formatRepoKamar(repo) {
            if (repo.loading) {
                return repo.text;
            }

            if (repo.name != undefined) {

                var $container = $(
                    "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-result-repository__avatar'><img style='" +
                    "object-fit:cover" +
                    "' src='https://hope.be/wp-content/uploads/2015/05/no-user-image.gif' /></div>" +
                    "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'></div>" +
                    "<div class='select2-result-repository__description'></div>" +
                    "<div class='select2-result-repository__statistics'>" +
                    "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> </div>" +
                    "<div class='select2-result-repository__stargazers'><i class='fa fa-bed'></i> </div>" +
                    "<div class='select2-result-repository__watchers'><i class='fa fa-code-fork'></i> </div>" +
                    "</div>" +
                    "</div>" +
                    "</div>"
                );

                $container.find(".select2-result-repository__title").text(repo.name);
                $container.find(".select2-result-repository__description").text(repo.description);
                $container.find(".select2-result-repository__forks").append(repo.kategori_kamar.name);
                $container.find(".select2-result-repository__stargazers").append(repo.terpakai + '/' + repo.kapasitas);
                $container.find(".select2-result-repository__watchers").append(repo.branch.kode);

                return $container;
            } else {
                // scrolling can be used
                var markup = $('<span value=' + repo.id + '>' + repo.text + '</span>');

                return markup;
            }
        }

        function formatRepoKamarSelection(repo) {
            if (repo.terpakai != undefined) {
                return repo.text + ' | ' + repo.terpakai + '/' + repo.kapasitas;
            } else {
                return repo.text;
            }
        }

        function appendResep() {
            $('#add-resep').addClass('disabled');
            $(".loading-resep").removeClass('hidden');
            $.ajax({
                url: "{{ route('tambahResepRuangan') }}",
                type: 'get',
                data: {
                    index: indexRacikan,
                    id: $('#rekam_medis_pasien_id').val(),
                },
                success: function(data) {
                    $('#append-resep').append(data)
                    $(".loading-resep").addClass('hidden');

                    $('.select2resep').select2({
                        dropdownParent: $("#modal-tambah-resep .modal-body"),
                        width: '100%',
                    })

                    $('.mask').maskMoney({
                        precision: 0,
                        thousands: ',',
                        allowZero: true,
                    })

                    // $('.mask-non-decimal').maskMoney({
                    //     precision: 0,
                    //     thousands: '',
                    //     allowZero: true,
                    // })
                    indexRacikan++;
                    $('#add-resep').removeClass('disabled');
                },
                error: function(data) {
                    appendResep();
                    $(".loading-resep").addClass('hidden');
                }
            });
        }

        function tambahChildRacikan(child) {
            var parent = $(child).parents('.parent-resep');
            $.ajax({
                url: "{{ route('tambahRacikanChildRuangan') }}",
                type: 'get',
                data: {
                    index: $(parent).find('.index_racikan').val(),
                    id: $('#rekam_medis_pasien_id').val(),
                },
                success: function(data) {
                    $(parent).find('.append-racikan').append(data);

                    $('.select2resep').select2({
                        dropdownParent: $("#modal-tambah-resep .modal-body"),
                        width: '100%',
                    })

                    // $('.mask-non-decimal').maskMoney({
                    //     precision: 0,
                    //     thousands: '',
                    //     allowZero: true,
                    // })

                },
                error: function(data) {
                    tambahResep();
                    $(".loading-resep").addClass('hidden');
                }
            });
        }

        $(document).on('click', '.select-racikan', function() {
            var par = $(this).parents('.parent-resep');
            var name = $(this).data('name');
            $(par).find('.select-racikan').removeClass('active');
            $(par).find('.racikan-child').addClass('hidden');

            $(this).addClass('active')
            $(par).find('.' + name).removeClass('hidden');
            $(par).find('.' + name).addClass('active');
            $(par).find('.parent_resep').val(name);
        })

        function pindahKamar() {
            var validation = 0;

            $('#modal-pindah-ruangan .required').each(function() {
                var par = $(this).parents('.parent-pindah-kamar');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null) {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        $(par).find('.select2-container').addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-pindah-ruangan').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;

            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);


                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahDiagnosa() {
            var validation = 0;
            $('#modal-tambah-diagnosa .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-tambah-diagnosa').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());
            formData.append('id_rekam_medis', idRekamMedis);

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-tambah-diagnosa');
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);


                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahCatatan() {
            var validation = 0;
            $('#modal-tambah-catatan .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-tambah-catatan').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());
            formData.append('id_rekam_medis', idRekamMedis);

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-tambah-catatan')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);


                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahKondisiHarian() {
            var validation = 0;
            $('#modal-tambah-kondisi-harian .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-tambah-kondisi-harian').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-tambah-kondisi-harian')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);


                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahTindakan() {
            var validation = 0;
            $('#modal-tambah-tindakan .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-tambah-tindakan').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());
            formData.append('id_rekam_medis', idRekamMedis);

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-tambah-tindakan')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);


                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahHasilLab() {
            var validation = 0;
            $('#modal-tambah-hasil-lab .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if ($('#dropify')[0].files[0] == undefined) {
                console.log($('#dropify')[0].files[0]);
                ToastNotification('warning', 'Hasil lab harus diisi');
                return false;
            }

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-tambah-hasil-lab').serializeArray();

            var input = document.getElementById("dropify");
            if (input != null) {
                file = input.files[0];
                formData.append("hasil_lab[]", file);
            }

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-tambah-hasil-lab')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);


                        },
                        error: function(data) {
                            overlay(false);
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

        // $('#modal-form-persetujuan').on('hidden.bs.modal', function () {
        //     console.log('Yes');
        // })

        function openModalData(modal) {
            rekamMedisPasienId = null;
            idRekamMedis = null;
            paramModal = null;

            $('#fileContainerPulangPaksa').empty();
            indexFilePulangPaksa = 1;

            $("#addFileButtonPulangPaksa").click();

            const el = document.querySelector(modal);
            const modalRekamMedis = tailwind.Modal.getOrCreateInstance(el);
            $('.parent-resep').remove();
            modalRekamMedis.toggle();
        }

        function tambahResep() {
            var validation = 0;

            $('#modal-tambah-resep .required').each(function() {
                var par = $(this).parents('.parent');
                var parentResep = $(this).parents('.racikan-child');
                console.log(parentResep.length);
                if (!$(par).hasClass('hidden') && parentResep.length == 0) {
                    if ($(this).val() == '' || $(this).val() == null) {
                        $(this).addClass('is-invalid');
                        $(par).find('.select2-container').addClass('is-invalid');
                        validation++
                        console.log($(this))
                    }
                }

                if (parentResep.length > 0 && !$(parentResep).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null) {
                        $(this).addClass('is-invalid');
                        $(par).find('.select2-container').addClass('is-invalid');
                        validation++
                        console.log($(this))
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-tambah-resep').serializeArray();

            if ($('.parent-resep').length == 0) {
                ToastNotification('warning', "Minimal harus mengisi satu resep.");
                return false;
            }

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    overlay(true);
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-tambah-resep')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);

                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahPakan() {
            var validation = 0;
            $('#modal-tambah-pakan .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-tambah-pakan').serializeArray();

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);
                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-tambah-pakan')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);

                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahItemNonObat() {
            var validation = 0;
            $('#modal-item-non-obat  .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-item-non-obat').serializeArray();

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-item-non-obat')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);

                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahPasienMeninggal() {
            var validation = 0;
            $('#modal-item-non-obat  .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-item-non-obat').serializeArray();

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-item-non-obat')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);

                        },
                        error: function(data) {
                            overlay(false);
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

        function tambahRekomendasiTindakanBedah() {
            var validation = 0;
            $('#modal-rekomendasi-tindakan-bedah .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })


            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-rekomendasi-tindakan-bedah').serializeArray();

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                refreshingData($('#rekam_medis_pasien_id').val());
                                openModalData('#modal-rekomendasi-tindakan-bedah')
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);

                        },
                        error: function(data) {
                            overlay(false);
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

        function pasienMeninggal() {
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('jenis', 'pasien_meninggal');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Proses pasien meninggal?",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                const el = document.querySelector("#modal-rekam-medis");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
                                getListRuangan();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);

                            table.ajax.reload();
                        },
                        error: function(data) {
                            overlay(false);
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

        function bolehPulang() {
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('jenis', 'boleh_pulang');
            formData.append('id', $('#rekam_medis_pasien_id').val());

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Proses boleh pulang",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')

                                const el = document.querySelector("#modal-rekam-medis");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
                                getListRuangan();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);

                            table.ajax.reload();
                        },
                        error: function(data) {
                            overlay(false);
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

        function pulangPaksa() {
            var validation = 0;
            $('#modal-pulang-paksa .required').each(function() {
                var par = $(this).parents('.parent-pulang-paksa');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        $(par).find('.dropify-wrapper').addClass('is-invalid');
                        validation++
                    } else {
                        $(par).find('.dropify-wrapper').removeClass('is-invalid');
                    }
                } else {
                    $(par).find('.dropify-wrapper').removeClass('is-invalid');
                }
            })

            if ($('input[name="pulang_paksa_file[]"]')[0].files.length === 0) {
                ToastNotification('warning', 'File pulang paksa harus diisi');
                return false;
            }

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('jenis', 'pulang_paksa');
            formData.append('id', $('#rekam_medis_pasien_id').val());
            formData.append('alasan_pulang_paksa', $('#alasan_pulang_paksa').val());

            $('.file-input-field-pp').each(function(index, input) {
                for (var i = 0; i < input.files.length; i++) {
                    formData.append('pulang_paksa_file[]', input.files[i]);
                }
            });

            $('.seq-input-field-pp').each(function(index, input) {
                formData.append('pulang_paksa_seq[]', input.value);
            });

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Proses pulang paksa?",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                $('.modal-data').find('select').trigger('change.select2')
                                openModalData('#modal-pulang-paksa');
                                openModalData('#modal-rekam-medis');
                                getListRuangan();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);

                            table.ajax.reload();
                        },
                        error: function(data) {
                            overlay(false);
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

        function formatRepoStatus(repo) {
            if (repo.loading) {
                return repo.text;
            }
            console.log(repo);
            // scrolling can be used
            var markup = $('<span value=' + repo.id + '>' + repo.text + '</span>');
            return markup;
        }

        function formatRepoStatusSelection(repo) {
            return repo.text || repo.text;
        }
        function printTindakanBedah() {
            window.open('{{ route('printRuangan') }}?id=' + $('#rekam_medis_pasien_id').val()+'&from=BEDAH');
        }

        function printPulangPaksa() {
            window.open('{{ route('printPulangPaksaRuangan') }}?id=' + $('#rekam_medis_pasien_id').val());
        }

        function formPersetujuan(id, file) {
            $('#form_persetujuan_id').val(id);
            $('.sudah-upload').addClass('hidden');
            $('.belum-upload').removeClass('hidden');
            $('#fileContainerFormPersetujuan').empty();
            indexFileFormPersetujuan = 1;

            $.ajax({
                url: '{{ route('editFormPersetujuan') }}',
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                type: 'get',
                success: function(data) {
                    if(data.data.length > 0){
                        $('.sudah-upload').removeClass('hidden');
                        $('.belum-upload').addClass('hidden');

                        data.data.forEach(function(value, index) {
                            const filePath = value.file;
                            const fileName = filePath.substring(filePath.lastIndexOf('/') + 1);

                            const fileInput = $("<div>", {
                                class: "col-span-12 mt-2 parent"
                            });
                            const fileLabel = $("<label>", {
                                class: "form-label",
                                text: "File " + indexFileFormPersetujuan
                            });
                            const inputFile = $("<input>", {
                                type: "file",
                                class: "form-control mb-2 multiple-file file-input-field",
                                name: "form_persetujuan_file[]",
                                multiple: true,
                                "data-id": indexFileFormPersetujuan,
                                style: "border-radius: unset;"
                            });
                            const labelFile = $("<label>", {
                                id: "label-filename" + indexFileFormPersetujuan,
                                text: fileName
                            });
                            const inputSeq = $("<input>", {
                                type: "hidden",
                                class: "form-control seq-input-field",
                                name: "form_persetujuan_seq[]"
                            });
                            const previewLink = $("<a>", {
                                href: "{{ url('/') }}/" + value.file,
                                class: "preview-link",
                                text: "Preview File " + indexFileFormPersetujuan,
                                style: "text-decoration: underline;"
                            });

                            fileInput.append(fileLabel, '<br>', inputFile, labelFile, '<br>', inputSeq, previewLink);
                            $("#fileContainerFormPersetujuan").append(fileInput);

                            indexFileFormPersetujuan++;
                        });

                        $('#form_persetujuan_function').val('Edit');
                    }else{
                        const fileInput = $("<div>", {
                            class: "col-span-12 mt-2 parent"
                        });
                        const fileLabel = $("<label>", {
                            class: "form-label",
                            text: "File " + indexFileFormPersetujuan
                        });
                        const inputFile = $("<input>", {
                            type: "file",
                            class: "form-control mb-2 multiple-file file-input-field",
                            name: "form_persetujuan_file[]",
                            multiple: true,
                            "data-id": indexFileFormPersetujuan,
                            style: "border-radius: unset;"
                        });
                        const labelFile = $("<label>", {
                            id: "label-filename" + indexFileFormPersetujuan,
                            text: 'No File Choosen'
                        });
                        const inputSeq = $("<input>", {
                            type: "hidden",
                            class: "form-control seq-input-field",
                            name: "form_persetujuan_seq[]"
                        });
                        const previewLink = $("<a>", {
                            href: "javascript:void(0);",
                            class: "preview-link",
                            text: "Preview File " + indexFileFormPersetujuan,
                            style: "text-decoration: underline;"
                        });
                        

                        fileInput.append(fileLabel, '<br>', inputFile, labelFile, '<br>', inputSeq, previewLink);
                        $("#fileContainerFormPersetujuan").append(fileInput);

                        indexFileFormPersetujuan++;
                        
                        $('#form_persetujuan_function').val('New');
                    }
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

            const el = document.querySelector("#modal-form-persetujuan");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
        }

        function printFormPersetujuan() {
            var id = $('#form_persetujuan_id').val();
            $.ajax({
                url: '{{ route('editFormPersetujuan') }}',
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                type: 'get',
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        window.open('{{ url('/') }}/' + data.data.upload_form_persetujuan);
                    } else if (data.status == 2) {
                        Swal.fire({
                            title: data.message,
                            icon: "warning",
                        });
                    }
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

        function deleteData(param, rekam_medis_pasien_id, id) {
            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Hapus Data",
                text: "Data yang telah dihapus tidak bisa dikembalikan.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);
                    $.ajax({
                        url: '{{ route('deleteRekamMedis') }}',
                        data: {
                            jenis: param,
                            rekam_medis_pasien_id: rekam_medis_pasien_id,
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });
                                clear();
                                refreshingData($('#rekam_medis_pasien_id').val());
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);
                        },
                        error: function(data) {
                            overlay(false);
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

        function uploadFormPersetujuan() {
            var validation = 0;

            if($('#form_persetujuan_function').val() == 'New'){
                if ($('input[name="form_persetujuan_file[]"]')[0].files.length === 0) {
                    ToastNotification('warning', 'File form persetujuan harus diisi');
                    return false;
                }
            }

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-form-persetujuan').serializeArray();

            $('.file-input-field').each(function(index, input) {
                for (var i = 0; i < input.files.length; i++) {
                    formData.append('form_persetujuan_file[]', input.files[i]);
                }
            });

            data.forEach((d, i) => {
                if(d.value != null && d.value != ''){
                    formData.append(d.name, d.value);
                }
            })

            formData.append('_token', '{{ csrf_token() }}');

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);

                    $.ajax({
                        url: '{{ route('storeRuangan') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: 'success',
                                });

                                $('.sudah-upload').removeClass('hidden')
                                $('.belum-upload').addClass('hidden');
                                table.ajax.reload();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            }
                            overlay(false);
                        },
                        error: function(data) {
                            overlay(false);
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
    </script>
@endsection
