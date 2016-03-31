<?php

namespace Krenor\LdapAuth\Exceptions;

use Exception;

class EmptySearchResultException extends Exception
{
    
    public function __construct()
    {
        parent::__construct('The search query returned zero results.');
    }

}