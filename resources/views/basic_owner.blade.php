@extends('layouts.base.owner')

@include('elements.css')
@include('elements.js-header')
@include('elements.js-footer')
@include('elements.navbar')
@include('elements.footer')

@yield('navbar')

@section('container')
    @yield('content')
@endsection