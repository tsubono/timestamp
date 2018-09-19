@extends('basic_notuser')

@section('content')
    <div class="middle-box text-center loginscreen animated fadeInDown"
         style="height: auto;margin-top: 0px;position:relative;">
        <div>
            <h3 style="font-size: 1.5em;">登録は、まだ完了していません</h3>
            <form action="" method="post" accept-charset="UTF-8" role="form" class="m-t applyForm">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <input name="token" type="hidden" value="{{$token}}">
                <input name="email" type="hidden" value="{{$email}}">

                <div class="apply_flow_sec" data-page="#page1">

                    @foreach ($errors->all() as $errs)
                        <li class="errors">{{ $errs }}</li>
                    @endforeach

                    <h3>契約情報登録</h3>

                    <!-- 契約情報 -->
                    <input type="hidden" name="contract[confirmation_flg]" value="1">
                    <input type="hidden" name="contract[free_end_date]" value="{{\Carbon\Carbon::now()->addMonths(1)}}">
                    <div class="form-group">
                        <input type="text" name="contract[company_name]" class="form-control" placeholder="会社名"
                               required="" value="{{ old('contract.company_name') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="contract[company_name_kana]" class="form-control" placeholder="会社名（カナ）"
                               required="" value="{{ old('contract.company_name_kana') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="contract[person_name]" class="form-control" placeholder="担当者名"
                               required="" value="{{ old('contract.person_name') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="contract[person_name_kana]" class="form-control" placeholder="担当者名（カナ）"
                               required="" value="{{ old('contract.person_name_kana') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="contract[zip_1]" class="form-control zipcode1 col-sm-4"
                               placeholder="郵便番号(3桁)" required="" value="{{ old('contract.zip_1') }}"
                               style="width: 110px;">
                        <span class="col-sm-1">-</span>
                        <input type="text" name="contract[zip_2]" class="form-control zipcode1 col-sm-5"
                               placeholder="郵便番号(4桁)" required="" value="{{ old('contract.zip_2') }}"
                               style="width: 125px;margin-bottom: 15px;">
                    </div>
                    <div class="form-group">
                        <input type="text" name="contract[pref]" class="form-control" placeholder="都道府県" required=""
                               value="{{ old('contract.pref') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="contract[address]" class="form-control" placeholder="市区町村・番地"
                               required="" value="{{ old('contract.address') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="contract[building]" class="form-control" placeholder="その他(建物名)"
                               value="{{ old('contract.building') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="contract[tel]" class="form-control" placeholder="電話番号" required=""
                               value="{{ old('contract.tel') }}">
                    </div>
                    <div class="form-group">
                        <label id="receive_mail_flg">お知らせメールを受信許可</label>
                        <input type="checkbox" name="contract[receive_mail_flg]"
                               value="1" {{old("contract.receive_mail_flg")==1?"checked":""}}>
                    </div>
                    <a href="#page2">
                        <button type="button" class="btn btn-primary block full-width m-b nextBtn">次へ</button>
                    </a>
                </div>

                <div class="apply_flow_sec" data-page="#page2">

                    @foreach ($errors->all() as $errs)
                        <li class="errors">{{ $errs }}</li>
                    @endforeach

                    <h3>店舗情報登録</h3>

                    <!-- 店舗情報 -->
                    <input type="hidden" name="plan_id" value="3">
                    <div class="form-group">
                        <input type="text" name="workplace[name]" class="form-control" placeholder="略称" required=""
                               value="{{ old('workplace.name') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="workplace[formal_name]" class="form-control" placeholder="正式名称"
                               required="" value="{{ old('workplace.formal_name') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="workplace[zip_1]" class="form-control zipcode2 col-sm-4"
                               placeholder="郵便番号(3桁)" required="" value="{{ old('workplace.zip_1') }}"
                               style="width: 110px;">
                        <span class="col-sm-1">-</span>
                        <input type="text" name="workplace[zip_2]" class="form-control zipcode2 col-sm-5"
                               placeholder="郵便番号(4桁)" required="" value="{{ old('workplace.zip_2') }}"
                               style="width: 125px;margin-bottom: 15px;">
                    </div>
                    <div class="form-group">
                        <input type="text" name="workplace[pref]" class="form-control" placeholder="都道府県" required=""
                               value="{{ old('workplace.pref') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="workplace[address]" class="form-control" placeholder="市区町村・番地"
                               required="" value="{{ old('workplace.address') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="workplace[building]" class="form-control" placeholder="その他(建物名)"
                               value="{{ old('workplace.building') }}" maxlength="30">
                    </div>
                    <div class="form-group">
                        <input type="text" name="workplace[tel]" class="form-control" placeholder="電話番号" required=""
                               value="{{ old('workplace.tel') }}">
                    </div>
                    <div class="form-group">
                        <input type="text" id="time" name="workplace[timing_of_tomorrow]" class="form-control"
                               placeholder="日付変更時刻" required="" value="{{ old('workplace.timing_of_tomorrow') }}">
                    </div>
                    <div class="form-group">
                        <label>出退勤時間の丸め分数</label>
                        <select name="workplace[round_minute_attendance]">
                            <option value="1" {{old("workplace.round_minute_attendance")=="1"?"selected":""}}>1</option>
                            <option value="10" {{old("workplace.round_minute_attendance")=="10"?"selected":""}}>10
                            </option>
                            <option value="15" {{old("workplace.round_minute_attendance")=="15"?"selected":""}}>15
                            </option>
                            <option value="30" {{old("workplace.round_minute_attendance")=="30"?"selected":""}}>30
                            </option>
                        </select>
                        分
                    </div>
                    <div class="form-group">
                        <label>休憩時間の丸め分数</label>
                        <select name="workplace[round_minute_break]">
                            <option value="1" {{old("workplace.round_minute_attendance")=="1"?"selected":""}}>1</option>
                            <option value="10" {{old("workplace.round_minute_attendance")=="10"?"selected":""}}>10
                            </option>
                            <option value="15" {{old("workplace.round_minute_attendance")=="15"?"selected":""}}>15
                            </option>
                            <option value="30" {{old("workplace.round_minute_attendance")=="30"?"selected":""}}>30
                            </option>
                        </select>
                        分
                    </div>
                    <div class="form-group">
                        <label>給与計算方法</label>
                        <label class="radio-inline">
                            <input type="radio" name="workplace[payroll_role]" value="1" {{(old("workplace.payroll_role")?old("workplace.payroll_role"):'1')=="1"?"checked":""}}>
                            切り捨て
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="workplace[payroll_role]" value="2" {{old("workplace.payroll_role")=="2"?"checked":""}}>
                            四捨五入
                        </label>
                    </div>
                    <a href="#page3">
                        <button type="button" class="btn btn-primary block full-width m-b nextBtn">次へ</button>
                    </a><br><br>
                    <a href="#page1">
                        <button type="button" class="btn block full-width m-b backBtn" style="color: black;">前へ</button>
                    </a>
                </div>

                <div class="apply_flow_sec" data-page="#page3">

                    @foreach ($errors->all() as $errs)
                        <li class="errors">{{ $errs }}</li>
                    @endforeach

                    <h3>ログイン情報登録</h3>

                    <!-- ログイン情報 -->
                    <input type="hidden" name="user[enable_flg]" value="1">
                    <input type="hidden" name="user[owner_flg]" value="1">
                    <input type="hidden" name="user[email]" value="{{urldecode($email)}}">
                    <div class="form-group">
                        <input type="text" name="user[login_id]" class="form-control" placeholder="ユーザーID" required=""
                               value="{{ old('user.login_id') }}">
                    </div>
                    <div class="form-group">
                        <input type="password" name="user[password]" class="form-control" placeholder="パスワード"
                               required="" value="{{ old('user.password') }}">
                    </div>
                    <div class="form-group">
                        <input type="password" name="user[password_confirmation]" class="form-control"
                               placeholder="パスワード(確認用)" required="" value="{{ old('user.password_confirmation') }}">
                    </div>

                    <button type="button" class="btn btn-primary block full-width m-b submitBtn">登録</button>
                    <br><br>
                    <a href="#page2">
                        <button type="button" class="btn block full-width m-b backBtn" style="color: black;">前へ</button>
                    </a>

                </div>

            </form>
            {{--<p class="m-t"> <small>Copyright &copy; 2017-2018 TIEMSTAMP.</small> </p>--}}
        </div>
    </div>
    <script>
        window.onload = function () {
            /*
             * ページ遷移関連
             */
            $(window).hashchange(function () {
                var hash = location.hash;
                if (hash == "") {
                    hash = "#page1";
                }
                var currentPage = hash.replace('#', '');
                $('.apply_flow_sec').css('display', 'none');

                $('.apply_flow_sec').each(function () {
                    var page = $(this).data('page');
                    if (page == hash) {
                        $(this).fadeIn();
                    }
                });
            });
            $(window).hashchange();

            $('.submitBtn').click(function () {
                $('.applyForm').submit();
            });
            $('.nextBtn').click(function () {
                $('.errors').each(function () {
                    $(this).text('');
                });
            });
            $('.backBtn').click(function () {
                $('.errors').each(function () {
                    $(this).text('');
                });
            });

            //時刻ピッカー
//            var options = {step: 1, timeFormat: 'H:i'};
//            $('#time').timepicker(options);

            $('#time').datetimepicker({
                format: 'H:i',
                lang: 'ja',
                datepicker:false
            });

            //郵便番号から住所を自動入力
            $('.zipcode1').change(function () {
                AjaxZip3.zip2addr('contract[zip_1]', 'contract[zip_2]', 'contract[pref]', 'contract[address]');
            });
            $('.zipcode2').change(function () {
                AjaxZip3.zip2addr('workplace[zip_1]', 'workplace[zip_2]', 'workplace[pref]', 'workplace[address]');
            });
        }

    </script>
    <style>
        .errors {
            color: red;
            list-style: none;
        }
    </style>
@endsection
