<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

/**
 * Request
 *
 * @package App\Http\Requests
 */
abstract class Request extends FormRequest
{
    /**
     * バリデーションルール
     * @return array
     */
    abstract public function rules();

    /**
     * 認可チェック
     * @return bool
     */
    abstract public function authorize();

    public function response(array $errors)
    {
        if (!$this->ajax()) {
            return parent::response($errors);
        }

        return $this->ajaxErrorResponse($this->errorMessageText($errors), 422);
    }

    /**
     * Ajax用エラーレスポンス(JSON)を作成します。
     * @param string $errors エラーメッセージ
     * @param int $statusCode ステータスコード
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ajaxErrorResponse($errors, $statusCode = 422)
    {
        $response = [
            'status_code' => $statusCode,
            'errors' => $errors,
        ];

        return new JsonResponse($response, $statusCode);
    }

    /**
     * HTML出力用のエラーメッセージを作成します。
     * @param array $errors
     * @return string
     */
    private function errorMessageText(array $errors)
    {
        return implode('<br>', array_flatten($errors));
    }
}
