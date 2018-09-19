@extends('basic')

@include('elements.toast')

@section('title')
    <title>契約プラン | TIMESTAMP</title>
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
                            <h5>契約プラン</h5>
                            <div class="ibox-tools">
                                <button data-toggle="modal" data-target="#planEditModal" class="btn btn-sm btn-primary btn-outline pull-right m-t-n-xs openBtn" type="button">プラン変更</button>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <table class="table table-striped">
                                <div class="">
                                    <div class="row">
                                        <div class="col-xs-3">プラン名</div>
                                        <div class="col-xs-5">
                                            {{\App\Models\Plan::getName($workplace->plan_id)}} <br>
                                            @if (!empty($workplace->next_plan_id))
                                                <span style="color:red;"><small>※{{Carbon\Carbon::parse($workplace->next_charge_date)->addDays('1')->format('Y年m月d日')}}より{{\App\Models\Plan::getName($workplace->next_plan_id)}}に変更予定</small></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="">
                                    <div class="row">
                                        <div class="col-xs-3">有効期限</div>
                                        <div class="col-xs-5">{{$workplace->expiration_date}}</div>
                                    </div>
                                </div>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('plan.modal.edit_plan')
@stop