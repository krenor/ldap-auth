<?php

namespace Krenor\LdapAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class LdapAuthServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $config = __DIR__ . '/config.php';

        // Add publishable configuration
        $this->publishes([
            $config => config_path('ldap.php'),
        ], 'ldap');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Auth::provider('ldap', function ($app) {
            $model = config('auth.providers.ldap-users.model');

            $ldap = new Ldap($app['config']['ldap']);

            return new LdapAuthUserProvider($ldap, $model);
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['auth'];
    }
}
