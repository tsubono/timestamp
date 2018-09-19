<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use Log;
use Auth;


class PaymentDeduction extends Model
{

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_deductions';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "workplace_uid","employee_uid","period","health_insurance","care_insurance","welfare_pension","employment_insurance",
        "social_insurance","income_tax","inhabitant_tax","free_name_1","free_value_1","free_name_2","free_value_2",
        "total","created_at","updated_at"
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
    public function getTotal () {
        $total = $this->health_insurance+$this->care_insurance+$this->welfare_pension+$this->employment_insurance+
            $this->social_insurance+$this->income_tax+$this->inhabitant_tax+$this->free_value_1+$this->free_value_2;

        return $total;
    }

}

