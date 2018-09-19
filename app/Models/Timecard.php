<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Config;
use DB;
use Log;

class Timecard extends Model
{
    use SoftDeletes;

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'timecards';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "employee_uid","workplace_uid","date"
    ];

    public function timecard_details()
    {
        return $this->hasMany('App\Models\TimecardDetail','timecard_id');
    }

    public function employee() {
        return $this->hasOne('App\Models\Employee', 'uid', 'employee_uid');
    }

    /*
     * スコープ
     */
    public function scopeOfWorkplace($query, $workplace_uid=NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        return $query->where('workplace_uid', $workplace_uid);
    }

    public function scopeOfEmployee($query, $employee_uid)
    {
        return $query->where('employee_uid', $employee_uid);
    }

    /*
     * タイムカードを取得する
     */
    public static function getRecord($date, $employee_uid, $sort='asc',$workplace_uid=NULL) {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        //日付指定がない場合
        if (!$date) {
            //一旦全部取得
            $timecards = Timecard::ofWorkplace($workplace_uid)->where('employee_uid', $employee_uid)
                ->orderBy('updated_at', $sort)->get();
            if (count($timecards)==0) {
                return null;
            }
            foreach ($timecards as $timecard) {
                $is_clocking_out = TimecardDetail::isClockingOut($timecard->id);
                //未退勤ならそれ返す
                if (!$is_clocking_out) {
                    return $timecard;
                }
            }
            //未退勤がなかったら最新日付のやつ
            return Timecard::ofWorkplace($workplace_uid)->where('employee_uid', $employee_uid)
                ->orderBy('updated_at', $sort)->first();
        }
       return Timecard::ofWorkplace($workplace_uid)->where('date', $date)->where('employee_uid', $employee_uid)
                    ->orderBy('updated_at', $sort)->first();
    }

    /*
     * 最新のタイムカードを取得する
     */
    public static function getCurrentRecord($employee_uid,$workplace_uid=NULL) {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        return Timecard::ofWorkplace($workplace_uid)->where('employee_uid', $employee_uid)->orderBy('id', 'desc')->first();
    }

    /*
     * 勤務日数取得
     */
    public static function getWorkCount($year, $month, $employee_uid, $workplace_uid=NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        $start_date = $year . "-" . $month . "-01";
        $end_date = Carbon::parse($start_date)->endOfMonth();

        $timecards = Timecard::ofWorkplace()->where('employee_uid', $employee_uid)
            ->where('date', '>=', $start_date)->where('date', '<=', $end_date)->get();

        return count($timecards);
    }

    /*
     * タイムカードを新規作成する
     */
    public static function createRecord($date, $employee_uid, $workplace_uid=NULL) {

        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        $timecard = new Timecard();
        $timecard->employee_uid = $employee_uid;
        $timecard->workplace_uid = $workplace_uid;
        $timecard->date = $date;
        $timecard->save();


        return DB::connection('customer-db')->getPdo()->lastInsertId();

    }

    /*
     * 打刻のコントロール一覧を返す
     */
    public static function getControls() {

        $controls = [];

        $control_ids = Config::get('const.control_ids');
        foreach ($control_ids as $control_id) {
            $controls[] = [
                'id' => $control_id,
                'label' => Config::get('const.control_names.'.$control_id)
            ];
        }

        return $controls;
    }

}

