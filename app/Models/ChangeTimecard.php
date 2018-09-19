<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ChangeTimecard extends Model
{
    use SoftDeletes;

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'change_timecards';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "timecard_id","employee_uid","workplace_uid","date","status"
    ];

    public function change_timecard_details()
    {
        return $this->hasMany('App\Models\ChangeTimecardDetail','timecard_id');
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

    /*
     * ステータス更新
     */
    public static function updateStatus($id, $status) {
        $change_timecard = ChangeTimecard::where('id', $id)->first();
        if (!empty($change_timecard)) {
            $change_timecard->status = $status;
            $change_timecard->save();
        }
    }

    /*
     * タイムカード変更依頼を新規作成する
     */
    public static function createRecord($timecard_id, $date, $employee_uid, $workplace_uid=NULL) {

        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }

        $timecard = ChangeTimecard::where('timecard_id', $timecard_id)->first();
        if (!empty($timecard)) {
            $timecard->forceDelete();
        }
        $timecard = new ChangeTimecard();
        $timecard->timecard_id = $timecard_id;
        $timecard->employee_uid = $employee_uid;
        $timecard->workplace_uid = $workplace_uid;
        $timecard->date = $date;
        $timecard->save();


        return DB::connection('customer-db')->getPdo()->lastInsertId();

    }

}

