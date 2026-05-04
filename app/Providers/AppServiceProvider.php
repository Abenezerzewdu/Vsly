<?php

namespace App\Providers;

use App\Models\Duel;
use App\Models\Take;
use App\Policies\DuelPolicy;
use App\Policies\TakePolicy;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
    Take::class => TakePolicy::class,
    Duel::class=>DuelPolicy::class,
];

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
        Vite::prefetch(concurrency: 3);
    }
}
