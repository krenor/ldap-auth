<?php

namespace Krenor\LdapAuth\Connections;

abstract class DomainController
{

    /**
     * Connection Protocol.
     *
     * @var string
     */
    protected $protocol;

    /**
     * Collection of domain controllers.
     *
     * @var array
     */
    protected $domain_controller = [];


    /**
     * DomainController constructor.
     *
     * @param string $protocol
     * @param array $domain_controller
     */
    public function __construct($protocol, array $domain_controller)
    {
        $this->protocol = $protocol;
        $this->domain_controller = $domain_controller;
    }


    /**
     * Get the hostname for an LDAP binding.
     * 
     * @return string
     */
    abstract public function getHostname();

}