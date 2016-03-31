<?php

return [
    'suffix' => '@dns.example.local',

    /*
    |--------------------------------------------------
    | Domain Controllers
    |--------------------------------------------------
    |
    | The domain controllers option is an array of servers located on your
    | network that serve Active Directory. You can insert as many servers or
    | as little as you'd like depending on your forest (with a minimum of one).
    |
    */
    'domain_controller' => [
        'dns.example.local',
        'dns-2.example.local'
    ],

    /*
    |--------------------------------------------------
    | Base Distinguished Name
    |--------------------------------------------------
    |
    | The base distinguished name is the base distinguished name you'd like
    | to perform operations on. An example base DN would be DC=dns,DC=example,DC=local.
    |
    | If none defined, then it will try to find it automatically by querying your server.
    | It's highly recommended to include it to limit queries executed per request.
    |
    */
    'base_dn' => 'OU=People,DC=dns,DC=example,DC=local',

    /*
    |--------------------------------------------------
    | Search Filter
    |--------------------------------------------------
    |
    | The filter option defines (you guessed it) on what filter to execute a query on.
    | The default filter is "sAMAccountName". For more information please check
    | msdn.microsoft.com/En-US/library/aa746475.aspx
    |
    */
    'search_filter' => 'sAMAccountName',

    /*
    |--------------------------------------------------
    | Search Fields
    |--------------------------------------------------
    |
    | The fields options defined what fields you want the be returned on a successful
    | query result. Note: The distinguished name is always returned.
    |
    */
    'search_fields' => [
        'samaccountname',
        'displayname',
        'memberof'
    ],
    
    /*
    |--------------------------------------------------
    | Backup Rebinding
    |--------------------------------------------------
    |
    | This options indicates to use the host names sequentially. This package will try
    | to connect to the first domain controller. If it's not reachable the next DC
    | will be tried.
    |
    | If this option is set to false load balancing will be used instead for multiple DC.
    |
    */
    'backup_rebind' => true,

    /*
    |--------------------------------------------------
    | SSL & TLS
    |--------------------------------------------------
    |
    | One of these options are recommended if you have the ability to connect to your server
    | securely. Ensure that only one option can be true. The other one must be false.
    |
    */
    'ssl' => false,
    'tls' => false,

    /*
    |--------------------------------------------------------------------------
    | Administrator Username & Password
    |--------------------------------------------------------------------------
    |
    | When connecting to your AD server, an administrator username and
    | password is required to be able to query and run operations on
    | your server(s). You can use any user account that has
    | these permissions to prevent anonymous bindings.
    |
    */
    'admin_user' => 'admin',
    'admin_pass' => 'admin',
];