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
    <label for="rekomendasi_tanggal_bedah" class="form-label">Tanggal Operasi</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="rekomendasi_tanggal_bedah" value="{{ dateStore() }}" name="rekomendasi_tanggal_bedah"
            type="text" class="form-control datepicker" placeholder="yyyy-mm-dd" placeholder="yyyy-mm-dd"
            data-single-mode="true">
    </div>
</div>
<div class="col-span-12 mb-3">
    <label class="form-label">Filter Ruangan Rawat Inap</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-bed"></i>
        </div>
        <select name="ruangan_rawat_inap" id="ruangan_rawat_inap" class="select2filter form-control w-full">
            <option value="">Semua Kamar</option>
            @foreach ($kamar as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-span-12 mb-3">
    <label for="tindakan_id_filter" class="form-label">Filter Tindakan Bedah</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fa-solid fa-syringe"></i>
        </div>
        <select name="tindakan_id_filter" id="tindakan_id_filter" class="select2filter form-control w-full">
            <option value="">Semua Tindakan Bedah</option>
            @foreach ($rekomendasiTindakanBedah as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-span-12 mb-3">
    <label for="status_filter" class="form-label">Filter Status Bedah</label>
    <select name="status_filter" id="status_filter" class="select2filter form-control w-full">
        <option selected value="Released">Released</option>
        <option value="Done">Done</option>
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
