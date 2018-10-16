<?php

namespace App\Http\Middleware;

use Closure;

class ShopAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $merchant_id = \Cookie::get('merchant_id');
        if ($merchant_id && is_numeric($merchant_id)) {
            return $next($request);
        } else {
            return redirect()->route('shopadmin-user-login');
        }
    }
}
