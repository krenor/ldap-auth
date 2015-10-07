<?php

namespace Krenor\LdapAuth\Contracts;

interface UserInterface
{
    /**
     * Build an LdapUser object from the LDAP entry
     *
     * @param array $entry
     * @return void
     */
    public function build(array $entry);

    /**
     * Check if the LdapUser is a member of requested group
     *
     * @param string $group
     * @return bool
     */
    public function isMemberOf($group);

}