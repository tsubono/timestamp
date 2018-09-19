<div class="modal inmodal" id="workplaceAddModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/owner/add_workplace" method="post" accept-charset="UTF-8" data-remote="data-remote"
          class="form-horizontal">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="plan_id" type="hidden" value="{{old("plan_id")}}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">店舗の追加</h4>
                </div>

                <div class="modal-body" style="padding: 0 40px 0 40px;">
                    <br>
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <form>
                        {{--<div class="form-group">--}}
                            {{--<label for="recipient-name" class="form-control-label">プラン:</label>--}}
                            {{--<select name="plan_id" class="form-control" required>--}}
                                {{--<option value=""> --</option>--}}
                                {{--@foreach(\App\Models\Plan::all() as $plan)--}}
                                    {{--<option value="{{$plan->id}}" {{old("plan_id")==$plan->id?"selected":""}}> {{$plan->getDetail($plan->id)}} </option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        {{--</div>--}}
                        <label style="font-weight: bold">プラン:</label>
                        <br>
                        <div class="row" style="margin-left: -6%;width:134%;">
                            <div class="text-center col-lg-3 col-md-3 col-sm-9 col-xs-9 plans tile"
                                 data-value="1" data-detail="{{\App\Models\Plan::getDetail('1')}}">
                                <div style="text-align:center;padding:90px 0 90px 0;">
                                    <p class="plan_name">{{$plans[0]->name}}</p>
                                    <br>
                                    <p class="">
                                        従業員数上限 :
                                        {{$plans[0]->employee_limit}}人
                                    </p>
                                    <p class="">
                                        月額 :
                                        {{$plans[0]->monthly_price}}円
                                    </p>
                                </div>
                            </div>
                            <div class="text-center col-lg-3 col-md-3 col-sm-9 col-xs-9 plans tile"
                                 data-value="2" data-detail="{{\App\Models\Plan::getDetail('2')}}">
                                <div style="text-align:center;padding:90px 0 90px 0;">
                                    <p class="plan_name">{{$plans[1]->name}}</p>
                                    <br>
                                    <p class="">
                                        従業員数上限 :
                                        {{$plans[1]->employee_limit}}人
                                    </p>
                                    <p class="">
                                        月額 :
                                        {{$plans[1]->monthly_price}}円
                                    </p>
                                </div>
                            </div>
                            <div class="text-center col-lg-3 col-md-3 col-sm-9 col-xs-9 plans tile"
                                 data-value="3" data-detail="{{\App\Models\Plan::getDetail('3')}}">
                                <div style="text-align:center;padding:90px 0 90px 0;">
                                    <p class="plan_name">{{$plans[2]->name}}</p>
                                    <br>
                                    <p class="">
                                        従業員数上限 :
                                        {{$plans[2]->employee_limit}}人
                                    </p>
                                    <p class="">
                                        月額 :
                                        {{$plans[2]->monthly_price}}円
                                    </p>
                                </div>
                            </div>

                        </div>
                        <br><br>
                        <div class="form-group">
                            <label for="message-text" class="form-control-label">店舗情報:</label>
                        </div>
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="略称" required
                                   value="{{ old('name') }}" maxlength="30">
                        </div>
                        <div class="form-group">
                            <input type="text" name="formal_name" class="form-control" placeholder="正式名称" required
                                   value="{{ old('formal_name') }}" maxlength="30">
                        </div>
                        <div class="form-group">
                            <input type="text" name="zip_1" class="form-control zipcode col-sm-4" placeholder="郵便番号(3桁)"
                                   required value="{{ old('zip_1') }}" style="width: 110px;">
                            <span class="col-sm-1">-</span>
                            <input type="text" name="zip_2" class="form-control zipcode col-sm-5" placeholder="郵便番号(4桁)"
                                   required value="{{ old('zip_2') }}" style="width: 125px;margin-bottom: 15px;">
                        </div>
                        <div class="form-group">
                            <input type="text" name="pref" class="form-control" placeholder="都道府県" required
                                   value="{{ old('pref') }}" maxlength="30">
                        </div>
                        <div class="form-group">
                            <input type="text" name="address" class="form-control" placeholder="市区町村・番地" required
                                   value="{{ old('address') }}" maxlength="30">
                        </div>
                        <div class="form-group">
                            <input type="text" name="building" class="form-control" placeholder="その他(建物名)"
                                   value="{{ old('building') }}" maxlength="30">
                        </div>
                        <div class="form-group">
                            <input type="text" name="tel" class="form-control" placeholder="電話番号" required
                                   value="{{ old('tel') }}">
                        </div>
                        <div class="form-group">
                            <input type="text" id="time" name="timing_of_tomorrow" class="form-control"
                                   placeholder="日付変更時刻" required value="{{ old('timing_of_tomorrow') }}">
                        </div>
                        <div class="form-group">
                            <label>出退勤時間の丸め分数</label>
                            <select name="round_minute_attendance">
                                <option value="1" {{old("round_minute_attendance")=="1"?"selected":""}}>1</option>
                                <option value="10" {{old("round_minute_attendance")=="10"?"selected":""}}>10</option>
                                <option value="15" {{old("round_minute_attendance")=="15"?"selected":""}}>15</option>
                                <option value="30" {{old("round_minute_attendance")=="30"?"selected":""}}>30</option>
                            </select>
                            分
                        </div>
                        <div class="form-group">
                            <label>休憩時間の丸め分数</label>
                            <select name="round_minute_break">
                                <option value="1" {{old("round_minute_attendance")=="1"?"selected":""}}>1</option>
                                <option value="10" {{old("round_minute_attendance")=="10"?"selected":""}}>10</option>
                                <option value="15" {{old("round_minute_attendance")=="15"?"selected":""}}>15</option>
                                <option value="30" {{old("round_minute_attendance")=="30"?"selected":""}}>30</option>
                            </select>
                            分
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="form-control-label">支払い情報:</label>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="card[name]" placeholder="名義"
                                   value="{{old("card.name")}}" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="card[number]" placeholder="カード番号"
                                   value="{{old("card.number")}}" required>
                        </div>
                        <div class="form-group">
                            <label>有効期限</label>
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="card[exp_month]" placeholder="月"
                                           value="{{old("card.exp_month")}}" required>
                                </div>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="card[exp_year]" placeholder="年"
                                           value="{{old("card.exp_year")}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>セキュリティコード</label>
                            <input type="number" class="form-control" name="card[cvc]" placeholder="***"
                                   value="{{old("card.cvc")}}" required>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">登録</button>
                </div>

            </div>
        </div>
    </form>
</div>