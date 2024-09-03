<table id="table">
    <thead align="center">
        <tr>
            <th><b>No</b></th>
            <th><b>Referensi</b></th>
            <th><b>Tanggal</b></th>
            <th><b>Tgl Exp</b></th>
            <th><b>Jenis Stock</b></th>
            <th><b>Branch</b></th>
            <th><b>Item</b></th>
            <th><b>Harga Satuan</b></th>
            <th><b>Quantity</b></th>
            <th><b>Total Harga</b></th>
            <th><b>Dibuat Oleh</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->referensi }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->expired_date }}</td>
                <td>{{ $item->jenis }}</td>
                <td>
                    @php
                        echo $item->Stock->Branch != null ? $item->Stock->Branch->kode . ' ' . $item->Stock->Branch->lokasi : '-';
                    @endphp
                </td>
                <td>
                    @php
                        if ($item->Stock->ProdukObat) {
                            echo $item->Stock->ProdukObat->name . ' ' . $item->Stock->ProdukObat->dosis . ' MG';
                        }
                        
                        if ($item->Stock->ItemNonObat) {
                            echo $item->Stock->ItemNonObat->name;
                        }
                    @endphp
                </td>
                <td>{{ number_format($item->harga_satuan) }}</td>
                <td>{{ number_format($item->qty) }}</td>
                <td>{{ number_format($item->total_harga) }}</td>
                <td>{{ $item->CreatedBy->name }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
