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
    <label for="divisi_id_filter" class="form-label">Filter Divisi</label>
    <select name="divisi_id_filter" id="divisi_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Divisi</option>
        @foreach (\App\Models\Divisi::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="bagian_id_filter" class="form-label">Filter Bagian</label>
    <select name="bagian_id_filter" id="bagian_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Bagian</option>
        @foreach (\App\Models\Bagian::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="jabatan_id_filter" class="form-label">Filter Jabatan</label>
    <select name="jabatan_id_filter" id="jabatan_id_filter" class="select2filter form-control w-full">
        <option value="">Semua Jabatan</option>
        @foreach (\App\Models\Jabatan::get() as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="jenis_kelamin" class="form-label">Filter Jenis Kelamin</label>
    <select name="jenis_kelamin_filter" id="jenis_kelamin_filter" class="select2filter form-control w-full">
        <option value="">Semua Jenis Kelamin</option>
        @foreach (\App\Models\Karyawan::$enumJenisKelamin as $item)
            <option value="{{ $item }}">{{ $item }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="status_pernikahan_filter" class="form-label">Filter Status Pernikahan</label>
    <select name="status_pernikahan_filter" id="status_pernikahan_filter" class="select2filter form-control w-full">
        <option value="">Semua Status Pernikahan</option>
        @foreach (\App\Models\Karyawan::$enumStatusPernikahan as $item)
            <option value="{{ $item }}">{{ $item }}</option>
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
