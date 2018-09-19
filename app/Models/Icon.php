<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{

    protected $connection = 'timestamp-db';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'icons';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    public static function getPath($name, $place) {

        $path = "/profile_icon/".$place."/".$name;

        return $path;

    }
}

