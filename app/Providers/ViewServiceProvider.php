<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationsUser;
use App\Models\NotificationsPharmacy;
use App\Models\UserCart;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Using closure based composers...
        View::composer('frontend.layouts.master', function ($view) {
            if (Auth::check()) {
                $cartItems = new UserCart();
                $cartItemsCount = $cartItems->where('user_id', Auth::id())->count();
                $notificationsUser = new NotificationsUser();
                $notificationsUserItems = $notificationsUser->where('user_id', Auth::id())
                    ->orderBy('notification_id', 'DESC')
                    ->get();
                $notificationsUserCount = $notificationsUser->where('user_id', Auth::id())->where('status', 'unread')->count();
                $notificationsPharmacy = new NotificationsPharmacy();
                $notificationsPharmacyItems = $notificationsPharmacy->where('vendor_id', Auth::id())
                    ->orderBy('notification_id', 'DESC')
                    ->get();
                $notificationsPharmacyCount = $notificationsPharmacy->where('vendor_id', Auth::id())->where('status', 'unread')->count();

                $view->with(compact(
                    'cartItems',
                    'cartItemsCount',
                    'notificationsUserItems',
                    'notificationsUserCount',
                    'notificationsPharmacyItems',
                    'notificationsPharmacyCount'
                ));
            }
        });
    }
}
