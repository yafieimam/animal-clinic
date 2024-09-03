<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('dist/images/amoreboxy.png') }}" type="image/x-icon"> <!-- Favicon-->
    <title>Bukti Pengeluaran</title>
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

        .text-right {
            text-align: right;
        }

        .badge {
            font-weight: 600;
            text-transform: uppercase;
            border: 1 px solid;
            padding: 2 px 5 px;
            line-height: 12px;
        }

        .badge-warning {
            background: transparent;
            border-color: #ff7321;
            color: #ff7321;
        }

        .badge-danger {
            background: transparent;
            border-color: #ff2121;
            color: #ff2121;
        }

        .badge-primary {
            background: transparent;
            border-color: #21cfff;
            color: #21cfff;
        }
    </style>
</head>

<body>
    <table style="width: 100%">
        <tr style="vertical-align: top">
            <td style="width: 25%;padding-top: 1rem">
                <img src="http://acm.amoreanimalclinic.com/assets/images/amoretext.png" style="width: 50%">
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

            </td>
        </tr>
    </table>
    <hr>
    <h4 style="text-align: center;margin: 5px 0px"><b>BUKTI KAS KELUAR</b></h4>
    <table style="width:50%;font-size:12px">
        <tr>
            <td>Kode Transaksi </td>
            <td>:</td>
            <td>{{ $data->kode }}</td>
        </tr>
        <tr>
            <td>Tanggal </td>
            <td>:</td>
            <td>{{ CarbonParse($data->tanggal, 'd M Y') }}</td>
        </tr>
        <tr>
            <td>Jenis </td>
            <td>:</td>
            <td>{{ ucwords(Str::lower($data->jenis)) }}</td>
        </tr>
        <tr>
            <td>Status </td>
            <td>:</td>
            <td>
                @if ($data->status == 'Released')
                    <span class="badge badge-warning">Belum Disetujui</span>
                @endif

                @if ($data->status == 'Approved')
                    <span class="badge badge-primary">Approved</span>
                @endif

                @if ($data->status == 'Rejected')
                    <span class="badge badge-danger">Rejected</span>
                @endif
            </td>
        </tr>
        @if ($data->status == 'Rejected')
            <tr>
                <td>Alasan Ditolak </td>
                <td>:</td>
                <td>{{ $data->alasan }}</td>
            </tr>
        @endif
    </table>
    <hr>
    <table class="item" style="width: 100%;font-size: 12px; border-collapse: collapse" border="1">
        <thead>
            <tr>
                <th>NO</th>
                <th>JENIS TRANSAKSI</th>
                <th>REDAKSI</th>
                <th>NOMINAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->JurnalDetail as $i => $item)
                <tr>
                    <td style="text-align: center">{{ $i + 1 }}</td>
                    <td>
                        {{ $item->MasterAkunTransaksi->name }}
                    </td>
                    <td>
                        {{ $item->redaksi }}
                    </td>
                    <td class="text-right">
                        {{ number_format($item->harga) }}
                    </td>
                </tr>
            @endforeach
            @for ($i = 0; $i < 10 - count($data->JurnalDetail); $i++)
                <tr>
                    <td style="text-align: center">{{ count($data->JurnalDetail) + $i + 1 }}</td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td class="text-center">
                    </td>
                </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">{{ number_format($data->nominal) }}</td>
            </tr>
        </tfoot>
    </table>
    <table style="width: 100%;margin-top: 20px">
        <tr>
            <td style="text-align: left;height: 100px;vertical-align: middle" colspan="2">
                Jakarta, {{ carbon\carbon::parse($data->created_at)->format('d-F-Y') }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center;vertical-align: top;height: 100px;width: 50%">
                {{-- @if ($data->status != 'Rejected')
                    <b>YANG MENSETUJUI</b>
                @else
                    <b>DITOLAK OLEH</b>
                @endif --}}
            </td>
            <td style="text-align: center;vertical-align: top;height: 100px;width: 50%">
                <b>YANG MENGELUARKAN</b>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;vertical-align: bottom">
                {{-- @if ($data->status != 'Released')
                    {{ $data->ApprovedBy->name }}
                @else
                    __________
                @endif --}}
            </td>
            <td style="text-align: center;vertical-align: bottom">
                {{ $data->CreatedBy->name }}
            </td>
        </tr>
    </table>
</body>

</html>
