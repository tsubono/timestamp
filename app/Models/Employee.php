<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;
use Log;
use Illuminate\Database\Eloquent\SoftDeletes;
use HTML;
use Illuminate\Support\Facades\Response;

class Employee extends Model
{
    use SoftDeletes;

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employees';

    private static $baseTimestamp = 1335798000000;
    private static $workerId = 1;
    /**
     * @var string
     */
    protected $primaryKey = 'uid';

    protected $fillable = [
        "uid","lname","fname","lname_kana","fname_kana","gender","birthday",
        "icon","icon_type","traffic_cost","joined_date","resigned_date"
    ];

    /*
     * スコープ
     */
    public function scopeOfEmployee($query, $employee_uid)
    {
        return $query->where('uid', $employee_uid);
    }

    /*
     * ミューテター
     */
    public function getNameAttribute() {
        return $this->lname. " ". $this->fname;
    }
    public function getNameKanaAttribute() {
        return $this->lname_kana. " ". $this->fname_kana;
    }
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

}

