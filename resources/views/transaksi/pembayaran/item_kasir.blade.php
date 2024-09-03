@if (count($item) != 0)
    <li>Item Obat</li>
@endif
@foreach ($item as $d)
    <li class="mb-2">
        <button class="btn text-left btn-primary text-white btn-rounded rounded block" style="width:100%"
            onclick="tambahItem('{{ $d->id }}', `{{ $d->name }}`, `{{ $d->harga }}`, `{{ $d->StockFirst->qty }}`,'{{ $d->type }}')">
            <b class="block">{{ $d->name }}</b>
            <hr class="my-2">
            <div class="flex justify-between">
                <span>Harga : <b>{{ number_format($d->harga) }}</b></span>
                <span>Sisa Stok : <b>{{ $d->StockFirst->qty }}</b></span>
            </div>
        </button>
    </li>
@endforeach
@if (count($itemNonObat) != 0)
    <li>Item Non Obat</li>
@endif
@foreach ($itemNonObat as $d)
    <li class="mb-2">
        <button class="btn text-left btn-primary text-white btn-rounded rounded block" style="width:100%"
            onclick="tambahItem('{{ $d->id }}', `{{ $d->name }}`, `{{ $d->harga }}`, `25`,'{{ $d->type }}')">
            <b class="block">{{ $d->name }}</b>
            <hr class="my-2">
            <div class="flex justify-between">
                <span>Harga : <b>{{ number_format($d->harga) }}</b></span>
                <span>Sisa Stok : <b>{{ $d->StockFirst->qty }}</b></span>
            </div>
        </button>
    </li>
@endforeach
