<ul>
    @foreach ($kamar as $item)
        <li data-jenis="kategori_kamar" data-value="{{ $item->id }}" data-jstree='{"icon":"fa-solid fa-bed-pulse"}'>
            &nbsp;&nbsp;{{ $item->name }}
            <ul>
                @foreach ($item->KamarRawatInapDanBedah as $item1)
                    <li data-jenis="kamar_rawat_inap_dan_bedah" data-value="{{ $item1->id }}"><a
                            href="#">[{{ $item1->Branch->kode }}] {{ $item1->name }} ({{ $item1->jumlah }})</a></li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
