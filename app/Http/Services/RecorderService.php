<?php
namespace App\Http\Services;
use App\Http\Requests\RecorderRequest;
use App\Models\Recorder;
use App\Models\Workplace;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Validator;
use DB;
use Auth;
use Exception;
use Log;
/*
 * レコーダー関連を扱うサービス
 */
class RecorderService
{
    /*
     * レコーダー登録
     */
    public static function save($data) {

        try {
            $recorder = new Recorder();
            $data['uid'] = $recorder->create_uid;
            $data['workplace_uid'] = Auth::user()->workplace_uid;
            $data['type'] = 'web';
            $data['token'] = str_random(40);
            $recorder->fill($data);
            $recorder->save();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /*
     * レコーダー更新
     */
    public static function update($data) {
        try {
            $recorder = Recorder::where('uid', $data['uid'])->first();
            $recorder->fill($data);
            $recorder->save();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /*
     * レコーダー削除
     */
    public static function delete($uid) {
        try {
            $recorder = Recorder::where('uid', $uid)->first();
            if (!empty($recorder)) {
                $recorder->delete();
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


}