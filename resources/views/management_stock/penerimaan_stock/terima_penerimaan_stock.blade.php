@extends('../layout/' . $layout)

@section('subhead')
    <title>CRUD Form - Rubick - Tailwind HTML Admin Template</title>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Form Penerimaan Stok</h2>
    </div>
    <form class="grid grid-cols-12 gap-6 mt-5" id="form-data">
        <div class="intro-y col-span-12">
            <!-- BEGIN: Form Layout -->
            <div class="intro-y box p-5 grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 md:col-span-6 parent">
                    <label for="kode" class="form-label">Kode Penerimaan Stok
                        {{ dot() }}</label>
                    <input id="kode" name="kode" type="text" readonly class="form-control required not-editable "
                        placeholder="Masukan Kode" value="{{ $data->kode }}">
                    <input type="hidden" id="id" name="id" value="{{ $data->id }}">
                    {{ csrf_field() }}
                </div>
                <div class="col-span-12 md:col-span-6 parent">
                    <label for="tanggal_terima" class="form-label">Tanggal {{ dot() }}</label>
                    <div class="input-group parent">
                        <div class="input-group-text">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <input id="tanggal_terima" readonly name="tanggal_terima" type="text"
                            class="form-control required" placeholder="yyyy-mm-dd"
                            value="{{ carbon\carbon::parse($data->tanggal_terima)->format('Y-m-d') }}"
                            data-single-mode="true">
                    </div>
                </div>
                <div class="col-span-12 md:col-span-6 parent disabled">
                    <label for="branch_id" class="form-label">Branch {{ dot() }}</label>
                    <select name="branch_id" id="branch_id" class="form-control branch_id select2 required">
                        <option value="">Pilih Branch</option>
                        @foreach (\App\Models\Branch::where('status', true)->get() as $item)
                            <option {{ $item->id == $data->branch_id ? 'selected' : '' }} value="{{ $item->id }}">
                                {{ $item->kode }} - {{ $item->lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 md:col-span-6 parent disabled">
                    <label class="form-label">Branch Pengirim <span style="color:red;font-weight:bold" class="important">
                            *</span>
                    </label>
                    <select name="branch_pengirim" id="branch_pengirim"
                        class="form-control branch_pengirim select2 required ">
                        @foreach (\App\Models\Branch::where('status', true)->get() as $item)
                            <option {{ $data->PengeluaranStock->branch_id == $item->id ? 'selected' : '' }}
                                value="{{ $item->id }}">{{ $item->kode }}
                                {{ $item->lokasi }}
                            </option>
                        @endforeach
                    </select>
                    {{ csrf_field() }}
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label>Dokumen</label>
                    <input type="file" class="dropify text-sm" id="file_faktur" name="file_faktur"
                        data-allowed-file-extensions="jpeg png jpg pdf">
                    <br>
                    <a target="_blank" class="w-full" href="{{ url('/') }}/{{ $data->file_faktur }}">
                        <button class="btn btn-primary w-full">Download Dokumen</button>
                    </a>
                </div>
                <div class="col-span-12 md:col-span-8 parent">
                    <label for="nomor_faktur" class="form-label">Nomor Faktur
                        {{ dot() }}</label>
                    <input id="nomor_faktur" name="nomor_faktur" value="{{ $data->nomor_faktur }}" type="text"
                        class="form-control required" placeholder="Masukan Kode">
                </div>
                <div class="col-span-12 parent">
                    <label for="keterangan" class="form-label">Keterangan
                        {{ dot() }}</label>
                    <textarea id="keterangan" name="keterangan" type="text" class="form-control required" placeholder="Masukan Kode">{{ $data->description }}</textarea>
                </div>

                <div class="col-span-12 ">
                    <div class="flex justify-between">
                        <h5 class="font-bold text-xl">Informasi Item</h5>
                    </div>

                </div>
                <div class="col-span-12">
                    <table class="table table-bordered">
                        <thead align="center">
                            <th>Jenis Item</th>
                            <th>Item</th>
                            <th>Exp Date</th>
                            <th>Satuan</th>
                            <th>Harga Satuan</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Opsi</th>
                        </thead>
                        <tbody id="append-data">
                            @foreach ($data->PenerimaanStockDetail as $i => $item)
                                <tr>
                                    <td>
                                        {{ $item->jenis_stock }}
                                        <input type="hidden" class="jenis_item" name="jenis_item[]"
                                            value="{{ $item->jenis_stock }}">
                                        <input type="hidden" class="item_kode" name="item_kode[]"
                                            value="{{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->kode : $item->ItemNonObat->kode }}">
                                        <input type="hidden" class="item_name" name="item_name[]"
                                            value="{{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->name : $item->ItemNonObat->name }}">
                                        <input type="hidden" class="item_id" name="item_id[]"
                                            value="{{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->id : $item->ItemNonObat->id }}">
                                        <input type="hidden" class="satuan" name="satuan[]"
                                            value="{{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->Satuan->kode : $item->ItemNonObat->Satuan->kode }}">
                                        <input type="hidden" class="harga_satuan" name="harga_satuan[]"
                                            value="{{ number_format($item->harga_satuan) }}">
                                        <input type="hidden" class="qty" name="qty[]"
                                            value="{{ $item->qty }}">
                                        <input type="hidden" class="total_harga" name="total_harga[]"
                                            value="{{ number_format($item->total_harga) }}">
                                        <input type="hidden" class="index" name="index[]"
                                            value="{{ $i }}">
                                        <input type="hidden" class="expired_date" name="expired_date[]"
                                            value="{{ $item->expired_date }}">
                                    </td>
                                    <td>
                                        {{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->kode : $item->ItemNonObat->kode }}
                                        <br>
                                        {{ $item->jenis_stock == 'OBAT' ? $item->ProdukObat->name : $item->ItemNonObat->name }}
                                    </td>
                                    <td class="text-right">
                                        {{ $item->expired_date }}
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
                                        {{-- <div class="btn-group">
                                            <button type="button" class="btn btn-info btn-rounded"
                                                onclick="edit(this)"><i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        </div> --}}
                                        -
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot id="hidden-when-not-empty">
                            <tr>
                                <td colspan="7" class="text-center">Belum ada item
                                    yang ditambahkan</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="text-right mt-5 col-span-12">
                    <a type="button" class="btn btn-outline-secondary w-24 mr-1"
                        href="{{ route('penerimaan-stock') }}">Kembali</a>
                    <button type="button" class="btn btn-primary" onclick="store()"><i class="fas fa-save mr-2"></i>
                        Terima Stock</button>
                </div>
            </div>
            <!-- END: Form Layout -->
        </div>
    </form>


    <div id="modal-tambah-data" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width: 80% !important">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Tambah Penerimaan</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 md:col-span-4 parent">
                        <label>Jenis Item</label>
                        <select name="jenis_item" id="jenis_item" class="form-control jenis_item select2filter required">
                            <option value="">Pilih Jenis Item</option>
                            @foreach (\App\Models\PenerimaanStockDetail::$enumJenisStock as $item)
                                <option data-name="{{ $item }}" value="{{ $item }}">
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="index">
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label>Item</label>
                        <select id="item_id" class="form-control select2 required">
                        </select>
                        <input type="hidden" id="satuan">
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="tanggal_terima" class="form-label">Exp Date</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <input id="expired_date" readonly name="expired_date" type="text"
                                class="form-control required date datepicker" placeholder="yyyy-mm-dd" value=""
                                data-single-mode="true">
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="name" class="form-label">Harga Jual {{ dot() }}</label>
                        <div class="input-group">
                            <div class="input-group-text">Rp.</div>
                            <input type="text" id="harga_satuan" class="form-control mask text-right required"
                                placeholder="Harga jual obat" aria-label="Harga jual obat" onkeyup="calculateTotal()">
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="qty" class="form-label">Qty {{ dot() }}</label>
                        <div class="input-group">
                            <input id="qty" type="text" class="form-control required"
                                onkeyup="calculateTotal()">
                            <div class="input-group-text" id="satuan_text"></div>
                        </div>
                    </div>
                    <div class="col-span-12 md:col-span-4 parent">
                        <label for="total_harga" class="form-label">Total {{ dot() }}</label>
                        <div class="input-group">
                            <div class="input-group-text">Rp.</div>
                            <input id="total_harga" readonly type="text" class="form-control required">
                        </div>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="tambahItem()">Tambahkan data item</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var table;
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

            $('.mask-non-decimal').maskMoney({
                precision: 0,
                thousands: '',
                allowZero: true,
            })

            $('.select2').select2({
                // theme: 'bootstrap4',
            })

            $('.select2filter').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                // theme: 'bootstrap4',
            })
            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            $('#tambah-data').click(function() {
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();

            })

            $('.dropify').dropify();
            tomGenerator('.tomSelect');

            $("#supplier_id").select2({
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
                dropdownParent: $("#modal-tambah-data .modal-body"),
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

            initialiseData();
        })()


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

        // document.addEventListener('scroll', function(e) {
        //     lastKnownScrollPosition = window.scrollY;

        //     if (!ticking) {
        //         window.requestAnimationFrame(function() {
        //             ticking = false;
        //             doSomething(lastKnownScrollPosition);
        //         });

        //         ticking = true;
        //     }
        // });


        // function doSomething(scrollPos) {
        //     if (scrollPos > 50) {
        //         $('#simpan').css('opacity', 1);
        //     } else {
        //         $('#simpan').css('opacity', 0);
        //     }
        // }

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
                var par = $(this).parents('.parent');
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).addClass('is-invalid');
                    $(par).find('.select2-container').addClass('is-invalid');
                    validation++
                }
            })

            if (validation != 0) {
                return ToastNotification('warning', 'Semua data harus diisi');

                return false;
            }

            var jenis_item = $('#jenis_item');
            var item_id = $('#item_id');
            var satuan = $('#satuan');
            var harga_satuan = $('#harga_satuan');
            var qty = $('#qty');
            var total_harga = $('#total_harga');
            var expired_date = $('#expired_date');

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
                            '<input type="hidden" class="index" name="index[]" value="' + $(this).val() + '">' +
                            '<input type="hidden" class="expired_date" name="expired_date[]" value="' + expired_date
                            .val() + '">';

                        $(par).find('td').eq(0).html(td0);
                        //td1
                        var td1 = item_id.select2('data')[0].kode != undefined ? item_id.select2('data')[0].kode +
                            '<br>' + item_id.select2('data')[0].name : item_id.find('option:selected').data(
                                'kode') + '<br>' +
                            item_id.find('option:selected').data('name');
                        $(par).find('td').eq(1).html(td1);
                        //td2
                        var td2 = expired_date.val();
                        $(par).find('td').eq(2).html(td2);
                        //td3
                        var td3 = satuan.val();
                        $(par).find('td').eq(3).html(td3);
                        //td4
                        var td4 = harga_satuan.val();
                        $(par).find('td').eq(4).html(td4);
                        //td5
                        var td5 = qty.val();
                        $(par).find('td').eq(5).html(td5);
                        //td6
                        var td6 = total_harga.val();
                        $(par).find('td').eq(6).html(td6);
                    }
                })

                $('#hidden-when-not-empty').addClass('hidden');
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();
            } else {
                validation = 0;
                $('.index').each(function() {
                    var par = $(this).parents('tr');
                    jenis_item_check = $(par).find('.jenis_item').val();
                    item_id_check = $(par).find('.item_id').val();

                    if (jenis_item_check == jenis_item.val() && item_id_check == item_id.val()) {
                        validation++;
                    }

                });

                if (validation != 0) {
                    return ToastNotification('warning', 'Data sudah ada, tidak bisa menambahkan item yang sama');
                }
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
                    '<input type="hidden" class="expired_date" name="expired_date[]" value="' + expired_date.val() + '">' +
                    '</td>' +

                    '<td>' +
                    item_id.select2('data')[0].kode + '<br>' + item_id.select2('data')[0].name +
                    '</td>' +

                    '<td>' +
                    expired_date.val() +
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
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();
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
            var expired_date = $(par).find('.expired_date').val();
            var index = $(par).find('.index').val();

            $('#jenis_item').val(jenis_item).trigger('change.select2');

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
            $('#expired_date').val(expired_date);
            $('#modal-tambah-data').find('.select2').trigger('change.select2');
            const el = document.querySelector("#modal-tambah-data");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
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
                var par = $(this).parents('.parent');
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).addClass('is-invalid');
                    $(par).find('.select2-container').addClass('is-invalid');
                    validation++
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
                console.log(error);
                ToastNotification('warning', error);

                return false;
            }

            var formData = new FormData();

            var input = document.getElementById("file_faktur");
            if (input != null) {
                file = input.files[0];
                formData.append("image", file);
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
                    $.ajax({
                        url: '{{ route('updatePenerimaanStock') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "success",
                                });

                                location.href = '{{ route('penerimaan-stock') }}'
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: data.message,
                                    icon: "warning",
                                });
                            } else {
                                Swal.fire({
                                    title: 'Ada Kesalahan !!!',
                                    text: data,
                                    icon: "warning",
                                    html: true,
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
            })
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
@endsection
