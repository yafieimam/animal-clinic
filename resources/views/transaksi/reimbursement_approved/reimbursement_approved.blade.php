@extends('../layout/' . $layout)

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
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
                    <th>No</th>
                    <th>Opsi</th>
                    <th>Nama Klaim</th>
                    <th>Nama Karyawan</th>
                    <th>Tanggal</th>
                    <th>Tipe Klaim</th>
                    <th>Jumlah Biaya</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </thead>

                <tbody>

                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
    </div>

    <!-- BEGIN: Modal Content -->
    <form id="modal-tambah-data" class="modal modal-data" enctype="multipart/form-data" data-tw-backdrop="static" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Form Data</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 parent">
                        <label for="nama_klaim" class="form-label">Nama Klaim {{ dot() }}</label>
                        <input id="nama_klaim" name="nama_klaim" type="text"
                            class="form-control uppercase required" placeholder="Masukan Nama Klaim">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent-tanggal">
                        <label for="tanggal" class="form-label">Tanggal
                            {{ dot() }}</label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <input id="tanggal" name="tanggal" type="text"
                                class="form-control required datepicker" placeholder="yyyy-mm-dd"
                                value="{{ \carbon\carbon::now()->format('Y-m-d') }}"
                                data-single-mode="true">
                        </div>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="tipe_klaim" class="form-label">Tipe Klaim {{ dot() }}</label>
                        <select id="tipe_klaim" name="tipe_klaim" class="w-full select2 required form-control">
                            <option value="">Pilih Tipe Klaim</option>
                            @foreach (\App\Models\Reimbursement::$enumTipeKlaim as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="jumlah_biaya" class="form-label">Jumlah Biaya {{ dot() }}</label>
                        <input id="jumlah_biaya" name="jumlah_biaya" type="text"
                            class="form-control text-right mask required">
                    </div>
                    <div class="col-span-12 parent">
                        <label for="keterangan" class="form-label">Keterangan {{ dot() }}</label>
                        <textarea id="keterangan" name="keterangan" type="text" class="form-control required"
                            placeholder="Masukan Keterangan"></textarea>
                    </div>
                    <div class="col-span-12 parent-approval">
                        <label for="keterangan_approval" class="form-label">Keterangan Approve {{ dot() }}</label>
                        <textarea id="keterangan_approval" name="keterangan_approval" type="text" class="form-control required"
                            placeholder="Masukan Keterangan"></textarea>
                    </div>
                    <div class="col-span-12 file-container" id="fileContainerKlaim">
                    </div>
                    <div class="col-span-9">&nbsp;</div>
                    <div class="col-span-3">
                        <button type="button" class="btn btn-primary" id="addFileButton">Add More File</button>
                    </div>
                    <div class="col-span-12 file-container" id="fileContainer">
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" id="reset-data"
                        class="btn btn-primary mr-1">Reset</button>
                    <button type="button" class="btn btn-primary w-20 mr-1" id="simpan" onclick="store()">Simpan</button>
                    <button type="button" class="btn btn-primary w-20 mr-1" id="reject" onclick="storeReject()">Simpan</button>
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20">Batal</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </form>
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
    <script>
        var table;
        var indexFile = 1;
        var indexFileApproval = 1;

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
                        url: "{{ route('datatableReimbursementApproved') }}",
                        data: {
                            _token: '{{ csrf_token() }}'
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
                        data: 'nama_klaim',
                        name: 'nama_klaim',
                        class: 'text-center',
                    }, {
                        data: 'nama_karyawan',
                        name: 'nama_karyawan',
                        class: 'text-center',
                    }, {
                        data: 'tanggal',
                        name: 'tanggal',
                        class: 'text-center',
                    }, {
                        data: 'tipe_klaim',
                        name: 'tipe_klaim',
                        class: 'text-center',
                    }, {
                        data: 'jumlah_biaya',
                        name: 'jumlah_biaya',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 0, 'Rp '),
                    }, {
                        data: 'keterangan',
                        name: 'keterangan',
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
                dropdownParent: $("#modal-tambah-data .modal-body"),
                // theme: 'bootstrap4',
            })

            $('.select2filter').select2({
                // theme: 'bootstrap4',
            })
            Inputmask("999999999999").mask('#telpon');
            Inputmask("99:99 aaaa").mask('.timeMask');

            // $(".timeMask").inputmask("99:99 aaaa");

            $("#addFileButton").click(function() {
                const fileInput = $("<div>", {
                    class: "col-span-12 mt-2 parent"
                });
                const fileLabel = $("<label>", {
                    class: "form-label",
                    text: "File " + indexFile
                });
                const inputFile = $("<input>", {
                    type: "file",
                    class: "form-control mb-2 file-input-field",
                    name: "file_data[]",
                    multiple: true,
                    "data-id": indexFile,
                    style: "border-radius: unset;"
                });
                const inputSeq = $("<input>", {
                    type: "hidden",
                    class: "form-control seq-input-field",
                    name: "seq_data[]"
                });
                const previewLink = $("<a>", {
                    href: "javascript:void(0);",
                    class: "preview-link",
                    text: "Preview File " + indexFile,
                    style: "text-decoration: underline;"
                });

                fileInput.append(fileLabel, inputFile, inputSeq, previewLink);
                $("#fileContainer").append(fileInput);

                indexFile++;
            });

            $("#fileContainer").on("click", ".preview-link", function(e) {
                e.preventDefault();
                const fileInput = $(this).siblings(".file-input-field");
                if(fileInput.length > 0){
                    if (fileInput[0].files.length > 0) {
                        const file = fileInput[0].files[0];
                        const url = URL.createObjectURL(file);
                        window.open(url);
                    }else{
                        if($(this).attr("href") != "javascript:void(0);"){
                            window.open($(this).attr("href"));
                        }
                    }
                }else{
                    if($(this).attr("href") != "javascript:void(0);"){
                        window.open($(this).attr("href"));
                    }
                }
            });

            $("#fileContainerKlaim").on("click", ".preview-link", function(e) {
                e.preventDefault();
                const fileInput = $(this).siblings(".file-input-field");
                if(fileInput.length > 0){
                    if (fileInput[0].files.length > 0) {
                        const file = fileInput[0].files[0];
                        const url = URL.createObjectURL(file);
                        window.open(url);
                    }else{
                        if($(this).attr("href") != "javascript:void(0);"){
                            window.open($(this).attr("href"));
                        }
                    }
                }else{
                    if($(this).attr("href") != "javascript:void(0);"){
                        window.open($(this).attr("href"));
                    }
                }
            });

            $("#fileContainer").on("change", ".file-input-field", function() {
                const previewLink = $(this).siblings(".preview-link");
                const seqData = $(this).siblings(".seq-input-field");
                if (this.files.length > 0) {
                    seqData.val($(this).data('id'));
                    previewLink.attr("href", "javascript:void(0);");
                }
            });

            $("#reset-data").click(function() {
                $('#fileContainer').empty();
                indexFile = 1;

                const fileInput = $("<div>", {
                    class: "col-span-12 mt-2 parent"
                });
                const fileLabel = $("<label>", {
                    class: "form-label",
                    text: "File " + indexFile
                });
                const inputFile = $("<input>", {
                    type: "file",
                    class: "form-control mb-2 file-input-field",
                    name: "file_data[]",
                    multiple: true,
                    "data-id": indexFile,
                    style: "border-radius: unset;"
                });
                const inputSeq = $("<input>", {
                    type: "hidden",
                    class: "form-control seq-input-field",
                    name: "seq_data[]"
                });
                const previewLink = $("<a>", {
                    href: "javascript:void(0);",
                    class: "preview-link",
                    text: "Preview File " + indexFile,
                    style: "text-decoration: underline;"
                });

                fileInput.append(fileLabel, inputFile, inputSeq, previewLink);
                $("#fileContainer").append(fileInput);

                indexFile++;
            });

            tomGenerator('.tomSelect');
        })()

        function approve(id) {
            $.ajax({
                url: '{{ route('editReimbursementApproved') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        $('#' + key).val(temp_value[key]);
                    }
                    $('.parent').addClass('disabled');
                    $('.parent-approval').removeClass('disabled');
                    $('.not-editable').prop('readonly', true);
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    $('#simpan').removeClass('hidden');
                    $('#reject').addClass('hidden');
                    $('#reset-data').removeClass('hidden');
                    $('#addFileButton').removeClass('hidden');

                    $('#fileContainer').empty();
                    indexFile = 1;
                    $("#fileContainerKlaim").empty();
                    indexFileApproval = 1;
                    $("#addFileButton").click();

                    data.data.reimbursement_file_klaim.forEach(function(value, index) {
                        const fileInput = $("<div>", {
                            class: "col-span-12 mt-2 parent"
                        });
                        const previewLink = $("<a>", {
                            href: "{{ url('/') }}/" + value.file,
                            class: "btn btn-primary preview-link",
                            text: "Preview File " + indexFileApproval,
                            style: "text-decoration: none;"
                        });

                        fileInput.append(previewLink);
                        $("#fileContainerKlaim").append(fileInput);

                        indexFileApproval++;
                    });

                    const el = document.querySelector("#modal-tambah-data");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
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

        function lihat(id) {
            $.ajax({
                url: '{{ route('editReimbursementApproved') }}',
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
                        $('#' + key).val(temp_value[key]);
                    }
                    $('.parent').addClass('disabled');
                    $('.parent-approval').addClass('disabled');
                    $('.not-editable').prop('readonly', true);
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    $('#simpan').addClass('hidden');
                    $('#reject').addClass('hidden');
                    $('#reset-data').addClass('hidden');
                    $('#addFileButton').addClass('hidden');

                    $('#fileContainer').empty();
                    indexFile = 1;
                    $("#fileContainerKlaim").empty();
                    indexFileApproval = 1;

                    data.data.reimbursement_file_klaim.forEach(function(value, index) {
                        const fileInput = $("<div>", {
                            class: "col-span-12 mt-2 parent"
                        });
                        const previewLink = $("<a>", {
                            href: "{{ url('/') }}/" + value.file,
                            class: "btn btn-primary preview-link",
                            text: "Preview File Klaim " + indexFile,
                            style: "text-decoration: none;"
                        });

                        fileInput.append(previewLink);
                        $("#fileContainerKlaim").append(fileInput);

                        indexFile++;
                    });

                    data.data.reimbursement_file_approval.forEach(function(value, index) {
                        const fileInput = $("<div>", {
                            class: "col-span-12 mt-2 parent"
                        });
                        const previewLink = $("<a>", {
                            href: "{{ url('/') }}/" + value.file,
                            class: "btn btn-primary preview-link",
                            text: "Preview File Approval " + indexFileApproval,
                            style: "text-decoration: none;"
                        });

                        fileInput.append(previewLink);
                        $("#fileContainer").append(fileInput);

                        indexFileApproval++;
                    });

                    const el = document.querySelector("#modal-tambah-data");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                },
                error: function(data) {
                    lihat(id);
                }
            });
        }

        function store() {
            var validation = 0;

            if(!$('#id').val()){
                if ($('input[name="file_data[]"]')[0].files.length === 0) {
                    ToastNotification('warning', 'File harus diisi');
                    return false;
                }
            }

            $('#modal-tambah-data .required').each(function() {
                var par = $(this).parents('.parent-approval');
                if ($(this).val() == '' || $(this).val() == null || $(this).val() == 0) {
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

            var data = $('#modal-tambah-data').serializeArray();

            $('.file-input-field').each(function(index, input) {
                for (var i = 0; i < input.files.length; i++) {
                    formData.append('file_data[]', input.files[i]);
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
                        url: '{{ route('storeReimbursementApproved') }}',
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
                                clear();
                                const el = document.querySelector("#modal-tambah-data");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
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

        function reject(id) {
            $.ajax({
                url: '{{ route('editReimbursementApproval') }}',
                data: {
                    id
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        $('#' + key).val(temp_value[key]);
                    }
                    $('.parent').addClass('disabled');
                    $('.parent-approval').removeClass('disabled');
                    $('.not-editable').prop('readonly', true);
                    $('#modal-tambah-data .select2').trigger('change.select2');
                    $('#simpan').addClass('hidden');
                    $('#reject').removeClass('hidden');
                    $('#reset-data').addClass('hidden');
                    $('#addFileButton').addClass('hidden');

                    $('#fileContainer').empty();
                    indexFile = 1;
                    $("#fileContainerKlaim").empty();
                    indexFileApproval = 1;
                    $("#addFileButton").click();

                    data.data.reimbursement_file_klaim.forEach(function(value, index) {
                        const fileInput = $("<div>", {
                            class: "col-span-12 mt-2 parent"
                        });
                        const previewLink = $("<a>", {
                            href: "{{ url('/') }}/" + value.file,
                            class: "btn btn-primary preview-link",
                            text: "Preview File Klaim " + indexFileApproval,
                            style: "text-decoration: none;"
                        });

                        fileInput.append(previewLink);
                        $("#fileContainerKlaim").append(fileInput);

                        indexFile++;
                    });

                    const el = document.querySelector("#modal-tambah-data");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
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

        function storeReject(id) {
            var validation = 0;

            if(!$('#id').val()){
                if ($('input[name="file_data[]"]')[0].files.length === 0) {
                    ToastNotification('warning', 'File harus diisi');
                    return false;
                }
            }

            $('#modal-tambah-data .required').each(function() {
                var par = $(this).parents('.parent-approval');
                if ($(this).val() == '' || $(this).val() == null || $(this).val() == 0) {
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

            var data = $('#modal-tambah-data').serializeArray();

            $('.file-input-field').each(function(index, input) {
                for (var i = 0; i < input.files.length; i++) {
                    formData.append('file_data[]', input.files[i]);
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
                title: "Reject Data",
                text: "Apakah Anda Yakin Ingin Menolak Pengajuan Klaim Ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('rejectReimbursementApproved') }}",
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
                                clear();
                                const el = document.querySelector("#modal-tambah-data");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
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
            });
        }

        function printReimbursement(id) {
            window.open('{{ route('printReimbursement') }}?id=' + id);
        }
    </script>
@endsection
