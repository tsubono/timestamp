<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use Log;
use Auth;


class Affiliation extends Model
{

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'affiliations';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        "workplace_uid","employee_uid","current_clock_in"
    ];

    public function employee() {
        return $this->hasOne('App\Models\Employee','uid','employee_uid');
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

}

