<?php
namespace App\Models;

use App\Http\Controllers\PlanController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;

class Plan extends Model
{

    protected $connection = 'timestamp-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'plans';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    public static function monthly_amount($id) {
        $plan = Plan::where('id', $id)->first();
        return $plan->monthly_price;
    }

    public static function getName($id) {
        $plan = Plan::where('id', $id)->first();
        return $plan->name;
    }
    public static function getDetail($id) {
        $plan = Plan::where('id', $id)->first();
        return $plan->name. "(従業員上限 ".$plan->employee_limit."人 : 月額".$plan->monthly_price."円)";
    }



}

