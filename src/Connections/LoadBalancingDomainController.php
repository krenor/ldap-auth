<?php

namespace Krenor\LdapAuth\Connections;

class LoadBalancingDomainController extends DomainController
{

    /**
     * @return string
     */
    public function getHostname()
    {
        $random_key = array_rand($this->domain_controller);
        $random_dc  = $this->domain_controller[$random_key];

        return $this->protocol . $random_dc;
    }
}