<?php

namespace App\Providers;

use App\Models\Compra;
use App\Models\Venda;
use App\Observers\CompraObserver;
use App\Observers\VendaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Compra::observe(CompraObserver::class);
        Venda::observe(VendaObserver::class);
    }
}
