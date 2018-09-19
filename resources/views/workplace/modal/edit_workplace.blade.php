<div class="modal inmodal" id="infoEditModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/workplace/edit_workplace" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="timing_of_tomorrow" type="hidden" value="{{ $workplace->timing_of_tomorrow }}">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">基本情報の編集</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">略称</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="name" value="{{ old("name", $workplace->name) }}"
                                       class="form-control" placeholder="略称" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">正式名称</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="formal_name"
                                       value="{{ old("formal_name", $workplace->formal_name) }}" class="form-control"
                                       placeholder="正式名称" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">住所</label>

                            <div class="form-group m-t-xs">
                                <?php /**/ $zip_1 = $workplace->zip_code != "" ? explode('-', $workplace->zip_code)[0] : "" /**/ ?>
                                <?php /**/ $zip_2 = $workplace->zip_code != "" ? explode('-', $workplace->zip_code)[1] : "" /**/ ?>

                                <input type="text" name="zip_1" class="form-control zipcode1 col-sm-4"
                                       placeholder="郵便番号(3桁)" value="{{ old('zip_1', $zip_1) }}"
                                       style="width: 110px;">
                                <span class="col-sm-1">-</span>
                                <input type="text" name="zip_2" class="form-control zipcode2 col-sm-5"
                                       placeholder="郵便番号(4桁)"  value="{{ old('zip_2', $zip_2) }}"
                                       style="width: 125px;">
                            </div>
                            {{--<div class="col-xs-12 col-sm-4">--}}
                            {{--<input type="text" name="postcode" value="{{ $workplace->zip_code }}" class="form-control" placeholder="郵便番号">--}}
                            {{--</div>--}}
                            <div class="col-xs-12 col-sm-5 col-sm-offset-2 m-t-xs">
                                <input type="text" name="pref" value="{{ old("pref", $workplace->pref) }}"
                                       class="form-control" placeholder="都道府県" maxlength="30">
                            </div>
                            <div class="col-xs-12 col-sm-10 col-sm-offset-2 m-t-xs">
                                <input type="text" name="address" value="{{ old("address", $workplace->address) }}"
                                       class="form-control" placeholder="住所(市区町村・番地)" maxlength="30">
                            </div>
                            <div class="col-xs-12 col-sm-10 col-sm-offset-2 m-t-xs">
                                <input type="text" name="building" value="{{ old("building", $workplace->building) }}"
                                       class="form-control" placeholder="その他(ビル名)" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">電話番号</label>

                            <div class="col-xs-12 col-sm-5">
                                <input type="text" name="tel" value="{{ old("tel", $workplace->tel) }}"
                                       class="form-control" placeholder="電話番号">
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
