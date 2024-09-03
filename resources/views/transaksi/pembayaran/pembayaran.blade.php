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

        .table-item th {
            padding: 4px;
        }

        .table-item td {
            padding: 4px;
            border: 1px solid black
        }

        .table-total th {
            padding: 4px 0px;
        }

        .table-total td {
            padding: 4px 0px;
        }

        .table-item th {
            background: #cccc;
            border: 1px solid black;
        }

        #side-menu-kasir {
            width: 30%;
            height: 100vh !important;
            background: #202223;
            top: 0px;
            position: fixed;
            right: 0px;
            z-index: 9999;
            display: block;
            box-shadow: 5px 10px #888888;
            transition: all 0.3s ease-in-out;
        }

        #side-menu-kasir::before {
            content: "";
            width: 5px;
            position: absolute;
            height: 100vh !important;
            background: red;
        }

        .close {
            right: -30% !important;
        }

        .scrollbar {
            overflow-y: scroll;
            scrollbar-color: #888888 #c3bebe;
            scrollbar-width: thin;
        }

        .small-scroll::-webkit-scrollbar-track {
            background: linear-gradient(to right, var(--scroll-track-color) 0%, var(--scroll-track-color) 35%, var(--scroll-color) 55%, var(--body-bg-color) 61%, var(--body-bg-color) 100%);
        }

        .small-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .small-scroll::-webkit-scrollbar-thumb {
            background-color: var(--scroll-color);
            border-radius: 5px;
        }
    </style>
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- BEGIN: Data List -->

        <div class="col-span-12 lg:col-span-4">

            <div class="intro-y pr-1">
                <div class="parent mb-2 {{ Auth::user()->akses('global') ? '' : 'hidden' }}">
                    <label for="name" class="form-label">Branch{{ dot() }}</label>
                    <select name="branch_id" id="branch_id" onchange="getPasien()" class="select2 form-control required">
                        <option value="">Pilih Branch</option>
                        @foreach (\App\Models\Branch::get() as $item)
                            <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }}
                                value="{{ $item->id }}">
                                {{ $item->kode }} - {{ $item->alamat }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary shadow-md w-full mb-2" onclick="getPembayaran(0)">
                    <i class="fa-solid fa-money-bill mr-2"></i>
                    Transaksi Langsung
                </button>
                <div class="box p-2">
                    <ul class="nav nav-pills" role="tablist">
                        <li id="ticket-tab" class="nav-item flex-1" role="presentation">
                            <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#ticket"
                                onclick="rubahStatus('Rawat Jalan')" type="button" role="tab" aria-controls="ticket"
                                aria-selected="true">
                                Rawat Jalan
                                <div class="px-2 float-right rounded-full text-xs bg-success text-white font-medium"
                                    id="jumlah-owner-rawat-jalan">
                                    {{ count($pasien) }}
                                </div>
                            </button>
                        </li>
                        <li id="ranap-tab" class="nav-item flex-1" role="presentation">
                            <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#ranap"
                                type="button" onclick="rubahStatus('Rawat Inap')" role="tab" aria-controls="ranap"
                                aria-selected="false">
                                Rawat Inap
                                <div class="px-2 float-right rounded-full text-xs bg-success text-white font-medium"
                                    id="jumlah-owner-rawat-inap">
                                    {{ count($pasienRawatInap) }}
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="list-pasien">
                <div id="ticket" class="tab-pane active" role="tabpanel" aria-labelledby="ticket-tab">
                    <div class="box p-5 mt-5">
                        @foreach ($pasien as $item)
                            <a href="javascript:;" onclick="getPembayaran('{{ $item->id }}')"
                                class="flex items-center p-3 border-b pasien cursor-pointer transition duration-300 ease-in-out bg-white dark:bg-darkmode-600 hover:bg-slate-100 dark:hover:bg-darkmode-400 rounded-md">
                                <div>
                                    <div class="text-slate-500 mr-1">{{ $item->kode }}</div>
                                    <div class="text-slate-500">Atas Owner
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->name }}</span>
                                    </div>
                                    <div class="text-slate-500">
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            Tanggal
                                            {{ CarbonParse($item->singleRekamMedisPasien->updated_at, 'd-m-Y') }}
                                            Jam
                                            {{ CarbonParse($item->singleRekamMedisPasien->updated_at, 'H:i:s A') }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div id="ranap" class="tab-pane" role="tabpanel" aria-labelledby="ranap-tab">
                    <div class="box p-5 mt-5">
                        @foreach ($pasienRawatInap as $item)
                            <a href="javascript:;" onclick="getPembayaran('{{ $item->id }}')"
                                class="flex items-center p-3 border-b pasien cursor-pointer transition duration-300 ease-in-out bg-white dark:bg-darkmode-600 hover:bg-slate-100 dark:hover:bg-darkmode-400 rounded-md">
                                <div>
                                    <div class="text-slate-500 mr-1">{{ $item->kode }}</div>
                                    <div class="text-slate-500">Atas Owner
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            {{ $item->name }}</span>
                                    </div>
                                    <div class="text-slate-500">
                                        <span class="font-medium text-slate-500  max-w-[70%]">
                                            Tanggal
                                            {{ CarbonParse($item->singleRekamMedisPasien->updated_at, 'd-m-Y') }}
                                            Jam
                                            {{ CarbonParse($item->singleRekamMedisPasien->updated_at, 'H:i:s A') }}
                                        </span>
                                    </div>
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
        <form class="col-span-12 xl:col-span-8" id="data-transaksi">
        </form>

        <div id="side-menu-kasir" class="close">
            <div class="grid grid-cols-6 gap-4 px-3 text-white py-2">
                <div class="col-span-12 flex justify-between mb-3">
                    <h5><b>Tambah Item</b></h5>
                    <h5><i class="fa fa-times text-white text-xl " style="cursor: pointer"
                            onclick="closeSideMenuKasir()"></i>
                    </h5>
                </div>
                <div class="col-span-12 mb-2">
                    <label for="">Nama Item</label>
                    <input type="text" class="text-white form-control" onkeyup="generateListItemKasir()" id="filter-item"
                        placeholder="Ketik untuk mencari item" style="background: transparent">
                </div>
                <div class="col-span-12 py-3 scrollbar small-scroll">
                    <ul class="p-0" style="list-style: none;height: 70vh;" id="append-list-item">
                        @for ($i = 0; $i < 20; $i++)
                        @endfor
                    </ul>
                </div>
            </div>
        </div>

        <div id="modal-deposit" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- BEGIN: Modal Header -->
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Deposit</h2>
                        <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                    </div>
                    <!-- END: Modal Header -->
                    <!-- BEGIN: Modal Body -->
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <table class="table" id="table">
                                <thead align="center">
                                    <th>No</th>
                                    <th>Owner</th>
                                    <th>Nilai Deposit</th>
                                    <th>Sisa Deposit</th>
                                    <th>Keterangan</th>
                                    <th>Opsi</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- END: Modal Body -->
                    <!-- BEGIN: Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    </div>
                    <!-- END: Modal Footer -->
                </div>
            </div>
        </div>
        <!-- END: Data List -->
    </div>
    @include('../rawat_inap/apotek/modal')
@endsection
@section('script')
    <script>
        var xhr = [];
        var table;
        var jenisTab = 'Rawat Jalan';
        var indexRacikan = 1;
        var idPembayaran;
        var idAfter;
        var itemTambahan = null;
        overlay(true);
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
                        url: "{{ route('datatableDepositPembayaran') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            owner_id: function() {
                                return $('#owner_id').val();
                            },
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center'
                    }, {
                        data: 'owner',
                        name: 'owner'
                    }, {
                        data: 'nilai_deposit',
                        name: 'nilai_deposit'
                    }, {
                        data: 'sisa_deposit',
                        name: 'sisa_deposit'
                    }, {
                        data: 'keterangan',
                        name: 'keterangan',
                        class: 'text-left',
                    }, {
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                    }, ]
                })
                .columns.adjust()
                .responsive.recalc();
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
                thousands: ',',
                allowZero: true,
                defaultZero: true,
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

            $('.select2rekomendasiTindakanPembayaran').select2({
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
                    url: "{{ route('select2Pembayaran') }}?param=kamar_rawat_inap_dan_bedah_id",
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
                    url: "{{ route('select2Pembayaran') }}?param=tindakan_id",
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
            overlay(false);
        })()

        $('.select2').select2({
            width: '100%',
        })

        $(document).on('click', '#kode_deposit', function() {
            const el = document.querySelector("#modal-deposit");
            const modalRekamMedis = tailwind.Modal.getOrCreateInstance(el);
            modalRekamMedis.toggle();
            table.ajax.reload();
        })

        $('.pasien').click(function() {
            console.log('tes');
            $('.pasien').removeClass('active');
            $(this).addClass('active');

            pasienActive = $(this).find('.pasien_id').val();
        })

        function getPembayaran(id) {
            overlay(true);
            $.ajax({
                url: "{{ route('getPembayaran') }}",
                type: 'get',
                data: {
                    id: id,
                    edit: true,
                    jenis: jenisTab,
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    $("#data-transaksi").html(data);
                    if (id == 0) {
                        $('#jenis_tab').val('transaksi_langsung');
                    } else {
                        $('#jenis_tab').val(jenisTab);
                    }

                    $('.select2resep').select2({
                        width: '100%',
                    })

                    $('.mask').maskMoney({
                        precision: 0,
                        thousands: ',',
                        allowZero: true,
                        defaultZero: true,
                    })
                    idPembayaran = id;
                    calcItemDiskon();
                    calcTotalLainLain();
                    calcTotalBayar();
                    $('.button-after-checkout').addClass('hidden');

                    $('.dropify').dropify();
                    overlay(false);
                },
                error: function(data) {}
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

        function getPasien() {
            $.ajax({
                url: "{{ route('getPasienPembayaran') }}",
                type: 'get',
                data: {
                    branch_id() {
                        return $('#branch_id').val();
                    },
                },
                success: function(data) {
                    $('#list-pasien').html(data)

                    $('#jumlah-owner-rawat-inap').html($('.owner-rawat-inap').length);
                    $('#jumlah-owner-rawat-jalan').html($('.owner-rawat-jalan').length);
                },
                error: function(data) {
                    appendResep();
                    $(".loading-resep").addClass('hidden');
                }
            });
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

        function refreshing() {
            location.reload();
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
                url: "{{ route('tambahResepPembayaran') }}",
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
                        defaultZero: true,
                    })
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
                url: "{{ route('tambahRacikanChildPembayaran') }}",
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

        function store() {
            var validation = 0;

            $('#data-transaksi .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null) {
                        $(this).addClass('is-invalid');
                        $(this).addClass('error');
                        $(par).find('.select2-container').addClass('is-invalid');
                        validation++
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).removeClass('error');
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#data-transaksi').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', idPembayaran);


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
                    overlay(true)
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('storePembayaran') }}',
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
                                // if (idPembayaran != 0) {
                                //     $('.owner_' + idPembayaran).remove();
                                // }
                                // $('#append-data').html('');
                                idPembayaran = null;
                                idAfter = data.id;
                                $('.button-before-checkout').addClass('hidden');
                                $('.button-after-checkout').removeClass('hidden');
                                printCheckout();

                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Perhatian',
                                    text: data.message,
                                    icon: "warning",
                                });
                            } else if (data.status == 3) {
                                Swal.fire({
                                    title: 'Perhatian',
                                    text: data.message,
                                    icon: "warning",
                                });
                                refreshStock()
                            }

                            overlay(false)
                        },
                        error: function(data) {
                            overlay(false)
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

        function refreshStock() {
            var ref = [];
            var jenis_stock = [];
            $('.ref').each(function() {
                var par = $(this).parents('tr');
                if ($(par).find('.stock').val() == 'YA') {
                    ref.push($(par).find('.ref').val());
                    jenis_stock.push($(par).find('.jenis_stock').val());
                }
            });

            $.ajax({
                url: "{{ route('refreshDataStock') }}",
                type: 'get',
                data: {
                    ref: ref,
                    jenis_stock: jenis_stock,
                },
                success: function(data) {
                    data.item_non_obat.forEach(d => {
                        $('.ref').each(function() {
                            var par = $(this).parents('tr');
                            if ($(par).find('.stock').val() == 'YA') {
                                if ($(par).find('.jenis_stock').val() == 'NON OBAT' || $(par)
                                    .find('.ref').val() == d.id) {
                                    $(par).find('.sisa_stock_text').text(d.stock_first.qty);
                                    $(par).find('.sisa_stock').val(d.stock_first.qty);
                                    $(par).find('.qty').val(d.stock_first.qty);
                                }
                            }
                        });

                    });
                    data.item.forEach(d => {
                        $('.ref').each(function() {
                            var par = $(this).parents('tr');
                            if ($(par).find('.stock').val() == 'YA') {
                                if ($(par).find('.jenis_stock').val() == 'OBAT' || $(par).find(
                                        '.ref').val() == d.id) {
                                    $(par).find('.sisa_stock_text').text(d.stock_first.qty);
                                    $(par).find('.sisa_stock').val(d.stock_first.qty);
                                    $(par).find('.qty').val(d.stock_first.qty);
                                }
                            }
                        });

                    });
                    $(parent).find('.append-racikan').append(data);
                },
                error: function(data) {
                    tambahChildRacikan(parents);
                    $(".loading-resep").addClass('hidden');
                }
            });
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

        $(document).on('keyup', '.qty', function() {
            var par = $(this).parents('tr');
            var qty = $(this).val() * 1
            var sisa = $(par).find('.sisa_stock').val() * 1;
            var harga = $(par).find('.harga').val().replace(/[^0-9\-]+/g, "") * 1;

            if (qty > sisa) {
                qty = sisa;
                $(this).val(qty);
            }

            $(par).find('.sub_total_text').html(accounting.formatNumber(qty * harga));
            $(par).find('.bruto_text').html(accounting.formatNumber(qty * harga));
            $(par).find('.sub_total').val(qty * harga);
            $(par).find('.bruto').val(qty * harga);
            calcItemDiskon();
            calcTotalLainLain()
        })

        $(document).on('change', '.qty', function() {
            var par = $(this).parents('tr');
            var qty = $(this).val() * 1
            var sisa = $(par).find('.sisa_stock').val() * 1;
            var harga = $(par).find('.harga').val().replace(/[^0-9\-]+/g, "") * 1;

            if (qty > sisa) {
                qty = sisa;
                $(this).val(qty);
            }

            $(par).find('.sub_total_text').html(accounting.formatNumber(qty * harga));
            $(par).find('.bruto_text').html(accounting.formatNumber(qty * harga));
            $(par).find('.sub_total').val(qty * harga);
            $(par).find('.bruto').val(qty * harga);
            calcItemDiskon();
            calcTotalLainLain()
        })

        $(document).on('keyup', '.harga', function() {
            var par = $(this).parents('tr');
            var qty = $(par).find('.qty').val() * 1
            var harga = $(par).find('.harga').val().replace(/[^0-9\-]+/g, "") * 1;
            console.log(qty);
            console.log(harga);
            $(par).find('.sub_total_text').html(accounting.formatNumber(qty * harga, {
                precision: 0
            }));
            $(par).find('.sub_total').val(qty * harga);
            $(par).find('.bruto').val(qty * harga);
            calcTotalLainLain()
        })

        $(document).on('keyup', '.diskon_penyesuaian', function() {
            var par = $(this).parents('tr');
            var qty = $(par).find('.qty').val();
            var bruto = $(par).find('.bruto').val();
            var diskon = $(this).val();

            if (diskon > 100) {
                diskon = 100;
                $(this).val(100);
            }

            console.log(qty)
            console.log(bruto)
            var nilaiDiskon = diskon / 100 * (bruto);
            var total = (bruto) - nilaiDiskon;
            $(par).find('.sub_total').val(total);
            $(par).find('.sub_total_text').text(accounting.formatNumber(total, {
                precision: 0
            }));

            $(par).find('.nilai_diskon_penyesuaian').val(accounting.formatNumber(nilaiDiskon, {
                precision: 0
            }));
            calcItemDiskon();
        })

        $(document).on('keyup', '.nilai_diskon_penyesuaian', function() {
            var par = $(this).parents('tr');
            var qty = $(par).find('.qty').val();
            var bruto = $(par).find('.bruto').val();
            var diskon = $(this).val().replace(/[^0-9\-]+/g, "") * 1;

            if (diskon > bruto) {
                diskon = bruto;
                $(this).val(accounting.formatNumber(diskon, {
                    precision: 0
                }));
            }

            var persenDiskon = diskon / bruto * 100;
            var total = (bruto) - diskon;
            $(par).find('.sub_total').val(total);
            $(par).find('.sub_total_text').text(accounting.formatNumber(total, {
                precision: 0
            }));

            $(par).find('.diskon_penyesuaian').val(accounting.formatNumber(persenDiskon, {
                precision: 0
            }));
            calcItemDiskon();
        })

        function calcTotalLainLain() {
            var total = 0;
            $('#append-lain-lain').find('.sub_total').each(function() {
                total += $(this).val().replace(/[^0-9\-]+/g, "") * 1;
            })

            $('#total_lain').val(accounting.formatNumber(total, {
                precision: 0
            }));

            $('#total_item_non_diskon').val(accounting.formatNumber(total, {
                precision: 0
            }));

            calcTotalBayar();
        }

        function calcItemDiskon() {
            var total = 0;

            $('#append-obat').find('.sub_total').each(function() {
                total += $(this).val() * 1;
                console.log($(this));
            })

            $('#total_obat').val(accounting.formatNumber(total, {
                precision: 0
            }));

            $('#total_item_diskon').val(accounting.formatNumber(total, {
                precision: 0
            }));
            calcTotalBayar();
        }

        function calcTotalBayar(param = 'rupiah') {
            var total = 0;
            var totalLain = $('#total_lain').val().replace(/[^0-9\-]+/g, "") * 1;
            if ($('#total_obat').length != 0) {
                var totalObat = $('#total_obat').val().replace(/[^0-9\-]+/g, "") * 1;
            } else {
                var totalObat = 0;
            }
            var total = totalLain + totalObat;
            var diskon = $('#diskon').val().replace(/[^0-9\-]+/g, "") * 1;
            var diskonPersen = $('#diskon_persen').val().replace(/[^0-9\-]+/g, "") * 1;
            var deposit = $('#deposit').val().replace(/[^0-9\-]+/g, "") * 1;


            if (param == 'persen') {
                if (diskonPersen > 100) {
                    diskonPersen = 100;
                    $('#diskon_persen').val(accounting.formatNumber(diskonPersen, {
                        precision: 0
                    }));
                }

                var temp = diskonPersen / 100 * total;
                diskon = temp;

                $('#diskon').val(accounting.formatNumber(diskon, {
                    precision: 0
                }));
            } else {

                var temp = diskon / total * 100;
                if (total == 0) {
                    temp = 0;
                }
                $('#diskon_persen').val(accounting.formatNumber(temp, {
                    precision: 0
                }));
            }

            if (diskon > total) {
                diskon = total;
                $('#diskon').val(accounting.formatNumber(diskon, {
                    precision: 0
                }));

                var temp = diskon / total * 100;
                $('#diskon_persen').val(accounting.formatNumber(temp, {
                    precision: 0
                }));
            }

            // total -= deposit;

            $('#total_bayar').val(accounting.formatNumber(total, {
                precision: 0
            }));

            var pembayaran = total - diskon;
            if (pembayaran < 0) {
                pembayaran = 0;
            }

            $('#pembayaran').val(accounting.formatNumber(pembayaran, {
                precision: 0
            }));

            var sisaPembayaran = pembayaran - deposit;
            if (sisaPembayaran < 0) {
                sisaPembayaran = 0;
            }

            $('#sisa_pembayaran').val(accounting.formatNumber(sisaPembayaran, {
                precision: 0
            }));

            calcKembalian();
        }

        function getTerbilang(nilai) {
            $.ajax({
                url: "{{ route('getTerbilang') }}",
                type: 'get',
                data: {
                    nilai: nilai,
                },
                success: function(data) {
                    $('#terbilang').html(data.data + ' rupiah');

                    if (nilai == 0) {
                        $('#terbilang').html('0 rupiah');
                    }
                },
                error: function(data) {
                    getTerbilang(nilai)
                }
            });
        }

        function calcKembalian() {
            var sisa_pembayaran = $('#sisa_pembayaran').val().replace(/[^0-9\-]+/g, "") * 1;
            var pembayaran = $('#pembayaran').val().replace(/[^0-9\-]+/g, "") * 1;
            var diterima = $('#diterima').val().replace(/[^0-9\-]+/g, "") * 1;
            var deposit = $('#deposit').val().replace(/[^0-9\-]+/g, "") * 1;
            var tarik_deposit = $('#tarik_deposit').val();
            var total_bayar = $('#total_bayar').val();
            var diskon = $('#diskon').val();

            if (tarik_deposit == 'YA') {
                var kembalian = diterima - (pembayaran - deposit);
            } else {
                var kembalian = diterima - sisa_pembayaran;

            }

            console.log(diterima);
            console.log(sisa_pembayaran);

            if (kembalian < 0) {
                kembalian = 0;
            }

            $('#uang_kembali').val(accounting.formatNumber(kembalian, {
                precision: 0
            }));
            $('#uang_kembali_text').html(accounting.formatNumber(kembalian, {
                precision: 0
            }));
            getTerbilang(kembalian);
        }

        function tambahItem(id, name, harga, sisaStock, type) {
            var validation = 0;
            $('#append-lain-lain').find('.ref').each(function() {
                var par = $(this).parents('tr');

                if (type == 'NON OBAT') {
                    if ($(this).val() == id && $(par).find('.table').val() == 'ms_item_non_obat' && $(par).find(
                            '.rekam_medis_pasien_id').val() == 'NON') {
                        validation++;
                    }
                } else {
                    if ($(this).val() == id && $(par).find('.table').val() == 'mo_produk_obat' && $(par).find(
                            '.rekam_medis_pasien_id').val() == 'NON') {
                        validation++;
                    }
                }

            });

            if (validation != 0) {
                $('#filter-item').val('');
                $('#append-list-item').html('');
                return ToastNotification('warning', 'Item ini sudah ditambahkan didalam list');
            };

            if (type == 'NON OBAT') {
                var table = 'ms_item_non_obat';
            } else {
                var table = 'mo_produk_obat';
            }

            if (itemTambahan == 'diskon') {
                var html = '<tr class="parent">' +
                    '<td class="text-center text-red-500" style="cursor: pointer"><i class="fa fa-trash text-red"' +
                    'aria-hidden="true" onclick="removeItem(this)"></i>' +
                    '</td>' +
                    '<td>' +
                    name +
                    '<input type="hidden" name="table[]" class="table"  value="' + table + '">' +
                    '<input type="hidden" name="ref[]" class="ref" value="' + id + '">' +
                    '<input type="hidden" name="stock[]" class="stock" value="YA">' +
                    '<input type="hidden" name="jenis_stock[]" class="jenis_stock" value="' + type + '">' +
                    '<input type="hidden" name="rekam_medis_pasien_id[]" class="rekam_medis_pasien_id" value="NON">' +
                    '<input type="hidden" name="pasiens_id[]" class="pasiens_id" value="NON">' +
                    '</td>' +
                    '<td>' +
                    '-' +
                    '</td>' +
                    '<td class="text-right">' +
                    accounting.formatNumber(harga, {
                        precision: 0
                    }) +
                    '<input type="hidden" name="harga[]" class="harga" value="' + harga + '">' +
                    '</td>' +
                    '<td class="text-center">' +
                    '<div class="input-group justify-center">' +
                    '<input type="text" id="qty" name="qty[]" class="form-control qty required text-right"' +
                    'placeholder="xxx,xxxx"' +
                    'style="width:50px">' +
                    '<div class="input-group-text">' +
                    '/' +
                    '<span class="sisa_stock_text">' + sisaStock + '</span>' +
                    '<input type="hidden" class="border-none sisa_stock" value="' + sisaStock + '">' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '<td class="text-right">' +
                    '<span class="bruto_text">0</span>' +
                    '<input type="hidden" name="bruto[]" class="bruto" value="' + 0 + '">' +
                    '</td>' +
                    '<td>' +
                    '<input type="number" max="100" maxlength="3" name="diskon_penyesuaian[]" value="" class="diskon_penyesuaian form-control text-center" style="width: 100%">' +
                    '</td>' +
                    '<td class="text-right">' +
                    '<input type="text" name="nilai_diskon_penyesuaian[]" value="0" class="nilai_diskon_penyesuaian form-control mask">' +
                    '</td>' +
                    '<td class="text-right">' +
                    '<span class="sub_total_text">0</span>' +
                    '<input type="hidden" name="sub_total[]" class="sub_total"  value="">' +
                    '</td>' +
                    '</tr>';
                $(html).insertBefore('.add-item-diskon');
            } else {
                var html = '<tr class="parent">' +
                    '<td class="text-center text-red-500" style="cursor: pointer"><i class="fa fa-trash text-red"' +
                    'aria-hidden="true" onclick="removeItem(this)"></i>' +
                    '</td>' +
                    '<td>' +
                    name +
                    '<input type="hidden" name="table[]" class="table"  value="' + table + '">' +
                    '<input type="hidden" name="ref[]" class="ref" value="' + id + '">' +
                    '<input type="hidden" name="stock[]" class="stock" value="YA">' +
                    '<input type="hidden" name="jenis_stock[]" class="jenis_stock" value="' + type + '">' +
                    '<input type="hidden" name="rekam_medis_pasien_id[]" class="rekam_medis_pasien_id" value="NON">' +
                    '<input type="hidden" name="pasiens_id[]" class="pasiens_id" value="NON">' +
                    '</td>' +
                    '<td>' +
                    '-' +
                    '</td>' +
                    '<td class="text-right">' +
                    accounting.formatNumber(harga, {
                        precision: 0
                    }) +
                    '<input type="hidden" name="harga[]" class="harga" value="' + harga + '">' +
                    '</td>' +
                    '<td class="text-center">' +
                    '<div class="input-group justify-center">' +
                    '<input type="text" id="qty" name="qty[]" class="form-control qty required mask text-right"' +
                    'placeholder="xxx,xxxx"' +
                    'style="width:50px">' +
                    '<div class="input-group-text">' +
                    '/' +
                    '<span class="sisa_stock_text">' + sisaStock + '</span>' +
                    '<input type="hidden" class="border-none sisa_stock" value="' + sisaStock + '">' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '<td class="text-right">' +
                    '<span class="bruto_text">0</span>' +
                    '<input type="hidden" name="bruto[]" class="bruto" value="' + 0 + '">' +
                    '<input type="hidden" name="diskon_penyesuaian[]" class="diskon_penyesuaian" value="' + 0 + '">' +
                    '<input type="hidden" name="nilai_diskon_penyesuaian[]" class="nilai_diskon_penyesuaian" value="' + 0 +
                    '">' +
                    '<input type="hidden" name="sub_total[]" class="sub_total" value="' + 0 + '">' +
                    '</td>' +
                    '</tr>';
                $(html).insertBefore('.add-item-non-diskon');
            }
            $('.mask').maskMoney({
                precision: 0,
                thousands: ',',
                allowZero: true,
                defaultZero: true,
            })

            $('#filter-item').val('');
            $('#append-list-item').html('');

            $('.tidak-ada-data').addClass('hidden');
            closeSideMenuKasir();
        }

        function openSideMenuKasir(param) {
            itemTambahan = param;
            $('#filter-item').focus();
            $('#side-menu-kasir').removeClass('close');
        }

        function closeSideMenuKasir() {
            $('#side-menu-kasir').addClass('close');
        }

        function removeItem(child) {
            $(child).parents('tr').remove()

            if ($('#append-lain-lain').find('tr').length == 2) {
                $('.tidak-ada-data').removeClass('hidden');
            }

            calcItemDiskon();
            calcTotalLainLain();
        }

        function generateListItemKasir() {
            $.ajax({
                url: "{{ route('generateItemKasir') }}",
                type: 'get',
                data: {
                    id: idPembayaran,
                    param() {
                        return $('#filter-item').val();
                    },
                    jenis: itemTambahan,
                },
                success: function(data) {
                    $('#append-list-item').html(data);
                },
                error: function(data) {
                    generateListItemKasir()
                }
            });
        }

        function printCheckout() {
            if (idAfter != null) {
                window.open('{{ route('printPembayaran') }}?id=' + idAfter);
            }
        }

        $(document).on('change', '#metode_pembayaran', function() {
            if ($(this).val() == 'TUNAI') {
                $('.non-tunai').addClass('hidden');
            } else {
                $('.non-tunai').removeClass('hidden');
            }
            $('.non-tunai').find('input').val('');
        })

        function pilihDeposit(id) {
            $.ajax({
                url: "{{ route('pilihDepositPembayaran') }}",
                type: 'get',
                data: {
                    id: id,
                },
                success: function(data) {
                    $('#kode_deposit').val(data.data.kode);

                    $('#sisa_deposit').val(accounting.formatNumber(data.data.sisa_deposit, {
                        precision: 0
                    }));
                    $('#deposit').val(accounting.formatNumber(data.data.sisa_deposit, {
                        precision: 0
                    }));
                    const el = document.querySelector("#modal-deposit");
                    const modalRekamMedis = tailwind.Modal.getOrCreateInstance(el);
                    modalRekamMedis.toggle();
                    calcTotalBayar();
                },
                error: function(data) {
                    pilihDeposit(id)
                }
            });
        }

        function hapusDeposit(params) {
            $('#kode_deposit').val('');
            $('#deposit').val('');
            $('#sisa_deposit').val('');
            calcTotalBayar();
        }


        function kirimKeEmail() {
            overlay(true);
            $.ajax({
                url: "{{ route('sendInvoicePembayaran') }}",
                type: 'get',
                data: {
                    id: idAfter,
                    email() {
                        return $('#email').val();
                    },
                },
                success: function(data) {
                    Swal.fire({
                        title: data.message,
                        icon: 'success',
                    });
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
    </script>
@endsection
