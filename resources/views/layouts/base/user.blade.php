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

<body>
<div class="">
    @yield('container')
</div>

@yield('js-footer')
</body>
</html>
