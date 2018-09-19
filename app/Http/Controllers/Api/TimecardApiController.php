<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\ChangeRequestService;
use App\Http\Services\TimecardService;
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
 * 打刻Apiコントローラー
 */
class TimecardApiController extends Controller
{
    use IsRecorderTrait;
    /*
     * 勤怠詳細を返す
     */
    public function getTimecardDetail(Request $request) {

        try {

            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";
            } else {
                $timecard = Timecard::getRecord($request->get('date'), $request->get('employee_uid'), 'asc', $recorder->workplace_uid);
                if (empty($timecard)) {
                    $response['error'] = [];
                    $response['error']['message'] = "タイムカードが存在しません。";
                } else {
                    $records = TimecardService::getRecordsForEdit($timecard->id);
                    foreach ($records as $idx => $record) {
                        $response['records'][$idx]['time'] = $record['time'];
                        $response['records'][$idx]['type'] = $this->getDispLabel($record['type']);
                    }
                    $response['is_clocking_out'] = TimecardDetail::isClockingOut($timecard->id);
                }

            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "勤怠詳細取得処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);
    }

    /*
     * アプリに表示用の文言に変換する
     */
    private function getDispLabel($type) {

        $res = '';

        switch ($type) {
            case '0':
                $res = '出勤';
                break;
            case '1':
                $res = '休入';
                break;
            case '2':
                $res = '休戻';
                break;
            case '3':
                $res = '退勤';
                break;
        }

        return $res;

    }

    /*
     * タイムカードを更新する
     */
    public function postTimecardUpdate(Request $request) {

        try {

            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";
            } else {

                $nowDateTime = Carbon::now()->format('Y-m-d H:i');
                $timecard_id = $request->get('timecard_id');

                $control_id = Config::get('const.control_ids.'.$request->get('control_name'));
                if ($control_id == "") {
                    $response['error'] = [];
                    $response['error']['message'] = 'タイムカード更新処理に失敗しました。';
                } else {
                    //バリデート
                    $error = TimecardService::validateForCreate($timecard_id, $nowDateTime, $control_id, $recorder->workplace_uid);
                    if (!empty($error)) {
                        $response['error'] = [];
                        $response['error']['message'] = $error;
                    }

                    //登録処理
                    $res = TimecardService::save($timecard_id, $request->get('employee_uid'), $nowDateTime, $control_id, $recorder->workplace_uid);
                    if (!$res) {
                        $response['error'] = [];
                        $response['error']['message'] = 'タイムカード更新処理に失敗しました。';
                    } else {
                        $response['success'] = 'OK';
                    }
                }

            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "タイムカード更新処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);
    }

    /*
     * 変更依頼処理
     */
    public function postChangeRequest(Request $request) {

        try {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";
            } else {

                $timecard_id = $request->get('timecard_id');
                $data = $request->all();
                $data['euid'] = $request->get('employee_uid');

                if (empty($timecard_id)) {
                    $timecard_id = 0;
                    $add_flg = true;
                    $data['date'] = TimecardService::getDateByTiming(Carbon::now()->format('Y-m-d H:i'), $recorder->workplace_uid);
                } else {
                    $add_flg = false;
                    $timecard = Timecard::where('id', $timecard_id)->first();
                    if (empty($timecard)) {
                        $response['error'] = [];
                        $response['error']['message'] = "タイムカードが存在しません。";
                        return response()->json($response);
                    }
                    $data['date'] = $timecard->date;
                }
                //バリデート
                $error = ChangeRequestService::validateForEdit($timecard_id, $data, $recorder->workplace_uid, $add_flg);
                if (!empty($error)) {
                    $response['error'] = [];
                    $response['error']['message'] = $error;
                    return response()->json($response);
                }

                //変更依頼テーブルに登録
                $res = ChangeRequestService::update($timecard_id, $data, $recorder->workplace_uid);
                if (!$res) {
                    $response['error'] = [];
                    $response['error']['message'] = "タイムカード変更依頼処理に失敗しました。";
                } else {
                    $response['success'] = 'OK';
                }

            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "タイムカード変更依頼処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);
    }

    /*
     * 現在日時を返す
     */
    public function getTime(Request $request) {
        try {

            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');

            //存在チェック
            $recorder = $this->isRecorder($request->get('token'));
            if (!$recorder) {
                $response['error'] = [];
                $response['error']['message'] = "トークンが不正です。";
            }
        } catch (Exception $e) {
            $response = [];
            $response['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $response['error'] = [];
            $response['error']['message'] = "日時取得処理に失敗しました。";
            return response()->json($response);
        }

        return response()->json($response);

    }

}
