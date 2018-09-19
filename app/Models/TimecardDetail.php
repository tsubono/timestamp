<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;
use Log;

class TimecardDetail extends Model
{
    use SoftDeletes;

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'timecard_details';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "timecard_id","start_time","end_time","type",
        "operating_time_round1","operating_time_round10","operating_time_round15","operating_time_round30","operating_time_round60"
    ];

    /*
     * タイムカード詳細を新規作成もしくは更新する
     */
    public static function updateRecord($timecard_id, $start_time, $end_time, $type) {

        $timecard_detail = TimecardDetail::ofTimecard($timecard_id)
                            ->where('type', $type)->where('end_time', NULL)->first();

        if (empty($timecard_detail)) {
            $timecard_detail = new TimecardDetail();
            $timecard_detail->type = $type;
            $timecard_detail->timecard_id = $timecard_id;
        }
        if ($start_time) {
            $timecard_detail->start_time = $start_time;
        }
        if ($end_time) {
            $timecard_detail->end_time = $end_time;
            $operating_times = self::getOperatingTimes($timecard_detail->start_time, $timecard_detail->end_time, $timecard_detail->type);
            $timecard_detail->fill($operating_times);
        }

        $timecard_detail->save();
    }


    /*
     * 詳細レコードたち取得
     */
    public static function getDetailRecords($id, $type = NULL, $sort='desc')
    {
        if (empty($type)) {
            $timecard_detail = TimecardDetail::ofTimecard($id)->orderBy('created_at', $sort)->get();
        } else {
            $timecard_detail = TimecardDetail::ofTimecard($id)
                                ->where('type', $type)->orderBy('created_at', $sort)->get();
        }
        if (!empty($timecard_detail)) {
            return $timecard_detail;
        }
        return [];
    }
    /*
     * 詳細レコード取得
     */
    public static function getDetailRecord($id, $type = NULL, $sort='desc')
    {
        if (empty($type)) {
            $timecard_detail = TimecardDetail::ofTimecard($id)->orderBy('id', $sort)->first();
        } else {
            $timecard_detail = TimecardDetail::ofTimecard($id)
                ->where('type', $type)->orderBy('id', $sort)->first();
        }
        if (!empty($timecard_detail)) {
            return $timecard_detail;
        }
        return NULL;
    }

    /*
    * 出勤時間取得
    */
    public static function getFirstTime($id)
    {
        $timecard_detail = TimecardDetail::ofTimecard($id)
                            ->where('type', '1')->orderBy('start_time', 'asc')->first();
        if (!empty($timecard_detail)) {
            return $timecard_detail->start_time;
        }
        return NULL;
    }

    /*
     * 退勤時間取得
     */
    public static function getLastTime($id)
    {

        $timecard_detail = TimecardDetail::ofTimecard($id)
                            ->where('type', '1')->orderBy('end_time', 'desc')->first();
        if (!empty($timecard_detail)) {
            return $timecard_detail->end_time;
        }
        return NULL;
    }

    /*
     * 退勤済みかどうか
     */
    public static function isClockingOut($id)
    {
        $timecard_detail = TimecardDetail::ofTimecard($id)
                            ->where('type', '1')->where('end_time', NULL)->first();
        //出・退勤でend_timeがNULLのレコードがあればまだ退勤していない
        if (!empty($timecard_detail)) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * 休憩戻り済みかどうか
     *
     */
    public static function isRestOut($timecard_id)
    {
        $timecard_detail = TimecardDetail::ofTimecard($timecard_id)
                            ->where('type', '2')->where('end_time', NULL)->first();
        //出・退勤でend_timeがNULLのレコードがあればまだ退勤していない
        if (!empty($timecard_detail)) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * 稼働時間たちを返す
     */
    public static function getOperatingTimes($start, $end, $type="1") {

        $carbon_start = Carbon::parse($start);
        $carbon_end = Carbon::parse($end);
        $res = [];

        if ($start != $end) {
            if ($type=="1") {
                $res['operating_time_round1'] = $carbon_start->diffInMinutes($carbon_end);
                $res['operating_time_round10'] = Carbon::parse(self::getRoundStartTime($start, '10'))
                    ->diffInMinutes(self::getRoundEndTime($end, '10'));
                $res['operating_time_round15'] = Carbon::parse(self::getRoundStartTime($start, '15'))
                    ->diffInMinutes(self::getRoundEndTime($end, '15'));
                $res['operating_time_round30'] = Carbon::parse(self::getRoundStartTime($start, '30'))
                    ->diffInMinutes(self::getRoundEndTime($end, '30'));
                $res['operating_time_round60'] = Carbon::parse(self::getRoundStartTime($start, '60'))
                    ->diffInMinutes(self::getRoundEndTime($end, '60'));
            } else {
                $res['operating_time_round1'] = $carbon_start->diffInMinutes($carbon_end);
                $res['operating_time_round10'] = Carbon::parse(self::getRoundEndTime($start, '10'))
                    ->diffInMinutes(self::getRoundStartTime($end, '10'));
                $res['operating_time_round15'] = Carbon::parse(self::getRoundEndTime($start, '15'))
                    ->diffInMinutes(self::getRoundStartTime($end, '15'));
                $res['operating_time_round30'] = Carbon::parse(self::getRoundEndTime($start, '30'))
                    ->diffInMinutes(self::getRoundStartTime($end, '30'));
                $res['operating_time_round60'] = Carbon::parse(self::getRoundEndTime($start, '60'))
                    ->diffInMinutes(self::getRoundStartTime($end, '60'));
            }
        } else {
            $res['operating_time_round1'] = 0;
            $res['operating_time_round10'] = 0;
            $res['operating_time_round15'] = 0;
            $res['operating_time_round30'] = 0;
            $res['operating_time_round60'] = 0;
        }




        return $res;
    }

    /*
     * 丸め時刻取得(start_time)
     */
    public static function getRoundStartTime($dateTime, $round)
    {
        $date = Carbon::parse($dateTime)->format('Y-m-d');
        $h = Carbon::parse($dateTime)->format('H');
        $i = Carbon::parse($dateTime)->format('i');

        $i = number_format(ceil(($i / $round)) * $round);

        if ($i == 0) {
            $i = "00";
        }
        if ($i == 60) {
            $i = "00";
            return Carbon::parse($date . " " . $h . ":" . $i)->addHours('1');
        }
        if ($i > 60) {
            $i = $i - 60;
            return Carbon::parse($date . " " . $h . ":" . $i)->addHours('1');
        }

        return Carbon::parse($date . " " . $h . ":" . $i);

    }

    /*
     * 丸め時刻取得(end_time)
     */
    public static function getRoundEndTime($dateTime, $round)
    {
        $date = Carbon::parse($dateTime)->format('Y-m-d');
        $h = Carbon::parse($dateTime)->format('H');
        $i = Carbon::parse($dateTime)->format('i');

        $i = number_format(floor(($i) / $round) * $round);

        if ($i == 0) {
            $i = "00";
        }
        if ($i == 60) {
            $i = "00";
            return Carbon::parse($date . " " . $h . ":" . $i)->addHours('1');
        }
        if ($i > 60) {
            $i = $i - 60;
            return Carbon::parse($date . " " . $h . ":" . $i)->addHours('1');
        }

        return Carbon::parse($date . " " . $h . ":" . $i);
    }


    /*
     * スコープ
     */
    public function scopeOfTimecard($query, $timecard_id)
    {
        return $query->where('timecard_id', $timecard_id);
    }

    public function getStartTimeAttribute($value)
    {
        if (!empty($value)) {
            return Carbon::parse($value)->format('Y-m-d H:i');
        } else {
            return $value;
        }
    }
    public function getEndTimeAttribute($value)
    {
        if (!empty($value)) {
            return Carbon::parse($value)->format('Y-m-d H:i');
        } else {
            return $value;
        }
    }

}

