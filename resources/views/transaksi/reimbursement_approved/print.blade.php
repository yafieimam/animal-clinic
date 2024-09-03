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
            font-size: 14px;
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
            <td style="text-align: center;font-size: 24px;width: 50%">Amore Animal Clinic<br>{{ $data->CreatedBy->Branch->lokasi }}
            </td>
            <td style="width: 25%;text-align: right;font-size: 12px;">
                {{ $data->CreatedBy->Branch->alamat }}<br>
                {{ $data->CreatedBy->Branch->telpon }}
            </td>
        </tr>
    </table>
    <br>
    <hr style="border: 0.1px solid grey">
    <table style="width: 100%">
        <tr>
            <td colspan="2" style="text-align: center; font-size: 22px;"><b>FORM PERMOHONAN PENGGANTIAN BIAYA MEDIS</b></td>
        </tr>
    </table>
    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td colspan="2" style="text-align: left;">Jakarta, {{ CarbonParse($data->tanggal, 'd F Y') }}</td>
        </tr>
    </table>
    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td colspan="4" style="text-align: left;">Saya yang bertanda tangan dibawah ini menyatakan bahwa saya adalah <b>Karyawan Amore Group</b> dengan data sebagai berikut :</td>
        </tr>
        <tr>
            <td style="width: 5%;text-align: center; font-size: 20px;">&#8226;</td>
            <td style="width: 25%;text-align: left;">Nama Lengkap</td>
            <td style="width: 5%;text-align: right;">:</td>
            <td style="text-align: left;">{{ $data->CreatedBy->name }}</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 20px;">&#8226;</td>
            <td style="text-align: left;">Jabatan</td>
            <td style="text-align: right;">:</td>
            <td style="text-align: left;">{{ $data->CreatedBy->Role->name }}</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 20px;">&#8226;</td>
            <td style="text-align: left;">Keperluan Medis</td>
            <td style="text-align: right;">:</td>
            <td style="text-align: left;">{{ $data->tipe_klaim }}</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 20px;">&#8226;</td>
            <td style="text-align: left;">Jumlah biaya yang diganti</td>
            <td style="text-align: right;">:</td>
            <td style="text-align: left;">Rp. {{ number_format($data->jumlah_biaya) }}</td>
        </tr>
    </table>
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td colspan="2" style="text-align: justify;">
                Dengan ini menyatakan telah mengeluarkan uang pribadi untuk keperluan medis <span style="text-decoration: none; border-bottom: 1px dotted #000;"><b>Senilai {{ number_format($data->jumlah_biaya) }} untuk {{ $data->tipe_klaim }} </b></span>
                dan mengajukan permohonan untuk mendapatkan pergantian biaya medis dengan melampirkan :
            </td>
        </tr>
        <tr>
            <td style="width: 5%;text-align: center; font-size: 20px;">&#8226;</td>
            <td style="text-align: left;">Surat keterangan surat diagnosa dari dokter</td>
        </tr>
        <tr>
            <td style="width: 5%;text-align: center; font-size: 20px;">&#8226;</td>
            <td style="text-align: left;">Kwitansi biaya perawatan</td>
        </tr>
    </table>
    <table style="width: 100%;margin-top: 10px">
        <tr>
            <td style="text-align: left;height: 100px;vertical-align: middle" colspan="3">
                Demikian permohonan ini saya sampaikan.
            </td>
        </tr>
        <tr>
            <td style="text-align: center;vertical-align: top;height: 100px;width: 30%">
                <b>Hormat Saya,</b>
            </td>
            <td style="text-align: center;vertical-align: top;height: 100px;width: 40%">&nbsp;</td>
            <td style="text-align: center;vertical-align: top;height: 100px;width: 30%">
                <b>Menyetujui,</b>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;vertical-align: bottom">
                ( {{ $data->CreatedBy->name }} )
            </td>
            <td style="text-align: center;vertical-align: bottom">&nbsp;</td>
            <td style="text-align: center;vertical-align: bottom">
                ( {{ $data->UpdatedBy->name }} )
            </td>
        </tr>
    </table>
    <table style="width: 80%;float: right; margin-top: 50px;">
        <tr>
            <td>
                <table style="width: 100%;border: 1px solid grey;border-radius: 10px;" class="main">
                    <tr>
                        <td style="text-align: center"><b>DOKUMEN INI TIDAK DIPERLUKAN TANDA TANGAN BRANCH MANAGER</b></td>
                        <!-- <td style="text-align: right"></td> -->
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
