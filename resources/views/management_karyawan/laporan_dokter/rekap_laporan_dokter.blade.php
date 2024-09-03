@extends('../layout/' . $layout)

@section('subcontent')
    <h2 class="intro-y text-lg font-medium mt-10">{{ convertSlug($global['title']) }}</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center justify-between mt-2">
            <div class="flex flex-wrap items-center">
                {{-- <div class="dropdown inline">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center">
                            <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40 ">
                        <ul class="dropdown-content">
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div> --}}
            </div>

            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" id="myInputTextField" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <div class="col-span-12 ">
            <h5><b>Filter</b></h5>
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
                            <option {{ Auth::user()->branch_id == $item->id ? 'selected' : '' }}
                                value="{{ $item->id }}">
                                {{ $item->kode }} - {{ $item->alamat }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif
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
        <div class="col-span-12 md:col-span-3">
            <label class="form-label block">&nbsp;</label>
            <button class="btn btn-primary shadow-md mr-2" onclick="filter()"><i
                    class="fas fa-search"></i>&nbsp;Search</button>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 p-8 mt-6 lg:mt-0 overflow-auto lg:overflow-visible rounded shadow bg-white">
            <table class="table mt-2 stripe hover table-bordered" id="table"
                style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead align="center">
                    <th>No</th>
                    <th>Nama Dokter</th>
                    <th>Branch</th>
                    <th>Jumlah Pasien</th>
                </thead>
                <tbody>
                    @php
                        $deposit = 0;
                        $cash = 0;
                        $debet = 0;
                        $transfer = 0;
                    @endphp
                    @foreach ($data as $i => $item)
                        <tr>
                            <td class="text-center" width=20>{{ $i + 1 }}</td>
                            <td class="text-left">{{ $item->karyawan->name }}</td>
                            <td class="text-center">{{ $item->branch->kode }}</td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right" colspan="3"><b><i>Total</i></b></td>
                        <td class="text-center">
                            {{ number_format($data->sum('jumlah')) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- END: Data List -->
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.select2filter').select2({
                width: '100%',
            })
        })

        function filter(params) {
            location.href = '{{ route('laporan-dokter') }}?tanggal_awal=' + $('#tanggal_awal').val() + '&tanggal_akhir' +
                $('#tanggal_akhir').val() + '&branch_id=' + $(
                    '#branch_id')
                .val();
        }
    </script>
@endsection
