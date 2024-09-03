<div class="col-span-4">
    <div class="flex flex-col">
        <div class="col-span-12">
            <h2 class="text-lg font-medium truncate mr-5">History</h2>
        </div>
        <hr class="mb-3">
        @foreach ($rekamMedis->sortByDesc('created_at') as $item)
            <div class="col-span-12 md:col-span-3 mb-3">
                <div class="box intro-y p-3">
                    Kode Pemeriksaan
                    <span></span>
                    <h5 class="box-title text-xl font-extrabold mt-2">
                        {{ $item->kode }}
                    </h5>
                    <p class="mb-2"></p>
                    <div class="w-full text-right">
                        <button class="btn btn-primary shadow-md mr-2"
                            onclick="window.open('{{ route('printPemeriksaanPasien', ['id' => Crypt::encrypt($item->id)]) }}')"><i
                                class="fas fa-print mr-2"></i> Print Persetujuan Tindakan
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<div class="col-span-8 grid grid-cols-12 gap-4 gap-y-3">
    <div class="col-span-12">
        <div class="intro-y box pb-2">
            <div class="flex items-center  border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-left bg-primary rounded-t-lg"
                style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
                <a href="javascript:;" class="font-medium text-white w-full text-xl">DATA PASIEN</a>
            </div>
            <div class="flex flex-col lg:flex-row border-slate-200/60 dark:border-darkmode-400 pb-5 px-5 -mx-5">
                <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                        <img alt="Amore Animal Clinic" class="rounded-full"
                            src="{{ $data->image ? route('dashboard') . '/' . $data->image : asset('dist/images/amore.png') }}">
                    </div>
                    <div class="ml-5">
                        <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">
                            {{ $data->name }}
                        </div>
                        <div class="text-slate-500">{{ $data->Binatang->name }}</div>
                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">Informasi Hewan</div>
                    <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <i class="fa-solid fa-paw mr-2 text-amber-400"></i>
                            <b>Ciri Khas</b>&nbsp;|&nbsp;{{ $data->ciri_khas }}
                        </div>
                        <div class="truncate sm:whitespace-normal flex items-center mt-3">
                            <i class="fa-solid fa-user mr-2 text-green-500"></i>
                            <b>Owner</b>&nbsp;|&nbsp;{{ $data->Owner->name }}
                        </div>
                        <div class="truncate sm:whitespace-normal flex items-center mt-3">
                            <i class="fa-solid fa-cake-candles mr-2 text-red-300"></i>
                            <b>Umur</b>&nbsp;|&nbsp;{{ Carbon\Carbon::parse($data->date_of_birth)->diff(Carbon\Carbon::now())->format('%y thn %m bln %d hri') }}
                        </div>
                        <div class="truncate sm:whitespace-normal flex items-center mt-3">
                            <i class="fa-solid fa-weight-scale text-blue-300 mr-2"></i>
                            <b>Berat</b>&nbsp;|&nbsp;{{ $data->berat }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-span-12 box p-8">
        <div class="alert alert-danger show mb-2" role="alert">SHIFT + MOUSE SCROLL untuk menscroll tab dibawah</div>
        <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center overflow-y-scroll disable-scrollbars"
            role="tablist">
            <li id="history-data-tab" class="nav-item" role="presentation">
                <a href="javascript:;" class="nav-link py-4 flex items-center active w-52 justify-center"
                    data-tw-target="#history-data" aria-controls="history-data" aria-selected="true" role="tab">
                    <i class="fa-solid fa-id-card-clip w-4 h-4 mr-2"></i>
                    History Pemeriksaan
                </a>
            </li>
            <li id="kondisi-harian-data-tab" class="nav-item" role="presentation">
                <a href="javascript:;" class="nav-link py-4 flex items-center text-center w-48 justify-center"
                    data-tw-target="#kondisi-harian-data" aria-selected="false" role="tab">
                    <i class="fa-solid fa-person-dots-from-line w-4 h-4 mr-2"></i>
                    Kondisi Harian
                </a>
            </li>
            <li id="hasil-pemeriksaan-data-tab" class="nav-item" role="presentation">
                <a href="javascript:;" class="nav-link py-4 flex items-center text-center w-56 justify-center"
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
                <a href="javascript:;" class="nav-link py-4 flex items-center justify-center  w-52"
                    data-tw-target="#tindakan-bedah-data" aria-selected="false" role="tab">
                    <i class="fa-solid fa-brain w-4 h-4 mr-2"></i>
                    Tindakan Bedah
                </a>
            </li>
            <li id="hasil-lab-data-tab" class="nav-item" role="presentation">
                <a href="javascript:;" class="nav-link py-4 flex items-center w-40 justify-center"
                    data-tw-target="#hasil-lab-data" aria-selected="false" role="tab">
                    <i class="fa-solid fa-flask w-4 h-4 mr-2"></i>
                    Hasil Lab
                </a>
            </li>
            <li id="obat-data-tab" class="nav-item" role="presentation">
                <a href="javascript:;" class="nav-link py-4 flex items-center justify-center"
                    data-tw-target="#obat-data" aria-selected="false" role="tab">
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
                <a href="javascript:;" class="nav-link py-4  w-48 flex items-center justify-center"
                    data-tw-target="#item-non-obat-data" aria-selected="false" role="tab">
                    <i class="fa-solid fa-cart-shopping w-4 h-4 mr-2"></i>
                    Item Non Obat
                </a>
            </li>
        </ul>
        <div class="tab-content mt-5 overflow-y-auto" style="max-height: 525px !important">
            <div id="history-data" class="tab-pane leading-relaxed active" role="tabpanel"
                aria-labelledby="example-1-tab">
                <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">
                    @php
                        $index = 1;
                    @endphp
                    @foreach ($data->RekamMedisLogHistory->sortByDesc('created_at') as $i => $item)
                        <li class="mb-10 ml-4">
                            <div
                                class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                            </div>
                            <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                                {{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}
                            </time>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                History {{ $index }}
                            </h3>
                            <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                                {!! $item->description !!}
                            </p>
                        </li>
                        @php
                            $index++;
                        @endphp
                    @endforeach
                </ol>
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
                        @foreach ($rekamMedis->sortByDesc('created_at') as $rm)
                            @foreach ($rm->RekamMedisKondisiHarian->sortByDesc('created_at') as $item)
                                <tr>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->suhu }} C</td>
                                    <td>{{ $item->makan }}</td>
                                    <td>{{ $item->minum }}</td>
                                    <td>{{ $item->urin }}</td>
                                    <td>{{ $item->feses }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="hasil-pemeriksaan-data" class="tab-pane leading-relaxed" role="tabpanel"
                aria-labelledby="example-2-tab">
                <div class="w-full">
                    @foreach ($rekamMedis->sortByDesc('created_at') as $rm)
                        <div class="grid grid-cols-12 gap-2 border rounded p-8 mb-2">
                            <div class="col-span-6">
                                <label for="" class="font-bold">Kode Pemeriksaan</label>
                                <p>{{ $rm->kode }}</p>
                            </div>
                            <div class="col-span-6">
                                <label for="" class="font-bold">Tujuan</label>
                                <p>{{ $rm->pendaftaran->poli->name }}</p>
                            </div>
                            <div class="col-span-12 ">
                                <label class="font-bold">Anamnesa Pendaftaran</label>
                                <table class="w-full">
                                    @foreach ($rm->pasien->PendaftaranPasienAnamnesa->where('pendaftaran_id', $rm->pendaftaran_id)->sortByDesc('created_at') as $item)
                                        @if ($item->anamnesa)
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
                                                        placeholder="Masukan keterangan"
                                                        value="{{ $item->keterangan }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                                <hr class="my-3">
                            </div>
                            <div class="col-span-12 md:col-span-6 parent">
                                <span class="font-bold">Lain Lain</span>
                                <p>{{ infoPasien($rm)->lain_lain }}</p>
                            </div>
                            <div class="col-span-12 md:col-span-6 parent">
                                <span class="font-bold">Catatan</span>
                                <p>{{ $rm->pendaftaran->catatan }}</p>
                            </div>
                            <div class="col-span-6 parent">
                                <label for="" class="font-bold">Berat</label>
                                <p>{{ $rm->berat }} Kg</p>
                            </div>
                            <div class="col-span-6  parent">
                                <label for="" class="font-bold">Suhu</label>
                                <p> {{ $rm->suhu }} C</p>
                            </div>
                            <div class="col-span-12  md:col-span-6  parent">
                                <label class="font-bold">Anamnesa</label>
                                <p>{{ $rm->anamnesa }}</p>
                            </div>
                            <div class="col-span-12  md:col-span-6  parent">
                                <label class="font-bold">Hasil Pemeriksaan</label>
                                <p>{{ $rm->hasil_pemeriksaan }}</p>
                            </div>
                            <div class="col-span-12  parent">
                                <label class="font-bold">Diagnosa</label>
                                <p>{{ $rm->diagnosa }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div id="diagnosa-data" class="tab-pane leading-relaxed" role="tabpanel"
                aria-labelledby="example-2-tab">
                <table class="w-full table table-bordered">
                    <thead>
                        <th>Diagnosa</th>
                        <th>Sumber</th>
                        <th>Tanggal</th>
                        <th>Yang Mendiagnosa</th>
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
                        @foreach ($rekamMedis->sortByDesc('created_at') as $rm)
                            @forelse ($rm->RekamMedisDiagnosa->sortByDesc('created_at') as $item)
                                <tr>
                                    <td>{{ $item->diagnosa }}</td>
                                    <td><b>{{ $item->resource }}</b></td>
                                    <td>{{ CarbonParse($item->created_at, 'd M Y H:i') }}</td>
                                    <td>{{ $item->CreatedBy->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        @endforeach
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
            <div id="tindakan-data" class="tab-pane leading-relaxed" role="tabpanel"
                aria-labelledby="example-4-tab">
                <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">
                    @forelse ($rekamMedis->sortByDesc('created_at') as $rm)
                        @foreach ($rm->RekamMedisTindakan->sortByDesc('created_at') as $item)
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
                    @endforeach
                </ol>
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
                    </thead>
                    <tbody>
                        @foreach ($rekamMedis->sortByDesc('created_at') as $rm)
                            @foreach ($rm->RekamMedisRekomendasiTindakanBedah->sortByDesc('created_at') as $item)
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
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            if ($item->status == 'Done') {
                                                echo '<div style="background:rgb(0, 149, 255)" class="py-1 px-2 rounded-full text-xs w-32 text-white cursor-pointer font-medium text-center">Done By ' . $item->UpdatedBy->name . '</div>';
                                            } elseif ($item->status == 'Rejected') {
                                                echo '<div style="background:rgb(194, 128, 37)" class="py-1 px-2 rounded-full text-xs w-32 text-white cursor-pointer font-medium text-center">Cancel By ' . $item->UpdatedBy->name . '</div>';
                                            } else {
                                                echo '<div class="py-1 px-2 rounded-full text-xs bg-pending text-white cursor-pointer w-32 font-medium text-center" >Menunggu</div>';
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
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="hasil-lab-data" class="tab-pane leading-relaxed" role="tabpanel"
                aria-labelledby="example-5-tab">
                <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">
                    @foreach ($rekamMedis->sortByDesc('created_at') as $rm)
                        @foreach ($rm->RekamMedisHasilLab->sortByDesc('created_at') as $item)
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
                    @endforeach
                </ol>
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
                    </thead>
                    <tbody>
                        @forelse ($rekamMedis->sortByDesc('created_at') as $rm)
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
                                            <span class="btn btn-outline-primary inline-block mr-1 mb-2">Langsung
                                                Digunakan</span>
                                        @elseif ($item->status_resep == 'Bedah')
                                            <span class="btn btn-outline-primary inline-block mr-1 mb-2">Bedah</span>
                                        @elseif ($item->status_resep == 'Kasir')
                                            <span class="btn btn-outline-primary inline-block mr-1 mb-2">Kasir</span>
                                        @else
                                            <span class="btn btn-outline-secondary inline-block mr-1 mb-2">Dibawa
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
                                                <span class="btn btn-outline-primary inline-block mr-1 mb-2">Selesai
                                                    dibuat
                                                    apotek</span>
                                            @else
                                                <span class="btn btn-outline-secondary inline-block mr-1 mb-2">Menunggu
                                                    dibuat
                                                    apotek</span>
                                            @endif
                                        @endif
                                        @if ($item->status_resep == 'Bedah')
                                            <span class="btn btn-outline-primary inline-block mr-1 mb-2">Langsung
                                                Digunakan</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->CreatedBy->name }} pada tanggal
                                        {{ CarbonParse($item->created_at, 'd/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @if ($item->status_pembuatan_obat == 'Done')
                                            {{ $item->UpdatedBy->name }} pada tanggal
                                            {{ CarbonParse($item->updated_at, 'd/m/Y H:i') }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="catatan-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-7-tab">
                <ol class="relative border-l border-gray-200 dark:border-gray-700 mb-3">

                    @foreach ($rekamMedis->sortByDesc('created_at') as $rm)
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
                        @foreach ($rm->RekamMedisCatatan->sortByDesc('created_at') as $item)
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
                    @endforeach
                </ol>
            </div>
            <div id="pakan-data" class="tab-pane leading-relaxed" role="tabpanel" aria-labelledby="example-8-tab">
                <table class="w-full table table-bordered">
                    <thead>
                        <th>Nama Pakan</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Pembuat</th>
                    </thead>
                    <tbody>
                        @forelse ($rekamMedis->sortByDesc('created_at') as $rm)
                            @forelse ($rm->RekamMedisPakan->sortByDesc('created_at') as $item)
                                <tr>
                                    <td>{{ $item->ItemNonObat->name }}</td>
                                    <td>{{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                        {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}</td>
                                    <td>{{ $item->jumlah }}</td>
                                    <td>{{ $item->CreatedBy->name }}</td>
                                </tr>
                            @empty
                            @endforelse
                        @empty
                        @endforelse
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
                        @forelse ($rekamMedis->sortByDesc('created_at') as $rm)
                            @forelse ($rm->RekamMedisNonObat->sortByDesc('created_at') as $item)
                                <tr>
                                    <td>{{ $item->ItemNonObat->name }}</td>
                                    <td>{{ carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                        {{ carbon\Carbon::parse($item->created_at)->format('H:i') }}</td>
                                    <td>{{ $item->jumlah }}</td>
                                    <td>{{ $item->CreatedBy->name }}</td>
                                </tr>
                            @empty
                            @endforelse
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
