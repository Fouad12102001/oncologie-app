<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;

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
        // Directive @canOnco('permission') / @endcanOnco
        Blade::directive('canOnco', function (string $expression) {
            return "<?php if(oncologieUserCan({$expression})): ?>";
        });
        Blade::directive('endcanOnco', function () {
            return "<?php endif; ?>";
        });

        // Directive @cannotOnco('permission') / @endcannotOnco
        Blade::directive('cannotOnco', function (string $expression) {
            return "<?php if(!oncologieUserCan({$expression})): ?>";
        });
        Blade::directive('endcannotOnco', function () {
            return "<?php endif; ?>";
        });

        // Directive @roleIs('medecin') / @endRoleIs
        Blade::directive('roleIs', function (string $expression) {
            return "<?php if(oncologieUserRole() === {$expression}): ?>";
        });
        Blade::directive('endRoleIs', function () {
            return "<?php endif; ?>";
        });

        // Directive @roleIsAny('medecin', 'pharmacien') / @endRoleIsAny
        Blade::directive('roleIsAny', function (string $expression) {
            return "<?php if(in_array(oncologieUserRole(), [{$expression}], true)): ?>";
        });
        Blade::directive('endRoleIsAny', function () {
            return "<?php endif; ?>";
        });
    }
    
}
