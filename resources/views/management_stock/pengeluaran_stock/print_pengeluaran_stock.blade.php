<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('assets/images/amore.png') }}" type="image/x-icon"> <!-- Favicon-->
    <title>Print {{ $data->kode }}</title>
    <style>
        * {
            font-family: Arial, sans-serif;
        }

        @page {
            margin: 0px 2rem !important;
            padding: 0px 0px 0px 0px !important;
            size: 210mm 297mm
        }

        th {
            vertical-align: middle;
        }

        .item td,
        th {
            padding: 5px 10px;
        }

        .text-center {
            text-align: center;
        }

    </style>
</head>

<body>
    <table style="width: 100%">
        <tr style="vertical-align: top">
            <td style="width: 25%;padding-top: 1rem">
                <img src="https://pjt-acm.amoreanimalclinic.com/dist/images/amoretext.png" style="width: 50%">
            </td>
            <td style="width: 50%; text-align: center;vertical-align: top;padding-top: 1rem">
                <h3 style="margin-bottom: 0px;padding: 0px ;margin: 0px"><b>AMORE ANIMAL CLINIC</b></h3>
                <span style="font-size: 12px;text-align: center">
                    Jl. Pejaten Raya Blok A. No. 21. Pejaten Barat.<br>
                    Villa Pejaten Mas, Pasar Minggu<br>
                    Jakarta Selatan<br>
                </span>
            </td>
            <td style="width: 25%;padding-top: 1rem">
                <table style="100%;font-size:12px">
                    <tr>
                        <td>No Faktur </td>
                        <td>:</td>
                        <td>{{ $data->kode }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal </td>
                        <td>:</td>
                        <td>{{ CarbonParse($data->tanggal_pengeluaran, 'd M Y') }}</td>
                    </tr>
                    <tr>
                        <td>Jenis </td>
                        <td>:</td>
                        <td>{{ ucwords(Str::lower($data->jenis)) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr>
    <h4 style="text-align: center;margin: 5px 0px"><b>FAKTUR PENGELUARAN OBAT</b></h4>
    <hr>
    <table style="100%;">
        <tr>
            <td>Dikirim Dari </td>
            <td>:</td>
            <td>Amore {{ $data->Branch->lokasi }}</td>
        </tr>
        <tr>
            <td>Dikirim Kepada </td>
            <td>:</td>
            <td>Amore {{ $data->BranchTujuan->lokasi }}</td>
        </tr>
        <tr>
            <td>Jenis </td>
            <td>:</td>
            <td>{{ ucwords(Str::lower($data->jenis)) }}</td>
        </tr>
    </table>
    <br>
    <table class="item" style="width: 100%;font-size: 12px; border-collapse: collapse" border="1">
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA OBAT/ITEM</th>
                <th>KATEGORI</th>
                <th>JUMLAH</th>
                <th>SATUAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->PengeluaranStockDetail as $i => $item)
                <tr>
                    <td style="text-align: center">{{ $i + 1 }}</td>
                    <td>
                        @if ($item->ProdukObat)
                            {{ $item->ProdukObat->name }}
                        @else
                            {{ $item->ItemNonObat->name }}
                        @endif
                    </td>
                    <td>
                        @if ($item->ProdukObat)
                            {{ $item->ProdukObat->KategoriObat->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $item->qty }}
                    </td>
                    <td class="text-center">
                        @if ($item->ProdukObat)
                            {{ $item->ProdukObat->Satuan->name }}
                        @else
                            {{ $item->ItemNonObat->Satuan->name }}
                        @endif
                    </td>
                </tr>
            @endforeach
            @for ($i = 0; $i < 10 - count($data->pengeluaranStockDetail); $i++)
                <tr>
                    <td style="text-align: center">{{ count($data->pengeluaranStockDetail) + $i + 1 }}</td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td class="text-center">
                    </td>
                    <td class="text-center">

                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
    <table style="width: 100%;margin-top: 20px">
        <tr>
            <td style="text-align: left;height: 100px;vertical-align: middle" colspan="2">
                Jakarta, {{ carbon\carbon::parse($data->created_at)->format('d-F-Y') }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center;vertical-align: top;height: 100px;width: 50%">
                <b>PENGIRIM</b>
            </td>
            <td style="text-align: center;vertical-align: top;height: 100px;width: 50%">
                <b>PENERIMA</b>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;vertical-align: bottom">
                {{ $data->CreatedBy->name }}
            </td>
            <td style="text-align: center;vertical-align: bottom">
                NAMA APOTEKER PENERIMA
            </td>
        </tr>
    </table>
</body>

</html>
