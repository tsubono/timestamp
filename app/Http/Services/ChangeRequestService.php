<?php
namespace App\Http\Services;

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
 * 打刻変更依頼関連を扱うサービス
 */

class ChangeRequestService
{
    /*
    * タイムカード更新
    * (Timecardおよび紐づくTimecardDetailを一括で更新する)
    * (編集時および変更申請承認時に使用するので退勤済みのもののみ対象)
    */
    public static function update($timecard_id, $data, $workplace_uid, $status=NULL)
    {
        try {
            DB::connection('customer-db')->transaction(function () use ($timecard_id, $data, $workplace_uid, $status) {

                //追加申請でない場合
                if (!empty($timecard_id)) {
                    $change_timecard = ChangeTimecard::where('timecard_id', $timecard_id)->whereNull('status')->first();
                    if (!empty($change_timecard)) {
                        $change_timecard->forceDelete();
                    }

                    //未承認のChangeTimecardDetailがある場合はそれも消す
                    $change_timecard_details = ChangeTimecardDetail::where('timecard_id', $timecard_id)->get();
                    foreach ($change_timecard_details as $deleted) {
                        $deleted->forceDelete();
                    }
                }


                //ChangeTimecards更新
                $change_timecard = new ChangeTimecard();
                $change_timecard->timecard_id = $timecard_id;
                $change_timecard->workplace_uid = $workplace_uid;
                if (!empty($data)) {
                    $change_timecard->employee_uid = $data['euid'];
                    $change_timecard->date = $data['date'];
                }
                $change_timecard->status = $status;
                $change_timecard->save();

                $change_timecard_id = DB::connection('customer-db')->getPdo()->lastInsertId();

                //ChangeTimecardDetail更新
                if (!empty($data)) {
                    foreach ($data['records'] as $idx => $detail) {
                        $res = self::setDataByControl($detail['type'], $detail['time']);
                        $start_time = $res['start_time'];
                        $end_time = $res['end_time'];
                        $type = $res['type'];

                        ChangeTimecardDetail::updateRecord($change_timecard_id, $timecard_id, $start_time, $end_time, $type);
                    }
                }

            });

        } catch (Exception $e) {
            log::info($e);
            return false;
        }
        return true;
    }

    /*
     * 変更依頼時バリデート
     */
    public static function validateForEdit($id, $data, $workplace_uid=NULL, $add_flg=false)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        $error = "";
        $timecard = Timecard::where('id', $id)->first();

        //タイムカードが存在しない
        if (empty($timecard) && !$add_flg) {
            $error = "指定されたタイムカードが存在しません。";
            //detailsレコードがない
        } elseif (count($data['records']) == 0) {
            $error = "タイムカードレコードを作成してください。";
            //detailsレコードチェック
        } else {
            foreach ($data['records'] as $idx => $record) {
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
                    $before_record = $data['records'][$idx - 1];

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
                } elseif ($data['records'][count($data['records']) - 1]["type"] != Config::get('const.control_ids.clocking_out')) {
                    $error = "退勤レコードを一番最後に選択してください。";
                    break;
                //期間内に他のタイムカードがないか
                } elseif (self::isOtherTimecard($id, $data['records'][0]["time"], $data['records'][count($data['records']) - 1]["time"], $data['euid'], $workplace_uid)) {
                    $error = "対象期間に他のタイムカードが既に存在します。";
                    break;
                }
                if (!(Carbon::parse(self::getDateByTiming($record["time"], $workplace_uid))->eq(Carbon::parse($data['date'])))) {
                    $error = $data['date']."以外の日付が含まれています。";
                    break;
                }
            }
        }

        return $error;
    }

    //-------------------------------------------------------------------
    // ここからバリデート用関数
    //-------------------------------------------------------------------

    /*
     * バリデート(追加時)
     */
    public static function validateForCreate($timecard_id, $time, $control_id,$workplace_uid=NULL)
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
                        if (self::isOtherTimecard($timecard_id, $timecard_detail->end_time, $time,$workplace_uid)) {
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
                            if (self::isOtherTimecard($timecard_id, $timecard_detail->start_time, $time,$workplace_uid)) {
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
                        if (self::isOtherTimecard($timecard_id, $timecard_detail->start_time, $time,$workplace_uid)) {
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
                        if (self::isOtherTimecard($timecard_id, $timecard_detail->end_time, $time,$workplace_uid)) {
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
                            if (self::isOtherTimecard($timecard_id, $timecard_detail->start_time, $time,$workplace_uid)) {
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
    private static function isOtherTimecard($timecard_id, $start_date_time, $end_date_time, $employee_uid, $workplace_uid=NULL)
    {
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
            $date = self::getDateByTiming($start->format('Y-m-d H:i'),$workplace_uid);
            //他のタイムカード取得
            $timecard = Timecard::ofWorkplace($workplace_uid)->ofEmployee($employee_uid)->where('id', '<>', $timecard_id)->where('date', $date)->first();
            if (!empty($timecard)) {
                $res = true;
                break;
            }
        }

        return $res;
    }


    //-------------------------------------------------------------------
    // ここから各種データ取得用関数
    //-------------------------------------------------------------------

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

}