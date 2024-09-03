@extends('../layout/' . $layout)

@section('style')
    <style>
        .shadow-strong {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 0 6px 20px rgba(0, 0, 0, 0.19);
        }

        input[type='file'].multiple-file { 
            width:100px; color:transparent; 
        }
    </style>
@endsection

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">Knowledge Sharing</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
                @if(Auth::user()->role_id == 1)
                <button class="btn btn-primary shadow-md mr-2" id="tambah-data" onclick="refreshState('#modal-tambah-data')">Tambah Data</button>
                @endif
                {{-- <div class="dropdown inline">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center">
                            <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40 ">
                        <ul class="dropdown-content">
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div> --}}
            </div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" id="myInputTextField" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="col-span-12" id="data-knowledge">
            
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
    <!-- BEGIN: Modal Content -->
    <form id="modal-tambah-data" class="modal modal-data" enctype="multipart/form-data" data-tw-backdrop="static" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- BEGIN: Modal Header -->
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Tambah Data</h2>
                    <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                </div>
                <!-- END: Modal Header -->
                <!-- BEGIN: Modal Body -->
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 parent">
                        <label for="title" class="form-label">Title {{ dot() }}</label>
                        <input id="title" name="title" type="text" class="form-control required"
                            placeholder="Masukan title">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" id="description"><p>Write description here.</p></textarea>
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
                    <button type="button" id="save-data" 
                        class="btn btn-primary w-20" onclick="uploadKnowledgeSharing()">Simpan</button>
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </form>
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script>
        var xhr = [];
        var indexFile = 1;
        var editor;
        
        (function() {
            editor = CKEDITOR.replace('description', {
                // CKEditor configuration options for updating
            });

            $('#tambah-data').click(function() {
                clear();
                $('.not-editable').not('.readonly').removeClass('disabled');
                $('.not-editable').find('input').not('.readonly').prop('readonly', false);

                $('#fileContainer').empty();
                indexFile = 1;
                $("#addFileButton").click();

                const el = document.querySelector("#modal-tambah-data");
                const modal = tailwind.Modal.getOrCreateInstance(el);
                modal.toggle();
            })

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
                    class: "form-control mb-2 multiple-file file-input-field",
                    name: "file_data[]",
                    multiple: true,
                    "data-id": indexFile,
                    style: "border-radius: unset;"
                });
                const labelFile = $("<label>", {
                    id: "label-filename" + indexFile,
                    text: 'No File Choosen'
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

                fileInput.append(fileLabel, '<br>', inputFile, labelFile, '<br>', previewLink);
                $("#fileContainer").append(fileInput);

                indexFile++;
            });

            $("#fileContainer").on("click", ".preview-link", function(e) {
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

            $("#fileContainer").on("change", ".file-input-field", function() {
                const previewLink = $(this).siblings(".preview-link");
                const seqData = $(this).siblings(".seq-input-field");
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    $('#label-filename' + $(this).data('id')).html(fileName);
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
                    class: "form-control mb-2 multiple-file file-input-field",
                    name: "file_data[]",
                    multiple: true,
                    "data-id": indexFile,
                    style: "border-radius: unset;"
                });
                const labelFile = $("<label>", {
                    id: "label-filename" + indexFile,
                    text: 'No File Choosen'
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

                fileInput.append(fileLabel, '<br>', inputFile, labelFile, '<br>', previewLink);
                $("#fileContainer").append(fileInput);

                indexFile++;
            });

            getKnowledgeSharing();

            $("#delete-data").on("click", function() {
                console.log('Yes');
                var id = $("#delete-data").data('id');
                console.log(id);
            });
        })()

        // document.addEventListener('DOMContentLoaded', function() {
        //     CKEDITOR.replace('description');
        // });

        function getKnowledgeSharing() {
            $.ajax({
                url: "{{ route('getKnowledgeSharing') }}",
                type: 'get',
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    var html = '';
                    data.data.forEach((d, i) => {
                        html += '<div class="intro-y col-span-12 p-5 mt-6 mb-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">' +
                                '<div class="flex justify-between">' +
                                '<h2 class="intro-y text-lg font-medium">' + d.title + '</h2>';
                        if(data.role == 1){
                            html += '<div class="flex space-x-2">' +
                                    '<a href="javascript:void(0)" class="p-2 edit-data" data-id="' + d.id + '"><i class="fas fa-edit"></i></a>' +
                                    '<a href="javascript:void(0)" class="p-2 delete-data" data-id="' + d.id + '"><i class="fas fa-trash"></i></a>' +
                                    '</div>';
                        }
                            html += '</div>' +
                                '<div>' + d.description + '</div>';
                        d.knowledge_sharing_file.forEach((d, i) => {
                            html += '<a href="' + "{{ url('/') }}/" + d.file + '" target="_blank" class="btn btn-primary mt-4 ml-1">View File</a>';
                        });

                        html += '</div>';
                    });

                    $("#data-knowledge").html(html);

                    $(".edit-data").click(function() {
                        var knowledgeId = $(this).data("id");
                        $('#fileContainer').empty();
                        indexFile = 1;

                        $.ajax({
                            url: '{{ route('editKnowledgeSharing') }}',
                            data: { 'knowledgeId' : knowledgeId },
                            type: 'get',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                $('#title').val(data.data[0].title);
                                $('#id').val(data.data[0].id);
                                editor.on('instanceReady', function(event) {
                                    var editorInstance = event.editor;
                                    editorInstance.setData(data.data[0].description);
                                });
                                // CKEDITOR.instances.description.setData(data.data[0].description);

                                data.data[0].knowledge_sharing_file.forEach(function(value, index) {
                                    const filePath = value.file;
                                    const fileName = filePath.substring(filePath.lastIndexOf('/') + 1);

                                    const fileInput = $("<div>", {
                                        class: "col-span-12 mt-2 parent"
                                    });
                                    const fileLabel = $("<label>", {
                                        class: "form-label",
                                        text: "File " + indexFile
                                    });
                                    const inputFile = $("<input>", {
                                        type: "file",
                                        class: "form-control mb-2 multiple-file file-input-field",
                                        name: "file_data[]",
                                        multiple: true,
                                        "data-id": indexFile,
                                        style: "border-radius: unset;"
                                    });
                                    const labelFile = $("<label>", {
                                        id: "label-filename" + indexFile,
                                        text: fileName
                                    });
                                    const inputSeq = $("<input>", {
                                        type: "hidden",
                                        class: "form-control seq-input-field",
                                        name: "seq_data[]"
                                    });
                                    const previewLink = $("<a>", {
                                        href: "{{ url('/') }}/" + value.file,
                                        class: "preview-link",
                                        text: "Preview File " + indexFile,
                                        style: "text-decoration: underline;"
                                    });

                                    fileInput.append(fileLabel, '<br>', inputFile, labelFile, '<br>', previewLink);
                                    $("#fileContainer").append(fileInput);

                                    indexFile++;
                                });

                                const el = document.querySelector("#modal-tambah-data");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
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
                    });

                    $(".delete-data").click(function() {
                        var knowledgeId = $(this).data("id");

                        var previousWindowKeyDown = window.onkeydown;
                        Swal.fire({
                            title: "Hapus Data",
                            text: "Klik Tombol Ya jika akan menghapus data.",
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
                                    url: '{{ route('deleteKnowledgeSharing') }}',
                                    data: { 'knowledgeId' : knowledgeId },
                                    type: 'post',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(data) {
                                        if (data.status == 1) {
                                            Swal.fire({
                                                title: data.message,
                                                icon: 'success',
                                            });

                                            getKnowledgeSharing();
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
                    });
                },
                error: function(data) {
                    getKnowledgeSharing();
                }
            });
        }

        function uploadKnowledgeSharing() {
            var validation = 0;
            console.log(!$('#id').val());

            if(!$('#id').val()){
                if ($('input[name="file_data[]"]')[0].files.length === 0) {
                    ToastNotification('warning', 'File harus diisi');
                    return false;
                }
            }

            if (validation != 0) {
                ToastNotification('warning', "Semua data harus diisi");
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

            formData.append('description', CKEDITOR.instances.description.getData());

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
                        url: '{{ route('storeKnowledgeSharing') }}',
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

                                getKnowledgeSharing();

                                const el = document.querySelector("#modal-tambah-data");
                                const modal = tailwind.Modal.getOrCreateInstance(el);
                                modal.toggle();
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
