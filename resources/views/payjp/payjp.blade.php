<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PAY.JPTest</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: black;
                color: #ffffff;
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
        <form class="card-block" method="post" action="/test_payjp/add_customer">
            {{csrf_field()}}
            <div class="card">
                <div class="card-header">カード情報入力</div>
                <div class="card-block">
                    <fieldset>
                        <legend>CARD INFO</legend>
                        <div class="form-group">
                            <label>NAME</label>
                            <input type="text" class="form-control" name="name" placeholder="YOUR NAME" value="NAME">
                        </div>
                        <div class="form-group">
                            <label>NUMBER</label>
                            <input type="text" class="form-control" name="number" placeholder="カード番号" value="4242424242424242" >
                        </div>
                        <div class="form-group">
                            <label>EXPIRE</label>
                            <div class="row">
                                <div class="col-lg-3">
                                    <input type="number" class="form-control" name="exp_month" placeholder="月" value="10">
                                </div>
                                <div class="col-lg-3">
                                    <input type="number" class="form-control" name="exp_year" placeholder="年" value="2020">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>CVC</label>
                            <input type="number" class="form-control" name="cvc" placeholder="***" value="123">
                        </div>
                        <div class="form-group">
                        <label>DESRIPTION</label>
                        <input type="text" class="form-control" name="description" placeholder="説明文" value="" >
                        </div>
                        <br>
                        <div class="card-footer">
                            <input type="submit" class="btn btn-block btn-danger" value="顧客作成">
                        </div>
                    </fieldset>
                </div>
            </div>
        </form>
        <br>
        &nbsp;&nbsp;<a href="/test_payjp/list"><input type="button" class="btn btn-block btn-danger" value="一覧画面"></a>
    </body>
</html>
