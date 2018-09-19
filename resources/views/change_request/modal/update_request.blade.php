<div class="modal inmodal" id="changeRequestModal_{{$idx}}" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">

    <form action="/change_request/update" method="post" accept-charset="UTF-8" class="form-horizontal"
          data-remote="data-remote" id="form_{{$idx}}">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="id" type="hidden" value="{{ $change_request->id }}">
        <input name="timecard_id" type="hidden" value="{{ $change_request->timecard_id }}">
        <input type="hidden" name="employee_uid" value="{{$change_request->employee->uid}}">
        <input type="hidden" name="status">

        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">閉じる</span></button>
                    <h4 class="modal-title">変更依頼詳細</h4>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>
                    <div class="row">
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">対象日付</label>

                            <div class="col-xs-10 col-sm-6">
                                {{$change_request->date}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-normal col-xs-12 col-sm-2 control-label">従業員名</label>

                            <div class="col-xs-10 col-sm-6">
                                {{$change_request->employee->name}}
                            </div>
                        </div>

                        <table class="table">
                            @forelse($change_request->details as $i => $detail)
                                <tr>
                                    <td>
                                        <select name="details[{{$i}}][type]" class="form-control" readonly>
                                            @foreach($controls as $control)
                                                @if ($control["id"]==$detail["type"])
                                                    <option value="{{$control["id"]}}" selected>{{$control["label"]}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="details[{{$i}}][time]" class="form-control" value="{{$detail["time"]}}" readonly/>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </table>
                    </div>
                </div>

                    @if (empty($change_request->status))
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary approveBtn" data-idx="{{$idx}}">承認</button>
                            <button type="button" class="btn btn-danger unApproveBtn" data-idx="{{$idx}}">否認</button>
                        </div>
                    @else
                        <div class="modal-footer" style="text-align: center;color:red;">
                            <span><strong>{{$change_request->status=="1"?"承認済み":"否認済み"}}</strong></span>
                        </div>
                    @endif

            </div>
        </div>
    </form>
</div>
