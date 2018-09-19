<?php

namespace App\Http\Middleware;

use App\Models\Contract;
use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Config;

class SetSubDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $domain_name = $request->route('subdomain');

            $contract = Contract::where('domain_name', $domain_name)->first();
            if (empty($contract)) {
                new NotFoundHttpException();
            }

            // 契約別DB接続設定の接続先データベースを設定
            Config::set("database.connections.customer-db.database", $domain_name);

            view()->share('subdomain', $domain_name);

        } catch (NotFoundHttpException $e) {
            \Log::debug($e);
            return redirect('/login');
        } catch (\Exception $e) {
            \Log::debug($e);
            return redirect('/login');
        }

        return $next($request);
    }

}
