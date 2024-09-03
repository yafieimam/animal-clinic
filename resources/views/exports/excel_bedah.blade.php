<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Pasien</th>
            <th>Owner</th>
            <th>Diagnosa</th>
            <th>Tindakan Bedah</th>
            <th>Ruangan</th>
            <th>Tanggal Bedah</th>
            <th>Urgensi</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->RekamMedisPasien->kode }}</td>
                <td>{{ $item->RekamMedisPasien->Pasien->name }}</td>
                <td>{{ $item->RekamMedisPasien->pasien != null ? ($item->RekamMedisPasien->pasien != '' ? $item->RekamMedisPasien->pasien->owner->name : '-') : '-' }}
                </td>
                <td>{{ $item->RekamMedisPasien->diagnosa }}</td>
                <td>{{ $item->Tindakan ? $item->Tindakan->name : '-' }}</td>
                <td>
                    @php
                        $html = '';
                        $temuin = array("<",">");
                        $ganti = array("Kurang dari", "Lebih dari");

                        foreach ($item->RekamMedisPasien->KamarRawatInapDanBedahDetail->sortBy('created_at') as $key => $value) {
                            $html = $value->KamarRawatInapDanBedah->name;
                            $munculin = str_replace ($temuin,$ganti,$html);
                        }
                        echo "<span>{$munculin}</span>";
                    @endphp
                </td>
                <td>{{ $item->tanggal_rekomendasi_bedah }}</td>
                <td>
                    @php
                        if ($item->status_urgensi == 'true') {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Urgent</div>';
                        } else {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium"  style="background:blue">Normal</div>';
                        }
                    @endphp
                </td>
                <td>
                    @php
                        if ($item->status == 'Released') {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Menunggu</div>';
                        } else {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-info text-white cursor-pointer font-medium">Done</div>';
                        }
                    @endphp
                </td>
            </tr>
        @endforeach
        {{-- {{ dd($data) }} --}}
    </tbody>
</table>
