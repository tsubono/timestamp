<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>ロック解除 | TIMESTAMP</title>

  <link href="/inspinia/css/bootstrap.min.css" rel="stylesheet">
  <link href="/inspinia/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="/inspinia/css/animate.css" rel="stylesheet">
  <link href="/inspinia/css/style.css" rel="stylesheet">
</head>

<body class="gray-bg">
<div class="middle-box text-center lockscreen animated fadeInDown">
  <div>
    <h3>{{ $name or '' }}</h3>

    <p>打刻画面を表示します。</p>

    <form action="/timestamp/{{$uid}}/unlock" method="post" accept-charset="UTF-8" role="form">
      <input name="_token" type="hidden" value="{{ csrf_token() }}">
      <div class="form-group">
        <input type="password" name="passcode" class="form-control" placeholder="パスコードを入力" required="">
      </div>
      <button type="submit" class="btn btn-primary block full-width">ロック解除</button>
    </form>

  </div>
  @if($errors)
    <br>
    <p style="color:red;">{{ $errors->first('passcode') }}</p>
  @endif
</div>
</body>
</html>
