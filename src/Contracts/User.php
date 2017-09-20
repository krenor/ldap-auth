<?php

namespace Krenor\LdapAuth\Contracts;

interface User
{
    /**
     * Check if the User is a member of given group.
     *
     * @param string $group
     * 
     * @return bool
     */
    public function member(string $group): bool;
}
