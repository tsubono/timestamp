"use strict"

var statusModalFactory = function($modal,ids){
    var modalInstance = $modal.open({
        animation: true,
        templateUrl: '/tmpl/statusModal.html',
        controller: 'StaffStatusModalController',
        size: "lg",
        resolve: {
            ids:function(){
                return ids;
            }
        }
    });
    return modalInstance;
};

var timecardFormModalFactory = function($modal,changeRequest,records){
    var modalInstance = $modal.open({
        animation: true,
        templateUrl: '/tmpl/timecardForm.html',
        controller: 'TimecardFormModalController',
        size: "md",
        resolve: {
            changeRequest:function(){ return changeRequest },
            records : function(){return records}
        }
    });
    return modalInstance;
};

var timecardDeleteModalFactory = function($modal,changeRequest,records){
    var modalInstance = $modal.open({
        animation: true,
        templateUrl: '/tmpl/timecardDeleteConfirm.html',
        controller: 'TimecardDeleteModalController',
        size: "md",
        resolve: {
            changeRequest:function(){ return changeRequest },
        }
    });
    return modalInstance;
};

var module = angular.module("recorder",[
    "ui.bootstrap",
  "ui.bootstrap.datetimepicker"
]);

module.controller("RootController",[
    "$scope","$modal","$http",
    function($scope,$modal,$http){
    $scope.recorderToken = null;
    $scope.openStatusModal = function(uid){
        statusModalFactory($modal,{
            recorderToken: $scope.recorderToken,
            userId: uid
        }).result.then(function (selectedItem) {
            //$scope.selected = selectedItem;
        }, function () {
        });
    };

    $scope.init = function(id){
        $scope.recorderToken = id;
    }
}]);


module.controller("StaffStatusModalController",[
    "$scope","$http","$modalInstance","$modal","ids",
    function($scope,$http,$modalInstance,$modal,ids){
        $scope.resolving = true;

        $scope.detail = null;
        $scope.year = (new Date()).getFullYear();
        $scope.month = (new Date()).getMonth()+1;
        $scope.date = null;
        $scope.euid = null;

        $scope.init = function(){
            var url = "/timestamp/api/"+ids.recorderToken+"/employee/"+ids.userId;
            $scope.recorderUid = ids.recorderToken;

            var http = $http.post(url);
            http.success(function(data){
                $scope.resolving = false;
                $scope.employee = data.employee;
                $scope.updateLink = data.updateLink;
                $scope.token = data.token;
                $scope.timecard_id = data.timecard_id;
                $scope.recorder = data.recorder;
            });
            $scope.load();
        };
        $scope.nextMonth = function(){
           if($scope != 12){
             $scope.month ++;
           }else{
             $scope.month = 1;
             $scope.year ++;
           }
           $scope.load();
        };
        $scope.prevMonth = function(){
          if($scope != 1){
            $scope.month --;
          }else{
            $scope.month = 12;
            $scope.year --;
          }
          $scope.load();

        };

        $scope.load = function(){
          var url = "/timestamp/api/"+ids.recorderToken+"/timecard/detail/"+ids.userId;
          var http = $http.post(url,{
            year  : $scope.year,
            month : $scope.month
          });
          http.success(function(data){
            $scope.dateList = data.dateList;
          })
        };

        $scope.getTime = function(time){
            if (time!="" && time!=undefined) {
                return moment(time).format("HH:mm")=="Invalid date"?"":moment(time).format("HH:mm")
            }
        };
        $scope.getLabel = function(label){
          var labelMap = {
            "0" : "出勤",
            "1" : "休憩入",
            "2" : "休憩戻",
            "3" : "退勤"
          };
          return labelMap[label] || label;
        };

        $scope.setDetail = function(datetime, euid){
          $scope.detail = {
            date: datetime,
            euid: euid
          };
          if($scope.dateList[datetime]){
            $scope.detail.records = $scope.dateList[datetime].records;
            $scope.detail.timecardId = $scope.dateList[datetime].timecardId;
            $scope.detail.change_request_flg = $scope.dateList[datetime].change_request_flg;
          }
        };

        $scope.sendDeleteRequest = function(){
          console.log($scope.form);
            timecardDeleteModalFactory($modal).result.then(function(){
            //var url = "/timestamp/api/change/request/"+ids.userId + "/delete/" + $scope.detail.timecardId;
            var url = "/timestamp/api/"+$scope.recorderUid+"/delete_request/"+$scope.detail.timecardId;


              var http = $http.post(url);
            http.success(function(data){
              showMessage("#deleteRequestTie");
            });
            http.error(function(){
              alert("削除リクエストを送信できませんでした。");
            })
          });
        };

        $scope.sendCreateRequest = function(datetime, euid){
            $scope.detail = {
                date: datetime,
                euid: euid
            };
          timecardFormModalFactory($modal,{
            "userId":ids.userId,
            "recorderUid":ids.recorderToken
          },{
            date: datetime,
            euid: euid,
            records:[
              {
                type:"clock_in",
                date_time: null
              }
            ]
          }).result.then(function(){
            //alert("追加依頼を送信しました。")
          })
        };

        $scope.sendUpdateRequest = function(datetime, euid){

          timecardFormModalFactory($modal,{
            "userId":ids.userId,
            "recorderUid":ids.recorderToken
          },$scope.detail).result.then(function(){
            //alert("編集依頼を送信しました。")
          })
        };

        $scope.close = function(){
          $modalInstance.dismiss();
        }


}]);

module.controller("TimecardFormModalController",[
  "$scope","$http","$modalInstance","$modal","changeRequest","records",
  function($scope,$http,$modalInstance,$modal,changeRequest,records){

    $scope.alert = null;

    $scope.resolving = true;

    $scope.changeRequest = changeRequest;

    $scope.records = records;

    $scope.error = null;

    console.log(changeRequest,records);
    // $scope.init = function(){
    //
    // };

    $scope.addRestForm = function(){
      $scope.records.records.push({
        time:null,type:null
      });
    };

    $scope.deleteRestForm = function(){
      $scope.records.records.splice(this.$index,1)
    };

    $scope.getDime = function(){
      $scope.records.records.splice(this.$index,1)
    };

    $scope.rowUp = function(){
      $scope.records.records.splice(this.$index-1,2,$scope.records.records[this.$index],$scope.records.records[this.$index-1])
    };

    $scope.rowDown = function(){
      $scope.records.records.splice(this.$index,2,$scope.records.records[this.$index+1],$scope.records.records[this.$index])
    };

    $scope.submit = function(){
      console.log($scope.records);
      // if(records && records.timecardId){
      //   var url = "/api/change/request/"+changeRequest.userId + "/modify/" + records.timecardId;
      // }else{
      //   var url = "/api/change/request/"+changeRequest.userId + "/add";
      // }
      var url = "/timestamp/api/"+changeRequest.recorderUid+"/change_request/"+records.timecardId;

      var http = $http.post(url,{"rests" : $scope.records});
      http.success(function(data){
        showMessage("#changeRequestTie");
        $modalInstance.dismiss();
          //angular.element('.requestBtn').css('display', 'none');
          setTimeout("location.reload()",1500);

      });
        http.error(function (data, status, headers, config) {
            $scope.alert = {
                type: "danger",
                msg: data.msg
            }
        })
    };
    $scope.close = function(){
      $modalInstance.dismiss();
    }
  }]);

module.controller("TimecardDeleteModalController",[
  "$scope","$http","$modalInstance","$modal",
  function($scope,$http,$modalInstance,$modal){

    $scope.submit = function(){
      $modalInstance.close();
    };

    $scope.close = function(){
      $modalInstance.dismiss();
    }
  }]);



module.config(function(){
    console.log("boot application")
});
