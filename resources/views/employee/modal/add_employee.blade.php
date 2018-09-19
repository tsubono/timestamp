<div class="modal inmodal" id="employeeCreateModal" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <form action="/employee/add_employee" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">従業員の追加</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">名前</label>

                            <div class="col-xs-6 col-sm-4">
                                <input type="text" name="lname" class="form-control" placeholder="姓"
                                       value="{{old("lname")}}" maxlength="15">
                            </div>
                            <div class="col-xs-6 col-sm-4">
                                <input type="text" name="fname" class="form-control" placeholder="名"
                                       value="{{old("fname")}}" maxlength="15">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">名前(カナ)</label>

                            <div class="col-xs-6 col-sm-4">
                                <input type="text" name="lname_kana" class="form-control" placeholder="姓(カナ)"
                                       value="{{old("lname_kana")}}" maxlength="15">
                            </div>
                            <div class="col-xs-6 col-sm-4">
                                <input type="text" name="fname_kana" class="form-control" placeholder="名(カナ)"
                                       value="{{old("fname_kana")}}" maxlength="15">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">性別</label>

                            <div class="col-xs-12 col-sm-5">
                                <select name="gender" class="form-control">
                                    <option value="male">男性</option>
                                    <option value="female">女性</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">生年月日</label>
                            <div class="col-xs-12 col-sm-5">
                                <input type="text" name="birthday" class="form-control date"
                                       placeholder="生年月日(例：{{\Carbon\Carbon::now()->format('Y-m-d')}})"
                                       value="{{old("birthday", NULL)}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">交通費(往復/1日)</label>
                            <div class="col-xs-12 col-sm-5">
                                <input type="number" name="traffic_cost" class="form-control" placeholder="交通費(往復/1日)"
                                       value="{{old("traffic_cost")}}" maxlength="10">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">入社日</label>
                            <div class="col-xs-12 col-sm-5">
                                <input type="text" name="joined_date" class="form-control date"
                                       placeholder="入社日(例：{{\Carbon\Carbon::now()->format('Y-m-d')}})"
                                       value="{{old("joined_date", NULL)}}">
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
