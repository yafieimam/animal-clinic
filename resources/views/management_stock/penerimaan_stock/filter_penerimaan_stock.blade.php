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
    <label for="supplier_id_filter" class="form-label">Filter Supplier</label>
    <select name="supplier_id_filter" id="supplier_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Supplier</option>
        @foreach (\App\Models\Supplier::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
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
