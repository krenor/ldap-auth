<?php

namespace Krenor\LdapAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class LdapAuthUserProvider implements UserProvider
{
    /**
     * @var Ldap
     */
    protected $ldap;

    /**
     * @var string
     */
    protected $model;

    /**
     * LdapAuthUserProvider constructor.
     *
     * @param Ldap $ldap
     * @param string $model
     */
    public function __construct(Ldap $ldap, string $model)
    {
        $this->ldap = $ldap;
        $this->model = $model;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     *
     * @return LdapUser|null
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
     *
     * @return null
     */
    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  Authenticatable $user
     * @param  string $token
     *
     * @return null
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        return null;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return LdapUser
     */
    public function retrieveByCredentials(array $credentials)
    {
        $result = $this->ldap->search($credentials['username']);

        return new $this->model($result);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  Authenticatable $user
     * @param  array $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (!$user instanceof LdapUser) {
            return false;
        }

        return $this->ldap->bind(
            $user->distinguishedName,
            $credentials['password']
        );
    }
}
