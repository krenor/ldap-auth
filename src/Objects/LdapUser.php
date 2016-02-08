<?php

namespace Krenor\LdapAuth\Objects;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Krenor\LdapAuth\Contracts\UserInterface as LdapUserContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

class LdapUser implements UserContract, AuthorizableContract, LdapUserContract
{

    use Authorizable;

    /**
     * Most of the ldap user's attributes.
     *
     * @var array
     */
    protected $attributes;


    /**
     * Build an LdapUser object from the LDAP entry
     *
     * @param array $entry
     *
     * @return void
     */
    public function build(array $entry)
    {
        $this->buildAttributesFromLdap($entry);
    }


    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'samaccountname';
    }


    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes[$this->getAuthIdentifierName()];
    }


    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        // this shouldn't be needed as you cannot directly access the password
    }


    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        // this shouldn't be needed as user / password is in ldap
    }


    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        // this shouldn't be needed as user / password is in ldap
    }


    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     *
     * @return void
     */
    public function setRememberToken($value)
    {
        // this shouldn't be needed as user / password is in ldap
    }


    /**
     * Dynamically access the user's attributes.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }


    /**
     * Dynamically set an attribute on the user.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }


    /**
     * Dynamically check if a value is set on the user.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset( $this->attributes[$key] );
    }


    /**
     * Setting of the LdapUser attributes
     *
     * @param array $entry
     */
    private function buildAttributesFromLdap($entry)
    {
        $this->attributes['display_name']   = $entry['displayname'][0];
        $this->attributes['samaccountname'] = $entry['samaccountname'][0];
        $this->attributes['dn']             = $entry['dn'];
        $this->attributes['member_of']      = $entry['memberof'];

        // Just for readability, unsetting count as we only fetch one user
        unset( $this->attributes['member_of']['count'] );
    }


    /**
     * Check if the LdapUser is a member of requested group
     *
     * @param string $group
     *
     * @return bool
     */
    public function isMemberOf($group)
    {
        foreach ($this->attributes['member_of'] as $groups) {
            if (preg_match('/^CN=' . $group . '/', $groups)) {
                return true;
            }
        }

        return false;
    }

}