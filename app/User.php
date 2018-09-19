<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;


class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

    protected $connection = 'customer-db';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "id","name","login_id","email","password","enable_flg","owner_flg","workplace_uid","remember_token"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /*
     * スコープ
     */
    public function scopeOfWorkplace($query, $workplace_uid)
    {
        return $query->where('workplace_uid', $workplace_uid);
    }
    public function scopeNotOwner($query)
    {
        return $query->whereNull('owner_flg');
    }


    /*
     * バリデート(ユーザー)
     */
    public static function validate_user($data) {
        $rules = [
            'login_id' => 'required|regex:/^[a-zA-Z0-9]+$/|between:3,16',
            'password' => 'required|regex:/^[a-zA-Z0-9]+$/|between:8,32|confirmed',
        ];
        $messages = [
            'login_id.required'  => 'ユーザーIDを入力してください。',
            'login_id.between'  => 'ユーザーIDは3～16文字で入力してください。',
            'login_id.regex'  => 'ユーザーIDは半角英数字で入力してください。',
            'password.required'  => 'パスワードを入力してください。',
            'password.between'  => 'パスワードは8～32文字で入力してください。',
            'password.regex'  => 'パスワードは半角英数字で入力してください。',
            'password.confirmed'  => 'パスワードとパスワード(確認用)が一致しません。',
        ];
        $validator = Validator::make($data, $rules, $messages);

        return $validator;
    }

    /*
     * 重複確認
     */
    public static function isEmail($email, $id) {
        $isData = User::where('id','<>',$id)->where('email', $email)->first();
        return $isData;

    }
    public static function isLoginId($login_id, $id) {
        $isData = User::where('id','<>',$id)->where('login_id', $login_id)->first();
        return $isData;
    }
}
