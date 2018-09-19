@extends('basic_notuser')

@section('content')
    <div class="middle-box text-center loginscreen animated fadeInDown" style="height: 580px;margin-top: 0px;position:relative;">
        <div>
            <br><br>
            <h3>TIMESTAMP</h3>
            <div class="loginArea">
                <p>{{ $host or '' }}</p>
                <p style="color:red" ;>{!! $message or '' !!}</p>
                <form action="" method="post" accept-charset="UTF-8" role="form" class="m-t">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <input type="text" name="login_id" class="form-control" placeholder="ユーザーID" required=""
                               value="{{ old('login_id') }}">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="パスワード" required="">
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">ログイン</button>

                    <a href="#" class="toReset">
                        <small>パスワードを忘れましたか？</small>
                    </a>
                </form>
            </div>
            <div class="resetArea">
                <p>パスワードリセットフォーム</p>
                @if ($errors->has('reset_error'))
                    <p style="color:red" ;>{!! $errors->first('reset_error') !!}</p>
                @elseif(session('sent_mail'))
                    <p style="color:red" ;>{!! session('sent_mail') !!}</p>
                @endif
                <form action="/send_reset_mail" method="post" accept-charset="UTF-8" role="form" class="m-t">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    @if (session('type') == "login_id" || session('type') == "")
                        <div class="form-group login_id">
                            <input type="text" name="login_id" class="form-control" placeholder="ユーザーID" required=""
                                   value="{{ old('login_id') }}">
                        </div>
                        <div class="form-group email" style="display:none;">
                            <input type="text" name="email" class="form-control" placeholder="メールアドレス" required=""
                                   value="{{ old('email') }}">
                        </div>
                    @else
                        <div class="form-group login_id" style="display:none;">
                            <input type="text" name="login_id" class="form-control" placeholder="ユーザーID" required=""
                                   value="{{ old('login_id') }}">
                        </div>
                        <div class="form-group email">
                            <input type="text" name="email" class="form-control" placeholder="メールアドレス" required=""
                                   value="{{ old('email') }}">
                        </div>
                    @endif
                    
                    <div class="form-group">
                        <label class="radio-inline"><input type="radio" name="type" class="" required=""
                                                           value="login_id" {{session('type') == "login_id" ? "checked":""}}>ユーザーID</label>
                        <label class="radio-inline"><input type="radio" name="type" class="" required="" value="email" {{session('type') == "email" ? "checked":""}}>メールアドレス</label>
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">メール送信</button>

                    <a href="#" class="toLogin">
                        <small>ログイン画面に戻る</small>
                    </a>
                </form>
            </div>
            {{--<p class="m-t">--}}
                {{--<small>Copyright &copy; 2014-2015 TIEMSTAMP.</small>--}}
            {{--</p>--}}
        </div>
    </div>
    <script>
        window.onload = function () {
            $('.toReset').click(function () {
                $('.loginArea').css('display', 'none');
                $('.resetArea').fadeIn();
            });
            $('.toLogin').click(function () {
                $('.loginArea').fadeIn();
                $('.resetArea').css('display', 'none');
            });

            @if ($errors->has('reset_error') || session('sent_mail'))
                $('.loginArea').css('display', 'none');
                $('.resetArea').fadeIn();
            @else
                $('.loginArea').fadeIn();
                $('.resetArea').css('display', 'none');
            @endif

            $('[name=login_id]').attr('required', false);
            $('[name=email]').attr('required', false);

            $('[name=type]').change(function () {
                var type = $('[name=type]:checked').val();

                $('[name=' + type + ']').attr('required', true);
                $('.login_id').css('display', 'none');
                $('.email').css('display', 'none');
                $('.' + type).css('display', '');
            });


        }
    </script>
@endsection
