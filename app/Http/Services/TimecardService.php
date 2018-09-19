<?php
namespace App\Http\Services;

use App\Models\Affiliation;
use App\Models\ChangeTimecard;
use App\Models\ChangeTimecardDetail;
use App\Models\Charge;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use App\Models\Workplace;
use Carbon\Carbon;
use DB;
use Log;
use Auth;
use Exception;
use Config;

/*
 * 打刻関連を扱うサービス
 */

class TimecardService
{

    /*
     * タイムカード追加
     * (出勤、退勤、休憩入り、休憩戻りのコントロールにより登録)
     */
    public static function save($timecard_id, $employee_uid, $time, $control_id, $workplace_uid = NULL)
    {
        try {

            DB::connection('customer-db')->transaction(function () use ($timecard_id, $employee_uid, $time, $control_id, $workplace_uid) {

                //Detail登録に必要なデータ作成
                $res = self::setDataByControl($control_id, $time);
                $start_time = $res['start_time'];
                $end_time = $res['end_time'];
                $type = $res['type'];

                //新規登録の場合
                if (empty($timecard_id)) {
                    //Timecard新規
                    //日付変更時刻によってTimecard用の日付変える
                    $date = self::getDateByTiming($time, $workplace_uid);

                    $timecard_id = Timecard::createRecord($date, $employee_uid, $workplace_uid);

                    //Detail新規
                    TimecardDetail::updateRecord($timecard_id, $start_time, $end_time, $type);

                    //Affiliationsの最新打刻日時更新
                    $affiliation = Affiliation::ofWorkplace($workplace_uid)->where('employee_uid', $employee_uid)->first();
                    $affiliation->current_clock_in = Carbon::now();
                    $affiliation->save();

                    //前にDetailレコードが存在する場合
                } else {
                    //日がまたがっている場合があるので1日ずつループして登録する
                    self::saveDetails($timecard_id, $start_time, $end_time, $employee_uid, $type, $control_id, $workplace_uid);
                }
            });
        } catch (Exception $e) {
            log::info($e);
            return false;
        }
        return true;
    }

    /*
     * ループしてもれなくDetailを登録していく
     */
    public static function saveDetails($id, $start_time, $end_time, $employee_uid, $type, $control_id, $workplace_uid = NULL)
    {

        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        //1つ前に更新されたDetail情報取得
        $before_detail = self::getBeforeDetail($control_id, $id);

        //始点(=前回のデータ) => $carbon_start_time
        //終点(=今回のデータ) => $carbon_end_time
        //出勤 or 休憩入り
        if (!$end_time) {

            //出勤
            if ($type == "1") {
                //前回は退勤
                $carbon_start_time = Carbon::parse($before_detail->end_time);
                //休憩入り
            } else {
                //前回が出勤
                if ($before_detail->type == "1") {
                    $carbon_start_time = Carbon::parse($before_detail->start_time);
                    //前回が休憩戻り
                } else {
                    $carbon_start_time = Carbon::parse($before_detail->end_time);
                }
            }
            //終点(=今回のデータ)
            $carbon_end_time = Carbon::parse($start_time);
            //今回のデータのdatetime
            $updated_date_time = $start_time;

            //start_timeがfalse(休憩戻りor退勤)
        } else {
            //退勤
            if ($type == "1") {
                //前回が出勤
                if ($before_detail->type == "1") {
                    $carbon_start_time = Carbon::parse($before_detail->start_time);
                    //前回が休憩戻り
                } else {
                    $carbon_start_time = Carbon::parse($before_detail->end_time);
                }
                //休憩戻り
            } else {
                //前回は休憩入り
                $carbon_start_time = Carbon::parse($before_detail->start_time);
            }
            //終点(=今回のデータ)
            $carbon_end_time = Carbon::parse($end_time);
            //今回のデータのdatetime
            $updated_date_time = $end_time;
        }

        $next_timecard_id = 0;
        $timecard_id = 0;

        $end_time_by_timing = self::getDateByTiming($carbon_end_time, $workplace_uid);


        //開始日時から終了日時まで1日ずつ加算して判定していく
        for ($start = $carbon_start_time;
             Carbon::parse(self::getDateByTiming($start, $workplace_uid))->format('Y-m-d') <= $carbon_end_time;
             $start = $start->modify('+1 day')) {

             $break_flg = false;

            $carbon_start_by_timing = Carbon::parse(self::getDateByTiming($start, $workplace_uid));
            $next_carbon_start_by_timing = $start->copy()->modify('+1 day');

            if ($carbon_start_by_timing->gt(Carbon::parse($end_time_by_timing))) {
                break;
            }

            //終了日まできたら、時間が開始日時のものになっているので元に戻す
            if ($carbon_start_by_timing->isSameDay(Carbon::parse($end_time_by_timing))) {
                $start = Carbon::parse($updated_date_time);
                //終了日が日付変更時刻ぴったりの場合
                if ($start->format('H:i') == Workplace::getTimingOfTomorrow($workplace_uid)) {
                    //退勤の場合はここでブレイクする(翌日出勤処理とかはしない)
                    $break_flg = true;
                }
                $carbon_start_by_timing = Carbon::parse(self::getDateByTiming($start, $workplace_uid));
            }

            $target_date_time = $start->format('Y-m-d H:i');

            //Timecard用に日付変更時間を加味した日時を取得
            $date = self::getDateByTiming($start->format('Y-m-d H:i'), $workplace_uid);

            //退勤の場合
            if ($type == "1" && $end_time && $break_flg) {
                //既存
                $before_date = Carbon::parse($date)->subDays('1');
                $timecard = Timecard::ofWorkplace($workplace_uid)
                    ->where('date', $before_date)->where('employee_uid', $employee_uid)->first();
                $timecard_id = $timecard->id;
            } else {
                $timecard = Timecard::ofWorkplace($workplace_uid)
                    ->where('date', $date)->where('employee_uid', $employee_uid)->first();
            }

            //出勤
            if ($type == "1" && $start_time) {
                if (empty($timecard)) {
                    //新規作成
                    $timecard_id = Timecard::createRecord($date, $employee_uid, $workplace_uid);
                } else {
                    $timecard_id = $timecard->id;
                }

                //Detail新規
                TimecardDetail::updateRecord($timecard_id, $target_date_time, false, $type);

                //次の日がまだある場合は当日を退勤まで締める必要
                if (!$carbon_start_by_timing->isSameDay(Carbon::parse($end_time_by_timing))) {
                    //Detail更新
                    TimecardDetail::updateRecord($timecard_id, false, self::getNextDateTime($target_date_time, true, $workplace_uid), $type);
                    //次の日出勤させとく
                    $next_date = Carbon::parse(self::getNextDateTime($target_date_time, false, $workplace_uid))->format('Y-m-d');
                    $next_timecard_id = Timecard::createRecord($next_date, $employee_uid, $workplace_uid);
                    TimecardDetail::updateRecord($next_timecard_id, self::getNextDateTime($target_date_time, false, $workplace_uid), false, $type);
                }

                //退勤
            } elseif ($type == "1" && $end_time) {

                if (empty($timecard)) {
                    $timecard_id = Timecard::createRecord($date, $employee_uid, $workplace_uid);
                } else {
                    $timecard_id = $timecard->id;
                }
                //次の日がまだある場合は当日を退勤まで締める必要
                if (!$break_flg && !$carbon_start_by_timing->isSameDay(Carbon::parse($end_time_by_timing))) {
                    if (Carbon::parse($updated_date_time)->format('H:i') != Workplace::getTimingOfTomorrow($workplace_uid)) {
                        //Detail更新
                        TimecardDetail::updateRecord($timecard_id, false, self::getNextDateTime($target_date_time, true, $workplace_uid), $type);
                        //次の日出勤させとく
                        $next_date = Carbon::parse(self::getNextDateTime($target_date_time, false, $workplace_uid))->format('Y-m-d');
                        $next_timecard_id = Timecard::createRecord($next_date, $employee_uid, $workplace_uid);

                        TimecardDetail::updateRecord($next_timecard_id, self::getNextDateTime($target_date_time, false, $workplace_uid), false, $type);
                    }
                    //そのまま退勤登録
                } else {
                    //Detail更新
                    TimecardDetail::updateRecord($timecard_id, false, $target_date_time, $type);
                }

                //休憩入り
            } elseif ($type == "2" && $start_time) {
                if (empty($timecard)) {
                    if ($start->format('H:i') == Workplace::getTimingOfTomorrow($workplace_uid)) {
                        //既存
                        $before_date = Carbon::parse($date)->subDays('1');
                        $timecard = Timecard::ofWorkplace($workplace_uid)
                            ->where('date', $before_date)->where('employee_uid', $employee_uid)->first();
                        $timecard_id = $timecard->id;
                    } else {
                        //新規作成
                        $timecard_id = Timecard::createRecord($date, $employee_uid, $workplace_uid);
                    }

                } else {
                    $timecard_id = $timecard->id;
                }

                //次の日がまだある場合は当日を出勤→退勤まで締める必要
                if (!$carbon_start_by_timing->isSameDay(Carbon::parse($end_time_by_timing))) {
                    //退勤
                    TimecardDetail::updateRecord($timecard_id, false, self::getNextDateTime($target_date_time, true, $workplace_uid), '1');
                    //次の日出勤させとく
                    $next_date = Carbon::parse(self::getNextDateTime($target_date_time, false, $workplace_uid))->format('Y-m-d');
                    $next_timecard_id = Timecard::createRecord($next_date, $employee_uid, $workplace_uid);
                    TimecardDetail::updateRecord($next_timecard_id, self::getNextDateTime($target_date_time, false, $workplace_uid), false, '1');
                } else {
                    //Detail新規
                    TimecardDetail::updateRecord($timecard_id, $target_date_time, false, $type);
                }

                //休憩戻り
            } elseif ($type == "2" && $end_time) {
                if (empty($timecard)) {
                    if ($start->format('H:i') == Workplace::getTimingOfTomorrow($workplace_uid)) {
                        //既存
                        $before_date = Carbon::parse($date)->subDays('1');
                        $timecard = Timecard::ofWorkplace($workplace_uid)
                            ->where('date', $before_date)->where('employee_uid', $employee_uid)->first();
                        $timecard_id = $timecard->id;
                    } else {
                        //新規作成
                        $timecard_id = Timecard::createRecord($date, $employee_uid, $workplace_uid);
                    }
                } else {
                    $timecard_id = $timecard->id;
                }

                //次の日がまだある場合は当日を出勤→休憩入り→休憩戻り→退勤まで締める必要
                if (!$carbon_start_by_timing->isSameDay(Carbon::parse($end_time_by_timing))) {

                    //休憩戻り
                    TimecardDetail::updateRecord($timecard_id, false, self::getNextDateTime($target_date_time, true, $workplace_uid), $type);
                    //退勤
                    TimecardDetail::updateRecord($timecard_id, false, self::getNextDateTime($target_date_time, true, $workplace_uid), '1');
                    //次の日出勤させとく
                    $next_date = Carbon::parse(self::getNextDateTime($target_date_time, false, $workplace_uid))->format('Y-m-d');
                    $next_timecard_id = Timecard::createRecord($next_date, $employee_uid, $workplace_uid);
                    TimecardDetail::updateRecord($next_timecard_id, self::getNextDateTime($target_date_time, false, $workplace_uid), false, '1');
                    //次の日休憩入りさせとく
                    TimecardDetail::updateRecord($next_timecard_id, self::getNextDateTime($target_date_time, false, $workplace_uid), false, '2');
                    //そのまま休憩戻り登録
                } else {
                    //Detail更新
                    TimecardDetail::updateRecord($timecard_id, false, $target_date_time, $type);
                }
            }

        }
        return !empty($next_timecard_id) ? $next_timecard_id : $timecard_id;

    }

    /*
     * 1つ前に更新されたDetail情報取得
     */
    private static function getBeforeDetail($control_id, $timecard_id)
    {

        $timecard_detail = "";

        switch ($control_id) {
            //出勤
            case Config::get('const.control_ids.clocking_in') :
                //前回の退勤レコード取得
                $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '1')->
                whereNotNull('end_time')->orderBy('updated_at', 'desc')->first();
                break;
            //休憩入り
            case Config::get('const.control_ids.break_in') :
                //前回の休憩レコード取得
                $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '2')->
                whereNotNull('end_time')->orderBy('updated_at', 'desc')->first();
                //初めての休憩だったら
                if (empty($timecard_detail)) {
                    //前回の出勤レコード取得
                    $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '1')->
                    whereNull('end_time')->orderBy('updated_at', 'desc')->first();
                }
                break;
            //休憩戻り
            case Config::get('const.control_ids.break_out') :
                //対象の休憩レコード取得
                $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '2')->
                whereNull('end_time')->orderBy('updated_at', 'desc')->first();
                break;
            //退勤
            case Config::get('const.control_ids.clocking_out') :
                //前回の休憩レコード取得
                $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '2')->
                whereNotNull('end_time')->orderBy('updated_at', 'desc')->first();
                //前回休憩してなかったら
                if (empty($timecard_detail)) {
                    //対象の出勤レコード取得
                    $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '1')->
                    whereNull('end_time')->orderBy('updated_at', 'desc')->first();
                }
                break;
        }

        return $timecard_detail;
    }

    /*
     * タイムカード更新
     * (Timecardおよび紐づくTimecardDetailを一括で更新する)
     * (編集時および変更申請承認時に使用するので退勤済みのもののみ対象)
     */
    public static function update($timecard_id, $employee_uid, $details, $change_request_flg = false, $workplace_uid = NULL)
    {
        try {
            $timecard_id = DB::connection('customer-db')->transaction(function () use ($timecard_id, $details, $employee_uid, $change_request_flg, $workplace_uid) {

                //TimecardおよびDetailは一旦すべて消す
                if (!empty($timecard_id)) {
                    $timecard = Timecard::where('id', $timecard_id)->first();
                    $timecard->forceDelete();
                }

                // 変更依頼じゃない場合
                if (!$change_request_flg) {
                    //未承認のChangeTimecardがある場合はそれも消す
                    $change_timecard = ChangeTimecard::where('timecard_id', $timecard_id)->whereNull('status')->first();
                    if (!empty($change_timecard)) {
                        $change_timecard->delete();
                    }
                }

                if (!empty($timecard_id)) {
                    $timecard_details = TimecardDetail::ofTimecard($timecard_id)->get();
                    foreach ($timecard_details as $deleted) {
                        $deleted->forceDelete();
                    }
                }
                // 変更依頼じゃない場合
                if (!$change_request_flg) {
                    //未承認のChangeTimecardDetailがある場合はそれも消す
                    $change_timecard_details = ChangeTimecardDetail::where('timecard_id', $timecard_id)->get();
                    foreach ($change_timecard_details as $deleted) {
                        $deleted->delete();
                    }
                }

                //更新
                foreach ($details as $idx => $detail) {
                    $res = self::setDataByControl($detail['type'], $detail['time']);
                    $start_time = $res['start_time'];
                    $end_time = $res['end_time'];
                    $type = $res['type'];

                    //Timecard新規
                    if ($idx == 0) {
                        //日付変更時刻によってTimecard用の日付変える
                        $date = self::getDateByTiming($detail['time'], $workplace_uid);
                        if (empty(Timecard::ofEmployee($employee_uid)->where('date', $date)->first())) {
                            $timecard_id = Timecard::createRecord($date, $employee_uid, $workplace_uid);
                        }
                        //Detail新規
                        TimecardDetail::updateRecord($timecard_id, $start_time, $end_time, $type);
                    } else {
                        //日がまたがっている場合があるので1日ずつループして登録する
                        $timecard_id = self::saveDetails($timecard_id, $start_time, $end_time, $employee_uid, $type, $detail['type'], $workplace_uid);
                    }
                }

                return $timecard_id;

            });

        } catch (Exception $e) {
            log::info($e);
            return false;
        }
        return $timecard_id;
    }

    /*
     * タイムカード削除
     */
    public static function delete($id)
    {
        try {
            DB::connection('customer-db')->transaction(function () use ($id) {

                //Timecard削除
                $timecard = Timecard::where('id', $id)->first();
                if (!empty($timecard)) {
                    $timecard->delete();
                }

                //Detail削除
                $timecard_details = TimecardDetail::ofTimecard($id)->get();
                foreach ($timecard_details as $timecard_detail) {
                    $timecard_detail->delete();
                }
            });

        } catch (Exception $e) {
            log::info($e);
            return false;
        }
        return true;
    }



    //-------------------------------------------------------------------
    // ここからバリデート用関数
    //-------------------------------------------------------------------

    /*
     * バリデート(追加時)
     */
    public static function validateForCreate($timecard_id, $time, $control_id, $workplace_uid = NULL)
    {
        $error = "";

        //新規追加(初出勤)のときはなにもなし
        if (empty($timecard_id)) {
            return $error;
        }

        if (!strtotime($time)) {
            $error = "日時は2017-01-01 00:00 形式で入力してください。";
            return $error;
        } elseif (!($time === date("Y-m-d H:i", strtotime($time)))) {
            $error = "日時が不正です。";
            return $error;
        }

        //前のコントロール時間よりも後か判定
        switch ($control_id) {
            //出勤
//            case Config::get('const.control_ids.clocking_in') :
//                //同日に退勤済みレコードがないか
//                $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '1')->
//                whereNotNull('end_time')->orderBy('updated_at', 'desc')->first();
//                //存在した場合、退勤時間より後でないとエラー
//                if (!empty($timecard_detail)) {
//                    if ($time < $timecard_detail->end_time) {
//                        $error = "退勤済みの時間よりも後の時間を入力してください。";
//                    }
//                }
//                break;
            //休憩入り
            case Config::get('const.control_ids.break_in') :

                //前回の休憩レコード取得
                $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '2')->
                whereNotNull('end_time')->orderBy('updated_at', 'desc')->first();
                //前回休憩してたら
                if (!empty($timecard_detail)) {
                    if (Carbon::parse($time) < Carbon::parse($timecard_detail->end_time)) {
                        $error = "休憩戻り時間よりも後の時間を入力してください。";
                    }
                    //日がまたがっていたら
                    if (!empty($timecard_detail) && !Carbon::parse($time)->isSameDay(Carbon::parse($timecard_detail->end_time))) {
                        if (self::isOtherTimecard($timecard_id, $timecard_detail->end_time, $time, $workplace_uid)) {
                            $error = "対象期間に他のタイムカードが既に存在します。";
                        }
                    }
                    //初めての休憩だったら
                } else {
                    //出勤レコード取得
                    $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '1')->
                    whereNull('end_time')->orderBy('updated_at', 'desc')->first();
                    if (!empty($timecard_detail)) {
                        if (Carbon::parse($time) < Carbon::parse($timecard_detail->start_time)) {
                            $error = "出勤時間よりも後の時間を入力してください。";
                        }
                        //日がまたがっていたら
                        if (!empty($timecard_detail) && !Carbon::parse($time)->isSameDay(Carbon::parse($timecard_detail->start_time))) {
                            if (self::isOtherTimecard($timecard_id, $timecard_detail->start_time, $time, $workplace_uid)) {
                                $error = "対象期間に他のタイムカードが既に存在します。";
                            }
                        }

                    } else {
                        $error = "出勤レコードが存在しません。";
                    }
                }
                break;
            //休憩戻り
            case Config::get('const.control_ids.break_out') :
                //対象の休憩レコード取得
                $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '2')->
                whereNull('end_time')->orderBy('updated_at', 'desc')->first();
                if (!empty($timecard_detail)) {
                    if (Carbon::parse($time) < Carbon::parse($timecard_detail->start_time)) {
                        $error = "休憩入り時間よりも後の時間を入力してください。";
                    }
                    //日がまたがっていたら
                    if (!empty($timecard_detail) && !Carbon::parse($time)->isSameDay(Carbon::parse($timecard_detail->start_time))) {
                        if (self::isOtherTimecard($timecard_id, $timecard_detail->start_time, $time, $workplace_uid)) {
                            $error = "対象期間に他のタイムカードが既に存在します。";
                        }
                    }

                } else {
                    $error = "休憩入りしたレコードが存在しません。";
                }
                break;
            //退勤
            case Config::get('const.control_ids.clocking_out') :
                //前回の休憩レコード取得
                $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '2')->
                whereNotNull('end_time')->orderBy('updated_at', 'desc')->first();
                //前回休憩してたら
                if (!empty($timecard_detail)) {
                    if (Carbon::parse($time) < Carbon::parse($timecard_detail->end_time)) {
                        $error = "休憩戻り時間よりも後の時間を入力してください。";
                    }

                    //日がまたがっていたら
                    if (!empty($timecard_detail) && !Carbon::parse($time)->isSameDay(Carbon::parse($timecard_detail->end_time))) {
                        if (self::isOtherTimecard($timecard_id, $timecard_detail->end_time, $time, $workplace_uid)) {
                            $error = "対象期間に他のタイムカードが既に存在します。";
                        }
                    }

                    //休憩してなかったら
                } else {
                    //対象の出勤レコード取得
                    $timecard_detail = TimecardDetail::where('timecard_id', $timecard_id)->where('type', '1')->
                    whereNull('end_time')->orderBy('updated_at', 'desc')->first();
                    if (!empty($timecard_detail)) {
                        if (Carbon::parse($time) < Carbon::parse($timecard_detail->start_time)) {
                            $error = "出勤時間よりも後の時間を入力してください。";
                        }
                        //日がまたがっていたら
                        if (!empty($timecard_detail) && !Carbon::parse($time)->isSameDay(Carbon::parse($timecard_detail->start_time))) {
                            if (self::isOtherTimecard($timecard_id, $timecard_detail->start_time, $time, $workplace_uid)) {
                                $error = "対象期間に他のタイムカードが既に存在します。";
                            }
                        }
                    } else {
                        $error = "出勤レコードが存在しません。";
                    }
                }
                break;
        }

        return $error;

    }

    /*
     * 指定期間内に他のタイムカードがないか確認
     */
    private static function isOtherTimecard($timecard_id, $start_date_time, $end_date_time, $workplace_uid = NULL)
    {
        $target = Timecard::where('id', $timecard_id)->first();
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        $carbon_start = Carbon::parse($start_date_time);
        $carbon_end = Carbon::parse($end_date_time);
        $res = false;
        //開始日時から終了日時まで1日ずつ加算して判定していく
        for ($start = $carbon_start;
             $start->format('Y-m-d') <= $carbon_end->format('Y-m-d');
             $start = $start->modify('+1 day')) {

            if ($start->isSameDay($carbon_end)) {
                $start = Carbon::parse($end_date_time);
            }

            //Timecardの日付作成
            $date = self::getDateByTiming($start->format('Y-m-d H:i'), $workplace_uid);
            //他のタイムカード取得
            $timecard = Timecard::ofWorkplace($workplace_uid)->ofEmployee($target->employee_uid)->where('id', '<>', $timecard_id)->where('date', $date)->first();
            if (!empty($timecard) && $start->format('H:i') != Workplace::getTimingOfTomorrow($workplace_uid)) {
                $res = true;
                break;
            }
        }

        return $res;
    }

    /*
     * 編集時バリデート
     */
    public static function validateForEdit($id, $employee_uid, $details, $workplace_uid = NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        $error = "";
        $timecard = Timecard::where('id', $id)->first();

        //タイムカードが存在しない
        if (empty($timecard)) {
            $error = "指定されたタイムカードが存在しません。";
            //detailsレコードがない
        } elseif (count($details) == 0) {
            $error = "タイムカードレコードを作成してください。";
            //detailsレコードチェック
        } else {
            foreach ($details as $idx => $record) {
                if (empty($record["time"]) || $record["type"] == "") {
                    $error = "未入力項目があります。";
                    break;
                    //日付形式
                } elseif (!strtotime($record["time"])) {
                    $error = "日時は2017-01-01 00:00 形式で入力してください。";
                    break;
                } elseif (!($record["time"] === date("Y-m-d H:i", strtotime($record["time"])))) {
                    $error = "日時が不正です。";
                    break;
                    //前のレコードとの比較
                } elseif ($idx != 0) {
                    $before_record = $details[$idx - 1];

                    if ($record["time"] < $before_record["time"]) {
                        $error = "上のレコードよりも後の日付を入力してください。";
                        break;
                    }
                    switch ($record["type"]) {
                        //出勤
                        case Config::get('const.control_ids.clocking_in') :
                            //前のレコードが退勤でなければエラー
                            if ($before_record["type"] != Config::get('const.control_ids.clocking_out')) {
                                $error = "出勤の前は退勤を選択してください。";
                            }
                            break;
                        //休憩入り
                        case Config::get('const.control_ids.break_in') :
                            //前のレコードが出勤もしくは休憩戻りでなければエラー
                            if ($before_record["type"] != Config::get('const.control_ids.clocking_in') &&
                                $before_record["type"] != Config::get('const.control_ids.break_out')
                            ) {
                                $error = "休憩入りの前は出勤もしくは休憩戻りを選択してください。";
                            }
                            break;
                        //休憩戻り
                        case Config::get('const.control_ids.break_out') :
                            //前のレコードが休憩入りでなければエラー
                            if ($before_record["type"] != Config::get('const.control_ids.break_in')) {
                                $error = "休憩戻りの前は休憩入りを選択してください。";
                            }
                            break;
                        //退勤
                        case Config::get('const.control_ids.clocking_out') :
                            //前のレコードが出勤もしくは休憩戻りでなければエラー
                            if ($before_record["type"] != Config::get('const.control_ids.clocking_in') &&
                                $before_record["type"] != Config::get('const.control_ids.break_out')
                            ) {
                                $error = "退勤の前は出勤もしくは休憩戻りを選択してください。";
                            }
                            break;
                    }
                    if (!empty($error)) {
                        break;
                    }
                    //最初に出勤があるか
                } elseif ($idx == "0" && $record["type"] != Config::get('const.control_ids.clocking_in')) {
                    $error = "出勤レコードを一番初めに選択してください。";
                    break;

                    //最後に退勤があるか
                } elseif ($details[count($details) - 1]["type"] != Config::get('const.control_ids.clocking_out')) {
                    $error = "退勤レコードを一番最後に選択してください。";
                    break;
                }
                //期間内に他のタイムカードがないか
                if (self::isOtherTimecard($id, $details[0]["time"], $details[count($details) - 1]["time"], $workplace_uid)) {
                    $error = "対象期間に他のタイムカードが既に存在します。";
                }

            }
        }
        return $error;
    }



    //-------------------------------------------------------------------
    // ここから各種データ取得用関数
    //-------------------------------------------------------------------

    /*
    * 翌日の開始日時を取得
    */
    public static function getNextDateTime($dateTime, $end_flg = false, $workplace_uid = NULL)
    {
        $date = Carbon::parse($dateTime)->format('Y-m-d');
        $time = Carbon::parse($dateTime)->format('H:i');

        if ($time >= Workplace::getTimingOfTomorrow($workplace_uid)) {
            $date = Carbon::parse($date)->addDays('1')->format('Y-m-d');
        }
        $next = $date . " " . Workplace::getTimingOfTomorrow($workplace_uid);
        if ($end_flg) {
            //$next = Carbon::parse($next)->subMinutes('1')->format('Y-m-d H:i');
        }

        return $next;
    }

    /*
     * 一覧表示用のタイムカードたち取得
     */
    public static function getTimecardsForList($start_date, $end_date, $employee_uid = NULL, $workplace_uid = NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        if (!empty($employee_uid)) {
            $timecards = Timecard::ofWorkplace($workplace_uid)
                ->where('employee_uid', $employee_uid)
                ->where('date', '>=', $start_date)->where('date', '<=', $end_date)->orderBy('date', 'desc')->get();
        } else {
            $timecards = Timecard::ofWorkplace($workplace_uid)
                ->where('date', '>=', $start_date)->where('date', '<=', $end_date)->orderBy('date', 'desc')->get();
        }

        foreach ($timecards as $timecard) {
            $timecard->first_time = TimecardDetail::getFirstTime($timecard->id);
            $timecard->last_time = TimecardDetail::getLastTime($timecard->id);
            $timecard->is_clocking_out = TimecardDetail::isClockingOut($timecard->id);
            $timecard->timecard_id = $timecard->id;
        }

        return $timecards;
    }

    /*
     * コントロールidからtimeとtypeデータを返す
     */
    public static function setDataByControl($control_id, $time)
    {
        $res = [
            'start_time' => false,
            'end_time' => false,
            'type' => false
        ];

        switch ($control_id) {
            //出勤
            case Config::get('const.control_ids.clocking_in') :
                $res['start_time'] = $time;
                $res['type'] = '1';
                break;
            //休憩入り
            case Config::get('const.control_ids.break_in') :
                $res['start_time'] = $time;
                $res['type'] = '2';
                break;
            //休憩戻り
            case Config::get('const.control_ids.break_out') :
                $res['end_time'] = $time;
                $res['type'] = '2';
                break;
            //退勤
            case Config::get('const.control_ids.clocking_out') :
                $res['end_time'] = $time;
                $res['type'] = '1';
                break;
        }

        return $res;
    }

    /*
     * 編集モーダル用詳細レコード取得
     */
    public static function getRecordsForEdit($id, $model = "TimecardDetail")
    {
        $details = []; //clock=出退勤レコード,rests=休憩レコード複数

        if ($model == "TimecardDetail") {
            $timecard_details = TimecardDetail::getDetailRecords($id, NULL, 'asc');
        } else {
            $timecard_details = ChangeTimecardDetail::getDetailRecords($id, NULL, 'asc');
        }

        $count = 0;
        $old_count = 0;
        //まず$detail[0]['clock'],$detail[0]['rests'],$detail[1]['clock']...の形式で作成
        foreach ($timecard_details as $timecard_detail) {
            if ($timecard_detail->type == "1") {
                $details[$count]['clock'] = $timecard_detail;
                $old_count = $count;
                $count++;
            } else {
                $details[$old_count]['rests'][] = $timecard_detail;
            }
        }

        //次にstart_timeとend_timeでさらにレコード分ける
        $records = [];
        $count = 0;
        foreach ($details as $detail) {
            //はじめに出勤
            $records[$count]['time'] = $detail['clock']->start_time;
            $records[$count]['type'] = '0';
            $count++;

            //休憩があれば
            if (!empty($detail['rests'])) {
                foreach ($detail['rests'] as $idx2 => $rest) {
                    $records[$count]['time'] = $rest->start_time;
                    $records[$count]['type'] = '1';
                    $count++;

                    $records[$count]['time'] = $rest->end_time;
                    $records[$count]['type'] = '2';
                    $count++;
                }
            }
            //最後に退勤
            $records[$count]['time'] = $detail['clock']->end_time;
            $records[$count]['type'] = '3';
            $count++;
        }

        return $records;
    }

    /*
     * 打刻時
     * 操作可能コントロール(出勤、退勤、休憩入り、休憩戻り)取得
     */
    public static function getEnableControl($time, $employee_uid, $employee_flg = false, $workplace_uid = NULL, $recorder_flg=false)
    {
        $controls = [];
        $control_keys = []; // const.control_idsに対応


        if (!strtotime($time)) {
            return $controls;
        }

        $date = self::getDateByTiming($time, $workplace_uid);

        //従業員詳細からきた場合
        //退勤済みタイムカードがないか確認(ajaxで絞れないので)
        if ($employee_flg) {
            $timecard = Timecard::getRecord($date, $employee_uid, 'desc', $workplace_uid);
            if (!empty($timecard)) {
                if (TimecardDetail::isClockingOut($timecard->id)) {
                    return ['error' => '既に退勤済みのタイムカードが存在するため新規追加できません。'];
                }
            }
        }

        //$timecard = Timecard::getRecord($date, $employee_uid, 'asc', $workplace_uid);
        //現在日時が最新の勤怠とは限らない
        $timecard = Timecard::getRecord(false, $employee_uid, 'asc', $workplace_uid);
        //新規打刻時

        if (!empty($timecard)) {

            if (TimecardDetail::isClockingOut($timecard->id) && !$recorder_flg) {
                return ['error' => '既に退勤済みのタイムカードが存在するため新規追加できません。'];
            }

            //最新の打刻情報
            $timecard_detail = TimecardDetail::getDetailRecord($timecard->id, NULL, 'desc');
            if (!empty($timecard_detail)) {
                //type=1の場合は出勤・退勤
                if ($timecard_detail->type == "1") {
                    //出勤済み
                    if (empty($timecard_detail->end_time)) {
                        //休憩入りor退勤
                        $control_keys = ['break_in', 'clocking_out'];

                        //退勤済み
                    } else {
                        //出勤
                        $control_keys = ['clocking_in'];
                    }
                    //type=2の場合は休憩入り・戻り
                } elseif ($timecard_detail->type == "2") {
                    //休憩入り済み
                    if (empty($timecard_detail->end_time)) {
                        //休憩戻り
                        $control_keys = ['break_out'];

                        //休憩戻り済み
                    } else {
                        //休憩入りor退勤
                        $control_keys = ['break_in', 'clocking_out'];
                    }
                }
            } else {
                //出勤
                $control_keys = ['clocking_in'];
            }
        } else {
            //最新のタイムカード
            $timecard = Timecard::getCurrentRecord($employee_uid, $workplace_uid);
            if (!empty($timecard)) {
                //休憩戻りしていないレコードがあれゔぁ
                if (!TimecardDetail::isRestOut($timecard->id)) {
                    //休憩戻り
                    $control_keys = ['break_out'];
                    //まだ退勤していないレコードがあれば
                } else if (!TimecardDetail::isClockingOut($timecard->id)) {
                    //休憩入りor退勤
                    $control_keys = ['break_in', 'clocking_out'];
                } else {
                    //出勤
                    $control_keys = ['clocking_in'];
                }
            }
        }

        if (count($control_keys) == 0) {
            $control_keys = ['clocking_in'];
        }
        foreach ($control_keys as $key) {
            $controls[] = Config::get('const.control_ids.' . $key);
        }


        return $controls;
    }

    /*
     * 打刻可能状態の従業員取得
     */
    public static function getEnableEmployee($time)
    {
        $date = self::getDateByTiming($time);
        $employeeData = EmployeeService::getEmployees();

        foreach ($employeeData['employees'] as $idx => $employee) {
            $timecard = Timecard::getRecord($date, $employee->uid);

            //退勤済みのタイムカードが存在したらその従業員は新規打刻できない
            if (!empty($timecard) && TimecardDetail::isClockingOut($timecard->id)) {
                unset($employeeData['employees'][$idx]);
                //出勤中のタイムカードが存在したらその従業員は新規打刻できない
            } elseif (EmployeeService::getCurrentStatus($employee->uid, Auth::user()->workplace_uid) != "待機中") {
                unset($employeeData['employees'][$idx]);
            } else {
                $employeeData['employees'][$idx]->uid = (string)$employeeData['employees'][$idx]->uid;
            }
        }

        return array_values($employeeData['employees']);
    }

    /*
     * 日付変更時刻によって日を変える処理
     */
    public static function getDateByTiming($dateTime, $workplace_uid = NULL)
    {
        $time = Carbon::parse($dateTime)->format('H:i');

        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        //日付変更時刻によってdateを変える
        if (strtotime($time . ':00') < strtotime(Workplace::getTimingOfTomorrow($workplace_uid) . ':00')) {
            //日付変更日時より小さかったら前の日付にする
            $date = Carbon::parse($dateTime)->subDays(1)->format('Y-m-d');
        } else {
            $date = Carbon::parse($dateTime)->format('Y-m-d');
        }
        return $date;
    }

    /*
     * タイムカードの情報（１日分）
     * 出勤時間、退勤時間、出勤総稼働時間、休憩総稼働時間
     * を集計
     */
    public static function getTimecardInfoDaily($date, $employee_uid, $workplace_uid = NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        //対象日付
        $timecard = Timecard::getRecord($date, $employee_uid);
        if (empty($timecard)) {
            $timecard = new Timecard();
        }

        //丸め分数
        $work_round = Workplace::ofWorkplace($workplace_uid)->first()->round_minute_attendance;
        $rest_round = Workplace::ofWorkplace($workplace_uid)->first()->round_minute_break;
        //稼働時間
        $rest_time = 0;
        $work_time = 0;
        //出勤時間
        $clockIn = "";
        //退勤時間
        $clockOut = "";

        //出勤時間取得
        $start = TimecardDetail::getDetailRecord($timecard->id, '1', $sort = 'asc');
        if (!empty($start->start_time)) {
            $clockIn = Carbon::parse($start->start_time)->format('H:i');
        }
        //退勤時間取得
        $end = TimecardDetail::getDetailRecord($timecard->id, '1', $sort = 'desc');
        if (!empty($end->end_time)) {
            $clockOut = Carbon::parse($end->end_time)->format('H:i');
        }

        //対象日付の総出勤レコード
        $works = TimecardDetail::where('timecard_id', $timecard->id)->where('type', '1')
            ->get();
        //対象日付の総休憩レコード
        $rests = TimecardDetail::where('timecard_id', $timecard->id)->where('type', '2')
            ->get();

        //出勤総稼働時間取得
        foreach ($works as $work) {
            $column_name = "operating_time_round" . $work_round;
            $time = $work->$column_name;
            $work_time += $time;
        }
        //休憩総稼働時間取得
        foreach ($rests as $rest) {
            $column_name = "operating_time_round" . $rest_round;
            $time = $rest->$column_name;
            $rest_time += $time;
        }

        return [
            "clockIn" => $clockIn,
            "clockOut" => $clockOut,
            "rest_time" => $rest_time,
            "work_time" => $work_time,
        ];
    }

    /*
     * 時給別の勤怠情報
     * (時給別の稼働時間・金額)
     * を月単位で集計
     */
    public static function getTimecardInfoBySalary($year, $month, $employee_uid, $workplace_uid = NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        $salarieList = [];
        //丸め分数
        $work_round = Workplace::ofWorkplace($workplace_uid)->first()->round_minute_attendance;
        //対象期間
        $start_date = $year . "-" . $month . "-01";
        $end_date = Carbon::parse($start_date)->endOfMonth();


        $timecards = Timecard::ofWorkplace($workplace_uid)->where('employee_uid', $employee_uid)
            ->where('date', '>=', $start_date)->where('date', '<=', $end_date)->get();

        //タイムカードごとに集計
        foreach ($timecards as $i => $timecard) {

            //対象日付の総出勤レコード
            $works = TimecardDetail::getDetailRecords($timecard->id, '1', $sort = 'asc');
            //対象日付の総休憩レコード
            $rests = TimecardDetail::getDetailRecords($timecard->id, '2', $sort = 'asc');

            $salarieList[$i] = [];
            $rest_count = 0;



            foreach ($works as $work) {

                //丸め後の出勤時間
                $carbon_start_time = TimecardDetail::getRoundStartTime($work->start_time, $work_round);
                //丸め後の退勤時間
                $carbon_end_time = TimecardDetail::getRoundEndTime($work->end_time, $work_round);

                $idx = 0;

                //分ごとに加算して支払額を算出
                for ($start = $carbon_start_time;
                     $start < $carbon_end_time;
                     $start = $start->modify('+1 minute')) {

                    //休憩中かどうか
                    $rest_flg = self::isRest($rests, $start);
                    //休憩じゃなければ集計処理実行
                    if (!$rest_flg) {
                        //list($salarieList, $idx) = self::setSalalyList($salarieList[$i], $idx, $carbon_start_time, $employee_uid);
                        $res = self::setSalalyList($salarieList, $i, $idx, $carbon_start_time, $employee_uid);
                       // $salarieList = $res['salarieList'];
                        $idx = $res['idx'];
                        if (!$salarieList) {
                            return false;
                        }
                    } else {
                        //$rest_count++;
                    }
                }
            }
//            if ($rest_count != 0) {
//                $salarieList[$i][0]['time'] -= 1;
//            }
        }

        return self::setTimecardInfoBySaralyList($salarieList);
    }

    /*
     * 時給別の勤怠情報集計で使用
     * 休憩中か確認
     */
    private static function isRest($rests, $start, $workplace_uid = NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        $rest_flg = false;

        //休憩の丸め分数
        $rest_round = Workplace::ofWorkplace($workplace_uid)->first()->round_minute_break;

        foreach ($rests as $rest) {
//            //丸め後の休憩時間
//            $carbon_rest_start_time = TimecardDetail::getRoundStartTime($rest->start_time, $rest_round);
//            //丸め後の休憩時間
//            $carbon_rest_end_time = TimecardDetail::getRoundEndTime($rest->end_time, $rest_round);
            //休憩は出退勤と丸め方が逆
            //丸め後の休憩時間
            $carbon_rest_start_time = TimecardDetail::getRoundEndTime($rest->start_time, $rest_round);
            //丸め後の休憩時間
            $carbon_rest_end_time = TimecardDetail::getRoundStartTime($rest->end_time, $rest_round);

            //休憩時間に被ってたら集計外
            if ($start->between($carbon_rest_start_time, $carbon_rest_end_time)) {
                if ($start == $carbon_rest_start_time) {
                    $rest_flg = false;
                } elseif ($start > $carbon_rest_end_time) {
                    $rest_flg = false;
                } else {
                    $rest_flg = true;
                }
                break;
            }
        }
        return $rest_flg;
    }

    /*
     * 時給別の勤怠情報集計で使用
     * 時給別の勤怠情報をセット
     */
    private static function setSalalyList(&$salarieList, $i, $idx, $carbon_start_time, $employee_uid)
    {

        $start_date = $carbon_start_time->format('Y-m-d');
        $start_time = $carbon_start_time->format('H:i') . ':00';

        //時給情報
        $employee_price = Salary::where('employee_uid', $employee_uid)
            ->where(function($query) use ($start_date, $start_time) {
                $query->where(function ($query) use ($start_date, $start_time) {
                    $query->where('apply_date', $start_date);
                    $query->where('start_time', '<=', $start_time);
                })->orWhere(function ($query) use ($start_date, $start_time) {
                    $query->where('apply_date', '<', $start_date);
                });
             })
            ->orderBy('apply_date', 'desc')->orderBy('start_time', 'desc')->first();


        //時給設定されてなかったらエラー
        if (empty($employee_price)) {
            return [
                'salarieList' => false,
                'idx' => false
            ];
        }
//
//        if ($idx == 0) {
//            if (empty($salarieList[$i][$idx])) {
//                $salarieList[$i][$idx] = [
//                    'id' => $employee_price->id,
//                    'apply_date' => $employee_price->apply_date,
//                    'start_time' => Carbon::parse($employee_price->start_time)->format('H:i'),
//                    'hourly_pay' => $employee_price->hourly_pay,
//                    'time' => 1,
//                ];
//                $idx++;
//            }
//
//        } else {


        $flg = false;
            foreach ($salarieList[$i] as &$r) {
                if ($r["id"] == $employee_price->id) {
                    $r["time"] += 1;
                    $flg = true;
                }
            }
            if (!$flg) {
                $salarieList[$i][$idx] = [
                    'id' => $employee_price->id,
                    'apply_date' => $employee_price->apply_date,
                    'start_time' => Carbon::parse($employee_price->start_time)->format('H:i'),
                    'hourly_pay' => $employee_price->hourly_pay,
                    'time' => 1,
                ];
                $idx++;
//            }
        }

        //時給別稼働時間
        //$salarieList[$i][$idx - 1]["time"] += 1;

        return [
            'salarieList' => $salarieList,
            'idx' => $idx
        ];

    }

    /*
     * 時給別の勤怠情報集計で使用
     * 時給別の勤怠情報を整理
     */
    private static function setTimecardInfoBySaralyList($salarieList)
    {

        $idx = 0;
        $res = [];

        foreach ($salarieList as $salaries) {

            foreach ($salaries as $salary) {

                if ($idx == 0) {
                    $res[$idx] = [
                        'id' => $salary["id"],
                        'apply_date' => $salary["apply_date"],
                        'start_time' => $salary["start_time"],
                        'hourly_pay' => $salary["hourly_pay"],
                        'time' => $salary["time"],
                    ];
                    $idx++;
                } else {
                    $flg = false;
                    foreach ($res as &$r) {
                        if ($r["id"] == $salary["id"]) {
                            $r["time"] += $salary["time"];
                            $flg = true;
                        }
                    }
                    if (!$flg) {
                        $res[$idx] = [
                            'id' => $salary["id"],
                            'apply_date' => $salary["apply_date"],
                            'start_time' => $salary["start_time"],
                            'hourly_pay' => $salary["hourly_pay"],
                            'time' => $salary["time"],
                        ];
                        $idx++;
                    }
                }
            }
        }

        return $res;
    }

    /*
     * 1ヶ月分の通勤手当取得
     */
    public static function getTrafficCostMonthly($year, $month, $employee_uid, $workplace_uid = NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        $start_date = $year . "-" . $month . "-01";
        $end_date = Carbon::parse($start_date)->endOfMonth();

        $timecards = Timecard::ofWorkplace($workplace_uid)->where('employee_uid', $employee_uid)
            ->where('date', '>=', $start_date)->where('date', '<=', $end_date)->get();

        $employee = Employee::where('uid', $employee_uid)->first();

        return count($timecards) * $employee->traffic_cost;
    }
}