<div class="col-span-12 mb-3">
    <label for="tanggal_periksa_awal" class="form-label">Tanggal Awal Periksa</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="tanggal_periksa_awal" value="{{ carbon\carbon::now()->startOfMonth()->format('Y-m-d') }}"
            name="tanggal_periksa_awal" type="text" class="form-control required datepicker" placeholder="yyyy-mm-dd"
            placeholder="yyyy-mm-dd" data-single-mode="true">
    </div>
</div>
<div class="col-span-12 mb-3">
    <label for="tanggal_periksa_akhir" class="form-label">Tanggal Akhir Periksa</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="tanggal_periksa_akhir" value="{{ carbon\carbon::now()->endOfMonth()->format('Y-m-d') }}"
            name="tanggal_periksa_akhir" type="text" class="form-control required datepicker"
            placeholder="yyyy-mm-dd" placeholder="yyyy-mm-dd" data-single-mode="true">
    </div>
</div>
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
    <label for="status_kepulangan" class="form-label">Filter Kepulangan</label>
    <select name="status_kepulangan" id="status_kepulangan" class="select2filter form-control w-full">
        <option value="">Semua Status Kepulangan</option>
        @foreach ($statusKepulangan as $item)
            <option value="{{ $item->status_kepulangan }}">{{ $item->status_kepulangan }}</option>
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
