@foreach ($data as $item)
    <div class="intro-y">
        <div class="box py-4 mb-3 flex items-center">
            <div class="ml-4 mr-auto">
                <div class="font-medium">{{ $item->kode }}</div>
                <div class="text-slate-500 text-xs mt-0.5">Oleh {{ $item->pendaftaran->Dokter->name }}</div>
                <div class="text-slate-500 text-xs mt-0.5 mb-2">{{ $item->created_at }}</div>
                <button class="btn btn-primary shadow-md px-2 py-0" id="tambah-data"
                    onclick="lihatRekamMedis('{{ $item->id }}')">Lihat Rekam Medis</button>
            </div>
        </div>
    </div>
@endforeach
