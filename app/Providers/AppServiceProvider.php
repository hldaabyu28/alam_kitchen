<?php

namespace App\Providers;

use App\Models\Store;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share store data globally
        \Illuminate\Support\Facades\View::share('store', \App\Models\Store::first());

        // Use a view composer to share unread counts and recent items with the header
        \Illuminate\Support\Facades\View::composer('layouts.partials.header', function ($view) {
            $unreadNotificationsCount = 0;
            $recentNotifications = collect();
            $unreadContactsCount = \App\Models\Contact::where('is_read', false)->count();
            $recentContacts = \App\Models\Contact::where('is_read', false)->latest()->take(5)->get();

            if (auth()->check()) {
                $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
                $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();
            }

            $view->with([
                'unreadNotificationsCount' => $unreadNotificationsCount,
                'recentNotifications'     => $recentNotifications,
                'unreadContactsCount'      => $unreadContactsCount,
                'recentContacts'           => $recentContacts,
            ]);
        });
    }
}
