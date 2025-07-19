<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // You can map models to policies here
    ];

    public function boot(): void
    {
        // Define a Gate called 'manage-categories'
        Gate::define('manage-categories', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
