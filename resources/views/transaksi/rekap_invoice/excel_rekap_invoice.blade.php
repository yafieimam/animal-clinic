<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No. INV</th>
            <th>Tanggal</th>
            <th>Branch</th>
            <th>Type Kasir</th>
            <th>Nama Customer</th>
            <th>Total Pembayaran</th>
            <th>Pembayaran</th>
            <th>Sisa Tagihan</th>
            <th>Status Pembayaran</th>
            <th>Metode Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->tanggal }}</td>
                <td>{{ $item->Branch->kode }}</td>
                <td>{{ $item->type_kasir }}</td>
                <td>{{ $item->nama_owner }}</td>
                <td>{{ number_format($item->total_bayar) }}</td>
                <td>{{ number_format($item->pembayaran - $item->sisa_pelunasan)  }}</td>
                <td>{{ number_format($item->sisa_pelunasan) }}</td>
                <td>
                    @php
                        if ($item->sisa_pelunasan > 0) {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Belum Lunas</div>';
                        } elseif ($item->sisa_pelunasan == 0) {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Lunas</div>';
                        }
                    @endphp
                </td>
                <td>{{ $item->metode_pembayaran }}</td>
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
            <td colspan="6" class="text-center font-medium">Total Lunas</td>
            <td>{{ number_format($total_lunas) }}</td>
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