<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('assets/images/amore.png') }}" type="image/x-icon"> <!-- Favicon-->
    <title>FORM PERSETUJUAN PULANG PAKSA</title>
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

        .pasien th,
        td {
            padding: 0px 0px;
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
                <h2 style="margin-bottom: 0px;padding: 0px ;margin: 0px"><b>AMORE ANIMAL CLINIC</b></h2>
                <span style="font-size: 16px;text-align: center">
                    {{-- {!! $data->Pendaftaran->Branch->alamat !!} --}}
                    {{ Auth::user()->Branch->alamat }}<br>
                +62{{ Auth::user()->Branch->telpon }}
                </span>
            </td>
            <td style="25%">

            </td>
        </tr>
    </table>
    <hr>
    <h4 style="text-align: center;margin: 5px 0px;text-decoration:underline">
        <b>
            SURAT KETERANGAN PULANG ATAS KEHENDAK PRIBADI
        </b>
    </h4>
    <p>
        Pada hari <b>{{ CarbonParseISO(now(), 'dddd') }}, Tanggal {{ CarbonParseISO(now(), 'LL') }}</b> di Amore Animal Clinic Cabang <b>{{ Auth::user()->Branch->lokasi }}</b>
        <br>
        Saya yang bertanda tangan di bawah ini :
    </p>
    <table class="pasien" style="width:100%;font-size:14px;margin-left: 2rem">
        <tr>
            <td>Nama</td>
            <td style="width: 80%;">:&nbsp;{{ $data->Pasien->Owner->name }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:&nbsp;{{ $data->Pasien->Owner->alamat }}</td>
        </tr>
        <tr>
            <td>No. Telp</td>
            <td>:&nbsp; {{ $data->Pasien->Owner->telpon }}</td>
        </tr>
    </table>
    <p>
        Selaku Pemilik / Perwakilan dari pasien :
    </p>
    <table class="pasien" style="width:100%;font-size:14px;margin-left: 2rem">
        <tr>
            <td>Nama</td>
            <td>:&nbsp;{{ $data->Pasien->name }}</td>
        </tr>

        <tr>
            <td>Umur / Ras</td>
            <td>
                :&nbsp;{{ Carbon\Carbon::parse($data->Pasien->date_of_birth)->diff(Carbon\Carbon::now())->format('%y tahun %m bulan %d hari') }}
                / {{ $data->Pasien->Ras->name }}
            </td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:&nbsp;{{ $data->Pasien->sex }}</td>
        </tr>
        <tr>
            <td>Diagnosa</td>
            <td>:&nbsp;{{ $data->diagnosa }}</td>
        </tr>
    </table>
    <p style="text-indent:2rem;">
        Menghendaki pasien diatas untuk dibawa pulang meskipun tidak direkomendasikan oleh dokter penanggung jawab
        pasien tersebut selain itu saya menyatakan telah memahami penjelasan petugas medis tentang segala hal yang
        berghubungan dengan penyakit yang di derita pasien serta kemungkinan resiko dan komplikasi yang mungkin timbul
        pasca pemulangan.
    </p>
    <p style="text-indent:2rem;">
        Saya tidak akan melakukan <b>TUNTUTAN HUKUM</b> apabila di kemudian hari terjadi sesuatu terhadap
        pasien yang bersangkutan.
    </p>
    <p style="text-indent:2rem;">
        Demikian surat ini saya buat dan saya nyatakan dalam kondisi sadar dan tidak berada
        dibawah tekanan siapapun, untuk dapa digunakan sebagaimana mestinya.
    </p>
    <br>
    <table style="width: 100%;font-size: 12px">
        <tr>
            <td style="width: 10%"></td>
            <td style="text-align: center;height: 50px;vertical-align: middle">
                Jakarta, {{ carbon\carbon::now()->format('d F Y') }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%"></td>
            <td style="text-align: center;vertical-align: top;height: 100px;width: 50%">
                <b>Pemilik Hewan</b>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;vertical-align: bottom;width: 50%">
                <table style="100%;font-size:12px">
                    <tr>
                        <td>Waktu </td>
                        <td>:</td>
                        <td>{{ CarbonParse(now(), 'H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td>Diterima Oleh </td>
                        <td>:</td>
                        <td>{{ $data->CreatedBy->name }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%" class="text-center">{{ $data->Pasien->Owner->name }}</td>
        </tr>
    </table>
</body>

</html>
