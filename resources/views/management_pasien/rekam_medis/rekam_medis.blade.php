@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('management_pasien.rekam_medis.filter_rekam_medis')
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
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <table class="table mt-2 stripe hover" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>Opsi</th>
                    <th>No Historis RM</th>
                    <th>Pasien</th>
                    <th>Owner</th>
                    <th>Dokter Periksa</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Keluar</th>
                    <th>Tindakan Medis</th>
                    <th>Jumlah Data</th>
                </thead>

                <tbody>

                </tbody>
            </table>
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
    @include('../management_pasien/rekam_medis/modal')
@endsection
@section('script')
    <script>
        var xhr = [];
        var table;
        var indexRacikan;
        var rekam_medis_pasien_id;
        var indexFileFormPersetujuan = 1;
        var indexFilePulangPaksa = 1;
        (function() {
            table = $('#table').DataTable({
                    // searching: false,
                    processing: true,
                    serverSide: true,
                    dom: 'Btip',
                    buttons: [
                        $.extend(true, {}, {
                            extend: 'csv',
                            className: 'btn btn-primary'
                        }),
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
                        url: "{{ route('datatableRekamMedis') }}",
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
                            status_kepulangan() {
                                return $('#status_kepulangan').val();
                            },
                            tanggal_periksa_awal() {
                                return $('#tanggal_periksa_awal').val();
                            },
                            tanggal_periksa_akhir() {
                                return $('#tanggal_periksa_akhir').val();
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
                        data: 'mp_pasien_name',
                        name: 'mp_pasien.name',
                        orderable: false,
                    }, {
                        data: 'mp_owner_name',
                        name: 'mp_pasien.mp_owner.name',
                        orderable: false,
                    }, {
                        data: 'dokter_name',
                        name: 'users.name',
                        orderable: false,
                    }, {
                        data: 'tanggal_masuk',
                        name: 'tanggal_masuk',
                        class: 'text-left',
                    }, {
                        data: 'tanggal_keluar',
                        name: 'tanggal_keluar',
                        class: 'text-left',
                    }, {
                        data: 'tindakan_medis',
                        name: 'tindakan_medis',
                        class: 'text-left',
                    }, {
                        data: 'jumlah_data',
                        name: 'jumlah_data',
                        class: 'text-left',
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

            $('.select2filter').select2({
                dropdownParent: $("#slide-over-filter .modal-body"),
                width: '100%',
            })

            $("#ras_id").select2({
                width: '100%',
                dropdownParent: $("#modal-tambah-data .modal-body"),
                ajax: {
                    url: "{{ route('select2RekamMedis') }}?param=ras_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            binatang_id() {
                                return $('#binatang_id').val();
                            },
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
                placeholder: 'Pilih Binatang Dahulu',
                minimumInputLength: 0,
                templateResult: formatRepoStatus,
                templateSelection: formatRepoStatusSelection
            });

            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");
            $('.dropify').dropify();

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


            $(".rekomendasi_tanggal_bedah").each(function() {
                let options = {
                    autoApply: false,
                    singleMode: false,
                    numberOfColumns: 2,
                    numberOfMonths: 2,
                    showWeekNumbers: true,
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

            tomGenerator('.tomSelect');

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

                $('#pulang_paksa_function').val('New');
            });
        })()

        function openFilter() {
            slideOver.toggle();
        }

        function filter() {
            slideOver.toggle();
            table.ajax.reload();
        }

        function edit(id) {
            $.ajax({
                url: '{{ route('editRekamMedis') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        if ($('#' + key).length != 0) {
                            if (!$('#' + key).hasClass('dropify')) {
                                $('#' + key).val(temp_value[key]);
                            }
                        }
                    }

                    if (data.data.ras != null) {
                        var newOption = new Option(data.data.ras.name,
                            data.data.ras.id,
                            true,
                            true
                        );

                        $('#ras_id').append(newOption).trigger('change');
                    }
                    var url = "{{ url('/') }}" + '/' + data.data.image;
                    var imagenUrl = url;
                    var drEvent = $('.dropify').dropify({
                        defaultFile: imagenUrl,
                    });

                    drEvent = drEvent.data('dropify');
                    drEvent.resetPreview();
                    drEvent.clearElement();
                    drEvent.settings.defaultFile = imagenUrl;
                    drEvent.destroy();
                    drEvent.init();
                    $('.not-editable').prop('readonly', true);
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    $('#simpan').removeClass('hidden');
                    $('.parent').removeClass('disabled');

                    const el = document.querySelector("#modal-tambah-data");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                },
                error: function(data) {
                    var html = '';
                    Object.keys(data.responseJSON).forEach(element => {
                        html += data.responseJSON[element][0] + '<br>';
                    });
                    swal({
                        title: 'Ada Kesalahan !!!',
                        html: data.responseJSON.message == undefined ? html : data
                            .responseJSON.message,
                        icon: "error",
                    });
                }
            });
        }

        function lihat(id) {
            $.ajax({
                url: '{{ route('editRekamMedis') }}',
                data: {
                    id,
                    param: 'lihat',
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        if ($('#' + key).length != 0) {
                            if (!$('#' + key).hasClass('dropify')) {
                                $('#' + key).val(temp_value[key]);
                            }
                        }
                    }

                    $('.parent').addClass('disabled');

                    if (data.data.ras != null) {
                        var newOption = new Option(data.data.ras.name,
                            data.data.ras.id,
                            true,
                            true
                        );

                        $('#ras_id').append(newOption).trigger('change');
                    }
                    var url = "{{ url('/') }}" + '/' + data.data.image;
                    var imagenUrl = url;
                    var drEvent = $('.dropify').dropify({
                        defaultFile: imagenUrl,
                    });

                    drEvent = drEvent.data('dropify');
                    drEvent.resetPreview();
                    drEvent.clearElement();
                    drEvent.settings.defaultFile = imagenUrl;
                    drEvent.destroy();
                    drEvent.init();
                    const el = document.querySelector("#modal-tambah-data");
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                    $('#simpan').addClass('hidden');
                },
                error: function(data) {
                    lihat(id);
                }
            });
        }

        function openModal(id) {
            $.ajax({
                url: "{{ route('getRekamMedisRekamMedis') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    idPasien = id;
                    $('#append-rekam-medis').html(data);
                    openModalData("#modal-rekam-medis");
                },
                error: function(data) {
                    openModal(id);
                }
            });
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

            // if (file) {
            //     var url = "{{ url('/') }}" + '/' + file;
            //     var imagenUrl = url;
            //     var drEvent = $('#form_persetujuan').dropify({
            //         defaultFile: imagenUrl,
            //     });

            //     drEvent = drEvent.data('dropify');
            //     drEvent.resetPreview();
            //     drEvent.clearElement();
            //     drEvent.settings.defaultFile = imagenUrl;
            //     drEvent.destroy();
            //     drEvent.init();

            //     $('.sudah-upload').removeClass('hidden');
            //     $('.belum-upload').addClass('hidden');
            // }

            const el = document.querySelector("#modal-form-persetujuan");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
        }

        function formPersetujuanPulangPaksa(id, alasan, file) {
            $('#pulang_paksa_id').val(id);
            rekam_medis_pasien_id = id;
            $('.sudah-upload-pulang-paksa').addClass('hidden');
            $('.belum-upload-pulang-paksa').removeClass('hidden');
            $('#alasan_pulang_paksa').val(alasan);
            $('#fileContainerPulangPaksa').empty();
            indexFilePulangPaksa = 1;
            
            // if (file) {
            //     var url = "{{ url('/') }}" + '/' + file;
            //     var imagenUrl = url;
            //     var drEvent = $('#upload_pulang_paksa').dropify({
            //         defaultFile: imagenUrl,
            //     });

            //     drEvent = drEvent.data('dropify');
            //     drEvent.resetPreview();
            //     drEvent.clearElement();
            //     drEvent.settings.defaultFile = imagenUrl;
            //     drEvent.destroy();
            //     drEvent.init();

            //     $('.sudah-upload-pulang-paksa').removeClass('hidden');
            //     $('.belum-upload-pulang-paksa').addClass('hidden');
            // }

            $.ajax({
                url: '{{ route('editPulangPaksa') }}',
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                type: 'get',
                success: function(data) {
                    if(data.data.length > 0){
                        $('.sudah-upload-pulang-paksa').removeClass('hidden');
                        $('.belum-upload-pulang-paksa').addClass('hidden');

                        data.data.forEach(function(value, index) {
                            const filePathPP = value.file;
                            const fileNamePP = filePathPP.substring(filePathPP.lastIndexOf('/') + 1);

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
                                text: fileNamePP
                            });
                            const inputSeqPP = $("<input>", {
                                type: "hidden",
                                class: "form-control seq-input-field-pp",
                                name: "pulang_paksa_seq[]"
                            });
                            const previewLinkPP = $("<a>", {
                                href: "{{ url('/') }}/" + value.file,
                                class: "preview-link-pp",
                                text: "Preview File " + indexFilePulangPaksa,
                                style: "text-decoration: underline;"
                            });

                            fileInputPP.append(fileLabelPP, '<br>', inputFilePP, labelFilePP, '<br>', inputSeqPP, previewLinkPP);
                            $("#fileContainerPulangPaksa").append(fileInputPP);

                            indexFilePulangPaksa++;
                        });

                        $('#pulang_paksa_function').val('Edit');
                    }else{
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
                        
                        $('#pulang_paksa_function').val('New');
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

            const el = document.querySelector("#modal-pulang-paksa");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
        }

        function pulangPaksa() {
            var validation = 0;
            $('#modal-pulang-paksa .required').each(function() {
                var par = $(this).parents('.parent-pulang-paksa');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        // $(par).find('.dropify-wrapper').addClass('is-invalid');
                        validation++
                    } else {
                        // $(par).find('.dropify-wrapper').removeClass('is-invalid');
                    }
                } else {
                    // $(par).find('.dropify-wrapper').removeClass('is-invalid');
                }
            })

            if($('#pulang_paksa_function').val() == 'New'){
                if ($('input[name="pulang_paksa_file[]"]')[0].files.length === 0) {
                    ToastNotification('warning', 'File pulang paksa harus diisi');
                    return false;
                }
            }

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#modal-pulang-paksa').serializeArray();
            
            formData.append('_token', '{{ csrf_token() }}');
            // formData.append('jenis', 'pulang_paksa');
            formData.append('id', rekam_medis_pasien_id);
            // formData.append('alasan_pulang_paksa', $('#alasan_pulang_paksa').val());

            // var input = document.getElementById("upload_pulang_paksa");
            // if (input != null) {
            //     file = input.files[0];
            //     formData.append("upload_pulang_paksa", file);
            // }

            data.forEach((d, i) => {
                if(d.value != null && d.value != ''){
                    formData.append(d.name, d.value);
                }
            })

            $('.file-input-field-pp').each(function(index, input) {
                for (var i = 0; i < input.files.length; i++) {
                    formData.append('pulang_paksa_file[]', input.files[i]);
                }
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
                                openModalData('#modal-pulang-paksa');
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

        // function store() {

        //     var validation = 0;

        //     $('#modal-tambah-data .required').each(function() {
        //         var par = $(this).parents('.parent');
        //         if ($(this).val() == '' || $(this).val() == null) {
        //             $(this).addClass('is-invalid');
        //             $(par).find('.select2-container').addClass('is-invalid');
        //             validation++
        //         }
        //     })

        //     if (validation != 0) {
        //         ToastNotification('warning', 'Semua data harus diisi');
        //         return false;
        //     }

        //     var formData = new FormData();

        //     var input = document.getElementById("image");
        //     if (input != null) {
        //         file = input.files[0];
        //         formData.append("image", file);
        //     }

        //     var data = $('#form-data').serializeArray();


        //     data.forEach((d, i) => {
        //         formData.append(d.name, d.value);
        //     })

        //     console.log(data);
        //     var previousWindowKeyDown = window.onkeydown;

        //     Swal.fire({
        //         title: 'Apakah Anda Yakin?',
        //         text: "Klik Tombol Ya jika data sudah benar.",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Ya',
        //         cancelButtonText: 'Tidak',
        //         showLoaderOnConfirm: true,
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             window.onkeydown = previousWindowKeyDown;
        //             $.ajax({
        //                 url: '{{ route('storeRekamMedis') }}',
        //                 data: formData,
        //                 type: 'post',
        //                 processData: false,
        //                 contentType: false,
        //                 success: function(data) {
        //                     if (data.status == 1) {
        //                         Swal.fire({
        //                             title: data.message,
        //                             icon: "success",
        //                         });
        //                         clear();
        //                     } else if (data.status == 2) {
        //                         Swal.fire({
        //                             title: data.message,
        //                             icon: "warning",
        //                         });
        //                     } else {
        //                         Swal.fire({
        //                             title: 'Ada Kesalahan !!!',
        //                             text: data,
        //                             icon: "warning",
        //                             html: true,
        //                         });
        //                     }
        //                     table.ajax.reload(null, false);
        //                 },
        //                 error: function(data) {
        //                     var html = '';
        //                     Object.keys(data.responseJSON).forEach(element => {
        //                         html += data.responseJSON[element][0] + '<br>';
        //                     });
        //                     Swal.fire({
        //                         title: 'Ada Kesalahan !!!',
        //                         html: data.responseJSON.message == undefined ? html : data
        //                             .responseJSON.message,
        //                         icon: "error",
        //                     });
        //                 }
        //             });
        //         }
        //     })
        // }

        function hapus(id, param) {
            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: "Hapus Data",
                text: "Data yang telah dihapus tidak bisa dikembalikan.",
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
                        url: '{{ route('deleteRekamMedis') }}',
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
                                clear();
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

        function lihatRekamMedis(id) {
            $.ajax({
                url: "{{ route('getRekamMedisPasien') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    idPasien = id;
                    $('#append-rekam-medis').html(data);
                    const el = document.querySelector("#modal-rekam-medis");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                },
                error: function(data) {
                    lihatRekamMedis(id);
                }
            });
        }

        function gantiStatus(param, id) {
            $.ajax({
                url: "{{ route('statusRekamMedis') }}",
                data: {
                    id,
                    param
                },
                type: 'get',
                success: function(data) {
                    table.ajax.reload(null, false);
                    ToastNotification('success', data.message);

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

        function formatRepoStatus(repo) {
            if (repo.loading) {
                return repo.text;
            }
            // scrolling can be used
            var markup = $('<span value=' + repo.id + '>' + repo.text + '</span>');
            return markup;
        }

        function formatRepoStatusSelection(repo) {
            return repo.text || repo.text;
        }

        function printTindakanBedah() {
            window.open('{{ route('printRuangan') }}?id=' + $('#rekam_medis_pasien_id').val());
        }

        function printPulangPaksa() {
            window.open('{{ route('printPulangPaksaRuangan') }}?id=' + $('#pulang_paksa_id').val() +
                '&rekam_medis=true');
        }

        function printFormPersetujuan() {
            var id = $('#form_persetujuan_id').val();
            $.ajax({
                url: '{{ route('editRuangan') }}',
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                type: 'get',
                success: function(data) {
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

        function uploadFormPersetujuan() {
            var validation = 0;

            // if ($('#form_persetujuan')[0].files[0] == undefined) {
            //     console.log($('#form_persetujuan')[0].files[0]);
            //     ToastNotification('warning', 'File form persetujuan harus diisi');
            //     return false;
            // }

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

            // var input = document.getElementById("form_persetujuan");
            // if (input != null) {
            //     file = input.files[0];
            //     formData.append("form_persetujuan", file);
            // }

            // data.forEach((d, i) => {
            //     formData.append(d.name, d.value);
            // })

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
                        url: '{{ route('storeRekamMedis') }}',
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

        function openModal(id) {
            $.ajax({
                url: "{{ route('getRekamMedisRekamMedis') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    idPasien = id;
                    $('#append-rekam-medis').html(data);
                    openModalData("#modal-rekam-medis");
                },
                error: function(data) {
                    openModal(id);
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
                        url: '{{ route('storeRekamMedis') }}',
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
                        url: '{{ route('storeRekamMedis') }}',
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
                        url: '{{ route('storeRekamMedis') }}',
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
                var par = $(this).parents('.parent-tindakan');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null) {
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
                        url: '{{ route('storeRekamMedis') }}',
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

        function tambahRekomendasiTindakanBedah() {
            var validation = 0;
            $('#modal-rekomendasi-tindakan-bedah .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null) {
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

            var data = $('#modal-rekomendasi-tindakan-bedah').serializeArray();

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
                        url: '{{ route('storeRekamMedis') }}',
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

        function tambahPakan() {
            var validation = 0;
            $('#modal-tambah-pakan .required').each(function() {
                var par = $(this).parents('.parent-pakan');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null) {
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

            var data = $('#modal-tambah-pakan').serializeArray();


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
                        url: '{{ route('storeRekamMedis') }}',
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

        function tambahHasilLab() {
            var validation = 0;
            $('#modal-tambah-hasil-lab .required').each(function() {
                var par = $(this).parents('.parent-hasil-lab');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        console.log($(this));
                        $(this).addClass('is-invalid');
                        validation++
                    }
                }
            })

            if ($('#dropify')[0].files[0] == undefined) {
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
                        url: '{{ route('storeRekamMedis') }}',
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
                                $('.dropify-clear').click();
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

        function openModalData(modal) {
            rekamMedisPasienId = null;
            idRekamMedis = null;
            paramModal = null;
            const el = document.querySelector(modal);
            const modalRekamMedis = tailwind.Modal.getOrCreateInstance(el);
            $('.parent-resep').remove();
            modalRekamMedis.toggle();
        }

        function refreshingData(id) {
            $.ajax({
                url: "{{ route('getRekamMedisRekamMedis') }}",
                type: 'get',
                data: {
                    id: id
                },
                success: function(data) {
                    idPasien = id;
                    $('#append-rekam-medis').html(data);
                    table.ajax.reload();
                },
                error: function(data) {
                    refreshingData(id);
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
                    overlay(true);
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('storeRekamMedis') }}',
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
    </script>
@endsection
