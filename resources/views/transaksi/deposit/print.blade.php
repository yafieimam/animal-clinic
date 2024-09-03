<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bukti Deposit</title>
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
            <td style="text-align: center;font-size: 24px;width: 50%">Amore Animal Clinic<br> {{ $data->Owner->Branch->lokasi }}
            </td>
            <td style="width: 25%;text-align: right">
                {{ $data->Owner->Branch->alamat }}<br>
                {{ $data->Owner->Branch->telpon }}
            </td>
        </tr>
    </table>
    <br>
    <hr style="border: 0.1px solid grey">
    <table style="width: 100%">
        <tr>
            <td style="text-align: left"><b>&nbsp;</b></td>
            <td style="text-align: right"><b>Bukti Deposit</b></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Nama Owner : {{ $data->Owner->name }} <br>
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
            </td>
            <td style="text-align: right;vertical-align: top">
                No : {{ $data->kode }}<br>
                <br>
                Tanggal Cetak : {{ CarbonParse($data->tanggal, 'd-M-Y') }}<br>
                <br>
                {{-- Dicetak Oleh: {{ $data->CreatedBy->Karyawan ? $data->CreatedBy->Karyawan->name : $data->CreatedBy->name }} --}}
                Dicetak Oleh: {{ Auth::user()->name }}

            </td>
        </tr>
    </table>
    <hr style="border: 0.1px solid grey">
    <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <thead style="background: #f5f5f5">
            <tr>
                <th>KETERANGAN</th>
                <th>JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <td style="text-align: center">{{ $data->keterangan }}</td>
            <td style="text-align: center">Rp. {{ number_format($data->nilai_deposit) }}</td>
        </tbody>
    </table>
    <br>
    {{-- <table style="width: 100%;border: 1px solid grey;border-radius: 5px;" class="main">
        <tr>
            <td>Terbilang : {{ terbilang($data->pembayaran) }} rupiah</td>

            </td>
        </tr>
    </table> --}}
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
                        <td>Total Deposit</td>
                        <td style="text-align: right">
                            Rp. {{ number_format($data->nilai_deposit) }}
                        </td>
                    </tr>               
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
