<?php

namespace Krenor\LdapAuth\Connections;

use ErrorException;
use Krenor\LdapAuth\Contracts\ConnectionInterface;
use Krenor\LdapAuth\Contracts\DomainController;
use Krenor\LdapAuth\Exceptions\ConnectionException;

class LdapConnection implements ConnectionInterface
{

    /**
     * Concrete strategy for getting the connection of the domain controllers
     *
     * @var DomainController
     */
    protected $domainController;

    /**
     * Indicates whether backup rebinding should be used.
     * If this is set to false load balancing is used instead.
     *
     * @var bool
     */
    protected $backup = false;

    /**
     * Indicates whether or not to use SSL
     *
     * @var bool
     */
    protected $ssl = false;

    /**
     * Indicates whether or not to use TLS
     *
     * @var bool
     */
    protected $tls = false;

    /**
     * The current LDAP Connection
     *
     * @var resource
     */
    protected $connection;

    /**
     * Indicates whether or not the current connection is bound
     *
     * @var bool
     */
    protected $bound = false;


    /**
     * LdapConnection constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->backup = $config['backup_rebind'];
        $this->tls    = $config['tls'];
        $this->ssl    = $config['ssl'];

        $this->domainController = $this->getDomainControllerStrategy($config['domain_controller']);
    }


    /**
     * Initialises a Connection via hostname
     *
     * @return resource
     */
    public function connect()
    {
        $port = $this->ssl ? $this::PORT_SSL : $this::PORT;

        $hostname = $this->domainController->getHostname();

        return $this->connection = ldap_connect($hostname, $port);
    }


    /**
     * Binds LDAP connection to the server
     *
     * @param $username
     * @param $password
     *
     * @return bool
     *
     * @throws ConnectionException
     */
    public function bind($username, $password)
    {
        // Tries to run the LDAP Connection as TLS
        if ($this->tls) {
            if ( ! ldap_start_tls($this->connection)) {
                throw new ConnectionException('Unable to Connect to LDAP using TLS.');
            }
        }

        try {
            $this->bound = ldap_bind($this->connection, $username, $password);
        } catch (ErrorException $e) {
            $this->bound = false;
        }

        return $this->bound;
    }


    /**
     * @param $option
     * @param $value
     *
     * @return bool
     */
    public function option($option, $value)
    {
        return ldap_set_option($this->connection, $option, $value);
    }


    /**
     * @return string
     */
    public function error()
    {
        return ldap_error($this->connection);
    }


    /**
     * @param string $dn
     * @param string $identifier
     * @param array  $fields
     *
     * @return resource
     */
    public function search($dn, $identifier, array $fields)
    {
        return ldap_search($this->connection, $dn, $identifier, $fields);
    }


    /**
     * @param $result
     *
     * @return array
     */
    public function entry($result)
    {
        return ldap_get_entries($this->connection, $result);
    }


    /**
     * @return bool
     */
    public function bound()
    {
        return $this->bound;
    }


    /**
     * @return bool
     */
    public function ssl()
    {
        return $this->ssl;
    }


    /**
     * @return bool
     */
    public function tls()
    {
        return $this->tls;
    }


    /**
     * @return resource
     */
    public function connection()
    {
        return $this->connection;
    }


    /**
     * Get the concrete strategy class for retrieving the hostname.
     *
     * @param array $domain_controller
     *
     * @return \Krenor\LdapAuth\Connections\DomainController
     */
    private function getDomainControllerStrategy(array $domain_controller)
    {
        $protocol = $this->ssl ? $this::PROTOCOL_SSL : $this::PROTOCOL;

        if (count($domain_controller) === 1) {
            return new SingleDomainController($protocol, $domain_controller);
        }

        if ($this->backup === true) {
            return new RebindDomainController($protocol, $domain_controller);
        } else {
            return new LoadBalancingDomainController($protocol, $domain_controller);
        }
    }

}