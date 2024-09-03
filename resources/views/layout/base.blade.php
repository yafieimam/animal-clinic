<!DOCTYPE html>
<!--
Template Name: Rubick - HTML Admin Dashboard Template
Author: Left4code
Website: http://www.left4code.com/
Contact: muhammadrizki@left4code.com
Purchase: https://themeforest.net/user/left4code/portfolio
Renew Support: https://themeforest.net/user/left4code/portfolio
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="{{ $dark_mode ? 'dark' : '' }}{{ $color_scheme != 'default' ? ' ' . $color_scheme : '' }}">
<!-- BEGIN: Head -->

<head>
    <meta charset="utf-8">
    <link href="{{ asset('dist/images/amore.png') }}" rel="shortcut icon">
    {{-- {!! \Sentry\Laravel\Integration::sentryTracingMeta() !!} --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Amore Animal Clinic">
    <meta name="keywords"
        content="Amore Animal Clinic Management">
    <meta name="author" content="LEFT4CODE">

    @yield('head')

    <!-- BEGIN: CSS Assets-->
    <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}" />
    <style>
        .swal2-container {
            z-index: 999999999999999999999999999999999 !important;
        }
    </style>
    <!-- END: CSS Assets-->
</head>
<!-- END: Head -->

@yield('body')

</html>
