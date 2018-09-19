<?php

namespace App\Http\Controllers\Api;


/*
 *  トークンが不正でないかチェックする
 */
use App\Models\Recorder;

trait IsRecorderTrait {

    protected function isRecorder($token) {
        $recorder = Recorder::where('token', $token)->first();

        if (empty($recorder)) {
            return false;
        }

        return $recorder;
    }

}