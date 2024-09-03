<table class="table table-bordered">
    <thead>
        <th class="text-center">No</th>
        <th>Jam</th>
        @foreach (hari() as $i => $d)
            <th class="text-center">{{ ucwords($d) }}</th>
        @endforeach
    </thead>
    <tbody>
        @php
            $hari['senin'] = [];
            $hari['selasa'] = [];
            $hari['rabu'] = [];
            $hari['kamis'] = [];
            $hari['jumat'] = [];
            $hari['sabtu'] = [];
            $hari['minggu'] = [];
        @endphp
        @foreach ($data as $i => $d)
            <tr>
                <td class="text-center">{{ $d->sequence }}</td>
                <td>{{ $d->jam_awal }}:{{ $d->menit_awal }} -
                    {{ $d->jam_akhir }}:{{ $d->menit_akhir }}</td>
                @foreach (hari() as $i1 => $d1)
                    @php
                        $count = 0;
                        $urutan_array = [];
                        $jadwal = $d->JamPertama
                            ->where('poli_id', $req->poli_id)
                            ->where('branch_id', $req->branch_id)
                            ->where('hari', hari()[$i1])
                            ->whereHas('JadwalDokterDetail', function ($q) use ($req) {
                                $q->where('dokter', $req->dokter);
                            })
                            ->where('jam_pertama_id', $d->id)
                            ->first();

                        if ($jadwal != null) {
                            $count = $jadwal->JamTerakhir->sequence - $jadwal->JamPertama->sequence;

                            if ($count == 0) {
                                $count = 1;
                                $rowspan = $count;
                            } else {
                                $rowspan = $count + 1;
                            }

                            for ($i2 = 0; $i2 < $count; $i2++) {
                                if ($count == 1) {
                                    if ($jadwal->JamPertama->sequence != $jadwal->JamTerakhir->sequence) {
                                        array_push($hari[hari()[$i1]], $jadwal->JamTerakhir->sequence);
                                    }
                                } else {
                                    array_push($hari[hari()[$i1]], $jadwal->JamTerakhir->sequence - $i2);
                                }
                            }
                        } else {
                            $rowspan = 1;
                        }

                    @endphp
                    @if (!in_array($d->sequence, $hari[hari()[$i1]]))

                        <td class="text-center {{ $count >= 1 ? 'pointer' : '' }}" rowspan="{{ $rowspan }}"
                            @if ($count >= 1) onclick="edit('{{ $jadwal->id }}')" @endif>
                    @endif
                    @if ($count >= 1)
                        <ul>
                            @foreach ($jadwal->JadwalDokterDetail as $item)
                                <li>
                                    <i style="font-size: 12px;"><b>{{ $item->Dokter->name }}</b></i><br>
                                    <i style="font-size: 10px;">Klik Untuk Edit/Hapus</i>
                                </li>
                            @endforeach
                        </ul>
                        </td>
                    @else
                        <i style="cursor: pointer;color: green" class="fa fa-plus"
                            onclick="addData('{{ $d->id }}','{{ hari()[$i1] }}')"></i>
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
