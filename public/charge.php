<?php
require "../vendor_fastpay/autoload.php"; # vendorフォルダまでのパスを記載
use FastPay\FastPay;
use FastPay\Error\CardError;
use FastPay\Error\InvalidRequestError;
use FastPay\Error\ApiError;
use FastPay\Error\ConnectionError;
use FastPay\Error\FastPayError;

try {
    $token = $_POST["fastpayToken"];
    $fastpay = new FastPay("6d4fb732ef776938ac3d575f7c8ac18264c840db"); # シークレットを入力
    // 課金を作成(失敗した場合例外があがります)
    $charge = $fastpay->charge->create(array(
        "amount" => 666, # 金額を記載(data-amountに指定された金額と同一の必要有り）
        "card" => $token,
        "description" => "", # 商品の詳細を記載
    ));
    header("Location: http://fastpay.yahoo.co.jp"); # 成功時の画面遷移先を入力
} catch (CardError $e) {
    // クレジットカードに関するエラー(HTTPのステータスが402のパターンです)
    header("Location: http://fastpay.yahoo.co.jp"); # エラー時の画面遷移先を入力
    switch($e->getCode()) {
        // 決済失敗ごとに細かくハンドリングしたい場合
        case "card_declined":
            header("Location: http://fastpay.yahoo.co.jp"); # エラー時の画面遷移先を入力
        // その他ハンドリングするCodeごとのcaseを記述
        default:
    }
} catch (InvalidRequestError $e) {
    echo "InvalidRequestError: リクエストが不正です。店舗にお問い合わせください。";
} catch (\Exception $e) {
    echo "システムエラーが発生しました";
}
