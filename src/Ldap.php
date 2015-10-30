<?php

namespace Krenor\LdapAuth;

use Krenor\LdapAuth\Connections\LdapConnection;
use Krenor\LdapAuth\Contracts\ConnectionInterface;
use Krenor\LdapAuth\Exceptions\MissingConfigurationException;

class Ldap {

    /**
     * The account suffix for the domain domain
     *
     * @var string
     */
    protected $suffix;

    /**
     * The base distinguished name for the domain
     *
     * @var string
     */
    protected $base_dn;

    /**
     * If no anonymous login is allowed
     *
     * @var string
     */
    private $admin_user;

    /**
     * If no anonymous login is allowed
     *
     * @var string
     */
    private $admin_pass;

    /**
     * Current LDAP Connection
     *
     * @var LdapConnection
     */
    protected $ldap;

    /**
     * Default fields to fetch a search or read by
     *
     * @var array
     */
    protected $fields = ['samaccountname', 'displayname', 'memberof'];

    /**
     * Default filter to execute a search query on
     *
     * @var string
     */
    private $search_filter = "sAMAccountName";

    /**
     * Tries to connect and bind to the LDAP
     *
     * @param array $options
     *
     * @throws MissingConfigurationException
     */
    public function __construct($options)
    {
        $config = $this->bindConfig($options);

        // Build Common Name from Config file and append to base DN
        $this->admin_user = 'CN=' . $this->admin_user . ',' . $this->base_dn;

        $this->ldap = new LdapConnection($config);
        $this->connect($this->ldap);
    }

    /**
     * Initializes the connecting parameters.
     * The actual connect happens with $this->ldap->bind()
     *
     * @param ConnectionInterface $connection
     *
     * @throws Exceptions\ConnectionException
     */
    protected function connect(ConnectionInterface $connection)
    {
        $this->ldap->connect();

        $this->ldap->option(LDAP_OPT_PROTOCOL_VERSION, $connection::PROTOCOL);
        $this->ldap->option(LDAP_OPT_REFERRALS, $connection::REFERRALS);
        $this->ldap->option(LDAP_OPT_TIMELIMIT, $connection::TIMELIMIT);
        $this->ldap->option(LDAP_OPT_NETWORK_TIMEOUT, $connection::TIMELIMIT);

        // For debug purposes only!
        // $this->ldap->option(LDAP_OPT_DEBUG_LEVEL, 7);

        $this->ldap->bind($this->admin_user, $this->admin_pass);
    }

    /**
     * Execute a search query in the entire LDAP tree
     *
     * @param string $filter msdn.microsoft.com/En-US/library/aa746475.aspx
     * @param array $fields specific attributes to be returned. Defaults are set
     * as $fields in this class. DN is always returned, no matter what.
     *
     * @return array $entry|null
     */
    public function find($filter, array $fields = [])
    {
        $results =  $this->ldap->search(
            $this->base_dn,
            $this->search_filter . '=' . $filter,
            ($fields ? $fields : $this->fields)
        );

        if(count($results) > 0){
            $entry = $this->ldap->entry($results);

            // Returning a single LDAP entry
            if(isset($entry[0]) && !empty($entry[0])) {
                return $entry[0];
            }
        }

        return null;
    }

    /**
     * Rebinds with a given DN and Password
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     *
     * @throws Exceptions\ConnectionException
     */
    public function auth($username, $password)
    {
        return $this->ldap->bind($username, $password);
    }

    /**
     * Bind configuration file to class properties
     * as long as these already exist
     *
     * @param array $config Complete config
     *
     * @return array $config Striped config
     */
    private function bindConfig(array $config)
    {
        foreach($config as $key => $value){
            if(property_exists($this, $key) ){
                $this->{$key} = $value;
                // Remove config key
                unset($config[$key]);
            }
        }

        // Every non-property key is left over and returned
        return $config;
    }

}