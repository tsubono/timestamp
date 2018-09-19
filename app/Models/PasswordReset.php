<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{

    protected $connection = 'customer-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'password_resets';

    protected $primaryKey = 'token';

    /**
     * @var string
     */
    protected $fillable = [
        "email","login_id","token","type"
    ];

}

