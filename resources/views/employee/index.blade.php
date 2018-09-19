@extends('basic')

@include('elements.toast')

@section('title')
    <title>従業員一覧 | TIMESTAMP</title>
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
                        <h5>従業員</h5> &nbsp;
                        <span style="float: left;margin-left: 5px;" class="badge badge-default">
                            {{ count($employees) + count($resigned_employees) }}
                        </span>
                    </div>
                    <div class="ibox-content">
                        @if(count($employees) + count($resigned_employees) <= $workplace->plan->employee_limit)
                            <button data-toggle="modal" data-target="#employeeCreateModal"
                                    class="btn btn-sm btn-primary btn-outline pull-right m-t-n-xs" type="button">従業員を追加
                            </button>
                        @endif
                        <div class="panel blank-panel">
                            <div class="panel-heading">
                                <div class="panel-options">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#tab-1">在籍 <span
                                                        class="badge badge-primary">{{ count($employees) }}</span></a>
                                        </li>
                                        <li class=""><a data-toggle="tab" href="#tab-2">退職 <span
                                                        class="badge badge-warning">{{ count($resigned_employees) }}</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="tab-content">
                                    <div id="tab-1" class="tab-pane active">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>氏名</th>
                                                    <th>最終勤務日</th>
                                                    <th>現在の出勤状態</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                @foreach($employees as $idx => $employee)
                                                    @if (!empty($employee))
                                                        <tr>
                                                            <td></td>
                                                            <td>{{ $employee->name }}</td>
                                                            <td>{{ $employee->current_date }}</td>
                                                            <td>{{ $employee->current_status }}</td>
                                                            <td><a href="/employee/{{$employee->uid}}"
                                                                   class="btn btn-xs btn-default">詳細</a></td>
                                                            <form action="/employee/delete_employee" id="form_{{$idx}}"
                                                                  method="post" accept-charset="UTF-8"
                                                                  class="form-horizontal">
                                                                <input name="_token" type="hidden"
                                                                       value="{{ csrf_token() }}">
                                                                <input type="hidden" name="uid" value="{{$employee->uid}}">
                                                                <td><a href="#" class="btn btn-xs btn-danger deleteBtn"
                                                                       data-idx="{{$idx}}">削除</a></td>
                                                            </form>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="tab-2" class="tab-pane">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>氏名</th>
                                                <th>最終勤務日</th>
                                                <th>現在の出勤状態</th>
                                                <th>&nbsp;</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            @foreach($resigned_employees as $idx2 => $employee)
                                                @if (!empty($employee))
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $employee->name }}</td>
                                                        <td>{{ $employee->current_date }}</td>
                                                        <td>{{ $employee->current_status }}</td>
                                                        <td><a href="/employee/{{$employee->uid}}"
                                                               class="btn btn-xs btn-default">詳細</a></td>
                                                        <form action="/employee/delete_employee" id="form_{{$idx+$idx2+1}}"
                                                              method="post" accept-charset="UTF-8"
                                                              class="form-horizontal">
                                                            <input name="_token" type="hidden"
                                                                   value="{{ csrf_token() }}">
                                                            <input type="hidden" name="uid" value="{{$employee->uid}}">
                                                            <td><a href="#" class="btn btn-xs btn-danger deleteBtn"
                                                               data-idx="{{$idx+$idx2+1}}">削除</a></td>
                                                        </form>
                                                    </tr>
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
    <style>
        th {
            text-align: center;
        }
    </style>
    <script>
        window.onload = function () {
            $('.deleteBtn').click(function () {
                if (confirm('本当に削除してもよろしいですか。')) {
                    var idx = $(this).data('idx');
                    $('#form_' + idx).submit();
                } else {
                    return false;
                }
            });

//            $('.date').datepicker({
//                language: 'ja',
//                format: "yyyy-mm-dd",
//                autoclose: true
//            });
            $('.date').datetimepicker({
                format: 'Y-m-d',
                lang: 'ja',
                timepicker:false
            });
        }
    </script>
    @include('employee.modal.add_employee')
@endsection
