<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PEMBAYARAN CICILAN AMORE</title>
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
                {{ $data->Kasir->Branch->alamat }}
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
                Nama Owner : {{ $data->Kasir->nama_owner }} <br>
                <br>
                No. Registrasi : {{ $data->Kasir->owner ? $data->Kasir->owner->kode ?? '-' : '-' }} <br>
                <br>
                Telepon : {{ $data->Kasir->owner ? $data->Kasir->owner->telpon : '-' }}<br>
            </td>
            <td style="text-align: right;vertical-align: top">
                No : {{ $data->Kasir->kode }}<br>
                <br>
                Tanggal Transaksi : {{ CarbonParse($data->created_at, 'd/m/Y') }}<br>
            </td>
        </tr>
    </table>
    <hr style="border: 0.1px solid grey">
    <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <thead style="background: #f5f5f5">
            <tr>
                <th>NO</th>
                <th style="text-align: left">KETERANGAN</th>
                <th style="text-align: left">PEMBAYARAN</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center">1</td>
                <td>Pembayaran Angsuran Ke {{ $data->id }}</td>
                <td>No Invoice {{ $data->Kasir->kode }}</td>
                <td style="text-align: right"> {{ number_format($data->nilai_pembayaran) }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <tr>
            <td>Terbilang : {{ terbilang($data->nilai_pembayaran) }} rupiah</td>

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
                {{ $data->CreatedBy->name }}
            </td>
        </tr>
    </table>
    <table style="float: right">
        <tr>
            <td>
                <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
                    <tr>
                        <td>Total Hutang</td>
                        <td style="text-align: right">
                            {{ number_format($totalHutang) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Total Bayar</td>
                        <td style="text-align: right">
                            {{ number_format($data->nilai_pembayaran) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Sisa Hutang</td>
                        <td style="text-align: right;color:red">
                            {{ number_format($totalHutang - $data->nilai_pembayaran) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
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
