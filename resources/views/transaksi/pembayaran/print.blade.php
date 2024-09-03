<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Invoice Amore</title>
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
            <td style="text-align: center;font-size: 24px;width: 50%">Amore Animal Clinic<br>{{ $data->Branch->lokasi }}
            </td>
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
            <td style="text-align: right"><b>Sales Invoice</b></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Nama Owner : {{ $data->nama_owner }} <br>
                <br>
                No. Registrasi : {{ $data->owner ? $data->owner->kode ?? '-' : '-' }} <br>
                <br>
                Telepon : {{ $data->owner ? $data->owner->telpon : '-' }}<br>
                <br>
                Komunitas :
                @if ($data->owner->komunitas == null)
                    -
                @else
                    {{ $data->owner->komunitas }}
                @endif
                <br><br>
                Nama Dokter :
                @if (!isset($data->kasirDetail[0]->rekamMedisPasien->createdBy->name))
                    -
                @else
                    {{ $data->kasirDetail[0]->rekamMedisPasien->createdBy->name }}
                @endif
            </td>
            <td style="text-align: right;vertical-align: top">
                No : {{ $data->kode }}<br>
                <br>
                Tanggal Invoice : {{ CarbonParse($data->created_at, 'd/m/Y H:i') }}<br>
                <br>
                Type : {{ $data ? $data->type_kasir : '-' }}<br>
                <br>
                Dicetak Oleh: {{ Auth::user()->name }}<br>
                <br>
                Tanggal Cetak: {{ carbon\carbon::now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>
    <hr style="border: 0.1px solid grey">
    <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <thead style="background: #f5f5f5">
            <tr>
                <th>NO</th>
                <th>PASIEN</th>
                <th>ITEM</th>
                <th>HARGA</th>
                <th>QTY</th>
                <th>HARGA SEBELUM DISKON</th>
                <th>DISKON %</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->kasirDetail->sortBy('id') as $i => $item)
                <tr>
                    <td style="text-align: center">{{ $i + 1 }}</td>
                    <td style="text-align: center">{{ $item->pasien ? $item->pasien->name : '-' }}</td>
                    <td class="text-left">
                        @if ($item->table == 'mp_rekam_medis_resep')
                            @php
                                $resep = \App\Models\RekamMedisResep::where('rekam_medis_pasien_id', $item->rekam_medis_pasien_id)
                                    ->where('id', $item->ref)
                                    ->first();
                                if ($resep) {
                                    if ($resep->jenis_obat == 'racikan') {
                                        echo $resep->KategoriObat->name;
                                    } else {
                                        echo $resep->ProdukObat->KategoriObat->name;
                                    }
                                } else {
                                    echo 'Data Master Terhapus';
                                }
                            @endphp
                        @elseif($item->table == 'mka_kamar_rawat_inap_dan_bedah_detail')
                            @php
                                // $kamar = \App\Models\KamarRawatInapDanBedahDetail::where('id', $item->ref)
                                //     ->where('rekam_medis_pasien_id', $item->rekam_medis_pasien_id)
                                //     ->first();
                                // if ($kamar) {
                                //     echo $kamar->KamarRawatInapDanBedah->name;
                                // }
                                echo 'Ruang Rawat Inap';
                            @endphp
                        @elseif($item->table == 'mp_rekam_medis_tindakan')
                            @php
                                $tindakan = \App\Models\RekamMedisTindakan::where('rekam_medis_pasien_id', $item->rekam_medis_pasien_id)
                                    ->where('id', $item->ref)
                                    ->first();
                                if ($tindakan) {
                                    echo $tindakan->Tindakan->name;
                                }
                            @endphp
                        @elseif($item->table == 'mp_rekam_medis_pakan')
                            @php
                                $itemNonObat = \App\Models\RekamMedisPakan::where('rekam_medis_pasien_id', $item->rekam_medis_pasien_id)
                                    ->where('id', $item->ref)
                                    ->first();
                                if ($itemNonObat) {
                                    echo $itemNonObat->ItemNonObat->name . ' (Rawat Inap)';
                                }
                            @endphp
                        @elseif($item->table == 'ms_item_non_obat')
                            @php
                                $itemNonObat = \App\Models\ItemNonObat::where('id', $item->ref)->first();
                                if ($itemNonObat) {
                                    echo $itemNonObat->name;
                                }
                            @endphp
                        @elseif($item->table == 'grooming')
                            @php
                                $grooming = \App\Models\RekamMedisPasien::where('id', $item->rekam_medis_pasien_id)->first();
                                if ($grooming) {
                                    echo $grooming->JenisGrooming->name;
                                }
                            @endphp
                        @elseif($item->table == 'mp_rekam_medis_tindakan_bedah')
                            @php
                                $tindakan = \App\Models\RekamMedisRekomendasiTindakanBedah::where('rekam_medis_pasien_id', $item->rekam_medis_pasien_id)
                                    ->where('tindakan_id', $item->ref)
                                    ->first();
                                if ($tindakan) {
                                    echo $tindakan->Tindakan->name;
                                } else {
                                    $tindakan = \App\Models\Tindakan::where('id', $item->ref)->first();
                                    echo $tindakan->name;
                                }
                            @endphp
                        @elseif($item->table == 'pasien_meninggal')
                            Pemakaman
                        @elseif($item->table == 'qm_pendaftaran')
                            Layanan Penjemputan
                        @elseif ($item->table == 'mo_produk_obat')
                            @php
                                $produkObat = \App\Models\ProdukObat::find($item->ref);
                                if ($produkObat) {
                                    echo $produkObat->name;
                                }
                            @endphp
                        @elseif ($item->table == 'mo_kategori_obat')
                            @php
                                $kategoriObat = \App\Models\KategoriObat::find($item->ref);
                                if ($kategoriObat) {
                                    echo $kategoriObat->name;
                                }
                            @endphp
                        @elseif ($item->table == 'mk_tindakan')
                            @php
                                $tindakan = \App\Models\Tindakan::find($item->ref);
                                if ($tindakan) {
                                    echo $tindakan->name;
                                }
                            @endphp
                        @endif
                    </td>
                    <td class="text-right">
                        Rp. {{ number_format($item->harga) }}
                    </td>
                    <td class="text-center">
                        {{ number_format($item->qty) }}
                    </td>
                    <td class="text-center">
                        Rp. {{ number_format($item->harga * $item->qty) }}
                    </td>
                    <td class="text-center">
                        {{ number_format($item->diskon_penyesuaian) }}
                    </td>
                    <td class="text-right">Rp. {{ number_format($item->sub_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <tr>
            <td>Terbilang : {{ terbilang($data->pembayaran) }} rupiah</td>

            </td>
        </tr>
    </table>
    <br>
    <table style="width: 60%;float: left">
        <tr>
            <td style="padding: 0px 0px"><b style="font-size: 14px">Dibuat Oleh</b></td>
        </tr>
        <tr>
            <td style="padding: 0px 0px;font-size: 14px">
                <br>
                <br>
                <br>
                <br>
                <br>
                {{ $data->CreatedBy->Karyawan ? $data->CreatedBy->Karyawan->name : $data->CreatedBy->name }}
            </td>
        </tr>
    </table>
    <table style="width: 60%;float: right">
        <tr>
            <td>
                <table style="width: 100%;border: 1px solid grey;border-radius: 10px;" class="main">
                    <tr>
                        <td>Total Bruto</td>
                        <td style="text-align: right">
                            Rp.
                            {{ number_format($data->KasirDetail->sum('nilai_diskon_penyesuaian') + $data->pembayaran) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Total Diskon</td>
                        <td style="text-align: right">
                            Rp. {{ number_format($data->KasirDetail->sum('nilai_diskon_penyesuaian')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Total Tagihan</td>
                        <td style="text-align: right">
                            Rp. {{ number_format($data->pembayaran) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Total {{ ucwords(strtolower($data->metode_pembayaran)) }}</td>
                        <td style="text-align: right">
                            Rp. {{ number_format($data->diterima) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Uang Kembali</td>
                        <td style="text-align: right">
                            Rp. {{ number_format($data->uang_kembali) }}
                        </td>
                    </tr>
                    @if ($data->sisa_pelunasan == 0)
                        <tr>
                            <td>Metode Pembayaran</td>
                            <td style="text-align: right">
                                {{ ucwords(strtolower($data->metode_pembayaran)) }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>Metode Pembayaran</td>
                            <td style="text-align: right">
                                Cicilan
                            </td>
                        </tr>
                    @endif
                    @if ($data->deposit != 0)
                        <tr>
                            <td>Penggunaan Deposit</td>
                            <td style="text-align: right;color:red">    
                                Rp. {{ number_format($data->deposit) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Penarikan Deposit</td>
                            <td style="text-align: right;color:red">
                                Rp. {{ number_format($data->penarikan_deposit) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Sisa Deposit</td>
                            <td style="text-align: right;color:red">
                                Rp.
                                {{ number_format($data->owner ? ($data->owner->deposit ? $data->owner->deposit->sisa_deposit : 0) : 0) }}
                            </td>
                        </tr>
                    @endif
                    @if ($data->sisa_pelunasan != 0)
                        <tr>
                            <td>Sisa Tagihan</td>
                            <td style="text-align: right;color:red">
                                Rp. {{ number_format($data->sisa_pelunasan) }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table style="width: 50%;float: right">
                    <tr>
                        <td>
                    <tr>
                        <td style="padding: 0px 0px"><b style="font-size: 24px">Info Pembayaran</b></td>
                    </tr>
                    <tr>
                        <td>
                            @foreach (rekening() as $item)
                                {{ $item->bank }} {{ $item->no_rekening }} <br>
                                a/n {{ $item->name }}
                                <br>
                                <br>
                            @endforeach

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
