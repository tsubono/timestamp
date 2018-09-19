<div class="modal inmodal" id="contractMailEditModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/owner/edit_contract_mail" method="post" accept-charset="UTF-8" class="form-horizontal" data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="id" type="hidden" value="{{ $contract->id }}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">メール情報の編集</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-4 control-label">メールアドレス</label>

                            <div class="col-xs-12 col-sm-6">
                                <input type="text" name="email" value="{{ old('email', $contract->email) }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-4 control-label">お知らせメール</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="checkbox" name="receive_mail_flg" id="receive_mail_flg" value="1" {{old('receive_mail_flg', $contract->receive_mail_flg) =='1'?'checked':''}}>
                                <label id="receive_mail_flg">受信する</label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">閉じる</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>

            </div>
        </div>
    </form>
</div>