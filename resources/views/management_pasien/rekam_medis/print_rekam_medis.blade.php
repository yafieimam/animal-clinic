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

        th,
        td {
            padding: 0px 4px;
        }

        .text-center {
            text-align: center;
        }

        .br {
            border-right: 1px solid black;
        }

        .bl {
            border-left: 1px solid black;
        }

        ul{
            padding: 0px 12px;
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
                    {!! Auth::user()->Branch->alamat !!}
                </span>
            </td>
            <td style="25%">

            </td>
        </tr>
    </table>
    <hr>
    <h4 style="text-align: center;margin: 5px 0px"><b>DATA REKAM MEDIS PASIEN</b></h4>
    <br>
    <table style="width:100%;font-size:12px">
        <tr>
            <td>Nama Owner</td>
            <td>{{ $data->Owner->name }}</td>
            <td>Date/Time Cetak</td>
            <td>{{ CarbonParse(now(), 'd/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td>{{ $data->name }}</td>
            <td>Print By</td>
            <td>{{ Auth::user()->name }}</td>
        </tr>
        <tr>
            <td>No. RM </td>
            <td>{{ $data->kode }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <br>
    <table class="pasien" style="width:100%;font-size:12px;border-collapse:collapse" border="1">
        <tr>
            <td class=""><b>Tanggal/Jam</b></td>
            <td class=""><b>Anamnese</b></td>
            <td class=""><b>Hasil Pemeriksaan</b></td>
            <td class=""><b>Diagnosa</b></td>
            <td class=""><b>Treatment</b></td>
            <td class=""><b>Dokter</b></td>
        </tr>
        @foreach ($data->Pendaftaran as $item)
            @if ($item->RekamMedisPasien)
                <tr>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->RekamMedisPasien->gejala }}</td>
                    <td>{{ $item->RekamMedisPasien->hasil_pemeriksaan }}</td>
                    <td>
                        <ul>
                            @foreach ($item->RekamMedisDiagnosa as $item1)
                                <li>{{ $item1->diagnosa }} </li>
                            @endforeach
                        </ul>

                    </td>
                    <td>
                        <ul>
                            @foreach ($item->RekamMedisTreatment as $item1)
                                <li>{{ $item1->treatment }} </li>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        {{ $item->Dokter->name }}
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
</body>

</html>
