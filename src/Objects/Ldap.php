<?php

namespace Krenor\LdapAuth\Objects;

use Krenor\LdapAuth\Connections\LdapConnection;
use Krenor\LdapAuth\Contracts\ConnectionInterface;
use Krenor\LdapAuth\Exceptions\EmptySearchResultException;
use Krenor\LdapAuth\Exceptions\MissingConfigurationException;

class Ldap
{

    /**
     * The current LDAP Connection.
     *
     * @var LdapConnection
     */
    protected $ldap;

    /**
     * The account suffix for the domain.
     *
     * @var string
     */
    protected $suffix;

    /**
     * The base distinguished name for the domain.
     *
     * @var string
     */
    protected $base_dn;

    /**
     * The filter to execute a search query on.
     *
     * @var string
     */
    private $search_filter;

    /**
     * The fields to fetch from a search result.
     *
     * @var array
     */
    protected $search_fields = [ ];

    /**
     * User with permissions for preventing anonymous bindings.
     *
     * @var string
     */
    private $admin_user;

    /**
     * Password of the user with permissions for preventing anonymous bindings.
     *
     * @var string
     */
    private $admin_pass;


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

        $this->ldap->option(LDAP_OPT_PROTOCOL_VERSION, $connection::VERSION);
        $this->ldap->option(LDAP_OPT_REFERRALS, $connection::REFERRALS);
        $this->ldap->option(LDAP_OPT_TIMELIMIT, $connection::TIMELIMIT);
        $this->ldap->option(LDAP_OPT_NETWORK_TIMEOUT, $connection::TIMELIMIT);

        // For debug purposes only.
        // $this->ldap->option(LDAP_OPT_DEBUG_LEVEL, 7);

        $this->ldap->bind($this->admin_user, $this->admin_pass);
    }


    /**
     * Execute a search query in the LDAP Base DN.
     *
     * @param string $identifier msdn.microsoft.com/En-US/library/aa746475.aspx
     * @param array  $fields     specific attributes to be returned
     *
     * @return array $entry
     * @throws EmptySearchResultException
     */
    public function find($identifier, array $fields = [ ])
    {
        // Get all result entries
        $results = $this->ldap->search(
            $this->base_dn,
            $this->search_filter . '=' . $identifier,
            ( $fields ?: $this->search_fields )
        );

        if (count($results) > 0) {
            $entry = $this->ldap->entry($results);

            // Returning a single LDAP entry
            if (isset( $entry[0] ) && ! empty( $entry[0] )) {
                return $entry[0];
            }
        }

        throw new EmptySearchResultException;
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
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
                // Remove config key
                unset( $config[$key] );
            }
        }

        // Every non-property key is left over and returned
        return $config;
    }

}
