@extends('../layout/' . $layout)

@section('subhead')
    <title>Amore Animal Clinic</title>
@endsection

@section('style')
    <style>
        .tab-content .tab-pane {
            position: relative !important;
            top: 0 !important;
            left: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
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

        .member-data {
            width: 300px;
            height: 100%;
        }

        .member-img {
            width: 300px;
        }

        .member-container {
            color: black;
            position: relative;
            color: black;
            font-family: 'Montserrat';
            font-weight: 700;
        }
    </style>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Registrasi Pasien</h2>
    </div>
    <form class="grid grid-cols-12 gap-6 mt-5" id="form-data">
        <div class="intro-y col-span-12 grid grid-cols-12">
            <!-- BEGIN: Form Layout -->
            <div id="smartwizard" class="intro-y box p-5 col-span-12">
                <ul class="nav">
                    <li>
                        <a class="nav-link" href="#step-1">
                            Data Owner
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#step-2">
                            Data Pasien
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#step-3">
                            Keluhan
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="step-1" class="tab-pane grid grid-cols-12 gap-4 gap-y-3" role="tabpanel">
                        <div class="col-span-12 md:col-span-4 parent">
                            <label>No. Registrasi</label>
                            <select name="kode" id="kode" class="form-control kode">
                            </select>
                            {{ csrf_field() }}
                            <input type="hidden" id="index">
                        </div>
                        <div class="col-span-12 md:col-span-4 parent">
                            <label>Nama Lengkap Owner {{ dot() }}</label>
                            <select name="owner_id" id="owner_id" class="form-control owner_id required">
                            </select>
                        </div>
                        <div class="col-span-12 md:col-span-4 parent">
                            <div class="mt-3">
                                <label>Layanan Penjemputan</label>
                                <div class="flex flex-col sm:flex-row mt-2">
                                    <div class="form-check mr-2">
                                        <input id="radio-switch-4" class="form-check-input" type="radio"
                                            name="status_pickup" value="false" checked="checked">
                                        <label class="form-check-label" for="radio-switch-4">Tidak dijemput</label>
                                    </div>
                                    <div class="form-check mr-2 mt-2 sm:mt-0">
                                        <input id="radio-switch-5" class="form-check-input" type="radio"
                                            name="status_pickup" value="true">
                                        <label class="form-check-label" for="radio-switch-5">Dijemput</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-4 parent">
                            <label for="telpon" class="form-label">No. Telepon {{ dot() }}</label>
                            <div class="input-group parent">
                                {{-- <div class="input-group-text">+62</div> --}}
                                <input id="telpon" name="telpon" type="text" class="form-control required"
                                    placeholder="Masukan No Telepon">
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-4 parent">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group parent">
                                <input id="email" name="email" type="text" class="form-control"
                                    placeholder="Masukan Email">
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-4 parent">
                            <label for="komunitas" class="form-label">Komunitas</label>
                            <div class="input-group parent">
                                <input id="komunitas" name="komunitas" type="text" class="form-control"
                                    placeholder="Masukan Komunitas">
                            </div>
                        </div>
                        <div class="col-span-12  parent">
                            <label for="alamat" class="form-label">Alamat {{ dot() }}</label>
                            <div class="input-group parent">
                                <textarea id="alamat" name="alamat" type="text" class="form-control required" placeholder="Masukan Alamat"></textarea>
                            </div>
                        </div>
                    </div>
                    <div id="step-2" class="tab-pane grid grid-cols-12 gap-4 gap-y-3" role="tabpanel">
                        <div class="col-span-12 parent_hewan" style="padding: 15px">
                            <div class="flex">
                                <div class="bg-success rounded-t-md pointer flex align-middle justify-center btn-partai text-center pt-1  mr-2"
                                    onclick="addHewan()">
                                    <i class="fas fa-plus text-white mr-1" aria-hidden="true"></i> <span
                                        class="text-white">Tambah?</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-4 gap-y-3 border rounded" style="padding: 15px">
                                <div class="col-span-12 md:col-span-4">
                                    <label for="">Foto</label>
                                    <input type="file" class="dropify text-sm" id="image">
                                </div>
                                <div class="col-span-12 md:col-span-8">
                                    <div class="grid grid-cols-12 gap-4 gap-y-3">
                                        <div class="col-span-12 parent">
                                            <label for="">Nama Hewan {{ dot() }}</label>
                                            <select class="form-control select2 pasien_id required" name="pasien_id[]">
                                                <option value="">Pilih Hewan</option>
                                            </select>
                                        </div>
                                        <div class="col-span-12 parent md:col-span-4 parent">
                                            <label class="flex justify-between"><span>Hewan {{ dot() }}</span>
                                                <a href="javascript:;" onclick="window.open('{{ route('hewan') }}')"><i
                                                        class="fa fa-plus text-info"></i>
                                                </a>
                                            </label>
                                            <select class="form-control binatang_id select2 required"
                                                name="binatang_id[]">
                                            </select>
                                        </div>
                                        <div class="col-span-12 parent md:col-span-4 parent">
                                            <label class="flex justify-between"><span>Ras {{ dot() }}</span>
                                                <a href="javascript:;" onclick="window.open('{{ route('ras') }}')"><i
                                                        class="fa fa-plus text-info"></i>
                                                </a>
                                            </label>
                                            <select class="form-control select2 ras_id required" name="ras_id[]">
                                                <option value="">Pilih Ras</option>
                                            </select>
                                        </div>
                                        <div class="col-span-12 parent md:col-span-4 parent">
                                            <label>Jenis Kelamin {{ dot() }}
                                            </label>
                                            <select class="form-control select2 sex required required" name="sex[]">
                                                <option value="">Pilih Jenis Kelamin</option>
                                                @foreach (\App\Models\Pasien::$enumJenisKelamin as $item)
                                                    <option value="{{ $item }}">
                                                        {{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-12 parent md:col-span-4 parent">
                                            <label>Tanggal Lahir
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-cake-candles"></i>
                                                </div>

                                                <input type="text" class="form-control date_of_birth"
                                                    data-single-mode="true" name="date_of_birth[]">
                                            </div>
                                        </div>
                                        <div class="col-span-12 parent md:col-span-4 parent">
                                            <label>Umur
                                            </label>
                                            <div class="input-group parent">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-cake-candles"></i>
                                                </div>
                                                <input type="text" class="form-control umur" readonly name="umur[]"
                                                    value="">
                                            </div>
                                        </div>
                                        <div class="col-span-12 parent md:col-span-4 parent">
                                            <label>Life Stage {{ dot() }}
                                            </label>
                                            <select class="form-control select2 life_stage required" name="life_stage[]">
                                                <option value="">Pilih Life Stage</option>
                                                @foreach (\App\Models\Pasien::$enumLifeStage as $i => $item)
                                                    <option value="{{ $item['title'] }}">
                                                        {{ $item['title'] }} | {{ $item['description'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-12 parent parent">
                                            <hr>
                                            <label for="">Ciri Khas (Specific Pattern)</label>
                                            <textarea name="ciri_khas[]" class="form-control ciri_khas" cols="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="step-3" class="tab-pane grid grid-cols-12 gap-4 gap-y-3 py-6" role="tabpanel">
                        <div class="col-span-12 parent text-right">
                            <div class="icheck-primary">
                                <input type="checkbox" class="mr-1" id="tanpa_owner" name="tanpa_owner">
                                <label for="tanpa_owner" class="font-bold">Tidak ada Owner</label>
                            </div>
                        </div>
                        <div class="col-span-12 md:col-span-6 parent">
                            <label for="font-bold">Jenis Antrean {{ dot() }}</label>
                            <hr>
                            <select name="poli_id" id="poli_id" class="form-control poli_id select2 required"
                                onchange="gantiAnamnesa()">
                                <option value="">Pilih Tujuan</option>
                                @foreach (\App\Models\Poli::where('status', true)->get() as $item)
                                    @if ($item->name == 'Steril' or
                                        $item->name == 'Emergency' or
                                        $item->name == 'Periksa' or
                                        $item->name == 'Grooming')
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12 md:col-span-6 parent">
                            <span class="font-bold">Request Dokter</span>
                            <select class="request_dokter form-control select2" id="request_dokter"
                                name="request_dokter">
                                <option selected value="">Tanpa Request</option>
                            </select>
                        </div>
                        <div class="col-span-12">
                            <span class="font-bold">Catatan Permintaan Owner</span>
                            <textarea type="text" class="catatan form-control" id="catatan" name="catatan"></textarea>
                        </div>
                        <div class="col-span-12">
                            <h4 class="font-bold text-2xl">Anamnesa</h4>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="on-store hidden text-right col-span-12">
                    <button type="button" class="btn shadow-md mr-2 text-white" style="background: rgb(28, 25, 187)"
                        onclick="downloadKartu()">
                        <i class="fa-solid fa-id-card mr-2"></i> Download member card
                    </button>
                    <button type="button" class="btn btn-warning shadow-md mr-2" onclick="printPendaftaran()">
                        <i class="fas fa-print mr-2"></i> Print Pendaftaran
                    </button>
                    <button type="button" class="btn btn-primary shadow-md" onclick="location.reload()">
                        <i class="fas fa-refresh mr-2"></i> Refresh Halaman
                    </button>
                </div>
            </div>
            <div class="col-span-12 parent-card hidden">
                <div class="grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 lg:col-span-6 parent flex justify-center">
                        <div class="member-container" id="id-depan">
                            <img alt="npwp" src="{{ asset('dist/images/1.png') }}" class="member-img">
                            <div class="absolute top-0 member-data flex justify-end flex-col"
                                style="padding-bottom: 0.8rem;">
                                <div class="w-full text-black" style="font-size:14px;padding-left:1rem">
                                    <span class="nama-member"></span>
                                </div>
                                <div class="w-full text-black" style="font-size:10px;padding-left:1rem">
                                    <span class="alamat-member"></span>
                                </div>
                                <div class="w-full h-8 py-2  parent-text text-black text-center"
                                    style="font-size:12px;padding-left:6rem">
                                    <span class="kode-member">AMORE-BKS-01042022-0001</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6 parent flex justify-center">
                        <div class="member-container" id="id-belakang">
                            <img alt="npwp" src="{{ asset('dist/images/2.png') }}" class="member-img">
                            <div class="absolute top-0 member-data flex justify-end flex-col"
                                style="padding-bottom:1.1rem">
                                <div class="w-full h-8 py-2 px-1 text-black text-center parent-text"
                                    style="padding-right:0.5rem;font-size:16px">
                                    <span class="telpon-cs"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END: Form Layout -->
        </div>
    </form>
@endsection

@section('script')
    <script>
        var table;
        var ownerId;
        var pasienId;
        var idPendaftaran;
        var pasien = [];
        var kodeMember;
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
                        url: "{{ route('datatableSupplier') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id_filter').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center'
                    }, {
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, {
                        data: 'kode',
                        name: 'kode'
                    }, {
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'branch',
                        name: 'branch'
                    }, {
                        data: 'npwp',
                        name: 'npwp'
                    }, {
                        data: 'telpon',
                        name: 'telpon'
                    }, {
                        data: 'email',
                        class: 'text-center',
                    }, {
                        data: 'alamat',
                        class: 'text-center',
                    }, {
                        data: 'komunitas',
                        class: 'text-center',
                    }, {
                        data: 'status',
                        class: 'text-center',
                        orderable: false,
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

            $('.select2').select2({
                // theme: 'bootstrap4',
                width: '100%',
            })

            $('#tujuan').select2({
                placeholder: 'Pilih tujuan',
                width: '100%',
            })

            $('.select2filter').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                // theme: 'bootstrap4',
            })

            Inputmask({
                "mask": "9999999999999",
                "placeholder": "",
            }).mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            $('#tambah-data').click(function() {
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();
            })

            $('.dropify').dropify();
            tomGenerator('.tomSelect');
            // Select 2 ajax
            $('#owner_id').on('select2:select', function(event) {
                var data = event.params.data;
                ownerId = data.id;
                if (data.name == undefined) {
                    ownerId = null;
                } else {
                    ownerId = data.id;
                }

                var newOption = new Option(data.kode,
                    data.id,
                    true,
                    true
                );

                $('#kode').append(newOption).trigger('change.select2');

                $("#telpon").val(data.telpon);
                $("#email").val(data.email);
                $("#alamat").val(data.alamat);
                $("#komunitas").val(data.komunitas);
                $("#nik").val(data.nik);
            })

            $("#owner_id").select2({
                width: '100%',
                tags: true,
                ajax: {
                    url: "{{ route('select2Pendaftaran') }}?param=owner_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
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
                placeholder: 'Masukan Nama Owner',
                minimumInputLength: 0,
                templateResult: formatRepoStatus,
                templateSelection: formatRepoStatusSelection
            });

            $('#kode').on('select2:select', function(event) {
                var data = event.params.data;
                ownerId = data.id;
                if (data.name == undefined) {
                    ownerId = null;
                } else {
                    ownerId = data.id;
                }

                var newOption = new Option(data.name,
                    data.id,
                    true,
                    true
                );

                $('#owner_id').append(newOption).trigger('change.select2');

                $("#telpon").val(data.telpon);
                $("#email").val(data.email);
                $("#alamat").val(data.alamat);
                $("#komunitas").val(data.komunitas);
                $("#nik").val(data.nik);
            })

            $("#kode").select2({
                width: '100%',
                ajax: {
                    url: "{{ route('select2Pendaftaran') }}?param=kode_owner",
                    dataType: 'json',
                    data: function(params) {
                        return {
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
                placeholder: 'Masukan No. Registrasi',
                minimumInputLength: 0,
                templateResult: formatRepoNormalStatus,
                templateSelection: formatRepoNormalStatusSelection
            });

            $('#smartwizard').smartWizard({
                selected: 0, // Initial selected step, 0 = first step
                theme: 'arrows', // theme for the wizard, related css need to include for other than default theme
                justified: true, // Nav menu justification. true/false
                darkMode: false, // Enable/disable Dark Mode if the theme supports. true/false
                autoAdjustHeight: false, // Automatically adjust content height
                cycleSteps: false, // Allows to cycle the navigation of steps
                backButtonSupport: true, // Enable the back button support
                enableURLhash: false, // Enable selection of the step based on url hash
                transition: {
                    animation: 'none', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
                    speed: '400', // Transion animation speed
                    easing: '' // Transition animation easing. Not supported without a jQuery easing plugin
                },
                toolbarSettings: {
                    toolbarPosition: 'bottom', // none, top, bottom, both
                    toolbarButtonPosition: 'right', // left, right, center
                    showNextButton: true, // show/hide a Next button
                    showPreviousButton: true, // show/hide a Previous button
                    toolbarExtraButtons: [] // Extra buttons to show on toolbar, array of jQuery input/buttons elements
                },
                anchorSettings: {
                    anchorClickable: true, // Enable/Disable anchor navigation
                    enableAllAnchors: false, // Activates all anchors clickable all times
                    markDoneStep: true, // Add done state on navigation
                    markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                    removeDoneStepOnNavigateBack: false, // While navigate back done step after active step will be cleared
                    enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
                },
                keyboardSettings: {
                    keyNavigation: true, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
                    keyLeft: [37], // Left key code
                    keyRight: [39] // Right key code
                },
                lang: { // Language variables for button
                    next: 'Next',
                    previous: 'Previous'
                },
                disabledSteps: [], // Array Steps disabled
                hiddenSteps: [], // Hidden steps
            });

            $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIndex, nextStepIndex,
                stepDirection) {
                console.log(stepDirection)
                var validation = 0;
                var list = [];
                if (stepDirection == 'forward') {
                    $('#step-' + (currentStepIndex + 1)).find('.required').each(function() {
                        var par = $(this).parents('.parent');
                        if ($(this).val() == '' || $(this).val() == null) {
                            $(this).addClass('is-invalid');
                            console.log($(this));
                            $(par).find('.select2-container').addClass('is-invalid');
                            validation++
                        }
                    })

                    if (validation != 0) {
                        ToastNotification('warning', 'Semua data harus diisi');
                        return false;
                    }
                    refreshState('#step-' + (currentStepIndex + 1), false);
                }

                if (nextStepIndex == 2) {
                    loadAnamnesa();
                    var button =
                        '<button type="button" class="btn btn-primary shadow-md mr-2" id="simpan" onclick="store()"><i class="fas fa-save"></i> Simpan Pendaftaran</button>';
                    $(button).appendTo('.toolbar');
                } else {
                    $('#simpan').remove();
                }
                // alert("Leaving step " + currentStepIndex + " to go to step " + nextStepIndex);
                // return false to stay on step and true to continue navigation

                return true;
            });

            getDokter();
            generateKode();
            loadSelect2Hewan();
            loadDatePicker();

        })()

        $(document).on('change', '.anamnesa_pilihan', function() {
            var parent = $(this).parents('tr');

            if ($(this).hasClass('ya')) {
                if ($(parent).find('.tidak').is(':checked')) {
                    $(parent).find('.tidak').prop('checked', false);
                }
            }

            if ($(this).hasClass('tidak')) {
                if ($(parent).find('.ya').is(':checked')) {
                    $(parent).find('.ya').prop('checked', false);
                }
            }


        });

        function removeHewan(child) {
            $(child).parents('.parent_hewan').remove();
        }

        $(document).on('change', '.binatang_id', function() {
            var par = $(this).parents('.parent_hewan');
            $(par).find('.ras_id').val(null).trigger('change.select2');
        })

        function loadDatePicker() {

            $(".date_of_birth").last().each(function() {
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
        }

        function generateKode() {
            $.ajax({
                url: "{{ route('generateKodePenerimaanStock') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id').val();
                    }
                },
                success: function(data) {
                    $('#kode').val(data.kode);
                },
                error: function(data) {
                    generateKode();
                }
            });
        }

        function getDokter() {
            $.ajax({
                url: "{{ route('getDokterMonitoringAntrian') }}",
                type: 'get',
                data: {
                    branch_id: '{{ Auth::user()->branch_id }}'
                },
                success: function(data) {
                    var html = '';
                    data.data.forEach((d, i) => {
                        var status = '<span class="badge badge-info">Tersedia</span>';
                        if (d.pengganti != null) {
                            if (d.pengganti.pendaftaran.length != 0) {
                                var status = '<span class="badge badge-danger">Sibuk</span>';
                            }

                            html += '<option value="' + d.pengganti.id + '">' + d.pengganti.name +
                                '</option>';
                        } else {
                            if (d.data_dokter.pendaftaran.length != 0) {
                                var status = '<span class="badge badge-danger">Sibuk</span>';
                            }

                            html += '<option value="' + d.data_dokter.id + '">' + d.data_dokter.name +
                                '</option>';

                        }

                    });

                    $("#request_dokter").append(html);
                    $("#request_dokter").trigger('change.select2');

                },
                error: function(data) {
                    generateKode();
                }
            });
        }

        function calculateTotal() {
            var harga_satuan = $('#harga_satuan').val().replace(/[^0-9\-]+/g, "") * 1;
            var qty = $('#qty').val().replace(/[^0-9\-]+/g, "") * 1;

            $('#total_harga').val(accounting.formatNumber(harga_satuan * qty));
        }

        function generateAge(parent) {
            var par = $(parent).parents('.parent_hewan');

            $.ajax({
                url: "{{ route('generateAgePendaftaran') }}",
                type: 'get',
                data: {
                    date_of_birth() {
                        return $(par).find('.date_of_birth').val();
                    }
                },
                success: function(data) {
                    $(par).find('.umur').val(data.data);

                    $(par).find('.life_stage').val(data.life_stage).trigger('change.select2');

                },
                error: function(data) {
                    generateKode();
                }
            });
        }

        function loadSelect2Hewan() {
            $('.pasien_id').on('select2:select', function(event) {
                var data = event.params.data;
                var par = $(this).parents('.parent_hewan');
                $(par).find(".berat").val(data.berat);
                $(par).find(".date_of_birth").val(data.date_of_birth);
                $(par).find(".tinggi").val(data.tinggi);
                $(par).find(".ciri_khas").val(data.ciri_khas);
                $(par).find(".sex").val(data.sex).trigger('change.select2');
                $(par).find(".binatang_id").val(data.binatang_id).trigger('change.select2');
                $(par).find(".dropify").prop('name', 'image_' + data.id);

                pasienId = data.id;
                if (data.binatang_id == undefined) {
                    pasienId = null;
                } else {
                    pasienId = data.id;
                }

                if (data.image != undefined) {
                    var url = "{{ url('/') }}" + '/' + data.image;
                    var imagenUrl = url;
                    var drEvent = $(par).find('.dropify').dropify({
                        defaultFile: imagenUrl,
                    });
                    console.log(drEvent);
                    drEvent = drEvent.data('dropify');
                    drEvent.resetPreview();
                    drEvent.clearElement();
                    drEvent.settings.defaultFile = imagenUrl;
                    drEvent.destroy();
                    drEvent.init();
                }

                if (data.binatang != null) {
                    var newOption = new Option(data.binatang.name,
                        data.binatang.id,
                        true,
                        true
                    );

                    $(par).find('.binatang_id').append(newOption).trigger('change.select2');
                }

                if (data.ras != null) {
                    var newOption = new Option(data.ras.name,
                        data.ras.id,
                        true,
                        true
                    );

                    $(par).find('.ras_id').append(newOption).trigger('change.select2');
                }
                generateAge($(this));
            })

            $('.pasien_id').each(function() {
                var par = $(this).parents('.parent_hewan');
                $(this).select2({
                    width: '100%',
                    tags: true,
                    ajax: {
                        url: "{{ route('select2Pendaftaran') }}?param=pasien_id",
                        dataType: 'json',
                        data: function(params) {
                            return {
                                q: params.term,
                                owner_id: ownerId,
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
                    placeholder: 'Masukan Data Hewan Peliharaan',
                    minimumInputLength: 0,
                    templateResult: formatRepoHewan,
                    templateSelection: formatRepoHewanSelection
                });
            })

            $('.binatang_id').each(function() {
                $(this).select2({
                    width: '100%',
                    ajax: {
                        url: "{{ route('select2Pendaftaran') }}?param=binatang_id",
                        dataType: 'json',
                        data: function(params) {
                            return {
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
                    placeholder: 'Pilih Hewan',
                    minimumInputLength: 0,
                    templateResult: formatRepoNormalStatus,
                    templateSelection: formatRepoNormalStatusSelection
                });
            });

            $('.ras_id').each(function() {
                var par = $(this).parents('.parent_hewan');
                $(this).select2({
                    width: '100%',
                    ajax: {
                        url: "{{ route('select2Pasien') }}?param=ras_id",
                        dataType: 'json',
                        data: function(params) {
                            return {
                                q: params.term,
                                binatang_id() {
                                    return $(par).find('.binatang_id').val();
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
                    placeholder: 'Pilih Hewan Dahulu',
                    minimumInputLength: 0,
                    templateResult: formatRepoNormalStatus,
                    templateSelection: formatRepoNormalStatusSelection
                });
            });
        }

        function addHewan() {
            var html = `@include('quick_menu.pendaftaran.template_add_hewan')`;
            $('#step-2').append(html);

            $('.loading').addClass('hidden');
            $('#step-2').find('.select2').select2({
                // theme: 'bootstrap4',
                width: '100%',
            })
            $('.dropify').last().dropify();


            loadSelect2Hewan();
            loadDatePicker();

        }

        function gantiAnamnesa() {
            $.ajax({
                url: '{{ route('gantiAnamnesaPendaftaran') }}',
                data: {
                    poli_id() {
                        return $('#poli_id').val();
                    }
                },
                type: 'get',
                success: function(data) {
                    $('.appendAnamnesa').html(data);

                    $('.pasien_id').each(function(i) {
                        $('.appendAnamnesa').eq(i).find('.anamnesa_id').prop('name', 'anamnesa_' + i +
                            '_id[]')
                        $('.appendAnamnesa').eq(i).find('.anamnesa_pilihan_ya').prop('name',
                            'anamnesa_pilihan_ya_' + i + '[]')
                        $('.appendAnamnesa').eq(i).find('.anamnesa_pilihan_tidak').prop('name',
                            'anamnesa_pilihan_tidak_' + i + '[]')
                        $('.appendAnamnesa').eq(i).find('.keterangan_anamnesa').prop('name',
                            'keterangan_anamnesa_' + i + '[]')
                    })
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

        function loadAnamnesa() {
            $('.parent_anamnesa').remove();
            var html = `@include('../quick_menu/pendaftaran/template_anamnesa')`;

            $('.pasien_id').each(function() {
                var par = $(this).parents('.parent_hewan');
                $('#step-3').append(html);
                var nama_text = $(this).find('option:selected').text();
                var hewan_text = $(par).find('.binatang_id').find('option:selected').text();
                var ras_text = $(par).find('.ras_id').find('option:selected').text();
                var sex_text = $(par).find('.sex').find('option:selected').text();
                var life_stage_text = $(par).find('.life_stage').find('option:selected').text();
                var ciri_khas_text = $(par).find('.ciri_khas').val();

                $('.nama_text').last().text(nama_text);
                $('.hewan_text').last().text(hewan_text);
                $('.ras_text').last().text(ras_text);
                $('.sex_text').last().text(sex_text);
                $('.life_stage_text').last().text(life_stage_text);
                $('.ciri_khas_text').last().text(ciri_khas_text);

            })

            $('#step-3').find('.select2').select2({
                width: '100%',
            });
            gantiAnamnesa();
        }

        function printPendaftaran(id) {
            if (id == null) {
                id = idPendaftaran;
            }
            window.open('{{ route('printPendaftaran') }}?id=' + id);
        }

        function store() {
            var validation = 0;

            $('#form-data .required').each(function() {
                var par = $(this).parents('.parent');
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).addClass('is-invalid');
                    $(par).find('.select2-container').addClass('is-invalid');
                    validation++
                }
            })

            if (validation != 0) {
                ToastNotification('warning', 'Semua data harus diisi');
                return false;
            }

            var formData = new FormData();


            $('.dropify').each(function(i) {
                file = $(this)[0].files[0];
                if (file != undefined) {
                    formData.append('image_' + $('.pasien_id').eq(i).val(), file);
                }
            })

            formData.append("id_owner", ownerId);


            if ($('#branch_id').length != 0) {
                formData.append("branch_id", $('#branch_id').val());
            }

            var data = $('#form-data').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Klik Tombol Ya jika data sudah benar.",
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
                    overlay(true);
                    $.ajax({
                        url: '{{ route('storePendaftaran') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                idPendaftaran = data.id;
                                printPendaftaran(data.id);
                                $('.on-store').removeClass('hidden');
                                $('.toolbar').remove();
                                kodeMember = data.kode;
                                $('.kode-member').html(data.kode);
                                $('.nama-member').html(data.name);
                                $('.alamat-member').html(data.alamat);
                                $('.telpon-cs').html('Call Center : +62 ' + data.telpon);
                                Swal.fire({
                                    title: 'Success',
                                    text: data.message,
                                    icon: "success",
                                }, function() {

                                });
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Ada Kesalahan !!!',
                                    html: data.message,
                                    icon: "warning",
                                });
                            } else {
                                Swal.fire({
                                    title: 'Ada Kesalahan !!!',
                                    html: data,
                                    icon: "warning",
                                });
                            }
                            overlay(false);
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
                            overlay(false);
                        }
                    });
                }
            })
        }

        function downloadKartu() {
            const nodeDepan = document.getElementById('id-depan');
            $('.parent-text').removeClass('py-2');
            $('.parent-card').removeClass('hidden');
            html2canvas(nodeDepan, {
                scale: 5,
            }).then(canvas => {
                // document.body.appendChild(canvas)
                // var img    = canvas.toDataURL("image/png");
                // document.write('<img src="'+img+'"/>');
                var link = document.createElement('a');
                link.download = 'member_depan_' + kodeMember + '.png';
                link.href = canvas.toDataURL()
                link.click();
            });

            const nodeBelakang = document.getElementById('id-belakang');

            html2canvas(nodeBelakang, {
                scale: 2,
            }).then(canvas => {
                // document.body.appendChild(canvas)
                // var img    = canvas.toDataURL("image/png");
                // document.write('<img src="'+img+'"/>');
                var link = document.createElement('a');
                link.download = 'member_belakang_' + kodeMember + '.png';
                link.href = canvas.toDataURL()
                link.click();
            });
            $('.parent-text').addClass('py-2');
            $('.parent-card').addClass('hidden');
        }

        function formatRepoStatus(repo) {
            if (repo.loading) {
                return repo.text;
            }
            console.log(repo);
            // scrolling can be used
            var markup = $('<span value=' + repo.id + '>' + repo.text + ' ' + (repo.telpon != undefined ? repo.telpon :
                '') + ' ' + (repo.email != undefined ? repo.email : '') + '</span>');
            return markup;
        }

        function formatRepoNormalStatusSelection(repo) {
            return repo.text || repo.text;
        }

        function formatRepoNormalStatus(repo) {
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

        function formatRepoHewan(repo) {
            if (repo.loading) {
                return repo.text;
            }

            if (repo.name != undefined) {
                image = repo.image ? "{{ url('/') }}" + "/" + repo.image : "{{ asset('dist/images/amore.png') }}";
                var $container = $(
                    "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-result-repository__avatar'><img style='" +
                    "object-fit:cover" + "' src='" +
                    image + "' /></div>" +
                    "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'></div>" +
                    "<div class='select2-result-repository__description'></div>" +
                    "<div class='select2-result-repository__statistics'>" +
                    "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> </div>" +
                    "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> </div>" +
                    "<div class='select2-result-repository__watchers'><i class='fa fa-code-fork'></i> </div>" +
                    "</div>" +
                    "</div>" +
                    "</div>"
                );

                $container.find(".select2-result-repository__title").text(repo.name);
                $container.find(".select2-result-repository__description").text(repo.ciri_khas);
                $container.find(".select2-result-repository__forks").append(repo.binatang.name);
                $container.find(".select2-result-repository__stargazers").append(repo.ras.name);
                $container.find(".select2-result-repository__watchers").append(repo.branch.kode);

                return $container;
            } else {
                // scrolling can be used
                var markup = $('<span value=' + repo.id + '>' + repo.text + '</span>');

                return markup;
            }
        }

        function formatRepoHewanSelection(repo) {
            return repo.text || repo.text;
        }

        $.fn.serializeArray = function() {
            var rselectTextarea = /^(?:select|textarea)/i;
            var rinput =
                /^(?:color|date|datetime|datetime-local|email|hidden|month|number|password|range|search|tel|text|time|url|week)$/i;
            var rCRLF = /\r?\n/g;

            return this.map(function() {
                return this.elements ? jQuery.makeArray(this.elements) : this;
            }).filter(function() {
                return this.name && !this.disabled && (this.checked || rselectTextarea.test(this.nodeName) ||
                    rinput.test(this.type) || this.type == "checkbox");
            }).map(function(i, elem) {
                var val = jQuery(this).val();
                if (this.type == 'checkbox' && this.checked === false) {
                    val = 'off';
                }
                return val == null ? null : jQuery.isArray(val) ? jQuery.map(val, function(val, i) {
                    return {
                        name: elem.name,
                        value: val.replace(rCRLF, "\r\n")
                    };
                }) : {
                    name: elem.name,
                    value: val.replace(rCRLF, "\r\n")
                };
            }).get();
        }
    </script>
@endsection
