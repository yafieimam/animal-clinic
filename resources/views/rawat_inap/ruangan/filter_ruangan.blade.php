<div class="col-span-12 mb-3">
    <label for="branch_id_filter" class="form-label">Filter Branch</label>
    <select name="branch_id_filter" id="branch_id_filter" class="select2filter form-control w-full">
        @if (Auth::user()->akses('global'))
            <option value="">Semua Branch</option>
        @endif
        @foreach (cabangFixed() as $item)
            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->lokasi }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="dokter_id" class="form-label">Filter Dokter</label>
    <select name="dokter_id" id="dokter_id" class="select2filter form-control w-full">
        <option value="">Semua Dokter</option>
        @foreach ($dokter as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="binatang_id_filter" class="form-label">Filter Hewan</label>
    <select name="binatang_id_filter" id="binatang_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Hewan</option>
        @foreach ($hewan as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="owner_id_filter" class="form-label">Filter Owner</label>
    <select name="owner_id_filter" id="owner_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Owner</option>
        @foreach ($owner as $item)
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
