<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak No Antrean</title>
    <style>
        @page {
            margin: 0px 0px 0px 0px !important;
            padding: 0px 0px 0px 0px !important;
            size: 480px 620px;
        }

        body {
            margin: 0px;
        }

        #all {
            padding: 0px 20px;
        }

    </style>
</head>

<body>
    <div id="all">
        <table style="width: 100%">
            <tr>
                <td colspan="2" style="text-align: center;font-weight: 800;font-size: 48px;padding: 12px">
                    {{ $kodePendaftaran }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;font-weight: 800;font-size: 20px;padding: 4px">
                    BUKTI DAFTAR PASIEN
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 12px">
                    <i class="fa fa-trademark" aria-hidden="true"></i>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;font-size: 14px;">Kode Pasien</td>
                <td style="width: 50%;font-size: 14px;">
                    @foreach ($data->PendaftaranPasien as $item)
                        {{ $item->pasien->kode }} <br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td style="width: 50%;font-size: 14px;">Nama Pasien</td>
                <td style="width: 50%;font-size: 14px;">
                    @foreach ($data->PendaftaranPasien as $item)
                        {{ $item->pasien->name }} <br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td style="width: 50%;font-size: 14px;">Nama Owner</td>
                <td style="width: 50%;font-size: 14px;">{{ $namaOwner }}</td>
            </tr>
            <tr>
                <td style="width: 50%;font-size: 14px;">No Telepon</td>
                <td style="width: 50%;font-size: 14px;">{{ $telpOwner }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 12px">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 4px;text-align:center;font-size: 20px">
                    Mohon tunggu panggilan dari Bagian Pendaftaran untuk Pemeriksaan di Poli <br><br>
                    Simpan bukti Pendaftaran ini hingga proses administrasi selesai. <br><br>
                    Jika hendak batal mendaftar dan meninggalkan antrean segera konfirmasi ke bagian pendaftaran.<br><br>
                    Awasi selalu kondisi hewan Anda.
                </td>
            </tr>
            <!-- <tr>
                <td colspan="2" style="padding: 4px;text-align:center;font-size: 20px">
                    dari counter
                </td>
            </tr> -->
            <!-- <tr>
                <td colspan="2" style="padding: 4px;text-align:center;font-size: 20px">
                    Simpan bukti Pendaftaran ini hingga administrasi selesai
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 4px;text-align:center;font-size: 20px">
                    Administrasi selesai.
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 4px;text-align:center;font-size: 20px">
                    Apabila batal berobat serahkan BUKTI DAFTAR
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 4px;text-align:center;font-size: 20px">
                    ke counter
                </td>
            </tr> -->

        </table>
        <table style="width: 100%">
            <tr>
                <td colspan="2" style="text-align: center;font-weight: 800;font-size: 20px;padding: 4px">
                    TERIMA KASIH TELAH MENDAFTAR<br>DI AMORE ANIMAL CLINIC
                </td>
            </tr>
            <tr style="border: none">
                <td style="width: 50%;border: none"></td>
                <td style="width: 50%;font-size: 16px;border: none">Print :
                    {{ carbon\carbon::now()->format('d-M-Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
