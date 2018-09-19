<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use Log;
use Auth;


class PaymentSupply extends Model
{

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_supplys';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "workplace_uid","employee_uid","period","base_salary","traffic_cost","over_cost","unemployment_cost",
        "free_name_1","free_value_1","free_name_2","free_value_2","free_name_3","free_value_3","free_name_4","free_value_4",
        "free_name_5","free_value_5","total","created_at","updated_at"
    ];

    public function employee() {
        return $this->hasOne('App\Models\Employee', 'uid', 'employee_uid');
    }

    public function workplace() {
        return $this->hasOne('App\Models\Workplace', 'uid', 'workplace_uid');
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
    public function scopeOfPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    /*
     * total算出
     */
    public static function getTotal ($payment_supply) {
        $total = $payment_supply->base_salary+$payment_supply->traffic_cost+$payment_supply->over_cost+$payment_supply->unemployment_cost+
            $payment_supply->free_value_1+$payment_supply->free_value_2+$payment_supply->free_value_3+$payment_supply->free_value_4+$payment_supply->free_value_5;

        return $total;
    }
}

