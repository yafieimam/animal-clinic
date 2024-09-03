<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pasien</th>
            <th>Branch</th>
            <th>Jenis Hewan</th>
            <th>Owner</th>
            <th>Dokter Poli</th>
            <th>Kamar Terakhir</th>
            <th>Tanggal Daftar Pasien</th>
            <th>Tanggal Meninggal Pasien</th>
            <th>Meninggal Saat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->Pendaftaran->Branch->kode }}</td>
                <td>{{ $item->Pasien->Binatang->name }}</td>
                <td>{{ $item->Pendaftaran->Owner->name }}</td>
                <td>
                    @php
                        echo $item->Pendaftaran->Dokter->name;
                    @endphp
                </td>
                <td>
                    @php
                        echo $item->kamarRawatInapDanBedahDetailFirst->KamarRawatInapDanBedah->name;
                    @endphp
                </td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->pasienMeninggal->created_at }}</td>
                <td>{{ $item->pasienMeninggal->meninggal_saat }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
