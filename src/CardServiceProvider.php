<?php

namespace Mphpmaster\NovaInfoCard;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class_alias(\Mphpmaster\NovaInfoCard\Interfaces\IInfoCardSource::class, \Mphpmaster\InfoCard\Interfaces\IInfoCardSource::class);
class_alias(\Mphpmaster\NovaInfoCard\CardServiceProvider::class, \Mphpmaster\InfoCard\CardServiceProvider::class);
class_alias(\Mphpmaster\NovaInfoCard\InfoCard::class, \Mphpmaster\InfoCard\InfoCard::class);
class_alias(\Mphpmaster\NovaInfoCard\InfoLine::class, \Mphpmaster\InfoCard\InfoLine::class);
class_alias(\Mphpmaster\NovaInfoCard\InfoOnClickEvent::class, \Mphpmaster\InfoCard\InfoOnClickEvent::class);

class CardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        file_exists($path=__DIR__ . "/Helpers/FMain.php") && require_once($path);

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('info-card', __DIR__.'/../dist/js/card.js');
            // Nova::style('info-card', __DIR__.'/../dist/css/card.css');
        });
    }

    /**
     * Register the card's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
                ->prefix('nova-vendor/info-card')
                ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
