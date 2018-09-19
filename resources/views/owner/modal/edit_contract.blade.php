<div class="modal inmodal" id="contractInfoEditModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/owner/edit_contract" method="post" accept-charset="UTF-8" class="form-horizontal" data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="id" type="hidden" value="{{ $contract->id  }}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">契約情報の編集</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>

                    <div class="row">

                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">会社名</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="company_name" value="{{ old('company_name', $contract->company_name) }}"
                                       class="form-control" placeholder="会社名" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">会社名カナ</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="company_name_kana" value="{{ old('company_name_kana', $contract->company_name_kana) }}"
                                       class="form-control" placeholder="会社名カナ" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">担当者名</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="person_name" value="{{ old('person_name', $contract->person_name) }}"
                                       class="form-control" placeholder="担当者名" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">担当者名カナ</label>

                            <div class="col-xs-10 col-sm-6">
                                <input type="text" name="person_name_kana" value="{{ old('person_name_kana', $contract->person_name_kana) }}"
                                       class="form-control" placeholder="担当者名カナ" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">電話番号</label>

                            <div class="col-xs-12 col-sm-5">
                                <input type="text" name="tel" value="{{ old('tel', $contract->tel) }}" class="form-control" placeholder="電話番号">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">住所</label>

                            <div class="form-group m-t-xs">
                                <?php /**/ $zip_1 = $contract->zipcode != "" ? explode('-', $contract->zipcode)[0] : "" /**/ ?>
                                <?php /**/ $zip_2 = $contract->zipcode != "" ? explode('-', $contract->zipcode)[1] : "" /**/ ?>

                                <input type="text" name="zip_1" class="form-control zipcode col-sm-4"
                                       placeholder="郵便番号(3桁)" value="{{ old('zip_1', $zip_1) }}"
                                       style="width: 110px;">
                                <span class="col-sm-1">-</span>
                                <input type="text" name="zip_2" class="form-control zipcode col-sm-5"
                                       placeholder="郵便番号(4桁)" value="{{ old('zip_2', $zip_2) }}"
                                       style="width: 125px;">
                            </div>

                            <div class="col-xs-12 col-sm-5 col-sm-offset-2 m-t-xs">
                                <input type="text" name="pref" value="{{ old('pref', $contract->pref) }}"
                                       class="form-control" placeholder="都道府県" maxlength="30">
                            </div>
                            <div class="col-xs-12 col-sm-10 col-sm-offset-2 m-t-xs">
                                <input type="text" name="address" value="{{ old('address', $contract->address) }}"
                                       class="form-control" placeholder="住所(市区町村・番地)" maxlength="30">
                            </div>
                            <div class="col-xs-12 col-sm-10 col-sm-offset-2 m-t-xs">
                                <input type="text" name="building" value="{{ old('building', $contract->building) }}"
                                       class="form-control" placeholder="その他(建物名)" maxlength="30">
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
