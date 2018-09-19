<?php
namespace App\Http\Services;
use App\User;
use Auth;
use Exception;

/*
 * ユーザー関連を扱うサービス
 */
class UserService
{
    /*
     * ユーザー登録
     */
    public static function save($data) {

        try {
            $user = new User();
            $data['password'] = \Hash::make($data['password']);
            $data['workplace_uid'] = Auth::user()->workplace_uid;
            if (empty($data['enable_flg'])) {
                $data['enable_flg'] = 0;
            }
            $user->fill($data);
            $user->save();

        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /*
     * ユーザー更新
     */
    public static function update($data) {

        try {
            $user = User::where('id', $data['id'])->first();
            if (!empty($user)) {
                $data['password'] = \Hash::make($data['password']);
                if (empty($data['enable_flg'])) {
                    $data['enable_flg'] = 0;
                }
                $user->fill($data);
                $user->save();
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /*
     * ユーザー削除
     */
    public static function delete($id) {
        try {
            $user = User::where('id', $id)->first();
            if (!empty($user)) {
                $user->delete();
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

}