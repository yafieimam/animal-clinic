<div class="col-span-12 mb-3">
    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control required datepicker"
            placeholder="yyyy-mm-dd" value="{{date('Y-m-d')}}"
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
    <label for="status_filter" class="form-label">Filter Status</label>
    <select name="status_filter" id="status_filter" class="select2filter form-control">
        <option value="">Semua Status</option>
        <option value="Released">Released</option>
        <option value="Done">Done</option>
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="branch_id" class="form-label">Filter Branch</label>
    <select name="branch_id" id="branch_id" class="select2filter form-control w-full">
        @if (Auth::user()->akses('global'))
            <option value="">Semua Branch</option>
        @endif
        @foreach (cabangFixed() as $item)
            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->lokasi }}</option>
        @endforeach
    </select>
</div>


<!-- <div class="col-span-12 mb-3">
    <label for="branch_id_filter" class="form-label">Filter Branch</label>
    <select name="branch_id_filter" id="branch_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Branch</option>
        @foreach (\App\Models\Branch::orderBy('id', 'ASC')->get() as $item)
            <option value="{{ $item->id }}">{{ $item->lokasi }}</option>
        @endforeach
    </select>
</div> -->
<div class="col-span-12 mb-3">
    <label for="owner_id_filter" class="form-label">Filter Owner</label>
    <select name="owner_id_filter" id="owner_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Owner</option>
        @foreach (\App\Models\Owner::get() as $item)
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
<!-- <div class="col-span-12 mb-3  w-full">
    <button class="btn btn-warning shadow-md mr-2 w-full" onclick="excel()"><i
            class="fa-solid fa-file-excel"></i>&nbsp;Excel</button>
</div> -->
