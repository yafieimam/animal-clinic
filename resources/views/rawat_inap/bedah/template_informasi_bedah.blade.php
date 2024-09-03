<table class="table table-bordered">
    <thead class="table-dark">
        <th>Tindakan</th>
        <th>Tanggal</th>
        <th>Status Urgensi</th>
        <th>Status</th>
    </thead>
    <tbody>
        @foreach ($data->RekamMedisRekomendasiTindakanBedah as $item)
            <tr>
                <td>{{ $item->Tindakan ? $item->Tindakan->name : '-' }}</td>
                <td>{{ dateStore($item->tanggal_rekomendasi_bedah) }}</td>
                <td class="text-center">
                    @php
                        if ($item->status_urgensi == 'true') {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-danger text-white cursor-pointer font-medium">Urgent</div>';
                        } else {
                            echo '<div class="py-1 px-2 text-center rounded-full text-xs bg-info text-white cursor-pointer font-medium"  style="background:blue">Normal</div>';
                        }
                    @endphp
                </td>
                <td>
                    @php
                        if ($item->status == 'Done') {
                            echo '<div style="background:rgb(0, 149, 255)" class="py-1 px-2 rounded-full text-xs text-white cursor-pointer font-medium text-center">Done By ' . $item->UpdatedBy->name . '</div>';
                        } elseif ($item->status == 'Rejected') {
                            echo '<div style="background:rgb(194, 128, 37)" class="py-1 px-2 rounded-full text-xs text-white cursor-pointer font-medium text-center">Cancel By ' . $item->UpdatedBy->name . '</div>';
                        } else {
                            echo '<div class="py-1 px-2 rounded-full text-xs bg-pending text-white cursor-pointer font-medium text-center" >Menunggu</div>';
                        }
                    @endphp
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
