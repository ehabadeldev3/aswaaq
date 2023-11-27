<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-dashboard', function (User $user) {
            return $user->auth_id == 1 &&  !in_array('representative',$user->role_name);
        });

        Gate::define('represenntative-api', function (User $user) {
            return in_array('representative',$user->role_name);
        });

        Gate::define('client-api', function (User $user) {
            return $user->auth_id == 2 &&  in_array('client',$user->role_name);
        });
    }
}
