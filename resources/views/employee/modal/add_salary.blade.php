<div class="modal inmodal" id="salaryAddModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;"
     xmlns="http://www.w3.org/1999/html">
    <form action="/employee/add_salary" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="uid" type="hidden" value="{{$employee->uid}}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">給与設定追加</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>

                    <p style="margin:1em 0;">
                        <a class="btn btn-default btn-xs add_salary_form">フォーム追加</a>
                    </p>

                    <table class="table">
                        <div class="col-sm-6">
                            <input type="text" class="form-control date" name="apply_date"
                                   placeholder="時給変更日付 (YYYY-MM-DD)" required/>
                        </div>
                    </table>

                    <table class="table">
                        <tr class="form-block" id="form_block[0]">
                            <td>
                                <input type="text" class="form-control" name="start_time[0]"
                                       value="00:00(デフォルト)"
                                       readonly/>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="hourly_pay[0]"
                                       placeholder="時給(円) 1000" required maxlength="10"/>
                            </td>
                        </tr>

                        <tr class="form-block" id="form_block[1]">
                            <td>
                                <input type="text" class="form-control start_time" name="start_time[1]"
                                       placeholder="変更時刻〜 (HH:MM)"/>
                            </td>
                            <td>
                                <input type="number" class="form-control hourly_pay" name="hourly_pay[1]"
                                       placeholder="時給(円) 1000" maxlength="10"/>
                            </td>
                            <td>
                                <a class="btn btn-default btn-sm delete_salary_form"><span
                                            class="glyphicon glyphicon-trash"></span></a>
                            </td>
                        </tr>

                    </table>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary add_salary_submit">追加</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
