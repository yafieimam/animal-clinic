<?php

namespace App\Main;

use App\Models\GroupMenu;
use App\Models\Menu;
use App\Models\TitleMenu;
use Illuminate\Support\Facades\Auth;

class SideMenu
{
    /**
     * List of side menu items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function menu()
    {

        $data = new TitleMenu();
        $data = $data
            ->where('status', true)
            ->orderBy('sequence', 'ASC')
            ->get();
        $titleMenu = [];
        $checkArray = [];
        $hasFeatureTitle = 1;
        foreach ($data as $key => $value) {
            $hasFeatureTitle = 1;
            foreach ($value->GroupMenu->where('status', true)->sortBy('sequence')  as $i => $d) {
                if ($d->type == 'Single') {
                    if (count($d->Menu) != 0) {
                        // foreach ($d->Menu->where('status', true)->where('type', 'NON MENU')->sortBy('sequence') as $i1 => $d1) {
                        //     if (Auth::user() != null) {
                        //         if (Auth::user()->aksesMenu('view', $d1->url)) {
                        //             $hasFeatureTitle++;
                        //         }
                        //     }
                        // }
                    } else {
                        $hasFeatureTitle++;
                    }
                }

                if ($d->type == 'Dropdown') {
                    // foreach ($d->Menu->where('status', true)->where('type', 'MENU')->sortBy('sequence') as $i1 => $d1) {
                    //     if (Auth::user() != null) {
                    //         if (Auth::user()->aksesMenu('view', $d1->url)) {
                    //             array_push($checkArray, $d1->url);
                    //             $hasFeatureTitle++;
                    //         }
                    //     }
                    // }
                }
            }
            $menu = [];
            foreach ($value->GroupMenu->where('status', true)->sortBy('sequence')  as $i => $d) {

                if ($hasFeatureTitle != 0) {
                    $hasFeatureGroup = 1;

                    if ($d->type != 'Single') {

                        // foreach ($d->Menu->where('status', true)->where('type', 'MENU')->sortBy('sequence') as $i1 => $d1) {

                        //     if (Auth::user() != null) {
                        //         if (Auth::user()->aksesMenu('view', $d1->url)) {
                        //             array_push($checkArray, $d1->url);
                        //             $hasFeatureGroup++;
                        //         }
                        //     }
                        // }
                    } else {
                        // foreach ($d->Menu->where('status', true)->where('type', 'NON MENU')->sortBy('sequence') as $i1 => $d1) {
                        //     if (Auth::user() != null) {
                        //         if (Auth::user()->aksesMenu('view', $d1->url)) {
                        //             $hasFeatureGroup++;
                        //         }
                        //     }
                        // }
                    }

                    if ($hasFeatureGroup != 0) {
                        $menu[$d->slug] = [
                            'icon' => $d->icon,
                            'title' => $d->name,
                            'slug' => $d->slug,
                        ];

                        if ($d->type == 'Single') {
                            $menu[$d->slug]['route_name'] =  $d->slug;
                            $menu[$d->slug]['url'] =  $d->url;
                            $menu[$d->slug]['params'] = [
                                'layout' => 'side-menu'
                            ];
                        }

                        if ($d->type == 'Dropdown') {
                            $menu[$d->slug]['sub_menu'] = [];
                            foreach ($d->Menu->where('status', true)->where('type', 'MENU')->sortBy('sequence') as $i1 => $d1) {
                                if ($d1->status == true) {
                                    $menu[$d->slug]['sub_menu'][str_replace('/index', '', $d1->url)] =  [
                                        'icon' => '',
                                        'title' => $d1->name,
                                        'slug' => $d1->slug,
                                    ];

                                    $menu[$d->slug]['sub_menu'][str_replace('/index', '', $d1->url)]['params'] =  [
                                        'layout' => 'side-menu',
                                    ];

                                    if ($d1->type == 'MENU') {
                                        $menu[$d->slug]['sub_menu'][str_replace('/index', '', $d1->url)]['route_name'] =  str_replace('/index', '', $d1->url);
                                        $menu[$d->slug]['sub_menu'][str_replace('/index', '', $d1->url)]['url'] =  $d1->url;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $titleMenu[$key]['title'] = $value->name;
            $titleMenu[$key]['data'] = $menu;
        }

        foreach ($titleMenu as $key => $value) {
            if (count($value['data']) == 0) {
                unset($titleMenu[$key]);
            }
        }
        $titleMenu = array_values($titleMenu);
        // dd($titleMenu);
        return $titleMenu;
    }
}
