@extends('../layout/' . $layout)

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">Hak Akses</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">

            <div class="grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-full">
                    <select class="w-full select2 required form-control" id="role_id">
                        <option value="">Pilih Role</option>
                        @foreach (\App\Models\Role::where('status', true)->get() as $i => $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-full" id="appendHakAkses">

                </div>
            </div>
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
    <div id="modal-tambah-data" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
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
                        <label for="name" class="form-label">Nama Title {{ dot() }}</label>
                        <input id="name" name="name" type="text" class="form-control uppercase required not-editable"
                            placeholder="Masukan nama title">
                        <input type="hidden" id="id" name="id">
                        {{ csrf_field() }}
                    </div>
                    <div class="col-span-12 parent">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" type="text" class="form-control"
                            placeholder="Masukan Keterangan"></textarea>
                    </div>
                </div>
                <!-- END: Modal Body -->
                <!-- BEGIN: Modal Footer -->
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal"
                        class="btn btn-outline-secondary w-20 mr-1">Batal</button>
                    <button type="button" class="btn btn-primary w-20" onclick="store()">Simpan</button>
                </div>
                <!-- END: Modal Footer -->
            </div>
        </div>
    </div>
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
    <script>
        (function() {

            $(document).on('change', '#role_id', function() {
                $.ajax({
                    url: "{{ route('editHakAkses') }}",
                    data: {
                        id() {
                            return $('#role_id').val();
                        }
                    },
                    type: 'get',
                    success: function(data) {
                        $('#appendHakAkses').html(data);
                    },
                    error: function(data) {
                        Toast.fire({
                            title: data.responseJSON.message,
                            type: 'warning',
                            confirmButtonText: 'Ok!',
                        });
                    }
                });
            })

            $('.select2').select2({
                // dropdownParent: $("#modal-tambah-data .modal-body"),
                // theme: 'bootstrap4',
            })
        })()

        function gantiHakAksesGlobal(column, par, groupItemId, roleId) {
            $('.' + column + '_' + groupItemId).prop('checked', $(par).is(':checked'));
            $.ajax({
                url: "{{ route('updateHakAkses') }}",
                data: {
                    column,
                    param: $(par).is(':checked'),
                    groupItemId,
                    roleId,
                    jenis: 'GLOBAL'
                },
                type: 'get',
                success: function(data) {
                    if (data.status == 1) {
                        ToastNotification('success', data.message);
                    } else if (data.status == 2) {
                        ToastNotification('warning', data.message);
                    }
                },
                error: function(data) {
                    ToastNotification('error', data.responseJSON.message);
                }
            });
        }

        function gantiHakAkses(hakAksesId, column, par, roleId, menuId) {
            $.ajax({
                url: "{{ route('updateHakAkses') }}",
                data: {
                    hakAksesId,
                    column,
                    param: $(par).is(':checked'),
                    menuId,
                    roleId,
                    jenis: 'MENU'
                },
                type: 'get',
                success: function(data) {
                    if (data.status == 1) {
                        ToastNotification('success', data.message);

                    } else if (data.status == 2) {
                        ToastNotification('warning', data.message);
                    }
                },
                error: function(data) {
                    ToastNotification('error', data.responseJSON.message);
                }
            });
        }
    </script>
@endsection
