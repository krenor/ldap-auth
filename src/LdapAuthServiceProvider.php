<?php

namespace Krenor\LdapAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Krenor\LdapAuth\Exceptions\MissingConfigurationException;

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
        // Register 'ldap' as authentication method
        Auth::provider('ldap', function($app){
            // Create new LDAP connection based on configuration files
            $ldap = new Ldap( $this->getLdapConfig() );

            return new LdapAuthUserProvider(
                $ldap, $app['config']['auth']['providers']['ldap-users']['model']
            );
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
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

    /**
     * @return array
     *
     * @throws MissingConfigurationException
     */
    private function getLdapConfig()
    {
        if( is_array($this->app['config']['ldap']) ){
            return $this->app['config']['ldap'];
        }

        throw new MissingConfigurationException();
    }

}