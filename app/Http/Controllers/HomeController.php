<?php

namespace App\Http\Controllers;

use App\Models\Workplace;
use Illuminate\Support\Facades\Auth;
use Config;

/*
 * ホーム用コントローラー
 */
class HomeController extends Controller
{
    /**
     * ホーム画面表示
     */
    public function getIndex()
    {
        return view('home',[
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first()
        ]);
    }
}
