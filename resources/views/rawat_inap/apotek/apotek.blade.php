@extends('../layout/' . $layout)
@section('header_filter')
    Filter {{ convertSlug($global['title']) }}
@endsection

@section('content_filter')
    @include('../rawat_inap/apotek/filter_apotek')
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

        @media (min-width: 768px) {
            .md\:col-span-2 {
                grid-column: span 2 / span 2;
            }
        }
    </style>
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- BEGIN: Data List -->
        <div class="col-span-12 lg:col-span-4">
            <div class="intro-y pr-1">
                <div class="box p-2">
                    <ul class="nav nav-pills" role="tablist">
                        <li id="rawat-jalan-tab" class="nav-item flex-1" role="presentation">
                            <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#rawat-jalan"
                                onclick="rubahStatus('Antrian')" type="button" role="tab" aria-controls="rawat-jalan"
                                aria-selected="true">
                                Obat Pulang
                                <div class="px-2 float-right rounded-full text-xs bg-success text-white font-medium"
                                    id="count-rawat-jalan">
                                    {{ count($pasien) }}
                                </div>
                            </button>
                        </li>
                        <li id="ranap-tab" class="nav-item flex-1" role="presentation">
                            <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#ranap"
                                type="button" onclick="rubahStatus('Langsung')" role="tab" aria-controls="ranap"
                                aria-selected="false">
                                Obat Rawat Inap
                                <div class="px-2 float-right rounded-full text-xs bg-success text-white font-medium"
                                    id="count-rawat-inap">
                                    {{ count($pasienRawatInap) }}
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="list-pasien">
                <div id="rawat-jalan" class="tab-pane active" role="tabpanel" aria-labelledby="rawat-jalan-tab">
                    <div class="box p-5 mt-5">
                        @foreach ($pasien as $item)
                            <a href="javascript:;" onclick="getPasien('{{ $item->id }}')"
                                class="item-rawat-jalan flex items-center p-3 border-b cursor-pointer transition duration-300 ease-in-out bg-white dark:bg-darkmode-600 hover:bg-slate-100 dark:hover:bg-darkmode-400 rounded-md">
                                <div>
                                    <div class="text-slate-500">
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->Pendaftaran->kode_pendaftaran }}</span>
                                    </div>
                                    <div class="text-slate-500">Nama Owner
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->pasien->owner->name }}</span>
                                    </div>
                                    <div class="text-slate-500">Atas pasien
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->pasien->name }}</span>
                                    </div>
                                    <div class="text-slate-500">
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            @if (isset($item->rekamMedisResep[0]))
                                                Tanggal
                                                {{ CarbonParse($item->rekamMedisResep[0]->created_at, 'd-m-Y') }}
                                                Jam {{ CarbonParse($item->rekamMedisResep[0]->created_at, 'H:i:s A') }}
                                            @else
                                                Tanggal
                                                {{ CarbonParse($item->created_at, 'd-m-Y') }}
                                                Jam {{ CarbonParse($item->created_at, 'H:i:s A') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    @if ($item->status_apoteker == 'waiting')
                                        <div
                                            class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">
                                            Waiting
                                        </div>
                                    @elseif ($item->status_apoteker == 'progress')
                                        <div
                                            class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">
                                            @if($item->updatedBy->role_id == 5)
                                            Dokter {{ $item->progress_by }} sedang membuat resep
                                            @else
                                            In Progress by {{ $item->progress_by }}
                                            @endif
                                        </div>
                                    @elseif ($item->status_apoteker == 'revisi')
                                        <div
                                            class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium">
                                            Revisi
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div id="ranap" class="tab-pane" role="tabpanel" aria-labelledby="ranap-tab">
                    <div class="box p-5 mt-5">
                        @foreach ($pasienRawatInap as $item)
                            <a href="javascript:;" onclick="getPasien('{{ $item->id }}')"
                                class="item-rawat-inap flex items-center p-3 border-b cursor-pointer transition duration-300 ease-in-out bg-white dark:bg-darkmode-600 hover:bg-slate-100 dark:hover:bg-darkmode-400 rounded-md">
                                <div>
                                    <div class="text-slate-500">
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->kode }}</span>
                                    </div>
                                    <div class="text-slate-500">Nama Owner
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->pasien->owner->name }}</span>
                                    </div>
                                    <div class="text-slate-500">Atas pasien
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->pasien->name }}</span>
                                    </div>
                                    <div class="text-slate-500"> Ruangan
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->KamarRawatInapDanBedahDetail[0]->KamarRawatInapDanBedah->name }}</span>
                                    </div>
                                    <div class="text-slate-500">
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            @if ($item->singleRekamMedisResep)
                                                Tanggal
                                                {{ CarbonParse($item->singleRekamMedisResep->created_at, 'd-m-Y') }}
                                                Jam
                                                {{ CarbonParse($item->singleRekamMedisResep->created_at, 'H:i:s A') }}
                                            @else
                                                Tanggal
                                                {{ CarbonParse($item->created_at, 'd-m-Y') }}
                                                Jam {{ CarbonParse($item->created_at, 'H:i:s A') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                @if ($item->status_apoteker == 'waiting')
                                    <div
                                        class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">
                                        Waiting
                                    </div>
                                @elseif ($item->status_apoteker == 'progress')
                                    <div
                                        class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">
                                        @if($item->updatedBy->role_id == 5)
                                        Dokter {{ $item->progress_by }} sedang membuat resep
                                        @else
                                        In Progress by {{ $item->progress_by }}
                                        @endif
                                    </div>
                                @elseif ($item->status_apoteker == 'revisi')
                                    <div
                                        class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium">
                                        Revisi
                                    </div>
                                @endif

                                <div class="ml-auto font-medium">
                                    @if ($item->status_pemeriksaan == 'Pasien Meninggal')
                                        <div
                                            class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium text-center">
                                            Pasien Meninggal
                                        </div>
                                    @else
                                        <div
                                            class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium text-center">
                                            Rawat Inap
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                        {{-- <div class="flex items-center border-b border-slate-200 dark:border-darkmode-400 pb-5">
                            <div>
                                <div class="text-slate-500">Time</div>
                                <div class="mt-1">02/06/20 02:10 PM</div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                icon-name="clock" data-lucide="clock"
                                class="lucide lucide-clock w-4 h-4 text-slate-500 ml-auto">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
        <form class="col-span-12 xl:col-span-8 grid grid-cols-12 gap-6" id="data-pemeriksaan">

        </form>
        <!-- END: Data List -->
    </div>
    @include('../rawat_inap/apotek/modal')
@endsection
@section('script')
    <script>
        var xhr = [];
        var pasienList = [];
        var table;
        var jenisTab = 'Antrian';
        var indexRacikan = 1;
        (function() {
            $('#rekomendasi_tanggal_bedah').val('');
            $('#html1').jstree();
            $('#html1').jstree("open_all");
            $('#html1').on('changed.jstree', function(e, data) {
                $('#jenis').val(data.node.data.jenis);
                $('#value').val(data.node.data.value);
                table.ajax.reload();
            }).jstree();

            $('#myInputTextField').keyup(debounce(function() {
                table.search($(this).val()).draw();
            }, 500));

            $('.mask').maskMoney({
                precision: 0,
                thousands: ','
            })

            channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
                if (data.jenis == 'Antrian Apotek') {
                    console.log(data.data.status_pemeriksaan);
                    if (data.data.status_pemeriksaan == 'Boleh Pulang' || data.data.status_pemeriksaan ==
                        'Pasien Meninggal' || data.data.status_pemeriksaan == 'Pulang Paksa') {

                        var validation = false;
                        pasienList.forEach(element => {
                            if (element * 1 == data.data.id * 1) {
                                validation = true;
                            }
                        });

                        if (validation) return $validation;

                        if (data.data.status_pengambilan_obat == false && data.data.status_pembayaran ==
                            false) {
                            var html = '<a href="javascript:;" onclick="getPasien(' + data.data
                                .id +
                                ')"' +
                                'class="item-rawat-jalan flex items-center p-3 border-b cursor-pointer transition duration-300 ease-in-out bg-white dark:bg-darkmode-600 hover:bg-slate-100 dark:hover:bg-darkmode-400 rounded-md">' +
                                '<div>' +
                                '<div class="text-slate-500 mr-1">' +
                                data.data.kode +
                                '</div>' +
                                '<div class="text-slate-500">Atas pasien ' +
                                '<span class="font-medium text-slate-500  max-w-[70%]">' +
                                data.data.pasien.name +
                                '</span>' +
                                '</div>' +
                                '</div>' +
                                '</a>';

                            $('#rawat-jalan .box').append(html);

                            $('#count-rawat-jalan').html($('.item-rawat-jalan').length);
                        }

                    } else if (data.data.status_pemeriksaan == 'Rawat Inap') {
                        if (data.data.status_pengambilan_obat == false && data.data.status_pembayaran ==
                            false) {
                            var validation = false;
                            pasienList.forEach(element => {
                                if (element * 1 == data.data.id * 1) {
                                    validation = true;
                                }
                            });

                            if (validation) return $validation;

                            if (data.data.status_pemeriksaan == 'Pasien Meninggal') {
                                var status_pemeriksaan = '<div ' +
                                    'class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">' +
                                    'Pasien Meninggal' +
                                    '</div>';
                            } else {
                                var status_pemeriksaan = '<div ' +
                                    'class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">' +
                                    'Rawat Inap' +
                                    '</div>';
                            }

                            var html = '<a href="javascript:;" onclick="getPasien(' + data.data
                                .id +
                                ')"' +
                                'class="item-rawat-inap flex items-center p-3 border-b cursor-pointer transition duration-300 ease-in-out bg-white dark:bg-darkmode-600 hover:bg-slate-100 dark:hover:bg-darkmode-400 rounded-md">' +
                                '<div>' +
                                '<div class="text-slate-500 mr-1">' +
                                data.data.kode +
                                '</div>' +
                                '<div class="text-slate-500">Atas pasien' +
                                '<span class="font-medium text-slate-500  max-w-[70%]"> ' +
                                data.data.pasien.name +
                                '</span>' +
                                '</div>' +
                                '</div>' +
                                '<div class="ml-auto font-medium">' +
                                status_pemeriksaan +
                                '</div>' +
                                '</a>';

                            $('#ranap .box').append(html);

                            $('#count-rawat-inap').html($('.item-rawat-inap').length);
                        }
                    }
                }
            });

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

            $('.select2rekomendasiTindakanApotek').select2({
                dropdownParent: $("#modal-rekomendasi-tindakan-bedah .modal-body"),
                width: '100%',
            })

            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            tomGenerator('.tomSelect');

            $("#kamar_rawat_inap_dan_bedah_id").select2({
                width: '100%',
                dropdownParent: $("#modal-pindah-ruangan .modal-body .parent-pindah-kamar"),
                ajax: {
                    url: "{{ route('select2Apotek') }}?param=kamar_rawat_inap_dan_bedah_id",
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
                placeholder: 'Pilih Ruang Rawat Inap',
                minimumInputLength: 0,
                templateResult: formatRepoKamar,
                templateSelection: formatRepoKamarSelection
            });

            $("#tindakan_id").select2({
                dropdownParent: $("#modal-tambah-tindakan .modal-body .parent-tindakan"),
                width: '100%',
                ajax: {
                    url: "{{ route('select2Apotek') }}?param=tindakan_id",
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
                placeholder: 'Masukan Tindakan',
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

            init();
        })()

        function init() {
            @foreach ($pasien as $item)
                pasienList.push('{{ $item->id }}');
            @endforeach

            @foreach ($pasienRawatInap as $item)
                pasienList.push('{{ $item->id }}');
            @endforeach
        }

        $('.pasien').click(function() {
            console.log('tes');
            $('.pasien').removeClass('active');
            $(this).addClass('active');

            pasienActive = $(this).find('.pasien_id').val();

            getPasien(pasienActive);
        })

        function getPasien(id) {
            overlay(true);
            $.ajax({
                url: "{{ route('getApotek') }}",
                type: 'get',
                data: {
                    id: id,
                    edit: true,
                    status_pemeriksaan: jenisTab,
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    if(data.hasOwnProperty("status")){
                        Swal.fire({
                            title: data.message,
                            icon: "warning",
                        });
                    }else{
                        $("#data-pemeriksaan").html(data);
                        $('.select2resep').select2({
                            width: '100%',
                        })

                        $('.dropify').dropify();
                    }
                    
                    overlay(false);
                },
                error: function(data) {
                    overlay(false);
                }
            });
        }

        function rubahStatus(param) {
            jenisTab = param;
            $('#append-data').html('');
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
                url: "{{ route('getRekamMedisPemeriksaanPasien') }}",
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
        }

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
                        url: '{{ route('deletePenerimaanStock') }}',
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
                url: "{{ route('tambahResepApotek') }}",
                type: 'get',
                data: {
                    index: indexRacikan,
                    id: $('#rekam_medis_pasien_id').val(),
                },
                success: function(data) {
                    $('#append-resep').append(data)
                    $(".loading-resep").addClass('hidden');

                    $('.select2resep').select2({
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
                url: "{{ route('tambahRacikanChildApotek') }}",
                type: 'get',
                data: {
                    index: $(parent).find('.index_racikan').val(),
                    id: $('#rekam_medis_pasien_id').val(),
                },
                success: function(data) {
                    $(parent).find('.append-racikan').append(data);

                    $('.select2resep').select2({
                        width: '100%',
                    })

                    // $('.mask-non-decimal').maskMoney({
                    //     precision: 0,
                    //     thousands: '',
                    //     allowZero: true,
                    // })

                },
                error: function(data) {
                    tambahChildRacikan(parents);
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

        function storeApotek() {
            var validation = 0;

            $('#data-pemeriksaan .required').each(function() {
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

            var data = $('#data-pemeriksaan').serializeArray();

            // if ($('.parent-resep').length == 0) {
            //     ToastNotification('warning', "Minimal harus mengisi satu resep.");
            //     return false;
            // }

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());
            formData.append('tab', jenisTab);

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
                        url: '{{ route('storeApotek') }}',
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
                                location.reload();
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

        function hapusResep(child) {
            $(child).parents('.parent-resep').remove();
        }

        function hapusRacikanChild(child) {
            $(child).parents('.parent-child-racikan').remove();
        }

        function racikanObat(child) {
            var par = $(child).parents('.parent-child-racikan');
            $(par).find('.racikan_sisa_qty').val($(child).find('option:selected').data('qty'));
        }

        function nonRacikanObat(child) {
            var par = $(child).parents('.non-racikan');
            console.log(par);
            $(par).find('.sisa_qty_non_racikan').val($(child).find('option:selected').data('qty'));
        }

        function statusApoteker() {
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}')
            formData.append('id', $('#rekam_medis_pasien_id').val())
            formData.append('status_apoteker', 'progress')
            formData.append('user_id', '{{ Auth::user()->id }}')

            Swal.fire({
                title: "Prosess Data ?",
                text: "Klik Tombol Ya jika ingin memproses obat.",
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

                    $.ajax({
                        url: `{{ route('statusApoteker') }}`,
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
                                location.reload();
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

        function saveData() {
            var validation = 0;

            $('#data-pemeriksaan .required').each(function() {
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

            var data = $('#data-pemeriksaan').serializeArray();

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#rekam_medis_pasien_id').val());
            formData.append('tab', jenisTab);

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
                        url: '{{ route('saveResep') }}',
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
                                location.reload();
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
