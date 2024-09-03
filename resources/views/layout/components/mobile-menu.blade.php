<!-- BEGIN: Mobile Menu -->
<div class="mobile-menu md:hidden">
    <div class="mobile-menu-bar">
        <a href="" class="flex mr-auto">
            <img alt="Amore Animal Clinic" class="w-6" src="{{ asset('dist/images/amore.png') }}">
        </a>
        <a href="javascript:;" id="mobile-menu-toggler">
            <i data-lucide="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i>
        </a>
    </div>
    <ul class="border-t border-white/[0.08] py-5 hidden">
        @php
            $title = \App\Models\TitleMenu::where('status', true)
                ->orderBy('sequence', 'ASC')
                ->get();

            $param = ['layout' => 'side-menu'];
        @endphp
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
                <li class="nav__devider my-2"></li>
                @foreach ($item->GroupMenu->where('status', true) as $item1)
                    @if ($item1->type == 'Single')
                        @if (count($item1->Menu) != 0)
                            @if (Auth::user()->aksesMenu('view', $item1->Menu[0]->url))
                                <li>
                                    <a href="{{ route($item1->slug, $param) }}"
                                        class="{{ Request::segment(2) == $item1->slug ? 'menu menu--active' : 'menu' }}">
                                        <div class="menu__icon">
                                            <i class="{{ $item1->icon }}"></i>
                                        </div>
                                        <div class="menu__title flex justify-between">
                                            {{ $item1->name }}
                                            @switch($item1->name)
                                                @case('Ruangan')
                                                    <div
                                                        class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                                        {{ pasienActive() }}
                                                    </div>
                                                @break

                                                @case('Bedah')
                                                    <div
                                                        class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                                        {{ bedahActive() }}
                                                    </div>
                                                @break

                                                @case('Apotek')
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
                                        class="{{ Request::segment(1) == $item1->slug ? 'menu menu--active' : 'menu' }}">
                                        <div class="menu__icon">
                                            <i class="{{ $item1->icon }}"></i>
                                        </div>
                                        <div class="menu__title flex justify-between">
                                            {{ $item1->name }}
                                            @if (count($item1->Menu->where('status', true)) != 0)
                                                <div
                                                    class="menu__sub-icon {{ Request::segment(1) == $item1->slug ? 'transform rotate-180' : '' }}">
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
                                    <ul class="{{ Request::segment(1) == $item1->slug ? 'menu__sub-open' : '' }}">
                                        @foreach ($menu as $item2)
                                            <li>
                                                <a href="{{ route(str_replace('/index', '', $item2->url), $param) }}"
                                                    class="{{ Request::segment(2) == str_replace('/index', '', $item2->url) ? 'menu menu--active' : 'menu' }}">
                                                    <div class="menu__icon">
                                                        <i data-lucide="activity"></i>
                                                    </div>
                                                    <div class="menu__title flex justify-between"
                                                        style="padding-right: 0.5rem">
                                                        {{ $item2->name }}
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
                                                {{-- <ul
                                                    class="{{ Request::segment(2) == str_replace('/index', '', $item2->url) ? 'menu__sub-open' : '' }}">
                                                    @foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu)
                                                        <li>
                                                            <a href="{{ isset($lastSubMenu['route_name']) ? route($lastSubMenu['route_name'], $lastSubMenu['params']) : 'javascript:;' }}"
                                                                class="{{ $third_level_active_index == $lastSubMenuKey ? 'menu menu--active' : 'menu' }}">
                                                                <div class="menu__icon">
                                                                    <i data-lucide="zap"></i>
                                                                </div>
                                                                <div class="menu__title">
                                                                    {{ $lastSubMenu['title'] }}


                                                                </div>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul> --}}
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endif
                    @endif
                @endforeach
            @endif
        @endforeach
        {{-- @foreach ($side_menu as $i => $d)
            @foreach ($d['data'] as $menuKey => $menu)
                <li>
                    <a href="{{ isset($menu['route_name']) ? route($menu['route_name'], $menu['params']) : 'javascript:;' }}"
                        class="{{ $first_level_active_index == $menuKey ? 'menu menu--active' : 'menu' }}">
                        <div class="menu__icon">
                            <i class="{{ $menu['icon'] }}"></i>
                        </div>
                        <div class="menu__title">
                            {{ $menu['title'] }}
                            @if (isset($menu['sub_menu']))
                                <i data-lucide="chevron-down"
                                    class="menu__sub-icon {{ $first_level_active_index == $menuKey ? 'transform rotate-180' : '' }}"></i>
                            @endif
                        </div>
                    </a>
                    @if (isset($menu['sub_menu']))
                        <ul class="{{ $first_level_active_index == $menuKey ? 'menu__sub-open' : '' }}">
                            @foreach ($menu['sub_menu'] as $subMenuKey => $subMenu)
                                <li>
                                    <a href="{{ isset($subMenu['route_name']) ? route($subMenu['route_name'], $subMenu['params']) : 'javascript:;' }}"
                                        class="{{ $second_level_active_index == $subMenuKey ? 'menu menu--active' : 'menu' }}">
                                        <div class="menu__icon">
                                            <i data-lucide="activity"></i>
                                        </div>
                                        <div class="menu__title">
                                            {{ $subMenu['title'] }}
                                            @if (isset($subMenu['sub_menu']))
                                                <i data-lucide="chevron-down"
                                                    class="menu__sub-icon {{ $second_level_active_index == $subMenuKey ? 'transform rotate-180' : '' }}"></i>
                                            @endif
                                        </div>
                                    </a>
                                    @if (isset($subMenu['sub_menu']))
                                        <ul
                                            class="{{ $second_level_active_index == $subMenuKey ? 'menu__sub-open' : '' }}">
                                            @foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu)
                                                <li>
                                                    <a href="{{ isset($lastSubMenu['route_name']) ? route($lastSubMenu['route_name'], $lastSubMenu['params']) : 'javascript:;' }}"
                                                        class="{{ $third_level_active_index == $lastSubMenuKey ? 'menu menu--active' : 'menu' }}">
                                                        <div class="menu__icon">
                                                            <i data-lucide="zap"></i>
                                                        </div>
                                                        <div class="menu__title">{{ $lastSubMenu['title'] }}</div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        @endforeach --}}
    </ul>
</div>
<!-- END: Mobile Menu -->
