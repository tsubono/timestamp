@section('nav')
    <nav class="navbar-default navbar-static-side" role="navigation" style="height: 100%;">
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
            <span>
              <a href="/">
                <span class="clear"><span class="block m-t-xs">
                    <strong class="font-bold">TIMESTAMP</strong><br>
                    <span>{{$workplace->name ?? ''}}</span>
                  </span></span>
              </a>
            </span>
                    </div>
                    <div class="logo-element">
                        TS
                    </div>
                </li>
                @if (\Carbon\Carbon::parse($workplace->expiration_date)->addDays('1')->isPast())
                    <li>
                        <a href="/payment">
                            <i class="fa fa-money"></i>
                            <span class="nav-label">支払い情報</span>
                        </a>
                    </li>
                @else
                    <li>
                        <a href="/workplace">
                            <i class="fa fa-building"></i>
                            <span class="nav-label">勤務場所</span>
                        </a>
                    </li>
                    <li>
                        <a href="/employee">
                            <i class="fa fa-users"></i>
                            <span class="nav-label">従業員</span>
                        </a>
                    </li>

                    <li>
                        <a href="/timecard">
                            <i class="fa fa-clock-o"></i>
                            <span class="nav-label">タイムカード</span>
                        </a>
                    </li>
                    <li>
                        <a href="/user">
                            <i class="fa fa-key"></i>
                            <span class="nav-label">ユーザー</span>
                        </a>
                    </li>
                    <li>
                        <a href="/recorder">
                            <i class="fa fa-list"></i>
                            <span class="nav-label">レコーダー</span>
                        </a>
                    </li>
                    <li>
                        <a href="/payment">
                            <i class="fa fa-money"></i>
                            <span class="nav-label">支払い情報</span>
                        </a>
                    </li>
                    <li>
                        <a href="/plan">
                            <i class="fa fa-cog"></i>
                            <span class="nav-label">契約プラン</span>
                        </a>
                    </li>
                    <li>
                        <a href="/working_report">
                            <i class="fa fa-cloud-download "></i>
                            <span class="nav-label">出勤簿出力</span>
                        </a>
                    </li>
                    <li>
                        <a href="/payment_report">
                            <i class="fa fa-cloud-download "></i>
                            <span class="nav-label">給与明細出力</span>
                        </a>
                    </li>
                    <li>
                        <a href="/change_request">
                            <i class="fa fa-check"></i>
                            <span class="nav-label">変更依頼一覧</span>
                        </a>
                    </li>
                 @endif
            </ul>

        </div>
    </nav>
    <br>
    <br>
@endsection