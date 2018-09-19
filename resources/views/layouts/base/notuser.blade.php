<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @section('title')
        <title>{{ $title or 'TIMESTAMP' }} | TIMESTAMP</title>
    @show

    @yield('css')
    @yield('js-header')
</head>

<body class="gray-bg">
@yield('container')

<div class="footer gray-bg" style="position: relative">
    <div class="pull-right">
        ver 0.0.0 (2017/00/00)
    </div>
    <div>
        <strong>Copyright</strong> TIMESTAMP &copy; 2017-2018
    </div>
</div>
<style>
    .footer {
        margin-top: 15px;
    }
</style>

@yield('js-footer')
</body>
</html>
