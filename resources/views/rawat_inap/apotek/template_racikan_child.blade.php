<div class="col-span-12 parent-child-racikan">
    <div class="grid grid-cols-12 gap-2">
        <div class="col-span-12 md:col-span-4 mb-2 parent">
            <label for="">Produk Obat {{ dot() }}</label>
            <select name="racikan_produk_obat_{{ $req->index }}[]" onchange="racikanObat(this)"
                class="racikan_produk_obat form-control required select2resep">
                <option value="">Pilih Jenis Obat</option>
                @foreach ($produkObat as $item)
                    <option data-qty="{{ $item->StockFirst != null ? $item->StockFirst->qty : 0 }}"
                        {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                        value="{{ $item->id }}">{{ $item->name }} {{-- {{ $item->dosis }} mg --}}
                        {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stok Kosong)' : '') : '(Stok Kosong)' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-span-4 md:col-span-2 mb-2 parent">
            <label for="">Stok Obat</label>
            <input type="number" readonly class="form-control harga w-100 mask text-right required racikan_sisa_qty"
                name="racikan_sisa_qty_{{ $req->index }}[]">
        </div>
        <div class="col-span-4 md:col-span-2 mb-2 parent">
            <label for="">Jumlah Obat {{ dot() }}</label>
            <input type="number" class="form-control harga w-100 mask-non-decimal text-right required racikan_qty"
                name="racikan_qty_{{ $req->index }}[]">
        </div>
        <div class="col-span-4 md:col-span-2 mb-2 parent">
            <label class="block">&nbsp;</label>
            <a type="button" onclick="hapusRacikanChild(this)"><i
                    class="fa fa-trash w-full cursor-pointer text-danger"></i></a>
        </div>
    </div>
</div>
