<?php

namespace Krenor\LdapAuth\Connections;

class RebindDomainController extends DomainController
{

    /**
     * @return string
     */
    public function getHostname()
    {
        $hostname = null;

        foreach ($this->domain_controller as $dc) {
            $hostname .= $this->protocol . "$dc ";
        }

        return $hostname;
    }
}