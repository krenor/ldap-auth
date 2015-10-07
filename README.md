[![Latest Stable Version](https://img.shields.io/packagist/v/krenor/ldap-auth.svg?style=flat-square)](https://packagist.org/packages/krenor/ldap-auth)
[![License](https://img.shields.io/packagist/l/krenor/ldap-auth.svg?style=flat-square)](https://packagist.org/packages/krenor/ldap-auth)

# ldap-auth

Very basic **READ ONLY** LDAP authentication driver for [Laravel 5.1.11+](http://laravel.com/)

## Installation

### Step 1: Install Through Composer

Add to your root composer.json and install with `composer install` or `composer update`

    {
      require: {
        "krenor/ldap-auth": "~1.0"
      }
    }

or use `composer require krenor/ldap-auth` in your console.

### Step 2: Add the Service Provider

Modify your `config/app.php` file and add the service provider to the providers array.

    'Krenor\LdapAuth\LdapAuthServiceProvider::class,'


## Configuration

### Step 1: Tweak the basic authentication


Update your `config/auth.php` to use **ldap** as authentication and the **LdapUser** Class.

    'driver' => 'ldap',

    'model' => Krenor\LdapAuth\Objects\LdapUser::class,


### Step 2: Create an LDAP config

Add a **ldap.php** to your config directory.
It should look like this.

```php
<?php

return [
    'suffix' => '@example.local',
    'domain_controller' => 'dns.example.local',
    'base_dn' => 'OU=People,DC=example,DC=local',
    'ssl' => false,	// if using TLS this MUST be false
    'tls' => false, // // if using SSL this MUST be false
    'admin_user' => 'admin', // Prevent anonymous bindings
    'admin_pass' => 'admin' // Prevent anonymous bindings
];
```

You may use an array of Domain Controllers instead of a single one.
```php
'domain_controller' => ['dns1.example.local', 'dns2.example.local']
```

## Usage

### Authentication
Look up here for an [Example](https://github.com/krenor/ldap-auth/blob/master/EXAMPLE.md) or
Look up here for all [Guard methods](https://github.com/neoascetic/laravel-framework/blob/master/src/Illuminate/Auth/Guard.php) using `$this->auth`.


## Contributing

### Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)**

- **Add tests** - Your patch won't be accepted if it doesn't have tests.

- **Document any changes** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Create feature branches** - Use `git checkout -b my-new-feature`

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.


## Licence

ldap-auth is distributed under the terms of the [MIT license](https://github.com/krenor/ldap-auth/blob/master/LICENSE.md)