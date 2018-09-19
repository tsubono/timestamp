@extends('basic')

@include('elements.toast')

@section('title')
    <title>給与明細出力 | TIMESTAMP</title>
@stop

@section('content')
    <div class="text-center loginscreen animated fadeInDown">
        <div class="row">
            @if (!empty($message))
                <div class="alert alert-success">{{$message}}</div>
            @endif
            @if (!empty($err_message))
                <div class="alert alert-danger" id="err_msg">{{$err_message}}</div>
            @else
                <div class="alert alert-danger" id="err_msg" style="display: none;">{{$err_message}}</div>
            @endif
            <div class="col-xs-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>給与詳細</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="panel blank-panel">
                            <div class="panel-heading">
                                <h4>{{$year}}年{{$month}}月分 : {{$employee->name}}</h4>
                            </div>
                            <div class="panel-body">
                                <form action="/payment_report/export" method="post" accept-charset="UTF-8"
                                      class="form-horizontal">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                    <input name="year" type="hidden" value="{{$year}}">
                                    <input name="month" type="hidden" value="{{$month}}">
                                    <input name="employee_uid" type="hidden" value="{{$employee->uid}}">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr class="gray">
                                                        <td rowspan="4">支給</td>
                                                        <th>基本給</th>
                                                        <th>
                                                            <input type="text" name="supply[free_name_1]" class="form-control"
                                                                   value="{{old("supply.free_name_1", $payment_supply->free_name_1)}}">
                                                        </th>
                                                        <th>
                                                            <input type="text" name="supply[free_name_2]" class="form-control"
                                                                   value="{{old("supply.free_name_2", $payment_supply->free_name_2)}}">
                                                        </th>
                                                        <th>
                                                            <input type="text" name="supply[free_name_3]" class="form-control"
                                                                   value="{{old("supply.free_name_3", $payment_supply->free_name_3)}}">
                                                        </th>
                                                        <th>
                                                            <input type="text" name="supply[free_name_4]" class="form-control"
                                                                   value="{{old("supply.free_name_4", $payment_supply->free_name_4)}}">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="supply[base_salary]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.base_salary", $payment_supply->base_salary))}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="supply[free_value_1]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.free_value_1", $payment_supply->free_value_1))}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="supply[free_value_2]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.free_value_2", $payment_supply->free_value_2))}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="supply[free_value_3]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.free_value_3", $payment_supply->free_value_3))}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="supply[free_value_4]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.free_value_4", $payment_supply->free_value_4))}}">
                                                        </td>
                                                    </tr>
                                                    <tr class="gray">
                                                        <th>
                                                            <input type="text" name="supply[free_name_5]" class="form-control"
                                                                   value="{{old("supply.free_name_5", $payment_supply->free_name_5)}}">
                                                        </th>
                                                        <th>時間外手当</th>
                                                        <th>通勤手当(非)</th>
                                                        <th>不就労控除</th>
                                                        <th>総支給額</th>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="supply[free_value_5]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.free_value_5", $payment_supply->free_value_5))}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="supply[over_cost]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.over_cost", $payment_supply->over_cost))}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="supply[traffic_cost]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.traffic_cost", $payment_supply->traffic_cost))}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="supply[unemployment_cost]" class="form-control supply_costs"
                                                                   value="{{number_format(old("supply.unemployment_cost", $payment_supply->unemployment_cost))}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="supply[total]" id="supply_total" class="form-control"
                                                                   value="{{number_format(old("supply.total", $payment_supply->total))}}">
                                                        </td>
                                                    </tr>
                                                </tbody>

                                                <tbody>
                                                <tr class="gray">
                                                    <td rowspan="4">控除</td>
                                                    <th>健康保険</th>
                                                    <th>介護保険</th>
                                                    <th>厚生年金</th>
                                                    <th>雇用保険</th>
                                                    <th>社会保険計</th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="text" name="deduction[health_insurance]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.health_insurance", $payment_deduction->health_insurance))}}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deduction[care_insurance]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.care_insurance", $payment_deduction->care_insurance))}}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deduction[welfare_pension]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.welfare_pension", $payment_deduction->welfare_pension))}}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deduction[employment_insurance]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.employment_insurance", $payment_deduction->employment_insurance))}}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deduction[social_insurance]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.social_insurance", $payment_deduction->social_insurance))}}">
                                                    </td>
                                                </tr>
                                                <tr class="gray">
                                                    <th>所得税</th>
                                                    <th>住民税</th>
                                                    <th>
                                                        <input type="text" name="deduction[free_name_1]" class="form-control"
                                                               value="{{old("deduction.free_name_1", $payment_deduction->free_name_1)}}">
                                                    </th>
                                                    <th>
                                                        <input type="text" name="deduction[free_name_2]" class="form-control"
                                                               value="{{old("deduction.free_name_2", $payment_deduction->free_name_2)}}">
                                                    </th>
                                                    <th>控除計</th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="text" name="deduction[income_tax]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.income_tax", $payment_deduction->income_tax))}}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deduction[inhabitant_tax]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.inhabitant_tax", $payment_deduction->inhabitant_tax))}}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deduction[free_value_1]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.free_value_1", $payment_deduction->free_value_1))}}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deduction[free_value_2]" class="form-control deduction_costs"
                                                               value="{{number_format(old("deduction.free_value_2", $payment_deduction->free_value_2))}}">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deduction[total]" id="deduction_total" class="form-control"
                                                               value="{{number_format(old("deduction.total", $payment_deduction->total))}}">
                                                    </td>
                                                </tr>
                                                <tr class="gray">
                                                    <td colspan="4" class="right"><strong>差引支給額</strong></td>
                                                    <td colspan="2" id="payment" class="right" style="font-weight: bold;">{{number_format($payment_supply->total - $payment_deduction->total)}}</td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-lg btn-primary">上記で給与明細を出力</button>
                                </form>

                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <style>
        td {
            text-align: left;
        }
        .gray {
            background: #F5F5F6;
        }
        .right {
            text-align: right;
        }
    </style>
    <script>
        window.onload = function () {

            $('.supply_costs ').change (function() {
                setSupplyTotal();
            });

            $('.deduction_costs ').change (function() {
                setDeductionTotal();
            });
            setSupplyTotal();
            setDeductionTotal();

            function setSupplyTotal() {
                var total = 0;
                $('.supply_costs').each (function () {
                    //total += $(this).val();
                    total += parseInt($(this).val().split(',').join('').trim());
                });
                $('#supply_total').val(number_format(total));

                $('#payment').html(number_format(parseInt($('#supply_total').val().split(',').join('').trim()) - parseInt($('#deduction_total').val().split(',').join('').trim())));

            }
            function setDeductionTotal() {
                var total = 0;
                $('.deduction_costs').each (function () {
                    total += Number($(this).val());
                });
                $('#deduction_total').val(number_format(total));

                $('#payment').html(number_format(parseInt($('#supply_total').val().split(',').join('').trim()) - parseInt($('#deduction_total').val().split(',').join('').trim())));

            }

            function number_format(num) {
                return num.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
            }
        }
    </script>

@stop