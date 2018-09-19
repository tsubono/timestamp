@extends('basic')

@include('elements.toast')

@section('title')
    <title>レコーダー | TIMESTAMP</title>
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
                        <h5>レコーダー</h5>
                    </div>
                    <div class="ibox-content">
                        <button data-toggle="modal" data-target="#recorderCreateModal"
                                class="btn btn-sm btn-primary btn-outline pull-right m-t-n-xs" type="button">レコーダーを追加
                        </button>

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>名前</th>
                                <th>URL</th>
                                <th>パスコード</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($recorders as $idx => $recorder)
                                <tr>
                                    <td></td>
                                    <td>{{ $recorder->name }}</td>
                                    <td>
                                        <a href="{{ env('APP_URL_SCHEME', 'http://').$subdomain.'.'.env('APP_URL_DOMAIN', 't-stamp.loc').'/timestamp/'.$recorder->uid }}" target="_blank">
                                            {{ env('APP_URL_SCHEME', 'http://').$subdomain.'.'.env('APP_URL_DOMAIN', 't-stamp.loc').'/timestamp/'.$recorder->uid }}
                                        </a>
                                    </td>
                                    <td>{{ $recorder->pass_code }}</td>
                                    <td>
                                        <a data-toggle="modal" data-target="#recorderEditModal_{{$idx}}"
                                           class="btn btn-xs btn-primary">編集</a>
                                        <form action="/recorder/delete_recorder" method="post" accept-charset="UTF-8">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="uid" value="{{$recorder->uid}}">
                                            <button type="submit" class="btn btn-xs btn-danger"
                                                    onclick="return confirm('削除してもよろしいですか。');">削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">表示するレコーダーがありません。</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
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
    @foreach($recorders as $idx => $recorder)
        @include('recorder.modal.edit_recorder',['idx'=>$idx])
    @endforeach
    @include('recorder.modal.add_recorder')
@stop