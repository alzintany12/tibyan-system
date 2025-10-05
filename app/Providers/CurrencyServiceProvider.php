<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\CurrencyHelper;

class CurrencyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('currency', function () {
            return new CurrencyHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // إضافة Blade directive للعملة
        Blade::directive('currency', function ($expression) {
            return "<?php echo currency($expression); ?>";
        });

        Blade::directive('currencySymbol', function () {
            return "<?php echo currency_symbol(); ?>";
        });

        Blade::directive('currencyName', function () {
            return "<?php echo currency_name(); ?>";
        });
    }
}