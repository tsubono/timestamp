@extends('basic')

@include('elements.toast')

@section('title')
    <title>出勤簿出力 | TIMESTAMP</title>
@stop

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
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>出勤簿出力</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="panel blank-panel">
                            <div class="panel-heading">
                                <div class="panel-options">

                                </div>
                            </div>
                            <div class="panel-body">
                                <form action="/working_report/export" method="post" accept-charset="UTF-8"
                                      class="form-horizontal">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="font-normal col-xs-12 col-sm-2 control-label">対象期間</label>
                                            <div class="col-xs-10 col-sm-3">
                                                <select name="start_y">
                                                    <option value=""></option>
                                                    @for($i=2000;$i<=\Carbon\Carbon::now()->format('Y');$i++)
                                                        <option value="{{$i}}">{{$i}}年</option>
                                                    @endfor
                                                </select>
                                                <select name="start_m">
                                                    <option value=""></option>
                                                    @for($i=1;$i<13;$i++)
                                                        <option value="{{$i}}">{{$i}}月</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-xs-10 col-sm-1">
                                                <small>〜</small>
                                            </div>
                                            <div class="col-xs-10 col-sm-3">
                                                <select name="end_y">
                                                    <option value=""></option>
                                                    @for($i=2000;$i<=\Carbon\Carbon::now()->format('Y');$i++)
                                                        <option value="{{$i}}">{{$i}}年</option>
                                                    @endfor
                                                </select>
                                                <select name="end_m">
                                                    <option value=""></option>
                                                    @for($i=1;$i<13;$i++)
                                                        <option value="{{$i}}">{{$i}}月</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <br>
                                        <br>
                                        <div class="form-group">
                                            <label class="font-normal col-xs-12 col-sm-2 control-label">従業員</label>
                                            <div class="col-xs-10 col-sm-3">
                                                <select name="employee_uid" class="form-control">
                                                    <option value="0" {{old('employee_uid')=="0"?"selected":""}}>全員
                                                    </option>
                                                    @foreach($employees as $employee)
                                                        <option value="{{$employee->uid}}" {{old('employee_uid')==$employee->uid?"selected":""}}>{{$employee->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-lg btn-primary submitBtn">上記の条件で出力</button>
                                </form>

                            </div>

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
            $('.submitBtn').click (function() {
                $('div.alert').each (function() {
                    $(this).css('display', 'none');
                })
            });

        }
    </script>

@stop