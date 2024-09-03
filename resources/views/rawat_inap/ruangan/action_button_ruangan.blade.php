<div class="dropdown">
    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
        <span class="w-5 h-5 flex items-center justify-center">
            <i class="fa fa-bars"></i>
        </span>
    </button>
    <div class="dropdown-menu w-56 ">
        <ul class="dropdown-content">
            @if (Auth::user()->akses('edit'))
                <li>
                    <a href="javascript:;" onclick="openModal('{{ $data->pasien_id }}')"
                        class="dropdown-item text-warning">
                        <i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Ubah
                    </a>
                </li>
            @endif
            <a href="javascript:;" onclick="lihatRekamMedis('{{ $data->id }}')" class="dropdown-item text-info">
                <i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Lihat Rekam Medis
            </a>
            <a href="javascript:;"
                onclick="formPersetujuan('{{ $data->id }}','{{ $data->upload_form_persetujuan }}')"
                class="dropdown-item text-danger">
                <i class="fa-solid fa-handshake"></i>&nbsp;&nbsp;&nbsp;Form Persetujuan
            </a>
        </ul>
    </div>
</div>
