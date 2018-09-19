<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;
use Log;
use Illuminate\Database\Eloquent\SoftDeletes;


class Salary extends Model
{
    use SoftDeletes;

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'salaries';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "employee_uid","apply_date","start_time","hourly_pay"
    ];

    public function scopeOfEmployee($query, $employee_uid)
    {
        return $query->where('employee_uid', $employee_uid);
    }

    /*
     * 給与設定一覧を日付ごとにまとめて返す
     */
    public static function getSalaries($employee_uid) {

        $salaries_dates = Salary::where('employee_uid', $employee_uid)
                            ->groupBy('apply_date')->orderBy('apply_date','desc')
                            ->pluck('apply_date')->all();
        $salaries = [];

        foreach ($salaries_dates as $idx => $salaries_date) {
            $salaries[$salaries_date] = [];
            $res = Salary::where('employee_uid', $employee_uid)
                        ->where('apply_date',$salaries_date)->orderBy('start_time','asc')
                        ->get();

            foreach ($res as $salary) {
                //日付ごとにふり分ける
                $salaries[$salaries_date][] = $salary;
            }
        }

        return $salaries;
    }

}

