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
                    <input type="hidden" value="" name="jenis_tab" id="jenis_tab">
                </div>
            </div>
            <div class="col-span-6">
                <label class="form-label">Nama Cashier</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name }}" placeholder="cashier"
                    readonly name="cashier" id="cashier">
            </div>
            <div class="col-span-12 md:col-span-6 parent">
                <label>Kode Owner</label>
                <select name="kode_member" id="kode_member" class="form-control kode_member">
                </select>
                {{ csrf_field() }}
                <input type="hidden" id="index">
            </div>
            <div class="col-span-12 md:col-span-6 parent">
                <label>Nama Owner{{ dot() }}</label>
                <select name="owner_id" id="owner_id" class="form-control owner_id required">
                </select>
            </div>
            @if (!$data)
                <div class="col-span-12  parent">
                    <label>Nama Pasien (ketika dipilih akan memasukan data obat ke rekam medis)</label>
                    <select name="pasien_id" id="pasien_id" class="form-control pasien_id">
                    </select>
                </div>
            @endif
            <div class="col-span-12 md:col-span-6 parent">
                <label class="form-label">No Telepon</label>
                <input type="text" class="form-control" value="{{ $data ? $data->telpon : '-' }}"
                    placeholder="No Telepon" name="telpon" id="telpon">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="form-label">Email Customer</label>
                <input type="text" class="form-control" value="{{ $data ? $data->email : '-' }}"
                    placeholder="Email Customer" name="email" id="email">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="form-label">Komunitas</label>
                <input type="text" class="form-control" value="{{ $data ? $data->komunitas : '-' }}"
                    placeholder="Nama Komunitas" name="komunitas" id="komunitas">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="form-label">Type</label>
                <select name="type_kasir" id="type_kasir" class="select2 form-control">
                    @foreach (\App\Models\Kasir::$enumTypeKasir as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            @if ($data)
                <div class="col-span-12 md:col-span-6">
                    <button type="button" class="btn btn-primary" onclick="backToApotek()">Kembalikan Ke Apotek</button>
                    @if ($req->jenis == 'Rawat Inap')
                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 10)
                            <button type="button" class="btn btn-primary" onclick="backToRanap()">Kembalikan Ke
                                Ranap</button>
                        @endif
                    @endif
                </div>
                <div class="col-span-12 md:col-span-6">
                    <table class="w-full">
                        <tr>
                            <td><b>Dokter</b></td>
                        </tr>
                        @foreach ($dokter as $item)
                            <tr>
                                <td>{{ $item }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="col-span-12 mb-3  box">
    <div class="flex items-center col-span-12 border-b border-slate-200/60 dark:border-darkmode-400 px-3 py-2 text-left bg-warning rounded-t-lg"
        style="border-top-right-radius: 0.5rem !important;border-top-left-radius: 0.5rem !important;background: rgb(163, 37, 37)">
        <a href="javascript:;" class="font-medium text-white w-full text-xl">Item Diskon</a>
    </div>
    <div class="grid grid-cols-12 gap-6 modal-parent p-8">
        <div class="col-span-12 overflow-x-auto table-responsive">
            <table border="1" class="mb-3 table-item" style="padding: 0px;width: 100%">
                <thead align="center">
                    <th class="text-center">
                        <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
                    </th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Pasien</th>
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
                                @if (count($d1->KamarRawatInapDanBedahDetail) != 0)
                                    @php
                                        $qtyHari = 0;
                                        $arrayPriceKamar = [];
                                        $tempKamar = $d1->kamarRawatInapDanBedahDetail->sortBy('tanggal_masuk')->filter(function ($data, int $key) {
                                            $value = 0;
                                            if ($data->KamarRawatInapDanBedah->diskon == 'true') {
                                                $value++;
                                            }
                                            if ($value > 0) {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        });
                                        
                                        $lastIndex = 0;
                                        foreach ($tempKamar as $i => $item) {
                                            if (count($arrayPriceKamar) == 0) {
                                                array_push($arrayPriceKamar, $item->KamarRawatInapDanBedah->tarif_per_hari);
                                            } else {
                                                $previousPrice = $d1->KamarRawatInapDanBedahDetail[$lastIndex]->KamarRawatInapDanBedah->tarif_per_hari;
                                                if (!in_array($item->KamarRawatInapDanBedah->tarif_per_hari, $arrayPriceKamar)) {
                                                    array_push($arrayPriceKamar, $item->KamarRawatInapDanBedah->tarif_per_hari);
                                                }
                                            }
                                            $lastIndex = $i;
                                        }
                                    @endphp
                                    @foreach ($arrayPriceKamar as $d2)
                                        @php
                                            $qtyHari = 0;
                                            $kamar = null;
                                            $filter = $d1->kamarRawatInapDanBedahDetail
                                                ->filter(function ($data, int $key) use ($d2) {
                                                    $value = 0;
                                                    if ($data->KamarRawatInapDanBedah->tarif_per_hari == $d2) {
                                                        $value++;
                                                    }
                                                    if ($value > 0) {
                                                        return true;
                                                    } else {
                                                        return false;
                                                    }
                                                })
                                                ->filter(function ($data, int $key) use ($d2) {
                                                    $value = 0;
                                                    if ($data->KamarRawatInapDanBedah->diskon == 'true') {
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
                                                        // $tempHari = 1;
                                                    }
                                                }
                                            
                                                if ($tempHari > 0) {
                                                    $kamar = $item;
                                                }
                                            
                                                $qtyHari += $tempHari;
                                                $total += $item->KamarRawatInapDanBedah->tarif_per_hari * $tempHari;
                                            }
                                        @endphp
                                        @if ($kamar)
                                            <tr class="item-lain-lain">
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
                                                </td>
                                                <td>
                                                    Ruang Rawat Inap {{ $kamar->KamarRawatInapDanBedah->name }}
                                                    <input type="hidden" name="table[]"
                                                        value="mka_kamar_rawat_inap_dan_bedah_detail" class="tabler">
                                                    <input type="hidden" name="ref[]" value="{{ $kamar->id }}"
                                                        class="ref">
                                                    <input type="hidden" name="stock[]" value="TIDAK"
                                                        class="stock">
                                                    <input type="hidden" name="jenis_stock[]"
                                                        value="KAMAR RAWAT INAP" class="jenis_stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                                    <input type="hidden" name="bruto[]" class="bruto"
                                                        value="{{ $kamar->KamarRawatInapDanBedah->tarif_per_hari * $qtyHari }}">
                                                </td>
                                                <td>
                                                    <input type="number" max="100" maxlength="3"
                                                        name="diskon_penyesuaian[]" value=""
                                                        class="diskon_penyesuaian form-control text-center"
                                                        style="width: 100%">
                                                </td>
                                                <td class="text-right">
                                                    <input type="text" name="nilai_diskon_penyesuaian[]"
                                                        value="0"
                                                        class="nilai_diskon_penyesuaian form-control mask">
                                                </td>

                                                <td class="text-right">
                                                    <span class="sub_total_text">
                                                        {{ number_format($kamar->KamarRawatInapDanBedah->tarif_per_hari * $qtyHari) }}
                                                    </span>
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
                    @endif
                    @if ($data)
                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @foreach ($d1->rekamMedisResep as $item)
                                    @if ($item->ProdukObat)
                                        @if ($item->ProdukObat->diskon == 'true')
                                            <tr>
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
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
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                                    <input type="text" name="nilai_diskon_penyesuaian[]"
                                                        value="0"
                                                        class="nilai_diskon_penyesuaian form-control mask">
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
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
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
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                                    <input type="text" name="nilai_diskon_penyesuaian[]"
                                                        value="0"
                                                        class="nilai_diskon_penyesuaian form-control mask">
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
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
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
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                                    <input type="text" name="nilai_diskon_penyesuaian[]"
                                                        value="0"
                                                        class="nilai_diskon_penyesuaian form-control mask">
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
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
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
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                                    <input type="text" name="nilai_diskon_penyesuaian[]"
                                                        value="0"
                                                        class="nilai_diskon_penyesuaian form-control mask">
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

                    @if ($data)
                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @foreach ($d1->rekamMedisTindakan as $item)
                                    @if (strtolower($item->Tindakan->diskon) == 'true')
                                        <tr class="item-lain-lain">
                                            <td class="text-red text-center">
                                                <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                            </td>
                                            <td>
                                                {{ $item->Tindakan->name }}
                                                <input type="hidden" name="table[]" class="table"
                                                    value="mk_tindakan">
                                                <input type="hidden" name="ref[]" class="ref"
                                                    value="{{ $item->tindakan_id }}">
                                                <input type="hidden" name="stock[]" class="stock" value="TIDAK">
                                                <input type="hidden" name="jenis_stock[]" value="TINDAKAN"
                                                    class="jenis_stock">
                                                <input type="hidden" name="rekam_medis_pasien_id[]"
                                                    value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                <input type="hidden" name="pasiens_id[]"
                                                    value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                                <input type="text" name="nilai_diskon_penyesuaian[]"
                                                    value="0" class="nilai_diskon_penyesuaian form-control">
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
                    @endif

                    @if ($data)
                        @foreach ($data->pasien as $d)
                            @foreach ($d->rekamMedisPasien as $d1)
                                @foreach ($d1->rekamMedisRekomendasiTindakanBedah->where('status', 'Done') as $item)
                                    @if (strtolower($item->Tindakan->diskon) == 'true')
                                        <tr class="item-lain-lain">
                                            <td class="text-red text-center">
                                                <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                            </td>
                                            <td>
                                                {{ $item->Tindakan->name }}
                                                <input type="hidden" name="table[]" class="table"
                                                    value="mp_rekam_medis_tindakan_bedah">
                                                <input type="hidden" name="ref[]" class="ref"
                                                    value="{{ $item->Tindakan->id }}">
                                                <input type="hidden" name="stock[]" class="stock" value="TIDAK">
                                                <input type="hidden" name="jenis_stock[]" value="TINDAKAN"
                                                    class="jenis_stock">
                                                <input type="hidden" name="rekam_medis_pasien_id[]"
                                                    value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                <input type="hidden" name="pasiens_id[]"
                                                    value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                            <td class="text-center">
                                                <input type="number" max="100" maxlength="3"
                                                    name="diskon_penyesuaian[]" value=""
                                                    class="diskon_penyesuaian form-control text-center"
                                                    style="width: 100%">
                                            </td>
                                            <td class="text-center">
                                                <input type="text" name="nilai_diskon_penyesuaian[]"
                                                    value="0" class="nilai_diskon_penyesuaian"
                                                    style="width: 100%">
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
                    @endif
                    <tr class="add-item-diskon">
                        <td colspan="9" class="text-center text-info">
                            <a href="javascript:;" onclick="openSideMenuKasir('diskon')">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                Tambah Item
                            </a>
                        </td>
                    </tr>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-center">
                            <b>Total Item Diskon</b>
                        </td>
                        <td colspan="3" class="text-right">
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
        <a href="javascript:;" class="font-medium text-white w-full text-xl">Item Non Diskon</a>
    </div>
    <div class="grid grid-cols-12 gap-6 modal-parent  p-8">
        <div class="col-span-12 overflow-x-auto table-responsive">

            <table border="1" class="mb-3 table-item" style="padding: 0px;width: 100%">
                <thead align="center">
                    <th class="text-center">
                        <i class="fa fa-info-circle text-info" aria-hidden="true"></i>
                    </th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Pasien</th>
                    <th class="text-right">Harga Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Jumlah</th>
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
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
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
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
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
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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

                                @foreach ($d1->rekamMedisResepRacikan as $item)
                                    @if ($item->ProdukObat)
                                        @if ($item->ProdukObat->diskon == 'false')
                                            <tr>
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
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
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                                <td class="text-red-500 text-center cursor-pointer">
                                                    <i class="fa fa-trash text-red" aria-hidden="true"
                                                        onclick="removeItem(this)"></i>
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
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                        $tempKamar = $d1->kamarRawatInapDanBedahDetail->sortBy('tanggal_masuk')->filter(function ($data, int $key) {
                                            $value = 0;
                                            if ($data->KamarRawatInapDanBedah->diskon == 'false') {
                                                $value++;
                                            }
                                            if ($value > 0) {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        });
                                        
                                        $lastIndex = 0;
                                        foreach ($tempKamar as $i => $item) {
                                            if (count($arrayPriceKamar) == 0) {
                                                array_push($arrayPriceKamar, $item->KamarRawatInapDanBedah->tarif_per_hari);
                                            } else {
                                                $previousPrice = $d1->KamarRawatInapDanBedahDetail[$lastIndex]->KamarRawatInapDanBedah->tarif_per_hari;
                                                if (!in_array($item->KamarRawatInapDanBedah->tarif_per_hari, $arrayPriceKamar)) {
                                                    array_push($arrayPriceKamar, $item->KamarRawatInapDanBedah->tarif_per_hari);
                                                }
                                            }
                                            $lastIndex = $i;
                                        }
                                    @endphp
                                    @foreach ($arrayPriceKamar as $d2)
                                        @php
                                            $qtyHari = 0;
                                            $kamar = null;
                                            $filter = $d1->kamarRawatInapDanBedahDetail
                                                ->filter(function ($data, int $key) use ($d2) {
                                                    $value = 0;
                                                    if ($data->KamarRawatInapDanBedah->tarif_per_hari == $d2) {
                                                        $value++;
                                                    }
                                                    if ($value > 0) {
                                                        return true;
                                                    } else {
                                                        return false;
                                                    }
                                                })
                                                ->filter(function ($data, int $key) use ($d2) {
                                                    $value = 0;
                                                    if ($data->KamarRawatInapDanBedah->diskon == 'false') {
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
                                                        // $tempHari = 1;
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
                                                    <input type="hidden" name="jenis_stock[]" value="NON OBAT"
                                                        class="jenis_stock">
                                                    <input type="hidden" name="rekam_medis_pasien_id[]"
                                                        value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                    <input type="hidden" name="pasiens_id[]"
                                                        value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                    @if (strtolower($item->Tindakan->diskon) == 'false')
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
                                                <input type="hidden" name="pasiens_id[]"
                                                    value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                    @if (strtolower($item->Tindakan->diskon) == 'false')
                                        <tr class="item-lain-lain">
                                            <td class="text-red text-center">
                                                <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                                            </td>
                                            <td>
                                                {{ $item->Tindakan->name }}
                                                <input type="hidden" name="table[]" class="table"
                                                    value="mp_rekam_medis_tindakan_bedah">
                                                <input type="hidden" name="ref[]" class="ref"
                                                    value="{{ $item->Tindakan->id }}">
                                                <input type="hidden" name="stock[]" class="stock"
                                                    value="TIDAK">
                                                <input type="hidden" name="jenis_stock[]" value="TINDAKAN"
                                                    class="jenis_stock">
                                                <input type="hidden" name="rekam_medis_pasien_id[]"
                                                    value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                                <input type="hidden" name="pasiens_id[]"
                                                    value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                            {{ $item->ItemNonObat->name }} Rawat Inap
                                            <input type="hidden" name="table[]" value="ms_item_non_obat"
                                                class="table">
                                            <input type="hidden" name="ref[]"
                                                value="{{ $item->ItemNonObat->id }}" class="ref">
                                            <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                                            <input type="hidden" name="jenis_stock[]" value="NON OBAT"
                                                class="jenis_stock">
                                            <input type="hidden" name="rekam_medis_pasien_id[]"
                                                value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                            <input type="hidden" name="pasiens_id[]"
                                                value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                        <td class="text-red-500 text-center cursor-pointer">
                                            <i class="fa fa-trash text-red" aria-hidden="true"
                                                onclick="removeItem(this)"></i>
                                        </td>
                                        <td>
                                            {{ $item->ItemNonObat->name }}
                                            <input type="hidden" name="table[]" value="ms_item_non_obat"
                                                class="table">
                                            <input type="hidden" name="ref[]"
                                                value="{{ $item->ItemNonObat->id }}" class="ref">
                                            <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                                            <input type="hidden" name="jenis_stock[]" value="NON OBAT"
                                                class="jenis_stock">
                                            <input type="hidden" name="rekam_medis_pasien_id[]"
                                                value="{{ $d1->id }}" class="rekam_medis_pasien_id">
                                            <input type="hidden" name="pasiens_id[]"
                                                value="{{ $d1->pasien_id }}" class="pasiens_id">
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
                                </tr>$
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
                                    <input type="hidden" name="rekam_medis_pasien_id[]" value=""
                                        class="rekam_medis_pasien_id">
                                    <input type="hidden" name="pasiens_id[]" value="" class="pasiens_id">
                                </td>
                                <td>-</td>
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
                                            ->where('binatang_id', $item->pasien->binatang_id)
                                            ->where('status', true)
                                            ->first();
                                        
                                        if (!$tindakan) {
                                            $tindakan = addPemakaman($item->pasien);
                                        }
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
                                            <input type="hidden" name="jenis_stock[]" value="PEMAKAMAN"
                                                class="jenis_stock">
                                            <input type="hidden" name="rekam_medis_pasien_id[]"
                                                value="{{ $item->id }}" class="rekam_medis_pasien_id">
                                            <input type="hidden" name="pasiens_id[]"
                                                value="{{ $item->pasien_id }}" class="pasiens_id">
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
                    <tr class="add-item-non-diskon">
                        <td colspan="7" class="text-center text-info">
                            <a href="javascript:;" onclick="openSideMenuKasir('non diskon')">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                Tambah Item
                            </a>
                        </td>
                    </tr>
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
                        <td colspan="4" class="text-center">
                            <b>Total Item Non Diskon</b>
                        </td>
                        <td class="text-right" colspan="4">
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
            <table class="table-deposit" style="width: 100%">
                <tr style="margin-bottom:1rem">
                    <td><b>Kode Deposit</b></td>
                    <td>
                        <div class="input-group">

                            <input type="text" readonly class="form-control kode_deposit" id="kode_deposit"
                                name="kode_deposit" placeholder="Klik untuk mencari deposit">
                            <div class="input-group-text" onclick="hapusDeposit()">
                                <button type="button" class="text-red"><i class="fas fa-times text-red"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr style="margin-bottom:1rem">
                    <td><b>Nominal Deposit</b></td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp
                            </div>
                            <input type="text" readonly class="form-control sisa_deposit" id="sisa_deposit"
                                name="sisa_deposit" placeholder="xxx,xxx.xx">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><b>Tarik Deposit</b></td>
                    <td>
                        <select name="tarik_deposit" id="tarik_deposit" class="form-control select2"
                            onchange="calcKembalian()">
                            <option value="TIDAK">TIDAK</option>
                            <!-- <option value="YA">YA</option> -->
                        </select>
                    </td>
                </tr>
            </table>
            <br>
            <table class="table-total" style="width: 100%">
                <tr class="parent">
                    <td colspan="5" rowspan="7" class="text-center text-white"
                        style="background-color: green">
                        <h3><b>Kembali : Rp <span id="uang_kembali_text"></span></b></h3>
                    </td>
                </tr>
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
                <tr class="parent hidden">
                    <td class="text-right" style="width: 25%;padding-right:1rem"><b>Diskon %</b></td>
                    <td class="text-right">
                        <div class="input-group">
                            <input type="text" id="diskon_persen" name="diskon_persen"
                                class="form-control mask text-right" placeholder="xxx"
                                onkeyup="calcTotalBayar('persen')">
                            <div class="input-group-text">
                                %
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="parent hidden">
                    <td class="text-right" style="width: 25%;padding-right:1rem"><b>Diskon Rp</b></td>
                    <td class="text-right">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="diskon" name="diskon"
                                class="form-control mask text-right" placeholder="xxx,xxxx"
                                onkeyup="calcTotalBayar('rupiah')">
                        </div>
                    </td>
                </tr>
                <tr class="parent hidden">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem"><b>Pembayaran</b></td>
                    <td class="text-right">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="pembayaran" name="pembayaran" readonly
                                class="form-control text-right" placeholder="xxx,xxxx">
                        </div>
                    </td>
                </tr>
                <tr class="parent">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem"><b>Deposit</b></td>
                    <td class="text-right" style="width: 30%">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="deposit" name="deposit" readonly
                                class="form-control text-right" value="" placeholder="xxx,xxxx">
                        </div>
                    </td>
                </tr>
                <tr class="parent">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem"><b>Sisa Pembayaran</b></td>
                    <td class="text-right">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="sisa_pembayaran" style="color: red" readonly
                                name="sisa_pembayaran" class="form-control text-right" placeholder="xxx,xxxx">
                        </div>
                    </td>
                </tr>
                <tr class="parent">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem"><b>Diterima</b></td>
                    <td class="text-right">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="diterima" name="diterima"
                                class="form-control mask text-right" placeholder="xxx,xxxx"
                                onkeyup="calcKembalian()">
                        </div>
                    </td>
                </tr>
                <tr class="parent">
                    <td colspan="5" rowspan="3" class="text-center">
                        {{-- <b id="terbilang"></b> --}}
                    </td>
                </tr>
                <tr class="parent">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem"><b>Uang Kembali</b></td>
                    <td class="text-right">
                        <div class="input-group">
                            <div class="input-group-text">
                                Rp.
                            </div>
                            <input type="text" id="uang_kembali" name="uang_kembali" readonly
                                class="form-control text-right required" placeholder="xxx,xxxx">
                        </div>
                    </td>
                </tr>
                <tr class="parent">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem"><b>Metode Pembayaran</b></td>
                    <td>
                        <select name="metode_pembayaran" id="metode_pembayaran"
                            class="form-control select2filter">
                            <option value="TUNAI">TUNAI</option>
                            <option value="DEBET">DEBET</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </td>
                </tr>
                <tr class="non-tunai hidden parent">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem" colspan="6"><b>Nama
                            Bank</b>
                    </td>
                    <td>
                        <input type="text" name="nama_bank" placeholder="Isikan Nama Bank" id="nama_bank"
                            class="form-control required uppercase">
                    </td>
                </tr>
                <tr class="non-tunai hidden parent">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem" colspan="6"><b>No.
                            Kartu</b>
                    </td>
                    <td>
                        <input type="text" name="nomor_kartu" id="nomor_kartu" placeholder="xxxxxxxxxx"
                            class="form-control required">
                    </td>
                </tr>
                <tr class="non-tunai hidden parent">
                    <td class="text-right pr-2" style="width: 25%;padding-right:1rem" colspan="6"><b>No.
                            Referensi</b>
                    </td>
                    <td>
                        <input type="text" name="nomor_transaksi" id="nomor_transaksi"
                            placeholder="xxxxxxxxxx" class="form-control required">
                    </td>
                </tr>
                <tr class="parent button-before-checkout">
                    <td colspan="7">
                        <button type="button" class="btn btn-primary brn-rounded rounded"
                            style="padding: 8px 0px;width: 100%" onclick="store()"><b>CHECKOUT</b></button>
                    </td>
                </tr>
                <tr class="parent">
                    <td colspan="7" class="text-right button-after-checkout hidden">
                        <button type="button" class="btn btn-warning brn-rounded rounded"
                            onclick="printCheckout()"><b><i class="fa fa-print"></i>&nbsp;PRINT</b>
                        </button>
                        <button type="button" class="btn btn-info brn-rounded rounded" onclick="refreshing()">
                            <i class="fa fa-refresh"></i>&nbsp;REFRESH
                        </button>
                        <button type="button" class="btn btn-info brn-rounded rounded" onclick="kirimKeEmail()">
                            <i class="fa fa-envelope"></i>&nbsp;KIRIM KE EMAIL
                        </button>
                    </td>
                </tr>
            </table>
            <div class="col-span-12">
                <label class="form-label" style="font-size: 25px">Catatan {{ dot() }}</label>
                <textarea id="catatan_kasir" name="catatan_kasir" type="text" class="form-control required"
                    placeholder="Masukan Catatan"></textarea>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-12 gap-6">

</div>
<script>
    (function() {
        $('.sex').select2({
            width: '100%',
            dropdownParent: $('#modal-rekam-medis .modal-body .modal-parent')
        })


        $('.select2').select2({
            width: '100%',
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

        $('.mask').maskMoney({
            precision: 0,
            thousands: ',',
            allowZero: true,
            defaultZero: true,
        })

        generateAge();

        $('#owner_id').on('select2:select', function(event) {
            var data = event.params.data;
            ownerId = data.id;
            if (data.name == undefined) {
                ownerId = null;
            } else {
                ownerId = data.id;
            }

            var newOption = new Option(data.kode,
                data.id,
                true,
                true
            );

            $('#kode_member').append(newOption).trigger('change.select2');

            $("#telpon").val(data.telpon);
            $("#email").val(data.email);
            $("#komunitas").val(data.komunitas);
            // $("#alamat").val(data.alamat);
            // $("#nik").val(data.nik);
        })

        $("#owner_id").select2({
            width: '100%',
            tags: true,
            ajax: {
                url: "{{ route('select2Pendaftaran') }}?param=owner_id",
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
            placeholder: 'Masukan Nama Owner',
            minimumInputLength: 0,
            templateResult: formatRepoStatus,
            templateSelection: formatRepoStatusSelection
        });

        $("#pasien_id").select2({
            width: '100%',
            ajax: {
                url: "{{ route('select2Pendaftaran') }}?param=pasien_id",
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term,
                        owner_id: $("#owner_id").val(),
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
            placeholder: 'Masukan Nama Hewan Peliharaan',
            minimumInputLength: 0,
            templateResult: formatRepoHewan,
            templateSelection: formatRepoHewanSelection
        });

        $('#kode_member').on('select2:select', function(event) {
            var data = event.params.data;
            ownerId = data.id;
            if (data.name == undefined) {
                ownerId = null;
            } else {
                ownerId = data.id;
            }

            var newOption = new Option(data.name,
                data.id,
                true,
                true
            );

            $('#owner_id').append(newOption).trigger('change.select2');

            $("#telpon").val(data.telpon);
            $("#email").val(data.email);
            $("#alamat").val(data.alamat);
            $("#komunitas").val(data.komunitas);
            $("#nik").val(data.nik);
        })

        $("#kode_member").select2({
            width: '100%',
            ajax: {
                url: "{{ route('select2Pendaftaran') }}?param=kode_owner",
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
            placeholder: 'Masukan Kode Owner',
            minimumInputLength: 0,
            templateResult: formatRepoNormalStatus,
            templateSelection: formatRepoNormalStatusSelection
        });

        @if ($data)
            var newOption = new Option('{{ $data->name }}',
                '{{ $data->id }}',
                true,
                true
            );
            $('#owner_id').append(newOption).trigger('change.select2');
        @endif


        @if ($data)
            var newOption = new Option('{{ $data->kode }}',
                '{{ $data->id }}',
                true,
                true
            );
            $('#kode_member').append(newOption).trigger('change.select2');
        @endif
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

    // function backToApotek() {
    //     var formData = new FormData();
    //     formData.append('_token', '{{ csrf_token() }}');
    //     @if ($data)
    //         @foreach ($data->pasien as $item)
    //             @foreach ($item->rekamMedisPasien as $item1)
    //                 formData.append('rekam_medis_pasien_id[]', '{{ $item1->id }}');
    //             @endforeach
    //         @endforeach
    //     @endif
    //     var previousWindowKeyDown = window.onkeydown;
    //     Swal.fire({
    //         title: "Kembalikan Ke Apotek",
    //         text: "Klik Tombol Ya jika ingin kembali ke Apotek.",
    //         icon: 'info',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Ya',
    //         cancelButtonText: 'Tidak',
    //         showLoaderOnConfirm: true,
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             overlay(true)
    //             window.onkeydown = previousWindowKeyDown;
    //             $.ajax({
    //                 url: '{{ route('backToApotek') }}',
    //                 data: formData,
    //                 type: 'post',
    //                 processData: false,
    //                 contentType: false,
    //                 success: function(data) {
    //                     if (data.status == 1) {
    //                         Swal.fire({
    //                             title: data.message,
    //                             icon: 'success',
    //                         }).then(function() {
    //                             location.reload();
    //                         });
    //                     } else if (data.status == 2) {
    //                         Swal.fire({
    //                             title: data.message,
    //                             icon: "warning",
    //                         });
    //                     }
    //                     overlay(false)
    //                 },
    //                 error: function(data) {
    //                     overlay(false)
    //                     var html = '';
    //                     Object.keys(data.responseJSON).forEach(element => {
    //                         html += data.responseJSON[element][0] + '<br>';
    //                     });
    //                     Swal.fire({
    //                         title: 'Ada Kesalahan !!!',
    //                         html: data.responseJSON.message == undefined ? html : data
    //                             .responseJSON.message,
    //                         icon: "error",
    //                     });
    //                 }
    //             });
    //         }
    //     })
    // }

    function backToApotek() {
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        @if ($data)
            @foreach ($data->pasien as $item)
                @foreach ($item->rekamMedisPasien as $item1)
                    formData.append('rekam_medis_pasien_id[]', '{{ $item1->id }}');
                @endforeach
            @endforeach
        @endif
        var previousWindowKeyDown = window.onkeydown;
        Swal.fire({
            title: "Kembalikan Ke Apotek",
            text: "Klik Tombol Ya jika ingin kembali ke Apotek.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
            showLoaderOnConfirm: true,
        }).then((result) => {
            if (result.value == true) {
                Swal.fire({
                    title: 'Notes Kasir',
                    html: "<div class='b'><p>Tulis Alasan Dikembalikan Untuk Apoteker</p></div><textarea id='swal-input2' class='swal2-input' style='resize:none; min-height:10rem; max-height:15rem; min-width:22rem; max-width:24rem' required/>",
                    confirmButtonText: 'Simpan Catatan',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        if (($('#swal-input2').val() == "") || ($('#swal-input2').val() == null)) {
                            Swal.showValidationMessage(`Maaf, Harus ada alasan pengembalian`)
                        }
                    }
                }).then((result) => {
                    formData.append('desc_kasir', $('#swal-input2').val())
                    if (result.isConfirmed) {
                        overlay(true)
                        window.onkeydown = previousWindowKeyDown;
                        $.ajax({
                            url: '{{ route('backToApotek') }}',
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
        })
    }

    function backToRanap() {
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        @if ($data)
            @foreach ($data->pasien as $item)
                @foreach ($item->rekamMedisPasien as $item1)
                    formData.append('rekam_medis_pasien_id[]', '{{ $item1->id }}');
                @endforeach
            @endforeach
        @endif
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

    function formatRepoHewan(repo) {
        if (repo.loading) {
            return repo.text;
        }

        if (repo.name != undefined) {
            image = repo.image ? "{{ url('/') }}" + "/" + repo.image : "{{ asset('dist/images/amore.png') }}";
            var $container = $(
                "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__avatar'><img style='" +
                "object-fit:cover" + "' src='" +
                image + "' /></div>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'></div>" +
                "<div class='select2-result-repository__description'></div>" +
                "<div class='select2-result-repository__statistics'>" +
                "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> </div>" +
                "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> </div>" +
                "<div class='select2-result-repository__watchers'><i class='fa fa-code-fork'></i> </div>" +
                "</div>" +
                "</div>" +
                "</div>"
            );

            $container.find(".select2-result-repository__title").text(repo.name);
            $container.find(".select2-result-repository__description").text(repo.ciri_khas);
            $container.find(".select2-result-repository__forks").append(repo.binatang.name);
            $container.find(".select2-result-repository__stargazers").append(repo.ras.name);
            $container.find(".select2-result-repository__watchers").append(repo.branch.kode);

            return $container;
        } else {
            // scrolling can be used
            var markup = $('<span value=' + repo.id + '>' + repo.text + '</span>');

            return markup;
        }
    }

    function formatRepoHewanSelection(repo) {
        return repo.text || repo.text;
    }
</script>
