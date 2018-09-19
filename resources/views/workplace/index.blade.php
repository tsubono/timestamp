@extends('basic')

@include('elements.toast')

@section('title')
    <title>勤務場所 | TIMESTAMP</title>
@stop

@section('header')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-xs-9">
            <h2>
                <small>{{ $workplace->formal_name }}</small>
            </h2>
            <div class="alert-danger">{{$error_msg or ''}}</div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        @if (!empty($message))
            <div class="alert alert-success">{{$message}}</div>
        @endif
        @if (!empty($err_message))
            <div class="alert alert-danger">{{$err_message}}</div>
        @endif
        <div class="col-sm-12 col-md-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>基本情報</h5>
                    <div class="ibox-tools">
                        <a data-toggle="modal" data-target="#infoEditModal"><i class="fa fa-edit"></i> 編集</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-xs-4">正式名称</div>
                        <div class="col-xs-8">{{ $workplace->formal_name }}</div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-4">住所</div>
                        <div class="col-xs-8">{{ $workplace->zip_code }}<br>{{ $workplace->pref }}
                            <br>{{ $workplace->address. " ".$workplace->building }}</div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-4">電話番号</div>
                        <div class="col-xs-8">{{ $workplace->tel }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>詳細設定</h5>
                    <div class="ibox-tools">
                        <a data-toggle="modal" data-target="#timeEditModal"><i class="fa fa-edit"></i> 編集</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-xs-4">日付変更</div>
                        <div class="col-xs-8">毎日 {{ $workplace->timing_of_tomorrow }}</div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-4">打刻時刻丸め</div>
                        <div class="col-xs-8">
                            <p>出勤・退勤:
                                {{ $workplace->round_minute_attendance }}分単位
                            </p>
                            <p>休憩・復帰:
                                {{ $workplace->round_minute_break }}分単位
                            </p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-4">給与計算方法</div>
                        <div class="col-xs-8">{{ $workplace->payroll_role=="1"?"切り捨て":"四捨五入" }}</div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-4"></div>
                        <div class="col-xs-8"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>その他の情報</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-striped">
                        <div class="">
                            <div class="row">
                                <div class="col-xs-4">有効期限日</div>
                                <div class="col-xs-8">{{ \Carbon\Carbon::parse($workplace->expiration_date)->format('Y-m-d') }}</div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-xs-4">次回決済日</div>
                                <div class="col-xs-8">{{ \Carbon\Carbon::parse($workplace->next_charge_date)->format(('Y-m-d')) }}</div>
                            </div>
                            <br>
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('workplace.modal.edit_workplace')
    @include('workplace.modal.edit_detail')

    <script>
        window.onload = function () {
            //時刻ピッカー
            //$('#time').timepicker({'timeFormat': 'H:i'});

            $('#time').datetimepicker({
                format: 'H:i',
                lang: 'ja',
                datepicker:false
            });

            //郵便番号から住所を自動入力
            $('.zipcode1').change(function () {
                AjaxZip3.zip2addr('zip_1', 'zip_2', 'pref', 'address');
            });
            $('.zipcode2').change(function () {
                AjaxZip3.zip2addr('zip_1', 'zip_2', 'pref', 'address');
            });
        }

    </script>
@stop