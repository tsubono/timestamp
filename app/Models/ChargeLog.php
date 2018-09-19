<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;

class ChargeLog extends Model
{

    protected $connection = 'timestamp-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'change_logs';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "id","charge_id","payment_charge_id","payment_customer_id","amount","refunded","captured",
        "failure_code","failure_message"
    ];

}

