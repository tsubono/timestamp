<div class="modal inmodal" id="timeEditModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/workplace/edit_time" method="post" accept-charset="UTF-8" data-remote="data-remote"
          class="form-horizontal">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="name" type="hidden" value="{{$workplace->name}}">
        <input name="formal_name" type="hidden" value="{{$workplace->formal_name}}">
        <?php /**/ $zip_1 = $workplace->zip_code != "" ? explode('-', $workplace->zip_code)[0] : "" /**/ ?>
        <?php /**/ $zip_2 = $workplace->zip_code != "" ? explode('-', $workplace->zip_code)[1] : "" /**/ ?>
        <input name="zip_1" type="hidden" value="{{$zip_1}}">
        <input name="zip_2" type="hidden" value="{{$zip_2}}">

        <input name="pref" type="hidden" value="{{$workplace->pref}}">
        <input name="address" type="hidden" value="{{$workplace->address}}">
        <input name="building" type="hidden" value="{{$workplace->building}}">
        <input name="tel" type="hidden" value="{{$workplace->tel}}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">勤務場所の詳細設定</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">日付変更</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="timing_of_tomorrow" id="time"
                                       value="{{ $workplace->timing_of_tomorrow }}" class="form-control"
                                       placeholder="00:00">
                                <span class="help-block m-b-none">営業日の日付を変更する時刻を設定します。</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">出勤・退勤</label>

                            <div class="col-xs-5">
                                <select name="round_minute_attendance" class="form-control">
                                    @foreach([1, 10, 15, 30, 60] as $min)
                                        <option value="{{ $min }}"
                                                @if($min == $workplace->round_minute_attendance) selected @endif>
                                            {{ $min }}分単位
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">休憩・復帰</label>

                            <div class="col-xs-5">
                                <select name="round_minute_break" class="form-control">
                                    @foreach([1, 10, 15, 30, 60] as $min)
                                        <option value="{{ $min }}"
                                                @if($min == $workplace->round_minute_break) selected @endif>
                                            {{ $min }}分単位
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">給与計算方法</label>

                            <div class="col-xs-5">
                                <label class="radio-inline">
                                    <input type="radio" name="payroll_role" value="1" {{$workplace->payroll_role=="1"?"checked":""}}>
                                    切り捨て
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="payroll_role" value="2" {{$workplace->payroll_role=="2"?"checked":""}}>
                                    四捨五入
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>

            </div>
        </div>
    </form>
</div>