@extends('layout.master')
@section('parentPageTitle', 'Tables')
@section('title', 'Amore | Kasir')


@section('content')
    <style>
        .select-racikan {
            color: hsl(240, 1%, 68%);
        }

        .select-racikan.active {
            color: #c70039 !important;
        }

    </style>
    <!-- Page header section  -->
    <div class="block-header">
        <div class="d-flex clearfix">
            <div class="col-xl-5 col-md-5 col-sm-12">
                <h1>Hi, {{ ucwords(Auth::user()->name) }}</h1>
                <span>Anda sedang berada di Halaman History Kasir</span>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label>Tanggal Awal</label>
                            <input type="text" id="tanggal_awal" class="form-control datepicker"
                                value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label>Tanggal Akhir</label>
                            <input type="text" id="tanggal_akhir" class="form-control datepicker"
                                value="{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                        </div>
                        @if (Auth::user()->akses('global'))
                            <div class="col-sm-6 mb-3">
                                <label>Cabang</label>
                                <select name="branch_id_filter" id="branch_id_filter" class="select2filter form-control">
                                    <option value="">Semua Branch</option>
                                    @foreach (\App\Branch::get() as $item)
                                        @if (Auth::user()->akses('global'))
                                            <option value="{{ $item->id }}">{{ $item->kode }} {{ $item->lokasi }}
                                            </option>
                                        @else
                                            <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->kode }} {{ $item->lokasi }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-sm-6 mb-3">
                            <label>Dokter Pemeriksa</label>
                            <select name="user_id_filter" id="user_id_filter" class="select2filter form-control">
                                <option value="">Semua Dokter</option>
                                @foreach ($dokter as $item)
                                    <option value="{{ $item->id }}">{{ $item->Branch->kode }} | {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class=" table-responsive">
                        <table class="table table-bordered" style="width: 100%" id="table">
                            <thead>
                                <th>No</th>
                                <th>Branch</th>
                                <th>No. Pendaftaran</th>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>Nama Owner</th>
                                <th>Tanggal Periksa</th>
                                <th>Dokter Pemeriksa</th>
                                <th>Berat</th>
                                <th>Suhu</th>
                                <th>Anamnese</th>
                                <th>Hasil Pemeriksaan</th>
                                <th>Tindakan Bedah</th>
                                <th>Anestesi</th>
                                <th>Rawat Inap</th>
                                <th>Rawat Jalan</th>
                                <th>Grooming</th>
                                <th>Titip Sehat</th>
                                <th>Ruang Rawat Inap</th>
                                <th>Catatan</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="modal-tambah-diagnosa" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 d-flex mb-3 parent-diagnosa">
                            <textarea name="diagnosa" placeholder="cth: Radang Usus" class="form-control diagnosa required" cols="2"
                                rows="2"></textarea>
                            <input type="hidden" name="jenis" value="diagnosa">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="tambahDiagnosa()">Tambahkan Data
                        Diagnosa</button>
                </div>
            </div>
        </div>
    </form>

    <form id="modal-tambah-treatment" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 d-flex mb-3 parent-treatment">
                            <textarea name="treatment" placeholder="cth: Memberi perban" class="form-control treatment required" cols="2"
                                rows="2"></textarea>
                            <input type="hidden" name="jenis" value="treatment">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="tambahTreatment()">Tambahkan Data
                        Treatment</button>
                </div>
            </div>
        </div>
    </form>

    <form id="modal-tambah-tindakan" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 d-flex mb-3 parent-tindakan">
                            <select name="tindakan_id" id="tindakan_id" class="form-control tindakan_id select2 required">
                            </select>
                            <input type="hidden" name="jenis" value="tindakan">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="tambahTindakan()">Tambahkan Data
                        Tindakan</button>
                </div>
            </div>
        </div>
    </form>

    <form id="modal-tambah-resep" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <span for="" class="d-flex justify-content-between align-middle mb-3">
                                <b>Obat</b>
                                <button type="button" class="btn btn-primary btn-xs" onclick="appendResep()" id="add-resep"><i
                                        class="fa fa-plus"></i>
                                    Tambah Obat</button>
                            </span>
                            <div class="row clearfix" id="append-resep">
                            </div>
                            <input type="hidden" name="jenis" value="resep">
                            <div class="loading-resep w-100 text-center hidden">
                                <i class="fa fa-circle-o-notch fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="tambahResep()">Tambahkan Data
                        Obat</button>
                </div>
            </div>
        </div>
    </form>

    <form id="modal-tambah-hasil-lab" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 d-flex mb-3 parent-hasil-lab">
                            <input type="file" class="dropify hasil_lab mb-2 required" id="dropify" name="hasil_lab[]"
                                data-allowed-file-extensions="pdf jpeg jpg">
                            <input type="hidden" name="jenis" value="hasil lab">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="tambahHasilLab()">Tambahkan Data
                        Hasil Lab</button>
                </div>
            </div>
        </div>
    </form>
@stop

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert/sweetalert.css') }}">
@stop
@section('vendor-script')
    <script src="{{ asset('assets/bundles/datatablescripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatable/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
@stop

@section('page-script')
    <script src="{{ asset('assets/js/pages/tables/jquery-datatable.js') }}"></script>

    <script type="text/javascript">
        var idApotek;
        var table;
        var indexRacikan = 1;
        $(document).ready(function() {
            $('body').addClass('toggle_menu_active')
            table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('datatablePenerimaanPasien') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tanggal_awal() {
                            return $('#tanggal_awal').val();
                        },
                        tanggal_akhir() {
                            return $('#tanggal_akhir').val();
                        },
                        user_id_filter() {
                            return $('#user_id_filter').val();
                        },
                        branch_id_filter() {
                            return $('#branch_id_filter').val();
                        }
                    }
                },
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
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    class: 'text-center'
                }, {
                    data: 'branch',
                    name: 'branch',
                }, {
                    data: 'kode_pendaftaran',
                    name: 'kode_pendaftaran',
                }, {
                    data: 'kode_rm',
                    name: 'kode_rm',
                }, {
                    data: 'pasien',
                    name: 'pasien',
                }, {
                    data: 'owner',
                    name: 'owner',
                }, {
                    data: 'tanggal',
                    name: 'tanggal',
                }, {
                    data: 'dokter',
                    name: 'dokter',
                }, {
                    data: 'berat',
                    name: 'berat',
                    class: 'text-center'
                }, {
                    data: 'suhu',
                    name: 'suhu',
                    class: 'text-center'
                }, {
                    data: 'gejala',
                    name: 'gejala',
                    class: 'text-center'
                }, {
                    data: 'hasil_pemeriksaan',
                    name: 'hasil_pemeriksaan',
                    class: 'text-center'
                }, {
                    data: 'tindakan_bedah',
                    name: 'tindakan_bedah',
                    class: 'text-center'
                }, {
                    data: 'bius',
                    name: 'bius',
                    class: 'text-center'
                }, {
                    data: 'rawat_inap',
                    name: 'rawat_inap',
                    class: 'text-center'
                }, {
                    data: 'rawat_jalan',
                    name: 'rawat_jalan',
                    class: 'text-center'
                }, {
                    data: 'grooming',
                    name: 'grooming',
                    class: 'text-center'
                }, {
                    data: 'titip_sehat',
                    name: 'titip_sehat',
                    class: 'text-center'
                }, {
                    data: 'kamar',
                    name: 'kamar',
                    class: 'text-center'
                }, {
                    data: 'catatan',
                    name: 'catatan',
                    class: 'text-center'
                }, ]
            });
            $('.mask').maskMoney({
                precision: 0,
                thousands: ','
            })

            $('.select2').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                theme: 'bootstrap4',
                width: '100%'
            })

            $('.select2filter').select2({
                theme: 'bootstrap4',
                width: '100%'
            })


            $("#telpon").mask("999-9999-99999");
            $('[data-toggle="tooltip"]').tooltip({
                delay: {
                    "show": 3000,
                    "hide": 100
                }
            });

            $(document).on('click', '.list-group-item-action', function() {
                var par = $(this).parents('.list-group');
                $(par).find('.list-group-item-action').removeClass('active');
                $(this).addClass('active');
            })


            $("#tindakan_id").select2({
                dropdownParent: $("#modal-tambah-tindakan .modal-body .parent-tindakan"),
                theme: "bootstrap4",
                width: '100%',
                tags: true,
                ajax: {
                    url: "{{ route('select2Apotek') }}?param=tindakan_id",
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
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
            }).on('changeDate', function() {
                table.ajax.reload();
            });
        });

        function openSideMenuKasir() {
            $('#side-menu-kasir').removeClass('close');
        }

        function closeSideMenuKasir() {
            $('#side-menu-kasir').addClass('close');
        }

        function store() {
            var validation = 0;

            $('#append-data .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '') {
                        $(this).addClass('is-invalid');
                        $(this).addClass('error');
                        validation++
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).removeClass('error');
                    }
                }
            })

            if (validation != 0) {
                ToastNotification('warning',"Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#append-data').serializeArray();


            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', idKasir);

            var previousWindowKeyDown = window.onkeydown;
            swal({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                window.onkeydown = previousWindowKeyDown;
                $.ajax({
                    url: '{{ route('storeKasir') }}',
                    data: formData,
                    type: 'post',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 1) {
                            swal({
                                title: data.message,
                                type: "success",

                            });
                            clear();
                            $('.pasien_' + idKasir).remove();
                            $('#append-data').html('');
                            idKasir = null;
                        } else if (data.status == 2) {
                            swal({
                                title: data.message,
                                type: "warning",
                            });
                        } else {
                            swal({
                                title: 'Ada Kesalahan !!!',
                                text: data,
                                type: "warning",
                                html: true,
                            });
                        }
                    },
                    error: function(data) {
                        var html = '';
                        Object.keys(data.responseJSON).forEach(element => {
                            html += data.responseJSON[element][0] + '<br>';
                        });
                        swal({
                            title: 'Ada Kesalahan !!!',
                            text: data.responseJSON.message == undefined ? html : data
                                .responseJSON.message,
                            icon: "error",
                            html: true,
                        });
                    }
                });
            });
        }

        function openModal(modal) {
            $(modal).modal('toggle');
        }

        function tambahItem(id, name, harga, sisaStock) {
            var validation = 0;
            $('#append-lain-lain').find('.ref').each(function() {
                var par = $(this).parents('tr');
                if ($(this).val() == id && $(par).find('.table').val() == 'ms_item_non_obat') {
                    validation++;
                }
            });

            if (validation != 0) {

                $('#filter-item').val('');
                $('#append-list-item').html('');
                return ToastNotification('warning','Item ini sudah ditambahkan didalam list');
            }
            var html = '<tr>' +
                '<td class="text-center" style="cursor: pointer"><i class="fa fa-trash text-red"' +
                'aria-hidden="true" onclick="removeItem(this)"></i>' +
                '</td>' +
                '<td>' +
                '-' +
                '<input type="hidden" name="table[]" class="table"  value="ms_item_non_obat">' +
                '<input type="hidden" name="ref[]" class="ref" value="' + id + '">' +
                '<input type="hidden" name="stock[]" class="stock" value="YA">' +
                '</td>' +
                '<td>' + name + '</td>' +
                '<td class="text-right">' +
                accounting.formatNumber(harga) +
                '<input type="hidden" name="harga[]" class="harga" value="' + harga + '">' +
                '</td>' +
                '<td class="text-center">' +
                '<input type="text" class="border qty text-center required" name="qty[]" style="border: none;width:25px">&nbsp;/&nbsp;' +
                sisaStock +
                '<input type="hidden" class="border-none sisa_stock" value="' + sisaStock + '">' +
                '</td>' +
                '<td class="text-right">' +
                '<span class="sub_total_text">0</span>' +
                '<input type="hidden" name="sub_total[]" class="sub_total" value="' + 0 + '">' +
                '</td>' +
                '</tr>';
            $(html).insertBefore('.add-item');

            $('#filter-item').val('');
            $('#append-list-item').html('');
            closeSideMenuKasir();
        }

        $(document).on('keyup', '.qty', function() {
            var par = $(this).parents('tr');
            var qty = $(this).val() * 1
            var sisa = $(par).find('.sisa_stock').val() * 1;
            var harga = $(par).find('.harga').val() * 1;

            if (qty > sisa) {
                qty = sisa;
                $(this).val(qty);
            }

            $(par).find('.sub_total_text').html(accounting.formatNumber(qty * harga));
            $(par).find('.sub_total').val(qty * harga);
            calcTotalLainLain()
        })

        $(document).on('keyup', '#diskon_penyesuaian', function() {
            var par = $(this).parents('tr');
            var subTotalObat = 0;
            $('#append-obat').find('.sub_total').each(function() {
                subTotalObat += $(this).val() * 1;
            })

            console.log(subTotalObat);
            var diskonPeny = $('#diskon_penyesuaian').val().replace(/[^0-9\-]+/g, "") * 1;

            if (diskonPeny > subTotalObat) {
                diskonPeny = subTotalObat;
                $(this).val(accounting.formatNumber(diskonPeny));
            }
            calcTotalObat();
        })

        function calcTotalLainLain() {
            var total = 0;
            $('#append-lain-lain').find('.sub_total').each(function() {
                total += $(this).val() * 1;
            })
            $('#total_lain').val(accounting.formatNumber(total));
            calcTotalBayar();
        }

        function calcTotalObat() {
            var total = 0;
            var diskonPeny = $('#diskon_penyesuaian').val().replace(/[^0-9\-]+/g, "") * 1;
            $('#append-obat').find('.sub_total').each(function() {
                total += $(this).val() * 1;
            })

            $('#total_obat').val(accounting.formatNumber(total - diskonPeny));
            calcTotalBayar();
        }

        function calcTotalBayar() {
            var total = 0;
            var totalLain = $('#total_lain').val().replace(/[^0-9\-]+/g, "") * 1;
            if ($('#total_obat').length != 0) {
                var totalObat = $('#total_obat').val().replace(/[^0-9\-]+/g, "") * 1;
            } else {
                var totalObat = 0;
            }
            var total = totalLain + totalObat;
            var diskon = $('#diskon').val().replace(/[^0-9\-]+/g, "") * 1;
            var deposit = $('#deposit').val().replace(/[^0-9\-]+/g, "") * 1;


            total -= deposit;
            if (diskon > total) {
                diskon = total;
                $('#diskon').val(accounting.formatNumber(diskon));
            }
            $('#total_bayar').val(accounting.formatNumber(total + deposit));
            $('#pembayaran').val(accounting.formatNumber(total - diskon));

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
                },
                error: function(data) {
                    tambahResep();
                    $(".loading-resep").addClass('hidden');
                }
            });
        }

        function calcKembalian() {
            var pembayaran = $('#pembayaran').val().replace(/[^0-9\-]+/g, "") * 1;
            var diterima = $('#diterima').val().replace(/[^0-9\-]+/g, "") * 1;

            $('#uang_kembali').val(accounting.formatNumber(diterima - pembayaran));
            $('#uang_kembali_text').html(accounting.formatNumber(diterima - pembayaran));
            getTerbilang(diterima - pembayaran);
        }

        function removeItem(child) {
            $(child).parents('tr').remove()
            calcTotalLainLain();
        }

        function generateListItemKasir() {
            $.ajax({
                url: "{{ route('generateItemKasir') }}",
                type: 'get',
                data: {
                    id: idKasir,
                    param() {
                        return $('#filter-item').val();
                    }
                },
                success: function(data) {
                    $('#append-list-item').html(data);
                },
                error: function(data) {
                    generateListItemKasir()
                }
            });
        }

        function printCheckout(id) {
            window.open('{{ route('printKasir') }}?id=' + id);
        }

        function tambahChildRacikan(child) {
            var parent = $(child).parents('.parent-resep');
            $.ajax({
                url: "{{ route('tambahRacikanChildKasir') }}",
                type: 'get',
                data: {
                    index: $(parent).find('.index_racikan').val(),
                    id: idKasir,
                },
                success: function(data) {
                    $(parent).find('.append-racikan').append(data);

                    $('.select2filter').select2({
                        width: '100%',
                        theme: 'bootstrap4',
                    })
                },
                error: function(data) {
                    tambahResep();
                    $(".loading-resep").addClass('hidden');
                }
            });
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

        $(document).on('keyup', '.racikan_qty', function() {
            var par = $(this).parents('.parent-child-racikan');
            var qty = $(this).val() * 1;
            var sisa = $(par).find('.racikan_sisa_qty').val() * 1;

            if (qty > sisa) {
                qty = sisa;
                $(this).val(qty);
            }
        })

        $(document).on('keyup', '.qty_non_racikan', function() {
            var par = $(this).parents('.non-racikan');
            var qty = $(this).val() * 1;
            var sisa = $(par).find('.sisa_qty_non_racikan').val() * 1;
            console.log(qty);
            console.log(sisa);
            if (qty > sisa) {
                qty = sisa;
                $(this).val(qty);
            }
        })

        $(document).on('blur', '.racikan_qty', function() {
            var par = $(this).parents('.parent-child-racikan');
            var qty = $(this).val() * 1;
            var sisa = $(par).find('.racikan_sisa_qty').val() * 1;

            if (qty > sisa) {
                qty = sisa;
                $(this).val(qty);
            }
        })

        $(document).on('blur', '.qty_non_racikan', function() {
            var par = $(this).parents('.non-racikan');
            var qty = $(this).val() * 1;
            var sisa = $(par).find('.sisa_qty_non_racikan').val() * 1;
            if (qty > sisa) {
                qty = sisa;
                $(this).val(qty);
            }
        })

        function hapus(param, id_detail) {
            var previousWindowKeyDown = window.onkeydown;
            console.log(id_detail);
            swal({
                title: "Hapus Data",
                text: "Data yang telah dihapus tidak bisa dikembalikan.",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                window.onkeydown = previousWindowKeyDown;
                $.ajax({
                    url: '{{ route('deleteKasir') }}',
                    data: {
                        id: idKasir,
                        id_detail: id_detail,
                        param: param,
                        _token: "{{ csrf_token() }}",

                    },
                    type: 'post',
                    success: function(data) {
                        if (data.status == 1) {
                            swal({
                                title: data.message,
                                type: "success",

                            });
                            clear();
                        } else if (data.status == 2) {
                            swal({
                                title: data.message,
                                type: "warning",
                            });
                        } else {
                            swal({
                                title: data.message,
                                type: "warning",
                                html: true,
                            });
                        }

                        pilihPasien('', idKasir);
                    },
                    error: function(data) {
                        var html = '';
                        Object.keys(data.responseJSON).forEach(element => {
                            html += data.responseJSON[element][0] + '<br>';
                        });
                        swal({
                            title: 'Ada Kesalahan !!!',
                            text: data.responseJSON.message == undefined ? html : data
                                .responseJSON.message,
                            icon: "error",
                            html: true,
                        });
                    }
                });
            });
        }

        function hapusTreatment(child) {
            $(child).parents('.parent-treatment').remove();
        }

        function hapusResep(child) {
            $(child).parents('.parent-resep').remove();
        }

        function hapusRacikanChild(child) {
            $(child).parents('.parent-child-racikan').remove();
        }

        function hapusHasilLab(child) {
            $(child).parents('.hasil-lab-parent').remove();
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

        $(document).on('change', '.tindakan_id', function() {
            var par = $(this).parents('.parent-treatment');
            var tarif = $(this).find('option:selected').data('tarif');
            $(par).find('.tarif_treatment').val(accounting.formatNumber(tarif));

        })

        function pilihPasien(child, id = null) {

            if (id == null) {
                id = $(child).data('id');
            }

            $.ajax({
                url: "{{ route('getKasir') }}",
                type: 'get',
                data: {
                    id: id,
                    edit: false,
                },
                success: function(data) {
                    $('#append-data').html(data);
                    $('.select2filter').select2({
                        theme: 'bootstrap4',
                        width: '100%'
                    })
                    $('.mask').maskMoney({
                        precision: 0,
                        thousands: ',',
                        allowZero: true,
                        defaultZero: true,
                    })
                    idKasir = id;
                    indexRacikan = 0;
                    calcTotalBayar();
                },
                error: function(data) {
                    pilihPasien(child, id = null);
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
    </script>
@stop
