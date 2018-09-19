@extends('layouts.base.user')

@include('elements.css')
@include('elements.js-header')
@include('elements.js-footer')
@include('elements.nav')
@include('elements.navbar')
@include('elements.footer')

@section('container')
    <div id="wrapper">

        @yield('nav')

        <div id="page-wrapper" class="gray-bg">
            @yield('navbar')

            @yield('header')

            <div class="wrapper wrapper-content animated fadeInRight">
                @yield('content')
            </div>
        </div>
        @yield('footer')
    </div>
@endsection