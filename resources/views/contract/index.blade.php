<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keyword" content="タイムカード,タイムレコーダー,勤怠管理,勤怠,タイムスタンプ,timestamp"/>
    <!-- 共通スタイルシート -->
    <link rel="stylesheet" type="text/css" href="http://t-stamp.net/wp-content/themes/timestamp/css/reset.css"/>
    <link rel="stylesheet" type="text/css" href="http://t-stamp.net/wp-content/themes/timestamp/css/base.css"/>

    <script type="text/javascript" src="http://t-stamp.net/wp-content/themes/timestamp/js/jquery-1.8.3.min.js"></script>

    <title>申し込みフォーム</title>
    <meta name="description" content="導入もスマートに簡単に。 タイムスタンプのご利用に弊社との打ち合わせはございません。すぐにタイムスタンプのご利用をスタートできます。"/>
    <!--個別CSS-->
    <link href="http://t-stamp.net/wp-content/themes/timestamp/css/introduction.css" rel="stylesheet" type="text/css"
          media="all"/>

    <link rel="alternate" type="application/rss+xml" title="タイムスタンプ &raquo; 導入について – タイムスタンプ - のコメントのフィード"
          href="http://t-stamp.net/introduction/feed/"/>
    <style type="text/css">
        .wp-pagenavi {
            margin-left: auto !important;
            margin-right: auto;
        !important
        }

        .wp-pagenavi a, .wp-pagenavi a:link, .wp-pagenavi a:visited, .wp-pagenavi a:active, .wp-pagenavi span.extend {
            background: #ffffff !important;
            border: 1px solid #00b8ed !important;
            color: #00b8ed !important;
        }

        .wp-pagenavi a:hover, .wp-pagenavi span.current {
            background: #00b8ed !important;
            border: 1px solid #ffffff !important;
            color: #ffffff !important;
        }

        .wp-pagenavi span.pages {
            color: #00b8ed !important;
        }

        .errorTxt01 {
            font-weight: bold;
            font-size: 150%;
            margin-bottom: 2%;
            color: #de0716 !important;
            text-decoration: underline;
        }

        .messageTxt01 {
            font-weight: bold;
            font-size: 150%;
            margin-bottom: 2%;
            color: #2ab27b !important;
            text-decoration: underline;
        }

        .input01 {
            height: 30px;
            width: 300px;
            font-size: 20px;
        }

        .submit01 {
            clear: both;
            text-align: center;
            margin-top: 70px;
        }

        .submit01 input {
            cursor: pointer;
        }
    </style>
    <link rel='stylesheet' id='contact-form-7-css'
          href='http://t-stamp.net/wp-content/plugins/contact-form-7/includes/css/styles.css?ver=3.5.4' type='text/css'
          media='all'/>
    <link rel='stylesheet' id='wp-pagenavi-style-css'
          href='http://t-stamp.net/wp-content/plugins/wp-pagenavi-style/style/default.css?ver=1.0' type='text/css'
          media='all'/>
    <script type='text/javascript' src='http://t-stamp.net/wp-includes/js/jquery/jquery.js?ver=1.10.2'></script>
    <script type='text/javascript'
            src='http://t-stamp.net/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.2.1'></script>
    <link rel="EditURI" type="application/rsd+xml" title="RSD" href="http://t-stamp.net/xmlrpc.php?rsd"/>
    <link rel="wlwmanifest" type="application/wlwmanifest+xml" href="http://t-stamp.net/wp-includes/wlwmanifest.xml"/>
    <link rel='prev' title='機能 – タイムスタンプ -' href='http://t-stamp.net/function/'/>
    <link rel='next' title='プライバシーポリシー – タイムスタンプ -' href='http://t-stamp.net/privacy/'/>
    <meta name="generator" content="WordPress 3.8.17"/>
    <link rel='canonical' href='http://t-stamp.net/introduction/'/>
    <link rel='shortlink' href='http://t-stamp.net/?p=20'/>
    <style type="text/css">
        .wp-pagenavi {
            font-size: 12px !important;
        }
    </style>
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-49899628-1', 't-stamp.net');
        ga('send', 'pageview');

    </script>
</head>
<body>
<div id="container" class="clearfix">
    <!-- コンテンツ -->
    <div id="contents_wrapper" class="left">
        <div id="introduction_header">
            <h2>申し込みフォーム</h2>
        </div>
        <p style="text-align:center" class="errorTxt01">{{$errors->first('email')}}</p>
        <p style="text-align:center" class="errorTxt01">{{$errors->first('domain_name')}}</p>
        <p style="text-align:center" class="messageTxt01">{!! $message??"" !!}</p>
        <form method="post" action="/contract">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="one_flow clearfix">
                <div class="flow_contents right">
                    <span style="font-size: 2em;">メールアドレス：</span>
                    <input type="text" name="email" value="{{old("email")}}" class="input01">
                </div>
                <br><br><br><br><br><br>
                <div class="flow_contents right">
                    <span style="font-size: 2em;">サブドメイン名：</span>
                    <input type="text" name="domain_name" value="{{old("domain_name")}}" class="input01" maxlength="30">
                    <span>(半角英数字)</span>
                </div>
                <div class="submit01">
                    <input type="submit" value="申し込む" style="width: 200px;">
                </div>

            </div>
        </form>
    </div>
</div>
<div id="footer_wrapper">

    <div id="footer_box" class="clearfix">
        <div class="left"><a href="http://t-stamp.net" id="footer_logo" class="left">タイムスタンプ</a></div>
        <ul class="left clearfix">
            <li class="left">
                <a href="http://t-stamp.net/about/">タイムスタンプについて</a>
            </li>
            <li class="left">
                <a href="http://t-stamp.net/function/">機能</a>
            </li>
            <li class="left">
                <a href="http://t-stamp.net/price/">価格</a>
            </li>
            <li class="left">
                <a href="http://t-stamp.net/introduction/">導入について</a>
            </li>
            <li class="left">
                <a href="#">お問い合わせ</a>
            </li>
            <li class="left">
                <a href="http://t-stamp.net/dealings/">特定商取引法に基づく表記</a>
            </li>
            <li class="left">
                <a href="http://t-stamp.net/account/">利用規約</a>
            </li>
            <li class="left">
                <a href="http://t-stamp.net/privacy/">プライバシーポリシー</a>
            </li>
            <li class="left">
                <a href="http://t-stamp.net/security/">情報セキュリティポリシー</a>
            </li>
        </ul>
    </div>
</div>
<div id="copyright">
    <p>Copyright 2013 © TIMESTAMP All Rights Reserved.</p>
</div>
</div>
<script type='text/javascript'
        src='http://t-stamp.net/wp-content/plugins/contact-form-7/includes/js/jquery.form.min.js?ver=3.45.0-2013.10.17'></script>
<script type='text/javascript'>
    /* <![CDATA[ */
    var _wpcf7 = {
        "loaderUrl": "http:\/\/t-stamp.net\/wp-content\/plugins\/contact-form-7\/images\/ajax-loader.gif",
        "sending": "\u9001\u4fe1\u4e2d ..."
    };
    /* ]]> */
</script>
<script type='text/javascript'
        src='http://t-stamp.net/wp-content/plugins/contact-form-7/includes/js/scripts.js?ver=3.5.4'></script>
</body>
</html>