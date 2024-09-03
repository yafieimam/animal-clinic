<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tagihan Sementara</title>
    <style>
        * {
            font-family: Gotham, sans-serif;
            font-size: 12px;
        }

        th {
            border: 1 px solid #f5f5f5;
            padding: 10px 10px;
        }

        .main td {
            padding: 10px 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <table style="width: 100%">
        <tr>
            <td style="width: 25%;">
                <img width="100" src="https://pjt-acm.amoreanimalclinic.com/dist/images/amoretext.png">
            </td>
            <td style="text-align: center;font-size: 24px;width: 50%">Amore Animal Clinic</td>
            <td style="width: 25%;text-align: right">
                {{ $data->Branch->alamat }}<br>
                {{ $data->Branch->telpon }}
            </td>
        </tr>
    </table>
    <br>
    <hr style="border: 0.1px solid grey">
    <table style="width: 100%">
        <tr>
            <td style="text-align: left"><b>&nbsp;</b></td>
            <td style="text-align: right"><b>Tagihan Sementara</b></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Nama Owner : {{ $data->name }} <br>
                <br>
                No. Registrasi : {{ $data ? $data->kode ?? '-' : '-' }} <br>
                <br>
                Telepon : {{ $data ? $data->telpon : '-' }}<br>
            </td>
            <td style="text-align: right;vertical-align: top">
                No : -<br>
                <br>
                Tanggal Invoice : {{ CarbonParse(now(), 'd/m/Y') }}<br>
                <br>
                Dicetak Oleh: {{ Auth::user()->name }}
            </td>
        </tr>
    </table>
    <hr style="border: 0.1px solid grey">
    <br>
    <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <thead style="background: #f5f5f5">
            <tr>
                <th>NO</th>
                <th>NAME</th>
                <th>PASIEN</th>
                <th>HARGA</th>
                <th>QUANTITY</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $isNull = 0;
                $index = 1;
            @endphp

            @foreach ($data->pasien as $d)
                @foreach ($d->rekamMedisPasien as $d1)
                    @foreach ($d1->rekamMedisResep as $item)
                        <tr>
                            <td class="text-center">
                                @php
                                    echo $index;
                                    $index++;
                                @endphp
                            </td>
                            <td>
                                @if ($item->jenis_obat == 'non-racikan')
                                    {{ $item->ProdukObat->name }}
                                @else
                                    {{ $item->KategoriObat->name }} Racikan
                                @endif
                            </td>
                            <td>
                                {{ $d->name }}
                            </td>
                            <td class="text-right">
                                @if ($item->jenis_obat == 'non-racikan')
                                    Rp. {{ number_format($item->ProdukObat->harga) }}
                                    <input type="hidden" name="harga[]" value="{{ $item->ProdukObat->harga }}">
                                @else
                                    @php
                                        $rule = \App\Models\RuleResepRacikan::where('kategori_obat_id', $item->kategori_obat_id)
                                            ->where('satuan', 'BERAT')
                                            ->where('min', '<=', round($d->berat))
                                            ->where('max', '>=', round($d->berat))
                                            ->first();
                                        if ($rule) {
                                            $harga = $rule->harga;
                                            $total += $harga;
                                        } else {
                                            $harga = 0;
                                        }
                                    @endphp
                                    Rp. {{ number_format($harga) }}
                                    <input type="hidden" name="harga[]" value="{{ $harga }}">
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $item->qty ? $item->qty : 1 }}
                                <input type="hidden" name="qty[]" class="qty"
                                    value="{{ $item->qty ? $item->qty : 1 }}">
                            </td>
                            <td class="text-right">
                                @if ($item->jenis_obat == 'non-racikan')
                                    @php
                                        $total += $item->ProdukObat->harga * $item->qty;
                                    @endphp
                                    Rp. {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                    <input type="hidden" name="sub_total[]" class="sub_total"
                                        value="{{ $item->ProdukObat->harga * $item->qty }}">
                                @else
                                    Rp. {{ number_format($harga) }}
                                    <input type="hidden" name="sub_total[]" class="sub_total"
                                        value="{{ $harga }}">
                                @endif
                            </td>
                        </tr>
                        @php
                            $isNull++;
                        @endphp
                    @endforeach

                    @foreach ($d1->rekamMedisResepRacikan as $item)
                        <tr>
                            <td class="text-center">
                                @php
                                    echo $index;
                                    $index++;
                                @endphp
                            </td>
                            <td>
                                @if ($item->jenis_obat == 'non-racikan')
                                    {{ $item->ProdukObat->name }}
                                @else
                                    {{ $item->KategoriObat->name }} Racikan
                                @endif
                            </td>
                            <td>
                                {{ $d->name }}
                            </td>
                            <td class="text-right">
                                @if ($item->jenis_obat == 'non-racikan')
                                    Rp. {{ number_format($item->ProdukObat->harga) }}
                                    <input type="hidden" name="harga[]" value="{{ $item->ProdukObat->harga }}">
                                @else
                                    @php
                                        $rule = \App\Models\RuleResepRacikan::where('kategori_obat_id', $item->kategori_obat_id)
                                            ->where('satuan', 'BERAT')
                                            ->where('min', '<=', round($d->berat))
                                            ->where('max', '>=', round($d->berat))
                                            ->first();
                                        if ($rule) {
                                            $harga = $rule->harga;
                                            $total += $harga * $item->qty;
                                        } else {
                                            $harga = 0;
                                        }
                                    @endphp
                                    Rp. {{ number_format($harga) }}
                                    <input type="hidden" name="harga[]" value="{{ $harga }}">
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $item->qty ? $item->qty : 1 }}
                                <input type="hidden" name="qty[]" class="qty"
                                    value="{{ $item->qty ? $item->qty : 1 }}">
                            </td>
                            <td class="text-right">
                                @if ($item->jenis_obat == 'non-racikan')
                                    @php
                                        $total += $item->ProdukObat->harga * $item->qty;
                                    @endphp
                                    Rp. {{ number_format($item->ProdukObat->harga * $item->qty) }}
                                    <input type="hidden" name="sub_total[]" class="sub_total"
                                        value="{{ $item->ProdukObat->harga * $item->qty }}">
                                @else
                                    Rp. {{ number_format($harga * $item->qty) }}
                                    <input type="hidden" name="sub_total[]" class="sub_total"
                                        value="{{ $harga * $item->qty }}">
                                @endif
                            </td>
                        </tr>
                        @php
                            $isNull++;
                        @endphp
                    @endforeach
                @endforeach
            @endforeach

            @foreach ($data->pasien as $d)
                @foreach ($d->rekamMedisPasien as $d1)
                    @if (count($d1->KamarRawatInapDanBedahDetail) != 0)
                        @php
                            $qtyHari = 0;
                            $kamar;
                            foreach ($d1->KamarRawatInapDanBedahDetail as $i => $item) {
                                $tanggalMasuk = carbon\carbon::parse($item->tanggal_masuk);
                                $tanggalKeluar = carbon\carbon::parse($item->tanggal_keluar);
                                $tempHari = carbon\Carbon::parse($tanggalMasuk)->diffInDays($tanggalKeluar);

                                if ($tempHari < 1) {
                                    $tempHari = 1;
                                }

                                if ($item->status_pindah) {
                                    // $tempHari--;
                                } else {
                                    $kamar = $item;
                                }

                                if ($item->status == 'Done') {
                                    // $tempHari--;
                                }

                                $qtyHari += $tempHari;
                                $total += $item->KamarRawatInapDanBedah->tarif_per_hari * $tempHari;
                            }
                        @endphp
                        <tr class="item-lain-lain">
                            <td class="text-center">
                                @php
                                    echo $index;
                                    $index++;
                                @endphp
                            </td>
                            <td>Ruang Rawat Inap {{ $kamar->KamarRawatInapDanBedah->name }}</td>
                            <td>
                                {{ $d->name }}
                            </td>
                            <td class="text-right">
                                Rp. {{ number_format($kamar->KamarRawatInapDanBedah->tarif_per_hari) }}
                                <input type="hidden" name="harga[]"
                                    value="{{ $kamar->KamarRawatInapDanBedah->tarif_per_hari }}">
                            </td>
                            <td class="text-center">
                                {{ $qtyHari }}
                                <input type="hidden" name="qty[]" class="qty" value="{{ $qtyHari }}">
                            </td>
                            <td class="text-right">
                                Rp. {{ number_format($kamar->KamarRawatInapDanBedah->tarif_per_hari * $qtyHari) }}
                                <input type="hidden" name="sub_total[]" class="sub_total"
                                    value="{{ $kamar->KamarRawatInapDanBedah->tarif_per_hari * $qtyHari }}">
                            </td>
                        </tr>
                        @php
                            $isNull++;
                        @endphp
                    @endif
                @endforeach
            @endforeach

            @foreach ($data->pasien as $d)
                @foreach ($d->rekamMedisPasien as $d1)
                    @foreach ($d1->rekamMedisTindakan as $item)
                        <tr class="item-lain-lain">
                            <td class="text-center">
                                @php
                                    echo $index;
                                    $index++;
                                @endphp
                            </td>
                            <td>{{ $item->Tindakan->name }}</td>
                            <td>
                                {{ $d->name }}
                            </td>
                            <td class="text-right">
                                Rp. {{ number_format($item->Tindakan->tarif) }}
                                <input type="hidden" name="harga[]" value="{{ $item->Tindakan->tarif }}">
                            </td>
                            <td class="text-center">
                                {{ $item->qty }}
                                <input type="hidden" name="qty[]" class="qty" value="{{ $item->qty }}">
                            </td>
                            <td class="text-right">
                                Rp. {{ number_format($item->Tindakan->tarif * $item->qty) }}
                                @php
                                    $total += $item->Tindakan->tarif * $item->qty;
                                @endphp
                                <input type="hidden" name="sub_total[]" class="sub_total"
                                    value="{{ $item->Tindakan->tarif * $item->qty }}">
                            </td>
                        </tr>
                        @php
                            $isNull++;
                        @endphp
                    @endforeach
                @endforeach
            @endforeach

            @foreach ($data->pasien as $d)
                @foreach ($d->rekamMedisPasien as $d1)
                    @foreach ($d1->rekamMedisRekomendasiTindakanBedah as $item)
                        <tr class="item-lain-lain">
                            <td class="text-center">
                                @php
                                    echo $index;
                                    $index++;
                                @endphp
                            </td>
                            <td>{{ $item->Tindakan->name }}</td>
                            <td>
                                {{ $d->name }}
                            </td>
                            <td class="text-right">
                                Rp. {{ number_format($item->Tindakan->tarif) }}
                                @php
                                    $total += $item->Tindakan->tarif * $item->qty;
                                @endphp
                                <input type="hidden" name="harga[]" value="{{ $item->Tindakan->tarif }}">
                            </td>
                            <td class="text-center">
                                {{ $item->qty }}
                                <input type="hidden" name="qty[]" class="qty" value="{{ $item->qty }}">
                            </td>
                            <td class="text-right">
                                Rp. {{ number_format($item->Tindakan->tarif * $item->qty) }}
                                <input type="hidden" name="sub_total[]" class="sub_total"
                                    value="{{ $item->Tindakan->tarif * $item->qty }}">
                            </td>
                        </tr>
                        @php
                            $isNull++;
                        @endphp
                    @endforeach
                @endforeach
            @endforeach

            @foreach ($data->pasien as $d)
                @foreach ($d->rekamMedisPasien as $d1)
                    @foreach ($d1->rekamMedisPakan as $item)
                        <tr>
                            <td class="text-center">
                                @php
                                    echo $index;
                                    $index++;
                                @endphp
                            </td>
                            <td>
                                {{ $item->ItemNonObat->name }} (Rawat Inap)
                            </td>
                            <td>
                                {{ $d->name }}
                            </td>
                            <td class="text-right">
                                Rp. {{ number_format($item->ItemNonObat->harga) }}
                                <input type="hidden" name="harga[]" value="{{ $item->ItemNonObat->harga }}">
                            </td>
                            <td class="text-center">
                                {{ $item->jumlah ? $item->jumlah : 1 }}
                                <input type="hidden" name="qty[]" class="qty"
                                    value="{{ $item->jumlah ? $item->jumlah : 1 }}">
                            </td>
                            <td class="text-right">
                                @php
                                    $total += $item->ItemNonObat->harga * $item->jumlah;
                                @endphp
                                Rp. {{ number_format($item->ItemNonObat->harga * $item->jumlah) }}
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

            @foreach ($data->pasien as $d)
                @foreach ($d->rekamMedisPasien->where('grooming', true) as $item)
                    <tr>
                        <td class="text-red text-center">
                            <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                        </td>
                        <td>
                            {{ $item->JenisGrooming->name }}
                        </td>
                        <td>
                            {{ $d->name }}
                        </td>
                        <td class="text-right">
                            Rp. {{ number_format($item->JenisGrooming->tarif) }}
                            <input type="hidden" name="harga[]" value="{{ $item->JenisGrooming->tarif }}">
                        </td>
                        <td class="text-center">
                            1
                            <input type="hidden" name="qty[]" class="qty" value="1">
                        </td>
                        <td class="text-right">
                            @php
                                $total += $item->JenisGrooming->tarif * 1;
                            @endphp
                            Rp. {{ number_format($item->JenisGrooming->tarif * 1) }}
                            <input type="hidden" name="sub_total[]" class="sub_total"
                                value="{{ $item->JenisGrooming->tarif * 1 }}">
                        </td>
                    </tr>
                    @php
                        $isNull++;
                    @endphp
                @endforeach
            @endforeach

            @foreach ($penjemputan as $item)
                <tr>
                    <td class="text-red text-center">
                        <i class="fa fa-ban text-red-500" aria-hidden="true"></i>
                    </td>
                    <td style="width: 40%">
                        Layanan Penjemputan
                        <input type="hidden" name="table[]" value="qm_pendaftaran" class="table">
                        <input type="hidden" name="ref[]" value="{{ $item->id }}" class="ref">
                        <input type="hidden" name="stock[]" value="TIDAK" class="stock">
                        <input type="hidden" name="jenis_stock[]" value="PICKUP" class="jenis_stock">
                        <input type="hidden" name="rekam_medis_pasien_id[]" value="{{ $item->id }}"
                            class="rekam_medis_pasien_id">
                        <input type="hidden" name="pasiens_id[]" value="{{ $item->pasien_id }}"
                            class="pasiens_id">
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
                        <input type="hidden" name="diskon_penyesuaian[]" class="diskon_penyesuaian" value="0">
                        <input type="hidden" name="nilai_diskon_penyesuaian[]" class="nilai_diskon_penyesuaian"
                            value="0">
                        <input type="hidden" name="bruto[]" class="bruto" value="0">
                        <input type="hidden" name="sub_total[]" class="sub_total" value="0">
                    </td>
                </tr>
                @php
                    $isNull++;
                @endphp
            @endforeach

        </tbody>
    </table>
    <br>
    <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <tr>
            <td>Terbilang : {{ terbilang($total) }} rupiah</td>

            </td>
        </tr>
    </table>
    <br>
    <table style="width: 40%;border: 1px solid grey;border-radius: 5px;float: right" class="main">
        <tr>
            <td>Total</td>
            <td style="text-align: right">
                Rp. {{ number_format($total) }}
            </td>
        </tr>
    </table>
</body>

</html>
