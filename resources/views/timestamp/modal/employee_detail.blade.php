<div class="modal inmodal" id="employeeDetailModal" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    <div class="controlHeader container-fluid">
        <p class="text-right">
            {{--<span ng-click="close()">閉じる</span>--}}
        </p>
        <ul>
            <li ng-show="control.clocking_in">
                <form action="" method="post" accept-charset="UTF-8">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <input name="control_id" type="hidden" value="0">
                    <input name="timecard_id" type="hidden" value="@{{ timecard_id }}">
                    <button type="submit"><img src="/dakoku/images/btn_01.png" alt="出勤" class="wink"></button>
                </form>
            </li>
            <li ng-show="control.break_in">
                <form action="" method="post" accept-charset="UTF-8">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <input name="control_id" type="hidden" value="1">
                    <input name="timecard_id" type="hidden" value="@{{ timecard_id }}">
                    <button type="submit"><img src="/dakoku/images/btn_02.png" alt="休憩" class="wink"></button>
                </form>
            </li>
            <li ng-show="control.break_out">
                <form action="" method="post" accept-charset="UTF-8">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <input name="control_id" type="hidden" value="2">
                    <input name="timecard_id" type="hidden" value="@{{ timecard_id }}">
                    <button type="submit"><img src="/dakoku/images/btn_03.png" alt="復帰" class="wink"></button>
                </form>
            </li>
            <li ng-show="control.clocking_out">
                <form action="" method="post" accept-charset="UTF-8">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <input name="control_id" type="hidden" value="3">
                    <input name="timecard_id" type="hidden" value="@{{ timecard_id }}">
                    <button type="submit"><img src="/dakoku/images/btn_04.png" alt="退勤" class="wink"></button>
                </form>
            </li>
        </ul>
    </div>

    <form action="/recorder/edit_recorder" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="uid" type="hidden" value="{{ $recorder->uid }}">

    </form>
</div>