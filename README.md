# ACL Package for Laravel

Simple access control based on User - Roles - Permissions, adapted from OctoberCMS https://github.com/octobercms/october

## Install

To install via Composer, run the following command:

    composer require macgriog/laravel-acl

## Add Service Provider

Then add the following service provider in config/app.php.

    'providers' => [
        Macgriog\Acl\AclServiceProvider::class,
    ],

## Publish Database Migrations

    php artisan vendor:publish --provider="Macgriog\Acl\AclServiceProvider"


## How it works

Access is granted based on a User having a specific permission.

Roles are considered sets of permissions.

A User can have permissions.

A User can have multiple roles assigned.

All permissions get merged.


Permissions are persisted in a JSON column, e.g.

    {"backend.read" : 1, "backend.write" : -1, "system.shutdown": 0}

1 = permission grantend
0 = permission not granted
-1 = force permission not granted (e.g. if grant was inherited from Role)


## Tests

Run tests via PHPUnit:

    vendor/bin/phpunit
