@section('navbar')
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0;">
            <div class="navbar-header" style="margin-left: 25px;">
                <br>
                <p><i class="fa fa-user" aria-hidden="true"> &nbsp;</i>{{\Auth::user()->login_id}}でログイン中</p>
            </div>
            <div class="collapse navbar-collapse navbar-right" style="margin-right: 10px;">
                <ul class="nav navbar-nav">
                    @if (\Auth::user()->owner_flg=="1")
                        <li>
                            <a href="/owner"><i class="fa fa-refresh"></i> 店舗選択</a>
                        </li>
                    @endif
                    @if (\Auth::user()->owner_flg=="1" && isset($contract_flg))
                        <li>
                            <a href="/owner/contract">
                                <i class="fa fa-cog"></i> 契約情報
                            </a>
                        </li>
                    @endif
                    @if (\Auth::user()->owner_flg=="1" && isset($account_flg))
                        <li>
                            <a data-toggle="modal" data-target="#accountEditModal">
                                <i class="fa fa-user"></i> アカウント編集
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="/logout"><i class="fa fa-sign-out"></i> ログアウト</a>
                    </li>
                </ul>
            </div>
        </nav>

    </div>
@endsection