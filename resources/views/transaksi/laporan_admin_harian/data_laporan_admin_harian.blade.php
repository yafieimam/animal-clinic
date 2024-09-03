<div class="intro-y col-span-12 p-8 my-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
    id="isi">
    <div class="col-span-12 text-right  mb-3">
        <button class="btn btn-primary shadow-md mr-2" onclick="excel()">Export Excel</button>
    </div>
    <table class="table" style="width: 100%">
        @if ($req->tanggal_awal == $req->tanggal_akhir)
            <tr>
                <td><b>Hari</b></td>
                <td>: {{ CarbonParseISO($req->tanggal_awal, 'dddd') }}</td>
            </tr>
        @endif
        <tr>
            <td><b>Tanggal</b></td>
            <td>: {{ CarbonParse($req->tanggal_awal, 'd/m/Y') }} - {{ CarbonParse($req->tanggal_akhir, 'd/m/Y') }}</td>
        </tr>
        <tr>
            <td><b>Deposit</b></td>
            <td>: Rp. {{ number_format($deposit) }}</td>
        </tr>
        <tr>
            <td><b>Cash</b></td>
            <td>: Rp. {{ number_format($tunai) }}</td>
        </tr>
        <tr>
            <td><b>Debet</b></td>
            <td>: Rp. {{ number_format($debet) }}</td>
        </tr>
        <tr>
            <td><b>Transfer</b></td>
            <td>: Rp. {{ number_format($transfer) }}</td>
        </tr>
        <tr>
            <td><b>Total</b></td>
            <td>: Rp. {{ number_format($transfer + $tunai + $debet + $deposit) }}</td>
        </tr>
    </table>
</div>

<div class="intro-y col-span-12 p-8 my-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
    id="isi1">
    <div class="col-span-12 text-right  mb-3">
        <button class="btn btn-primary shadow-md mr-2" onclick="excel1()">Export Excel</button>
    </div>
    <table class="table-item" style="width: 100%" border="1">
        <thead align="center">
            <th>NO</th>
            <th>KODE</th>
            <th>REF</th>
            <th>TANGGAL TRANSAKSI</th>
            <th>DESKRIPSI</th>
            <th>CABANG</th>
            <th>PEMASUKAN</th>
            <th>PENGELUARAN</th>
        </thead>
        <tbody>
            @php
                $debet = 0;
                $kredit = 0;
            @endphp
            @foreach ($data as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->ref }}</td>
                    <td align="center">{{ CarbonParse($item->tanggal, 'd-M-Y') }}</td>
                    <td>{{ $item->description }}</td>
                    <td align="center">{{ $item->Branch->kode }}</td>
                    <td class="text-right">
                        @if ($item->dk == 'DEBET')
                            <div style="float:left;width:10%;">
                                Rp.
                            </div>
                            <div style="float:right;width:50%;">
                                {{ number_format($item->nominal) }}
                            </div>
                            @php
                                $debet += $item->nominal;
                            @endphp
                        @else
                            0
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($item->dk == 'KREDIT')
                            <div style="float:left;width:10%;">
                                Rp.
                            </div>
                            <div style="float:right;width:50%;">
                                {{ number_format($item->nominal) }}
                            </div>
                            @php
                                $kredit += $item->nominal;
                            @endphp
                        @else
                            0
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><b>TOTAL</b></td>
                <td colspan="1" class="text-right" style="width: 150px">
                    <div style="float:left;width:10%;">
                        Rp.
                    </div>
                    <div style="float:right;width:50%;">
                        <b>{{ number_format($debet) }}</b>
                    </div>
                </td>
                <td colspan="1" class="text-right" style="width: 150px">
                    <div style="float:left;width:10%;">
                        Rp.
                    </div>
                    <div style="float:right;width:50%;">
                        <b>{{ number_format($kredit) }}</b>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><b>TOTAL PENDAPATAN</b></td>
                <td colspan="2" class="text-right">
                    <div style="float:left;width:0%;">
                        Rp.
                    </div>
                    <div style="float:right;width:50%;">
                        <b>{{ number_format($debet - $kredit) }}</b>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="intro-y col-span-12 p-8 my-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white"
    id="isi2">
    <div class="col-span-12 text-right  mb-3">
        <button class="btn btn-primary shadow-md mr-2" onclick="excel2()">Export Excel</button>
    </div>
    <table class="table-item" style="width: 100%;border-color: black" border="1">
        <thead align="center">
            <tr>
                <th>TANGGAL</th>
                <th>DESKRIPSI</th>
                <th>PEMASUKAN</th>
                <th>PENGELUARAN</th>
            </tr>
        </thead>
        <tbody>
            @php
                $debet = 0;
                $kredit = 0;
                $debetTertinggi = 0;
                $kreditTertinggi = 0;
            @endphp
            @foreach ($dates as $i => $item)
                @php
                    $debet += $item['penerimaan'];
                    $kredit += $item['pengeluaran'];
                    
                    if ($debetTertinggi < $item['penerimaan']) {
                        $debetTertinggi = $item['penerimaan'];
                    }
                    
                    if ($kreditTertinggi < $item['pengeluaran']) {
                        $kreditTertinggi = $item['pengeluaran'];
                    }
                @endphp
                <tr>
                    <td>{{ $item['tanggal'] }}</td>
                    <td>PEMASUKAN KLINIK</td>
                    <td style="text-align: right">{{ number_format($item['penerimaan']) }}</td>
                    <td style="text-align: right">0</td>
                </tr>
                <tr>
                    <td></td>
                    <td>PENGELUARAN KLINIK</td>
                    <td style="text-align: right">0</td>
                    <td style="text-align: right">{{ number_format($item['pengeluaran']) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right">Total</td>
                <td style="text-align: right">{{ number_format($debet) }}</td>
                <td style="text-align: right">{{ number_format($kredit) }}</td>
            </tr>
            <tr>
                <td colspan="2">Rata-rata per Hari</td>
                <td style="text-align: right">{{ number_format($debet / count($dates)) }}</td>
                <td style="text-align: right">{{ number_format($kredit / count($dates)) }}</td>
            </tr>
            <tr>
                <td colspan="2">Nilai Tertinggi</td>
                <td style="text-align: right">{{ number_format($debetTertinggi) }}</td>
                <td style="text-align: right">{{ number_format($kreditTertinggi) }}</td>
            </tr>
        </tbody>

    </table>
</div>
