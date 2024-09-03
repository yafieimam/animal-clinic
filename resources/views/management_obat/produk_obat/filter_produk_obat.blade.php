<div class="col-span-12 md:col-span-3 ">
    <label for="poli_id_filter" class="form-label">Filter Kategori Obat</label>
    <select name="kategori_obat_id_filter" id="kategori_obat_id_filter" class="select2filter form-control">
        <option value="">Semua Kategori Obat</option>
        @foreach (\App\Models\KategoriObat::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 md:col-span-3 ">
    <label for="satuan_obat_id_filter" class="form-label">Filter Satuan Obat</label>
    <select name="satuan_obat_id_filter" id="satuan_obat_id_filter" class="select2filter form-control">
        <option value="">Semua Satuan Obat</option>
        @foreach (\App\Models\SatuanObat::get() as $item)
            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 md:col-span-3 mb-3">
    <label for="type_obat_id_filter" class="form-label">Filter Sediaan Obat</label>
    <select name="type_obat_id_filter" id="type_obat_id_filter" class="select2filter form-control">
        <option value="">Semua Sediaan Obat</option>
        @foreach (\App\Models\TypeObat::get() as $item)
            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <button class="btn btn-primary shadow-md mr-2 w-full" onclick="filter()">
        <i class="fas fa-search mr-2"></i> Search
    </button>
</div>

<div class="col-span-12 mb-3">
    <button class="btn btn-warning shadow-md mr-2 w-full" onclick="reseting('#slide-over-filter')">
        <i class="fas fa-refresh mr-2"></i> Reset
    </button>
</div>
