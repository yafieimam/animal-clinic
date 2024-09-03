<div class="col-span-12 mb-3">
    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control required datepicker"
            placeholder="yyyy-mm-dd" value="{{ \carbon\carbon::now()->startOfMonth()->format('Y-m-d') }}"
            data-single-mode="true">
    </div>
</div>
<div class="col-span-12 mb-3">
    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="tanggal_akhir" name="tanggal_akhir" type="text" class="form-control required datepicker"
            placeholder="yyyy-mm-dd" placeholder="yyyy-mm-dd" data-single-mode="true">
    </div>
</div>
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
    <button class="btn btn-primary shadow-md mr-2 w-full" onclick="filter()">
        <i class="fas fa-search mr-2"></i> Search
    </button>
</div>
<div class="col-span-12 mb-3">
    <button class="btn btn-warning shadow-md mr-2 w-full" onclick="reseting('#slide-over-filter')">
        <i class="fas fa-refresh mr-2"></i> Reset
    </button>
</div>
