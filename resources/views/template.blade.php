@foreach ($side_menu as $i => $d)
    <li>
        <h4 class="text-white font-bold text-lg">{{ $d['title'] }}</h4>
    </li>
    <li class="side-nav__devider my-2"></li>
    @foreach ($d['data'] as $menuKey => $menu)
        <li>
            <a href="{{ isset($menu['route_name']) ? route($menu['route_name'], $menu['params']) : 'javascript:;' }}"
                class="{{ Request::segment(1) == $menu['slug'] ? 'side-menu side-menu--active' : '' }}  {{ $first_level_active_index == $menuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                <div class="side-menu__icon">
                    <i class="{{ $menu['icon'] }}"></i>
                </div>
                <div class="side-menu__title flex justify-between">
                    {{ $menu['title'] }}
                    @switch($menu['title'])
                        @case('Ruangan')
                            <div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                {{ pasienActive() }}
                            </div>
                        @break

                        @case('Bedah')
                            <div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                {{ bedahActive() }}
                            </div>
                        @break

                        @case('Apotek')
                            <div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                {{ apotekActive() }}
                            </div>
                        @break

                        @default
                    @endswitch
                    @if (isset($menu['sub_menu']))
                        <div
                            class="side-menu__sub-icon {{ Request::segment(1) == $menu['slug'] ? 'transform rotate-180' : '' }} {{ $first_level_active_index == $menuKey ? 'transform rotate-180' : '' }}">
                            <i data-lucide="chevron-down"></i>
                        </div>
                    @endif
                </div>
            </a>
            @if (isset($menu['sub_menu']))
                <ul
                    class="{{ Request::segment(1) == $menu['slug'] ? 'side-menu__sub-open' : '' }} {{ $first_level_active_index == $menuKey ? 'side-menu__sub-open' : '' }}">
                    @foreach ($menu['sub_menu'] as $subMenuKey => $subMenu)
                        <li>
                            <a href="{{ isset($subMenu['route_name']) ? route($subMenu['route_name'], $subMenu['params']) : 'javascript:;' }}"
                                class="{{ Request::segment(2) == str_replace('/index', '', $subMenu['url']) ? 'side-menu side-menu--active' : '' }} {{ $second_level_active_index == $subMenuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                <div class="side-menu__icon">
                                    <i data-lucide="activity"></i>
                                </div>
                                <div class="side-menu__title flex justify-between" style="padding-right: 0.5rem">
                                    {{ $subMenu['title'] }}

                                    @switch($subMenu['title'])
                                        @case('Pembayaran')
                                            <div
                                                class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                                {{ pembayaranActive() }}
                                            </div>
                                        @break

                                        @default
                                    @endswitch
                                    @if (isset($subMenu['sub_menu']))
                                        <div
                                            class="side-menu__sub-icon {{ $second_level_active_index == $subMenuKey ? 'transform rotate-180' : '' }}">
                                            <i data-lucide="chevron-down"></i>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            @if (isset($subMenu['sub_menu']))
                                <ul
                                    class="{{ $second_level_active_index == $subMenuKey ? 'side-menu__sub-open' : '' }}">
                                    @foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu)
                                        <li>
                                            <a href="{{ isset($lastSubMenu['route_name']) ? route($lastSubMenu['route_name'], $lastSubMenu['params']) : 'javascript:;' }}"
                                                class="{{ $third_level_active_index == $lastSubMenuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                <div class="side-menu__icon">
                                                    <i data-lucide="zap"></i>
                                                </div>
                                                <div class="side-menu__title">
                                                    {{ $lastSubMenu['title'] }}


                                                </div>
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
@endforeach
