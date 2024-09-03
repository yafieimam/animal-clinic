<div class="col-span-12 " style="overflow-x: scroll !important">
    <table class="table table-bordered">
        <thead>
            {{-- <th>Print</th> --}}
            <th>Ref</th>
            <th>Tanggal</th>
            <th>Cashier</th>
            <th>Keterangan</th>
            <th>Jenis Pembayaran</th>
            <th>Nama Bank</th>
            <th>No. Rekening</th>
            <th>No. Transaksi</th>
            <th>Nilai Pembayaran</th>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    {{-- <td><button class="btn btn-warning" type="button" onclick="printBuktiPembayaran()"><i class="fas fa-print"></i></button></td> --}}
                    <td>{{ $item->ref }}</td>
                    <td>{{ CarbonParse($item->created_at, 'd-M-Y') }}</td>
                    <td>{{ $item->CreatedBy->name }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ $item->jenis_pembayaran }}</td>
                    <td>{{ $item->nama_bank }}</td>
                    <td>{{ $item->nomor_kartu }}</td>
                    <td>{{ $item->nomor_transaksi }}</td>
                    <td>{{ number_format($item->nilai_pembayaran) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        Tidak ada data
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text-right"><b><i>Total</i></b></td>
                <td class="text-right">{{ number_format($data->sum('nilai_pembayaran')) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
