<div class="dropdown">
    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
        <span class="w-5 h-5 flex items-center justify-center">
            <i class="fa fa-bars"></i>
        </span>
    </button>
    <div class="dropdown-menu w-40 ">
        <ul class="dropdown-content">
            @if ($data->PenerimaanStock)
                @if ($data->PenerimaanStock->status == 'Belum Diterima')
                    @if (Auth::user()->akses('edit'))
                        <li>
                            <a href="{{ route('editPengeluaranStock', ['id' => Crypt::encrypt($data->id)]) }}"
                                class="dropdown-item text-info">
                                <i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->akses('delete'))
                        <li>
                            <a href="javascript:;" onclick="hapus('{{ $data->id }}')"
                                class="dropdown-item text-danger">
                                <i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus
                            </a>
                        </li>
                    @endif
                @endif
            @else
                @if (Auth::user()->akses('edit'))
                    <li>
                        <a href="{{ route('editPengeluaranStock', ['id' => Crypt::encrypt($data->id)]) }}"
                            class="dropdown-item text-info">
                            <i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah
                        </a>
                    </li>
                @endif
                @if (Auth::user()->akses('delete'))
                    <li>
                        <a href="javascript:;" onclick="hapus('{{ $data->id }}')" class="dropdown-item text-danger">
                            <i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus
                        </a>
                    </li>
                @endif
            @endif
            <li>
                <a href="{{ route('lihatPengeluaranStock', ['id' => Crypt::encrypt($data->id)]) }}"
                    class="dropdown-item text-warning">
                    <i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat
                </a>
            </li>
            <li>
                <a href="{{ route('printPengeluaranStock', ['id' => Crypt::encrypt($data->id)]) }}"
                    class="dropdown-item text-pending">
                    <i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Print
                </a>
            </li>
        </ul>
    </div>
</div>
