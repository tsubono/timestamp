<div class="modal inmodal" id="timecardDeleteModal_{{$date}}" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/employee/delete_salary" method="post" accept-charset="UTF-8" class="form-horizontal" data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="apply_date" type="hidden" value="{{ $date }}">
        <input name="uid" type="hidden" value="{{ $employee->uid }}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">
                <div class="modal-header">
                    <h4 class="modal-title">時給設定の削除</h4>
                </div>
                <div class="modal-body">
                    <p>本当に時給設定を削除しますか?</p>
                    <p class="text-danger">注意) 過去の時給設定を削除した場合、再出力した給与帳票が、出力済の内容と大きく変わる可能性があります。</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">時給設定の削除</button>
                </div>
            </div>
        </div>
    </form>
</div>
