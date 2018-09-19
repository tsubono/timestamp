@extends('basic_owner')

@include('elements.toast')

@section('title')
    <title>契約情報 | TIMESTAMP</title>
@stop

@section('content')
    <div class="text-center loginscreen animated fadeInDown">
        <div class="row">
            @if (!empty($message))
                <br>
                <div class="alert alert-success">{{$message}}</div>
            @endif
            @if (!empty($err_message))
                <br>
                <div class="alert alert-danger">{{$err_message}}</div>
            @endif
                <h3 style="font-size: 1.5em;">契約情報</h3>

                <div class="col-sm-12 col-md-14">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>基本情報</h5>
                        <div class="ibox-tools">
                            <a data-toggle="modal" data-target="#contractInfoEditModal"><i class="fa fa-edit"></i> 編集</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-xs-4">会社名</div>
                            <div class="col-xs-4" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ $contract->company_name }}</div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-4">会社名カナ</div>
                            <div class="col-xs-4" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ $contract->company_name_kana }}</div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-4">担当者名</div>
                            <div class="col-xs-4" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ $contract->person_name }}</div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-4">担当者名カナ</div>
                            <div class="col-xs-4" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{ $contract->person_name_kana }}</div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-4">電話番号</div>
                            <div class="col-xs-4">{{ $contract->tel }}</div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-4">住所</div>
                            <div class="col-xs-4" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                                {{ $contract->zipcode }}<br>
                                {{ $contract->pref }}{{ $contract->address }}<br>
                                {{ $contract->building }}
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-14">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>メール情報</h5>
                        <div class="ibox-tools">
                            <a data-toggle="modal" data-target="#contractMailEditModal"><i class="fa fa-edit"></i> 編集</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-xs-4">メールアドレス</div>
                            <div class="col-xs-4">{{ $contract->email }}</div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-4">お知らせメール</div>
                            <div class="col-xs-4">{{ $contract->receive_mail_flg?"受信":"受信不可" }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-14">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>問い合わせ</h5>
                    </div>
                    <div class="ibox-content">
                        <p>ご契約などのお問い合わせは下記メールアドレスにてお受けしております。</p>
                        <div class="row">
                            <div class="col-xs-4">メールアドレス</div>
                            <div class="col-xs-4">toiawase@example.co.jp</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include("owner.modal.edit_contract")
    @include("owner.modal.edit_contract_mail")
    @include("owner.modal.edit_account",["contract_flg"=>true, "user"=>$user])

    <script>
        window.onload = function () {
            //郵便番号から住所を自動入力
            $('.zipcode').change(function () {
                AjaxZip3.zip2addr('zip_1', 'zip_2', 'pref', 'address');
            });
        }

    </script>
    <style>
        body {
            background: #FFF;
        }
    </style>

@endsection
