<?php

namespace Krenor\LdapAuth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class LdapAuthUserProvider implements UserProvider
{
    /**
     * LDAP Wrapper.
     *
     * @var Ldap
     */
    protected $ldap;

    /**
     * LDAP Auth User Class.
     *
     * @var string
     */
    protected $model;

    /**
     * @param Ldap $ldap
     * @param string $model
     */
    public function __construct(Ldap $ldap, $model)
    {
        $this->ldap = $ldap;
        $this->model = $model;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->retrieveByCredentials(
            ['username' => $identifier]
        );
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // this shouldn't be needed as user / password is in ldap
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // this shouldn't be needed as user / password is in ldap
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Krenor\LdapAuth\Objects\LdapUser|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $username = $credentials['username'];
        $result = $this->ldap->find($username);

        if( !is_null($result) ){
            $user = new $this->model;
            $user->build( $result );

            return $user;
        }

        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->ldap->auth(
            $user->dn,
            $credentials['password']
        );
    }

}