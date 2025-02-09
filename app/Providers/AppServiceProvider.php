<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        // Define Gates
        Gate::define('manage-users', function (User $user) {
            return $user->role === UserRole::SUPERADMIN;
        });

        Gate::define('manage-files-folders', function (User $user) {
            return $user->role === UserRole::SUPERADMIN || $user->role === UserRole::USER;
        });

        Gate::define('view-logs', function (User $user) {
            return $user->role === UserRole::SUPERADMIN || $user->role === UserRole::ADMIN;
        });

        Gate::define('view-bookmarks', function (User $user) {
            return $user->role === UserRole::USER;
        });

        // Include Helper Functions
        require_once app_path('Helpers/helpers.php');
    }
}
