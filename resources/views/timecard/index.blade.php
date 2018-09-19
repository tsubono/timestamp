@extends('basic')

@include('elements.toast')

@section('title')
    <title>タイムカード | TIMESTAMP</title>
@stop

@section('header')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-9">
            <h2>
                {{ $year }} 年 {{ $month }}月　タイムカード一覧
                <small></small>
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="text-center loginscreen animated fadeInDown">
        <div class="row">
            @if (!empty($message))
                <div class="alert alert-success">{{$message}}</div>
            @endif
            @if (!empty($err_message))
                <div class="alert alert-danger">{{$err_message}}</div>
            @endif
            <div class="col-xs-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>タイムカード</h5>
                    </div>
                    <div class="ibox-content">
                        <a class="btn btn-sm btn-white"
                           href="/timecard?year={{\Carbon\Carbon::parse($date)->subMonths("1")->format('Y')}}&month={{\Carbon\Carbon::parse($date)->subMonths("1")->format('m')}}">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <span>{{ $year."年".$month."月" }}</span>
                        <a class="btn btn-sm btn-white"
                           href="/timecard?year={{\Carbon\Carbon::parse($date)->addMonths("1")->format('Y')}}&month={{\Carbon\Carbon::parse($date)->addMonths("1")->format('m')}}">
                            <i class="fa fa-arrow-right"></i>
                        </a>

                        <div class="text-right">
                            <form action="/working_report/export" method="post" accept-charset="UTF-8"
                                  class="form-horizontal">
                                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                <input name="start_y" type="hidden" value="{{ $year }}">
                                <input name="start_m" type="hidden" value="{{ $month }}">
                                <input name="end_y" type="hidden" value="{{ $year }}">
                                <input name="end_m" type="hidden" value="{{ $month }}">
                                <button type="submit" class="btn btn-success">出勤簿出力</button>
                            </form>

                            {{--<a href="" class="btn btn-success">給与明細出力</a>--}}
                            <a data-toggle="modal" data-target="#timecardAddModal"
                               class="btn btn-success add_timecard">新規追加</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>日付</th>
                                    <th>従業員名</th>
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
                                            <td><span style={{empty($timecard->employee)?'color:red;':""}}>{{ !empty($timecard->employee)?$timecard->employee->name : "削除済み従業員" }}</span></td>
                                            <td>{{ !empty($timecard->first_time)?\Carbon\Carbon::parse($timecard->first_time)->format('Y-m-d H:i'):"" }}</td>
                                            <td>{{ !empty($timecard->last_time)?\Carbon\Carbon::parse($timecard->last_time)->format('Y-m-d H:i'):"" }}</td>
                                            @if (!$timecard->is_clocking_out)
                                                <td><a data-toggle="modal" data-target="#timecardAddModal"
                                                       class="btn btn-xs btn-success update_status"
                                                       data-employee_uid="{{$timecard->employee->uid??""}}"
                                                       data-date="{{$timecard->date}}" data-id="{{$timecard->id}}">
                                                        出勤状態更新
                                                    </a>
                                            @else
                                                <td></td>
                                            @endif
                                            <td><a href="/timecard/{{$timecard->id}}" class="btn btn-xs btn-default">詳細</a>
                                            </td>
                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">この月の勤務記録はありません。</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <input type="hidden" id="selected_employee" value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        td {
            text-align: left;
        }
    </style>

    <script>
        window.onload = function () {


            /*
             * 新規追加モーダル項目切り替え
             */
            $('[name=time]').keydown(function () {
                $('.controls').css('display', 'none');
            });
            $('[name=time]').change(function () {
                $('#add_error').text("");
                $('.controls').css('display', 'none');
                if ($(this).data('disable_flg') != "1") {
                    ajax_employee();
                }
                ajax_control();
            });

            $('[name=employee_uid]').change(function () {
                ajax_control();
            });
            ajax_employee();
            ajax_control();

            //選択可能な従業員リストを取得
            function ajax_employee() {
                $.ajax({
                    type: "POST",
                    url: "/timecard/get_enable_employee",
                    data: {
                        "time": $('[name=time]').val(),
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        var employees = JSON.parse(data);
                        $('[name=employee_uid]').empty();
                        for (var i = 0; i < employees.length; i++) {
                            $('[name=employee_uid]').append('<option value="' + employees[i]["uid"] + '">' + escapeHtml(employees[i]["lname"]) + ' ' + escapeHtml(employees[i]["fname"]) + '</option>')
                        }
                        ajax_control();
                        $('#add_error').text("");
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        alert('Error : ' + errorThrown);
                    }
                });
            }
            function escapeHtml(str) {
                str = str.replace(/&/g, '&amp;');
                str = str.replace(/</g, '&lt;');
                str = str.replace(/>/g, '&gt;');
                str = str.replace(/"/g, '&quot;');
                str = str.replace(/'/g, '&#39;');
                return str;
            }

            //選択可能なコントロールリストを取得
            function ajax_control() {
                $('.controls').css('display', 'none');
                $.ajax({
                    type: "POST",
                    url: "/timecard/get_enable_control",
                    data: {
                        "time": $('[name=time]').val(),
                        "employee_uid": $('[name=employee_uid]').val(),
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
                document.getElementsByName('time')[0].setAttribute('data-disable_flg', 0);
                $('[name=timecard_id]').val("");
                $('[name=employee_uid]').empty();
                ajax_employee();
                ajax_control();
            });

            /*
             * 出勤状態更新押下時
             */
            $('.update_status').click(function () {
                var selected_employee = $(this).data('employee_uid');

                $('[name=time]').val($(this).data('date') + ' '+ '{{\App\Models\Workplace::getTimingOfTomorrow()}}');
                $('[name=timecard_id]').val($(this).data('id'));
                $('[name=employee_uid]').empty();

                @foreach($employees as $employee)
                    $('[name=employee_uid]').append('<option value="{{$employee->uid}}">{{$employee->name}}</option>');
                @endforeach
                $('[name=employee_uid] option').each(function () {
                    if ($(this).val() != selected_employee) {
                        $(this).remove();
                    }
                });
                document.getElementsByName('time')[0].setAttribute('data-disable_flg', 1);

                ajax_control();
            });

            $('.dateTimes').datetimepicker({
                format: 'Y-m-d H:i',
                lang: 'ja'
            });

        }

    </script>
    @include('timecard.modal.add_timecard')
@stop