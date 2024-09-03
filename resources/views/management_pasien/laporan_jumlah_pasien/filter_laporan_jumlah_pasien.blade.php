<div class="col-span-12 md:col-span-3 mb-3">
    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="tanggal_awal" name="tanggal_awal" type="text" class="form-control required datepicker"
            placeholder="yyyy-mm-dd" value="{{ \carbon\carbon::parse($req->tanggal_awal)->format('Y-m-d') }}"
            data-single-mode="true">
    </div>
</div>
<div class="col-span-12 md:col-span-3 mb-3">
    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="tanggal_akhir" name="tanggal_akhir" type="text" class="form-control required datepicker"
            placeholder="yyyy-mm-dd" value="{{ \carbon\carbon::parse($req->tanggal_akhir)->format('Y-m-d') }}"
            data-single-mode="true">
    </div>
</div>
@if (Auth::user()->akses('global'))
    <div class="col-span-12 md:col-span-3 mb-3">
        <label for="branch_id" class="form-label">Branch{{ dot() }}</label>
        <select name="branch_id" id="branch_id" class="select2filter form-control required">
            <option value="">Pilih Branch</option>
            @foreach (\App\Models\Branch::get() as $item)
                @if (Auth::user()->akses('global'))
                    <option value="{{ $item->id }}">
                        {{ $item->kode }} - {{ $item->alamat }}</option>
                @else
                    <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                        {{ $item->kode }} - {{ $item->alamat }}</option>
                @endif
            @endforeach
        </select>
    </div>
@endif
<div class="col-span-12 md:col-span-3 mb-3">
    <label for="tindakan_id" class="form-label">Tindakan{{ dot() }}</label>
    <select name="tindakan_id" id="tindakan_id" class="select2filter form-control required">
        <option value="">Pilih Tindakan</option>
        @foreach (\App\Models\Tindakan::get() as $item)
            <option {{ $req->tindakan_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                {{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 md:col-span-3 mb-3">
    <label for="binatang_id" class="form-label">Jenis Hewan{{ dot() }}</label>
    <select name="binatang_id" id="binatang_id" class="select2filter form-control required">
        <option value="">Pilih Binatang</option>
        @foreach (\App\Models\Binatang::get() as $item)
            <option {{ $req->binatang_id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                {{ $item->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-span-12 mb-3">
    <label class="form-label block">&nbsp;</label>
    <button class="btn btn-primary shadow-md mr-2 w-full" onclick="filter()"><i
            class="fas fa-search"></i>&nbsp;Search</button>
</div>
<div class="col-span-12 mb-3">
    <button class="btn btn-warning shadow-md mr-2 w-full" onclick="reseting('#slide-over-filter')">
        <i class="fas fa-refresh mr-2"></i> Reset
    </button>
</div>
