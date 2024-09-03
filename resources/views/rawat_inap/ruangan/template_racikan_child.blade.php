<div class="col-span-12 parent-child-racikan">
    <div class="grid grid-cols-12 gap-2">
        <div class="col-span-6 mb-2 parent">
            <label for="">Produk Obat {{ dot() }}</label>
            <select name="racikan_produk_obat_{{ $req->index }}[]"
                class="racikan_produk_obat form-control required select2resep">
                <option value="">Pilih Jenis Obat</option>
                @foreach ($produkObat as $item)
                    <option
                        {{ $item->StockFirst != null? ($item->StockFirst->qty == 0? 'disabled="disabled"': ''): 'disabled="disabled"' }}
                        value="{{ $item->id }}">{{ $item->name }} {{ $item->dosis }}
                        {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stok Kosong)' : '') : '(Stok Kosong)' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2 parent">
            <label for="">Qty {{ dot() }}</label>
            <input type="number" class="form-control harga w-100 mask-non-decimal text-right required racikan_qty"
                name="racikan_qty_{{ $req->index }}[]">
        </div>
        <div class="col-span-2">
            <label class="block">&nbsp;</label>
            <a type="button" onclick="hapusRacikanChild(this)"><i class="fa fa-trash w-full cursor-pointer text-danger"></i></a>
        </div>
    </div>
</div>
