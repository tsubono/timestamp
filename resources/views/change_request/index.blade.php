@extends('basic')

@include('elements.toast')

@section('title')
    <title>変更依頼一覧 | TIMESTAMP</title>
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
                        <h5>変更依頼一覧</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="panel blank-panel">

                            <div class="panel-heading">
                                <div class="panel-options">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#tab-1">未承認 <span
                                                        class="badge badge-primary">{{ count($change_requests) }}</span></a>
                                        </li>
                                        <li class=""><a data-toggle="tab" href="#tab-2">承認履歴 <span
                                                        class="badge badge-warning">{{ count($change_request_histories) }}</span></a>
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
                                                <th></th>
                                                <th>id</th>
                                                <th>対象日付</th>
                                                <th>従業員</th>
                                                <th>承認状態</th>
                                                <th></th>
                                                </thead>

                                                <tbody>
                                                @foreach($change_requests as $idx => $change_request)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $change_request->id }}</td>
                                                        <td>{{ $change_request->date }}</td>
                                                        <td>{{ $change_request->employee->name }}</td>
                                                        <td>
                                                            @if ($change_request->status=="1")
                                                                承認
                                                            @elseif ($change_request->status=="2")
                                                                否認
                                                            @else
                                                                未承認
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a data-toggle="modal"
                                                               data-target="#changeRequestModal_{{$idx}}"
                                                               class="btn btn-sm btn-primary">更新</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="tab-2" class="tab-pane">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                <th></th>
                                                <th>id</th>
                                                <th>対象日付</th>
                                                <th>従業員</th>
                                                <th>承認状態</th>
                                                <th></th>
                                                </thead>

                                                <tbody>
                                                @foreach($change_request_histories as $idx => $change_request_history)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $change_request_history->id }}</td>
                                                        <td>{{ $change_request_history->date }}</td>
                                                        <td>{{ $change_request_history->employee->name }}</td>
                                                        <td>
                                                            @if ($change_request_history->status=="1")
                                                                承認
                                                            @elseif ($change_request_history->status=="2")
                                                                否認
                                                            @else
                                                                未承認
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a data-toggle="modal"
                                                               data-target="#changeRequestModal_{{$idx+count($change_requests)+1}}"
                                                               class="btn btn-sm btn-primary">詳細</a>
                                                        </td>
                                                    </tr>
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

            $('.approveBtn').click(function () {
                if (confirm('承認してもよろしいですか。')) {
                    var idx = $(this).data('idx');
                    var form = $('#form_' + idx);
                    form.find('[name=status]').val('1');
                    form.submit();
                }
            });

            $('.unApproveBtn').click(function () {
                if (confirm('否認してもよろしいですか。')) {
                    var idx = $(this).data('idx');
                    var form = $('#form_' + idx);
                    form.find('[name=status]').val('2');
                    form.submit();
                }
            });

        }
    </script>

    @foreach($change_requests as $idx => $change_request)
        @include('change_request.modal.update_request',['idx'=>$idx])
    @endforeach
    @foreach($change_request_histories as $idx => $change_request)
        @include('change_request.modal.update_request',['idx'=>$idx+count($change_requests)+1])
    @endforeach
    {{--@include('user.modal.add_user')--}}
@stop