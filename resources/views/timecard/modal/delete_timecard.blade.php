<div class="modal inmodal" id="timecardDeleteModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <form action="/timecard/delete_timecard" method="post" accept-charset="UTF-8" class="form-horizontal" data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input type="hidden" name="year" value="{{\Carbon\Carbon::parse($timecard->date)->format('Y')}}">
        <input type="hidden" name="month" value="{{\Carbon\Carbon::parse($timecard->date)->format('m')}}">
        <input type="hidden" name="id" value="{{$timecard->id}}">
        <input type="hidden" name="employee_flg" value="{{$employee_flg}}">
        <input type="hidden" name="employee_uid" value="{{$employee->uid??""}}">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">
                <div class="modal-header">
                    <h4 class="modal-title">タイムカードの削除</h4>
                </div>
                <div class="modal-body">
                    表示中のタイムカードを削除します。
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" ng-click="submit()">タイムカード削除</button>
                </div>
            </div>
        </div>
    </form>
</div>