<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/*----------------- 新規契約 ------------------*/
Route:: group(['prefix' => 'contract', 'middleware' => ['web','guest']], function() {
    Route::get('/', 'ContractController@getContract');
    Route::post('/', 'ContractController@postContract');
    Route::get('/apply', 'ContractController@getContractApply');
    Route::post('/apply', 'ContractController@postContractApply');

    Route::get('/list', 'ContractController@getContractList');
});


/*------------------- 認証 --------------------*/
Route::group(['domain' => '{subdomain}.'.env('APP_URL_DOMAIN','t-stamp.loc'), 'middleware' => ['subdomain_setup']], function() {
    // ログイン・ログアウト
    Route::get('/login', 'Auth\LoginController@getLogin');
    Route::post('/login', 'Auth\LoginController@postLogin');
    Route::get('/logout', 'Auth\LoginController@getLogout');

    // パスワードリマインダー
    Route::post('/send_reset_mail', 'PasswordResetController@postSendEmail');
    Route::get('/reset_password', 'PasswordResetController@showResetForm');
    Route::post('/reset_password', 'PasswordResetController@postResetPassword');
});


/*----------------- ログイン後 管理画面 -----------------*/
Route::group(['domain' => '{subdomain}.'.env('APP_URL_DOMAIN','t-stamp.loc'), 'middleware' => ['auth','subdomain_setup']], function() {

    // ホーム
    Route::get('/', 'HomeController@getIndex');

//    Route::get('/503', function () {
//        return view('errors.503');
//    });
//    Route::get('/500', function () {
//        return view('errors.500');
//    });
//    Route::get('/404', function () {
//        return view('errors.404');
//    });
//    Route::get('/403', function () {
//        return view('errors.403');
//    });

    // オーナー専用画面
    Route::get('/owner', 'OwnerController@getIndex');
    Route::post('/owner/select_workplace', 'OwnerController@postSelectWorkplace');
    Route::post('/owner/add_workplace', 'OwnerController@postAddWorkplace');
    Route::post('/owner/edit_account', 'OwnerController@postEditAccount');
    Route::get('/owner/contract', 'OwnerController@getContract');
    Route::post('/owner/edit_contract', 'OwnerController@postEditContract');
    Route::post('/owner/edit_contract_mail', 'OwnerController@postEditContractMail');

    // タイムカード
    Route::get('/timecard', 'TimecardController@getIndex');
    Route::get('/timecard/{id}', 'TimecardController@getDetail');
    Route::post('/timecard/add_timecard', 'TimecardController@postAddTimecard');
    Route::post('/timecard/edit_timecard', 'TimecardController@postEditTimecard')->middleware('api');
    Route::post('/timecard/delete_timecard', 'TimecardController@postDeleteTimecard')->middleware('api');
    Route::post('/timecard/get_enable_employee', 'TimecardController@ajaxGetEnableEmployee');
    Route::post('/timecard/get_enable_control', 'TimecardController@ajaxGetEnableControl');
    Route::post('/timecard/get_timecard_details', 'TimecardController@ajaxGetTimecardDetails')->middleware('api');

    // 従業員
    Route::get('/employee', 'EmployeeController@getIndex');
    Route::get('/employee/{uid}', 'EmployeeController@getDetail');
    Route::post('/employee/add_employee', 'EmployeeController@postAddEmployee');
    Route::post('/employee/edit_employee', 'EmployeeController@postEditEmployee');
    Route::post('/employee/delete_employee', 'EmployeeController@postDeleteEmployee');
    Route::post('/employee/edit_icon', 'EmployeeController@postEditIcon');
    Route::post('/employee/add_salary', 'EmployeeController@postAddSalary');
    Route::post('/employee/delete_salary', 'EmployeeController@postDeleteSalary');
    Route::post('/employee/add_timecard', 'EmployeeController@postAddTimecard');
    Route::get('/employee/timecard/{employee_uid}/{id}', 'EmployeeController@getDetailTimecard');

    // 勤務場所
    Route::get('/workplace', 'WorkplaceController@getIndex');
    Route::post('/workplace/edit_workplace', 'WorkplaceController@postEditWorkplace');
    Route::post('/workplace/edit_time', 'WorkplaceController@postEditTime');

    // ユーザー
    Route::get('/user', 'UserController@getIndex');
    Route::post('/user/add_user', 'UserController@postAddUser');
    Route::post('/user/edit_user', 'UserController@postEditUser');
    Route::post('/user/delete_user', 'UserController@postDeleteUser');

    // レコーダー
    Route::get('/recorder', 'RecorderController@getIndex');
    Route::post('/recorder/add_recorder', 'RecorderController@postAddRecorder');
    Route::post('/recorder/edit_recorder', 'RecorderController@postEditRecorder');
    Route::post('/recorder/delete_recorder', 'RecorderController@postDeleteRecord');

    // 支払い情報
    Route::get('/payment', 'PaymentController@getIndex');
    Route::post('/payment/edit_payment', 'PaymentController@postEditPayment');

    //プラン設定
    Route::get('/plan', 'PlanController@getIndex');
    Route::post('/plan/edit_plan', 'PlanController@postEditPlan');
    Route::post('/plan/get_amount', 'PlanController@ajaxGetAmount')->middleware('api');

    // 出勤簿
    Route::get('/working_report', 'WorkingReportController@getIndex');
    Route::post('/working_report/export', 'WorkingReportController@postExport');

    // 給与明細
    Route::get('/payment_report', 'PaymentReportController@getIndex');
    Route::post('/payment_report/detail', 'PaymentReportController@postCalcDetail');
    Route::get('/payment_report/detail', 'PaymentReportController@getCalcDetail');
    Route::post('/payment_report/export', 'PaymentReportController@postExport');

    // 変更申請一覧
    Route::get('/change_request', 'ChangeRequestController@getIndex');
    Route::post('/change_request/update', 'ChangeRequestController@postUpdate');


});

/*----------------- web版タイムスタンプ打刻画面 -----------------*/
Route::group(['domain' => '{subdomain}.'.env('APP_URL_DOMAIN','t-stamp.loc'), 'middleware' => ['subdomain_setup', 'recorder']], function() {
    Route::get('/timestamp/{uid}/locked', 'TimestampController@locked');
    Route::post('/timestamp/{uid}/unlock', 'TimestampController@unlock');
    Route::get('/timestamp/{uid}/lock', 'TimestampController@lock');
    Route::post('/timestamp/{uid}/lock', 'TimestampController@lock');
});
Route::group(['domain' => '{subdomain}.'.env('APP_URL_DOMAIN','t-stamp.loc'), 'middleware' => ['subdomain_setup', 'recorder.token']], function() {
    Route::get('/timestamp/{uid}/', 'TimestampController@index');
    Route::get('/timestamp/{uid}/employee/{euid}', 'TimestampController@employee');

    Route::post('/timestamp/api/{uid}/get_employee_details', 'TimestampApiController@ajaxGetEmployeeDetails')->middleware('api');
    Route::post('/timestamp/api/{uid}/get_timecard_lists', 'TimestampApiController@ajaxGetTimecardLists')->middleware('api');
    Route::post('/timestamp/api/{uid}/get_timecard_details', 'TimestampApiController@ajaxGetTimecardDetails')->middleware('api');
    Route::post('/timestamp/api/{uid}/change_request', 'TimestampApiController@postChangeRequest')->middleware('api');
    Route::post('/timestamp/api/{uid}/update_status', 'TimestampApiController@postUpdateStatus')->middleware('api');

//    Route::post('/timestamp/api/{uid}/delete_request/{timecard_id}', 'TimestampApiController@deleteRequest');

});

