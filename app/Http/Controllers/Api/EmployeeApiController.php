<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\EmployeeService;
use App\Models\Employee;
use App\Models\Icon;
use App\Models\Timecard;
use App\Models\TimecardDetail;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Log;
use Exception;
/*
 * 従業員Apiコントローラー
 */
class EmployeeApiController extends Controller
{
    use IsRecorderTrait;
    /*
     * 従業員一覧を返す
     */
    public function getEmployeeList(Request $request) {

        try {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";
            } else {
                $workplace_uid = $recorder->workplace_uid;
                $employees = EmployeeService::getEmployees($workplace_uid);

                $working_count = 0;
                $waiting_count = 0;

                //$employees['employees']が退職していない従業員一覧
                foreach ($employees['employees'] as $employee) {
                    if (EmployeeService::getCurrentStatus($employee->uid, $workplace_uid) != "待機中") {
                        $response['working_employees'][$working_count] = [];
                        $response['working_employees'][$working_count]['uid'] = $employee->uid;
                        $response['working_employees'][$working_count]['name'] = $employee->name;
                        $response['working_employees'][$working_count]['icon'] = $employee->icon;
                        $response['working_employees'][$working_count]['status'] = EmployeeService::getCurrentStatus($employee->uid, $workplace_uid);
                        $working_count++;

                    } else {
                        $response['waiting_employees'][$waiting_count] = [];
                        $response['waiting_employees'][$waiting_count]['uid'] = $employee->uid;
                        $response['waiting_employees'][$waiting_count]['name'] = $employee->name;
                        $response['waiting_employees'][$waiting_count]['icon'] = $employee->icon;
                        $response['waiting_employees'][$waiting_count]['status'] = '待機中';
                        $waiting_count++;
                    }
                }
            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "従業員取得処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);
    }

    /*
     * 従業員詳細を返す
     */
    public function getEmployeeDetail(Request $request) {

        try {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";
            } else {
                $employee = Employee::where('uid', $request->get('employee_uid'))->first();
                if (empty($employee)) {
                    $response['error'] = [];
                    $response['error']['message'] = "従業員が存在しません。";
                } else {
                    $response['employee'] = [];
                    $response['employee']['uid'] = $employee->uid;
                    $response['employee']['name'] = $employee->name;
                    $response['employee']['icon'] = $employee->icon;
                    $response['employee']['status'] = EmployeeService::getCurrentStatus($employee->uid, $recorder->workplace_uid);
                    $response['employee']['possible_controls'] = $this->getPossibleControls($response['employee']['status']);

                    //現在日のタイムカード取得
                    $timecard = Timecard::where('workplace_uid', $recorder->workplace_uid)->ofEmployee($employee->uid)
                        ->where('date', Carbon::now()->format('Y-m-d'))->first();
                    //最新のタイムカード取得
                    $current = Timecard::getCurrentRecord($employee->uid, $recorder->workplace_uid);

                    if (!empty($current)) {
                        //出勤中の場合
                        if (!TimecardDetail::isClockingOut($current->id)) {
                            $response['timecard_id'] = $current->id;
                        } else {
                            if (!empty($timecard)) {
                                $response['timecard_id'] = $timecard->id;
                            } else {
                                $response['timecard_id'] = 0;
                            }
                        }
                    } else {
                        if (!empty($timecard)) {
                            $response['timecard_id'] = $timecard->id;
                        } else {
                            $response['timecard_id'] = 0;
                        }
                    }
                }

            }

        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "従業員詳細取得処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);

    }

    /*
     * 打刻可能なコントロールを返す
     */
    private function getPossibleControls($status) {

        $res = [];

        switch ($status) {
            case '出勤中':
                $res[] = 'break_in';
                $res[] = 'clocking_out';
                break;
            case '休憩中':
                $res[] = 'break_out';
                break;
            case '待機中':
                $res[] = 'clocking_in';
                break;
        }

        return $res;

    }

    /*
     * アイコン一覧を返す
     */
    public function getEmployeeIconList(Request $request) {

        try {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";
            } else {
                $icons = Icon::all();
                foreach ($icons as $idx => $icon) {
                    $response['icons'][$idx] = [];
                    $response['icons'][$idx]['id'] = $icon->id;
                    $response['icons'][$idx]['name'] = $icon->name;
                }
            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "アイコン取得処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);

    }

    /*
     * 従業員情報を更新する
     */
    public function postEmployeeUpdate(Request $request) {

        try {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";
            } else {
                $employee = Employee::where('uid', $request->get('employee_uid'))->first();
                if (empty($employee)) {
                    $response['error'] = [];
                    $response['error']['message'] = "従業員が存在しません。";
                } else {
                    //更新処理
                    $data = $request->all();
                    //現状アプリから更新できるのはアイコンだけ
                    $data['icon_type'] = 'icon';
                    $employee->fill($data);
                    $employee->save();

                    $response['success'] = "OK";
                }
            }

        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "従業員情報更新得処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);

    }


}
