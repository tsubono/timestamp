@extends('basic')

@include('elements.toast')

@section('title')
    <title>支払い情報 | TIMESTAMP</title>
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
                            <h5>支払い情報</h5>
                            <div class="ibox-tools">
                                <a data-toggle="modal" data-target="#paymentEditModal"><i class="fa fa-edit"></i> 編集</a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            @if ($card)
                                <table class="table table-striped">
                                    <div class="">
                                        <div class="row">
                                            <div class="col-xs-3">名義</div>
                                            <div class="col-xs-5">{{$card->name ?? ""}}</div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-xs-3">支払い種類</div>
                                            <div class="col-xs-5">{{$card->brand ?? ""}}</div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-xs-3">番号下4桁</div>
                                            <div class="col-xs-5">{{$card->last4 ?? ""}}</div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-xs-3">有効期限</div>
                                            <div class="col-xs-5">{{$card->exp_month."/".$card->exp_year ?? ""}}</div>
                                        </div>
                                        <br>
                                    </div>
                                </table>
                            @else
                                <p style="color:red;">支払い情報が未登録もしくは無効です。</p>
                                <a data-toggle="modal" data-target="#paymentEditModal" class="btn btn-primary">支払い情報を登録する</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('payment.modal.edit_payment')
@stop