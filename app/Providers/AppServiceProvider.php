<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Actions\ViewAction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Voyager::addAction(\App\Actions\AddDonationAction::class);
        Voyager::replaceAction(ViewAction::class,\App\Actions\ViewDonatorAction::class);
        Voyager::replaceAction(ViewAction::class,\App\Actions\ViewDetailUserAction::class);
    }
}
