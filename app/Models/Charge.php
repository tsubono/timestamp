<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Validator;

class Charge extends Model
{

    protected $connection = 'timestamp-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'charges';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "id","contract_id","workplace_uid","workplace_name","amount","status","charge_date"
    ];

}

