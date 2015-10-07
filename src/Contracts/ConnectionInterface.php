<?php

namespace Krenor\LdapAuth\Contracts;

interface ConnectionInterface
{
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
     * LDAP Protocol Version
     *
     * @var integer
     */
    const VERSION = 3;

    /**
     * Whether to automatically follow referrals returned by the LDAP server
     *
     * @var boolean
     */
    const REFERRALS = false;

    /**
     * Connects the specified hostname to the LDAP server
     *
     * @param string $hostname
     *
     * @return resource
     */
    public function connect($hostname);

    /**
     * Binds the LDAP connection to the server with login credentials
     *
     * @param $username
     * @param $password
     *
     * @return bool
     */
    public function bind($username, $password);

    /**
     * Sets an option key value pair for the current connection
     *
     * @param $option
     * @param $value
     *
     * @return bool
     */
    public function option($option, $value);

    /**
     * Searches in LDAP with the scope of LDAP_SCOPE_SUBTREE
     *
     * @param string $dn
     * @param string $filter
     * @param array $fields
     *
     * @return array
     */
    public function search($dn, $filter, array $fields);

    /**
     * Check if connection is bound
     *
     * @return bool
     */
    public function bound();

    /**
     * Check if connection is using tls
     *
     * @return bool
     */
    public function tls();

    /**
     * Check if connection is using ssl
     *
     * @return bool
     */
    public function ssl();

    /**
     * Retrieve last error occurrence
     *
     * @return string
     */
    public function error();

    /**
     * Retrieve current LDAP connection
     *
     * @return resource
     */
    public function connection();

    /**
     * Retrieve LDAP Entry
     *
     * @param $resultset
     *
     * @return array
     */
    public function entry($resultset);

}