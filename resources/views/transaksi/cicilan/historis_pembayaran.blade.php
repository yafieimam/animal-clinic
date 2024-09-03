<div class="col-span-12 " style="overflow-x: scroll !important">
    <table class="table table-bordered">
        <thead>
            <th>Print</th>
            <th>Ref</th>
            <th>Tanggal</th>
            <th>Cashier</th>
            <th>Keterangan</th>
            <th>Metode Pembayaran</th>
            <th>Nama Bank</th>
            <th>No. Rekening</th>
            <th>No. Transaksi</th>
            <th>Tagihan Awal</th>
            <th>Nilai Pembayaran</th>
            <th>Sisa Tagihan</th>
            <th>Diskon Cicilan</th>
            <th>Bukti Transfer</th>
        </thead>
        <tbody>
            @php
                $pembayaran = $kasir->pembayaran;
            @endphp
            @forelse ($data->sortBy('created_at') as $item)
                <tr>
                    <td>
                        <button class="btn btn-warning" type="button"
                            onclick="printBuktiPembayaran('{{ $item->kasir_id }}','{{ $item->id }}')"><i
                                class="fas fa-print"></i>
                        </button>
                    </td>
                    <td>{{ $item->ref }}</td>
                    <td>{{ CarbonParse($item->created_at, 'd-M-Y') }}</td>
                    <td>{{ $item->CreatedBy->name }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ $item->jenis_pembayaran }}</td>
                    <td>{{ $item->nama_bank ? $item->nama_bank : '-' }}</td>
                    <td>{{ $item->nomor_kartu ? $item->nomor_kartu : '-' }}</td>
                    <td>{{ $item->nomor_transaksi ? $item->nomor_transaksi : '-' }}</td>
                    {{-- <td>{{ $item->nomor_kartu }}</td> --}}
                    {{-- <td>{{ $item->nomor_transaksi }}</td> --}}
                    <td>Rp. {{ number_format($pembayaran) }}</td>
                    <td>Rp. {{ number_format($item->nilai_pembayaran) }}</td>
                    @php
                        $pembayaran -= $item->nilai_pembayaran;
                    @endphp
                    <td>Rp. {{ number_format($pembayaran - $item->diskon_cicilan) }}</td>
                    <td>Rp. {{ number_format($item->diskon_cicilan) }}</td>
                    <td>
                    @if($item->jenis_pembayaran == 'DEBET' || $item->jenis_pembayaran == 'TRANSFER')
                        @if($item->bukti_transfer != null)
                            <a href="{{ url('/') . '/' . $item->bukti_transfer }}" target="_blank"><img style="width:100px;height:100px;object-fit:cover;cursor:pointer" src="{{ url('/') . '/' . $item->bukti_transfer }}" alt="No image"></a>
                        @else
                            <button class="btn btn-primary btn-round btn-xs" onclick="uploadBuktiTransfer('{{ $item->kasir_id }}', '{{ $item->id }}')">Upload</button>
                        @endif
                    @else
                        -
                    @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">
                        Tidak ada data
                    </td>
                </tr>
            @endforelse
        </tbody>
        {{-- <tfoot>
            <tr>
                <td colspan="8" class="text-right"><b><i>Total</i></b></td>
                <td class="text-right"></td>
                <td class="text-right">{{ number_format($data->sum('nilai_pembayaran')) }}</td>
                <td class="text-right"></td>
            </tr>
        </tfoot> --}}
    </table>
</div>
