<div class="box col-span-12">
    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-left bg-primary rounded-t-lg"
            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
            <a href="javascript:;" class="font-medium text-white w-full text-xl">DATA PASIEN</a>
        </div>
        <div class="p-5 parent-hewan" id="data-pasien">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 md:col-span-4">
                    <img alt="Amore Animal Clinic" class="rounded-md"
                        src="{{ $pasien->image ? route('dashboard') . '/' . $pasien->image : asset('dist/images/amore.png') }}">
                </div>
                <div class="col-span-12 md:col-span-8 grid grid-cols-12 gap-4">

                    <div class="col-span-12 parent md:col-span-4">
                        <label for="">Kode Owner</label>
                        <input type="text" class="form-control" name="name" value="{{ $data->owner->kode }}"
                            readonly>
                    </div>

                    <div class="col-span-12 parent md:col-span-4">
                        <label for="">Nama Owner</label>

                        @if ($data->status_owner)
                            <span
                                class="py-1 px-2 rounded-full text-xs bg-danger text-center text-white cursor-pointer font-medium">
                                Leave
                            @else
                                <span
                                    class="py-1 px-2 rounded-full text-xs bg-success text-center text-white cursor-pointer font-medium">
                                    Available
                        @endif
                        </span>
                        <input type="text" class="form-control" readonly name="nama_owner"
                            value="{{ $data->owner->name }}">
                    </div>

                    <div class="col-span-12 parent md:col-span-4">
                        <label for="">No Handphone {{ dot() }}</label>
                        <input type="text" class="form-control" name="name" value="{{ $data->owner->telpon }}">
                    </div>

                    <div class="col-span-12 parent md:col-span-4">
                        <label for="">Nama Hewan {{ dot() }}</label>
                        <input type="text" class="form-control" name="name" value="{{ $pasien->name }}">
                    </div>

                    <div class="col-span-12 parent md:col-span-4">
                        <label for="">Komunitas</label>
                        <input type="text" readonly class="form-control" name="komunitas"
                            @if ($data->owner->komunitas == null) value="-">
                        @else
                            value="{{ $data->owner->komunitas }}"> @endif
                            <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                        <input type="hidden" name="pendaftaran_id" value="{{ $data->id }}">
                        {{ csrf_field() }}
                    </div>



                    <div class="col-span-12 parent md:col-span-4">
                        <label class="flex justify-between"><span>Spesies {{ dot() }}</span>
                            <a href="javascript:;" onclick="window.open('{{ route('hewan') }}')"><i
                                    class="fa fa-plus text-info"></i>
                            </a>
                        </label>
                        <select class="form-control binatang_id select2 required" name="binatang_id">
                        </select>
                    </div>
                    <div class="col-span-12 parent md:col-span-6">
                        <label class="flex justify-between"><span>Ras {{ dot() }}</span>
                            <a href="javascript:;" onclick="window.open('{{ route('ras') }}')"><i
                                    class="fa fa-plus text-info"></i>
                            </a>
                        </label>
                        <select class="form-control select2 ras_id required" name="ras_id">
                            <option value="">Pilih Ras</option>
                        </select>
                    </div>
                    <div class="col-span-12 parent md:col-span-6">
                        <label>Jenis Kelamin {{ dot() }}
                        </label>
                        <select class="form-control select2 sex required required" name="sex">
                            <option value="">Pilih Jenis Kelamin</option>
                            @foreach (\App\Models\Pasien::$enumJenisKelamin as $item)
                                <option {{ $pasien->sex == $item ? 'selected' : '' }} value="{{ $item }}">
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 parent md:col-span-4">
                        <label>Tanggal Lahir
                        </label>
                        <div class="input-group">
                            <div class="input-group-text">
                                <i class="fa-solid fa-cake-candles"></i>
                            </div>

                            <input type="text" class="form-control date_of_birth" data-single-mode="true"
                                name="date_of_birth" value="{{ $pasien->date_of_birth }}">
                        </div>
                    </div>
                    <div class="col-span-12 parent md:col-span-4">
                        <label>Umur
                        </label>
                        <div class="input-group parent">
                            <div class="input-group-text">
                                <i class="fa-solid fa-cake-candles"></i>
                            </div>
                            <input type="text" class="form-control umur" readonly name="umur" value="">
                        </div>
                    </div>
                    <div class="col-span-12 parent md:col-span-4">
                        <label>Life Stage {{ dot() }}
                        </label>
                        <select class="form-control select2 life_stage required" name="life_stage">
                            <option value="">Pilih Life Stage</option>
                            @foreach (\App\Models\Pasien::$enumLifeStage as $i => $item)
                                <option {{ $item['title'] == $pasien->life_stage ? 'selected' : '' }}
                                    value="{{ $item['title'] }}">
                                    {{ $item['title'] }} | {{ $item['description'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 parent">
                        <label for="">Ciri Khas {{ dot() }}</label>
                        <textarea type="text" class="form-control required" name="ciri_khas">{{ $pasien->ciri_khas }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box col-span-12">
    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-left bg-warning rounded-t-lg"
            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
            <a href="javascript:;" class="font-medium text-white w-full text-xl">PEMERIKSAAN</a>
        </div>
        <div class="w-full p-5" id="data-pemeriksaan">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <label for="" class="form-label font-bold">Jenis Antrean</label>
                    <input type="text" class="form-control" readonly value="{{ $data->poli->name }}">
                </div>
                <div class="col-span-12">
                    <label class="form-label font-bold">Anamnesa Pendaftaran</label>

                    <table class="w-full">
                        @foreach ($pasien->PendaftaranPasienAnamnesa->where('pendaftaran_id', $data->id) as $item)
                            <tr class="{{ $item->anamnesa ? $item->anamnesa->name : '-' }}">
                                <td>
                                    {{ $item->anamnesa ? $item->anamnesa->name : '-' }}
                                    <input type="hidden" class="anamnesa_id"
                                        value="{{ $item->anamnesa ? $item->anamnesa->id : '-' }}">
                                </td>
                                <td class="text-center">
                                    @if ($item->ya == 'on')
                                        <label
                                            class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Ya</label>
                                    @elseif ($item->tidak == 'on')
                                        <label
                                            class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Tidak</label>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <input type="text" class="form-control keterangan_anamnesa"
                                        placeholder="Masukan keterangan" value="{{ $item->keterangan }}">
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <hr class="my-3">
                </div>
                <div class="col-span-12 parent">
                    <span class="form-label font-bold">Tujuan Periksa {{ dot() }}</span>
                    <textarea type="text" class="lain_lain form-control" placeholder="Isi lain lain pada hewan" name="lain_lain">{{ $infoPasien->lain_lain }}</textarea>
                </div>
                <div class="col-span-12 parent">
                    <span class="form-label font-bold">Catatan Pemeriksaan</span>
                    <textarea type="text" class="catatan form-control" placeholder="Isi catatan pendaftaran" name="catatan">{{ $data->catatan }}</textarea>
                </div>
                <div class="col-span-6 parent">
                    <label for="" class="form-label font-bold">Berat {{ dot() }}</label>
                    <div class="input-group parent">
                        <input type="text" class="form-control required maskdec" name="berat"
                            placeholder="Isi berat hewan" value="{{ $pasien->berat }}">
                        <div class="input-group-text">
                            Kg
                        </div>
                    </div>
                </div>
                <div class="col-span-6 parent">
                    <label for="" class="form-label font-bold">Suhu {{ dot() }}</label>
                    <div class="input-group parent">
                        <input type="text" class="form-control required maskdec" name="suhu"
                            placeholder="Isi suhu hewan" value="{{ $pasien->suhu }}">
                        <div class="input-group-text">
                            C
                        </div>
                    </div>
                </div>
                <div class="col-span-12 parent">
                    <label class="form-label font-bold">Anamnesa {{ dot() }}</label>
                    <textarea type="text" class="anamnesa form-control required" placeholder="Isi anamnesa pemeriksaan"
                        name="anamnesa"></textarea>
                </div>
                <div class="col-span-12 parent">
                    <label class="form-label font-bold">Hasil Pemeriksaan {{ dot() }}</label>
                    <textarea type="text" class="hasil_pemeriksaan form-control required" placeholder="Isi hasil pemeriksaan"
                        name="hasil_pemeriksaan"></textarea>
                </div>
                <div class="col-span-12 parent" id="hasil-lab">
                    <div class="flex justify-between w-full">
                        <label class="form-label font-bold">Hasil Lab</label>
                        <a href="javascript:;" onclick="tambahHasilLab()" class="dropdown-item text-primary">
                            <i class="fa-solid fa-book-medical mr-2"></i>Tambah hasil lab
                        </a>
                    </div>
                </div>
                <div class="col-span-12 parent">
                    <hr class="my-2">
                    <label class="form-label font-bold">Diagnosa {{ dot() }}</label>
                    <textarea type="text" class="diagnosa form-control required" placeholder="Isi hasil diagnosa" name="diagnosa"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box col-span-12">
    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-left rounded-t-lg"
            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(75, 50, 216)">
            <a href="javascript:;" class="font-medium text-white w-full text-xl">TINDAKAN MEDIS</a>
        </div>
        <div class="p-5" id="data-pasien">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-6 flex justify-between">
                    <label class="font-bold">Rawat Jalan</label>
                    <input type="checkbox" class="mr-1" id="rawat_jalan" name="rawat_jalan">
                </div>
                <div class="col-span-6 flex justify-between">
                    <label class="font-bold">Titip Sehat</label>
                    <input type="checkbox" class="mr-1" id="titip_sehat" name="titip_sehat">
                </div>
                <div class="col-span-6 flex justify-between">
                    <label class="font-bold">Grooming</label>
                    <input type="checkbox" class="mr-1" id="grooming" name="grooming">
                </div>
                <div class="col-span-6 flex justify-between parent-rawat-inap">
                    <label class="font-bold">Rawat Inap</label>
                    <input type="checkbox" class="mr-1" id="rawat_inap" name="rawat_inap">
                </div>
                <div class="col-span-6 flex justify-between parent-rawat-inap">
                    <label class="font-bold">Menyetujui Anestesi</label>
                    <input type="checkbox" class="mr-1" id="bius" name="bius">
                </div>
                <div class="col-span-6 flex justify-between">
                    <label class="font-bold">Bedah</label>
                    <input type="checkbox" class="mr-1" id="tindakan_bedah" name="tindakan_bedah">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box col-span-12">
    <div class="intro-y col-span-12 md:col-span-6 xl:col-span-4">
        <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-left rounded-t-lg"
            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(25, 154, 135)">
            <a href="javascript:;" class="font-medium text-white w-full text-xl">TREATMENT</a>
        </div>
        <div class="p-5" id="data-pasien">
            <div class="grid grid-cols-12 gap-2">
                <div
                    class="col-span-12 flex justify-between rawat_jalan_div rawat_inap_div titip_sehat_div additional tindakan_parent">
                    <label class="form-label font-bold">Tindakan Poli</label>
                    <a href="javascript:;" onclick="tambahTreatment()" class="dropdown-item text-primary">
                        <i class="fa-solid fa-hand-holding-medical mr-2"></i>Tambah Tindakan Poli
                    </a>
                </div>
                <div class="grid grid-cols-12 gap-6 col-span-12 rawat_jalan_div rawat_inap_div titip_sehat_div parent additional tindakan_div tindakan_parent"
                    id="append-treatment">

                </div>
                <div
                    class=" col-span-12 mb-2 rawat_jalan_div rawat_inap_div titip_sehat_div additional tindakan_parent">
                    <hr>
                </div>
                <div
                    class="col-span-12 flex justify-between rawat_jalan_div rawat_inap_div titip_sehat_div grooming_div additional resep_obat_parent">
                    <label class="form-label font-bold">Resep Obat</label>
                    <a href="javascript:;" onclick="tambahResep()" class="dropdown-item text-primary"
                        id="add-resep">
                        <i class="fa-solid fa-kit-medical mr-2"></i>
                        Tambah Obat
                    </a>
                </div>
                <div class="grid grid-cols-12 gap-6 col-span-12 clearfix rawat_jalan_div rawat_inap_div grooming_div titip_sehat_div additional resep_obat_div resep_obat_parent"
                    id="append-resep">
                </div>
                <div class="loading-resep col-span-12 text-center hidden">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </div>
                <div class=" col-span-12 mb-2 rawat_jalan_div rawat_inap_div additional resep_obat_parent">
                    <hr>
                </div>
                <div class="col-span-12 mb-2 rawat_inap_div titip_sehat_div parent additional pakan_parent">
                    <label class="form-label font-bold">Pakan</label>
                    <select name="pakan" id="pakan" class="form-control select2">
                        <option value="">Pilih pakan</option>
                        @foreach ($pakan as $item)
                            <option
                                {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                                value="{{ $item->id }}">{{ $item->name }}
                                {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stock Kosong)' : '') : '(Stock Kosong)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 mb-2 rawat_inap_div titip_sehat_div parent additional kamar_parent">
                    <label class="form-label font-bold">Ruang Rawat Inap {{ dot() }}</label>
                    <select class="form-control required" name="kamar_rawat_inap_dan_bedah_id"
                        id="kamar_rawat_inap_dan_bedah_id">
                    </select>
                </div>
                <div class="col-span-12 mb-2 grooming_div parent additional grooming_parent">
                    <label class="form-label font-bold">Jenis Grooming {{ dot() }}</label>
                    <select name="jenis_grooming" id="jenis_grooming" class="form-control select2 required">
                        <option value="">Pilih jenis grooming</option>
                        @foreach ($tindakanGrooming as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 mb-2 grooming_div parent additional grooming_parent">
                    <label class="form-label font-bold">Cukur {{ dot() }}</label>
                    <select name="cukur" id="cukur" class="form-control select2 required">
                        <option value="">Pilih cukur</option>
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </div>
                <div class="col-span-12 mb-2 tindakan_bedah_div parent additional tindakan_bedah_parent">
                    <label class="form-label font-bold">Tindakan Bedah {{ dot() }}</label>
                    <select name="rekomendasi_tindakan_bedah[]" id="rekomendasi_tindakan_bedah"
                        class="form-control select2 required" multiple>
                        <option value="">Pilih Tindakan Bedah</option>
                        @foreach ($tindakanBedah as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 parent">
                    <label class="form-label font-bold">Catatan Pemeriksaan{{ dot() }}</label>
                    <textarea type="text" class="catatan form-control required" placeholder="Isi Catatan Pemeriksaan" name="catatan"></textarea>
                </div>
                <div class="col-span-12 mb-2 tindakan_bedah_div parent additional tindakan_bedah_parent">
                    <label class="form-label font-bold">Status Urgensi {{ dot() }}</label>
                    <select name="status_urgent" id="status_urgent" class="form-control select2 required">
                        <option value="false">Normal</option>
                        <option value="true">Urgensi</option>
                    </select>
                </div>
                <div class="col-span-12 parent tindakan_bedah_div additional tindakan_bedah_parent">
                    <label for="rekomendasi_tanggal_bedah" class="form-label">Rekomendasi Tanggal Bedah
                        {{ dot() }}
                    </label>
                    <div class="input-group parent">
                        <div class="input-group-text">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <input id="rekomendasi_tanggal_bedah" name="rekomendasi_tanggal_bedah" type="text"
                            class="form-control required datepicker" placeholder="yyyy-mm-dd"
                            placeholder="yyyy-mm-dd" data-single-mode="true">
                    </div>
                </div>
                <div class="col-span-12 mb-2 flex justify-between">
                    <div class="dropdown inline">
                        <button type="button" class="dropdown-toggle btn px-2 box" aria-expanded="false"
                            data-tw-toggle="dropdown">
                            <span class="w-5 h-5 flex items-center justify-center">
                                <i class="fas fa-trash"></i>
                            </span>
                        </button>
                        <div class="dropdown-menu w-56">
                            <ul class="dropdown-content">
                                <li>
                                    <a href="javascript:;" class="dropdown-item text-danger"
                                        onclick="hapus('{{ $data->id }}','all')">
                                        <i class="w-4 h-4 fa-solid fa-trash mr-4"></i>
                                        Batalkan semua pasien yang belum diperiksa
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;" class="dropdown-item text-warning"
                                        onclick="hapus('{{ $data->id }}','{{ $pasien->id }}')"><i
                                            class="fas fa-trash mr-4"></i>Batalkan Pasien Ini
                                    </a>

                                </li>
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="simpan" onclick="store()"><i
                            class="fas fa-save mr-2"></i>
                        Simpan Pemeriksaan</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function() {
        $('.select2').select2({
            width: '100%',
        })
        $('.additional').addClass('hidden')

        $(".datepicker").each(function() {
            if ($(this).data("format")) {
                format = $(this).data('format');
            } else {
                format = 'YYYY-MM-DD';
            }

            let options = {
                autoApply: false,
                singleMode: false,
                numberOfColumns: 2,
                numberOfMonths: 2,
                showWeekNumbers: true,
                format: format,
                dropdowns: {
                    minYear: 1990,
                    maxYear: null,
                    months: true,
                    years: true,
                },
            };

            if ($(this).data("single-mode")) {
                options.singleMode = true;
                options.numberOfColumns = 1;
                options.numberOfMonths = 1;
            }

            if ($(this).data("format")) {
                options.format = $(this).data("format");
            }

            if (!$(this).val()) {
                let date = dayjs().format(options.format);
                date += !options.singleMode ?
                    " - " + dayjs().add(1, "month").format(options.format) :
                    "";
                $(this).val(date);
            }

            new Litepicker({
                element: this,
                ...options,
                setup: (picker) => {
                    picker.on('button:apply', (date1, date2) => {
                        generateHariAwal();
                    });
                },
            });
        });
        $('.maskdec').maskMoney({
            precision: 2,
            thousands: '',
            decimals: '.',
            allowZero: true,
        })

        $(document).on('change', '.binatang_id', function() {
            var par = $(this).parents('.parent_hewan');
            $(par).find('.ras_id').val(null).trigger('change.select2');
        })

        $(".date_of_birth").each(function() {
            let options = {
                autoApply: false,
                singleMode: false,
                numberOfColumns: 2,
                numberOfMonths: 2,
                showWeekNumbers: true,
                format: "YYYY-MM-DD",
                dropdowns: {
                    minYear: 1990,
                    maxYear: null,
                    months: true,
                    years: true,
                },
            };

            if ($(this).data("single-mode")) {
                options.singleMode = true;
                options.numberOfColumns = 1;
                options.numberOfMonths = 1;
            }

            if ($(this).data("format")) {
                options.format = $(this).data("format");
            }

            new Litepicker({
                element: this,
                ...options,
                setup: (picker) => {
                    picker.on('button:apply', (date1, date2) => {
                        generateAge($(this));
                    });
                },
            });
        });

        $('.binatang_id').each(function() {
            $(this).select2({
                width: '100%',
                ajax: {
                    url: "{{ route('select2Pendaftaran') }}?param=binatang_id",
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
                placeholder: 'Pilih Hewan',
                minimumInputLength: 0,
                templateResult: formatRepoNormalStatus,
                templateSelection: formatRepoNormalStatusSelection
            });
        });

        $('.ras_id').each(function() {
            var par = $(this).parents('.parent_hewan');
            $(this).select2({
                width: '100%',
                ajax: {
                    url: "{{ route('select2Pasien') }}?param=ras_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            binatang_id() {
                                return $('.binatang_id').val();
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
                placeholder: 'Pilih Hewan Dahulu',
                minimumInputLength: 0,
                templateResult: formatRepoNormalStatus,
                templateSelection: formatRepoNormalStatusSelection
            });
        });

        @if ($pasien->Binatang)
            var newOption = new Option('{{ $pasien->Binatang->name }}',
                '{{ $pasien->Binatang->id }}',
                true,
                true
            );
            $('.binatang_id').append(newOption).trigger('change.select2');
        @endif


        @if ($pasien->Ras)
            var newOption = new Option('{{ $pasien->Ras->name }}',
                '{{ $pasien->ras->id }}',
                true,
                true
            );

            $('.ras_id').append(newOption).trigger('change.select2');
        @endif


        generateAge();
    })()
    var indexRacikan = 1;

    function tambahTreatment() {
        var select = '<select class="form-control select2filter required tindakan_id" name="tindakan_id[]">';
        select += '<option data-tarif="" value="">Pilih Tindakan</option>';
        @foreach (\App\Models\Tindakan::where('status', true)->where('binatang_id', $pasien->binatang_id)->get() as $item)
            select +=
                '<option data-tarif="{{ $item->tarif }}" value="{{ $item->id }}">{{ $item->name }}</option>';
        @endforeach
        select += '</select>';
        var html =
            '<div class="mb-3 col-span-12 flex parent-treatment">' +
            '<div class="block" style="width:100%">' +
            '<div style="width:100%">' +
            select +
            '</div>' +
            '<div stlye="width:100%">' +
            '<textarea  name="treatment[]" placeholder="Masukan Advice/Treatment" ' +
            'class="form-control treatment"></textarea>' +
            '</div>' +
            '</div>' +
            '<button type="button" class="btn btn-danger" onclick="hapusTreatment(this)"><i' +
            ' class="fa fa-trash"></i></button>' +
            '</div>';
        $("#append-treatment").append(html);

        $('.select2filter').select2({
            width: '100%',
        })
    }


    function tambahResep() {
        $(".loading-resep").removeClass('hidden');
        $('#add-resep').addClass('disabled');
        $.ajax({
            url: "{{ route('tambahResepPemeriksaanPasien') }}",
            type: 'get',
            data: {
                index: indexRacikan,
                pendaftaran_id: '{{ $data->id }}'
            },
            success: function(data) {
                $('#append-resep').append(data)
                $(".loading-resep").addClass('hidden');

                $('.select2filter').select2({
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
                tambahResep();
                $(".loading-resep").addClass('hidden');
            }
        });
    }

    function tambahChildRacikan(child) {
        var parent = $(child).parents('.parent-resep');
        $.ajax({
            url: "{{ route('tambahRacikanChildPemeriksaanPasien') }}",
            type: 'get',
            data: {
                index: $(parent).find('.index_racikan').val(),
                pendaftaran_id: '{{ $data->id }}'
            },
            success: function(data) {
                $(parent).find('.append-racikan').append(data);

                $('.select2filter').select2({
                    width: '100%',
                })

                // $('.mask-non-decimal').maskMoney({
                //     precision: 0,
                //     thousands: '',
                //     allowZero: true,
                // })
            },
            error: function(data) {
                tambahResep();
                $(".loading-resep").addClass('hidden');
            }
        });
    }

    function hapusDiagnosa(child) {
        $(child).parents('.parent-diagnosa').remove();
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

    function formatRepoNormalStatusSelection(repo) {
        return repo.text || repo.text;
    }

    function formatRepoNormalStatus(repo) {
        if (repo.loading) {
            return repo.text;
        }
        console.log(repo);
        // scrolling can be used
        var markup = $('<span value=' + repo.id + '>' + repo.text + '</span>');
        return markup;
    }

    function generateAge() {
        $.ajax({
            url: "{{ route('generateAgePendaftaran') }}",
            type: 'get',
            data: {
                date_of_birth() {
                    return $('.date_of_birth').val();
                }
            },
            success: function(data) {
                $('.umur').val(data.data);

                $('.life_stage').val(data.life_stage).trigger('change.select2');

            },
            error: function(data) {
                generateKode();
            }
        });
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
                    url: '{{ route('deleteMonitoringAntrian') }}',
                    data: {
                        id: id,
                        param: param,
                        _token: "{{ csrf_token() }}"
                    },
                    type: 'post',
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
</script>
