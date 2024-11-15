<?php

namespace App\Providers;

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

        Gate::define('isAdmin', function( $user) {
            return $user->type == 'admin';
        });

        Gate::define('isAdminOrEmployee', function( $user) {
            return $user->type == 'admin' || $user->type == 'employee';
        });

        Gate::define('isClient', function($user) {
            return $user->type == 'client';
        });

        Gate::define('isOperator', function($user) {
            return $user->type == 'operator';
        });

        Gate::define('isMarketer', function($user) {
            return $user->type == 'marketer';
        });

        Gate::define('isNotOperator', function($user) {
            return $user->type != 'operator';
        });

        Gate::define('isNotClient', function($user) {
            return $user->type != 'client';
        });

        Gate::define('isEmployeeHasPermission', function($user, $permission) {
            return ($user->type != 'employee' || in_array($permission, json_decode($user->groupe->permissions)) || in_array($permission, json_decode($user->employee->permissions ?? "") ?? []));
        });

    }
}
