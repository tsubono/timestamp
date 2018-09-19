<div class="modal inmodal" id="timecardAddModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;"
     xmlns="http://www.w3.org/1999/html">
    <form action="/timecard/add_timecard" method="post" accept-charset="UTF-8" class="form-horizontal" id="addForm"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="timecard_id" type="hidden" value="">
        <input name="date" type="hidden" value="{{$timecard->date??""}}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">タイムカード追加</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        {{--<div class="form-group">--}}
                            {{--<label class="font-normal col-xs-12 col-sm-2 control-label">日付</label>--}}

                            {{--<div class="col-xs-10 col-sm-6">--}}
                                {{--<input type="text" name="date" value="{{old('date')}}" class="form-control"--}}
                                       {{--placeholder="例) {{\Carbon\Carbon::now()->format('Y-m-d')}}">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="form-group time">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">日時</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="time" value="{{old('time')}}" class="form-control dateTimes"
                                       placeholder="例) {{\Carbon\Carbon::now()->format('Y-m-d H:i')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">従業員名</label>

                            <div class="col-xs-10 col-sm-6">
                                <select name="employee_uid">
                                    {{--@foreach($employees as $employee)--}}
                                        {{--<option value="{{$employee->uid}}">{{$employee->name}}</option>--}}
                                    {{--@endforeach--}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <p id="add_error" style="color: red;"></p>
                        <input type="hidden" name="control_id" value="-1">
                        @foreach ($controls as $control)
                            <input type="button" id="control_{{$control['id']}}" data-id="{{$control['id']}}" class="btn btn-primary btn-lg controls" value="{{$control['label']}}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
