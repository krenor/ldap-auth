<?php

namespace Krenor\LdapAuth\Contracts;

interface Connection
{
    /**
     * Error code when the username is valid but the combination of password and user credential is invalid.
     *
     * @var int
     */
    const LDAP_INVALID_CREDENTIALS = 49;

    /**
     * Error code when the username is valid but the combination of password and user credential is invalid.
     *
     * @var string
     */
    const AD_INVALID_CREDENTIALS = '52e';

    /**
     * The SSL LDAP protocol string.
     *
     * @var string
     */
    const PROTOCOL_SSL = 'ldaps://';

    /**
     * The non-SSL LDAP protocol string.
     *
     * @var string
     */
    const PROTOCOL = 'ldap://';

    /**
     * The LDAP SSL Port number.
     *
     * @var string
     */
    const PORT_SSL = '636';

    /**
     * The non SSL LDAP port number.
     *
     * @var string
     */
    const PORT = '389';

    /**
     * LDAP Protocol Version.
     *
     * @var integer
     */
    const VERSION = 3;

    /**
     * Whether to automatically follow referrals returned by the LDAP server.
     *
     * @var integer
     */
    const REFERRALS = 0; // FIXME: INTEGER?!

    /**
     * Defines the time-out limit for connecting and binding in seconds.
     *
     * @var integer
     */
    const TIMELIMIT = 6;

    /**
     * Binds the LDAP connection to the server with login credentials.
     *
     * @param string $username
     * @param string $password
     *
     * @throws ConnectionException
     *
     * @return bool
     */
    public function bind(string $username, string $password): bool;

    /**
     * Searches in LDAP with the scope of LDAP_SCOPE_SUBTREE.
     *
     * @param string $identifier
     * @param array $fields
     *
     * @return array
     */
    public function search(string $identifier, $fields = []): array;

    /**
     * Sets an option key value pair for the current connection.
     *
     * @param string $option
     * @param mixed $value
     *
     * @return bool
     */
    public function configure(string $option, $value): bool;
}
