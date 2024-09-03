@extends('layout.master')
@section('parentPageTitle', 'Tables')
@section('title', 'Amore | Penerimaan Stock')


@section('content')
    <!-- Page header section  -->
    <div class="block-header">
        <div class="row clearfix">
            <div class="col-xl-5 col-md-5 col-sm-12">
                <h1>Hi, {{ ucwords(Auth::user()->name) }}</h1>
                <span>Anda sedang berada di Halaman Data Penerimaan Stock</span>
            </div>
            <div class="col-xl-7 col-md-7 col-sm-12 text-md-right">
                <div class="d-flex align-items-center justify-content-lg-end mt-4 mt-lg-0 flex-wrap vivify pullUp delay-550">
                    <div class="border-right pr-4 mr-4 mb-2 mb-xl-0 hidden-xs">
                        <p class="text-muted mb-1">Total <span id="mini-bar-chart3" class="mini-bar-chart"></span></p>
                        <center>
                            <h5 class="mb-0">{{ \App\Models\PenerimaanStock::count() }}</h5>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>Penerimaan Stock <small></small></h2>
                    <ul class="header-dropdown dropdown">
                        <li><a href="javascript:void(0);" class="full-screen"><i class="icon-frame"></i></a></li>
                        <li class="dropdown">
                            <a href="{{ route('penerimaan-stock') }}" class="btn btn-primary btn-round"
                                style="color: white;">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card">
                                <div class="body">
                                    <form id="form-data">
                                        <h5><b>Informasi Pengirim</b></h5>
                                        <fieldset>
                                            <div class="row clearfix">
                                                <div class="col-sm-6 mb-3">
                                                    <div class="form-group c_form_group readonly disabled">
                                                        <label>Kode Penerimaan Stock</label>
                                                        <input type="text" class="kode form-control"
                                                            value="{{ $data->kode }}" id="kode" name="kode">
                                                        <input type="hidden" value="{{ $data->id }}" name="id">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <div class="form-group c_form_group disabled readonly">
                                                        <label>Tanggal Terima Barang<span style="color:red;font-weight:bold"
                                                                class="important"> *</span></label>

                                                        <input id="tanggal_terima" type="text"
                                                            class="form-control border-none required" name="tanggal_terima"
                                                            autocomplete="tanggal_terima"
                                                            placeholder="Masukan Tanggal terima"
                                                            value="{{ CarbonParse($data->tanggal_terima,'Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <div class="form-group c_form_group disabled readonly">
                                                        <label>Branch</label>
                                                        <select name="branch_id" id="branch_id"
                                                            class="form-control branch_id select2validate required">
                                                            <option value="">Pilih Branch</option>
                                                            @foreach (\App\Models\Branch::where('status', true)->get() as $item)
                                                                <option
                                                                    {{ $data->branch_id == $item->id ? 'selected' : '' }}
                                                                    value="{{ $item->id }}">{{ $item->kode }}
                                                                    {{ $item->lokasi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-3">
                                                    <div class="form-group c_form_group disabled">
                                                        <label>Branch Pengirim <span style="color:red;font-weight:bold"
                                                                class="important"> *</span>
                                                            <a href="javascript:;"
                                                                onclick="window.open('{{ route('supplier') }}')"><i
                                                                    class="fa fa-plus text-info"></i>
                                                            </a>
                                                        </label>
                                                        <select name="branch_pengirim" id="branch_pengirim"
                                                            class="form-control branch_pengirim select2validate required">
                                                            @foreach (\App\Models\Branch::where('status', true)->get() as $item)
                                                                <option
                                                                    {{ $data->PengeluaranStock->branch_id == $item->id ? 'selected' : '' }}
                                                                    value="{{ $item->id }}">{{ $item->kode }}
                                                                    {{ $item->lokasi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        {{ csrf_field() }}
                                                    </div>
                                                </div>

                                                <div class="col-sm-4 mb-3">
                                                    <div class="form-group c_form_group">
                                                        <label>File Faktur <span style="color:red;font-weight:bold"
                                                                class="important"> *</span></label>
                                                        <input type="file" class="dropify" id="dropify"
                                                            name="file_faktur"
                                                            data-allowed-file-extensions="jpeg png jpg pdf">
                                                    </div>
                                                </div>
                                                <div class="col-sm-8 mb-3">
                                                    <div class="form-group c_form_group">
                                                        <label>Nomor Faktur <span style="color:red;font-weight:bold"
                                                                class="important"> *</span></label>
                                                        <input id="nomor_faktur" type="text"
                                                            class="form-control border-none required text-uppercase"
                                                            name="nomor_faktur" value="{{ $data->nomor_faktur }}"
                                                            autocomplete="nomor_faktur" placeholder="Masukan Nomor Faktur"
                                                            autofocus>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 mb-3">
                                                    <div class="form-group c_form_group">
                                                        <label>Keterangan <span style="color:red;font-weight:bold"
                                                                class="important"> *</span></label>
                                                        <textarea id="keterangan" type="text" class="form-control required"
                                                            name="keterangan" required autocomplete="keterangan"
                                                            placeholder="Masukan Keterangan"
                                                            autofocus>{{ $data->description }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <h5 class="d-flex justify-content-between mb-3">
                                            <b>Informasi Item</b>
                                        </h5>
                                        <fieldset>
                                            <div class="row clearfix">
                                                <div class="col-sm-12 table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <th>Jenis Item</th>
                                                            <th>Item</th>
                                                            <th>Satuan</th>
                                                            <th>Harga Satuan</th>
                                                            <th>Qty</th>
                                                            <th>Total</th>
                                                            <th>Opsi</th>
                                                        </thead>
                                                        <tbody id="append-data">
                                                            @foreach ($data->PenerimaanStockDetail as $i => $item)
                                                                <tr>
                                                                    <td>
                                                                        {{ $item->jenis_stock }}
                                                                        <input type="hidden" class="jenis_item"
                                                                            name="jenis_item[]"
                                                                            value="{{ $item->jenis_stock }}">
                                                                        <input type="hidden" class="item_kode"
                                                                            name="item_kode[]"
                                                                            value="{{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->kode : $item->ItemNonObat->kode }}">
                                                                        <input type="hidden" class="item_name"
                                                                            name="item_name[]"
                                                                            value="{{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->name : $item->ItemNonObat->name }}">
                                                                        <input type="hidden" class="item_id"
                                                                            name="item_id[]"
                                                                            value="{{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->id : $item->ItemNonObat->id }}">
                                                                        <input type="hidden" class="satuan"
                                                                            name="satuan[]"
                                                                            value="{{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->Satuan->kode : $item->ItemNonObat->Satuan->kode }}">
                                                                        <input type="hidden" class="harga_satuan"
                                                                            name="harga_satuan[]"
                                                                            value="{{ number_format($item->harga_satuan) }}">
                                                                        <input type="hidden" class="qty"
                                                                            name="qty[]" value="{{ $item->qty }}">
                                                                        <input type="hidden" class="total_harga"
                                                                            name="total_harga[]"
                                                                            value="{{ number_format($item->total_harga) }}">
                                                                        <input type="hidden" class="index"
                                                                            name="index[]" value="{{ $i }}">
                                                                    </td>
                                                                    <td>
                                                                        {{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->kode : $item->ItemNonObat->kode }}
                                                                        <br>
                                                                        {{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->name : $item->ItemNonObat->name }}
                                                                    </td>
                                                                    <td>
                                                                        {{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->Satuan->kode : $item->ItemNonObat->Satuan->kode }}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {{ number_format($item->harga_satuan) }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ number_format($item->qty) }}
                                                                    </td>
                                                                    <td class="text-right">
                                                                        {{ number_format($item->total_harga) }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="btn-group">
                                                                            <button type="button"
                                                                                class="btn btn-info btn-rounded"
                                                                                onclick="edit(this)"><i
                                                                                    class="fa fa-pencil"
                                                                                    aria-hidden="true"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot id="hidden-when-not-empty" class="hidden">
                                                            <tr>
                                                                <td colspan="7" class="text-center">Belum ada item
                                                                    yang ditambahkan</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <button type="button" class="btn btn-primary btn-round" id="simpan"
                                            style="right: 80px;position: fixed;bottom: 30px;opacity: 0;transition: opacity 500ms;"
                                            style="" id="tambah-data" onclick="store()">
                                            <i class="fa fa-save"></i> Update
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-tambah-data" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <div class="form-group c_form_group disabled">
                                <label>Jenis Item</label>
                                <select name="jenis_item" id="jenis_item" class="form-control jenis_item select2 required">
                                    <option value="">Pilih Jenis Item</option>
                                    @foreach (\App\Models\PenerimaanStockDetail::$enumJenisStock as $item)
                                        <option data-name="{{ $item }}" value="{{ $item }}">
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="index">
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-group c_form_group disabled">
                                <label>Item</label>
                                <select id="item_id" class="form-control select2 required">
                                </select>
                                <input type="hidden" id="satuan">
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group c_form_group disabled">
                                <label>Harga Satuan <span style="color:red;font-weight:bold" class="important">
                                        *</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input id="harga_satuan" type="text" class="form-control required mask text-right"
                                        name="harga_satuan" value="" required autocomplete="harga_satuan"
                                        placeholder="Masukan Harga Satuan" onkeyup="calculateTotal()">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group c_form_group">
                                <label>Qty<span style="color:red;font-weight:bold" class="important">
                                        *</span></label>
                                <div class="input-group">
                                    <input id="qty" type="text" class="form-control required mask" name="qty" value=""
                                        required autocomplete="qty" placeholder="Masukan Qty" onkeyup="calculateTotal()">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="satuan_text"></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group c_form_group disabled readonly">
                                <label>Total <span style="color:red;font-weight:bold" class="important">
                                        *</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input id="total_harga" type="text" class="form-control text-right" name="total_harga"
                                        value="0" required autocomplete="total_harga" placeholder="Harga Total" autofocus>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="tambahItem()">Tambahkan Data item</button>
                </div>
            </div>
        </div>
    </div>
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
    <script src="{{ asset('assets/vendor/jquery-validation/jquery.validate.js') }}"></script>
    <script type="text/javascript">
        var index = 0;
        var table;
        var ownerId;
        var pasienId;
        var lastKnownScrollPosition = 0;
        var ticking = false;
        $(document).ready(function() {
            $('.mask').maskMoney({
                precision: 0,
                thousands: ','
            })

            $('.select2validate').select2({
                theme: 'bootstrap4',
                width: '100%'
            })

            $("#telpon").inputmask({
                "mask": "9999999999999"
            });
            $('[data-toggle="tooltip"]').tooltip({
                delay: {
                    "show": 3000,
                    "hide": 100
                }
            });

            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Drag and drop or click to replace',
                    'remove': 'Remove',
                    'error': 'Ooops, something wrong happended.'
                }
            });


            $("#supplier_id").select2({
                theme: "bootstrap4",
                width: '100%',
                ajax: {
                    url: "{{ route('select2PenerimaanStock') }}?param=supplier_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            branch_id() {
                                return $('#branch_id').val();
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
                placeholder: 'Masukan Nama Supplier',
                minimumInputLength: 0,
                templateResult: formatRepoNormal,
                templateSelection: formatRepoNormalSelection
            });

            $("#item_id").select2({
                theme: "bootstrap4",
                width: '100%',
                ajax: {
                    url: "{{ route('select2PenerimaanStock') }}?param=item_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            jenis_item() {
                                return $('#jenis_item').val();
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
                placeholder: 'Cari Item',
                minimumInputLength: 0,
                templateResult: formatRepoNormal,
                templateSelection: formatRepoNormalSelection
            });

            $('#item_id').on('select2:select', function(event) {
                var data = event.params.data;
                $('#satuan').val(data.satuan.kode);
                $('#satuan_text').html(data.satuan.kode);
            })

            $('#jenis_item').on('select2:select', function(event) {
                $("#item_id").val(null).trigger('change.select2');
                $("#item_id").html(null).trigger('change.select2');
            })

            //date picker
            $('#tanggal_terima').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                endDate: '{{ carbon\carbon::now()->format('Y-m-d') }}',
                startDate: '{{ carbon\carbon::now()->format('Y-m-d') }}',
            });

            initialiseData();
        });


        function initialiseData() {
            var newOption = new Option('{{ $data->Branch->name }}',
                '{{ $data->supplier_id }}',
                true,
                true
            );

            $('#supplier_id').append(newOption).trigger('change');

            var url = "{{ url('/') }}" + '/{{ $data->file_faktur }}';
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


            index = '{{ $data->PenerimaanStockDetail->count() }}' * 1;
            $('#hidden-when-not-empty').addClass('hidden');
        }

        document.addEventListener('scroll', function(e) {
            lastKnownScrollPosition = window.scrollY;

            if (!ticking) {
                window.requestAnimationFrame(function() {
                    ticking = false;
                    doSomething(lastKnownScrollPosition);
                });

                ticking = true;
            }
        });


        function doSomething(scrollPos) {
            if (scrollPos > 50) {
                $('#simpan').css('opacity', 1);
            } else {
                $('#simpan').css('opacity', 0);
            }
        }

        $('#tambah-data').click(function() {
            $('#modal-tambah-data').find('.c_form_group').not('.readonly').removeClass('disabled');
            $('#modal-tambah-data').find('.c_form_group').find('input').not('.readonly').prop('readonly', false);
            $('#modal-tambah-data').find('.c_form_group').find('input').val('');;
            $('#modal-tambah-data').find('.c_form_group').find('.select2').val(null).trigger('change.select2');
            $('#modal-tambah-data').find('.not-editable').not('.readonly').removeClass('disabled');
            $('#modal-tambah-data').find('.not-editable').find('input').not('.readonly').prop('readonly', false);
            $('#modal-tambah-data').modal('toggle');
        })

        function calculateTotal() {
            var harga_satuan = $('#harga_satuan').val().replace(/[^0-9\-]+/g, "") * 1;
            var qty = $('#qty').val().replace(/[^0-9\-]+/g, "") * 1;

            $('#total_harga').val(accounting.formatNumber(harga_satuan * qty));
        }

        function tambahItem(params) {
            var validation = 0;

            $('#modal-tambah-data .required').each(function() {
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).addClass('is-invalid');
                    validation++
                }
            })

            if (validation != 0) {
                ToastNotification('warning',"Semua data harus diisi");
                return false;
            }

            var jenis_item = $('#jenis_item');
            var item_id = $('#item_id');
            var satuan = $('#satuan');
            var harga_satuan = $('#harga_satuan');
            var qty = $('#qty');
            var total_harga = $('#total_harga');

            if ($('#index').val() != '') {

                $('.index').each(function() {
                    if ($(this).val() == $('#index').val()) {
                        var par = $(this).parents('tr');
                        //td0
                        var td0 = jenis_item.find('option:selected').data('name') +
                            '<input type="hidden" class="jenis_item" name="jenis_item[]" value="' + jenis_item
                            .val() + '">' +
                            '<input type="hidden" class="item_kode" name="item_kode[]" value="' + (item_id.select2(
                                'data')[0].kode != undefined ? item_id.select2('data')[0].kode : item_id.find(
                                'option:selected').data('kode')) + '">' +
                            '<input type="hidden" class="item_name" name="item_name[]" value="' + (item_id.select2(
                                'data')[0].name != undefined ? item_id.select2('data')[0].name : item_id.find(
                                'option:selected').data('name')) + '">' +
                            '<input type="hidden" class="item_id" name="item_id[]" value="' + item_id.val() + '">' +
                            '<input type="hidden" class="satuan" name="satuan[]" value="' + satuan.val() + '">' +
                            '<input type="hidden" class="harga_satuan" name="harga_satuan[]" value="' + harga_satuan
                            .val() + '">' +
                            '<input type="hidden" class="qty" name="qty[]" value="' + qty.val() + '">' +
                            '<input type="hidden" class="total_harga" name="total_harga[]" value="' + total_harga
                            .val() + '">' +
                            '<input type="hidden" class="index" name="index[]" value="' + $(this).val() + '">';

                        $(par).find('td').eq(0).html(td0);
                        //td1
                        var td1 = item_id.select2('data')[0].kode != undefined ? item_id.select2('data')[0].kode +
                            '<br>' + item_id.select2('data')[0].name : item_id.find('option:selected').data(
                                'kode') + '<br>' +
                            item_id.find('option:selected').data('name');
                        $(par).find('td').eq(1).html(td1);
                        //td2
                        var td2 = satuan.val();
                        $(par).find('td').eq(2).html(td2);
                        //td3
                        var td3 = harga_satuan.val();
                        $(par).find('td').eq(3).html(td3);
                        //td4
                        var td4 = qty.val();
                        $(par).find('td').eq(4).html(td4);
                        //td5
                        var td5 = total_harga.val();
                        $(par).find('td').eq(5).html(td5);
                    }
                })

                $('#hidden-when-not-empty').addClass('hidden');
                $('#modal-tambah-data').modal('toggle');
            } else {
                // validation = 0;
                // $('.index').each(function() {
                //     var par = $(this).parents('tr');
                //     jenis_item_check = $(par).find('.jenis_item').val();
                //     item_id_check = $(par).find('.item_id').val();

                //     if (jenis_item_check == jenis_item.val() && item_id_check == item_id.val()) {
                //         validation++;
                //     }

                // });

                // if (validation != 0) {
                //     return ToastNotification('warning','Data sudah ada, tidak bisa menambahkan item yang sama');
                // }
                var html =
                    '<tr>' +
                    '<td>' +
                    jenis_item.find('option:selected').data('name') +
                    '<input type="hidden" class="jenis_item" name="jenis_item[]" value="' + jenis_item.val() + '">' +
                    '<input type="hidden" class="item_kode" name="item_kode[]" value="' + item_id.select2('data')[0].kode +
                    '">' +
                    '<input type="hidden" class="item_name" name="item_name[]" value="' + item_id.select2('data')[0].name +
                    '">' +
                    '<input type="hidden" class="item_id" name="item_id[]" value="' + item_id.val() + '">' +
                    '<input type="hidden" class="satuan" name="satuan[]" value="' + satuan.val() + '">' +
                    '<input type="hidden" class="harga_satuan" name="harga_satuan[]" value="' + harga_satuan.val() + '">' +
                    '<input type="hidden" class="qty" name="qty[]" value="' + qty.val() + '">' +
                    '<input type="hidden" class="total_harga" name="total_harga[]" value="' + total_harga.val() + '">' +
                    '<input type="hidden" class="index" name="index[]" value="' + index + '">' +
                    '</td>' +

                    '<td>' +
                    item_id.select2('data')[0].kode + '<br>' + item_id.select2('data')[0].name +
                    '</td>' +

                    '<td>' +
                    satuan.val() +
                    '</td>' +

                    '<td class="text-right">' +
                    harga_satuan.val() +
                    '</td>' +

                    '<td>' +
                    qty.val() +
                    '</td>' +

                    '<td class="text-right">' +
                    total_harga.val() +
                    '</td>' +

                    '<td class="text-center">' +
                    '<div class="btn-group">' +
                    '<button type="button" class="btn btn-info btn-rounded" onclick="edit(this)"><i class="fa fa-pencil" aria-hidden="true"></i></button>' +
                    '<button type="button" class="btn btn-danger btn-rounded" onclick="hapus(this)"><i class="fa fa-trash" aria-hidden="true"></i></button>' +
                    '</div>' +
                    '</td>' +

                    '</tr>';

                index++;
                $('#append-data').append(html);
                $('#hidden-when-not-empty').addClass('hidden');
                $('#modal-tambah-data').modal('toggle');
            }

        }

        function edit(child) {
            var par = $(child).parents('tr');

            var jenis_item = $(par).find('.jenis_item').val();
            var item_id = $(par).find('.item_id').val();
            var item_kode = $(par).find('.item_kode').val();
            var item_name = $(par).find('.item_name').val();
            var satuan = $(par).find('.satuan').val();
            var harga_satuan = $(par).find('.harga_satuan').val();
            var qty = $(par).find('.qty').val();
            var total_harga = $(par).find('.total_harga').val();
            var index = $(par).find('.index').val();

            $('#jenis_item').val(jenis_item);

            var newOption = new Option(item_name,
                item_id,
                true,
                true
            );

            newOption.setAttribute('data-kode', item_kode);
            newOption.setAttribute('data-name', item_name);
            $('#item_id').append(newOption).trigger('change');
            $('#satuan').val(satuan);
            $('#harga_satuan').val(harga_satuan);
            $('#qty').val(qty);
            $('#total_harga').val(total_harga);
            $('#index').val(index);
            $('#modal-tambah-data').find('.select2').trigger('change.select2');
            $('#modal-tambah-data').modal('toggle');
        }

        function hapus(child) {
            var par = $(child).parents('tr').remove();
            console.log($('.item_id').length);
            if ($('.item_id').length == 0) {
                $('#hidden-when-not-empty').removeClass('hidden');
            }
        }

        function store() {
            var validation = 0;

            $('#form-data .required').each(function() {
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).addClass('is-invalid');
                    validation++
                } else {
                    $(this).removeClass('is-invalid');
                }
            })
            var list = [];
            if ($('.item_id').length == 0) {
                list.push("Minimal harus ada satu item.");
            }

            if (validation != 0) {
                list.push("Semua data harus diisi.");
            }

            if (list.length != 0) {
                var error = '<ul style="padding:18px">';
                list.forEach(element => {
                    error += '<li>' + element + '</li>';
                });
                error += '</ul>';
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": 0,
                    "extendedTimeOut": 0,
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut",
                    "tapToDismiss": true
                }
                toastr["warning"](error, "Terjadi kesalahan");
                return false;
            }

            var formData = new FormData();

            var input = document.getElementById("dropify");
            if (input != null) {
                file = input.files[0];
                formData.append("image", file);
            }


            var data = $('#form-data').serializeArray();

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            var previousWindowKeyDown = window.onkeydown;
            swal({
                title: "Simpan Data",
                text: "Klik Tombol Ya jika data sudah benar.",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                allowEscapeKey: true,
            }, function() {
                window.onkeydown = previousWindowKeyDown;
                $.ajax({
                    url: '{{ route('updatePenerimaanStock') }}',
                    data: formData,
                    type: 'post',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 1) {
                            swal({
                                title: 'Success',
                                text: data.message,
                                type: "success",
                                allowEscapeKey: true,
                            }, function() {
                                location.reload();
                            });
                        } else if (data.status == 2) {
                            swal({
                                title: 'Ada Kesalahan !!!',
                                text: data.message,
                                html: true,
                                type: "warning",
                                allowEscapeKey: true,
                            });
                        } else {
                            swal({
                                title: 'Ada Kesalahan !!!',
                                text: data,
                                type: "warning",
                                html: true,
                                allowEscapeKey: true,
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

        function formatRepoStatus(repo) {
            if (repo.loading) {
                return repo.text;
            }
            console.log(repo);
            // scrolling can be used
            var markup = $('<span data-name=' + repo.name + ' value=' + repo.id + '>' + repo.text + ' ' + (repo.telpon !=
                undefined ? repo.telpon :
                '') + ' ' + (repo.email != undefined ? repo.email : '') + '</span>');
            return markup;
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
    </script>
@stop
