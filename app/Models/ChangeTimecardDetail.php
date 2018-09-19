<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class ChangeTimecardDetail extends Model
{
    use SoftDeletes;

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'change_timecard_details';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "change_timecard_id","timecard_id","start_time","end_time","type",
        "operating_time_round1","operating_time_round10","operating_time_round15","operating_time_round30","operating_time_round60"
    ];

    /*
     * 詳細レコードたち取得
     */
    public static function getDetailRecords($id, $type = NULL, $sort='desc')
    {
        if (empty($type)) {
            $timecard_detail = ChangeTimecardDetail::ofTimecard($id)->orderBy('created_at', $sort)->get();
        } else {
            $timecard_detail = ChangeTimecardDetail::ofTimecard($id)
                ->where('type', $type)->orderBy('created_at', $sort)->get();
        }
        if (!empty($timecard_detail)) {
            return $timecard_detail;
        }
        return [];
    }

    /*
     * スコープ
     */
    public function scopeOfTimecard($query, $timecard_id)
    {
        return $query->where('change_timecard_id', $timecard_id);
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

    /*
     * タイムカード詳細変更依頼を新規作成する
     */
    public static function updateRecord($change_timecard_id, $timecard_id, $start_time, $end_time, $type) {

        $change_timecard_detail = ChangeTimecardDetail::ofTimecard($change_timecard_id)
            ->where('type', $type)->where('end_time', NULL)->first();

        if (empty($change_timecard_detail)) {
            $change_timecard_detail = new ChangeTimecardDetail();
        }
        $change_timecard_detail->type = $type;
        $change_timecard_detail->change_timecard_id = $change_timecard_id;
        $change_timecard_detail->timecard_id = $timecard_id;

        if ($start_time) {
            $change_timecard_detail->start_time = $start_time;
        }
        if ($end_time) {

            $change_timecard_detail->end_time = $end_time;
            $operating_times = self::getOperatingTimes($change_timecard_detail->start_time, $change_timecard_detail->end_time, $type);
            $change_timecard_detail->fill($operating_times);

        }


        $change_timecard_detail->save();
    }

    /*
    * 稼働時間たちを返す
    */
    public static function getOperatingTimes($start, $end, $type="1") {

        $carbon_start = Carbon::parse($start);
        $carbon_end = Carbon::parse($end);

        $res = [];

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
            return Carbon::parse($date . " " . $h . ":" . $i)->addDays('1');
        }
        if ($i > 60) {
            $i = $i - 60;
            return Carbon::parse($date . " " . $h . ":" . $i)->addDays('1');
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
        if ($i > 60) {
            $i = $i - 60;
            return Carbon::parse($date . " " . $h . ":" . $i)->addDays('1');
        }
        return Carbon::parse($date . " " . $h . ":" . $i);
    }

}

