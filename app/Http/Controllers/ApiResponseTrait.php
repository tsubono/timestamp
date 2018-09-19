<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/*
 * ajaxの応答を作成する
 */
trait ApiResponseTrait {

    protected function responseOk($msg = "response ok",$data=[]){
        $data = array_merge($data,[
            "status" => "OK",
            "msg" => $msg
        ]);
        return new JsonResponse($data);
    }

    protected function responseBad($msg = "response bad"){
        return new JsonResponse([
            "status" => "BAD",
            "msg" => $msg
        ],400);
    }

}