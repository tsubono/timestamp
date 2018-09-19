<div class="modal inmodal" id="accountEditModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/owner/edit_account" method="post" accept-charset="UTF-8" data-remote="data-remote"
          class="form-horizontal">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        @if (isset($contract_flg))
            <input name="contract_flg" type="hidden" value="{{$contract_flg}}">
        @endif

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">アカウント情報の編集</h4>
                </div>

                <div class="modal-body" style="padding: 0 40px 0 40px;">
                    <br>
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <form>
                        <div class="form-group">
                            <label for="recipient-name" class="form-control-label">メールアドレス:</label>
                            <input type="text" name="email" class="form-control" placeholder="メールアドレス" required
                                   value="{{ old('email', $user->email) }}">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="form-control-label">ユーザーID:</label>
                            <input type="text" name="login_id" class="form-control" placeholder="略称" required
                                   value="{{ old('login_id', \Auth::user()->login_id) }}">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="form-control-label">パスワード:</label>
                            <input type="password" name="password" class="form-control" placeholder="パスワード" required>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="form-control-label">パスワード(確認用):</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="パスワード(確認用)" required>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>

            </div>
        </div>
    </form>
</div>