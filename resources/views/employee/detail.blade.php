@extends('basic')

@include('elements.toast')

@section('title')
    <title>従業員詳細 | TIMESTAMP</title>
@stop

@section('header')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-9">
            <h2>
                @if(!empty($employee->resigned_date))
                    <span class="label label-default">退職</span>
                @endif
                {{ $employee->name }}
                <small>{{ $employee->name_kana }}</small>
            </h2>
        </div>
    </div>
@endsection

@section('js-footer')
    @parent
@endsection

@section('content')
    <div class="row">
        @if (!empty($message))
            <div class="alert alert-success">{{$message}}</div>
        @endif
        @if (!empty($err_message))
            <div class="alert alert-danger">{{$err_message}}</div>
        @endif
        <div class="col-sm-4 col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>アイコン</h5>
                    <div class="ibox-tools">
                        <a data-toggle="modal" data-target="#iconEditModal"><i class="fa fa-edit"></i> 編集</a>
                    </div>
                </div>
                <div class="ibox-content">
                    @if (!empty($employee->icon))
                        @if ($employee->icon_type=="icon")
                            <img alt="" src="{{ \App\Models\Icon::getPath($employee->icon,'original_mini') }}" style="display: block;">
                        @elseif ($employee->icon_type=="icon_file")
                            <img alt="" src="{{ asset('storage'.\App\Models\Icon::getPath($employee->icon,'original_mini')) }}"
                                 style="display: block;">
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-sm-8 col-md-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>基本情報</h5>
                    <div class="ibox-tools">
                        <a data-toggle="modal" data-target="#profileEditModal"><i class="fa fa-edit"></i> 編集</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-xs-4">性別</div>
                        <div class="col-xs-8">
                            @if($employee->gender === 'male')
                                <span class="label label-success">男性</span>
                            @elseif($employee->gender === 'female')
                                <span class="label label-danger">女性</span>
                            @endif
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-4">生年月日</div>
                        <div class="col-xs-8">{{ $employee->birthday }}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">交通費(往復/1日)</div>
                        <div class="col-xs-8">{{ $employee->traffic_cost }}円</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">入社日</div>
                        <div class="col-xs-8">{{ $employee->joined_date }}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4">退職日</div>
                        <div class="col-xs-8">{{ $employee->resigned_date }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--タイムカード一覧表示--}}
    <div class="row">
        <div class="col-xs-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>タイムカード</h5>
                </div>
                <div class="ibox-content">
                    <div class="text-center">
                        <a class="btn btn-sm btn-white"
                           href="/employee/{{$employee->uid}}?year={{\Carbon\Carbon::parse($date)->subMonths("1")->format('Y')}}&month={{\Carbon\Carbon::parse($date)->subMonths("1")->format('m')}}">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <span>{{ $year."年".$month."月" }}</span>
                        <a class="btn btn-sm btn-white"
                           href="/employee/{{$employee->uid}}?year={{\Carbon\Carbon::parse($date)->addMonths("1")->format('Y')}}&month={{\Carbon\Carbon::parse($date)->addMonths("1")->format('m')}}">
                            <i class="fa fa-arrow-right"></i>
                        </a>


                        <div class="text-right">
                            {{--<a href="" class="btn btn-success">出勤簿出力</a>--}}
                            {{--<a href="" class="btn btn-success">給与明細出力</a>--}}
                            @if ($is_clock_out)
                                <a data-toggle="modal" data-target="#timecardAddModal"
                                   class="btn btn-success add_timecard">新規追加</a>
                            @endif
                        </div>

                        <div id="employee-attendance-table" class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>日付</th>
                                    <th>出勤日時</th>
                                    <th>退勤日時</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($timecards as $timecard)
                                    <tr>
                                        <td></td>
                                        <td>{{ $timecard->date }}</td>
                                        <td>{{ !empty($timecard->first_time)?\Carbon\Carbon::parse($timecard->first_time)->format('Y-m-d H:i'):"" }}</td>
                                        <td>{{ !empty($timecard->last_time)?\Carbon\Carbon::parse($timecard->last_time)->format('Y-m-d H:i'):"" }}</td>
                                        @if (!$timecard->is_clocking_out)
                                            <td><a data-toggle="modal" data-target="#timecardAddModal"
                                                   class="btn btn-xs btn-success update_status"
                                                   data-employee_uid="{{$timecard->employee->uid??""}}"
                                                   data-time="{{$timecard->date}} 00:00"
                                                   data-date="{{$timecard->date}}"
                                                   data-timecard_id="{{$timecard->id}}"
                                                >
                                                    出勤状態更新
                                                </a>
                                        @else
                                            <td></td>
                                        @endif
                                        <td><a href="/employee/timecard/{{$employee->uid}}/{{$timecard->id}}"
                                               class="btn btn-xs btn-default">詳細</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">この月の勤務記録はありません。</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                {{--給与項目表示--}}
                <div class="row">
                    <div class="col-xs-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>給与設定履歴</h5>
                                <div class="ibox-tools">
                                    <a data-toggle="modal" data-target="#salaryAddModal"><i class="fa fa-edit"></i>
                                        追加</a>
                                </div>
                            </div>

                            <div class="ibox-content">

                                <div id="employee-payment-table" class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>適用開始日付</th>
                                            <th>時間帯</th>
                                            <th>給与設定</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($salaries as $date => $salary)
                                            @foreach($salary as $idx => $record)
                                                @if ($idx == 0)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{\Carbon\Carbon::parse($date)->format('Y-m-d')}}</td>
                                                        <td>{{\Carbon\Carbon::parse(\Carbon\Carbon::parse($date)->format('Y-m-d'). ' '.$record->start_time)->format('H:i')}}</td>
                                                        <td>{{$record->hourly_pay}}円</td>
                                                        @if (!$record->default_flg)
                                                            <td><a data-toggle="modal"
                                                                   data-target="#timecardDeleteModal_{{$date}}"
                                                                   class="btn btn-xs btn-default">削除</a></td>
                                                        @endif
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{\Carbon\Carbon::parse(\Carbon\Carbon::parse($date)->format('Y-m-d'). ' '.$record->start_time)->format('H:i')}}</td>
                                                        <td>{{$record->hourly_pay}}円</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @foreach($salaries as $date => $salary)
                                        @include('employee.modal.delete_salary',['date'=>$date])

                                    @endforeach

                                </div>
                            </div>
                            <br><br>
                            <a href="/employee" class="btn btn-default">従業員一覧に戻る</a>

                        </div>

                    </div>
                </div>
            </div>

            <script>
                window.onload = function () {

                    /******************************************/
                    /* タイムカード新規追加
                     ******************************************/
                    /*
                     * 新規追加モーダル項目切り替え
                     */
                    $('[name=time]').keydown(function () {
                        $('.controls').css('display', 'none');
                    });
                    $('[name=time]').change(function () {
                        $('#add_error').text("");
                        $('.controls').css('display', 'none');
                        ajax_control();
                    });
                    ajax_control();

                    //選択可能なコントロールリストを取得
                    function ajax_control() {
                        $('.controls').css('display', 'none');
                        $.ajax({
                            type: "POST",
                            url: "/timecard/get_enable_control",
                            data: {
                                "time": $('[name=time]').val(),
                                "employee_uid": $('[name=employee_uid]').val(),
                                "employee_flg": true,
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function (data) {
                                var controls = JSON.parse(data);
                                if (controls['error'] != undefined && controls['error'] != "") {
                                    $('#add_error').text(controls['error']);
                                    return;
                                }
                                for (var i = 0; i < controls.length; i++) {
                                    $('#control_' + controls[i]).fadeIn();
                                    $('.time').fadeIn();
                                }
                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                alert('Error : ' + errorThrown);
                            }
                        });
                    }

                    /*
                     * submit前処理
                     */
                    $('.controls').click(function () {
                        $('[name=control_id]').val($(this).data('id'));
                        $('#addForm').submit();
                    });

                    /*
                     * 新規追加押下時
                     */
                    $('.add_timecard').click(function () {
                        $('.time').css('display', 'none');
                        $('[name=time]').val("");
                        $('[name=timecard_id]').val("");
                        $('#add_error').text("");

                    });

                    /*
                     * 出勤状態更新押下時
                     */
                    $('.update_status').click(function () {
                        $('.time').css('display', 'none');

                        var selected_employee = $(this).data('employee_uid');

                        $('[name=time]').val($(this).data('date') + ' '+ '{{\App\Models\Workplace::getTimingOfTomorrow()}}');

                        $('[name=timecard_id]').val($(this).data('timecard_id'));

                        ajax_control();
                    });


                    /******************************************/
                    /* アイコン
                     ******************************************/
                    $('[name=icon_type]').change(function () {
                        var type = $('[name=icon_type]:checked').val();
                        $('.icon_input').css('display', 'none');
                        $('#' + type).fadeIn();

                        if (type=="icon_file") {
                            $('#icon_file').attr('required', true);
                        } else {
                            $('#icon_file').removeAttr('required');
                        }

                    });
                    var type = $('[name=icon_type]:checked').val();
                    $('.icon_input').css('display', 'none');
                    $('#' + type).fadeIn();

                    if (type=="icon_file") {
                        $('#icon_file').attr('required', true);
                    } else {
                        $('#icon_file').removeAttr('required');
                    }


//                    $('.date').datepicker({
//                        language: 'ja',
//                        format: "yyyy-mm-dd",
//                        autoclose: true
//                    });

                    $('.date').datetimepicker({
                        format: 'Y-m-d',
                        lang: 'ja',
                        timepicker:false
                    });

//                    var options = {step: 1, timeFormat: 'H:i'};
//                    $('.start_time').timepicker(options);
                    $('.start_time').datetimepicker({
                        format: 'H:i',
                        lang: 'ja',
                        datepicker:false
                    });

                    $('.dateTimes').datetimepicker({
                        format: 'Y-m-d H:i',
                        lang: 'ja'
                    });

                    /******************************************/
                    /* 給与設定
                     ******************************************/
                    $('#form_block\\[' + 1 + '\\]').css('display', 'none');

                    var frm_cnt = 1;
                    var original = $('#form_block\\[' + frm_cnt + '\\]');
                    var originCnt = frm_cnt;

                    $('.add_salary_form').click (function() {

                        if (frm_cnt == 1) {
                            original.fadeIn();
                            frm_cnt++;
                        } else {
                            original
                                    .clone()
                                    .hide()
                                    .insertAfter(original)
                                    .attr('id', 'form_block[' + frm_cnt + ']') // クローンのid属性を変更。
                                    .end() // 一度適用する
                                    .find('input').each(function(idx, obj) {
                                        $(obj).attr({
                                            name: $(obj).attr('name').replace(/\[[0-9]\]+$/, '[' + frm_cnt + ']')
                                        });
                                        if ($(obj).attr('type') == 'text') {
                                            $(obj).val('');
                                        }
                            });

                            var clone = $('#form_block\\[' + frm_cnt + '\\]');
                            clone.fadeIn();

//                            var options = {step: 1, timeFormat: 'H:i'};
//                            $('.start_time').timepicker(options);

                            $('.start_time').datetimepicker({
                                format: 'H:i',
                                lang: 'ja',
                                datepicker:false
                            });

                            $('.delete_salary_form').click (function() {
                                var removeObj = $(this).parent().parent();
                                removeObj.fadeOut('fast', function() {
                                    removeObj.remove();
                                    // 番号振り直し
                                    frm_cnt = 0;
                                    $(".form-block[id^='form_block']").each(function(index, formObj) {
                                        if ($(formObj).attr('id') != 'form_block[0]') {
                                            frm_cnt++;
                                            $(formObj)
                                                    .attr('id', 'form_block[' + frm_cnt + ']') // id属性を変更。
                                                    .find('input').each(function(idx, obj) {
                                                $(obj).attr({
                                                    name: $(obj).attr('name').replace(/\[[0-9]\]+$/, '[' + frm_cnt + ']')
                                                });
                                            });
                                        }
                                    });

                                    if (frm_cnt==0) {
                                        frm_cnt = 1;
                                    }
                                });
                            });

                            frm_cnt++;
                        }
                    });

                    $('.delete_salary_form').click (function() {
                        var removeObj = $(this).parent().parent();
                        removeObj.fadeOut('fast', function() {
                            removeObj.remove();
                            // 番号振り直し
                            frm_cnt = 0;
                            $(".form-block[id^='form_block']").each(function(index, formObj) {
                                if ($(formObj).attr('id') != 'form_block[0]') {
                                    frm_cnt++;
                                    $(formObj)
                                            .attr('id', 'form_block[' + frm_cnt + ']') // id属性を変更。
                                            .find('input').each(function(idx, obj) {
                                        $(obj).attr({
                                            name: $(obj).attr('name').replace(/\[[0-9]\]+$/, '[' + frm_cnt + ']')
                                        });
                                    });
                                }
                            });

                            if (frm_cnt==0) {
                                frm_cnt = 1;
                            }
                        });
                    });
                }


            </script>
            <style>
                td {text-align: left;}
            </style>


    @include('employee.modal.add_timecard')
    @include('employee.modal.edit_employee')
    @include('employee.modal.icon_employee')
    @include('employee.modal.add_salary')
@endsection