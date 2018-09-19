@extends('layouts.base.notuser')

@include('elements.css')
@include('elements.js-header')
@include('elements.js-footer')
@include('elements.footer')

@section('container')
    @yield('content')
@endsection