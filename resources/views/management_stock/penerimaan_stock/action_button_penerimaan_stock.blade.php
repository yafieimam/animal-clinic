<div class="dropdown">
    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
        <span class="w-5 h-5 flex items-center justify-center">
            <i class="fa fa-bars"></i>
        </span>
    </button>
    <div class="dropdown-menu w-40 ">
        <ul class="dropdown-content">
            @if (!$data->PengeluaranStock)
                @if ($status != 'Sudah Terpakai')
                    @if (Auth::user()->akses('edit'))
                        <li>
                            <a href="{{ route('editPenerimaanStock', ['id' => Crypt::encrypt($data->id)]) }}"
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
                <a href="{{ route('lihatPenerimaanStock', ['id' => Crypt::encrypt($data->id)]) }}"
                    class="dropdown-item text-warning"><i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat
                </a>
            @else
                <li>
                    <a href="{{ route('lihatPenerimaanStock', ['id' => Crypt::encrypt($data->id)]) }}"
                        class="dropdown-item text-warning">
                        <i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat
                    </a>
                </li>
                @if ($data->status == 'Belum Diterima')
                    <li>
                        <a href="{{ route('terimaPenerimaanStock', ['id' => Crypt::encrypt($data->id)]) }}"
                            onclick="lihat('{{ $data->id }}')" class="dropdown-item text-warning">
                            <i class="fa fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Terima Stock
                        </a>
                    </li>
                @else
                    <a href="javascript:;" class="dropdown-item text-info">
                        <span class="badge badge-info">Sudah Diterima</span>
                    </a>
                @endif
            @endif
        </ul>
    </div>
</div>
