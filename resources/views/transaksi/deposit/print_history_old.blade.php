<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BUKTI
        @if ($data->jenis_deposit == 'DEBET')
            PENERIMAAN
        @else
            PENARIKAN
        @endif DEPOSIT
    </title>
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

                <b style="color: rgb(85, 89, 89);font-size: 24px">Bukti
                    @if ($data->jenis_deposit == 'DEBET')
                        Penerimaan
                    @else
                        Penarikan
                    @endif

                    Deposit
                </b>
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
            <td colspan="2" style="text-align: left;">
                @if ($data->jenis_deposit == 'DEBET')
                    <b style="font-size:18px !important">Deposit Dari</b>
                @else
                    <b style="font-size:18px !important">Penarikan Oleh</b>
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Nama
            </td>
            <td>
                : {{ $data->Deposit->Owner->name }}
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                No. Registrasi
            </td>
            <td>
                : {{ $data->Deposit->Owner->kode }}
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Alamat
            </td>
            <td>
                : {{ $data->Deposit->Owner->alamat }}
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Telepon
            </td>
            <td>
                : {{ $data->Deposit->Owner->telpon }}
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Tanggal
            </td>
            <td>
                : {{ CarbonParse($data->created_at, 'd-M-Y') }}
            </td>
        </tr>
    </table>
    <hr style="border: 0.1px solid grey">
    <br>
    <table style="width: 70%;">
        <tr>
            <td colspan="2" style="text-align: left;">
                <b style="font-size:18px !important">Pembayaran Dengan</b>
            </td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: top">
                Metode Pembayaran
            </td>
            <td>
                : {{ $data->metode_pembayaran }}
            </td>
        </tr>
        @if ($data->metode_pembayaran != 'TUNAI')
            <tr>
                <td style="text-align: left;vertical-align: top">
                    Nama Bank
                </td>
                <td>
                    : {{ $data->nama_bank }}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align: top">
                    No Rekening
                </td>
                <td>
                    : {{ $data->nomor_kartu }}
                </td>
            </tr>
        @endif


    </table>
    <hr style="border: 0.1px solid grey">
    <br>
    @php
        $total = 0;
    @endphp
    <br>
    @if ($data->jenis_deposit == 'DEBET')
        <p style="font-size: 18px;">Telah diterima uang sebesar
            <b style="font-size: 18px;">{{ number_format($data->nilai) }}</b>
            yang telah didepositkan ke pihak
            Amore Animal Clinic
        </p>
    @else
        <p style="font-size: 18px;">Penarikan uang sebesar
            <b style="font-size: 18px;">{{ number_format($data->nilai) }}</b>
            yang sebelumnya di depositkan ke pihak
            Amore Animal Clinic
        </p>
    @endif
    <table style="width: 100%;">
        <td style="width:50%"></td>
        <td style="width:15%;" class="text-right">
            Jumlah&nbsp;
        </td>
        <td style="width:35%;" class="text-right" id="trapezoid">
            <b style="font-size: 18px;">{{ number_format($data->nilai) }}</b>
        </td>
    </table>
    <table style="width: 100%;margin-top:2cm">
        <td style="width:70%"></td>
        <td style="width:30%;border:1px solid black;height: 100px;vertical-align: bottom;text-align: center">
            {{ $data->CreatedBy->name }}
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
                            {{ $data->Deposit->Owner->Branch->kode }}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b
                            style="color: white;display: inline-block;font-size:14px;">{{ $data->Deposit->Owner->Branch->alamat }}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b
                            style="color: white;display: inline-block;font-size:14px;">{{ $data->Deposit->Owner->Branch->telpon }}</b>
                    </td>
                </tr>
            </table>
        </div>
    </footer>
</body>

</html>
