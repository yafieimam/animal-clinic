<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('assets/images/amore.png') }}" type="image/x-icon"> <!-- Favicon-->
    <title>FORM PERSETUJUAN {{ $data->kode }}</title>
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
                    {!! $data->Pendaftaran->Branch->alamat !!}
                </span>
            </td>
            <td style="25%">

            </td>
        </tr>
    </table>
    <hr>
    <h4 style="text-align: center;margin: 5px 0px;text-decoration:underline"><b>FORM PERSETUJUAN</b></h4>
    <br>
    <table class="pasien" style="width:100%;font-size:12px;">
        <caption style="text-align: left;font-size: 14px;padding: 10px 0px"><b>KETERANGAN PELIHARAAN</b></caption>
        <tr>
            <td>Nama Hewan</td>
            <td>:&nbsp;{{ $data->Pasien->name }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:&nbsp;{{ $data->Pasien->sex }}</td>
        </tr>
        <tr>
            <td>Umur / Ras</td>
            <td>
                :&nbsp;{{ Carbon\Carbon::parse($data->Pasien->date_of_birth)->diff(Carbon\Carbon::now())->format('%y tahun %m bulan %d hari') }}
                / {{ $data->Pasien->Ras->name }}
            </td>
        </tr>
        <tr>
            <td>Warna/Tanda</td>
            <td>:&nbsp;{{ $data->Pasien->ciri_khas }}</td>
        </tr>
        <!-- <tr>
            <td>Kondisi Hewan</td>
            <td style="width: 80%;">
                :&nbsp;<div style="border-bottom: 1px dotted black;width: 100%;margin-left: 8px"></div>
            </td>
        </tr> -->
    </table>
    <br>
    <table class="pasien" style="width:100%;font-size:12px;">
        <caption style="text-align: left;font-size: 14px;padding: 10px 0px"><b>KETERANGAN PEMILIK</b></caption>
        <tr>
            <td>Nama Pemilik</td>
            <td style="width: 80%;">:&nbsp;{{ $data->Pasien->Owner->name }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:&nbsp;{{ $data->Pasien->Owner->alamat }}</td>
        </tr>
        <tr>
            <td>No. Telepon</td>
            <td>:&nbsp; {{ $data->Pasien->Owner->telpon }}</td>
        </tr>
    </table>
    <br>
    <h4 style="text-align: center;margin: 5px 0px;"><b>PENGAKUAN PEMILIK</b></h4>
    <table class="pasien" style="width:100%;font-size:12px;">
        <caption style="text-align: left;font-size: 12px;padding: 10px 0px" align="justify">
            Dengan ini saya menyerahkan hewan peliharaan milik saya seperti keterangan diatas kepada pihak AMORE ANIMAL
            CLINIC untuk :
        </caption>
        <tr>
            <td style="width: 33.33%;font-family: DejaVu Sans, sans-serif;">({{ $data->rawat_jalan ? '✔' : '...' }})
                Rawat Jalan
            </td>
            <td style="width: 33.33%;font-family: DejaVu Sans, sans-serif;">({{ $data->bius ? '✔' : '...' }})
                Anestasi/Bius
            </td>
            <td style="width: 33.33%;font-family: DejaVu Sans, sans-serif;">({{ $data->grooming ? '✔' : '...' }})
                Grooming
            </td>
        </tr>
        <tr>
            <td style="width: 33.33%;font-family: DejaVu Sans, sans-serif;">({{ $data->rawat_inap ? '✔' : '...' }})
                Rawat Inap
            </td>
            <td style="width: 33.33%;font-family: DejaVu Sans, sans-serif;">
                @if ($data->tindakan_bedah || request('from') == 'BEDAH')
                    (✔)
                @else
                    (...)
                @endif
                Operasi
            </td>
            <td style="width: 33.33%;font-family: DejaVu Sans, sans-serif;">({{ $data->titip_sehat ? '✔' : '...' }})
                Titip Sehat
            </td>
        </tr>
    </table>
    </div>
    <table class="pernyataan" style="width: 100%;font-size: 12px;margin-top: 5px">
        <tr>
            <td style="font-size: 12px" align="justify">
                <p>Saya juga telah mengerti dan menyadari sepenuhnya atas penjelasan dokter hewan untuk Tindakan Nomor 3
                    dan Tindakan Nomor 4 bahwa :</p>
                <ol type="a">
                    <li>
                        <b>
                            Saya mengerti dan menerima semua resiko tindakan medis yang akan dilakukan dan memberikan
                            kewenangan penuh pada dokter hewan untuk melakukan tindakan tersebut sesuai prosedur
                            kedokteran hewan.
                        </b>
                    </li>

                    <li>
                        <b>
                            Saya tidak akan menuntut secara hukum baik pidana maupun perdata dalam bentuk apapun atas
                            resiko yang ditimbulkan atas tindakan medis yang telah diambil sesuai dengan prosedur
                            standar seorang dokter hewan professional.
                        </b>
                    </li>
                </ol>
            </td>
        </tr>
    </table>

    <table class="pernyataan" style="width: 100%;font-size: 12px;margin-top: 5px">
        <tr>
            <td style="font-size: 12px" align="justify">
                <p>Saya juga telah mengerti dan menyadari sepenuhnya atas penjelasan dokter hewan untuk Tindakan Nomor
                    2, 5 dan 6 bahwa:</p>
                <ol type="a">
                    <li>
                        <b>
                            Saya mengerti dan menerima semua resiko yang dapat terjadi selama penitipan dan memberikan
                            kewenangan penuh pada dokter hewan untuk melakukan tindakan medis apabila diperlukan dengan
                            dan/ atau tanpa sepengetahuan pemilik sesuai prosedur kedokteran hewan.
                        </b>
                    </li>

                    <li>
                        <b>
                            Saya tidak akan menuntut secara hukum baik pidana maupun perdata dalam bentuk apapun atas
                            resiko yang ditimbulkan atas tindakan medis yang telah diambil sesuai dengan prosedur
                            standar seorang dokter hewan professional.
                        </b>
                    </li>

                    <li>
                        <b>
                            Saya setuju membayar penuh jasa yang telah diberikan, termasuk tindakan lain yang dianggap
                            perlu.
                        </b>
                    </li>
                </ol>
            </td>
        </tr>
    </table>

    <table class="pernyataan" style="width: 100%;font-size: 12px;margin-top:0px">
        <tr>
            <td style="font-size: 12px">
                <p>Pernyataan ini dibuat dengan penuh kesadaran atas segala resiko tindak medis tersebut diatas.</p>
            </td>
        </tr>
    </table>
    <table style="width: 100%;font-size: 12px">
        <tr>
            <td style="width: 10%"></td>
            <td style="text-align: center;height: 50px;vertical-align: middle">
                Jakarta, {{ carbon\carbon::parse($data->created_at)->format('d F Y') }}
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
