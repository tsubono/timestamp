<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Log;


class Workplace extends Model
{
    use SoftDeletes;

    protected $connection = 'customer-db';

    private static $baseTimestamp = 1335798000000;
    private static $workerId = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workplaces';

    /**
     * @var string
     */
    protected $primaryKey = 'uid';

    protected $fillable = [
        "uid","name","formal_name","zip_code","pref","address","building",
        "tel","timing_of_tomorrow","round_minute_attendance","round_minute_break","plan_id","next_plan_id",
        "payment_method","payment_customer_id","payment_card_id",
        "next_charge_date","expiration_date","suspend_flg","payroll_role","charged_flg"
    ];

    public function plan()
    {
        return $this->hasOne('App\Models\Plan','id','plan_id');
    }

    /*
     * バリデート(勤務場所)
     */
    public static function validate_workplace($data) {
        $rules = [
            'name' => 'required',
            'formal_name' => 'required',
            'zip_1' => 'required|digits:3/|numeric',
            'zip_2' => 'required|digits:4/|numeric',
            'pref' => 'required',
            'address' => 'required',
            'tel' => 'required|regex:/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/',
            'timing_of_tomorrow' => 'required|date_format:H:i',
        ];

        $messages = [
            'name.required'  => '略称を入力してください。',
            'formal_name.required'  => '正式名称を入力してください。',
            'zip_1.required' => '郵便番号(3桁)を入力してください。',
            'zip_1.numeric' => '郵便番号(3桁)を半角数字で入力してください。',
            'zip_1.digits' => '郵便番号(3桁)は3桁で入力してください。',
            'zip_2.required' => '郵便番号(4桁)を入力してください。',
            'zip_2.numeric' => '郵便番号(4桁)を半角数字で入力してください。',
            'zip_2.digits' => '郵便番号(4桁)は4桁で入力してください。',
            'pref.required' => '都道府県を入力してください。',
            'address.required' => '市区町村・番地を入力してください。',
            'tel.required' => '電話番号を入力してください。',
            'tel.regex' => '電話番号は数字とハイフンで入力してください。',
            'timing_of_tomorrow.required' => '日付変更時刻を入力してください。',
            'timing_of_tomorrow.date_format' => '日付変更時刻は00:00形式で入力してください。',
        ];
        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }

    /*
     * ミューテター
     */
    public function getCreateUidAttribute() {
        // 現在のタイムスタンプから基準を引いた値
        $time = decbin((int)round(microtime(true) * 1000) - self::$baseTimestamp);

        // worker id(7 bits, 0-127)
        $worker = sprintf('%07s', decbin(self::$workerId));

        // sequence number(15 bits, 0-32767)
        $random = sprintf('%015s', decbin(mt_rand(0, 32768 - 1)));

        $bit = $time . $worker . $random;

        return bindec($bit);
    }
    public function getUidAttribute($value)
    {
        return (string)$value;
    }

    public static function getTimingOfTomorrow($workplace_uid = NULL) {

        if (!empty($workplace_uid)) {
            $workplace = Workplace::where('uid', $workplace_uid)->first();
        } else {
            $workplace = Workplace::where('uid', Auth::user()->workplace_uid)->first();
        }
        return $workplace->timing_of_tomorrow;
    }

    /*
     * スコープ
     */
    public function scopeOfWorkplace($query, $workplace_uid=NULL)
    {
        if (empty($workplace_uid)) {
            $workplace_uid = Auth::user()->workplace_uid;
        }
        return $query->where('uid', $workplace_uid);
    }

}

