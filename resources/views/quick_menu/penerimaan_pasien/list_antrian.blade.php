@foreach ($data as $item)
    <tr>
        <td class="border-l-2 border-b-2 px-3 py-2 text-center">
            {{ $item->kode_pendaftaran }}
        </td>
        <td class="border-l-2 border-r-2 border-b-2 px-3 py-2 text-center">
            {{ $item->requestDokter ? $item->requestDokter->name : '-' }}
        </td>
        <td class="border-l-2 border-r-2 border-b-2 px-3 py-2 text-center">
            @if ($item->requestDokter)
                @if ($item->requestDokter->id == Auth::user()->id)
                    <button class="btn btn-primary shadow-md mr-2" id="tambah-data"
                        onclick="terimaPasien('{{ $item->id }}')">Terima Pasien</button>
                @else
                -
                @endif
            @else
                <button class="btn btn-primary shadow-md mr-2" id="tambah-data"
                    onclick="terimaPasien('{{ $item->id }}')">Terima Pasien</button>
            @endif
        </td>

         <td class="border-l-2 border-r-2 border-b-2 px-3 py-2 text-center">
            @if ($item->status_owner)
                    <div class="py-1 px-2 rounded-full text-xs bg-danger text-center text-white cursor-pointer font-medium">Leave</div>
            @else
                <div class="py-1 px-2 rounded-full text-xs bg-success text-center text-white cursor-pointer font-medium">Available</div>
            @endif
        </td>
    </tr>
@endforeach
