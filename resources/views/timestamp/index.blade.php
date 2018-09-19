<!doctype html>
<html ng-app="recorder">
<head>
  <meta charset="UTF-8">

  <title>タイムスタンプ 打刻画面</title>

  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,user-scalable=no">

    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/assets/css/common.css"/>

  <link href="/dakoku/css/reset.css" rel="stylesheet">
  <link href="/dakoku/css/style.css" rel="stylesheet">
  <link href="/dakoku/css/dakoku.css" rel="stylesheet">
  <link href="/dakoku/css/colorbox.css" rel="stylesheet">

  <link rel="stylesheet" href="/css/jquery-ui.css"/>
  <link rel="stylesheet" href="/css/datetimepicker.css"/>
  <link rel="stylesheet" href="/css/custom.css"/>


  <style>
        #recorderHeader li{
            height: 4em;
        }
    </style>
</head>

<body ng-controller="RecorderController">
<div id="wrap">
  <div id="wrapIn">
    <header id="recorderHeader">
      <ul class="clearfix">
        <li><a href="#" onclick="window.location.reload();">画面更新</a></li>
        <li><a href="/timestamp/{{$uid}}/lock">ロックする</a></li>
      </ul>
      <div class="logo"><a href="#"><img src="/dakoku/images/logo.png" alt="logo"/></a></div>
      <p id="fittext"><span id="watch1">0000.00/00</span><span id="watch2">00:00</span><span id="watch3">00</span></p>
    </header>
    <div id="cont" class="clearfix">
      @if(!empty($errors->first('error')))
        <br>
        <p style="color:red;height: 64px;background:lightblue;padding:20px;font-weight:bold;font-size:2em;">{{ $errors->first('error') }}</p>
      @endif
      <div style="height: 100%;">
        <div id="block_1" class="">
          <h2>勤務中</h2>

          <div class="InnerWrap">
            <div class="Inner">
              {{-- 出勤一覧 --}}
              <?php $count = 0; ?>
              @foreach($employees as $employee)
                @if ($employee->current_status=="出勤中" || $employee->current_status=="休憩中")
                  <?php $count+=1 ?>
                  <table>
                    <tr>
                      <td rowspan="2" class="icon">
                        @if (!empty($employee["icon"]))
                          @if ($employee["icon_type"]=="icon")
                            <img alt="" src="{{ \App\Models\Icon::getPath($employee["icon"],'recorder') }}" style="display: block;">
                          @elseif ($employee["icon_type"]=="icon_file")
                            <img alt="" src="{{ asset('storage'.\App\Models\Icon::getPath($employee->icon,'recorder')) }}"
                                 style="display: block;">
                          @endif
                        @endif
                      </td>
                      <td class="name">{{ $employee->name }}</td>
                    </tr>
                    <tr>
                      <td class="status">
                        <a ng-click="openEmployeeModal('{{$recorder->uid}}', '{{$employee->uid}}')" class="btn_content"
                           data-toggle="modal" data-target="#employeeDetailModal"
                        >
                            <img src="/dakoku/images/GUI-PC_29.png" alt="勤務中" class="wink" />
                        </a>
                      </td>
                    </tr>
                  </table>
                @endif
              @endforeach
              <div id="clock_in_count" data-count="{{$count}}"></div>
            </div>
          </div>
        </div>
        <div id="block_2">
          <h2>社員一覧</h2>

          <div class="InnerWrap">
            <div class="Inner">
              {{-- 一覧 --}}
              <?php $count = 0; ?>
              @foreach($employees as $employee)
                @if ($employee->current_status!="出勤中" && $employee->current_status!="休憩中")
                  <?php $count += 1; ?>
                <table class="">
                  <tr>
                    <td rowspan="2" class="icon">
                      @if (!empty($employee["icon"]))
                        @if ($employee["icon_type"]=="icon")
                          <img alt="" src="{{ \App\Models\Icon::getPath($employee["icon"],'recorder') }}" style="display: block;">
                        @elseif ($employee["icon_type"]=="icon_file")
                          <img alt="" src="{{ asset('storage'.\App\Models\Icon::getPath($employee->icon,'recorder')) }}"
                               style="display: block;">
                        @endif
                      @endif
                    </td>
                    <td class="name">{{ $employee['name'] }}</td>
                  </tr>
                  <tr>
                    <td class="status">
                      <a ng-click="openEmployeeModal('{{$recorder->uid}}', '{{$employee->uid}}')" class="btn_content"
                         data-toggle="modal" data-target="#employeeDetailModal"
                      >
                          <img src="/dakoku/images/GUI-PC_03.png" alt="待機中" class="wink" />
                      </a>
                    </td>
                  </tr>
                </table>
                @endif
              @endforeach
              <div id="clock_out_count" data-count="{{$count}}"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="changeRequestTie" class="messageTie">
  変更依頼を送信しました。
</div>

<div id="deleteRequestTie" class="messageTie">
  削除依頼を送信しました。
</div>

{{--@include('timestamp.modal.employee_detail')--}}

{{--メッセージ帯のCSSです。--}}
<style>
  .messageTie{
    position: absolute;
    width: 100%;
    height: 3em;
    background: rgba(49, 112, 143, 0.8);;
    top: 50%;
    margin-top: -1.5em;
    color: white;
    line-height: 3em;
    font-size: 1.5em;
    letter-spacing: 3px;
    text-shadow: 2px 2px 10px black;
    z-index: 100000;
    display:none;
  }
</style>
<script>
  var showMessage = function(id){
    $(id).fadeIn(500);
    setTimeout(function(){
      $(id).fadeOut(500);
    },1500);
  }
</script>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/bower_components/jquery/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.js"></script>
<script src="/bower_components/angular/angular.min.js"></script>
<script src="/bower_components/angular-bootstrap/ui-bootstrap.min.js"></script>
<script src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.js"></script>
<script src="/bower_components/moment/min/moment.min.js"></script>
<script src="/assets/js/webRecorder.js"></script>
<script src="/dakoku/js/smartRollover.js"></script>
<script src="/dakoku/js/heightLine.js"></script>
<script src="/dakoku/js/jquery.fittext.js"></script>
<script src="/dakoku/js/jquery.colorbox-min.js"></script>

<!-- Datetime Plugin JavaScript -->
<script src="/js/datetimepicker.js"></script>
<script src="/js/datetimepicker.templates.js"></script>


<script>
  $(function () {

    $(window).resize(function () {
      hsize = $(window).height();
      $("body").css("height", hsize + "px");
    });

    hsize = $(window).height();
    $("body").css("height", hsize + "px");


    setInterval("disptime()", 1000);
    //disptime();

   // $("#fittext").fitText(2);

    //勤務中一覧の縦幅調整
    var count = $('#clock_in_count').data('count');
    var height = (count/3)*200;
    $('#block_1 .Inner').height(height);

    //社員一覧の横幅調整
    var count = $('#clock_out_count').data('count');
    var height = (count/3)*200;
    $('#block_2 .Inner').height(height);

  });

  function disptime() {

    var now = new Date();

    var years = now.getFullYear();
    var month = now.getMonth() + 1;
    var day = now.getDate();

    var hour = now.getHours(); // 時
    var min = now.getMinutes(); // 分
    var sec = now.getSeconds(); // 秒

    if (hour < 10) {
      hour = "0" + hour;
    }
    if (min < 10) {
      min = "0" + min;
    }
    if (sec < 10) {
      sec = "0" + sec;
    }

    var watch1 = years + '.' + month + '/' + day + ' '; // パターン1
    var watch2 = hour + ':' + min + ' '; // パターン2
    var watch3 = sec;


    $('#watch1').text(watch1);
    $('#watch2').text(watch2);
    $('#watch3').text(watch3);
  }
</script>

</body>
</html>