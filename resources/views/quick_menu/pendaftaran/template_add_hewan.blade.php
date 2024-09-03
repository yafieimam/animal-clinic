<div class="col-span-12 parent_hewan parent_remove" style="padding: 15px">
    <div class="flex">
        <div class="bg-success rounded-t-md pointer flex align-middle justify-center btn-partai  text-center pt-1  mr-2"
            onclick="addHewan()">
            <i class="fas fa-plus text-white mr-1" aria-hidden="true"></i> <span class="text-white">Tambah?</span>
        </div>
        <div class="bg-red-600 rounded-t-md pointer flex align-middle justify-center btn-partai  text-center pt-1  mr-2"
            onclick="removeHewan(this)">
            <i class="fas fa-trash text-white mr-1" aria-hidden="true"></i> <span class="text-white">Hapus?</span>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-4 gap-y-3 border rounded" style="padding: 15px">
        <div class="col-span-12 md:col-span-4">
            <label for="">Foto</label>
            <input type="file" class="dropify text-sm">
        </div>
        <div class="col-span-12 md:col-span-8">
            <div class="grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 parent">
                    <label for="">Nama Hewan {{ dot() }}</label>
                    <select class="form-control select2 pasien_id required" name="pasien_id[]">
                        <option value="">Pilih Hewan</option>
                    </select>
                </div>
                <div class="col-span-12 md:col-span-4 parent">
                    <label class="flex justify-between"><span>Hewan {{ dot() }}</span>
                        <a href="javascript:;" onclick="window.open('{{ route('hewan') }}')"><i
                                class="fa fa-plus text-info"></i>
                        </a>
                    </label>
                    <select class="form-control binatang_id select2 required" name="binatang_id[]">
                    </select>
                </div>
                <div class="col-span-12 md:col-span-4 parent">
                    <label class="flex justify-between"><span>Ras {{ dot() }}</span>
                        <a href="javascript:;" onclick="window.open('{{ route('hewan') }}')"><i
                                class="fa fa-plus text-info"></i>
                        </a>
                    </label>
                    <select class="form-control select2 ras_id required" name="ras_id[]">
                        <option value="">Pilih Ras</option>
                    </select>
                </div>
                <div class="col-span-12 md:col-span-4 parent">
                    <label>Jenis Kelamin {{ dot() }}
                    </label>
                    <select class="form-control select2 sex" name="sex[]">
                        <option value="">Pilih Jenis Kelamin</option>
                        @foreach (\App\Models\Pasien::$enumJenisKelamin as $item)
                            <option value="{{ $item }}">
                                {{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 md:col-span-4 parent">
                    <label>Tanggal Lahir
                    </label>
                    <div class="input-group parent">
                        <div class="input-group-text">
                            <i class="fa-solid fa-cake-candles"></i>
                        </div>

                        <input type="text" class="form-control date_of_birth" data-single-mode="true"
                            name="date_of_birth[]">
                    </div>
                </div>
                <div class="col-span-12 md:col-span-4 parent">
                    <label>Umur
                    </label>
                    <div class="input-group parent">
                        <div class="input-group-text">
                            <i class="fa-solid fa-cake-candles"></i>
                        </div>
                        <input type="text" class="form-control umur" readonly name="umur[]" value="">
                    </div>
                </div>
                <div class="col-span-12 md:col-span-4 parent">
                    <label>Life Stage {{ dot() }}
                    </label>
                    <select class="form-control select2 life_stage" name="life_stage[]">
                        <option value="">Pilih Life Stage</option>
                        @foreach (\App\Models\Pasien::$enumLifeStage as $i => $item)
                            <option value="{{ $item['title'] }}">
                                {{ $item['title'] }} | {{ $item['description'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 parent">
                    <label for="">Ciri Khas (Specific Pattern)</label>
                    <textarea name="ciri_khas[]" class="form-control ciri_khas except" cols="2"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
