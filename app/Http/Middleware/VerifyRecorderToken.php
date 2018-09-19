<?php
namespace App\Http\Middleware;

use App\Http\Controllers\TimestampController;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * VerifyRecorderToken
 *
 * @package App\Http\Middleware
 */
class VerifyRecorderToken
{
    /** @type \Illuminate\Cookie\CookieJar */
    private $cookie;
    /** @type \Illuminate\Http\Response */
    private $response;

    public function __construct(
        CookieJar $cookie,
        Response $response
    ) {
        $this->cookie = $cookie;
        $this->response = $response;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $recorderUid = $request->route()->getParameter('uid');

        $recorder = \DB::connection('customer-db')->table('recorders')->where('uid', $recorderUid)->first();
        if ($recorder === null) {
            throw new NotFoundHttpException;
        }

        // トークンチェック(必要なルートのみ)
        if (in_array($request->route()->getActionName(), $this->expect(), true)
            || $this->verifyToken($request, $recorder)
        ) {
            return $next($request);
        }

        return redirect('/timestamp/'.$recorderUid.'/locked');

    }

    /**
     * トークンチェックをしないアクション名リスト
     * @return array
     */
    private function expect()
    {
        return [
            VerifyRecorder::class . '@lock',
        ];
    }

    /**
     * トークン認証
     * @param \Illuminate\Http\Request $request
     * @param array $recorder
     * @return bool
     */
    private function verifyToken($request, $recorder)
    {
        // Cookieからトークンを取得
        $token = $request->cookie('recorder_token', false);
        if ($token === false) {
            return false;
        }

        // トークン確認
        if ($recorder['token'] !== $token) {
            // Cookie削除
            $cookie = $this->cookie->forget('recorder_token');
            $this->response->withCookie($cookie);
            return false;
        }

        return true;
    }

    private function redirectPath($uid)
    {
        return redirect('/timestamp/'.$uid.'/locked');

    }
}
