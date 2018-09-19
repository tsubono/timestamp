<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PayJPList</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: black;
                color: #fff;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="card-block">
            <fieldset>
                <legend>顧客一覧</legend>
                <table>
                    @forelse ($customerList->data as $key => $value)
                        <tr>
                            <td>
                                ID:{{$value->id}}
                            </td>
                            <td>
                                カードID:{{$value->cards->data[0]->id}}
                            </td>
                            <td>
                                名前:{{$value->cards->data[0]->name}}
                            </td>
                            <td>
                                説明:{{$value->description}}
                            </td>
                            <td>----->課金</td>
                            <td>
                                <form class="card-block" id="chargeForm" method="post" action="/test_payjp/add_charge">
                                    {{csrf_field()}}
                                    <input type="hidden" name="customer_id">
                                    <div class="form-group">
                                        <label>PRICE</label>
                                        <input type="text" class="form-control" name="amount" placeholder="金額" value="" >
                                    </div>
                                    <div class="form-group">
                                        <label>DESRIPTION</label>
                                        <input type="text" class="form-control" name="description" placeholder="説明文" value="" >
                                    </div>
                                    <div class="card-footer">
                                        <input type="button" class="btn btn-block btn-danger" id="chargeBtn" value="決済" data-id="{{$value->id}}">
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </table>
            </fieldset>
        </div>

        <div class="card-block">
            <fieldset>
                <legend>課金一覧</legend>
                <table>
                    @forelse ($chargeList->data as $key => $value)
                        <tr>
                            <td>
                               ID:{{$value->id}}
                            </td>
                            <td>
                                カードID:{{$value->card->id}}
                            </td>
                            <td>
                               名前:{{$value->card->name}}
                            </td>
                            <td>
                               金額:&yen;{{$value->amount}}
                            </td>
                            <td>
                               ブランド:{{$value->card->brand}}
                            </td>
                            <td>
                                下４桁:{{$value->card->last4}}
                            </td>
                            <td>
                                説明:{{$value->description}}
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </table>
            </fieldset>
        </div>

        &nbsp;&nbsp;<a href="/test_payjp"><input type="button" class="btn btn-block btn-danger" value="戻る"></a>

        <script
                src="https://code.jquery.com/jquery-3.1.1.js"
                integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA="
                crossorigin="anonymous"></script>
    <script>
        window.onload = function() {
            $(function () {
                $('#chargeBtn').click(function () {
                    $('[name=customer_id]').val($(this).data('id'));
                    $('#chargeForm').submit();
                });
            });
        }
    </script>
    </body>
</html>
