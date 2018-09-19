var module = angular.module("recorder",[
    "ui.bootstrap",
    "ui.bootstrap.datetimepicker"
]);

//従業員詳細モーダル
var employeeModalFactory = function($modal,data){
    var modalInstance = $modal.open({
        animation: true,
        templateUrl: '/tmpl/employeeDetailModal.html',
        controller: 'EmployeeModalController',
        size: "lg",
        resolve: {
            data:function(){
                return data;
            }
        }
    });
    return modalInstance;
};

//タイムカード変更依頼モーダル
var timecardFormModalFactory = function($modal, data){
    var modalInstance = $modal.open({
        animation: true,
        templateUrl: '/tmpl/timecardForm.html',
        controller: 'TimecardFormModalController',
        size: "md",
        resolve: {
            data:function(){
                return data;
            }
        }
    });
    return modalInstance;
};

//レコーダーTop画面
module.controller('RecorderController', ['$scope', '$modal',

    //従業員詳細モーダル設定
    function ($scope, $modal) {
        $scope.openEmployeeModal = function(recorder_uid, employee_uid){
            employeeModalFactory($modal,{
                recorder_uid: recorder_uid,
                employee_uid: employee_uid
            }).result.then(function (selectedItem) {
            }, function () {
            });
        };
    }
]);

//従業員詳細モーダル
//data = { recorder_uid:'', employee_uid:'' }
module.controller('EmployeeModalController', ['$scope', '$http', '$modalInstance', '$modal', 'data',
    function ($scope, $http, $modalInstance, $modal, data) {

        /*
         * 初期化
         */
        $scope.employee = {
            'name': null,
            'icon': null,
            'status': null,
            'uid': null
        };

        $scope.timecard_id = 0;

        $scope.control = {
            'clocking_in': false,
            'break_in': false,
            'break_out': false,
            'clocking_out': false
        };

        $scope.resolving = true;

        $scope.detail = null;
        $scope.year = (new Date()).getFullYear();
        $scope.month = (new Date()).getMonth() + 1;
        $scope.date = null;

        $scope.recorder_uid = data.recorder_uid;
        $scope.employee_uid = data.employee_uid;
        $scope.update_url = '/timestamp/api/'+data.recorder_uid+'/update_status';
        $scope.token = null;


        /*
         * モーダル表示時に実行(init)
         * 従業員詳細を取得する
         */
        $scope.getEmployeeDetail = function () {

            $http({
                method: 'POST',
                url: '/timestamp/api/' + $scope.recorder_uid + '/get_employee_details',
                data: {
                    "recorder_uid": $scope.recorder_uid,
                    "employee_uid": $scope.employee_uid
                }
            }).success(function (res, status, headers, config) {
                $scope.employee = res['employee'];
                $scope.timecard_id = res['timecard_id'];
                $scope.control = res['control'];
                $scope.resolving = false;
                $scope.token = res['token'];

            }).error(function (data, status, headers, config, errorThrown) {
                //alert('Error : ' + errorThrown);
            });

            //タイムカード一覧を取得する
            $scope.getTimecardList();
        };

        /*
         * タイムカード一覧を取得する
         */
        $scope.getTimecardList = function () {

            $http({
                method: 'POST',
                url: '/timestamp/api/' + $scope.recorder_uid + '/get_timecard_lists',
                data: {
                    "recorder_uid": $scope.recorder_uid,
                    "employee_uid": $scope.employee_uid,
                    "year": $scope.year,
                    "month": $scope.month
                }
            }).success(function (res, status, headers, config) {
                $scope.dateList = res['dateList'];

            }).error(function (data, status, headers, config, errorThrown) {
                //alert('Error : ' + errorThrown);
            });
        };

        /*
         * 次の月を表示
         */
        $scope.nextMonth = function () {
            if ($scope.month != 12) {
                $scope.month++;
            } else {
                $scope.month = 1;
                $scope.year++;
            }
            //タイムカード一覧を再取得
            $scope.getTimecardList();
        };
        /*
         * 前の月を表示
         */
        $scope.prevMonth = function () {
            if ($scope.month != 1) {
                $scope.month--;
            } else {
                $scope.month = 12;
                $scope.year--;
            }
            //タイムカード一覧を再取得
            $scope.getTimecardList();

        };

        /*
         * 時間フォーマット
         */
        $scope.getTime = function (time) {
            if (time != "" && time != undefined) {
                return moment(time).format("HH:mm") == "Invalid date" ? "" : moment(time).format("HH:mm")
            }
        };

        /*
         * ステータスフォーマット
         */
        $scope.getLabel = function (label) {
            var labelMap = {
                "0": "出勤",
                "1": "休憩入",
                "2": "休憩戻",
                "3": "退勤"
            };
            return labelMap[label] || label;
        };

        /*
         * タイムカード詳細設定
         */
        $scope.setDetail = function (date, employee_uid, add_request_flg) {
            $scope.detail = {
                date: date,
                employee_uid: employee_uid,
                add_request_flg: add_request_flg
            };
            if ($scope.dateList[date]) {
                $scope.detail.records = $scope.dateList[date].records;
                $scope.detail.timecardId = $scope.dateList[date].timecardId;
                $scope.detail.add_request_flg = $scope.dateList[date].add_request_flg;
                $scope.detail.change_request_flg = $scope.dateList[date].change_request_flg;
                $scope.detail.clock_out_flg = $scope.dateList[date].clock_out_flg;
            }
        };

        // $scope.sendDeleteRequest = function(){
        //     console.log($scope.form);
        //     timecardDeleteModalFactory($modal).result.then(function(){
        //         //var url = "/timestamp/api/change/request/"+ids.userId + "/delete/" + $scope.detail.timecardId;
        //         var url = "/timestamp/api/"+$scope.recorderUid+"/delete_request/"+$scope.detail.timecardId;
        //
        //
        //         var http = $http.post(url);
        //         http.success(function(data){
        //             showMessage("#deleteRequestTie");
        //         });
        //         http.error(function(){
        //             alert("削除リクエストを送信できませんでした。");
        //         })
        //     });
        // };

        /*
         * 追加依頼送信
         */
        $scope.sendAddRequest = function (date) {
            $scope.detail = {
                date: date,
                employee_uid: $scope.employee_uid
            };
            timecardFormModalFactory($modal,{
                recorder_uid: $scope.recorder_uid,
                date: date,
                employee_uid: $scope.employee_uid,
                timecard_id: 0
            }, {
                date: date,
                employee_uid: $scope.employee_uid,
                records: [
                    {
                        type: "clock_in",
                        date_time: null
                    }
                ]
            }).result.then(function () {
                alert("追加依頼を送信しました。")
            })
        };

        /*
         * 変更依頼送信
         */
        $scope.sendUpdateRequest = function (date) {

            timecardFormModalFactory($modal, {
                recorder_uid: $scope.recorder_uid,
                date: date,
                employee_uid: $scope.employee_uid,
                timecard_id: $scope.detail.timecardId
            }, $scope.detail).result.then(function () {
                //alert("編集依頼を送信しました。")
            })
        };

        $scope.close = function () {
            $modalInstance.dismiss();
        };
    }]);

/*
 * タイムカード追加・変更依頼モーダル
 */
module.controller('TimecardFormModalController', ['$scope', '$http', '$modalInstance', '$modal', 'data',
    function ($scope, $http, $modalInstance, $modal, data) {

        /*
         * 初期化
         */
        // $scope.timecards = [{
        //     startTime: null,
        //     endTime: null,
        //     rests: [{
        //         startTime: null,
        //         endTime: null
        //     }]
        // }];
        $scope.alert = null;
        $scope.resolving = true;
        $scope.timecard_id = data.timecard_id;
        $scope.employee_uid = data.employee_uid;
        $scope.error = null;
        $scope.date = data.date;

        /*
         * 非同期通信でタイムカード詳細取得
         */
        $scope.getTimecardForEdit = function() {
            $http({
                method: 'POST',
                url: '/timestamp/api/' + data.recorder_uid + '/get_timecard_details',
                data: {
                    "employee_uid": $scope.employee_uid,
                    "timecard_id": $scope.timecard_id,
                    "date": $scope.date
                }
            }).success(function (data, status, headers, config) {

                // $scope.timecard_id = data['id'];

                var idx = 0;
                var rest_idx = 0;
                var tmp = [];

                if (data['details'] != undefined ) {
                    if (data['details'].length > 0) {
                        for (var i = 0; i < data['details'].length; i++) {
                            //出勤
                            if (data['details'][i]['type'] == 0) {
                                if (i == 0) {
                                    tmp[idx] = {
                                        startTime: null,
                                        endTime: null,
                                        rests: [{
                                            startTime: '',
                                            endTime: ''
                                        }]
                                    };

                                    tmp[idx]['startTime'] = data['details'][i]['time'];
                                } else {
                                    idx++;
                                    tmp[idx] = {
                                        startTime: null,
                                        endTime: null,
                                        rests: [{
                                            startTime: '',
                                            endTime: ''
                                        }]
                                    };
                                    tmp[idx]['startTime'] = data['details'][i]['time'];
                                }

                                //休憩入り
                            } else if (data['details'][i]['type'] == 1) {
                                if (tmp[idx]['rests'][0]['startTime'] == '') {
                                    tmp[idx]['rests'][0]['startTime'] = data['details'][i]['time'];
                                } else {
                                    rest_idx++;
                                    tmp[idx]['rests'][rest_idx] = {
                                        startTime: '',
                                        endTime: ''
                                    };
                                    tmp[idx]['rests'][rest_idx]['startTime'] = data['details'][i]['time'];
                                }

                                //休憩戻り
                            } else if (data['details'][i]['type'] == 2) {
                                tmp[idx]['rests'][rest_idx]['endTime'] = data['details'][i]['time'];

                                //退勤
                            } else if (data['details'][i]['type'] == 3) {
                                tmp[idx]['endTime'] = data['details'][i]['time'];
                            }
                        }
                    } else {
                        tmp[0] = {
                            startTime: data['default_date_time'],
                            endTime:  data['default_date_time'],
                            rests: [{
                                startTime:  data['default_date_time'],
                                endTime:  data['default_date_time']
                            }]
                        };
                    }
                    $scope.timecards = tmp;
                }


            }).error(function (data, status, headers, config) {
                //alert('Error : ' + errorThrown);
            });
        };

        /*
         * 出勤追加
         */
        $scope.addTimecard = function () {
            this.timecards.push({
                startTime: null,
                endTime: null,
                rests: [{
                    startTime: '',
                    endTime: ''
                }]
            });
        };

        /*
         * 出勤削除
         */
        $scope.removeTimecard = function (timecardIndex) {
            this.timecards.splice(timecardIndex, 1);
        };

        /*
         * 休憩追加
         */
        $scope.addRest = function (timecardIndex) {
            this.timecards[timecardIndex].rests.push({
                startTime: null,
                endTime: null
            });
        };

        /*
         * 休憩削除
         */
        $scope.removeRest = function (timecardIndex, restIndex) {
            this.timecards[timecardIndex].rests.splice(restIndex, 1);
        };

        /*
         * 更新実行
         */
        $scope.submit = function() {

            $http({
                method: 'POST',
                url: '/timestamp/api/' + data.recorder_uid + '/change_request',
                data: {
                    "details": $scope.timecards,
                    "date": $scope.date,
                    "euid": $scope.employee_uid,
                    //"id": $scope.timecard_id,
                    "employee_flg" : false,
                    "timecard_id" : $scope.timecard_id,
                }
            }).success(function (data) {

                showMessage("#changeRequestTie");
                $modalInstance.dismiss();
                setTimeout("location.reload()",1500);

            }).error(function (data, status, headers, config) {

                $scope.alert = {
                    type: "danger",
                    msg: data.msg
                }
            });
        };

        $scope.close = function(){
            $modalInstance.dismiss();
        }
    }]);

