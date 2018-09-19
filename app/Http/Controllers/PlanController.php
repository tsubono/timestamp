<?php

namespace App\Http\Controllers;

use App\Http\Services\EmployeeService;
use App\Http\Services\PlanService;
use App\Models\Affiliation;
use App\Models\Plan;
use App\Models\Workplace;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Log;

/*
 * プランコントローラー
 */
class PlanController extends Controller
{
    use ExpirationCheckTrait;

    /*
     * プラン情報表示
     */
    public function getIndex()
    {
        //有効期限チェック
        $res = $this->expirationCheck();
        if (!$res) {
            return redirect('/');
        }

        $messages = $this->getMessages();
        $params = [
            'workplace' => Workplace::ofWorkplace(Auth::user()->workplace_uid)->first(),
            'employee_count' => EmployeeService::getEmployeeCount(),
            'plans' => Plan::all()
        ];

        return view('plan.index', array_merge($messages, $params));
    }

    /*
     * プラン情報更新処理
     */
    public function postEditPlan($subdomain, Request $request) {

        //更新処理
        $res = PlanService::update($subdomain, $request->get('plan'));
        if ($res) {
            session(['message' => 'プランを更新しました。']);
        } else {
            session(['message' => 'プランを更新できませんでした。']);
        }

        return [
            'status_code' => 200,
            'payloads' => ['location' => '/plan'],
        ];
    }

    /*
     * 決済金額取得
     */
    public function ajaxGetAmount(Request $request) {

        $data = $request->all();

        $workplace = Workplace::ofWorkplace(Auth::user()->workplace_uid)->first();

        $amount = PlanService::getAmount($data['plan_id'], $data['old_plan_id'], $workplace->charged_flg);

        echo json_encode($amount);
    }

    /*
     * セッションメッセージを取得
     */
    private function getMessages() {
        $message = session('message');
        $err_message = session('err_message');
        session()->forget('message');
        session()->forget('err_message');

        return compact("message","err_message");
    }

}
