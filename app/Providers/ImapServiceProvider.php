<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Imap\Imap;

class ImapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\Imap\Contracts\ImapContract', function(){
            return new Imap();
        });
    }
}
