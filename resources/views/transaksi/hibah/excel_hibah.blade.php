<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No. INV</th>
            <th>Tanggal</th>
            <th>Branch</th>
            <th>Type Kasir</th>
            <th>Nama Customer</th>
            <th>No Registrasi</th>
            <th>Total Pembayaran</th>
            <th>Status Pembayaran</th>
            <th>Catatan</th>
            <th>Metode Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->kasir->kode }}</td>
                <td>{{ CarbonParse($item->created_at, 'd-M-Y') }}</td>
                <td>{{ $item->kasir->Branch->kode }}</td>
                <td>{{ $item->kasir->type_kasir }}</td>
                <td>{{ $item->kasir->nama_owner }}</td>
                <td>{{ $item->kasir->owner->kode }}</td>
                <td>{{ number_format($item->nilai_pembayaran) }}</td>
                <td>
                    @php
                        if ($item->kasir->sisa_pelunasan > 0) {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Belum Lunas</div>';
                        } elseif ($item->kasir->sisa_pelunasan == 0) {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">Lunas</div>';
                        }
                    @endphp
                </td>
                <td>
                    @php
                        if ($item->catatan_kasir != 0) {
                            echo $item->catatan_kasir;
                        } elseif ($item->catatan_kasir == null) {
                            echo '-';
                        }
                    @endphp
                </td>
                <td>{{ $item->kasir->metode_pembayaran }}</td> 
              
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
            <td>{{ number_format($hibah) }}</td>
            <td></td>
            <td></td>
        </tr>

    </tbody>
</table>
