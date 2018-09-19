@extends('basic')

@include('elements.toast')

@section('title')
    <title>給与明細出力一覧 | TIMESTAMP</title>
@stop

@section('content')
    <div class="text-center loginscreen animated fadeInDown">
        <div class="row">
            @if (!empty($message))
                <div class="alert alert-success">{{$message}}</div>
            @endif
            @if (!empty($err_message))
                <div class="alert alert-danger" id="err_msg">{{$err_message}}</div>
            @else
                <div class="alert alert-danger" id="err_msg" style="display: none;">{{$err_message}}</div>
            @endif
            <div class="col-xs-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>給与明細出力一覧</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="panel blank-panel">
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div id="tab-1" class="tab-pane active">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>氏名</th>
                                                    <th>出力年月</th>
                                                    <th>出力</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                @foreach($employees as $idx => $employee)
                                                    @if (!empty($employee))
                                                        <form action="/payment_report/detail" id="form_{{$idx}}"
                                                              method="post" accept-charset="UTF-8"
                                                              class="form-horizontal">
                                                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                                            <tr>
                                                                <td></td>
                                                                <td>{{ $employee->name }}</td>
                                                                <td>
                                                                    <select name="year">
                                                                        @for($i=1;$i<=\Carbon\Carbon::now()->format('Y');$i++)
                                                                            <option value="{{$i}}" {{\Carbon\Carbon::now()->format('Y')==$i?"selected":""}}>{{$i}}
                                                                                年
                                                                            </option>
                                                                        @endfor
                                                                    </select>
                                                                    <select name="month">
                                                                        @for($i=1;$i<13;$i++)
                                                                            <option value="{{$i}}" {{\Carbon\Carbon::now()->format('m')==$i?"selected":""}}>{{$i}}
                                                                                月
                                                                            </option>
                                                                        @endfor
                                                                    </select>
                                                                </td>
                                                                <input type="hidden" name="employee_uid" value="{{$employee->uid}}">
                                                                <td>
                                                                    <a href="#" class="btn btn-xs btn-primary submitBtn"
                                                                       data-idx="{{$idx}}">給与詳細画面へ</a>
                                                                </td>
                                                            </tr>
                                                        </form>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
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

            $('.submitBtn').click(function () {
                var idx = $(this).data('idx');
                $('#form_' + idx).submit();
            });
        }
    </script>

@stop