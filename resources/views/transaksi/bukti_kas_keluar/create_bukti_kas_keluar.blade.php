@extends('../layout/' . $layout)

@section('subhead')
    <title>CRUD Form - Rubick - Tailwind HTML Admin Template</title>
@endsection

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Pengeluaran</h2>
    </div>
    <form class="grid grid-cols-12 gap-6 mt-5" id="form-data">
        <div class="intro-y col-span-12">
            <!-- BEGIN: Form Layout -->
            <div class="intro-y box p-5 grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 md:col-span-6 parent">
                    <label for="kode" class="form-label">Kode {{ convertSlug($global['title']) }}
                        {{ dot() }}</label>
                    <input id="kode" name="kode" type="text" readonly class="form-control required not-editable "
                        placeholder="Masukan Kode">
                    <input type="hidden" id="id" name="id">
                    {{ csrf_field() }}
                </div>
                <div class="col-span-12 md:col-span-6 parent">
                    <label for="tanggal_pengeluaran" class="form-label">Tanggal {{ dot() }}</label>
                    <div class="input-group parent">
                        <div class="input-group-text">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <input id="tanggal_pengeluaran" readonly name="tanggal_pengeluaran" type="text"
                            class="form-control required" placeholder="yyyy-mm-dd"
                            value="{{ carbon\carbon::now()->format('Y-m-d') }}" data-single-mode="true">
                    </div>
                </div>
                <div class="col-span-12 md:col-span-6 parent {{ !Auth::user()->akses('global') ? 'disabled' : '' }}">
                    <label for="branch_id" class="form-label">Branch {{ dot() }}</label>
                    <select name="branch_id" id="branch_id" class="form-control branch_id select2 required">
                        <option value="">Pilih Branch</option>
                        @foreach (\App\Models\Branch::where('status', true)->get() as $item)
                            <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }}
                                value="{{ $item->id }}">{{ $item->kode }} - {{ $item->lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 md:col-span-6 parent disabled">
                    <label class="form-label">Total<span style="color:red;font-weight:bold" class="important">
                            *</span></label>
                    <div class="input-group">
                        <div class="input-group-text">
                            Rp.
                        </div>
                        <input id="total" type="text" class="form-control required mask text-right" readonly required
                            autocomplete="total" placeholder="XXX,XXX" name="total">
                    </div>
                </div>
                <div class="col-span-12 parent">
                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran
                        {{ dot() }}</label>
                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-control select2">
                        <option value="TUNAI">TUNAI</option>
                        <option value="DEBET">DEBET</option>
                        <option value="TRANSFER">TRANSFER</option>
                    </select>
                </div>
                <div class="col-span-6 parent non-tunai hidden">
                    <label for="nama_bank" class="form-label">Nama Bank {{ dot() }}</label>
                    <input id="nama_bank" name="nama_bank" type="text" class="form-control text-left uppercase required"
                        placeholder="Masukan nama bank">
                </div>
                <div class="col-span-6 parent non-tunai hidden">
                    <label for="nomor_kartu" class="form-label">No. Rekening {{ dot() }}</label>
                    <input type="text" name="nomor_kartu" id="nomor_kartu" placeholder="xxxxxxxxxx"
                        class="form-control required">
                </div>
                <div class="col-span-12 parent">
                    <label for="keterangan" class="form-label">Keterangan
                        {{ dot() }}</label>
                    <textarea id="keterangan" name="keterangan" type="text" class="form-control required"
                        placeholder="Masukan Keterangan"></textarea>
                </div>

                <div class="col-span-12 ">
                    <div class="flex justify-between">
                        <h5 class="font-bold text-xl">Informasi Item</h5>
                        <button type="button" class="btn btn-primary" id="tambah-data"><i class="mr-2 fas fa-plus"></i>
                            Tambah Pengeluaran
                            Item</button>
                    </div>

                </div>
                <div class="col-span-12">
                    <table class="table table-bordered">
                        <thead align="center">
                            <th>Jenis Transaksi</th>
                            <th>Item</th>
                            <th>Nominal</th>
                            <th>Kuintansi</th>
                            <th>Opsi</th>
                        </thead>
                        <tbody id="append-data">

                        </tbody>
                        <tfoot id="hidden-when-not-empty">
                            <tr>
                                <td colspan="5" class="text-center">
                                    Belum ada item yang ditambahkan
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="text-right mt-5 col-span-12">
                    <a type="button" class="btn btn-outline-secondary w-24 mr-1"
                        href="{{ route('bukti-kas-keluar') }}">Kembali</a>
                    <button type="button" class="btn btn-primary" onclick="store()"><i class="fas fa-save mr-2"></i>
                        Simpan
                        Perubahan</button>
                </div>
            </div>
            <!-- END: Form Layout -->
        </div>
    </form>

    <div id="modal-tambah-data" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Tambah Pengeluaran</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 mb-3 parent">
                        <label>Jenis Transaksi</label>
                        <select id="master_akun_transaksi_id" class="form-control select2filter required">
                            <option value="">Pilih Jenis Transaksi</option>
                            @foreach (\App\Models\MasterAkunTransaksi::where('status', true)->get() as $item)
                                <option data-name="{{ $item->name }}" value="{{ $item->id }}">
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="index">
                    </div>
                    <div class="col-span-12 mb-3 parent">
                        <label>Item<span style="color:red;font-weight:bold" class="important">
                                *</span></label>
                        <div class="input-group">
                            <input id="redaksi" type="text" class="form-control required text-uppercase"
                                value="" required autocomplete="redaksi" placeholder="Masukan Item">
                        </div>
                    </div>
                    <div class="col-span-12 mb-3 parent">
                        <label>Nominal<span style="color:red;font-weight:bold" class="important">
                                *</span></label>
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input id="harga" type="text" class="form-control required mask text-right" required
                                autocomplete="harga" placeholder="Masukan Nominal">
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
        var index = 0;
        (function() {
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

            $('.select2').select2({})

            $('.select2filter').select2({
                dropdownParent: $("#modal-tambah-data .modal-body"),
                // theme: 'bootstrap4',
            })
            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            $('#tambah-data').click(function() {
                $('#master_akun_transaksi_id').val('');
                $('#redaksi').val('');
                $('#harga').val('');
                $('#modal-tambah-data').find('.select2filter').trigger('change.select2');
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();

            })

            tomGenerator('.tomSelect');

            $("#supplier_id").select2({
                width: '100%',
                ajax: {
                    url: "{{ route('select2BuktiKasKeluar') }}?param=supplier_id",
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
                    url: "{{ route('select2BuktiKasKeluar') }}?param=item_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            jenis_item() {
                                return $('#jenis_item').val();
                            },
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
                placeholder: 'Cari Item',
                minimumInputLength: 0,
                templateResult: formatRepoNormal,
                templateSelection: formatRepoNormalSelection
            });

            generateKode();
        })()

        $(document).on('change', '#metode_pembayaran', function() {
            if ($(this).val() == 'TUNAI') {
                $('.non-tunai').addClass('hidden');
            } else {
                $('.non-tunai').removeClass('hidden');
            }
            $('.non-tunai').find('input').val('');
        })

        function generateKode() {
            $.ajax({
                url: "{{ route('generateKodeBuktiKasKeluar') }}",
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

        function filter(params) {
            table.ajax.reload();
        }

        function gantiStatus(param, id) {
            $.ajax({
                url: "{{ route('statusBuktiKasKeluar') }}",
                data: {
                    id,
                    param
                },
                type: 'get',
                success: function(data) {
                    table.ajax.reload(null, false);
                    toastr.success(data.message);
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

        function calculateQty() {
            var qty_tersisa = $('#qty_tersisa').val().replace(/[^0-9\-]+/g, "") * 1;
            var qty = $('#qty').val().replace(/[^0-9\-]+/g, "") * 1;

            if (qty > qty_tersisa) {
                qty = qty_tersisa;
            }
            $('#qty').val(accounting.formatNumber(qty));

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
                ToastNotification('warning', "Semua data harus diisi");
                return false;
            }

            var master_akun_transaksi_id = $('#master_akun_transaksi_id');
            var redaksi = $('#redaksi');
            var harga = $('#harga');

            if ($('#index').val() != '') {
                $('.index').each(function() {
                    if ($(this).val() == $('#index').val()) {
                        var par = $(this).parents('tr');
                        //td0
                        var td0 = master_akun_transaksi_id.find('option:selected').data('name') +
                            '<input type="hidden" class="master_akun_transaksi_id" name="master_akun_transaksi_id[]" value="' +
                            master_akun_transaksi_id.val() + '">' +
                            '<input type="hidden" class="redaksi" name="redaksi[]" value="' + redaksi.val()
                            .toUpperCase() + '">' +
                            '<input type="hidden" class="harga" name="harga[]" value="' + harga.val() + '">' +
                            '<input type="hidden" class="index" name="index[]" value="' + $(this).val() + '">';

                        $(par).find('td').eq(0).html(td0);
                        //td1
                        var td1 = redaksi.val();
                        $(par).find('td').eq(1).html(td1);
                        //td2
                        var td2 = harga.val();
                        $(par).find('td').eq(2).html(td2);
                    }
                })

                $('#hidden-when-not-empty').addClass('hidden');
                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();
            } else {
                validation = 0;
                // $('.index').each(function() {
                //     var par = $(this).parents('tr');
                //     master_akun_transaksi_id_check = $(par).find('.master_akun_transaksi_id').val();
                //     item_id_check = $(par).find('.item_id').val();

                //     if (master_akun_transaksi_id_check == master_akun_transaksi_id.val()) {
                //         validation++;
                //     }

                // });

                if (validation != 0) {
                    return ToastNotification('warning', 'Data sudah ada, tidak bisa menambahkan item yang sama');
                }
                var html =
                    '<tr>' +
                    '<td>' +
                    master_akun_transaksi_id.find('option:selected').data('name') +
                    '<input type="hidden" class="master_akun_transaksi_id" name="master_akun_transaksi_id[]" value="' +
                    master_akun_transaksi_id.val() + '">' +
                    '<input type="hidden" class="redaksi" name="redaksi[]" value="' + redaksi.val().toUpperCase() + '">' +
                    '<input type="hidden" class="harga" name="harga[]" value="' + harga.val() + '">' +
                    '<input type="hidden" class="index" name="index[]" value="' + index + '">' +
                    '</td>' +

                    '<td class="text-left">' +
                    redaksi.val() +
                    '</td>' +

                    '<td class="text-right">' +
                    harga.val() +
                    '</td>' +

                    '<td class="text-center">' +
                    '<input type="file" class="dropify" name="proofment[]">' +
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
                $('.dropify').last().dropify();
            }
            calcTotal();
        }

        function hapus(child) {
            var par = $(child).parents('tr').remove();
            if ($('.master_akun_transaksi_id').length == 0) {
                $('#hidden-when-not-empty').removeClass('hidden');
            }
            calcTotal();
        }

        function calcTotal() {
            var total = 0;
            $('.harga').each(function() {
                total += $(this).val().replace(/[^0-9\-]+/g, "") * 1
            })
            console.log(total);
            $('#total').val(accounting.formatNumber(total));
        }

        function edit(child) {
            var par = $(child).parents('tr');

            var master_akun_transaksi_id = $(par).find('.master_akun_transaksi_id').val();
            var redaksi = $(par).find('.redaksi').val();
            var harga = $(par).find('.harga').val();
            var index = $(par).find('.index').val();

            $('#master_akun_transaksi_id').val(master_akun_transaksi_id);

            $('#redaksi').val(redaksi);
            $('#harga').val(harga);
            $('#index').val(index);
            $('#modal-tambah-data').find('.select2filter').trigger('change.select2');
            const el = document.querySelector("#modal-tambah-data");
            const modal = tailwind.Modal.getOrCreateInstance(el);
            modal.toggle();
        }

        function store() {
            var validation = 0;

            $('#form-data .required').each(function() {
                var par = $(this).parents('.parent');
                if (!$(par).hasClass('hidden')) {
                    if ($(this).val() == '' || $(this).val() == null) {
                        $(this).addClass('is-invalid');
                        $(par).find('.select2-container').addClass('is-invalid');
                        validation++
                    }
                }
            })

            var list = [];
            if ($('.master_akun_transaksi_id').length == 0) {
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


            $('.dropify').each(function(i) {
                file = $(this)[0].files[0];
                if (file != undefined) {
                    formData.append('proofment_' + i, file);
                }
            })


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
                    overlay(true);
                    window.onkeydown = previousWindowKeyDown;
                    $.ajax({
                        url: '{{ route('storeBuktiKasKeluar') }}',
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
                                location.reload();
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
