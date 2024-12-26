<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Events\Login;
use App\Http\Controllers\Frontend\CartController;

class TransferCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  \Illuminate\Auth\Events\Login  $event
     */
    public function handle(Login $event)
    {
        $cartController = new CartController();
        $cartController->transferSessionCartToDatabase();
    }
}
