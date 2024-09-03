<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bukti Pembayaran Amore</title>
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

        td {
            font-size: 18px;
        }

        footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2cm;
            background-color: red;
            color: white;
            text-align: left;
            background-color: #991b1b;
            padding: 1rem;
            vertical-align: middle
        }

        #trapezoid {
            border-top-left-radius: 20px;
            background: red;
            padding-right: 0.5rem;
            color: white
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

                <b style="color: rgb(85, 89, 89);font-size: 24px">BUKTI PENERIMAAN DEPOSIT</b>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <br>
                <br>
            </td>
        </tr>
    </table>
    <br>
    <hr style="border: 0.1px solid grey">
    <table style="width: 70%;">
        <tr>
            <td colspan="2" style="text-align: left;"><b style="font-size:18px !important">Pembayaran Dari</b></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Nama Customer
            </td>
            <td>
                : {{ $data->Owner->name }}
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                No. Registrasi
            </td>
            <td>
                : {{ $data->Owner->kode }}
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Alamat
            </td>
            <td>
                : {{ $data->Owner->alamat }}
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Telepon
            </td>
            <td>
                : {{ $data->Owner->telpon }}
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Tanggal
            </td>
            <td>
                : {{ CarbonParse($data->created_at, 'd/m/Y') }}
            </td>
        </tr>
    </table>
    <hr style="border: 0.1px solid grey">
    <br>
    @php
        $total = 0;
    @endphp
    <br>
    <p style="font-size: 18px;">Telah diterima uang sebesar <b
            style="font-size: 18px;">{{ number_format($data->nilai_deposit) }}</b> yang
        telah didepositkan ke pihak
        Amore Animal Clinic
    </p>
    <table style="width: 100%;">
        <td style="width:50%"></td>
        <td style="width:15%;" class="text-right">
            Jumlah&nbsp;
        </td>
        <td style="width:35%;" class="text-right" id="trapezoid">
            <b style="font-size: 18px;">{{ number_format($data->nilai_deposit) }}</b>
        </td>
    </table>
    <table style="width: 100%;margin-top:5cm">
        <td style="width:70%"></td>
        <td style="width:30%;border:1px solid black;height: 100px;vertical-align: bottom;text-align: center">
            Kasir
        </td>
    </table>
    <footer>
        <div style="width: 100%;">
            <table style="width: 100%">
                <tr>
                    <td style="width: 50px" rowspan="3">
                        <img style="height: 2cm;display: inline-block"
                            src="https://pjt-acm.amoreanimalclinic.com/dist/images/amoreboxy.svg">
                    </td>
                    <td>
                        <b style="color: white;display: inline-block;font-size:14px;">Amore Animal Clinic Cabang
                            {{ $data->Owner->Branch->kode }}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b
                            style="color: white;display: inline-block;font-size:14px;">{{ $data->Owner->Branch->alamat }}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b
                            style="color: white;display: inline-block;font-size:14px;">{{ $data->Owner->Branch->telpon }}</b>
                    </td>
                </tr>
            </table>
        </div>
    </footer>
</body>

</html>
