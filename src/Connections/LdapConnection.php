<?php

namespace Krenor\LdapAuth\Connections;

use ErrorException;
use Krenor\LdapAuth\Contracts\ConnectionInterface;
use Krenor\LdapAuth\Exceptions\ConnectionException;

class LdapConnection implements ConnectionInterface
{
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
        if($config['tls']) $this->tls = true;
        if($config['ssl']) $this->ssl = true;
    }

    /**
     * Initialises a Connection via hostname
     *
     * @param string $hostname
     *
     * @return resource
     */
    public function connect($hostname)
    {
        $protocol = $this->ssl ? $this::PROTOCOL_SSL : $this::PROTOCOL;
        $port = $this->ssl ? $this::PORT_SSL : $this::PORT;

        return $this->connection = ldap_connect($protocol . $hostname, $port);
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
        if($this->tls){
            if(!ldap_start_tls($this->connection)){
                throw new ConnectionException('Unable to Connect to LDAP using TLS.');
            }
        }

        try{
            $this->bound = ldap_bind($this->connection, $username, $password);
        }
        catch(ErrorException $e){
            $this->bound = false;
        }

        return $this->bound;
    }

    /**
     * @param $option
     * @param $value
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
     * @param array $fields
     * @return resource
     */
    public function search($dn, $filter, array $fields)
    {
        return ldap_search($this->connection, $dn, $filter, $fields);
    }

    /**
     * @param $result
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