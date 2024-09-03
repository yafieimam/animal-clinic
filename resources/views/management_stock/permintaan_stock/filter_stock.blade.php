<div class="col-span-12 mb-3">
    <label for="branch_id_filter" class="form-label">Filter Branch</label>
    <select name="branch_id_filter" id="branch_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Branch</option>
        @foreach (\App\Models\Branch::get() as $item)
            <option value="{{ $item->id }}">{{ $item->lokasi }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="jenis_item" class="form-label">Filter Jenis Stock</label>
    <select name="jenis_item" id="jenis_item" class="form-control select2filter required">
        <option value="">Pilih Jenis Item</option>
        @foreach (\App\Models\PenerimaanStockDetail::$enumJenisStock as $item)
            <option data-name="{{ $item }}" value="{{ $item }}">
                {{ $item }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="item_id" class="form-label">Filter Item</label>
    <select name="item_id" id="item_id" class="form-control select2filter required">
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
