<?php

namespace Krenor\LdapAuth\Exceptions;

use Exception;

class MissingConfigurationException extends Exception
{

    public function __construct()
    {
        parent::__construct("Please ensure that a ldap.php file is present in the config/ root directory.");
    }

}