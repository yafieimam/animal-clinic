<div class="dropdown">
    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
        <span class="w-5 h-5 flex items-center justify-center">
            <i class="fa fa-bars"></i>
        </span>
    </button>
    <div class="dropdown-menu w-40 ">
        <ul class="dropdown-content">
            @if (Auth::user()->akses('edit'))
                <li>
                    <a href="javascript:;" onclick="edit('{{ $data->id }}')" class="dropdown-item text-info">
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
        </ul>
    </div>
</div>
