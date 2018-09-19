<div ng-app="timestamp" class="modal inmodal" id="timecardEditModal" tabindex="-1" role="dialog" aria-hidden="true"
     style="display: none;">
    {{--<form ng-controller="TimecardEditFormController" >--}}
    <form ng-controller="TimecardEditFormController">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInDown">
                <div class="modal-header">
                    <h4 class="modal-title">タイムカード編集</h4>
                </div>
                <div class="modal-body">
                    {{--<div class="alert alert-danger animated flash hide">ERROR MESSAGE</div>--}}
                    <alert style="color: red;" ng-show="alert" type="@{{alert.type}}">@{{alert.msg}}</alert>
                    <br>
                    <table class="table table-bordered" ng-repeat="(timecardIndex, timecard) in timecards">
                        <thead>
                        <tr>
                            <th style="width: 100px; text-align: right;background: #f8fafb;">出勤</th>
                            <td style="background: #f8fafb;">
                                <div class="dropdown">
                                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" data-target="#" href="#">
                                        <div class="input-group">
                                            <input type="text" class="form-control" data-ng-model="timecard.startTime" data-modelType="Y-m-d">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                        <datetimepicker data-ng-model="timecard.startTime" data-datetimepicker-config="{'modelType':'YYYY-MM-DD HH:mm'}"/>
                                    </ul>
                                </div>
                                {{--<input type="text" ng-model="timecard.startTime" class="form-control">--}}
                            </td>
                            <td style="width: 100px;background: #f8fafb;">
                                <button type="button" ng-click="removeTimecard(timecardIndex)"
                                        ng-if="timecards.length > 1" class="btn btn-default btn-block">
                                    &times;出勤を削除
                                </button>
                            </td>
                        </tr>
                        </thead>
                        <tbody ng-repeat="(restIndex, rest) in timecard.rests">
                        <tr ng-if="rest.startTime !=''">
                            <th style="width: 100px; text-align: right;">休憩入り</th>
                            <td>
                                <div class="dropdown">
                                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" data-target="#" href="#">
                                        <div class="input-group">
                                            <input type="text" class="form-control" data-ng-model="rest.startTime">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                        <datetimepicker data-ng-model="rest.startTime" data-datetimepicker-config="{'modelType':'YYYY-MM-DD HH:mm'}"/>
                                    </ul>
                                </div>
                                {{--<input type="text" ng-model="rest.startTime" class="form-control">--}}
                            </td>
                            <td rowspan="2" style="width: 100px;">
                                <button type="button" ng-click="removeRest(timecardIndex, restIndex)"
                                        class="btn btn-default btn-block">
                                    &times;休憩を削除
                                </button>
                            </td>
                        </tr>
                        <tr ng-if="rest.startTime !=''">
                            <th style="width: 100px; text-align: right;">休憩戻り</th>
                            <td>
                                <div class="dropdown">
                                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" data-target="#" href="#">
                                        <div class="input-group">
                                            <input type="text" class="form-control" data-ng-model="rest.endTime">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                        <datetimepicker data-ng-model="rest.endTime" data-datetimepicker-config="{'modelType':'YYYY-MM-DD HH:mm'}"/>
                                    </ul>
                                </div>
                                {{--<input type="text" ng-model="rest.endTime" class="form-control">--}}
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th style="width: 100px; text-align: right;"></th>
                            <td>
                                <button type="button" ng-click="addRest(timecardIndex)"
                                        class="btn btn-default btn-block">
                                    ＋休憩を追加
                                </button>
                            </td>
                            <td style="width: 100px;"></td>
                        </tr>
                        <tr>
                            <th style="width: 100px; text-align: right;">退勤</th>
                            <td>
                                <div class="dropdown">
                                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" data-target="#" href="#">
                                        <div class="input-group">
                                            <input type="text" class="form-control" data-ng-model="timecard.endTime" data-modelType="Y-m-d">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                        <datetimepicker data-ng-model="timecard.endTime" data-datetimepicker-config="{'modelType':'YYYY-MM-DD HH:mm'}"/>
                                    </ul>
                                </div>
                                {{--<input type="text" ng-model="timecard.endTime" class="form-control">--}}
                            </td>
                            <td style="width: 100px;"></td>
                        </tr>
                        <tr ng-if="$last">
                            <th style="width: 100px; text-align: right;"></th>
                            <td>
                                <button type="button" ng-click="addTimecard()"
                                        class="btn btn-default btn-block">
                                    ＋出勤を追加
                                </button>
                            </td>
                            <td style="width: 100px;"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" ng-click="submit()">更新</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    angular
            .module('timestamp', ['ui.bootstrap.datetimepicker'])
            .controller('TimecardEditFormController', ['$scope', '$http',  function ($scope, $http) {

                /*
                 * タイムカード詳細初期化
                 */
                $scope.timecards = [{
                    startTime: null,
                    endTime: null,
                    rests: [{
                        startTime: null,
                        endTime: null
                    }]
                }];

                /*
                 * 非同期通信でタイムカード詳細取得
                 */
                $http({
                    method: 'POST',
                    url: '/timecard/get_timecard_details',
                    data: {
                        "employee_uid": "{{ $employee->uid }}",
                        "timecard_id": "{{ $timecard->id }}",
                        "_token": "{{ csrf_token() }}"
                    }
                }).success(function (data, status, headers, config) {

                    $scope.timecard_id = data['id'];

                    var idx = 0;
                    var rest_idx = 0;
                    var tmp = [];

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

                    $scope.timecards = tmp;

                }).error(function (data, status, headers, config) {
                    alert('Error : ' + errorThrown);
                });


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
                        url: '/timecard/edit_timecard',
                        data: {
                            "details": $scope.timecards,
                            "employee_uid": "{{ $employee->uid }}",
                            "id": "{{ $timecard->id }}",
                            "_token": "{{ csrf_token() }}",
                            "ajaxFlg" : true,
                            "employee_flg" : false
                        }
                    }).success(function (data) {
                        if (!data.employee_flg) {
                            location.href = "/timecard/" + data.id;
                        } else {
                            location.href = "/employee/timecard/" + data.employee_uid + "/" + data.id;
                        }
                    }).error(function (data, status, headers, config) {
                        $scope.alert = {
                            type: "danger",
                            msg: data.msg
                        }
                    });
                }
            }]);

</script>

