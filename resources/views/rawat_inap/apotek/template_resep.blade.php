<div class="col-span-12 mb-3 parent-resep border rounded p-2">
    <div class="grid grid-cols-12 gap-2">
        <div class="col-span-12">
            <a data-name="racikan" class="select-racikan racikan-button active" href="javascript:;">Racikan</a> |
            <a data-name="non-racikan" class="select-racikan non-racikan-button" href="javascript:;">Non Racikan</a>
            <input type="hidden" name="parent_resep[]" class="parent_resep" value="racikan">
            <input type="hidden" name="index_racikan[]" class="index_racikan" value="{{ $req->index }}">
            <input type="hidden" name="created_by[]" value="{{ me() }}">
            <input type="hidden" name="created_at[]" value="{{ now() }}">
            <input type="hidden" name="id_detail[]" value="0">
        </div>

        <div class="col-span-12 racikan-child racikan">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 md:col-span-6 ">
                    <label class="form-label">Jenis Obat {{ dot() }}</label>
                    <select name="jenis_obat_racikan[]" class="jenis_obat_racikan form-control required select2resep">
                        <option value="">Pilih Jenis Obat</option>
                        @foreach (\App\Models\KategoriObat::where('status', true)->get() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-4 md:col-span-2 parent">
                    <label class="form-label">Satuan {{ dot() }}</label>
                    <select name="satuan_racikan[]" class="satuan_racikan form-control required select2filter">
                        <option value="">Pilih Satuan</option>
                        @foreach (\App\Models\SatuanObat::where('status', true)->get() as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-4 md:col-span-2 parent">
                    <label for="" class="form-label">Jumlah Obat {{ dot() }}</label>
                    <input type="number" class="form-control w-100 text-right mask-non-decimal required qty_racikan"
                        name="qty_racikan[]">
                </div>
                <div class="col-span-4 md:col-span-2 parent">
                    <label class="form-label block">&nbsp;</label>
                    <button type="button" class="btn btn-danger" onclick="hapusResep(this)"><i
                            class="fa fa-trash w-100"></i></button>
                </div>
                <div class="col-span-12 parent text-info">
                    <a href="javascript:;" onclick="tambahChildRacikan(this)"><i class="fa fa-plus"
                            aria-hidden="true"></i> Tambah Racikan</a>
                </div>
                <div class="col-span-12 v">
                    <div class="append-racikan pl-5">

                    </div>
                </div>
                <div class="col-span-12 parent">
                    <label class="form-label">Signature (Keterangan) {{ dot() }}</label>
                    <textarea name="description_racikan[]" class="form-control description_racikan required" cols="2" rows="2"></textarea>
                </div>
            </div>
        </div>
        <div class="col-span-12 racikan-child non-racikan hidden">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 md:col-span-6 parent">
                    <label for="" class="form-label">Produk Obat {{ dot() }}</label>
                    <select name="produk_obat_non_racikan[]" onchange="nonRacikanObat(this)"
                        class="produk_obat_non_racikan form-control required select2resep">
                        <option value="">Pilih Jenis Obat</option>
                        @foreach ($produkObat as $item)
                            <option data-qty="{{ $item->StockFirst != null ? $item->StockFirst->qty : 0 }}"
                                {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? 'disabled="disabled"' : '') : 'disabled="disabled"' }}
                                value="{{ $item->id }}">{{ $item->name }}
                                {{-- {{ $item->dosis }} mg --}}
                                {{ $item->StockFirst != null ? ($item->StockFirst->qty == 0 ? '(Stock Kosong)' : '') : '(Stock Kosong)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-4 md:col-span-2 mb-2 parent">
                    <label for="" class="form-label">Stok Obat</label>
                    <input type="number" class="form-control w-100 text-right required sisa_qty_non_racikan" readonly
                        value="" name="sisa_qty_non_racikan[]">
                </div>
                <div class="col-span-4 md:col-span-2 mb-2 parent">
                    <label for="" class="form-label">Jumlah Obat {{ dot() }}</label>
                    <input type="number"
                        class="form-control w-100 text-right required mask-non-decimal qty_non_racikan"
                        name="qty_non_racikan[]">
                </div>

                <div class="col-span-4 md:col-span-2 mb-2 parent">
                    <label class="block form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger" onclick="hapusResep(this)"><i
                            class="fa fa-trash w-100"></i></button>
                </div>
                <div class="col-span-12 parent">
                    <label class="form-label">Signature (Keterangan) {{ dot() }}</label>
                    <textarea name="description_non_racikan[]" class="form-control description_non_racikan required" cols="2"
                        rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
