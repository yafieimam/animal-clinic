@extends('../layout/main')

@section('head')
    <title>
        Amore Animal Clinic
    </title>
@endsection
@section('content')
    @include('../layout/components/mobile-menu')
    <div class="flex">
        <!-- BEGIN: Side Menu -->
        <nav class="side-nav">
            <a href="javascript:;" class="intro-x flex items-center pl-5 pt-4 opacity-100">
                <img alt="Amore Animal Clinic" class="w-44" src="{{ asset('dist/images/amoreboxy.svg') }}">
            </a>
            <div class="side-nav__devider my-6"></div>
            @php
                $title = \App\Models\TitleMenu::where('status', true)
                    ->orderBy('sequence', 'ASC')
                    ->get();

                $param = ['layout' => 'side-menu'];
            @endphp
            <ul>
                @foreach ($title as $item)
                    @php
                        $hasFeatureTitle = 0;
                        $checkArray = [];
                        foreach ($item->GroupMenu->where('status', true)->sortBy('sequence') as $i1 => $d1) {
                            if ($d1->type == 'Single') {
                                if (count($d1->Menu) != 0) {
                                    foreach (
                                        $d1->Menu
                                            ->where('status', true)
                                            ->where('type', 'NON MENU')
                                            ->sortBy('sequence')
                                        as $i2 => $d2
                                    ) {
                                        if (Auth::user() != null) {
                                            if (Auth::user()->aksesMenu('view', $d2->url)) {
                                                $hasFeatureTitle++;
                                            }
                                        }
                                    }
                                } else {
                                    $hasFeatureTitle++;
                                }
                            } else {
                                foreach (
                                    $d1->Menu
                                        ->where('status', true)
                                        ->where('type', 'MENU')
                                        ->sortBy('sequence')
                                    as $i2 => $d2
                                ) {
                                    if (Auth::user() != null) {
                                        if (Auth::user()->aksesMenu('view', $d2->url)) {
                                            array_push($checkArray, $d2->url);
                                            $hasFeatureTitle++;
                                        }
                                    }
                                }
                            }
                        }
                    @endphp
                    @if ($hasFeatureTitle != 0)
                        <li class="hidden lg:block">
                            <h4 class="text-white font-bold text-lg">{{ $item->name }}</h4>
                        </li>
                        <li class="side-nav__devider my-2"></li>
                        @foreach ($item->GroupMenu->where('status', true)->sortBy('sequence') as $item1)
                            @if ($item1->type == 'Single')
                                @if (count($item1->Menu) != 0)
                                    @if (Auth::user()->aksesMenu('view', $item1->Menu[0]->url))
                                        <li>
                                            <a href="{{ route($item1->slug, $param) }}"
                                                class="{{ Request::segment(2) == $item1->slug ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                <div class="side-menu__icon">
                                                    <i class="{{ $item1->icon }}"></i>
                                                </div>
                                                <div class="side-menu__title flex justify-between">
                                                    {{ convertSlug($item1->name) }}
                                                    @switch($item1->slug)
                                                        @case('ruangan')
                                                            <div
                                                                class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                                                {{ pasienActive() }}
                                                            </div>
                                                        @break

                                                        @case('bedah')
                                                            <div
                                                                class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                                                {{ bedahActive() }}
                                                            </div>
                                                        @break

                                                        @case('apotek')
                                                            <div
                                                                class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                                                {{ apotekActive() }}
                                                            </div>
                                                        @break

                                                        @default
                                                    @endswitch
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                @else
                                    Error
                                    {{-- <li
                                        class="{{ Request::segment(1) == str_replace('/', '', $item1->url) ? 'active' : null }}">
                                        <a href="{{ url($item1->url) }}">
                                            <i class="{{ $item1->icon }}"></i>
                                            <span>{{ $item1->name }}</span>
                                        </a>
                                    </li> --}}
                                @endif
                            @elseif($item1->type == 'Dropdown')
                                @php
                                    $hasFeatureGroup = 0;
                                    foreach (
                                        $item1->Menu
                                            ->where('status', true)
                                            ->where('type', 'MENU')
                                            ->sortBy('sequence')
                                        as $i2 => $d2
                                    ) {
                                        if (Auth::user() != null) {
                                            if (Auth::user()->aksesMenu('view', $d2->url)) {
                                                array_push($checkArray, $d2->url);
                                                $hasFeatureGroup++;
                                            }
                                        }
                                    }
                                @endphp
                                @if (count($item1->Menu) != 0)
                                    @if ($hasFeatureGroup != 0)
                                        <li>
                                            <a href="javascript:;"
                                                class="{{ Request::segment(1) == $item1->slug ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                <div class="side-menu__icon">
                                                    <i class="{{ $item1->icon }}"></i>
                                                </div>
                                                <div class="side-menu__title flex justify-between">
                                                    {{ $item1->name }}
                                                    @if (count($item1->Menu->where('status', true)) != 0)
                                                        <div
                                                            class="side-menu__sub-icon {{ Request::segment(1) == $item1->slug ? 'transform rotate-180' : '' }}">
                                                            <i data-lucide="chevron-down"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                            @php
                                                $menu = $item1->Menu
                                                    ->where('status', true)
                                                    ->where('type', 'MENU')
                                                    ->sortBy('sequence');
                                            @endphp
                                            <ul
                                                class="{{ Request::segment(1) == $item1->slug ? 'side-menu__sub-open' : '' }}">
                                                @foreach ($menu as $item2)
                                                    @if (Auth::user()->aksesMenu('view', $item2->url))
                                                        <li>
                                                            <a href="{{ $item2->name == 'Klaim' ? 'javascript:;' : route(str_replace('/index', '', $item2->url), $param) }}"
                                                                class="{{ Request::segment(2) == str_replace('/index', '', $item2->url) ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                                <div class="side-menu__icon">
                                                                    <i data-lucide="activity"></i>
                                                                </div>
                                                                <div class="side-menu__title flex justify-between"
                                                                    style="padding-right: 0.5rem">
                                                                    {{ $item2->name }}
                                                                    @if ($item2->name == 'Klaim')
                                                                        <div
                                                                            class="side-menu__sub-icon">
                                                                            <i data-lucide="chevron-down"></i>
                                                                        </div>
                                                                    @endif
                                                                    @switch($item2->name)
                                                                        @case('Pembayaran')
                                                                            <div
                                                                                class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                                                                {{ pembayaranActive() }}
                                                                            </div>
                                                                        @break

                                                                        @case('Permintaan Stock')
                                                                            <div
                                                                                class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                                                                {{ permintaanStockActive() }}
                                                                            </div>
                                                                        @break

                                                                        @default
                                                                    @endswitch
                                                                </div>
                                                            </a>
                                                            @if ($item2->name == 'Klaim')
                                                            <ul
                                                            class="{{ Request::segment(2) == str_replace('/index', '', $item2->url) ? 'side-menu__sub-open' : '' }}">
                                                                <li>
                                                                    <a href="{{ route('reimbursement-approval') }}"
                                                                        class="{{ 'side-menu' }}">
                                                                        <div class="side-menu__icon">
                                                                            <i data-lucide="zap"></i>
                                                                        </div>
                                                                        <div class="side-menu__title">
                                                                            Approval
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('reimbursement-approved') }}"
                                                                        class="{{ 'side-menu' }}">
                                                                        <div class="side-menu__icon">
                                                                            <i data-lucide="zap"></i>
                                                                        </div>
                                                                        <div class="side-menu__title">
                                                                            Approved
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </nav>
        <!-- END: Side Menu -->
        <!-- BEGIN: Content -->
        <div class="content">
            @include('../layout/components/top-bar')
            <div id="slide-over-filter" class="modal modal-slide-over" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header p-5">
                            <h2 class="font-medium text-base mr-auto">@yield('header_filter')</h2>
                        </div>
                        <div class="modal-body">
                            <div class="grid grid-cols-12 gap-6">
                                <div class="intro-y col-span-12 items-center justify-between">
                                    @yield('content_filter')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @yield('subcontent')
        </div>
        <!-- END: Content -->
    </div>
@endsection
