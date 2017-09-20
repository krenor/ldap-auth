<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Domain Controllers
    |--------------------------------------------------------------------------
    |
    | The domain controller(s) located in an Active Directory network to connect
    | to. You can insert as many host names or as little as you'd like
    | depending on your forest (with a minimum of one).
    |
    */

    'domain_controller' => [
        'dns.example.local',
        'dns-2.example.local',
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

    'baseDN' => 'OU=People,DC=dns,DC=example,DC=local',

    'connection' => [

        /*
        |--------------------------------------------------------------------------
        | Rebinding
        |--------------------------------------------------------------------------
        |
        | Indicates to use the domain controllers sequentially if the previous
        | controller can't be reached. Otherwise a random controller will be
        | chosen to connect to.
        |
        */

        'rebind' => true,

        /*
        |--------------------------------------------------------------------------
        | SSL & TLS
        |--------------------------------------------------------------------------
        |
        | One of these options are recommended if you have the ability to connect
        | to your server securely. One of these can be set to true, the other
        | one must be false due to different ports the protocols use.
        |
        */

        'ssl' => false,
        'tls' => false,

        /*
        |--------------------------------------------------------------------------
        | Service Account
        |--------------------------------------------------------------------------
        |
        | When connecting to an AD server, a privileged username and password
        | might be required. This is to prevent anonymous bindings running
        | operations on the server. Any account that has permissions can
        | be used. You can also emit this fields to bind anonymously.
        |
        */

        'privileged' => [
            'username' => 'admin',
            'password' => 'admin',
        ],
    ],

    'search' => [

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

        'filter' => 'sAMAccountName',

        /*
        |--------------------------------------------------
        | Search Fields
        |--------------------------------------------------
        |
        | The fields options defined what fields you want the be returned on a successful
        | query result. Note: The distinguished name is always returned.
        |
        */

        'fields' => [
            'samaccountname',
            'displayname',
            'memberof',
        ],
    ],
];
