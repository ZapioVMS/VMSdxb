<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        // Share active sites with all views for topbar site dropdown
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $globalSites = Site::where('status', 'active')->orderBy('name')->get();
                $view->with('globalSites', $globalSites);
            }
        });

        // Global Authorization Gate Interceptor using AuthorizationService
        Gate::before(function (User $user, string $ability) {
            // Let the AuthorizationService determine if the user has the permission key.
            // Returning null allows standard fallback, but since we map everything to our DB,
            // we can return the boolean check if the permission exists in our DB.
            $authService = app(\App\Services\AuthorizationService::class);
            
            // If the service returns true, grant access
            if ($authService->hasPermission($user, $ability)) {
                return true;
            }
            
            // If it returns false, we might want to return null to allow other gates,
            // but in our RBAC, if it's in the DB and returns false, they don't have it.
            // For now, return null to let other potential custom gates run, though we rely on this.
            return null;
        });
    }
}
