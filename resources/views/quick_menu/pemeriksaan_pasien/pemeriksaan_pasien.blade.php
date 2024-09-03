@extends('../layout/' . $layout)

@section('subhead')
    <title>CRUD Form - Rubick - Tailwind HTML Admin Template</title>
@endsection

@section('style')
    <style>
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
    </style>
@endsection

@section('subcontent')
    <div class="intro-y flex justify-between items-center mt-8">
    </div>
    <div class="grid grid-cols-12 gap-6">
        <!-- BEGIN: Form Layout -->
        <div class="col-span-12 xl:col-span-3">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">Pasien {{ $data->kode_pendaftaran }}</h2>
            </div>
            <div id="list-pasien">
                @foreach ($data->PendaftaranPasien as $i => $item)
                    @php
                        $isDone = count($item->Pasien->RekamMedisPasien->where('pendaftaran_id', $data->id));

                        if ($item->status == 'Cancel') {
                            $isDone = 2;
                        }
                    @endphp
                    <div class="intro-y {{ $isDone == 1 ? 'disabled' : '' }}">
                        <div
                            class="box px-4 py-4 mb-3 flex items-center zoom-in {{ $isDone == 0 ? 'pasien' : '' }} pasien_{{ $item->pasien_id }}">
                            <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                <img alt="Amore Animal Clinic"
                                    src="{{ $item->Pasien->image ? route('dashboard') . '/' . $item->Pasien->image : asset('dist/images/amore.png') }}">
                                <input type="hidden" value="{{ $item->pasien_id }}" class="pasien_id">
                            </div>
                            <div class="ml-4 mr-auto">
                                <div class="font-medium">{{ $item->Pasien->name }}</div>
                                <div class="text-slate-500 text-xs mt-0.5">{{ $item->Pasien->Binatang->name }}</div>
                            </div>
                            @if ($isDone == 0)
                                <div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">
                                    Waiting
                                </div>
                            @elseif($isDone == 2)
                                <div
                                    class="py-1 px-2 rounded-full text-xs bg-warning text-white cursor-pointer font-medium">
                                    Cancel
                                </div>
                            @else
                                <div
                                    class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                    Done
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">Riwayat Rekam Medis</h2>
            </div>
            <div id="list-rekam-medis">

            </div>
        </div>
        <form class="col-span-12 xl:col-span-9 grid grid-cols-12 gap-6" id="data-pemeriksaan">

        </form>
        <!-- END: Form Layout -->
        <div id="modal-rekam-medis" class="modal" data-tw-backdrop="static" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" style="width: 80% !important">
                <div class="modal-content">
                    <!-- BEGIN: Modal Header -->
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">Rekam Medis</h2>
                        <i class="fas fa-times cursor-pointer" data-tw-dismiss="modal"></i>
                    </div>
                    <!-- END: Modal Header -->
                    <!-- BEGIN: Modal Body -->
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3" id="append-rekam-medis">

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
    </div>
@endsection

@section('script')
    <script>
        var table;
        var pasienActive = '{{ $pasienActive->pasien_id }}';
        var xhr = [];
        (function() {

            $('.pasien_' + pasienActive).addClass('active');
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
                        url: "{{ route('datatableMonitoringAntrian') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            branch_id() {
                                return $('#branch_id').val();
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
                        data: 'kode_pendaftaran',
                        name: 'kode_pendaftaran'
                    }, {
                        data: 'pasien',
                        name: 'pasien'
                    }, {
                        data: 'owner',
                        name: 'owner',
                    }, ]
                })
                .columns.adjust()
                .responsive.recalc();

            $('.select2').select2({
                width: '100%',
            })
            getPasien();
        })()

        function tambahHasilLab() {
            var html =
                '<div class="col-span-4 mb-2 hasil-lab-parent">' +
                '<input type="file" class="dropify hasil_lab mb-2" name="hasil_lab[]" data-allowed-file-extensions="pdf jpeg jpg">' +
                '<button class="btn btn-danger w-full" onclick="hapusHasilLab(this)">Hapus</button>' +
                '</div>';

            $('#hasil-lab').append(html);

            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Drag and drop or click to replace',
                    'remove': 'Remove',
                    'error': 'Ooops, something wrong happended.'
                }
            });
        }

        function hapusHasilLab(child) {
            $(child).parents('.hasil-lab-parent').remove();
        }

        $('.pasien').click(function() {
            console.log('tes');
            $('.pasien').removeClass('active');
            $(this).addClass('active');

            pasienActive = $(this).find('.pasien_id').val();

            getPasien();
        })

        function getPasien() {
            overlay(true);
            $.ajax({
                url: "{{ route('getPasienPemeriksaanPasien') }}",
                type: 'get',
                data: {
                    id: pasienActive,
                    pendaftaran_id: '{{ $data->id }}',
                },
                beforeSend: function(jqXHR) {
                    xhr.push(jqXHR);
                },
                success: function(data) {
                    $("#data-pemeriksaan").html(data);
                    $('.dropify').dropify();
                    $("#kamar_rawat_inap_dan_bedah_id").select2({
                        width: '100%',
                        ajax: {
                            url: "{{ route('select2PemeriksaanPasien') }}?param=kamar_rawat_inap_dan_bedah_id",
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
                        placeholder: 'Pilih Ruang Rawat Inap',
                        minimumInputLength: 0,
                        templateResult: formatRepoKamar,
                        templateSelection: formatRepoKamarSelection
                    });

                    getListRekamMedis();
                    overlay(false);
                },
                error: function(data) {
                    overlay(false);
                }
            });
        }

        function getListRekamMedis() {
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
                    $('#append-rekam-medis').html(data);
                    const el = document.querySelector("#modal-rekam-medis");
                    const modal = tailwind.Modal.getOrCreateInstance(el);
                    modal.toggle();
                },
                error: function(data) {
                    lihatRekamMedis(id);
                }
            });
        }



        function checkDiv() {
            var tindakan_parent = 0;
            var resep_obat_parent = 0;
            var pakan_parent = 0;
            var kamar_parent = 0;
            var grooming_parent = 0;
            var tindakan_bedah_parent = 0;
            if ($('#rawat_jalan').is(':checked')) {
                tindakan_parent += 1;
                resep_obat_parent += 1;
            }

            if ($('#grooming').is(':checked')) {
                grooming_parent += 1;
                resep_obat_parent += 1;
            }


            if ($('#titip_sehat').is(':checked')) {
                tindakan_parent += 1;
                resep_obat_parent += 1;
                pakan_parent += 1;
            }

            if ($('#rawat_inap').is(':checked')) {
                tindakan_parent += 1;
                resep_obat_parent += 1;
                pakan_parent += 1;
                kamar_parent += 1;
            }

            if ($('#tindakan_bedah').is(':checked')) {
                tindakan_bedah_parent += 1;
            }

            if (tindakan_parent > 0) {
                $('.tindakan_parent').removeClass('hidden');
            } else {
                $('.tindakan_parent').addClass('hidden');
            }

            if (resep_obat_parent > 0) {
                $('.resep_obat_parent').removeClass('hidden');
            } else {
                $('.resep_obat_parent').addClass('hidden');
            }

            if (pakan_parent > 0) {
                $('.pakan_parent').removeClass('hidden');
            } else {
                $('.pakan_parent').addClass('hidden');
            }

            if (kamar_parent > 0) {
                $('.kamar_parent').removeClass('hidden');
            } else {
                $('.kamar_parent').addClass('hidden');
            }

            if (grooming_parent > 0) {
                $('.grooming_parent').removeClass('hidden');
            } else {
                $('.grooming_parent').addClass('hidden');
            }

            if (tindakan_bedah_parent > 0) {
                $('.tindakan_bedah_parent').removeClass('hidden');
            } else {
                $('.tindakan_bedah_parent').addClass('hidden');
            }
        }


        $(document).on('change', '#rawat_jalan', function() {
            $('.rawat_jalan_div').removeClass('hidden');
            if (!$(this).is(':checked')) {
                $('.rawat_jalan_div').addClass('hidden');
            }
            checkDiv();
        })
        $(document).on('change', '#grooming', function() {
            $('.grooming_div').removeClass('hidden');
            if (!$(this).is(':checked')) {
                $('#jenis_grooming').val('').trigger('change.select2');
                $('#cukur').val('').trigger('change.select2');
                $('.grooming_div').addClass('hidden');
            }
            checkDiv();
        })

        $(document).on('change', '#titip_sehat', function() {
            $('.titip_sehat_div').removeClass('hidden');
            if (!$(this).is(':checked')) {
                $('.titip_sehat_div').addClass('hidden');
                $('#rawat_inap').prop('checked', false);
                $('#titip_sehat').prop('checked', false);
                $(".parent-rawat-inap").removeClass('disabled');
            } else {
                $('#rawat_inap').prop('checked', true);
                $(".parent-rawat-inap").addClass('disabled');
            }
            checkDiv();
        })

        $('.parent-rawat-inap .disabled').click(function() {
            ToastNotification('warning', 'Rawat inap wajib jika ada tindakan bedah.');
        })

        $(document).on('change', '#rawat_inap', function() {
            $('.rawat_inap_div').removeClass('hidden');
            if ($(this).is(':checked')) {
                $('.parent-kamar').removeClass('hidden');
            } else {
                $('.parent-kamar').addClass('hidden');
                $('.rawat_inap_div').addClass('hidden');
                $('#grooming').change();
            }
            $('#pakan').val('').trigger('change.select2');
            $('#kamar_rawat_inap_dan_bedah_id').val(null).trigger('change.select2');
            checkDiv();
        })

        $(document).on('change', '#tindakan_bedah', function() {
            $('.tindakan_bedah_div').removeClass('hidden');
            if ($(this).is(':checked')) {
                $('.parent-urgent').removeClass('hidden');
                $('#rawat_inap').prop('checked', true);
                $('#bius').prop('checked', true);
                $(".parent-rawat-inap").addClass('disabled');
            } else {
                $('.parent-urgent').addClass('hidden');
                $('.tindakan_bedah_div').addClass('hidden');
                $('#rawat_inap').prop('checked', false);
                $('#titip_sehat').prop('checked', false);
                $('#bius').prop('checked', false);
                $(".parent-rawat-inap").removeClass('disabled');
                $('#append-bedah').html('');
            }
            $('#rawat_inap').change();
            $('#status_urgent').val('false').trigger('change.select2');
            checkDiv();
        })



        function store() {
            var validation = 0;
            $('#data-pemeriksaan .required').each(function() {
                var par = $(this).parents('.parent');
                var parentResep = $(this).parents('.racikan-child');
                if (!$(par).hasClass('hidden')) {
                    if (parentResep.length == 0) {
                        if ($(this).val() == '' || $(this).val() == null) {
                            $(this).addClass('is-invalid');
                            $(par).find('.select2-container').addClass('is-invalid');
                            validation++
                            console.log(parentResep.length);
                            console.log($(this))
                        }
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
                ToastNotification('warning', 'Semua data harus diisi');
                return false;
            }

            var formData = new FormData();

            $('.hasil_lab').each(function(i) {
                file = $(this)[0].files[0];
                if (file != undefined) {
                    formData.append('hasil_lab[]', file);
                }
            })

            var data = $('#data-pemeriksaan').serializeArray();

            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            if ($('.tindakan_div').hasClass('hidden')) {
                formData.delete('tindakan_id[]');
            }

            if ($('.resep_obat_div').hasClass('hidden')) {
                formData.delete('parent_resep[]');
            }

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
                        url: '{{ route('storePemeriksaanPasien') }}',
                        data: formData,
                        type: 'post',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                if (data.need_print > 0) {
                                    cetak(data.id);
                                }

                                Swal.fire({
                                    title: 'Success',
                                    text: data.message,
                                    icon: "success",
                                }).then(function() {
                                    location.reload();
                                });

                            } else if (data.status == 2) {
                                if (data.need_print > 0) {
                                    cetak(data.id);
                                }

                                Swal.fire({
                                    title: 'Success',
                                    text: data.message,
                                    icon: "success",
                                }).then(function() {
                                    location.reload();
                                });
                            } else if (data.status == 3) {
                                Swal.fire({
                                    title: 'Terjadi kesalahan',
                                    text: data.message,
                                    icon: "warning",
                                }).then(function() {
                                    location.reload();
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

                // if (result.isConfirmed) {
                //     overlay(true);
                //     window.onkeydown = previousWindowKeyDown;
                //     $('#simpan').addClass('disabled');
                //     $('#simpan').html('<i class="fa-solid fa-circle-notch fa-spin"></i>');
                //     $('#data-pemeriksaan').submit();
                //     // location.reload();
                // }
            })
        }

        function cetak(id) {
            window.open('{{ route('printPemeriksaanPasien', '') }}/' + id);
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
    </script>
@endsection
