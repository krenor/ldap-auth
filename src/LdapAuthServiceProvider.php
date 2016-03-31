<?php

namespace Krenor\LdapAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Krenor\LdapAuth\Exceptions\MissingConfigurationException;
use Krenor\LdapAuth\Objects\Ldap;

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
        $config = __DIR__ . '/config/ldap.php';

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
        // Register 'ldap' as authentication method
        Auth::provider('ldap', function ($app) {

            $model = $app['config']['auth']['providers']['ldap-users']['model'];

            // Create a new LDAP connection
            $connection = new Ldap($this->getLdapConfig());

            return new LdapAuthUserProvider($connection, $model);
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ 'auth' ];
    }


    /**
     * @return array
     *
     * @throws MissingConfigurationException
     */
    private function getLdapConfig()
    {
        if (is_array($this->app['config']['ldap'])) {
            return $this->app['config']['ldap'];
        }

        throw new MissingConfigurationException();
    }

}