<?php

namespace Krenor\LdapAuth\Connections;

use ErrorException;
use Krenor\LdapAuth\Contracts\ConnectionInterface;
use Krenor\LdapAuth\Exceptions\ConnectionException;

class LdapConnection implements ConnectionInterface
{

    /**
     * Array of domain controller(s) to balance LDAP queries
     *
     * @var array
     */
    protected $domainController = [ ];

    /**
     * Indicates whether or not to use the array of domain controller sequentially
     * So on downtime of a server it checks if the next    one can be reached.
     * If this is set to false load balancing is used instead for multiple dc's
     *
     * @var bool
     */
    protected $useBackup = false;

    /**
     * Indicates whether or not to use SSL
     *
     * @var bool
     */
    protected $ssl = false;

    /**
     * Indicates whether or not to use TLS
     * If it's used ensure that ssl is set to false and vice-versa
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
     * @var bool
     */
    protected $bound = false;


    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->domainController = $config['domain_controller'];

        if ($config['tls']) {
            $this->tls = true;
        }
        if ($config['ssl']) {
            $this->ssl = true;
        }
        if ($config['backup_rebind']) {
            $this->useBackup = true;
        }
    }


    /**
     * Initialises a Connection via hostname
     *
     * @return resource
     */
    public function connect()
    {
        $port = $this->ssl ? $this::PORT_SSL : $this::PORT;

        $hostname = $this->chooseDomainController();

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
     * Chooses based on the configuration which domain controller to connect to
     *
     * @return string
     */
    private function chooseDomainController()
    {
        $protocol = $this->ssl ? $this::PROTOCOL_SSL : $this::PROTOCOL;
        $count    = count($this->domainController);

        if ($count === 1) {
            // Single domain controller, so use this one
            return $protocol . $this->domainController[0];
        }

        if ($this->useBackup === true) {
            $connectionString = null;

            foreach ($this->domainController as $dc) {
                $connectionString .= $protocol . $dc . ' ';
            }

            // In case of using backup_rebind we have to build a string of all
            // domain controller which will be walked through sequentially
            return $connectionString;
        }

        $loadBalancedDC = $this->domainController[array_rand($this->domainController)];

        // Otherwise use "load balancing" by using a random domain controller
        return $protocol . $loadBalancedDC;
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
     * @param string $filter
     * @param array  $fields
     *
     * @return resource
     */
    public function search($dn, $filter, array $fields)
    {
        return ldap_search($this->connection, $dn, $filter, $fields);
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

}