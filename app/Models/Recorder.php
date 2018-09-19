<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Database\Eloquent\SoftDeletes;


class Recorder extends Model
{
    protected $connection = 'customer-db';

    private static $baseTimestamp = 1335798000000;
    private static $workerId = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'recorders';

    /**
     * @var string
     */
    protected $primaryKey = 'uid';

    protected $fillable = [
        "uid","workplace_uid","type","name","pass_code","token", "terminal_id"
    ];

    /*
     * スコープ
     */
    public function scopeOfWorkplace($query, $workplace_uid)
    {
        return $query->where('workplace_uid', $workplace_uid);
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
}

