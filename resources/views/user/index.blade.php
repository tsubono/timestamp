@extends('basic')

@include('elements.toast')

@section('title')
    <title>ユーザー一覧 | TIMESTAMP</title>
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
                        <h5>ユーザー</h5> &nbsp;<span class="badge badge-default">{{ count($users) }}</span>
                    </div>
                    <div class="ibox-content">
                        <button data-toggle="modal" data-target="#userCreateModal"
                                class="btn btn-sm btn-primary btn-outline pull-right m-t-n-xs" type="button">ユーザーを追加
                        </button>
                        <div class="panel blank-panel">
                            <div class="panel-heading">
                                <div class="panel-options">

                                </div>
                            </div>
                            <div class="panel-body">
                                <div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                <th></th>
                                                {{--<th>名前</th>--}}
                                                <th>ログインID</th>
                                                <th>メールアドレス</th>
                                                <th></th>
                                                <th>&nbsp;</th>
                                                </thead>

                                                <tbody>
                                                @foreach($users as $idx => $user)
                                                    <tr>
                                                        <td></td>
                                                        {{--<td>{{ $user->name }}</td>--}}
                                                        <td>{{ $user->login_id }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>@if($user->enable_flg) <span
                                                                    class="label label-info">有効</span> @else <span
                                                                    class="label label-white">無効</span> @endif</td>
                                                        <td><a data-toggle="modal" data-target="#userEditModal_{{$idx}}"
                                                               class="btn btn-xs btn-primary">編集</a></td>
                                                        <form action="/user/delete_user" id="form_{{$idx}}"
                                                              method="post" accept-charset="UTF-8"
                                                              class="form-horizontal">
                                                            <input name="_token" type="hidden"
                                                                   value="{{ csrf_token() }}">
                                                            <input type="hidden" name="id" value="{{$user->id}}">
                                                            <td><a href="#" class="btn btn-xs btn-danger deleteBtn"
                                                                   data-idx="{{$idx}}">削除</a></td>
                                                        </form>
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
            $('.deleteBtn').click(function () {
                if (confirm('本当に削除してもよろしいですか。')) {
                    var idx = $(this).data('idx');
                    $('#form_' + idx).submit();
                } else {
                    return false;
                }
            })
        }
    </script>

    @foreach($users as $idx => $user)
        @include('user.modal.edit_user',['idx'=>$idx])
    @endforeach
    @include('user.modal.add_user')
@stop