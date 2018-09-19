<div class="modal inmodal" id="planEditModal" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <form action="/plan/edit_plan" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="old_plan" type="hidden" value="{{ $workplace->plan_id }}">
        <input name="plan" type="hidden" value="">
        <input name="old_plan_rank" type="hidden" value="{{ $workplace->plan->rank }}">
        <input name="plan_rank" type="hidden" value="">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div id="form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                        <h4 class="modal-title">契約プランの変更</h4>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                        <div class="row">
                            @if (!empty($workplace->payment_customer_id) && !empty($workplace->payment_card_id))
                                <div class="form-group" style="margin-left: 6%;">
                                    @foreach($plans as $plan)
                                        @if ($plan->id != $workplace->plan_id)
                                            @if ($employee_count <= $plan->employee_limit)
                                                <div class="text-center col-lg-5 col-md-5 col-sm-10 col-xs-10 plans tile"
                                                     data-rank="{{$plan->rank}}" data-value="{{$plan->id}}" data-detail="{{$workplace->plan->getDetail($plan->id)}}">
                                                    <div style="text-align:center;padding:90px 0 90px 0;">
                                                        <p class="plan_name">{{$plan->name}}</p>
                                                        <br>
                                                        <p class="">
                                                            従業員数上限 :
                                                            {{$plan->employee_limit}}人
                                                        </p>
                                                        <p class="">
                                                            月額 :
                                                            {{$plan->monthly_price}}円
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                         @endif
                                    @endforeach

                                </div>

                            @else
                                <div style="text-align: center">
                                    <p style="color: red;">支払い情報を設定してください</p>
                                    <a href="/payment" class="btn btn-primary">支払い情報</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        @if (!empty($workplace->payment_customer_id) && !empty($workplace->payment_card_id))
                            <p style="text-align: center;">
                                <small>※プランをアップグレードする場合は、本日からの適用となり、<br>更新ボタン押下時に日割り差額と翌月分の決済を行います。</small>
                            </p>
                            <p style="text-align: center;">
                                <small>※プランをダウングレードする場合は、次月からの適用となります。</small>
                            </p>
                            <button type="button" class="btn btn-primary nextBtn">次へ</button>
                        @endif
                    </div>
                </div>
                <div id="confirm" style="display: none;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                        <h4 class="modal-title">契約プランの変更の確認</h4>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                        <div class="row up" style="display: none;">
                            <p>現在プラン：{{$workplace->plan->getDetail($workplace->plan_id)}}</p>
                            <p>変更後プラン：<span class="new_plan"></span></p>
                            <p style="color:red;">※{{\Carbon\Carbon::now()->startOfMonth()->format('Y年m')}}
                                月分の差額と{{\Carbon\Carbon::now()->startOfMonth()->addMonths('1')->format('Y年m')}}月分を合わせた
                                <span id="amount"></span>を即時決済します。</p>
                            <p style="color:red;">※ 変更後プランは本日より適用されます。</p>
                        </div>
                        <div class="row down" style="display: none;">
                            <p>現在プラン：{{$workplace->plan->getDetail($workplace->plan_id)}}</p>
                            <p>変更後プラン：<span class="new_plan"></span></p>
                            <p style="color:red;">※ 変更後プランの金額を{{Carbon\Carbon::parse($workplace->next_charge_date)->format('Y年m月d日')}}
                                に決済します。</p>
                            <p style="color:red;">※ 変更後プランは次月からの適用となります。</p>
                        </div>

                        <div class="modal-footer">
                            @if (!empty($workplace->payment_customer_id) && !empty($workplace->payment_card_id))
                                <button type="submit" class="btn btn-primary">更新</button>
                            @endif
                            <button type="button" class="btn btn-default beforeBtn">戻る</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    window.onload = function () {
        $('.nextBtn')[0].disabled = true;

        $('.nextBtn').click(function () {
            $('#form').css('display', 'none');
            $('#confirm').fadeIn();
            //ダウングレード
            if ($('[name=plan_rank]').val() < $('[name=old_plan_rank]').val()) {
                $('.row.down .new_plan').text($('.activePlan').data('detail'));
                $('.row.up').css('display', 'none');
                $('.row.down').fadeIn();
                //アップグレード
            } else if ($('[name=plan_rank]').val() > $('[name=old_plan_rank]').val()) {

                $.ajax({
                    type: "POST",
                    url: "/plan/get_amount",
                    data: {
                        "plan_id": $('[name=plan]').val(),
                        "old_plan_id": $('[name=old_plan]').val(),
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        var amount = JSON.parse(data);
                        $('#amount').text(amount + "円");
                        $('.row.up .new_plan').text($('.activePlan').data('detail'));
                        $('.row.down').css('display', 'none');
                        $('.row.up').fadeIn();
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        alert('Error : ' + errorThrown);
                    }
                });
            }
        });
        $('.beforeBtn').click(function () {
            $('#confirm').css('display', 'none');
            $('#form').fadeIn();
        });

        //plan選択時
        $('.plans').click (function() {
            if ($('.activePlan').length!=0) {
                $('.activePlan').removeClass('activePlan');
            }
            $(this).addClass('activePlan');
            $('[name=plan]').val($(this).data('value'));
            $('[name=plan_rank]').val($(this).data('rank'));
            $('.nextBtn')[0].disabled = false;
        });

        $('.openBtn').click (function() {
            $('#confirm').css('display', 'none');
            $('#form').fadeIn();

            if ($('.activePlan').length!=0) {
                $('.activePlan').removeClass('activePlan');
            }
            $('.nextBtn')[0].disabled = true;
        });

    }

</script>
<style>
    .tile {
        height: 250px;
        margin-left: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        background-color: #f2dede;
        color: black;
    }

    .tile:hover {
        background-color: #1f648b;
        color: #FFF;
    }

    .tile::before,
    .tile::after {
        position: absolute;
        z-index: -1;
        display: block;
        content: '';
    }

    .tile,
    .tile::before,
    .tile::after {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        -webkit-transition: all .3s;
        transition: all .3s;
    }

    .tile.activePlan {
        background-color: #1f648b;
        color: #FFF;
        border: 1px dashed #fff;
        border-radius: 8px;
        box-shadow: 0 0 0 4px #fff;
    }

    .plan_name {
        font-weight: bold;
        font-size: 1.3em;
    }
</style>