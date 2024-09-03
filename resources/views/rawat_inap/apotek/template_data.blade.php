<div class="col-span-12">
    <div class="intro-y box pb-2">
        <div class="border-b border-slate-200/60 dark:border-darkmode-400 px-5 py-4 text-left bg-primary rounded-t-lg"
            style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important">
            <div class="col-span-12 flex justify-between align-middle">
                <a href="javascript:;" class="font-medium text-white text-xl">DATA PASIEN</a>
                {{-- @if (Auth::user()->role_id == 7 || Auth::user()->role_id == 1) --}}
                @if ($rm->status_apoteker == 'waiting' || $rm->status_apoteker == 'revisi')
                    <button type="button" class="btn btn-warning btn-xs ml-auto" onclick="statusApoteker()">Proses
                        Resep</button>
                @endif
            </div>
        </div>
        <div class="flex flex-col lg:flex-row border-slate-200/60 dark:border-darkmode-400 pb-5 px-5 -mx-5">
            <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                    <img alt="Amore Animal Clinic" class="rounded-full"
                        src="{{ $rm->Pasien->image ? route('dashboard') . '/' . $rm->Pasien->image : asset('dist/images/amore.png') }}">
                </div>
                <div class="ml-5">
                    <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">
                        {{ $rm->Pasien->name }}
                        <input type="hidden" class="rekam_medis_pasien_id" id="rekam_medis_pasien_id"
                            name="rekam_medis_pasien_id" value="{{ $rm->id }}">
                    </div>
                    <div class="text-slate-500">{{ $rm->Pasien->Binatang->name }}</div>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3">Informasi Pasien</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <i class="fa-solid fa-user mr-2 text-green-500"></i>
                        <b>Nama Owner</b>&nbsp;|&nbsp;{{ $rm->Pasien->Owner->name }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i class="fa-solid fa-paw mr-2 text-amber-400"></i>
                        <b>Ciri Khas</b>&nbsp;|&nbsp;{{ $rm->Pasien->ciri_khas }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i class="fa-solid fa-cake-candles mr-2 text-red-300"></i>
                        <b>Umur</b>&nbsp;|&nbsp;{{ Carbon\Carbon::parse($rm->Pasien->date_of_birth)->diff(Carbon\Carbon::now())->format('%y Tahun %m Bulan %d Hari') }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i class="fa-solid fa-weight-scale text-blue-300 mr-2"></i>
                        <b>Berat</b>&nbsp;|&nbsp;{{ $rm->Pasien->berat }} Kg
                    </div>
                    @forelse ($dokter as $item)
                        <div class="truncate sm:whitespace-normal flex items-center mt-3">
                            <i class="fa-solid fa-user-md text-red-300 mr-2"></i>
                            <b>Dokter</b>&nbsp;|&nbsp;{{ $item->name }}
                        </div>
                    @endforeach
                    @if ($rm->rawat_inap == true and $req->status_pemeriksaan != 'Langsung')
                        @if (Auth::user()->role->name == 'Admin Webs' or Auth::user()->role->name == 'Superuser')
                            <button type="button" class="btn btn-primary mt-3" onclick="backToRanap()">Kembalikan Ke
                                Ranap</button>
                        @endif
                    @endif
                </div>
            </div>

        </div>
        @if ($rm->desc_kasir)
            <div class="description">
                <div class="p-5">
                    <b class="align-middle">Deskripsi Pengembalian Obat</b>
                    <p class="text-justify">
                        {{ $rm->desc_kasir }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
<div class="col-span-12">
    <div class="intro-y box pb-2">
        <div class="grid grid-cols-12 gap-6 col-span-12 clearfix  p-5">
            <div class="header col-span-12 flex justify-between align-middle">
                <b class="align-middle">RESEP SUDAH DIBUAT</b>
            </div>
            <ul class="col-span-12">
                <li class="list-group-item text-center must-hidden w-full">
                    <div>
                        Belum ada data obat
                    </div>
                </li>
            </ul>
        </div>
        <div class="grid grid-cols-12 gap-6 col-span-12 clearfix  p-5">
            <div class="col-span-12 flex justify-between align-middle">

            </div>
        </div>
    </div>
</div>
<div class="col-span-12">
    <div class="intro-y box pb-2">
        <div class="grid grid-cols-12 gap-6 col-span-12 clearfix  p-5" id="append-resep">
            <div class="header col-span-12 flex justify-between align-middle">
                <b class="align-middle">RESEP OBAT</b>
                    @if ($req->edit == 'true')
                        @if ($rm->status_apoteker == 'progress')
                            <button type="button" class="btn btn-primary btn-xs ml-auto" onclick="appendResep()"
                                id="add-resep"><i class="fa fa-plus"></i>
                                Tambah Resep</button>
                        @endif
                    @endif
            </div>

            @forelse ($rm->RekamMedisResep->where('status_pembuatan_obat','Undone')->sortByDesc('created_at')  as $i => $d)
                <div class="col-span-12 mb-3 parent-resep border rounded p-2">
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-12">
                            <a data-name="racikan"
                                class="select-racikan racikan-button {{ $d->jenis_obat == 'racikan' ? 'active' : '' }}"
                                href="javascript:;">Racikan</a> |
                            <a data-name="non-racikan"
                                class="select-racikan non-racikan-button {{ $d->jenis_obat != 'racikan' ? 'active' : '' }}"
                                href="javascript:;">Non Racikan</a>
                            <input type="hidden" name="parent_resep[]" class="parent_resep"
                                value="{{ $d->jenis_obat }}">
                            <input type="hidden" name="index_racikan[]" class="index_racikan"
                                value="{{ $i + 1 }}">
                            <input type="hidden" name="id_detail[]" class="id_detail" value="{{ $d->id }}">
                            <input type="hidden" name="created_by[]" value="{{ $d->created_by }}">
                            <input type="hidden" name="created_at[]" value="{{ $d->created_at }}">
                        </div>
                        <div
                            class="col-span-12 racikan-child racikan {{ $d->jenis_obat == 'racikan' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-12 md:col-span-6 parent">
                                    <label class="form-label">Jenis Obat {{ dot() }}</label>
                                    <select name="jenis_obat_racikan[]"
                                        class="jenis_obat_racikan form-control required select2resep">
                                        <option value="">Pilih Jenis Obat</option>
                                        @foreach (\App\Models\KategoriObat::where('status', true)->get() as $item)
                                            <option {{ $d->kategori_obat_id == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-4 md:col-span-2 parent">
                                    <label class="form-label">Satuan {{ dot() }}</label>
                                    <select name="satuan_racikan[]"
                                        class="satuan_racikan form-control required select2filter">
                                        <option value="">Pilih Satuan</option>
                                        @foreach (\App\Models\SatuanObat::where('status', true)->get() as $item)
                                            <option {{ $d->satuan_obat_id == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-4 md:col-span-2 parent">
                                    <label for="" class="form-label">Jumlah Obat {{ dot() }}</label>
                                    <input type="number"
                                        class="form-control w-100 text-right mask-non-decimal required qty_racikan"
                                        name="qty_racikan[]" value="{{ $d->qty }}">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <label class="form-label block">&nbsp;</label>
                                    @if ($req->edit == 'true')
                                        <button type="button" class="btn btn-danger" onclick="hapusResep(this)"><i
                                                class="fa fa-trash w-100"></i></button>
                                    @endif
                                </div>
                                <div class="col-span-12 text-info">
                                    @if ($req->edit == 'true')
                                        <a href="javascript:;" onclick="tambahChildRacikan(this)"><i
                                                class="fa fa-plus" aria-hidden="true"></i>
                                            Tambah
                                            Racikan</a>
                                    @endif
                                </div>
                                <div class="col-span-12">
                                    <div class="append-racikan pl-5">
                                        @foreach ($d->rekamMedisResepRacikan as $d1)
                                            <div class="col-span-12 parent-child-racikan">
                                                <div class="grid grid-cols-12 gap-2">
                                                    <div class="col-span-12 md:col-span-4 mb-2 parent">
                                                        <label for="">Produk Obat {{ dot() }}</label>
                                                        <select name="racikan_produk_obat_{{ $i + 1 }}[]"
                                                            onchange="racikanObat(this)"
                                                            class="racikan_produk_obat form-control required select2resep">
                                                            <option value="">Pilih Jenis Obat</option>
                                                            @foreach ($produkObat as $item)
                                                                <option
                                                                    data-qty="{{ $item->StockFirst != null ? $item->StockFirst->qty : 0 }}"
                                                                    {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                                                                    {{ $d1->produk_obat_id == $item->id ? 'selected' : '' }}
                                                                    value="{{ $item->id }}">
                                                                    {{ $item->name }}
                                                                    {{-- {{ $item->dosis }} mg --}}
                                                                    {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stok Kosong)' : '') : '(Stok Kosong)' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @if ($req->edit == 'true')
                                                        <div class="col-span-4 md:col-span-2 mb-2 parent">
                                                            <label for="">Stok Obat</label>
                                                            <input type="number" readonly
                                                                class="form-control harga w-100 mask text-right required racikan_sisa_qty"
                                                                value="{{ count($d1->ProdukObat->Stock) != 0 ? $d1->ProdukObat->Stock->where('branch_id', $rm->Pendaftaran->branch_id)->first()->qty : 0 }}"
                                                                name="racikan_sisa_qty_{{ $i + 1 }}[]">
                                                        </div>
                                                    @endif
                                                    <div class="col-span-4 md:col-span-2 mb-2 parent">
                                                        <label for="">Jumlah Obat {{ dot() }}</label>
                                                        <input type="number"
                                                            class="form-control harga w-100 mask-non-decimal text-right required racikan_qty"
                                                            value="{{ $d1->qty }}"
                                                            name="racikan_qty_{{ $i + 1 }}[]"
                                                            {{ $req->edit == 'false' ? 'readonly' : '' }}>
                                                    </div>
                                                    @if ($req->edit == 'true')
                                                        <div class="col-span-4 md:col-span-2 mb-2">
                                                            <label class="block">&nbsp;</label>
                                                            <a type="button" onclick="hapusRacikanChild(this)"><i
                                                                    class="fa fa-trash w-full cursor-pointer text-danger"></i></a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-span-12 parent">
                                    <label class="form-label">Signature (Keterangan) {{ dot() }}</label>
                                    <textarea name="description_racikan[]" class="form-control description_racikan required" cols="2"
                                        rows="2">{{ $d->description }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-span-12 racikan-child non-racikan {{ $d->jenis_obat == 'non-racikan' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-4 md:col-span-6 mb-2 parent">
                                    <label for="" class="form-label">Produk Obat {{ dot() }}</label>
                                    <select name="produk_obat_non_racikan[]" onchange="nonRacikanObat(this)"
                                        class="produk_obat_non_racikan form-control required select2resep">
                                        <option value="">Pilih Jenis Obat</option>
                                        @foreach ($produkObat as $item)
                                            <option
                                                data-qty="{{ $item->StockFirst != null ? $item->StockFirst->qty : 0 }}"
                                                {{ $d->produk_obat_id == $item->id ? 'selected' : '' }}
                                                {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                                                value="{{ $item->id }}">{{ $item->name }}
                                                {{-- {{ $item->dosis }} mg --}}
                                                {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stok Kosong)' : '') : '(Stok Kosong)' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($req->edit == 'true')
                                    <div class="col-span-4 md:col-span-2 mb-2 parent">
                                        <label for="" class="form-label">Stok Obat</label>
                                        @if ($d->produkObat)
                                            <input type="number"
                                                class="form-control w-100 text-right required sisa_qty_non_racikan"
                                                readonly
                                                value="{{ $d->ProdukObat->StockFirst != null ? $d->ProdukObat->StockFirst->qty : 0 }}"
                                                name="sisa_qty_non_racikan[]">
                                        @else
                                            <input type="number"
                                                class="form-control w-100 text-right required sisa_qty_non_racikan"
                                                readonly value="" name="sisa_qty_non_racikan[]">
                                        @endif

                                    </div>
                                @endif
                                <div class="col-span-4 md:col-span-2 mb-2 parent">
                                    <label for="" class="form-label">Jumlah Obat {{ dot() }}</label>
                                    <input type="number"
                                        class="form-control w-100 text-right required qty_non_racikan"
                                        name="qty_non_racikan[]" value="{{ $d->qty }}"
                                        {{ $req->edit == 'false' ? 'readonly' : '' }}>
                                </div>

                                <div class="col-span-4 md:col-span-2 mb-2">
                                    <label class="block form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger" onclick="hapusResep(this)"><i
                                            class="fa fa-trash w-100"></i></button>
                                </div>
                                <div class="col-span-12 mb-2 parent">
                                    <label class="form-label">Signature (Keterangan) {{ dot() }}</label>
                                    <textarea name="description_non_racikan[]" class="form-control description_non_racikan required" cols="2"
                                        rows="2" {{ $req->edit == 'false' ? 'readonly' : '' }}>{{ $d->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <ul class="col-span-12">
                    <li class="list-group-item text-center must-hidden w-full">
                        <div>
                            Belum ada data obat non racikan
                        </div>
                    </li>
                </ul>
            @endforelse

            @forelse ($rm->RekamMedisResep->where('status_pembuatan_obat','Done')->where('status_resep','Antrian')->sortByDesc('created_at')  as $i => $d)
                <div class="col-span-12 mb-3 parent-resep border rounded p-2">
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-12 disabled">
                            <a data-name="racikan"
                                class="select-racikan racikan-button {{ $d->jenis_obat == 'racikan' ? 'active' : '' }}"
                                href="javascript:;">Racikan</a> |
                            <a data-name="non-racikan"
                                class="select-racikan non-racikan-button {{ $d->jenis_obat != 'racikan' ? 'active' : '' }}"
                                href="javascript:;">Non Racikan</a>
                            <input type="hidden" class="parent_resep" value="{{ $d->jenis_obat }}">
                            <input type="hidden" class="index_racikan" value="{{ $i + 1 }}">
                            <input type="hidden" class="id_detail" value="{{ $d->id }}">
                        </div>
                        <div
                            class="col-span-12 racikan-child racikan {{ $d->jenis_obat == 'racikan' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-6 parent disabled">
                                    <label class="form-label">Jenis Obat {{ dot() }}</label>
                                    <select class="jenis_obat_racikan form-control required select2resep">
                                        <option value="">Pilih Jenis Obat</option>
                                        @foreach (\App\Models\KategoriObat::where('status', true)->get() as $item)
                                            <option {{ $d->kategori_obat_id == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2 parent disabled">
                                    <label class="form-label">Satuan {{ dot() }}</label>
                                    <select class="satuan_racikan form-control required select2filter">
                                        <option value="">Pilih Satuan</option>
                                        @foreach (\App\Models\SatuanObat::where('status', true)->get() as $item)
                                            <option {{ $d->satuan_obat_id == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-4 md:col-span-2 parent">
                                    <label for="" class="form-label">Jumlah Obat {{ dot() }}</label>
                                    <input type="number" readonly
                                        class="form-control w-100 text-right mask-non-decimal required qty_racikan"
                                        value="{{ $d->qty }}">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <label class="form-label block">&nbsp;</label>
                                    @if ($req->edit == 'true')
                                        <button type="button" class="btn btn-danger" onclick="hapusResep(this)"><i
                                                class="fa fa-trash w-100"></i></button>
                                    @endif
                                </div>
                                <div class="col-span-12">
                                    <div class="append-racikan pl-5">
                                        @foreach ($d->rekamMedisResepRacikan as $d1)
                                            <div class="col-span-12 parent-child-racikan">
                                                <div class="grid grid-cols-12 gap-2">
                                                    <div class="col-span-4 mb-2 parent disabled">
                                                        <label for="">Produk Obat {{ dot() }}</label>
                                                        <select onchange="racikanObat(this)"
                                                            class="racikan_produk_obat form-control required select2resep">
                                                            <option value="">Pilih Jenis Obat</option>
                                                            @foreach ($produkObat as $item)
                                                                <option
                                                                    data-qty="{{ $item->StockFirst != null ? $item->StockFirst->qty : 0 }}"
                                                                    {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                                                                    {{ $d1->produk_obat_id == $item->id ? 'selected' : '' }}
                                                                    value="{{ $item->id }}">
                                                                    {{ $item->name }}
                                                                    {{-- {{ $item->dosis }} mg --}}
                                                                    {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stok Kosong)' : '') : '(Stok Kosong)' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @if ($req->edit == 'true')
                                                        <div class="col-span-2 mb-2 parent">
                                                            <label for="">Stok Obat</label>
                                                            <input type="number" readonly
                                                                class="form-control harga w-100 mask text-right required racikan_sisa_qty"
                                                                value="{{ count($d1->ProdukObat->Stock) != 0 ? $d1->ProdukObat->Stock->where('branch_id', $rm->Pendaftaran->branch_id)->first()->qty : 0 }}">
                                                        </div>
                                                    @endif
                                                    <div class="col-span-4 md:col-span-2 mb-2 parent">
                                                        <label for="">Jumlah Obat {{ dot() }}</label>
                                                        <input type="number" readonly
                                                            class="form-control harga w-100 mask-non-decimal text-right required racikan_qty"
                                                            value="{{ $d1->qty }}"
                                                            {{ $req->edit == 'false' ? 'readonly' : '' }}>
                                                    </div>
                                                    @if ($req->edit == 'true')
                                                        <div class="col-span-4 md:col-span-2 mb-2">
                                                            <label class="block">&nbsp;</label>
                                                            <a type="button" onclick="hapusRacikanChild(this)"><i
                                                                    class="fa fa-trash w-full cursor-pointer text-danger"></i></a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-span-12 parent">
                                    <label class="form-label">Signature (Keterangan) {{ dot() }}</label>
                                    <textarea readonly class="form-control description_racikan required" cols="2" rows="2">{{ $d->description }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-span-12 racikan-child non-racikan {{ $d->jenis_obat == 'non-racikan' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-4 mb-2 parent disabled">
                                    <label for="" class="form-label">Produk Obat {{ dot() }}</label>
                                    <select onchange="nonRacikanObat(this)"
                                        class="produk_obat_non_racikan form-control required select2resep">
                                        <option value="">Pilih Jenis Obat</option>
                                        @foreach ($produkObat as $item)
                                            <option
                                                data-qty="{{ $item->StockFirst != null ? $item->StockFirst->qty : 0 }}"
                                                {{ $d->produk_obat_id == $item->id ? 'selected' : '' }}
                                                {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                                                value="{{ $item->id }}">{{ $item->name }}
                                                {{-- {{ $item->dosis }} mg --}}
                                                {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stok Kosong)' : '') : '(Stok Kosong)' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($req->edit == 'true')
                                    <div class="col-span-4 mb-2 parent disabled">
                                        <label for="" class="form-label">Stok Obat</label>
                                        @if ($d->produkObat)
                                            <input type="number" readonly
                                                class="form-control w-100 text-right required sisa_qty_non_racikan"
                                                readonly
                                                value="{{ $d->ProdukObat->StockFirst != null ? $d->ProdukObat->StockFirst->qty : 0 }}">
                                        @else
                                            <input type="number" readonly
                                                class="form-control w-100 text-right required sisa_qty_non_racikan"
                                                readonly value="" name="sisa_qty_non_racikan[]">
                                        @endif

                                    </div>
                                @endif
                                <div class="col-span-2 mb-2 parent disabled">
                                    <label for="" class="form-label">Jumlah Obat {{ dot() }}</label>
                                    <input type="number" readonly
                                        class="form-control w-100 text-right required qty_non_racikan"
                                        value="{{ $d->qty }}" {{ $req->edit == 'false' ? 'readonly' : '' }}>
                                </div>


                                <div class="col-span-12 mb-2 parent">
                                    <label class="form-label">Signature (Keterangan) {{ dot() }}</label>
                                    <textarea class="form-control description_non_racikan required" readonly cols="2" rows="2"
                                        {{ $req->edit == 'false' ? 'readonly' : '' }}>{{ $d->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!--  <ul class="col-span-12">
                    <li class="list-group-item text-center must-hidden w-full">
                        <div>
                            Belum ada data obat racikan
                        </div>
                    </li>
                </ul> -->
            @endforelse

        </div>
        <div class="grid grid-cols-12 gap-6 col-span-12 clearfix  p-5">
            @if ($rm->status_apoteker == 'progress')
                @if (Auth::user()->role_id == 1)
                    <div class="col-span-12 flex align-middle">
                        <button type="button" class="btn btn-primary btn-xs ml-auto" onclick="saveData()">
                            <i class="fa fa-save mr-2"></i>
                            Simpan Data
                        </button>
                        <button type="button" class="btn btn-primary btn-xs ml-1" onclick="storeApotek()">
                            <i class="fa fa-folder mr-2"></i>
                            Proses Data
                        </button>
                    </div>
                @elseif (Auth::user()->role_id == 5)
                    <div class="col-span-12 flex align-middle">
                        <button type="button" class="btn btn-primary btn-xs ml-auto" onclick="saveData()">
                            <i class="fa fa-save mr-2"></i>
                            Simpan Data
                        </button>
                    </div>
                @else
                    <div class="col-span-12 flex align-middle">
                        <button type="button" class="btn btn-primary btn-xs ml-1" onclick="storeApotek()">
                            <i class="fa fa-folder mr-2"></i>
                            Proses Data
                        </button>
                    </div>
                @endif
            @endif
            <!-- @if ($rm->status_apoteker == 'waiting' || $rm->status_apoteker == 'revisi')
                <div class="col-span-12 flex align-middle">
                    <button type="button" class="btn btn-primary btn-xs ml-auto" onclick="saveData()">
                        <i class="fa fa-save mr-2"></i>
                        Simpan Data
                    </button>
                    <button type="button" class="btn btn-primary btn-xs ml-1" onclick="storeApotek()">
                        <i class="fa fa-folder mr-2"></i>
                        Proses Data
                    </button>
                </div>
            @else
                <div class="col-span-12 flex justify-between align-middle">
                    @if ($rm->status_apoteker == 'progress')
                        <button type="button" class="btn btn-primary btn-xs ml-auto" onclick="storeApotek()">
                            <i class="fa fa-folder mr-2"></i>
                            Proses Data
                        </button>
                    @endif
                </div>
            @endif -->
        </div>
    </div>
</div>
<script>
    indexRacikan = '{{ $rm->RekamMedisResep->count() }}';

    function backToRanap() {
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('rekam_medis_pasien_id[]', '{{ $rm->id }}');
        var previousWindowKeyDown = window.onkeydown;
        Swal.fire({
            title: "Kembalikan Ke Ranap",
            text: "Klik Tombol Ya jika ingin kembali ke Ranap.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
            showLoaderOnConfirm: true,
        }).then((result) => {
            if (result.value == true) {
                overlay(true)
                window.onkeydown = previousWindowKeyDown;
                $.ajax({
                    url: '{{ route('backToRanap') }}',
                    data: formData,
                    type: 'post',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 1) {
                            Swal.fire({
                                title: data.message,
                                icon: 'success',
                            }).then(function() {
                                location.reload();
                            });
                        } else if (data.status == 2) {
                            Swal.fire({
                                title: data.message,
                                icon: "warning",
                            });
                        }
                        overlay(false)
                    },
                    error: function(data) {
                        overlay(false)
                        var html = '';
                        Object.keys(data.responseJSON).forEach(element => {
                            html += data.responseJSON[element][0] + '<br>';
                        });
                        Swal.fire({
                            title: 'Ada Kesalahan !!!',
                            html: data.responseJSON.message == undefined ?
                                html : data
                                .responseJSON.message,
                            icon: "error",
                        });
                    }
                });
            }
        })
    }
</script>
