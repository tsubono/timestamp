@extends('basic')

@include('elements.toast')

@section('title')
    <title>タイムカード詳細 | TIMESTAMP</title>
@stop

@section('header')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-9">
            <h2>
                {{ $timecard->date }}
                <small>{{ $employee->name??"削除済み従業員" }}</small>
            </h2>
        </div>
    </div>
@endsection

@section('js-footer')
    @parent
    {{--<script src="/assets/js/admin/timeCardShow.js"></script>--}}
    {{--<script src="/assets/js/admin/timeCardEdit.js"></script>--}}
@endsection

@section('content')
    <div class="row">
        @if (!empty($message))
            <div class="alert alert-success">{{$message}}</div>
        @endif
        @if (!empty($err_message))
            <div class="alert alert-danger">{{$err_message}}</div>
        @endif
    </div>


    <div class="row">
        <div class="col-xs-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>勤務</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>営業日</th>
                                <th>実労働時間</th>
                                <th>休憩時間</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td>{{ $timecard->date }}</td>
                                    <td>{{ floor($time_card_info['work_time']/60)}}時間{{$time_card_info['work_time']%60}}分</td>
                                    <td>{{ floor($time_card_info['rest_time']/60)}}時間{{$time_card_info['rest_time']%60}}分</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--<div class="row" ng-app="timeCardShow">--}}
        {{--<div class="col-xs-12" ng-controller="timecardListController"--}}
             {{--ng-init="init('{{csrf_token()}}',{{$recordsJson}})">--}}
    <div class="row">
        <div class="col-xs-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>詳細</h5>
                    <div class="ibox-tools">
                        @if ($is_clocking_out && !empty($employee))
                            <a data-toggle="modal" data-target="#timecardEditModal"><i class="fa fa-edit"></i> 編集</a>
                            {{--<a ng-click="openEdit('')"><i class="fa fa-edit"></i> 編集</a>--}}
                        @endif
                        <a data-toggle="modal" data-target="#timecardDeleteModal"><i class="fa fa-edit"></i> 削除</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>種別</th>
                                <th>開始時間</th>
                                <th>終了時間</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($all_record as $record)
                                <tr>
                                    <td></td>
                                    <td>
                                        @if ($record->type=="1")
                                            出勤・退勤
                                        @else
                                            休憩入り・戻り
                                        @endif
                                    </td>
                                    <td>{{ $record->start_time}}</td>
                                    <td>{{ $record->end_time}}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                @if ($employee_flg)
                    <a href="/employee/{{$employee->uid}}" class="btn btn-default">従業員詳細に戻る</a>
                @else
                    <a href="/timecard" class="btn btn-default">タイムカード一覧に戻る</a>
                @endif
            </div>
        </div>
        {{--<script type="text/ng-template" id="timecardEdit.html">--}}
            {{--<div class="modal-header">--}}
                {{--<h3 class="modal-title">タイムカード編集</h3>--}}
            {{--</div>--}}
            {{--<div class="modal-body">--}}
                {{--<alert ng-show="alert" type="@{{alert.type}}">@{{alert.msg}}</alert>--}}
                {{--<form>--}}
                    {{--<div class="container-fluid">--}}

                        {{--<input type="hidden" ng-model="records.id">--}}

                        {{--<p style="margin:1em 0;">--}}
                            {{--<a class="btn btn-default btn-xs" ng-click="addRestForm()">フォーム追加</a>--}}
                        {{--</p>--}}
                        {{--<table class="table">--}}
                            {{--<tr ng-repeat="detail in records.details track by $index">--}}
                                {{--<td>--}}
                                    {{--<select ng-model="detail.type" class="form-control" id="type_@{{$index}}">--}}
                                        {{--@foreach($controls as $control)--}}
                                            {{--<option value="{{$control["id"]}}">{{$control["label"]}}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--<input type="text" class="form-control times" ng-model="detail.time" data-index="@{{$index}}"/>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--<a class="btn btn-default btn-sm" ng-click="rowDown()"><span class="glyphicon glyphicon-arrow-down"></span></a>--}}
                                    {{--<a class="btn btn-default btn-sm" ng-click="rowUp()"><span class="glyphicon glyphicon-arrow-up"></span></a>--}}
                                    {{--<a class="btn btn-default btn-sm" ng-click="deleteRestForm()"><span class="glyphicon glyphicon-trash"></span></a>--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                        {{--</table>--}}
                    {{--</div>--}}
                {{--</form>--}}
            {{--</div>--}}
            {{--<div class="modal-footer">--}}
                {{--<button class="btn btn-primary" ng-click="submit()">更新</button>--}}
            {{--</div>--}}
        {{--</script>--}}
    </div>
    <script>
       // window.onload = function () {

            //選択可能なコントロールリストを取得
//            function get_type_list(select_index) {
//                $('#type_'+select_index).empty();
//
//                if (select_index==0) {
//
//                    var obj = '<option value="0">出勤</option>';
//                    $('#type_'+select_index).append(obj);
//
//                } else {
//                    var obj = "";
//                    var before_type = $('#type_'+select_index-1).val();
//                    switch (before_type) {
//                        case 0: //出勤
//                            obj = '<option value="1">休憩入り</option>'+
//                                        '<option value="3">退勤</option>';
//
//                            break;
//                        case 1: //休憩入り
//                            obj = '<option value="2">休憩戻り</option>';
//                            break;
//                        case 2: //休憩戻り
//                            obj = '<option value="1">休憩入り</option>'+
//                                    '<option value="3">退勤</option>';
//                            break;
//                        case 3: //退勤
//                            obj = '<option value="0">出勤</option>';
//                            break;
//                        default:
//                            break;
//                    }
//                    $('#type_'+select_index).append(obj);
//
//                }
//            }

   //     }



    </script>

    @include('timecard.modal.edit_timecard')
    @include('timecard.modal.delete_timecard')
@endsection
