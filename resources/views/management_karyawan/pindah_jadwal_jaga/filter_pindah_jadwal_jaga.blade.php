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
    <label for="dokter_peminta_filter" class="form-label">Filter Dokter Pembuat</label>
    <select name="dokter_peminta_filter" id="dokter_peminta_filter" class="select2filter form-control">
        <option value="">Semua Dokter</option>
        @foreach (dokter() as $item)
            <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->role->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label for="dokter_diminta_filter" class="form-label">Filter Dokter Penerima</label>
    <select name="dokter_diminta_filter" id="dokter_diminta_filter" class="select2filter form-control">
        <option value="">Semua Dokter</option>
        @foreach (dokter() as $item)
            <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->role->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <button class="btn btn-primary shadow-md mr-2 w-full" onclick="filter()">
        <i class="fas fa-search mr-2"></i> Search
    </button>
</div>
