<!-- BEGIN: Top Bar -->
<div class="top-bar">
    <!-- BEGIN: Breadcrumb -->
    <nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Amore Animal Clinic Management -> {{ Auth::user()->name }}</a></li>
            {{-- <li class="breadcrumb-item active" aria-current="page">{{ convertSlug($global['title'] ?? 'Dashboard') }}
            </li> --}}
        </ol>
    </nav>
    <!-- END: Breadcrumb -->
    <!-- BEGIN: Search -->
    <div class="intro-x relative mr-3 sm:mr-6">
        <div class="search hidden sm:block">
            <input type="text" class="search__input form-control border-transparent" id="search-menu"
                placeholder="Search...">
            <i data-lucide="search" class="search__icon dark:text-slate-500"></i>
        </div>
        <a class="notification sm:hidden" href="">
            <i data-lucide="search" class="notification__icon dark:text-slate-500"></i>
        </a>
        <div class="search-result">
            <div class="search-result__content">
                <div class="search-result__content__title">Pages</div>
                @php
                    $menu = \App\Models\Menu::where('status', true)
                        ->take(5)
                        ->get();
                @endphp
                <div class="mb-5" id="result-menu">
                    @foreach ($menu as $key => $item)
                        <a href="{{ route(str_replace('/index', '', $item->url)) }}" class="flex items-center mt-2">
                            <div
                                class="w-8 h-8 bg-success/20 dark:bg-success/10 text-success flex items-center justify-center rounded-full">
                                <i class="w-4 h-4 {{ $item->GroupMenu->icon }}"></i>
                            </div>
                            <div class="ml-3">{{ $item->name }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- END: Search -->
    <!-- BEGIN: Notifications -->
    <div class="intro-x dropdown mr-auto sm:mr-6">
        <div class="dropdown-toggle notification {{ count(Auth::user()->unreadNotifications) != 0 ? 'notification--bullet' : '' }}  cursor-pointer"
            role="button" aria-expanded="false" data-tw-toggle="dropdown">
            <i data-lucide="bell" class="notification__icon dark:text-slate-500"></i>
        </div>
        <div class="notification-content pt-2 dropdown-menu">
            <div class="notification-content__box dropdown-content">
                <div class="notification-content__title flex justify-between">Notifications
                    <a href="{{ route('markAsRead') }}">Mark As Read</a>
                </div>
                @foreach (Auth::user()->unreadNotifications()->take(5)->get() as $key => $value)
                    <div onclick="window.open('{{ $value->data['url'] }}?notification_id={{ $value->id }}')"
                        class="cursor-pointer relative flex items-center {{ $key ? 'mt-5' : '' }}">
                        <div class="w-12 h-12 flex-none image-fit mr-1">
                            <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center">
                                <b class="text-white">K</b>
                            </div>
                        </div>
                        <div class="ml-2 overflow-hidden w-full">
                            <div class="flex items-center">
                                <a href="javascript:;" class="font-medium truncate mr-5">
                                    {{ $value->data['jenis'] }}
                                </a>
                                <div class="text-xs text-slate-400 ml-auto whitespace-nowrap">
                                    {{ CarbonParse($value->created_at, 'd/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">
                                {{ $value->data['message'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- END: Notifications -->
    <!-- BEGIN: Account Menu -->
    <div class="intro-x dropdown w-8 h-8">
        <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in" role="button"
            aria-expanded="false" data-tw-toggle="dropdown">

            @if (Auth::user()->image != null)
                <img alt="Amore Animal Clinic" src="{{ url('/') . '/' . Auth::user()->image }}">
            @else
                <img alt="Amore Animal Clinic" src="{{ asset('dist/images/amoreboxy.svg') }}">
            @endif
        </div>
        <div class="dropdown-menu w-56">
            <ul class="dropdown-content bg-primary text-white">
                <li class="p-2">
                    <div class="font-medium">{{ Auth::user()->nama_panggilan }}</div>
                    {{-- <div class="text-xs text-white/70 mt-0.5 dark:text-slate-500">{{ Auth::user()->Role->name }}
                    </div> --}}
                </li>
                <li>
                    <hr class="dropdown-divider border-white/[0.08]">
                </li>
                <li>
                    <a href="{{ route('editProfile') }}" class="dropdown-item hover:bg-white/5">
                        <i data-lucide="user" class="w-4 h-4 mr-2"></i> Profile
                    </a>
                </li>
                {{-- <li>
                    <a href="" class="dropdown-item hover:bg-white/5">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i> Add Account
                    </a>
                </li> --}}
                {{-- <li>
                    <a href="" class="dropdown-item hover:bg-white/5">
                        <i data-lucide="lock" class="w-4 h-4 mr-2"></i> Reset Password
                    </a>
                </li>
                <li>
                    <a href="" class="dropdown-item hover:bg-white/5">
                        <i data-lucide="help-circle" class="w-4 h-4 mr-2"></i> Help
                    </a>
                </li> --}}
                <li>
                    <hr class="dropdown-divider border-white/[0.08]">
                </li>
                <li>
                    <a href="{{ route('logout') }}" class="dropdown-item hover:bg-white/5">
                        <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- END: Account Menu -->
</div>

@if (\App\Models\PengumumanKaryawan::where('status', true)->count() != 0)
    <div class="alert alert-dark show mt-4 overflow-hidden flex items-center">
        <i class="fa-solid fa-bell mr-2"></i>
        <div class='marquee w-full overflow-hidden font-bold text-md' data-duration='10000' data-gap='10'
            data-duplicated='true'>
            @foreach (\App\Models\PengumumanKaryawan::where('status', true)->get() as $item)
                <span class="mr-6">{{ $item->description }}</span><span class="mr-6">|</span>
            @endforeach
        </div>
    </div>
@endif


<!-- END: Top Bar -->
