<?php

namespace App\Providers;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Actions\ViewAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Voyager::addAction(\App\Actions\AddPrintKwitansiAction::class);
        Voyager::replaceAction(ViewAction::class,\App\Actions\ViewDonatorAction::class);
        Voyager::addAction(\App\Actions\AddDonationAction::class);
        Voyager::addAction(\App\Actions\AddDeleteDonaturgroupsAction::class);
        Voyager::addAction(\App\Actions\AddDeleteUsersAction::class);
        Voyager::addAction(\App\Actions\ViewDonaturGroupsAction::class);
        Voyager::replaceAction(ViewAction::class,\App\Actions\ViewDetailUserAction::class);
        Voyager::addAction(\App\Actions\AddDeleteDonaturAction::class);
    }
}
