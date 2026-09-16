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

        // Global Super Admin Gate Bypass
        Gate::before(function (User $user, string $ability) {
            if ($user->roles()->where('name', 'Super Admin')->exists()) {
                return true;
            }
        });

        // Fixed Permission Gates matching Madkhal RBAC Matrix
        $fixedPermissions = [
            'visitors.view_all',
            'visitors.checkin',
            'visitors.preregister',
            'visitors.print_badges',
            'security.manage_watchlist',
            'security.block_visitors',
            'security.trigger_evacuation',
            'employees.view',
            'employees.edit',
            'employees.delete',
            'system.view_reports',
            'system.manage_settings',
            'system.manage_users',
            'system.manage_nda',
            'system.access_billing',
        ];

        foreach ($fixedPermissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                return $user->roles()->whereHas('permissions', function ($q) use ($permission) {
                    $q->where('name', $permission);
                })->exists();
            });
        }
    }
}
