<?php
namespace App\Http\Middleware;

use Illuminate\Cookie\CookieJar;

/**
 * VerifyRecorder
 *
 * @package App\Http\Middleware
 */
class VerifyRecorder
{
    /** @type \Illuminate\Cookie\CookieJar */
    private $cookie;

    public function __construct(CookieJar $cookie)
    {
        $this->cookie = $cookie;
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
            $cookie = $this->cookie->forget('recorder_token');
            return response()->view('errors.404', [], 404)->withCookie($cookie);
        }

        return $next($request);
    }
}
