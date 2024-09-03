@foreach ($menu as $key => $item)
    <a href="{{ route(str_replace('/index', '', $item->url)) }}" class="flex items-center mt-2">
        <div class="w-8 h-8 bg-success/20 dark:bg-success/10 text-success flex items-center justify-center rounded-full">
            <i class="w-4 h-4 {{ $item->GroupMenu->icon }}"></i>
        </div>
        <div class="ml-3">{{ $item->name }}</div>
    </a>
@endforeach
