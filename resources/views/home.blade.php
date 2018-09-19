@extends('basic')

@include('elements.toast')

@section('title')
    <title>ホーム | TIMESTAMP</title>
@stop

@section('content')
    <div class="text-center loginscreen animated fadeInDown">
        @if (\Carbon\Carbon::parse($workplace->expiration_date)->addDays('1')->isPast())
            <div class="row">
                <div class="col-xs-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>支払い情報</h5>
                        </div>
                        <div class="ibox-content">
                            <p style="color:red;">{{\Carbon\Carbon::parse($workplace->next_charge_date)->startOfMonth()->addMonths('1')->format('Y年m月')}}分の支払い確認がとれておりません。</p>
                            <p style="color:red;">下記リンクより支払い情報を更新してください。</p>
                            <a href="/payment" class="btn btn-primary">支払い情報を更新する</a>
                        </div>
                    </div>
                </div>
            </div>
        @elseif (empty($workplace->payment_customer_id) || empty($workplace->payment_card_id))
            <div class="row">
                <div class="col-xs-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>支払い情報</h5>
                        </div>
                        <div class="ibox-content">
                            <p style="color:red;">{{$workplace->next_charge_date}}までに支払い情報を登録してください。</p>
                            <a href="/payment" class="btn btn-primary">支払い情報を登録する</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
