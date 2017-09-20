<?php


namespace Krenor\LdapAuth;

use Krenor\LdapAuth\Contracts\Connection as ConnectionContract;
use Krenor\LdapAuth\Exceptions\ConnectionException;
use Illuminate\Support\Collection;
use Exception;

class Ldap implements ConnectionContract
{
    /**
     * @var resource
     */
    protected $connection;

    /**
     * @var bool
     */
    protected $bound;

    /**
     * @var Collection
     */
    protected $controller;

    /**
     * @var string
     */
    protected $baseDN;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Ldap constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->controller = collect($config['domain_controller']);
        $this->baseDN = $config['baseDN'];
        $this->settings = [
            'connection' => $config['connection'],
            'search'     => $config['search'],
        ];

        $this->connect();
    }

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
    public function bind(string $username, string $password): bool
    {
        if ($this->setting('connection.tls') && !ldap_start_tls($this->connection)) {
            throw new ConnectionException('Unable to connect to LDAP using the TLS protocol.');
        }

        try {
            return $this->bound = ldap_bind($this->connection, $username, $password);
        } catch (Exception $e) {
            $error = $this->retrieveError($e);

            throw new ConnectionException($error);
        }
    }

    /**
     * Searches in LDAP with the scope of LDAP_SCOPE_SUBTREE.
     *
     * @param string $identifier
     * @param array $fields
     *
     * @return array
     */
    public function search(string $identifier, $fields = []): array
    {
        $search = $this->setting('search');

        $result = ldap_search(
            $this->connection,
            $this->baseDN,
            "{$search['filter']}=$identifier",
            $fields ?: $search['fields']
        );

        $entries = ldap_get_entries($this->connection, $result);

        // Due to an unique identifier there can only be one entry.
        return $entries[0];
    }

    /**
     * Sets an option key value pair for the current connection.
     *
     * @param string $option
     * @param mixed $value
     *
     * @return bool
     */
    public function configure(string $option, $value): bool
    {
        return ldap_set_option($this->connection, $option, $value);
    }

    /**
     * Connects the specified hostname to the LDAP server.
     *
     * @return resource
     */
    private function connect()
    {
        $port = $this->setting('connection.ssl') ? $this::PORT_SSL : $this::PORT;

        $this->connection = ldap_connect($this->target(), $port);

        $this->configure(LDAP_OPT_PROTOCOL_VERSION, self::VERSION);
        $this->configure(LDAP_OPT_REFERRALS, self::REFERRALS);
        $this->configure(LDAP_OPT_TIMELIMIT, self::TIMELIMIT);
        $this->configure(LDAP_OPT_NETWORK_TIMEOUT, self::TIMELIMIT);

        if (env('APP_DEBUG')) {
            $this->configure(LDAP_OPT_DEBUG_LEVEL, 7);
        }

        return $this->establish();
    }

    /**
     * Determine the URL connection string.
     *
     * @return string
     */
    private function target(): string
    {
        $protocol = $this->setting('connection.ssl') ? $this::PROTOCOL_SSL : $this::PROTOCOL;

        if (count($this->controller) === 1) {
            return "{$protocol}{$this->controller->first()}";
        }

        if ($this->backup === true) {
            return $this->controller->map(function ($controller) use ($protocol) {
                return "{$protocol}{$controller}";
            })->implode(' ');
        }

        return $this->controller->random();
    }

    /**
     * Establish the connection to LDAP.
     *
     * @return bool
     */
    private function establish(): bool
    {
        $account = $this->setting('connection.privileged');

        $userDN = "CN={$account['username']},{$this->baseDN}";

        return $this->bind($userDN, $account['password']);
    }

    /**
     * Retrieve a setting via dot notation.
     *
     * @param string $key
     *
     * @return mixed
     */
    private function setting(string $key)
    {
        return array_get($this->settings, $key);
    }

    /**
     * @param Exception $e
     *
     * @return string
     */
    private function retrieveError(Exception $e): string
    {
        if (env('APP_DEBUG')) {
            ldap_get_option($this->connection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $error);

            if (!$error) {
                ldap_get_option($this->connection, LDAP_OPT_ERROR_STRING, $error);
            }
        }

        return $error ?? $e->getMessage();
    }

    /**
     * @return bool
     */
    public function isBound(): bool
    {
        return $this->bound;
    }
}
