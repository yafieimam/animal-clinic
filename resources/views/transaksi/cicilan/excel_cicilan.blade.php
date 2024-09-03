<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No. Invoice</th>
            <th>Branch</th>
            <th>Tanggal</th>
            <th>Nama Owner</th>
            <th>Tagihan</th>
            <th>Kurang Bayar</th>
            <th>Catatan</th>
            <th>Cashier</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->Branch->kode }}</td>
                <td>{{ CarbonParse($item->tanggal, 'd-M-Y') }}</td>
                <td>{{ $item->nama_owner }}</td>
                <td>{{ number_format($item->total_bayar) }}</td>
                <td>{{ number_format($item->sisa_pelunasan) }}</td>
                <td>{{ $item->catatan_kasir }}</td>
                <td>{{ $item->CreatedBy->name }}</td> 
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="6" class="text-center font-medium">Total Hutang</td>
            <td>{{ number_format($total_hutang) }}</td>
            <td></td>
            <td></td>
        </tr>

    </tbody>
</table>
