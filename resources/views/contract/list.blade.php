@extends('basic_notuser')

@section('content')
    <div class="text-center loginscreen animated fadeInDown" style="height: auto;margin-top: 0px;position:relative;">
        <div>
            <h1 style="font-size: 1.5em;">契約一覧</h1>
            <table class="table" style="background: #FFF;">
                <thead style="background: black;color:#FFF;">
                    <tr>
                        <th>id</th>
                        <th>ドメイン名</th>
                        <th>メールアドレス</th>
                        <th>状態</th>
                        <th>会社名</th>
                        {{--<th>会社名カナ</th>--}}
                        <th>担当者名</th>
                        {{--<th>担当者名カナ</th>--}}
                        <th>電話番号</th>
                        <th>郵便番号</th>
                        <th>都道府県</th>
                        <th>市区町村・番地</th>
                        <th>建物名</th>
                        {{--<th>メール受信可否</th>--}}
                        <th>更新日</th>
                    </tr>
                </thead>
            @foreach($contracts as $contract)
                <tr>
                    <td>{{$contract->id}}</td>
                    <td>{{$contract->domain_name}}</td>
                    <td>{{$contract->email}}</td>
                    <td {{!$contract->confirmation_flg?"style=color:red;":""}}>{{$contract->confirmation_flg?'登録完了':'仮登録'}}</td>
                    <td>{{$contract->company_name}}</td>
                    {{--<td>{{$contract->company_name_kana}}</td>--}}
                    <td>{{$contract->person_name}}</td>
                    {{--<td>{{$contract->person_name_kana}}</td>--}}
                    <td>{{$contract->tel}}</td>
                    <td>{{$contract->zipcode}}</td>
                    <td>{{$contract->pref}}</td>
                    <td>{{$contract->address}}</td>
                    <td>{{$contract->building}}</td>
                    {{--<td>{{$contract->receive_mail_flg?'受信可能':'受信拒否'}}</td>--}}
                    <td>{{$contract->updated_at}}</td>
                </tr>


            @endforeach
            </table>
        </div>
    </div>

@endsection
