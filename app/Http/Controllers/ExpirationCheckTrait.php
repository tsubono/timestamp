<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 15/07/05
 * Time: 23:52
 */

namespace App\Http\Controllers;

use App\Models\Workplace;
use Carbon\Carbon;
use Auth;

/*
 * 有効期限をチェックする
 */
trait ExpirationCheckTrait {

    protected function expirationCheck(){
        $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();

        if (Carbon::parse($workplace->expiration_date)->addDays('1')->isPast()) {
            return false;
        }
        return true;
    }

}