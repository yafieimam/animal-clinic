<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kamar</th>
            <th>No Rekam Medis</th>
            <th>Nama Pasien</th>
            <th>Nama Owner</th>
            <th>Tarif Per Kamar</th>
            <th>Selisih hari</th>
            <th>Total Selisih</th>
            <th>Status Kasir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->KamarRawatInapDanBedah->name }}</td>
                <td>{{ $item->RekamMedisPasien->kode }}</td>
                <td>{{ $item->RekamMedisPasien->Pasien->name }}</td>
                <td>{{ $item->RekamMedisPasien->Pasien->Owner->name }}</td>
                @php
                    $qtyHari = 0;
                    $kamar;
                    $tanggalMasuk = carbon\carbon::parse($item->tanggal_masuk);
                    $tanggalKeluar = carbon\carbon::parse($item->tanggal_keluar);
                    $tempHari = carbon\Carbon::parse($tanggalMasuk)->diffInDays($tanggalKeluar);

                    if ($tempHari < 1) {
                        $tempHari = 1;
                    }

                    if ($item->status_pindah) {
                        // $tempHari--;
                    } else {
                        $kamar = $item;
                    }

                    if ($item->status == 'Done') {
                        // $tempHari--;
                    }

                    if ($tanggalMasuk == $tanggalKeluar) {
                        $tempHari = 0;
                    }
                    $qtyHari += $tempHari;
                @endphp
                <td>{{ $item->KamarRawatInapDanBedah->tarif_per_hari }}</td>
                <td>{{ $qtyHari }}</td>
                <td>{{ $qtyHari * $item->KamarRawatInapDanBedah->tarif_per_hari }}</td>
                <td>{{ $item->RekamMedisPasien->status_pembayaran ? 'Sudah' : 'Belum' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
