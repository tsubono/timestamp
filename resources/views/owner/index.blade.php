@extends('basic_owner')

@include('elements.toast')

@section('title')
    <title>店舗一覧 | TIMESTAMP</title>
@stop

@section('content')
    <div class="text-center loginscreen animated fadeInDown">
        <div>
            @if (!empty($message))
                <br>
                <div class="alert alert-success">{{$message}}</div>
            @endif
            @if (!empty($err_message))
                <br>
                <div class="alert alert-danger">{{$err_message}}</div>
            @endif
            <h3 style="font-size: 1.5em;">店舗選択画面</h3>

            <div class="workplaces_area">
                @foreach($workplaces as $idx => $workplace)
                    <form action="/owner/select_workplace" method="post" accept-charset="UTF-8" role="form" class="m-t"
                          id="form_{{$idx}}">
                        <input name="_token" type="hidden" value="{{ csrf_token() }}">
                        <input name="workplace_uid" type="hidden" value="{{ $workplace->uid }}">
                        <div class="text-center col-lg-3 col-md-3 col-sm-9 col-xs-9 workplaces tile"
                             data-idx="{{$idx}}">
                            <div style="text-align:center;padding:90px 0 90px 0;">
                                <p style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{$workplace->formal_name}}</p>
                                <p style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">{{$workplace->name}}</p>
                            </div>
                        </div>
                    </form>
                @endforeach
                <a data-toggle="modal" data-target="#workplaceAddModal">
                    <div class="text-center col-lg-3 col-md-3 col-sm-9 col-xs-9 tile" id="add_workplace">
                        <img src="/assets/img/add.png" width="150" height="150" style="margin-top: 50px;">
                    </div>
                </a>

            </div>

        </div>
    </div>

    @include("owner.modal.add_workplace")
    @include("owner.modal.edit_account",["contract_flg"=>false, "user"=>$user])
    <script>
        window.onload = function () {
            //店舗選択
            $('.workplaces').click(function () {
                var idx = $(this).data('idx');
                $('#form_' + idx).submit();
            });

            //時刻ピッカー
            //$('#time').timepicker({'timeFormat': 'H:i'});

            $('#time').datetimepicker({
                format: 'H:i',
                lang: 'ja',
                datepicker:false
            });

            //郵便番号から住所を自動入力
            $('.zipcode').change(function () {
                AjaxZip3.zip2addr('zip_1', 'zip_2', 'pref', 'address');
            });

            //plan選択時
            $('.plans').click (function() {
                if ($('.activePlan').length!=0) {
                    $('.activePlan').removeClass('activePlan');
                }
                $(this).addClass('activePlan');
                $('[name=plan_id]').val($(this).data('value'));
            });
        }

    </script>
    <style>
        body {
            background: #FFF;
        }

        .errors {
            color: red;
            list-style: none;
        }

        .tile {
            height: 250px;
            margin-left: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            background-color: #9ea6b9;
            color: #fff;
        }

        .tile:hover {
            background-color: #1f648b;
        }

        .tile::before,
        .tile::after {
            position: absolute;
            z-index: -1;
            display: block;
            content: '';
        }

        .tile,
        .tile::before,
        .tile::after {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-transition: all .3s;
            transition: all .3s;
        }

        .workplaces_area {
            margin-left: 10%;
        }

        .plans.tile {
            height: 250px;
            margin-left: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            background-color: #f2dede;
            color: black;
        }

        .plans.tile:hover {
            background-color: #1f648b;
            color: #FFF;
        }

        .tile.activePlan {
            background-color: #1f648b;
            color: #FFF;
            border: 1px dashed #fff;
            border-radius: 8px;
            box-shadow: 0 0 0 4px #fff;
        }

        .plan_name {
            font-weight: bold;
        }

    </style>
@endsection
