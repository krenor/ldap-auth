<?php


namespace Krenor\LdapAuth;

use Krenor\LdapAuth\Contracts\User as LdapContract;
use Illuminate\Auth\GenericUser as User;

class LdapUser extends User implements LdapContract
{
    /**
     * LdapUser constructor.
     *
     * @param array $entry
     *
     * @return self
     */
    public function __construct(array $entry)
    {
        $this->bootstrapAttributes($entry);
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'sAMAccountName';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return null;
    }

    /**
     * Get the "remember me" token value.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the "remember me" token value.
     *
     * @param  string $value
     *
     * @return void
     */
    public function setRememberToken($value)
    {
        return null;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * Check if the User is a member of given group.
     *
     * @param string $needle
     *
     * @return bool
     */
    public function member(string $needle): bool
    {
        if (!$this->memberOf) {
            return null;
        }

        $haystack = collect($this->memberOf);

        return $haystack->filter(function ($group) use ($needle) {
            return preg_match("/^CN=$needle/", $group);
        })->isNotEmpty();
    }

    /**
     * Bootstrap the attributes by the given config fields.
     * The ldap_search function returns the attributes in lowercase, which
     * will be mapped to the configured field name format.
     *
     * @param array $entry
     */
    private function bootstrapAttributes(array $entry): void
    {
        $this->distinguishedName = $entry['dn'];

        $fields = collect(config('ldap.search.fields'));

        $fields->map(function ($field) {
            return strtolower($field);
        })->each(function ($field, $index) use ($entry, $fields) {
            $value = $entry[$field][0] ?? null;

            $this->{$fields[$index]} = $value;
        });
    }
}
