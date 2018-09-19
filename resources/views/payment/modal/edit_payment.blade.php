<div class="modal inmodal" id="paymentEditModal" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <form action="/payment/edit_payment" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="payment_customer_id" type="hidden" value="{{ $workplace->payment_customer_id }}">
        <input name="payment_card_id" type="hidden" value="{{ $workplace->payment_card_id }}">
        <input name="uid" type="hidden" value="{{ $workplace->uid }}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">支払い情報の更新</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">名義</label>

                            <div class="col-xs-12 col-sm-10">
                                <input type="text" class="form-control" name="name" placeholder="名義"
                                   value="{{old("name")}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">カード番号</label>

                            <div class="col-xs-12 col-sm-10">
                                <input type="text" class="form-control" name="number" placeholder="カード番号"
                                   value="{{old("number")}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">有効期限</label>

                            <div class="col-xs-12 col-sm-10">
                                <input type="number" class="form-control col-sm-1" name="exp_month" placeholder="月"
                                       value="{{old("exp_month")}}" style="width: 110px;">
                                <input type="number" class="form-control col-sm-1" name="exp_year" placeholder="年"
                                       value="{{old("exp_year")}}" style="width: 110px;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">セキュリティコード</label>

                            <div class="col-xs-12 col-sm-10">
                                <input type="number" class="form-control" name="cvc" placeholder="***"
                                   value="{{old("cvc")}}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>

            </div>
        </div>
    </form>
</div>