<div class="col-span-12 mb-3">
    <label for="branch_id_filter" class="form-label">Filter Branch</label>
    <select name="branch_id_filter" id="branch_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Branch</option>
        @foreach (\App\Models\Branch::get() as $item)
            <option value="{{ $item->id }}">{{ $item->kode }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="owner_id_filter" class="form-label">Filter Owner</label>
    <select name="owner_id_filter" id="owner_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Owner</option>
        @foreach (\App\Models\Owner::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="binatang_id_filter" class="form-label">Filter Binatang</label>
    <select name="binatang_id_filter" id="binatang_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Binatang</option>
        @foreach (\App\Models\Binatang::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="ras_id_filter" class="form-label">Filter Ras</label>
    <select name="ras_id_filter" id="ras_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Ras</option>
        @foreach (\App\Models\Ras::get() as $item)
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