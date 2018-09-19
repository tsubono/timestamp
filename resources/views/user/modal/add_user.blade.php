<div class="modal inmodal" id="userCreateModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/user/add_user" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="id" type="hidden" value="0">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">ユーザーの追加</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        {{--<div class="form-group">--}}
                        {{--<label class="font-normal col-xs-12 col-sm-2 control-label">名前</label>--}}

                        {{--<div class="col-xs-10 col-sm-6">--}}
                        {{--<input type="text" name="name" value="{{ old("name") }}" class="form-control" placeholder="名前">--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">ユーザーID</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="login_id" value="{{ old("login_id") }}" class="form-control"
                                       placeholder="ユーザーID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">メールアドレス</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="email" value="{{ old("email") }}" class="form-control"
                                       placeholder="メールアドレス">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">パスワード</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="password" name="password" value="" class="form-control" placeholder="パスワード"
                                       >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">パスワード（確認用）</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="password" name="password_confirmation" value="" class="form-control"
                                       placeholder="パスワード（確認用）">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">有効フラグ</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="checkbox" name="enable_flg" value="1"
                                       class="form-control" {{ old("enable_flg")=="1"?"checked":"" }}>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">登録</button>
                </div>

            </div>
        </div>
    </form>
</div>
