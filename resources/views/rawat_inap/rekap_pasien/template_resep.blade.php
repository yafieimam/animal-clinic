<div class="col-span-12 mb-3 parent-resep border rounded p-2">
    <div class="grid grid-cols-12 gap-2">
        <div class="col-span-12">
            <a data-name="racikan" class="select-racikan racikan-button active" href="javascript:;">Racikan</a> |
            <a data-name="non-racikan" class="select-racikan non-racikan-button" href="javascript:;">Non Racikan</a>
            <input type="hidden" name="parent_resep[]" class="parent_resep" value="racikan">
            <input type="hidden" name="index_racikan[]" class="index_racikan" value="{{ $req->index }}">
        </div>
        <div class="col-span-12 racikan-child parent racikan">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-6 ">
                    <label class="form-label">Jenis Obat {{ dot() }}</label>
                    <select name="jenis_obat_racikan[]" class="jenis_obat_racikan form-control required select2resep">
                        <option value="">Pilih Jenis Obat</option>
                        @foreach (\App\Models\KategoriObat::where('status', true)->get() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-4">
                    <label class="form-label">Satuan {{ dot() }}</label>
                    <select name="satuan_racikan[]" class="satuan_racikan form-control required select2resep">
                        <option value="">Pilih Satuan</option>
                        @foreach (\App\Models\SatuanObat::where('status', true)->get() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="form-label block">&nbsp;</label>
                    <button type="button" class="btn btn-danger" onclick="hapusResep(this)"><i
                            class="fa fa-trash w-100"></i></button>
                </div>
                <div class="col-span-12 text-info">
                    <a href="javascript:;" onclick="tambahChildRacikan(this)"><i class="fa fa-plus"
                            aria-hidden="true"></i> Tambah Racikan</a>
                </div>
                <div class="col-span-12">
                    <div class="append-racikan pl-5">

                    </div>
                </div>
                <div class="col-span-12">
                    <label class="form-label">Signature (Keterangan) {{ dot() }}</label>
                    <textarea name="description_racikan[]" class="form-control description_racikan required" cols="2" rows="2"></textarea>
                </div>
            </div>
        </div>
        <div class="col-span-12 racikan-child parent non-racikan hidden">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-8">
                    <label for="" class="form-label">Produk Obat {{ dot() }}</label>
                    <select name="produk_obat_non_racikan[]"
                        class="produk_obat_non_racikan form-control required select2resep">
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
                <div class="col-span-2">
                    <label for="" class="form-label">Qty {{ dot() }}</label>
                    <input type="number" class="form-control w-100 text-right required qty_non_racikan"
                        name="qty_non_racikan[]">
                </div>

                <div class="col-span-2">
                    <label class="block form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger" onclick="hapusResep(this)"><i
                            class="fa fa-trash w-100"></i></button>
                </div>
                <div class="col-span-12">
                    <label class="form-label">Signature (Keterangan) {{ dot() }}</label>
                    <textarea name="description_non_racikan[]" class="form-control description_non_racikan required" cols="2"
                        rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
