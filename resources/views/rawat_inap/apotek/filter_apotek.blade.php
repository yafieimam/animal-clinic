<div class="col-span-12 mb-3">
    <label for="rekomendasi_tanggal_bedah" class="form-label">Tanggal Operasi</label>
    <div class="input-group parent">
        <div class="input-group-text">
            <i class="fas fa-calendar"></i>
        </div>
        <input id="rekomendasi_tanggal_bedah" name="rekomendasi_tanggal_bedah" type="text"
            class="form-control required datepicker" placeholder="yyyy-mm-dd" placeholder="yyyy-mm-dd"
            data-single-mode="true">
    </div>
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
