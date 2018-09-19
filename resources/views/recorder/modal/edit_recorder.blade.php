<div class="modal inmodal" id="recorderEditModal_{{$idx}}" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <form action="/recorder/edit_recorder" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="uid" type="hidden" value="{{ $recorder->uid }}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">レコーダーの編集</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">名前</label>

                            <div class="col-xs-12 col-sm-10">
                                <input type="text" name="name" value="{{old("name", $recorder->name)}}"
                                       class="form-control" placeholder="" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">パスコード</label>

                            <div class="col-xs-12 col-sm-5">
                                <input type="text" name="pass_code" class="form-control"
                                       value="{{old("pass_code", $recorder->pass_code)}}" placeholder="半角英数字 4-12文字"
                                       maxlength="12">
                            </div>
                        </div>
                        <span style="color: red;"><small>※パスコードを変更すると、レコーダー画面で再ログインが必要になります。</small></span>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>

            </div>
        </div>
    </form>
</div>