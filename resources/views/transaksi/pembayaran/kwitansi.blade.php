<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-INVOICE AMORE</title>
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
            <td>
                <img width="100" src="https://pjt-acm.amoreanimalclinic.com/dist/images/amoretext.png">
            </td>
            <td style="text-align: right;font-size: 24px">
                @if ($data->sisa_pelunasan != 0)
                    <b style="color: red;font-size: 24px">UNPAID</b>
                @else
                    <b style="color: green;font-size: 24px">PAID</b>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <br>
                <br>
            </td>
        </tr>
        <tr>
            <td>
                INVOICE
            </td>
        </tr>
        <tr>
            <td>
                <b>#{{ $data->kode }}</b>
            </td>
        </tr>
    </table>
    <br>
    <hr style="border: 0.1px solid grey">
    <table style="width: 100%">
        <tr>
            <td style="text-align: left"><b>Ditagihkan ke</b></td>
            <td style="text-align: right"><b>Dibayar Ke</b></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                <br>

                {{ $data->nama_owner }}<br>
                No. Registrasi :<br>
                {{ $data->owner ? $data->owner->kode ?? '-' : '-' }} <br>
                Alamat :<br>
                {{ $data->owner ? $data->owner->alamat : '-' }}<br>
                Telepon :<br>
                {{ $data->owner ? $data->owner->telpon : '-' }}<br>
            </td>
            <td style="text-align: right;vertical-align: top">
                <br>
                Amore Animal Clinic<br>
                Alamat :<br>
                {{ $data->Branch->alamat }}<br>
                Telepon :<br>
                {{ $data->Branch->telpon }}<br>
            </td>
        </tr>
    </table>
    <hr style="border: 0.1px solid grey">
    <table style="width: 100%">
        <tr>
            <td style="text-align: left"><b>Tanggal Invoice</b></td>
        </tr>
        <tr>
            <td style="text-align: left">{{ carbon\carbon::parse($data->i_tanggal)->format('d/m/Y') }}</td>
        </tr>
    </table>
    <br>
    <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <thead style="background: #f5f5f5">
            <tr>
                <th>NO</th>
                <th>NAME</th>
                <th>HARGA</th>
                <th>QUANTITY</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->KasirDetail->sortBy('id') as $i => $item)
                <tr>
                    <td style="text-align: center">{{ $i + 1 }}</td>
                    <td>
                        @if ($item->table == 'mp_rekam_medis_resep')
                            @php
                                $resep = \App\Models\RekamMedisResep::where('rekam_medis_pasien_id', $item->rekam_medis_pasien_id)
                                    ->where('id', $item->ref)
                                    ->first();
                                if ($resep) {
                                    if ($resep->jenis_obat == 'racikan') {
                                        echo $resep->KategoriObat->name;
                                    } else {
                                        echo $resep->ProdukObat->name;
                                    }
                                }
                            @endphp
                        @elseif($item->table == 'mka_kamar_rawat_inap_dan_bedah_detail')
                            @php
                                $kamar = \App\Models\KamarRawatInapDanBedahDetail::where('id', $item->ref)
                                    ->where('rekam_medis_pasien_id', $item->rekam_medis_pasien_id)
                                    ->first();
                                if ($kamar) {
                                    echo $kamar->KamarRawatInapDanBedah->name;
                                }
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
                                    ->where('id', $item->ref)
                                    ->first();
                                if ($tindakan) {
                                    echo $tindakan->Tindakan->name;
                                }
                            @endphp
                        @elseif($item->table == 'qm_pendaftaran')
                        Layanan Penjemputan
                        @endif
                    </td>
                    <td class="text-right">
                        {{ number_format($item->harga) }}
                    </td>
                    <td class="text-center"> {{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->sub_total) }}</td>
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
            <td style="padding: 0px 0px"><b style="font-size: 24px">Info Pembayaran</b></td>
        </tr>
        <tr>
            <td style="padding: 0px 0px">
                <br>
                Ditransfer ke :
            </td>
        </tr>
        <tr>
            <td>
                PT. JAWA PRATAMA MANDIRI
            </td>
        </tr>
        <tr>
            <td>
                BCA KCP Bhayangkara Surabaya <br>
                Jl.A. Yani Surabaya <br>
                A/C : <b>6100897979</b> <br>
                <br>
            </td>
        </tr>
    </table>
    <table style="width: 40%;border: 1px solid grey;border-radius: 5px;float: right" class="main">
        <tr>
            <td>Discount</td>
            <td style="text-align: right">
                {{ number_format($data->diskon + $data->diskon_penyesuaian) }}
            </td>
        </tr>
        <tr>
            <td>Deposit</td>
            <td style="text-align: right">
                {{ number_format($data->deposit) }}
            </td>
        </tr>
        <tr>
            <td>Total</td>
            <td style="text-align: right">
                {{ number_format($data->pembayaran) }}
            </td>
        </tr>
        @if ($data->sisa_pelunasan != 0)
            <tr>
                <td>Sisa Pelunasan</td>
                <td style="text-align: right;color:red">
                    {{ number_format($data->sisa_pelunasan) }}
                </td>
            </tr>
        @endif

    </table>
</body>

</html>
