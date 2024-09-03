<div id="ticket" class="tab-pane active" role="tabpanel" aria-labelledby="ticket-tab">
    <div class="box p-5 mt-5">
        @foreach ($pasien as $item)
            <a href="javascript:;" onclick="getPembayaran('{{ $item->id }}')"
                class="owner-rawat-jalan flex items-center p-3 border-b pasien cursor-pointer transition duration-300 ease-in-out bg-white dark:bg-darkmode-600 hover:bg-slate-100 dark:hover:bg-darkmode-400 rounded-md">
                <div>
                    <div class="text-slate-500 mr-1">{{ $item->kode }}</div>
                    <div class="text-slate-500">Atas Owner
                        <span class="font-medium text-slate-500  max-w-[70%]">
                            {{ $item->name }}</span>
                    </div>
                    <div class="text-slate-500">
                        <span class="font-medium text-slate-500  max-w-[70%]">
                            Tanggal
                            {{ CarbonParse($item->singleRekamMedisPasien->updated_at, 'd-m-Y') }}
                            Jam
                            {{ CarbonParse($item->singleRekamMedisPasien->updated_at, 'H:i:s A') }}
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
<div id="ranap" class="tab-pane" role="tabpanel" aria-labelledby="ranap-tab">
    <div class="box p-5 mt-5">
        @foreach ($pasienRawatInap as $item)
            <a href="javascript:;" onclick="getPembayaran('{{ $item->id }}')"
                class="owner-rawat-inap flex items-center p-3 border-b pasien cursor-pointer transition duration-300 ease-in-out bg-white dark:bg-darkmode-600 hover:bg-slate-100 dark:hover:bg-darkmode-400 rounded-md">
                <div>
                    <div class="text-slate-500 mr-1">{{ $item->kode }}</div>
                    <div class="text-slate-500">Atas Owner
                        <span class="font-medium text-slate-500  max-w-[70%]">
                            {{ $item->name }}</span>
                    </div>
                    <div class="text-slate-500">
                        <span class="font-medium text-slate-500  max-w-[70%]">
                            Tanggal
                            {{ CarbonParse($item->singleRekamMedisPasien->updated_at, 'd-m-Y') }}
                            Jam
                            {{ CarbonParse($item->singleRekamMedisPasien->updated_at, 'H:i:s A') }}
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
        {{-- <div class="flex items-center border-b border-slate-200 dark:border-darkmode-400 pb-5">
            <div>
                <div class="text-slate-500">Time</div>
                <div class="mt-1">02/06/20 02:10 PM</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                icon-name="clock" data-lucide="clock"
                class="lucide lucide-clock w-4 h-4 text-slate-500 ml-auto">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div> --}}

    </div>
</div>
