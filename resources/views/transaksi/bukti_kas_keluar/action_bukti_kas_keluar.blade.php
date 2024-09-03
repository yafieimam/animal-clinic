<div class="dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fa fa-bars"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-left">
        @if ($data->status == 'Released')
            <a href="{{ route('editBuktiKasKeluar', ['id' => Crypt::encrypt($data->id)]) }}"
                class="dropdown-item text-info"><i class="fa fa-edit"></i> Ubah</a>
            <div class="dropdown-divider"></div>
            <a href="javascript:;" onclick="hapus('{{ $data->id }}')" class="dropdown-item text-danger">
                <i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Hapus
            </a>
            <div class="dropdown-divider"></div>
        @endif
        <a href="{{ route('lihatBuktiKasKeluar', ['id' => Crypt::encrypt($data->id)]) }}"
            class="dropdown-item text-warning"><i class="fa fa-edit"></i> Lihat
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('printBuktiKasKeluar', ['id' => Crypt::encrypt($data->id)]) }}"
            class="dropdown-item text-secondary">
            <i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Print
        </a>
        @if ($data->status == 'Released' && Auth::user()->akses('validation'))
            <div class="dropdown-divider"></div>
            <a href="{{ route('terimaBuktiKasKeluar', ['id' => Crypt::encrypt($data->id)]) }}"
                class="dropdown-item text-info">
                <i class="fa fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Setujui Bukti Kas Keluar
            </a>
        @elseif($data->status == 'Rejected')
            <div class="dropdown-divider"></div>
            <a href="javascript:;" class="dropdown-item text-info">
                <span class="badge badge-danger">Sudah Ditolak</span>
            </a>
        @elseif($data->status == 'Approved')
            <div class="dropdown-divider"></div>
            <a href="javascript:;" class="dropdown-item text-info">
                <span class="badge badge-primary">Sudah Disetujui</span>
            </a>
        @endif
    </div>
</div>
