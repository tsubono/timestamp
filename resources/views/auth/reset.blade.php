@extends('basic_notuser')

@section('content')
    <div class="middle-box text-center loginscreen animated fadeInDown" style="height: 580px;margin-top: 0px;position:relative;">
        <div>
            <br><br>
            <h3 style="font-size: 1.5em;">パスワード再設定</h3>
            <form action="" method="post" accept-charset="UTF-8" role="form" class="m-t applyForm">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <input name="type" type="hidden" value="{{ $type }}">
                <input name="token" type="hidden" value="{{ $token }}">

                <div>
                    @foreach ($errors->all() as $errs)
                        <li class="errors">{{ $errs }}</li>
                    @endforeach

                    <div class="form-group">
                        @if ($type=='email')
                            <span>メールアドレス</span>
                        @else
                            <span>ユーザーID</span>
                        @endif
                        <input type="text" name="{{$type}}" class="form-control" value="{{ $val }}" readonly>
                    </div>
                    <div class="form-group">
                        <span>パスワード</span>
                        <input type="password" name="password" class="form-control" placeholder="パスワード" required="">
                    </div>
                    <div class="form-group">
                        <span>パスワード(確認用)</span>
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="パスワード(確認用)" required="">
                    </div>

                    <button type="submit" class="btn btn-primary block full-width m-b submitBtn">パスワードリセット</button>
                    <br><br>
                </div>

            </form>
            {{--<p class="m-t">--}}
                {{--<small>Copyright &copy; 2017-2018 TIEMSTAMP.</small>--}}
            {{--</p>--}}
        </div>
    </div>
    <style>
        .errors {
            color: red;
            list-style: none;
        }
    </style>
@endsection
