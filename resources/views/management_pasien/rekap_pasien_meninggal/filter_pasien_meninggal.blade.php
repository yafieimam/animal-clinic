<form id="data-filter">
    <div class="col-span-12 mb-3">
        <label for="tanggal_awal" class="form-label">Tanggal Awal (Meninggal)</label>
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
        <label for="tanggal_akhir" class="form-label">Tanggal Akhir (Meninggal)</label>
        <div class="input-group parent">
            <div class="input-group-text">
                <i class="fas fa-calendar"></i>
            </div>
            <input id="tanggal_akhir" name="tanggal_akhir" type="text" class="form-control required datepicker"
                placeholder="yyyy-mm-dd" placeholder="yyyy-mm-dd" data-single-mode="true">
        </div>
    </div>
    <div class="col-span-12 mb-3">
        <label for="binatang_id_filter" class="form-label">Filter Hewan</label>
        <select name="binatang_id" id="binatang_id_filter" class="select2filter form-control w-full">
            <option value="">Semua Hewan</option>
            @foreach ($hewan as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-12 mb-3">
        <label for="owner_id_filter" class="form-label">Filter Owner</label>
        <select name="owner_id" id="owner_id_filter" class="select2filter form-control w-full">
            <option value="">Semua Owner</option>
            @foreach ($owner as $item)
                <option value="{{ $item->id }}">{{ $item->kode }} | {{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-12 mb-3">
        <label for="dokter_poli" class="form-label">Filter Dokter</label>
        <select name="dokter_poli" id="dokter_poli" class="select2filter form-control w-full">
            <option value="">Semua Dokter</option>
            @foreach ($dokter as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-12 mb-3">
        <label for="kamar_rawat_inap_dan_bedah_id" class="form-label">Filter Kamar Rawat Inap Dan Bedah</label>
        <select name="kamar_rawat_inap_dan_bedah_id" id="kamar_rawat_inap_dan_bedah_id"
            class="select2filter form-control w-full">
            <option value="">Semua Kamar Rawat Inap Dan Bedah</option>
            @foreach ($kamarRawatInapDanBedah as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
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
        <button type="button" class="btn btn-primary shadow-md mr-2 w-full" onclick="filter()">
            <i class="fas fa-search mr-2"></i> Search
        </button>
    </div>
    <div class="col-span-12 mb-3">
        <button type="button" class="btn btn-warning shadow-md mr-2 w-full" onclick="reseting('#slide-over-filter')">
            <i class="fas fa-refresh mr-2"></i> Reset
        </button>
    </div>
</form>
