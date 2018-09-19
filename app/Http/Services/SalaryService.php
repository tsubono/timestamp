<?php
namespace App\Http\Services;

use App\Models\Salary;
use App\Models\Workplace;
use DB;
use Log;
use Auth;
use Exception;

/*
 * 給与設定関連を扱うサービス
 */

class SalaryService
{
    /*
     * 給与設定登録
     */
    public static function save($data)
    {

        try {
            DB::connection('customer-db')->transaction(function () use ($data) {

                foreach ($data['records'] as $idx => $item) {

                    $salary = new Salary();
                    $salary->employee_uid = $data['uid'];
                    $salary->apply_date = $data['apply_date'];
                    $salary->hourly_pay = $item['hourly_pay'];

                    //一番はじめはデフォルト固定
                    if ($idx == 0) {
                        $salary->start_time = '00:00';
                    } else {
                        $salary->start_time = $item['start_time'];
                    }
                    $salary->save();
                }
            });

        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /*
     * 給与設定バリデーション
     */
    public static function validate($data) {

        $error = "";
        $result = true;

        foreach ($data as $idx => $item) {
            if (empty($item['hourly_pay']) || ($idx!=0 && empty($item['start_time']))) {
                $error = '未入力の項目があります。';
            } elseif ($item['hourly_pay'] < 0) {
                $error = '時給(円)の形式が不正です。';
//            } elseif ($idx!=0 && count(explode(":", $item['start_time']))<2) {
//                $error = '変更時刻の形式が不正です。';
//            } elseif ($idx!=0 && !self::checktime(explode(":", $item['start_time'])[0], explode(":", $item['start_time'])[1])) {
//                $error = '変更時刻の形式が不正です。';
            }
        }

        if (!empty($error)) {
            $result = false;
        }

        return [
            'result' => $result,
            'message' => $error
        ];

    }

    private static function checktime($hour, $min) {
        if ($hour < 0 || $hour > 23 || !is_numeric($hour)) {
            return false;
        }
        if ($min < 0 || $min > 59 || !is_numeric($min)) {
            return false;
        }
        return true;
    }

    /*
     * 給与削除
     */
    public static function delete($data)
    {
        try {
            $salaries = Salary::where('apply_date', $data['apply_date'])->where('employee_uid', $data['uid'])->get();
            foreach ($salaries as $salary) {
                $salary->delete();
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}