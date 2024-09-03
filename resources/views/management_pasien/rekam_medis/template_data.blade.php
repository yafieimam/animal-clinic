<div class=" col-span-12 mb-3  box">
    <div class="flex items-center col-span-12 border-b border-slate-200/60 dark:border-darkmode-400 px-3 py-2 text-left bg-warning rounded-t-lg"
        style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(163, 37, 37)">
        <a href="javascript:;" class="font-medium text-white w-full text-xl">Ruang Rawat Inap</a>
    </div>
    <div class="grid grid-cols-12 gap-6 modal-parent  p-8">
        <div class="col-span-12 md:col-span-4">
            <img alt="Amore Animal Clinic" class="rounded-md"
                src="{{ $data->image ? route('dashboard') . '/' . $data->image : asset('dist/images/amore.png') }}">
        </div>
        <div class="col-span-12 md:col-span-8 grid grid-cols-12 gap-4 parent_hewan disabled">
            <div class="col-span-12 parent ">
                <label for="">Nama Hewan</label>
                <input type="text" readonly class="form-control" name="name" value="{{ $data->name }}">
                <input type="hidden" name="pasien_id" id="pasien_id" value="{{ $data->id }}">
                <input type="hidden" class="rekam_medis_pasien_id" name="rekam_medis_pasien_id"
                    id="rekam_medis_pasien_id" value="{{ $rm->id }}">
                {{ csrf_field() }}
            </div>

            @if ($rm->rawat_inap)
                <div class="col-span-12 parent md:col-span-6">
                    <label for="">Kamar Ranap</label>
                    <div class="input-group">
                        <div class="input-group-text">
                            <i class="fa-solid fa-bed"></i>
                        </div>
                        @php
                            $kamar = \App\Models\KamarRawatInapDanBedahDetail::where('status_pindah', false)
                                ->where('rekam_medis_pasien_id', $rm->id)
                                ->first();
                        @endphp
                        <input type="text" readonly class="form-control" name="name"
                            value="{{ $kamar ? $kamar->KamarRawatInapDanBedah->name : '' }}">

                    </div>
                </div>
                <div class="col-span-12 parent md:col-span-6">
                    <label for="">Tanggal Ranap</label>
                    <div class="input-group">
                        <div class="input-group-text">
                            <i class="fa-solid fa-calendar"></i>
                        </div>

                        <input type="text" readonly class="form-control" name="name"
                            value="{{ CarbonParse($rm->created_at, 'd/m/Y') }}">

                    </div>
                </div>
            @endif
            <div class="col-span-12 parent md:col-span-4">
                <label for="">Spesies</label>
                <select class="form-control binatang_id required" name="binatang_id">
                </select>
            </div>
            <div class="col-span-12 parent md:col-span-4">
                <label for="">Ras</label>
                <select class="form-control ras_id required" name="ras_id">
                    <option value="">Pilih Ras</option>
                </select>
            </div>
            <div class="col-span-12 parent md:col-span-4">
                <label>Jenis Kelamin
                </label>
                <select class="form-control sex required required" name="sex">
                    <option value="">Pilih Jenis Kelamin</option>
                    @foreach (\App\Models\Pasien::$enumJenisKelamin as $item)
                        <option {{ $data->sex == $item ? 'selected' : '' }} value="{{ $item }}">
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

                    <input type="text" readonly class="form-control date_of_birth" data-single-mode="true"
                        name="date_of_birth" value="{{ $data->date_of_birth }}">
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
                <label>Life Stage
                </label>
                <select class="form-control select2 life_stage required" name="life_stage">
                    <option value="">Pilih Life Stage</option>
                    @foreach (\App\Models\Pasien::$enumLifeStage as $i => $item)
                        <option {{ $item['title'] == $data->life_stage ? 'selected' : '' }}
                            value="{{ $item['title'] }}">
                            {{ $item['title'] }} | {{ $item['description'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-12 parent">
                <label for="">Ciri Khas</label>
                <textarea type="text" readonly class="form-control required" name="ciri_khas">{{ $data->ciri_khas }}</textarea>
            </div>
        </div>
    </div>
</div>
<div class="col-span-12 md:col-span-9 box p-8">
    <div class="alert alert-danger show mb-2" role="alert">SHIFT + MOUSE SCROLL untuk menscroll tab dibawah</div>
    <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center overflow-y-scroll disable-scrollbars"
        role="tablist">
        <li id="history-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center active w-full md:w-52 justify-center"
                data-tw-target="#history-data" aria-controls="history-data" aria-selected="true" role="tab">
                <i class="fa-solid fa-id-card-clip w-4 h-4 mr-2"></i>
                History Pemeriksaan
            </a>
        </li>
        <li id="kondisi-harian-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center text-center w-full md:w-48 justify-center"
                data-tw-target="#kondisi-harian-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-person-dots-from-line w-4 h-4 mr-2"></i>
                Kondisi Harian
            </a>
        </li>
        <li id="hasil-pemeriksaan-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center text-center w-full md:w-56 justify-center"
                data-tw-target="#hasil-pemeriksaan-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-user-doctor  w-4 h-4 mr-2"></i>
                Hasil Pemeriksaan
            </a>
        </li>
        <li id="diagnosa-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center text-center justify-center"
                data-tw-target="#diagnosa-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-person-dots-from-line w-4 h-4 mr-2"></i>
                Diagnosa
            </a>
        </li>
        {{-- <li id="treatment-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center" data-tw-target="#treatment-data"
                aria-selected="false" role="tab">
                <i class="fa-solid fa-hand-holding-medical w-4 h-4 mr-2"></i>
                Treatment
            </a>
        </li> --}}
        <li id="tindakan-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center justify-center"
                data-tw-target="#tindakan-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-thermometer w-4 h-4 mr-2"></i>
                Tindakan
            </a>
        </li>
        <li id="tindakan-bedah-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center justify-center  w-full md:w-52"
                data-tw-target="#tindakan-bedah-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-brain w-4 h-4 mr-2"></i>
                Tindakan Bedah
            </a>
        </li>
        <li id="hasil-lab-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center w-full md:w-40 justify-center"
                data-tw-target="#hasil-lab-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-flask w-4 h-4 mr-2"></i>
                Hasil Lab
            </a>
        </li>
        <li id="obat-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center justify-center" data-tw-target="#obat-data"
                aria-selected="false" role="tab">
                <i class="fa-solid fa-prescription-bottle-medical w-4 h-4 mr-2"></i>
                Obat
            </a>
        </li>
        <li id="catatan-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center justify-center"
                data-tw-target="#catatan-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-file-medical w-4 h-4 mr-2"></i>
                Catatan
            </a>
        </li>
        <li id="pakan-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4 flex items-center justify-center"
                data-tw-target="#pakan-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-bone  w-4 h-4 mr-2"></i>
                Pakan
            </a>
        </li>
        <li id="item-non-obat-data-tab" class="nav-item" role="presentation">
            <a href="javascript:;" class="nav-link py-4  w-full md:w-48 flex items-center justify-center"
                data-tw-target="#item-non-obat-data" aria-selected="false" role="tab">
                <i class="fa-solid fa-cart-shopping w-4 h-4 mr-2"></i>
                Item Non Obat
            </a>
        </li>
    </ul>
    <div class="tab-content mt-5 overflow-y-auto" style="max-height: 525px !important">
        <div id="history-data" class="tab-pane leading-relaxed active" role="tabpanel"
            aria-labelledby="example-1-tab">
            @if (count($rm->RekamMedisLogHistory) != 0)
                <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">
                    @foreach ($rm->RekamMedisLogHistory->sortByDesc('created_at') as $i => $item)
                        <li class="mb-10 ml-4">
                            <div
                                class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                            </div>
                            <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                                {{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}
                            </time>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                History {{ $i + 1 }}
                            </h3>
                            <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                                {!! $item->description !!}
                            </p>
                        </li>
                    @endforeach
                </ol>
            @else
            @endif
        </div>
        <div id="kondisi-harian-data" class="tab-pane leading-relaxed" role="tabpanel"
            aria-labelledby="example-2-tab">
            <table class="w-full table table-bordered">
                <thead>
                    <th>Tgl Jam</th>
                    <th>Suhu</th>
                    <th>Makan</th>
                    <th>Minum</th>
                    <th>Urin</th>
                    <th>Feses</th>
                    <th>Keterangan</th>
                </thead>
                <tbody>
                    @forelse ($rm->RekamMedisKondisiHarian->sortByDesc('created_at') as $item)
                        <tr>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->suhu }} C</td>
                            <td>{{ $item->makan }}</td>
                            <td>{{ $item->minum }}</td>
                            <td>{{ $item->urin }}</td>
                            <td>{{ $item->feses }}</td>
                            <td>{{ $item->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="hasil-pemeriksaan-data" class="tab-pane leading-relaxed" role="tabpanel"
            aria-labelledby="example-2-tab">
            <div class="w-full">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12">
                        <label for="" class="form-label font-bold">Tujuan</label>
                        <p>{{ $rm->pendaftaran->poli->name }}</p>
                    </div>
                    <div class="col-span-12 ">
                        <label class="form-label font-bold">Anamnesa Pendaftaran</label>
                        <table class="w-full">
                            @foreach ($rm->Pendaftaran->PendaftaranPasienAnamnesa->where('pasien_id', $rm->pasien_id) as $item)
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
                                        {{ $item->keterangan }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <hr class="my-3">
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <span class="form-label font-bold">Lain Lain</span>
                        <p>{{ $infoPasien->lain_lain }}</p>
                    </div>
                    <div class="col-span-12 md:col-span-6 parent">
                        <span class="form-label font-bold">Catatan</span>
                        <p>{{ $rm->pendaftaran->catatan }}</p>
                    </div>
                    <div class="col-span-6 parent">
                        <label for="" class="form-label font-bold">Berat</label>
                        <p>{{ $rm->berat }} Kg</p>
                    </div>
                    <div class="col-span-6  parent">
                        <label for="" class="form-label font-bold">Suhu</label>
                        <p> {{ $rm->suhu }} C</p>
                    </div>
                    <div class="col-span-12  md:col-span-6  parent">
                        <label class="form-label font-bold">Anamnesa</label>
                        <p>{{ $rm->anamnesa }}</p>
                    </div>
                    <div class="col-span-12  md:col-span-6  parent">
                        <label class="form-label font-bold">Hasil Pemeriksaan</label>
                        <p>{{ $rm->hasil_pemeriksaan }}</p>
                    </div>
                    <div class="col-span-12  parent">
                        <label class="form-label font-bold">Diagnosa</label>
                        <p>{{ $rm->diagnosa }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="diagnosa-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-2-tab">
            <table class="w-full table table-bordered">
                <thead>
                    <th>Diagnosa</th>
                    <th>Sumber</th>
                    <th>Tanggal</th>
                    <th>Yang Mendiagnosa</th>
                    <th>Opsi</th>
                </thead>
                <tbody>
                    {{-- <tr>
                        <td>{{ $rm->diagnosa }}</td>
                        <td><b>Pemeriksaan</b></td>
                        <td>{{ CarbonParse($rm->created_at, 'd M Y H:i') }}</td>
                        <td>{{ $rm->CreatedBy->name }}</td>
                        <td class="text-center">
                            <i class="fa-solid fa-stop"></i>
                        </td>
                    </tr> --}}
                    @forelse ($rm->RekamMedisDiagnosa->sortByDesc('created_at') as $item)
                        <tr>
                            <td>{{ $item->diagnosa }}</td>
                            <td><b>{{ $item->resource }}</b></td>
                            <td>{{ CarbonParse($item->created_at, 'd M Y H:i') }}</td>
                            <td>{{ $item->CreatedBy->name }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary"
                                    onclick="openModalEdit('#modal-tambah-diagnosa',`{{ $item->rekam_medis_pasien_id }}`,`{{ $item->id }}`,`{{ $item->diagnosa }}`)"><i
                                        class="fa-solid fa-edit"></i></button>
                                <button class="btn btn-warning"
                                    onclick="deleteData('diagnosa','{{ $item->rekam_medis_pasien_id }}','{{ $item->id }}')"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- <div id="treatment-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-3-tab">
            <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">
                @forelse ($rm->RekamMedisTreatment->sortByDesc('created_at') as $item)
                    <li class="mb-10 ml-4">
                        <div
                            class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                        </div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}
                        </time>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $item->treatment }}
                        </h3>
                        <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                            Oleh <b>{{ $item->CreatedBy->name }}</b>
                        </p>
                    </li>
                @endforeach
            </ol>
        </div> --}}
        <div id="tindakan-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-4-tab">
            <table class="w-full table table-bordered">
                <thead>
                    <th>Tindakan</th>
                    <th>Tanggal</th>
                    <th>Yang Membuat</th>
                    <th>Opsi</th>
                </thead>
                <tbody>
                    @forelse ($rm->RekamMedisTindakan->sortByDesc('created_at') as $item)
                        <tr>
                            <td>{{ $item->Tindakan->name }}</td>
                            <td>{{ CarbonParse($item->created_at, 'd M Y H:i') }}</td>
                            <td>{{ $item->CreatedBy->name }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary"
                                    onclick="openModalEdit('#modal-tambah-tindakan',`{{ $item->rekam_medis_pasien_id }}`,`{{ $item->id }}`,`{{ $item->tindakan_id }}`,`{{ $item->Tindakan->name }}`)"><i
                                        class="fa-solid fa-edit"></i></button>
                                <button class="btn btn-warning"
                                    onclick="deleteData('tindakan','{{ $item->rekam_medis_pasien_id }}','{{ $item->id }}')"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="4">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">
                @forelse ($rm->RekamMedisTindakan->sortByDesc('created_at') as $item)
                    <li class="mb-10 ml-4">
                        <div
                            class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                        </div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}
                        </time>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $item->Tindakan->name ?? '-' }}
                        </h3>
                        <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                            <b>{{ $item->treatment }}</b>
                        </p>
                        <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                            Oleh <b>{{ $item->CreatedBy->name }}</b>
                        </p>
                    </li>
                @endforeach
            </ol> --}}
        </div>
        <div id="tindakan-bedah-data" class="tab-pane leading-relaxed" role="tabpanel"
            aria-labelledby="example-2-tab">
            <table class="w-full table table-bordered">
                <thead align="center">
                    <th>Tindakan Bedah</th>
                    <th>Tanggal Bedah</th>
                    <th>Dokter Poli</th>
                    <th>Urgensi</th>
                    <th>Status bedah</th>
                    <th>Keterangan</th>
                    <th>Dokter bedah</th>
                    <th>Form Persetujuan</th>
                    <th>Opsi</th>
                </thead>
                <tbody>
                    @forelse ($rm->RekamMedisRekomendasiTindakanBedah->sortByDesc('created_at') as $item)
                        <tr>
                            <td>{{ $item->Tindakan->name }}</td>
                            <td>{{ CarbonParse($item->tanggal_rekomendasi_bedah, 'd-M-Y') }}</td>
                            <td>{{ $item->CreatedBy->name }}</td>
                            <td>
                                @php
                                    if ($item->status_urgensi == 'true') {
                                        echo '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium text-center">Urgent</div>';
                                    } else {
                                        echo '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium text-center"  style="background:blue">Normal</div>';
                                    }

                                    $item->tanggal_rekomendasi_bedah = CarbonParse($item->tanggal_rekomendasi_bedah, 'Y-m-d');

                                @endphp
                            </td>
                            <td>
                                @php
                                    if ($item->status == 'Done') {
                                        echo '<div style="background:rgb(0, 149, 255)" class="py-1 px-2 rounded-full text-xs text-white cursor-pointer font-medium text-center">Done By ' . $item->UpdatedBy->name . '</div>';
                                    } elseif ($item->status == 'Rejected') {
                                        echo '<div style="background:rgb(194, 128, 37)" class="py-1 px-2 rounded-full text-xs text-white cursor-pointer font-medium text-center">Cancel By ' . $item->UpdatedBy->name . '</div>';
                                    } else {
                                        echo '<div class="py-1 px-2 rounded-full text-xs bg-pending text-white cursor-pointer font-medium text-center" >Menunggu</div>';
                                    }
                                @endphp
                            </td>
                            <td>
                                {{ $item->keterangan }}
                            </td>
                            <td>
                                @if ($item->status == 'Done')
                                    {{ $item->UpdatedBy->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($item->upload_form_persetujuan == null)
                                    -
                                @else
                                    <button class="btn btn-primary"
                                        onclick="window.open('{{ url('/') }}/{{ $item->upload_form_persetujuan }}')"><i
                                            class="fas fa-download mr-2"></i>
                                        Download</button>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-primary"
                                    onclick="openModalEdit('#modal-rekomendasi-tindakan-bedah',`{{ $item->rekam_medis_pasien_id }}`,`{{ $item->id }}`,`{{ $item }}`,`{{ $item->Tindakan->name }}`)"><i
                                        class="fa-solid fa-edit"></i></button>
                                <button class="btn btn-warning"
                                    onclick="deleteData('rekomendasi_tindakan_bedah','{{ $item->rekam_medis_pasien_id }}','{{ $item->id }}')"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="9">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="hasil-lab-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-5-tab">
            <table class="w-full table table-bordered">
                <thead align="center">
                    <th>Nama File</th>
                    <th>Tanggal</th>
                    <th>Yang Membuat</th>
                    <th>Download</th>
                    <th>Opsi</th>
                </thead>
                <tbody>
                    @forelse ($rm->RekamMedisHasilLab->sortByDesc('created_at') as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ CarbonParse($item->created_at, 'd M Y H:i') }}</td>
                            <td>{{ $item->CreatedBy->name }}</td>
                            <td class="text-center">
                                <a href="javascript:;" onclick="window.open('{{ url('/') . '/' . $item->file }}')"
                                    class="inline-flex items-center py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-200 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                    <i class="fa-solid fa-file-arrow-down mr-2"></i>
                                    Download
                                </a>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning"
                                    onclick="deleteData('hasil lab','{{ $item->rekam_medis_pasien_id }}','{{ $item->id }}')"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="5">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">
                @forelse ($rm->RekamMedisHasilLab->sortByDesc('created_at') as $item)
                    <li class="mb-10 ml-4">
                        <div
                            class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                        </div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}
                        </time>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        </h3>
                        <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                            Oleh <b>{{ $item->CreatedBy->name }}</b>
                        </p>
                        <a href="javascript:;" onclick="window.open('{{ url('/') . '/' . $item->file }}')">
                            {{ $item->name }}
                        </a>
                        <a href="javascript:;" onclick="window.open('{{ url('/') . '/' . $item->file }}')"
                            class="inline-flex items-center py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-200 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            <i class="fa-solid fa-file-arrow-down mr-2"></i>
                            Download File
                        </a>
                    </li>
                @endforeach
            </ol> --}}
        </div>
        <div id="obat-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-6-tab">
            <table class="w-full table table-bordered">
                <thead>
                    <th>Nama Obat</th>
                    <th>Jenis Obat</th>
                    <th>Sediaan Obat</th>
                    <th>Signature</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Resep di buat oleh</th>
                    <th>Dibuat oleh</th>
                    <th>Opsi</th>
                </thead>
                <tbody>
                    @forelse ($rm->RekamMedisResep->sortByDesc('created_at')  as $item)
                        <tr>
                            <td class="align-middle">
                                @if ($item->jenis_obat == 'non-racikan')
                                    <b class="font-semibold">{{ $item->ProdukObat->name }}</b>
                                @else
                                    <b>{{ $item->KategoriObat ? $item->KategoriObat->name : '-' }}</b>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if ($item->jenis_obat == 'non-racikan')
                                    Non Racikan
                                @else
                                    Racikan
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if ($item->status_resep == 'Langsung')
                                    <span
                                        class="btn btn-ou
                                tline-primary inline-block mr-1 mb-2">Obat
                                        Rawat Inap</span>
                                @elseif ($item->status_resep == 'Bedah')
                                    <span class="btn btn-outline-primary inline-block mr-1 mb-2">Bedah</span>
                                @elseif ($item->status_resep == 'Kasir')
                                    <span class="btn btn-outline-primary inline-block mr-1 mb-2">Kasir</span>
                                @else
                                    <span class="btn btn-outline-secondary inline-block mr-1 mb-2">Obat
                                        Pulang</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ $item->description }}
                            </td>
                            <td class="align-middle">
                                @if ($item->jenis_obat == 'non-racikan')
                                    {{ romawi($item->qty) }} <b>{{ $item->ProdukObat->SatuanObat->name }}</b>
                                @else
                                    {{ romawi($item->qty > 1 ? $item->qty : 1) }}
                                    <b>Takaran</b>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if ($item->status_resep == 'Langsung' or $item->status_resep == 'Antrian')
                                    @if ($item->status_pembuatan_obat == 'Done')
                                        <span class="btn btn-outline-primary inline-block mr-1 mb-2">Selesai dibuat
                                            apotek</span>
                                    @else
                                        <span class="btn btn-outline-secondary inline-block mr-1 mb-2">Menunggu
                                            dibuat
                                            apotek</span>
                                    @endif
                                @endif
                                @if ($item->status_resep == 'Bedah')
                                    <span class="btn btn-outline-primary inline-block mr-1 mb-2">Obat Rawat Inap</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->CreatedBy->name }} pada tanggal
                                {{ CarbonParse($item->created_at, 'd-M-Y') }}
                            </td>
                            <td>
                                @if ($item->status_pembuatan_obat == 'Done')
                                    {{ $item->UpdatedBy->name }} pada tanggal
                                    {{ CarbonParse($item->updated_at, 'd/m/Y H:i') }}
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning"
                                    onclick="deleteData('resep','{{ $item->rekam_medis_pasien_id }}','{{ $item->id }}')"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="catatan-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-7-tab">
            <table class="w-full table table-bordered">
                <thead>
                    <th>Catatan</th>
                    <th>Sumber</th>
                    <th>Opsi</th>
                </thead>
                <tbody>
                    {{-- <tr>
                        <td>{{ $rm->catatan }}</td>
                        <td><b>{{ $item->resource }}</b></td>
                        <td>{{ CarbonParse($rm->created_at, 'd M Y H:i') }}</td>
                        <td>{{ $rm->CreatedBy->name }}</td>
                        <td class="text-center">
                            <i class="fa-solid fa-stop"></i>
                        </td>
                    </tr> --}}
                    @forelse ($rm->RekamMedisCatatan->sortByDesc('created_at') as $item)
                        <tr>
                            <td>{{ $item->catatan }}</td>
                            <td><b>{{ $item->resource }}</b></td>
                            <td class="text-center">
                                <button class="btn btn-primary"
                                    onclick="openModalEdit('#modal-tambah-catatan',`{{ $item->rekam_medis_pasien_id }}`,`{{ $item->id }}`,`{{ $item->catatan }}`)"><i
                                        class="fa-solid fa-edit"></i></button>
                                <button class="btn btn-warning"
                                    onclick="deleteData('catatan','{{ $item->rekam_medis_pasien_id }}','{{ $item->id }}')"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">
                <li class="mb-10 ml-4">
                    <div
                        class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                    </div>
                    <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                        Catatan Dari Pemeriksaan
                    </time>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $rm->catatan }}
                    </h3>
                    <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                        Oleh <b>{{ $rm->CreatedBy->name }}</b>
                    </p>
                </li>
                @forelse ($rm->RekamMedisCatatan->sortByDesc('created_at') as $item)
                    <li class="mb-10 ml-4">
                        <div
                            class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                        </div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}
                        </time>
                        <h3 class="text-lg text-gray-900 dark:text-white">
                            {{ $item->catatan }}
                        </h3>
                        <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                            Oleh <b>{{ $item->CreatedBy->name }}</b>
                        </p>
                    </li>
                @endforeach
            </ol> --}}
        </div>
        <div id="pakan-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-8-tab">
            <table class="w-full table table-bordered">
                <thead>
                    <th>Nama Pakan</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Pembuat</th>
                    <th>Opsi</th>
                </thead>
                <tbody>
                    @forelse ($rm->RekamMedisPakan->sortByDesc('created_at') as $item)
                        <tr>
                            <td>{{ $item->ItemNonObat->name }}</td>
                            <td>{{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ $item->CreatedBy->name }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning"
                                    onclick="deleteData('pakan','{{ $item->rekam_medis_pasien_id }}','{{ $item->id }}')"><i
                                        class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="item-non-obat-data" class="tab-pane leading-relaxed" role="tabpanel"
            aria-labelledby="example-8-tab">
            <table class="w-full table table-bordered">
                <thead>
                    <th>Nama Item</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Pembuat</th>
                </thead>
                <tbody>
                    @forelse ($rm->RekamMedisNonObat->sortByDesc('created_at') as $item)
                        <tr>
                            <td>{{ $item->ItemNonObat->name }}</td>
                            <td>{{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ $item->CreatedBy->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-span-12 md:col-span-3">
    <div class="box">
        <div class="flex items-center col-span-12 border-b border-slate-200/60 dark:border-darkmode-400 px-3 py-2 text-left bg-warning rounded-t-lg"
            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(220, 18, 176)">
            <a href="javascript:;" class="font-medium text-white  w-full text-xl">Menu Rekam Medis</a>
        </div>
        <div class="list-group  p-8">
            <button type="button" class="list-group-item list-group-item-action"
                onclick="openModalData('#modal-tambah-diagnosa')">
                Tambah Diagnosa
            </button>
            <button type="button" class="list-group-item list-group-item-action"
                onclick="openModalData('#modal-tambah-hasil-lab')">
                Tambah Hasil Lab
            </button>
            <button type="button" class="list-group-item list-group-item-action"
                onclick="openModalData('#modal-tambah-tindakan')">
                Tambah Tindakan
            </button>
            <button type="button" class="list-group-item list-group-item-action"
                onclick="openModalData('#modal-tambah-catatan')">
                Tambah Catatan
            </button>
            <button type="button" class="list-group-item list-group-item-action"
                onclick="openModalData('#modal-rekomendasi-tindakan-bedah')">
                Rekomendasi Tindakan Bedah
            </button>
            <button type="button" class="list-group-item list-group-item-action"
                onclick="openModalData('#modal-tambah-resep')">
                Tambah Obat
            </button>
            <button type="button" class="list-group-item list-group-item-action"
                onclick="openModalData('#modal-tambah-pakan')">
                Tambah Pakan
            </button>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<script>
    var rekamMedisPasienId;
    var idRekamMedis;
    var paramModal;
    (function() {
        $('.no-rekam-medis').html('{{ $rm->kode }}')
        $('.sex').select2({
            width: '100%',
            dropdownParent: $('#modal-rekam-medis .modal-body .modal-parent')
        })
        $('.additional').addClass('hidden')

        $(".datepicker").each(function() {
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
                dropdownParent: $("#modal-rekam-medis .modal-body .modal-parent"),
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
                dropdownParent: $("#modal-rekam-medis .modal-body .modal-parent"),
                width: '100%',
                ajax: {
                    url: "{{ route('select2Pasien') }}?param=ras_id",
                    dataType: 'json',
                    data: function(params) {
                        return {
                            q: params.term,
                            binatang_id() {
                                return $(par).find('.binatang_id').val();
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



        @if ($data->Binatang)
            var newOption = new Option('{{ $data->Binatang->name }}',
                '{{ $data->Binatang->id }}',
                true,
                true
            );
            $('.binatang_id').append(newOption).trigger('change.select2');
        @endif


        @if ($data->Ras)
            var newOption = new Option('{{ $data->Ras->name }}',
                '{{ $data->ras->id }}',
                true,
                true
            );

            $('.ras_id').append(newOption).trigger('change.select2');
        @endif


        generateAge();
    })()

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

    function hapusResep(child) {
        $(child).parents('.parent-resep').remove();
    }

    function hapusRacikanChild(child) {
        $(child).parents('.parent-child-racikan').remove();
    }

    function openModalEdit(param, rekam_medis_pasien_id, id, value, valueTambahan = null) {
        openModalData(param);
        paramModal = param;
        rekamMedisPasienId = rekam_medis_pasien_id;
        idRekamMedis = id;
        switch (param) {
            case '#modal-tambah-diagnosa':
                $('#diagnosa').val(value);
                break;
            case '#modal-tambah-tindakan':
                var newOption = new Option(valueTambahan,
                    value,
                    true,
                    true
                );
                $('#tindakan_id').append(newOption).trigger('change.select2');
                break;
            case '#modal-rekomendasi-tindakan-bedah':
                value = JSON.parse(value);
                var newOption = new Option(value.tindakan.name,
                    value.tindakan.id,
                    true,
                    true
                );
                $('#rekomendasi_tindakan_bedah').append(newOption).trigger('change.select2');
                var date1 = new Date(value.tanggal_rekomendasi_bedah);
                const format2 = "YYYY-MM-DD"
                var dateTime1 = moment(date1).format(format2);
                console.log(dateTime1)
                $('#rekomendasi_tanggal_bedah').val(dateTime1)
                $('#keterangan_rekomendasi_tindakan_bedah').val(value.keterangan)
                break;

            case '#modal-tambah-catatan':
                $('#catatan').val(value);
                break;
            case '#modal-tambah-pakan':
                var newOption = new Option(valueTambahan,
                    value,
                    true,
                    true
                );
                $('#pakan').append(newOption).trigger('change.select2');
                break;
            default:
                break;
        }
    }
</script>
