<div class="col-span-12 mb-3  box">
    <div class="flex items-center col-span-12 border-b border-slate-200/60 dark:border-darkmode-400 px-3 py-2 text-left bg-warning rounded-t-lg"
        style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(163, 37, 37)">
        <a href="javascript:;" class="font-medium text-white w-full text-xl">Pembayaran</a>
    </div>
    <div class="grid grid-cols-12 gap-6 modal-parent  p-8">
        <div class="col-span-12  grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <label class="form-label">Nomor Invoice</label>
                <input type="text" class="form-control" value="{{ $kode }}" placeholder="Kode" readonly
                    name="kode" id="kode">
                {{ csrf_field() }}
            </div>

            <div class="col-span-6">
                <label class="form-label">Tanggal Transaksi
                </label>
                <div class="input-group disabled">
                    <div class="input-group-text">
                        <i class="fa-solid fa-cake-candles"></i>
                    </div>

                    <input type="text" class="form-control tanggal" readonly data-single-mode="true" name="tanggal"
                        value="{{ dateStore() }}">
                </div>
            </div>
            <div class="col-span-6">
                <label class="form-label">Cashier</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name }}" placeholder="cashier"
                    readonly name="cashier" id="cashier">
            </div>
            <div class="col-span-6">
                <label class="form-label">No. Registrasi</label>
                <input type="text" class="form-control" value="{{ $data->kode }}" placeholder="No. Registrasi"
                    readonly name="kode_member" id="kode_member">
                <input type="hidden" name="owner_id" value="{{ $data->id }}" id="owner_id">
            </div>
            <div class="col-span-6">
                <label class="form-label">Nama Member</label>
                <input type="text" class="form-control" value="{{ $data->name }}" readonly
                    placeholder="Nama Member" name="nama_owner" id="nama_owner">
            </div>
            <div class="col-span-12">
                <label class="form-label">Email Cutomer</label>
                <input type="text" class="form-control" value="{{ $data->email }}" readonly
                    placeholder="Email Customer" name="email" id="email">
            </div>
        </div>
    </div>
</div>

<div class="col-span-12 mb-3  box">
    <div class="flex items-center col-span-12 border-b border-slate-200/60 dark:border-darkmode-400 px-3 py-2 text-left bg-warning rounded-t-lg"
        style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(163, 37, 37)">
        <a href="javascript:;" class="font-medium text-white w-full text-xl">Item Diskon</a>
    </div>
    <div class="grid grid-cols-12 gap-6 modal-parent  p-8">
        <div class="col-span-12">
            <table border="1" class="mb-3 table-item" style="padding: 0px;width: 100%">
                <thead align="center">
                    <th class="text-center">
                        <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
                    </th>
                    <th class="text-left">Item</th>
                    <th class="text-left">Pasien</th>
                    <th class="text-right">Harga Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-center">Diskon (%)</th>
                    <th class="text-right">Total Diskon</th>
                    <th class="text-right">Harga</th>
                </thead>
                <tbody id="append-obat">
                    @php
                        $total = 0;
                        $isNull = 0;
                    @endphp

                    @if ($data)
                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @foreach ($d1->rekamMedisResep as $item)
                                    @if ($item->ProdukObat)
                                        @if ($item->ProdukObat->diskon == 'true')
                                            <tr>
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->ProdukObat->name }} ({{ $d1->pasien->name }})
                                                    @else
                                                        {{ $item->KategoriObat->name }} Racikan
                                                    @endif
                                                    <input type="hidden" name="table[]" value="mp_rekam_medis_resep"
                                                        class="table">
                                                    <input type="hidden" name="ref[]" value="{{ $item->id }}"
                                                        class="ref">
                                                    <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ number_format($item->ProdukObat->harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $item->ProdukObat->harga }}">
                                                    @else
                                                        @php
                                                            $rule = ruleResepRacikan($item, $d);
                                                            
                                                            if ($rule) {
                                                                $harga = $rule->harga;
                                                                $total += $harga;
                                                            } else {
                                                                $harga = 0;
                                                            }
                                                        @endphp
                                                        {{ number_format($harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->qty ? $item->qty : 1 }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty ? $item->qty : 1 }}">
                                                    @else
                                                        1
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="1">
                                                    @endif

                                                </td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        @php
                                                            $total += $item->ProdukObat->harga * $item->qty;
                                                        @endphp
                                                        {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        {{ number_format($harga) }}
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" max="100" maxlength="3"
                                                        name="diskon_penyesuaian[]" value=""
                                                        class="diskon_penyesuaian form-control text-center"
                                                        style="width: 100%">
                                                </td>
                                                <td class="text-right">
                                                    <span class="nilai_diskon_penyesuaian_text">
                                                        0
                                                    </span>
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        value="0" class="nilai_diskon_penyesuaian">
                                                </td>
                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        @if ($item->jenis_obat == 'non-racikan')
                                                            {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        @else
                                                            {{ number_format($harga) }}
                                                        @endif
                                                    </span>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $isNull++;
                                            @endphp
                                        @endif
                                    @elseif ($item->KategoriObat)
                                        @if ($item->KategoriObat->diskon == 'true')
                                            <tr>
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->ProdukObat->name }} ({{ $d1->pasien->name }})
                                                    @else
                                                        {{ $item->KategoriObat->name }} Racikan
                                                    @endif
                                                    <input type="hidden" name="table[]" value="mp_rekam_medis_resep"
                                                        class="table">
                                                    <input type="hidden" name="ref[]" value="{{ $item->id }}"
                                                        class="ref">
                                                    <input type="hidden" name="stock[]" value="TIDAK"
                                                        class="stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ number_format($item->ProdukObat->harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $item->ProdukObat->harga }}">
                                                    @else
                                                        @php
                                                            $rule = ruleResepRacikan($item, $d);
                                                            
                                                            if ($rule) {
                                                                $harga = $rule->harga;
                                                                $total += $harga;
                                                            } else {
                                                                $harga = 0;
                                                            }
                                                        @endphp
                                                        {{ number_format($harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->qty ? $item->qty : 1 }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty ? $item->qty : 1 }}">
                                                    @else
                                                        1
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="1">
                                                    @endif

                                                </td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        @php
                                                            $total += $item->ProdukObat->harga * $item->qty;
                                                        @endphp
                                                        {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        {{ number_format($harga) }}
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" max="100" maxlength="3"
                                                        name="diskon_penyesuaian[]" value=""
                                                        class="diskon_penyesuaian form-control text-center"
                                                        style="width: 100%">
                                                </td>
                                                <td class="text-right">
                                                    <span class="nilai_diskon_penyesuaian_text">
                                                        0
                                                    </span>
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        value="0" class="nilai_diskon_penyesuaian">
                                                </td>
                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        @if ($item->jenis_obat == 'non-racikan')
                                                            {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        @else
                                                            {{ number_format($harga) }}
                                                        @endif
                                                    </span>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @php
                                            $isNull++;
                                        @endphp
                                    @endif
                                @endforeach

                                @foreach ($d1->rekamMedisResepRacikan as $item)
                                    @if ($item->ProdukObat)
                                        @if ($item->ProdukObat->diskon == 'true')
                                            <tr>
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->ProdukObat->name }} ({{ $d1->pasien->name }})
                                                    @else
                                                        {{ $item->KategoriObat->name }} Racikan
                                                    @endif
                                                    <input type="hidden" name="table[]" value="mo_produk_obat"
                                                        class="table">
                                                    <input type="hidden" name="ref[]"
                                                        value="{{ $item->ProdukObat->id }}" class="ref">
                                                    <input type="hidden" name="stock[]" value="YA"
                                                        class="stock">
                                                    <input type="hidden" name="jenis_stock[]" value="OBAT"
                                                        class="jenis_stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ number_format($item->ProdukObat->harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $item->ProdukObat->harga }}">
                                                    @else
                                                        @php
                                                            $rule = ruleResepRacikan($item, $d);
                                                            $hargaTemp = 0;
                                                            if ($rule) {
                                                                $hargaTemp = $rule->harga;
                                                                $harga = $rule->harga * $item->qty;
                                                                $total += $harga;
                                                            } else {
                                                                $harga = 0;
                                                            }
                                                        @endphp
                                                        {{ number_format($hargaTemp) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $hargaTemp }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->qty ? $item->qty : 1 }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty ? $item->qty : 1 }}">
                                                    @else
                                                        {{ $item->qty }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty }}">
                                                    @endif

                                                </td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        @php
                                                            $total += $item->ProdukObat->harga * $item->qty;
                                                        @endphp
                                                        {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        {{ number_format($harga) }}
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" max="100" maxlength="3"
                                                        name="diskon_penyesuaian[]" value=""
                                                        class="diskon_penyesuaian form-control text-center"
                                                        style="width: 100%">
                                                </td>
                                                <td class="text-right">
                                                    <span class="nilai_diskon_penyesuaian_text">
                                                        0
                                                    </span>
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        value="0" class="nilai_diskon_penyesuaian">
                                                </td>
                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        @if ($item->jenis_obat == 'non-racikan')
                                                            {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        @else
                                                            {{ number_format($harga) }}
                                                        @endif
                                                    </span>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $isNull++;
                                            @endphp
                                        @endif
                                    @elseif ($item->KategoriObat)
                                        @if ($item->KategoriObat->diskon == 'true')
                                            <tr>
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->ProdukObat->name }} ({{ $d1->pasien->name }})
                                                    @else
                                                        {{ $item->KategoriObat->name }} Racikan
                                                    @endif
                                                    <input type="hidden" name="table[]" value="mo_kategori_obat"
                                                        class="table">
                                                    <input type="hidden" name="ref[]"
                                                        value="{{ $item->KategoriObat->id }}" class="ref">
                                                    <input type="hidden" name="stock[]" value="TIDAK"
                                                        class="stock">
                                                    <input type="hidden" name="jenis_stock[]" value="OBAT"
                                                        class="jenis_stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ number_format($item->ProdukObat->harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $item->ProdukObat->harga }}">
                                                    @else
                                                        @php
                                                            $rule = ruleResepRacikan($item, $d);
                                                            $hargaTemp = 0;
                                                            if ($rule) {
                                                                $hargaTemp = $rule->harga;
                                                                $harga = $rule->harga * $item->qty;
                                                                $total += $harga;
                                                            } else {
                                                                $harga = 0;
                                                            }
                                                        @endphp
                                                        {{ number_format($hargaTemp) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $hargaTemp }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->qty ? $item->qty : 1 }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty ? $item->qty : 1 }}">
                                                    @else
                                                        {{ $item->qty }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty }}">
                                                    @endif

                                                </td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        @php
                                                            $total += $item->ProdukObat->harga * $item->qty;
                                                        @endphp
                                                        {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        {{ number_format($harga) }}
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" max="100" maxlength="3"
                                                        name="diskon_penyesuaian[]" value=""
                                                        class="diskon_penyesuaian form-control text-center"
                                                        style="width: 100%">
                                                </td>
                                                <td class="text-right">
                                                    <span class="nilai_diskon_penyesuaian_text">
                                                        0
                                                    </span>
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        value="0" class="nilai_diskon_penyesuaian">
                                                </td>
                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        @if ($item->jenis_obat == 'non-racikan')
                                                            {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        @else
                                                            {{ number_format($harga) }}
                                                        @endif
                                                    </span>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @php
                                            $isNull++;
                                        @endphp
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach
                    @endif

                    @foreach ($data->pasien as $d)
                        @foreach ($d->rekamMedisPasien as $d1)
                            @foreach ($d1->rekamMedisTindakan as $item)
                                @if ($item->Tindakan->diskon == 'true')
                                    <tr class="item-lain-lain">
                                        <td class="text-red text-center">
                                            <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                        </td>
                                        <td>
                                            {{ $item->Tindakan->name }}
                                            <input type="hidden" name="table[]" class="table"
                                                value="mp_rekam_medis_tindakan">
                                            <input type="hidden" name="ref[]" class="ref"
                                                value="{{ $item->id }}">
                                            <input type="hidden" name="stock[]" class="stock" value="TIDAK">
                                            <input type="hidden" name="rekam_medis_pasien_id[]"
                                                value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                        </td>
                                        <td>{{ $d1->Pasien->name }}</td>
                                        <td class="text-right">
                                            {{ number_format($item->Tindakan->tarif) }}
                                            @php
                                                $total += $item->Tindakan->tarif * $item->qty;
                                            @endphp
                                            <input type="hidden" name="harga[]"
                                                value="{{ $item->Tindakan->tarif }}">
                                        </td>
                                        <td class="text-center">
                                            {{ $item->qty }}
                                            <input type="hidden" name="qty[]" class="qty"
                                                value="{{ $item->qty }}">
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($item->Tindakan->tarif * $item->qty) }}
                                            <input type="hidden" name="bruto[]" class="bruto"
                                                value="{{ $item->Tindakan->tarif * $item->qty }}">
                                        </td>
                                        <td>
                                            <input type="number" max="100" maxlength="3"
                                                name="diskon_penyesuaian[]" value=""
                                                class="diskon_penyesuaian form-control text-center"
                                                style="width: 100%">
                                        </td>
                                        <td class="text-right">
                                            <span class="nilai_diskon_penyesuaian_text">
                                                0
                                            </span>
                                            <input type="hidden" name="nilai_diskon_penyesuaian[]" value="0"
                                                class="nilai_diskon_penyesuaian">
                                        </td>
                                        <td class="text-right">
                                            <span class="sub_total_text">
                                                {{ number_format($item->Tindakan->tarif * $item->qty) }}
                                            </span>
                                            <input type="hidden" name="sub_total[]" class="sub_total"
                                                value="{{ $item->Tindakan->tarif * $item->qty }}">
                                        </td>
                                    </tr>
                                    @php
                                        $isNull++;
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach

                    @foreach ($data->pasien as $d)
                        @foreach ($d->rekamMedisPasien as $d1)
                            @foreach ($d1->rekamMedisRekomendasiTindakanBedah->where('status', 'Done') as $item)
                                @if ($item->Tindakan->diskon == 'true')
                                    <tr class="item-lain-lain">
                                        <td class="text-red text-center">
                                            <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                        </td>
                                        <td>
                                            {{ $item->Tindakan->name }}
                                            <input type="hidden" name="table[]" class="table"
                                                value="mp_rekam_medis_tindakan_bedah">
                                            <input type="hidden" name="ref[]" class="ref"
                                                value="{{ $item->id }}">
                                            <input type="hidden" name="stock[]" class="stock" value="TIDAK">
                                            <input type="hidden" name="rekam_medis_pasien_id[]"
                                                value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                        </td>
                                        <td>{{ $d1->Pasien->name }}</td>
                                        <td class="text-right">
                                            {{ number_format($item->Tindakan->tarif) }}
                                            @php
                                                $total += $item->Tindakan->tarif * $item->qty;
                                            @endphp
                                            <input type="hidden" name="harga[]"
                                                value="{{ $item->Tindakan->tarif }}">
                                        </td>
                                        <td class="text-center">
                                            {{ $item->qty }}
                                            <input type="hidden" name="qty[]" class="qty"
                                                value="{{ $item->qty }}">
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($item->Tindakan->tarif * $item->qty) }}
                                            <input type="hidden" name="bruto[]" class="bruto"
                                                value="{{ $item->Tindakan->tarif * $item->qty }}">
                                        </td>
                                        <td>
                                            <input type="number" max="100" maxlength="3"
                                                name="diskon_penyesuaian[]" value=""
                                                class="diskon_penyesuaian form-control text-center"
                                                style="width: 100%">
                                        </td>
                                        <td class="text-right">
                                            <span class="nilai_diskon_penyesuaian_text">
                                                0
                                            </span>
                                            <input type="hidden" name="nilai_diskon_penyesuaian[]" value="0"
                                                class="nilai_diskon_penyesuaian">
                                        </td>
                                        <td class="text-right">
                                            <span class="sub_total_text">
                                                {{ $item->Tindakan->tarif * $item->qty }}
                                            </span>
                                            <input type="hidden" name="sub_total[]" class="sub_total"
                                                value="{{ $item->Tindakan->tarif * $item->qty }}">
                                        </td>
                                    </tr>
                                    @php
                                        $isNull++;
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach

                    @if ($isNull == 0)
                        <tr>
                            <td colspan="9" class="text-center">
                                Tidak ada Data
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="hidden">
                        <td colspan="6" class="text-right">
                            <b>Diskon</b>
                        </td>
                        <td class="text-right" style="width: 30%">
                            <div class="input-group">
                                <div class="input-group-text">
                                    Rp.
                                </div>

                                <input type="text" id="diskon_penyesuaian" name="diskon_penyesuaian"
                                    class="form-control mask text-right" placeholder="xxx,xxxx">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-right">
                            <b>Total</b>
                        </td>
                        <td class="text-right" style="width: 30%">
                            <div class="input-group">
                                <div class="input-group-text">
                                    Rp.
                                </div>

                                <input type="text" id="total_obat" value="{{ number_format($total) }}" readonly
                                    name="total_obat" class="form-control text-right" placeholder="xxx,xxxx">
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="col-span-12 mb-3  box">
    <div class="flex items-center col-span-12 border-b border-slate-200/60 dark:border-darkmode-400 px-3 py-2 text-left bg-warning rounded-t-lg"
        style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(77, 35, 185)">
        <a href="javascript:;" class="font-medium text-white w-full text-xl">Lain Lain</a>
    </div>
    <div class="grid grid-cols-12 gap-6 modal-parent  p-8">
        <div class="col-span-12">
            <table border="1" class="mb-3 table-item" style="padding: 0px;width: 100%">
                <thead align="center">
                    <th class="text-center">
                        <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
                    </th>
                    <th class="text-left">Item Code</th>
                    <th class="text-left">Keterangan</th>
                    <th class="text-right">Harga</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Jumlah</th>
                </thead>
                <tbody id="append-lain-lain">
                    @php
                        $totalLainLain = 0;
                        $isNull = 0;
                    @endphp
                    @if ($data)
                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @foreach ($d1->rekamMedisResep as $item)
                                    @if ($item->ProdukObat)
                                        @if ($item->ProdukObat->diskon == 'false')
                                            <tr>
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->ProdukObat->name }} ({{ $d1->pasien->name }})
                                                    @else
                                                        {{ $item->KategoriObat->name }} Racikan
                                                    @endif
                                                    <input type="hidden" name="table[]" value="mp_rekam_medis_resep"
                                                        class="table">
                                                    <input type="hidden" name="ref[]"
                                                        value="{{ $item->id }}" class="ref">
                                                    <input type="hidden" name="stock[]" value="TIDAK"
                                                        class="stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ number_format($item->ProdukObat->harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $item->ProdukObat->harga }}">
                                                    @else
                                                        @php
                                                            $rule = ruleResepRacikan($item, $d);
                                                            
                                                            if ($rule) {
                                                                $harga = $rule->harga;
                                                                $totalLainLain += $harga;
                                                            } else {
                                                                $harga = 0;
                                                            }
                                                        @endphp
                                                        {{ number_format($harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->qty ? $item->qty : 1 }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty ? $item->qty : 1 }}">
                                                    @else
                                                        1
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="1">
                                                    @endif

                                                </td>
                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        @if ($item->jenis_obat == 'non-racikan')
                                                            {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        @else
                                                            {{ number_format($harga) }}
                                                        @endif
                                                    </span>
                                                    <input type="hidden" name="diskon_penyesuaian[]"
                                                        class="diskon_penyesuaian" value="0">
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        class="nilai_diskon_penyesuaian" value="0">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $harga }}">
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $isNull++;
                                            @endphp
                                        @endif
                                    @elseif ($item->KategoriObat)
                                        @if ($item->KategoriObat->diskon == 'false')
                                            <tr>
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->ProdukObat->name }} ({{ $d1->pasien->name }})
                                                    @else
                                                        {{ $item->KategoriObat->name }} Racikan
                                                    @endif
                                                    <input type="hidden" name="table[]" value="mp_rekam_medis_resep"
                                                        class="table">
                                                    <input type="hidden" name="ref[]"
                                                        value="{{ $item->id }}" class="ref">
                                                    <input type="hidden" name="stock[]" value="TIDAK"
                                                        class="stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ number_format($item->ProdukObat->harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $item->ProdukObat->harga }}">
                                                    @else
                                                        @php
                                                            $rule = ruleResepRacikan($item, $d);
                                                            
                                                            if ($rule) {
                                                                $harga = $rule->harga;
                                                                $totalLainLain += $harga;
                                                            } else {
                                                                $harga = 0;
                                                            }
                                                        @endphp
                                                        {{ number_format($harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->qty ? $item->qty : 1 }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty ? $item->qty : 1 }}">
                                                    @else
                                                        1
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="1">
                                                    @endif

                                                </td>
                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        @if ($item->jenis_obat == 'non-racikan')
                                                            {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        @else
                                                            {{ number_format($harga) }}
                                                        @endif
                                                    </span>
                                                    <input type="hidden" name="diskon_penyesuaian[]"
                                                        class="diskon_penyesuaian" value="0">
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        class="nilai_diskon_penyesuaian" value="0">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $harga }}">
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @php
                                            $isNull++;
                                        @endphp
                                    @endif
                                @endforeach

                                @foreach ($d1->rekamMedisResepRacikan as $item)
                                    @if ($item->ProdukObat)
                                        @if ($item->ProdukObat->diskon == 'false')
                                            <tr>
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->ProdukObat->name }} ({{ $d1->pasien->name }})
                                                    @else
                                                        {{ $item->KategoriObat->name }} Racikan
                                                    @endif
                                                    <input type="hidden" name="table[]" value="mo_produk_obat"
                                                        class="table">
                                                    <input type="hidden" name="ref[]"
                                                        value="{{ $item->ProdukObat->id }}" class="ref">
                                                    <input type="hidden" name="stock[]" value="YA"
                                                        class="stock">
                                                    <input type="hidden" name="jenis_stock[]" value="OBAT"
                                                        class="jenis_stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ number_format($item->ProdukObat->harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $item->ProdukObat->harga }}">
                                                    @else
                                                        @php
                                                            $rule = ruleResepRacikan($item, $d);
                                                            $hargaTemp = 0;
                                                            if ($rule) {
                                                                $hargaTemp = $rule->harga;
                                                                $harga = $rule->harga * $item->qty;
                                                                $totalLainLain += $harga;
                                                            } else {
                                                                $harga = 0;
                                                            }
                                                        @endphp
                                                        {{ number_format($hargaTemp) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $hargaTemp }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->qty ? $item->qty : 1 }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty ? $item->qty : 1 }}">
                                                    @else
                                                        {{ $item->qty }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty }}">
                                                    @endif

                                                </td>
                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        @if ($item->jenis_obat == 'non-racikan')
                                                            {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        @else
                                                            {{ number_format($harga) }}
                                                        @endif
                                                    </span>
                                                    <input type="hidden" name="diskon_penyesuaian[]"
                                                        class="diskon_penyesuaian" value="0">
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        class="nilai_diskon_penyesuaian" value="0">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $harga }}">
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $isNull++;
                                            @endphp
                                        @endif
                                    @elseif ($item->KategoriObat)
                                        @if ($item->KategoriObat->diskon == 'false')
                                            <tr>
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->ProdukObat->name }} ({{ $d1->pasien->name }})
                                                    @else
                                                        {{ $item->KategoriObat->name }} Racikan
                                                    @endif
                                                    <input type="hidden" name="table[]" value="mo_kategori_obat"
                                                        class="table">
                                                    <input type="hidden" name="ref[]"
                                                        value="{{ $item->KategoriObat->id }}" class="ref">
                                                    <input type="hidden" name="stock[]" value="TIDAK"
                                                        class="stock">
                                                    <input type="hidden" name="jenis_stock[]" value="OBAT"
                                                        class="jenis_stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ number_format($item->ProdukObat->harga) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $item->ProdukObat->harga }}">
                                                    @else
                                                        @php
                                                            $rule = ruleResepRacikan($item, $d);
                                                            $hargaTemp = 0;
                                                            if ($rule) {
                                                                $hargaTemp = $rule->harga;
                                                                $harga = $rule->harga * $item->qty;
                                                                $totalLainLain += $harga;
                                                            } else {
                                                                $harga = 0;
                                                            }
                                                        @endphp
                                                        {{ number_format($hargaTemp) }}
                                                        <input type="hidden" name="harga[]"
                                                            value="{{ $hargaTemp }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        {{ $item->qty ? $item->qty : 1 }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty ? $item->qty : 1 }}">
                                                    @else
                                                        {{ $item->qty }}
                                                        <input type="hidden" name="qty[]" class="qty"
                                                            value="{{ $item->qty }}">
                                                    @endif

                                                </td>
                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        @if ($item->jenis_obat == 'non-racikan')
                                                            {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                                        @else
                                                            {{ number_format($harga) }}
                                                        @endif
                                                    </span>
                                                    <input type="hidden" name="diskon_penyesuaian[]"
                                                        class="diskon_penyesuaian" value="0">
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        class="nilai_diskon_penyesuaian" value="0">
                                                    @if ($item->jenis_obat == 'non-racikan')
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $item->ProdukObat->harga * $item->qty }}">
                                                    @else
                                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                                            value="{{ $harga }}">
                                                        <input type="hidden" name="bruto[]" class="bruto"
                                                            value="{{ $harga }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @php
                                            $isNull++;
                                        @endphp
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach
                    @endif

                    @if ($data)
                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @if (count($d1->KamarRawatInapDanBedahDetail) != 0)
                                    @php
                                        $qtyHari = 0;
                                        $arrayPriceKamar = [];
                                        foreach ($d1->KamarRawatInapDanBedahDetail->sortBy('tanggal_masuk') as $i => $item) {
                                            if (count($arrayPriceKamar) == 0) {
                                            // if($i == 0){
                                                array_push($arrayPriceKamar, $item->KamarRawatInapDanBedah->tarif_per_hari);
                                            } else {
                                                if($i > 0){
                                                    $previousPrice = $d1->KamarRawatInapDanBedahDetail[$i - 1]->KamarRawatInapDanBedah->tarif_per_hari;
                                                }
                                                if (!in_array($item->KamarRawatInapDanBedah->tarif_per_hari, $arrayPriceKamar)) {
                                                    array_push($arrayPriceKamar, $item->KamarRawatInapDanBedah->tarif_per_hari);
                                                }
                                            }
                                        }
                                    @endphp
                                    @foreach ($arrayPriceKamar as $d2)
                                        @php
                                            $qtyHari = 0;
                                            $kamar = null;
                                            $filter = $d1->kamarRawatInapDanBedahDetail->filter(function ($data, int $key) use ($d2) {
                                                $value = 0;
                                                if ($data->KamarRawatInapDanBedah->tarif_per_hari == $d2) {
                                                    $value++;
                                                }
                                                if ($value > 0) {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            });
                                            
                                            foreach ($filter as $i => $item) {
                                                $tanggalMasuk = carbon\carbon::parse($item->tanggal_masuk);
                                                $tanggalKeluar = carbon\carbon::parse($item->tanggal_keluar);
                                                $tempHari = carbon\Carbon::parse($tanggalMasuk)->diffInDays($tanggalKeluar);
                                            
                                                if ($tempHari < 1) {
                                                    if ($item->status != 'In Use') {
                                                        $tempHari = 1;
                                                    }
                                                }
                                            
                                                if ($tempHari > 0) {
                                                    $kamar = $item;
                                                }
                                            
                                                $qtyHari += $tempHari;
                                                $totalLainLain += $item->KamarRawatInapDanBedah->tarif_per_hari * $tempHari;
                                            }
                                        @endphp
                                        @if ($kamar)
                                            <tr class="item-lain-lain">
                                                <td class="text-red text-center">
                                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                                </td>
                                                <td>
                                                    Ruang Rawat Inap {{ $kamar->KamarRawatInapDanBedah->name }}
                                                    <input type="hidden" name="table[]"
                                                        value="mka_kamar_rawat_inap_dan_bedah_detail" class="tabler">
                                                    <input type="hidden" name="ref[]"
                                                        value="{{ $kamar->id }}" class="ref">
                                                    <input type="hidden" name="stock[]" value="TIDAK"
                                                        class="stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                </td>
                                                <td>{{ $d1->Pasien->name }}</td>
                                                <td class="text-right">
                                                    {{ number_format($kamar->KamarRawatInapDanBedah->tarif_per_hari) }}
                                                    <input type="hidden" name="harga[]"
                                                        value="{{ $kamar->KamarRawatInapDanBedah->tarif_per_hari }}">
                                                </td>
                                                <td class="text-center">
                                                    {{ $qtyHari }}
                                                    <input type="hidden" name="qty[]" class="qty"
                                                        value="{{ $qtyHari }}">
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($kamar->KamarRawatInapDanBedah->tarif_per_hari * $qtyHari) }}
                                                    <input type="hidden" name="diskon_penyesuaian[]"
                                                        class="diskon_penyesuaian" value="0">
                                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                        class="nilai_diskon_penyesuaian" value="0">
                                                    <input type="hidden" name="bruto[]" class="bruto"
                                                        value="{{ $kamar->KamarRawatInapDanBedah->tarif_per_hari * $qtyHari }}">
                                                    <input type="hidden" name="sub_total[]" class="sub_total"
                                                        value="{{ $kamar->KamarRawatInapDanBedah->tarif_per_hari * $qtyHari }}">
                                                </td>
                                            </tr>
                                            @php
                                                $isNull++;
                                            @endphp
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endforeach
                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @foreach ($d1->rekamMedisTindakan as $item)
                                    @if ($item->Tindakan->diskon == 'false')
                                        <tr class="item-lain-lain">
                                            <td class="text-red text-center">
                                                <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                            </td>
                                            <td>
                                                {{ $item->Tindakan->name }}
                                                <input type="hidden" name="table[]" class="table"
                                                    value="mk_tindakan">
                                                <input type="hidden" name="ref[]" class="ref"
                                                    value="{{ $item->Tindakan->id }}">
                                                <input type="hidden" name="stock[]" class="stock" value="TIDAK">
                                                <input type="hidden" name="jenis_stock[]" value="TINDAKAN"
                                                    class="jenis_stock">
                                                <input type="hidden" name="rekam_medis_pasien_id[]"
                                                    value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                            </td>
                                            <td>{{ $d1->Pasien->name }}</td>
                                            <td class="text-right">
                                                {{ number_format($item->Tindakan->tarif) }}
                                                @php
                                                    $totalLainLain += $item->Tindakan->tarif * $item->qty;
                                                @endphp
                                                <input type="hidden" name="harga[]"
                                                    value="{{ $item->Tindakan->tarif }}">
                                            </td>
                                            <td class="text-center">
                                                {{ $item->qty }}
                                                <input type="hidden" name="qty[]" class="qty"
                                                    value="{{ $item->qty }}">
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($item->Tindakan->tarif * $item->qty) }}
                                                <input type="hidden" name="diskon_penyesuaian[]"
                                                    class="diskon_penyesuaian" value="0">
                                                <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                    class="nilai_diskon_penyesuaian" value="0">
                                                <input type="hidden" name="bruto[]" class="bruto"
                                                    value="{{ $item->Tindakan->tarif * $item->qty }}">
                                                <input type="hidden" name="sub_total[]" class="sub_total"
                                                    value="{{ $item->Tindakan->tarif * $item->qty }}">
                                            </td>
                                        </tr>
                                        @php
                                            $isNull++;
                                        @endphp
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach

                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @foreach ($d1->rekamMedisRekomendasiTindakanBedah->where('status', 'Done') as $item)
                                    @if ($item->Tindakan->diskon == 'false')
                                        <tr class="item-lain-lain">
                                            <td class="text-red text-center">
                                                <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                            </td>
                                            <td>
                                                {{ $item->Tindakan->name }}
                                                <input type="hidden" name="table[]" class="table"
                                                    value="mp_rekam_medis_tindakan_bedah">
                                                <input type="hidden" name="ref[]" class="ref"
                                                    value="{{ $item->id }}">
                                                <input type="hidden" name="stock[]" class="stock" value="TIDAK">
                                                <input type="hidden" name="rekam_medis_pasien_id[]"
                                                    value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                            </td>
                                            <td>{{ $d1->Pasien->name }}</td>
                                            <td class="text-right">
                                                {{ number_format($item->Tindakan->tarif) }}
                                                @php
                                                    $totalLainLain += $item->Tindakan->tarif * $item->qty;
                                                @endphp
                                                <input type="hidden" name="harga[]"
                                                    value="{{ $item->Tindakan->tarif }}">
                                            </td>
                                            <td class="text-center">
                                                {{ $item->qty }}
                                                <input type="hidden" name="qty[]" class="qty"
                                                    value="{{ $item->qty }}">
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($item->Tindakan->tarif * $item->qty) }}
                                                <input type="hidden" name="diskon_penyesuaian[]"
                                                    class="diskon_penyesuaian" value="0">
                                                <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                    class="nilai_diskon_penyesuaian" value="0">
                                                <input type="hidden" name="bruto[]" class="bruto"
                                                    value="{{ $item->Tindakan->tarif * $item->qty }}">
                                                <input type="hidden" name="sub_total[]" class="sub_total"
                                                    value="{{ $item->Tindakan->tarif * $item->qty }}">
                                            </td>
                                        </tr>
                                        @php
                                            $isNull++;
                                        @endphp
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach

                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @foreach ($d1->rekamMedisPakan as $item)
                                    <tr>
                                        <td class="text-red text-center">
                                            <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                        </td>
                                        <td>
                                            {{ $item->ItemNonObat->name }} (Rawat Inap)
                                            <input type="hidden" name="table[]" value="mp_rekam_medis_pakan"
                                                class="table">
                                            <input type="hidden" name="ref[]" value="{{ $item->id }}"
                                                class="ref">
                                            <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                                            <input type="hidden" name="rekam_medis_pasien_id[]"
                                                value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                        </td>
                                        <td>{{ $d1->Pasien->name }}</td>
                                        <td class="text-right">
                                            {{ number_format($item->ItemNonObat->harga) }}
                                            <input type="hidden" name="harga[]"
                                                value="{{ $item->ItemNonObat->harga }}">
                                        </td>
                                        <td class="text-center">
                                            {{ $item->jumlah ? $item->jumlah : 1 }}
                                            <input type="hidden" name="qty[]" class="qty"
                                                value="{{ $item->jumlah ? $item->jumlah : 1 }}">
                                        </td>
                                        <td class="text-right">
                                            @php
                                                $totalLainLain += $item->ItemNonObat->harga * $item->jumlah;
                                            @endphp
                                            {{ number_format($item->ItemNonObat->harga * $item->jumlah) }}
                                            <input type="hidden" name="diskon_penyesuaian[]"
                                                class="diskon_penyesuaian" value="0">
                                            <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                class="nilai_diskon_penyesuaian" value="0">
                                            <input type="hidden" name="bruto[]" class="bruto"
                                                value="{{ $item->ItemNonObat->harga * $item->jumlah }}">
                                            <input type="hidden" name="sub_total[]" class="sub_total"
                                                value="{{ $item->ItemNonObat->harga * $item->jumlah }}">
                                        </td>
                                    </tr>
                                    @php
                                        $isNull++;
                                    @endphp
                                @endforeach
                                @foreach ($d1->rekamMedisNonObat as $item)
                                    <tr>
                                        <td class="text-red text-center">
                                            <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                        </td>
                                        <td>
                                            {{ $item->ItemNonObat->name }}
                                            <input type="hidden" name="table[]" value="mp_rekam_medis_pakan"
                                                class="table">
                                            <input type="hidden" name="ref[]" value="{{ $item->id }}"
                                                class="ref">
                                            <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                                            <input type="hidden" name="rekam_medis_pasien_id[]"
                                                value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                        </td>
                                        <td>{{ $d1->Pasien->name }}</td>
                                        <td class="text-right">
                                            {{ number_format($item->ItemNonObat->harga) }}
                                            <input type="hidden" name="harga[]"
                                                value="{{ $item->ItemNonObat->harga }}">
                                        </td>
                                        <td class="text-center">
                                            {{ $item->jumlah ? $item->jumlah : 1 }}
                                            <input type="hidden" name="qty[]" class="qty"
                                                value="{{ $item->jumlah ? $item->jumlah : 1 }}">
                                        </td>
                                        <td class="text-right">
                                            @php
                                                $totalLainLain += $item->ItemNonObat->harga * $item->jumlah;
                                            @endphp
                                            {{ number_format($item->ItemNonObat->harga * $item->jumlah) }}
                                            <input type="hidden" name="diskon_penyesuaian[]"
                                                class="diskon_penyesuaian" value="0">
                                            <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                class="nilai_diskon_penyesuaian" value="0">
                                            <input type="hidden" name="bruto[]" class="bruto"
                                                value="{{ $item->ItemNonObat->harga * $item->jumlah }}">
                                            <input type="hidden" name="sub_total[]" class="sub_total"
                                                value="{{ $item->ItemNonObat->harga * $item->jumlah }}">
                                        </td>
                                    </tr>
                                    @php
                                        $isNull++;
                                    @endphp
                                @endforeach
                            @endforeach
                        @endforeach

                        {{-- @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien->where('grooming', true) as $item)
                                <tr>
                                    <td class="text-red text-center">
                                        <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                    </td>
                                    <td>
                                        {{ $item->JenisGrooming->name }}
                                        <input type="hidden" name="table[]" value="grooming" class="table">
                                        <input type="hidden" name="ref[]" value="{{ $item->id }}"
                                            class="ref">
                                        <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                                        <input type="hidden" name="rekam_medis_pasien_id[]"
                                            value="{{ $item->id }}" class="rekam_medis_pasien_id">
                                    </td>
                                    <td>{{ $d1->Pasien->name }}</td>
                                    <td class="text-right">
                                        {{ number_format($item->JenisGrooming->tarif) }}
                                        <input type="hidden" name="harga[]"
                                            value="{{ $item->JenisGrooming->tarif }}">
                                    </td>
                                    <td class="text-center">
                                        1
                                        <input type="hidden" name="qty[]" class="qty" value="1">
                                    </td>
                                    <td class="text-right">
                                        @php
                                            $totalLainLain += $item->JenisGrooming->tarif * 1;
                                        @endphp
                                        {{ number_format($item->JenisGrooming->tarif * 1) }}
                                        <input type="hidden" name="diskon_penyesuaian[]" class="diskon_penyesuaian"
                                            value="0">
                                        <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                            class="nilai_diskon_penyesuaian" value="0">
                                        <input type="hidden" name="bruto[]" class="bruto"
                                            value="{{ $item->JenisGrooming->tarif * 1 }}">
                                        <input type="hidden" name="sub_total[]" class="sub_total"
                                            value="{{ $item->JenisGrooming->tarif * 1 }}">
                                    </td>
                                </tr>
                                @php
                                    $isNull++;
                                @endphp
                            @endforeach
                        @endforeach --}}

                        @foreach ($penjemputan as $item)
                            <tr>
                                <td class="text-red text-center">
                                    <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                </td>
                                <td style="width: 40%">
                                    Layanan Penjemputan
                                    <input type="hidden" name="table[]" value="qm_pendaftaran" class="table">
                                    <input type="hidden" name="ref[]" value="{{ $item->id }}"
                                        class="ref">
                                    <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                                    <input type="hidden" name="jenis_stock[]" value="PICKUP"
                                        class="jenis_stock">
                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                        value="{{ $item->id }}" class="rekam_medis_pasien_id">
                                    <input type="hidden" name="pasiens_id[]" value="{{ $item->pasien_id }}"
                                        class="pasiens_id">
                                </td>
                                <td>{{ $d1->Pasien->name }}</td>
                                <td class="text-right" style="width: 15%">
                                    <input type="text" name="harga[]" value=""
                                        class="harga mask form-control text-right" style="width: 100%">
                                </td>
                                <td class="text-center" style="width: 15%">
                                    1
                                    <input type="hidden" name="qty[]" class="qty" value="1">
                                </td>
                                <td class="text-right" style="width: 10%">
                                    <span class="sub_total_text">0</span>
                                    <input type="hidden" name="diskon_penyesuaian[]" class="diskon_penyesuaian"
                                        value="0">
                                    <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                        class="nilai_diskon_penyesuaian" value="0">
                                    <input type="hidden" name="bruto[]" class="bruto" value="0">
                                    <input type="hidden" name="sub_total[]" class="sub_total" value="0">
                                </td>
                            </tr>
                            @php
                                $isNull++;
                            @endphp
                        @endforeach


                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $item)
                                @if ($item->status_pemeriksaan == 'Pasien Meninggal')
                                    @php
                                        $tindakan = \App\Models\Tindakan::where('name', 'Pemakaman')
                                            ->where('status', true)
                                            ->first();
                                    @endphp
                                    <tr>
                                        <td class="text-red text-center">
                                            <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                        </td>
                                        <td style="width: 40%">
                                            Pemakaman
                                            <input type="hidden" name="table[]" value="pasien_meninggal"
                                                class="table">
                                            <input type="hidden" name="ref[]" value="{{ $tindakan->id }}"
                                                class="ref">
                                            <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                                            <input type="hidden" name="rekam_medis_pasien_id[]"
                                                value="{{ $item->id }}" class="rekam_medis_pasien_id">
                                        </td>
                                        <td>{{ $item->Pasien->name }}</td>
                                        <td class="text-right" style="width: 15%">
                                            <input type="text" name="harga[]"
                                                value="{{ $tindakan ? number_format($tindakan->tarif) : 0 }}"
                                                class="harga mask form-control text-right" style="width: 100%">
                                        </td>
                                        <td class="text-center" style="width: 15%">
                                            1
                                            <input type="hidden" name="qty[]" class="qty" value="1">
                                        </td>
                                        <td class="text-right" style="width: 10%">
                                            <span
                                                class="sub_total_text">{{ $tindakan ? number_format($tindakan->tarif) : 0 }}</span>
                                            <input type="hidden" name="diskon_penyesuaian[]"
                                                class="diskon_penyesuaian" value="0">
                                            <input type="hidden" name="nilai_diskon_penyesuaian[]"
                                                class="nilai_diskon_penyesuaian" value="0">
                                            <input type="hidden" name="bruto[]" class="bruto"
                                                value="{{ $tindakan ? number_format($tindakan->tarif) : 0 }}">
                                            <input type="hidden" name="sub_total[]" class="sub_total"
                                                value="{{ $tindakan ? $tindakan->tarif : 0 }}">
                                        </td>
                                    </tr>
                                    @php
                                        $totalLainLain += $tindakan ? $tindakan->tarif : 0;
                                        $isNull++;
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                    {{-- @if ($isNull == 0)
                        <tr class="tidak-ada-data">
                            <td colspan="7" class="text-center">
                                Tidak ada Data
                            </td>
                        </tr>
                    @endif --}}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">
                            <b>Total</b>
                        </td>
                        <td class="text-right" colspan="3">
                            <div class="input-group">
                                <div class="input-group-text">
                                    Rp.
                                </div>

                                <input type="text" id="total_lain"
                                    value="{{ number_format($totalLainLain) }}" readonly name="total_lain"
                                    class="form-control text-right" placeholder="xxx,xxxx">
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <hr>
            <br>
            <table class="table-total" style="width: 100%">
                <tr class="parent">
                    <td class="text-right" style="width: 25%;padding-right:1rem"><b>Total Item Diskon</b></td>
                    <td class="text-right" style="width: 30%">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="total_item_diskon" name="total_item_diskon" readonly
                                class="form-control mask text-right" placeholder="xxx,xxxx">
                        </div>
                    </td>
                </tr>
                <tr class="parent">
                    <td class="text-right" style="width: 25%;padding-right:1rem"><b>Total Item Non Diskon</b></td>
                    <td class="text-right" style="width: 30%">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="total_item_non_diskon" name="total_item_non_diskon" readonly
                                class="form-control mask text-right" placeholder="xxx,xxxx">
                        </div>
                    </td>
                </tr>
                <tr class="parent">
                    <td class="text-right" style="width: 25%;padding-right:1rem"><b>Total Pembayaran</b></td>
                    <td class="text-right" style="width: 30%">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="total_bayar" name="total_bayar" readonly
                                class="form-control mask text-right" placeholder="xxx,xxxx">
                        </div>
                    </td>
                </tr>
                <tr class="parent">
                    <td colspan="2" class="text-left">
                        <button type="button" class="btn btn-warning brn-rounded rounded"
                            onclick="printCheckout()"><b><i class="fa fa-print"></i> PRINT</b></button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>


<script>
    (function() {
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

        $(".tanggal").each(function() {
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

        $('.mask').maskMoney({
            precision: 0,
            thousands: ','
        })


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
</script>
