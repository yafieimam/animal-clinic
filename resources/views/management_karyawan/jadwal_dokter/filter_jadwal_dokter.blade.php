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
    <label for="poli_id_filter" class="form-label">Filter Poli</label>
    <select name="poli_id_filter" id="poli_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Poli</option>
        @foreach (\App\Models\Poli::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="hari_filter" class="form-label">Filter Bagian</label>
    <select name="hari_filter" id="hari_filter" class="select2filter form-control w-full">
        <option value="">Pilih Hari</option>
        @foreach (hari() as $item)
            <option value="{{ $item }}">
                {{ ucwords($item) }}
            </option>
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
