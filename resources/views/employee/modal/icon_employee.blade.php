<div class="modal inmodal" id="iconEditModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/employee/edit_icon" method="post" accept-charset="UTF-8" data-remote="data-remote"
          class="form-horizontal" enctype="multipart/form-data">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="uid" type="hidden" value="{{ !empty($employee)?$employee->uid:0 }}">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">アイコン情報の編集</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        <div class="form-group" style="margin-left: 10px;">
                            <label class="radio-inline"><input type="radio" name="icon_type" class="" required=""
                                                               value="icon_file" checked>画像をアップロード</label>
                            <label class="radio-inline"><input type="radio" name="icon_type" class="" required=""
                                                               value="icon">アイコンを選択</label>
                        </div>
                        <input type="file" name="icon_file" class="icon_input" id="icon_file" accept='image/*'/>
                        <div class="icon_input" id="icon">
                            @foreach($icon_list as $idx => $icon)
                                <div class="col-xs-3">
                                    <label>
                                        <input type="radio" name="icon" value="{{ $icon->name }}" {{$idx==0?"checked":""}}>
                                        <img src="{{ \App\Models\Icon::getPath($icon->name,'thumbnail') }}" alt="">
                                    </label>
                                </div>
                            @endforeach
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
<style>
    #icon {
        height: 335px;
        overflow-y: auto;
    }
</style>
