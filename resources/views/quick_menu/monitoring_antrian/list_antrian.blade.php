@foreach ($data as $item)
    <tr>
        <td class="border-l-2 border-b-2 px-3 py-2 text-center">
            {{ $item->kode_pendaftaran }}
        </td>
        <td class="border-l-2 border-r-2 border-b-2 px-3 py-2 text-center">
            {{ $item->requestDokter ? $item->requestDokter->name : 'Tidak ada' }}
        </td>
    </tr>
@endforeach
