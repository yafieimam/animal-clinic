<table class="w-full">
    @foreach ($data as $item)
        <tr class="{{ $item->name }}">
            <td>
                {{ $item->name }}
                <input type="hidden" class="anamnesa_id" value="{{ $item->id }}">
            </td>
            <td>
                <input type="checkbox" class="mr-1 anamnesa_pilihan anamnesa_pilihan_ya ya">
                <label for="">Ya</label>
            </td>
            <td>
                <input type="checkbox" class="mr-1 anamnesa_pilihan anamnesa_pilihan_tidak tidak">
                <label for="">Tidak</label>
            </td>
            <td>
                <input type="text" class="form-control keterangan_anamnesa" placeholder="Masukan keterangan">
            </td>
        </tr>
    @endforeach
</table>
