[![Build Status](https://travis-ci.org/macghriogair/laravel-acl.svg?branch=master)](https://travis-ci.org/macghriogair/laravel-acl)


# ACL Package for Laravel

Simple access control based on User - Roles - Permissions, adapted from OctoberCMS.

## How it works

Access is granted based on a User having a specific permission.

Roles are considered sets of permissions.

A User can have permissions.

A User can have multiple roles assigned.

For a single User, all permissions get merged.


Permissions are persisted in a JSON column, e.g.

    {"backend.read" : 1, "backend.write" : -1, "system.shutdown": 0}

1 = permission grantend
0 = permission not granted
-1 = forcibly revoke granted permission (e.g. if was inherited from Role)

## Installation

To install via Composer, run the following command:

    composer require macgriog/laravel-acl

## For Laravel 5.4 and 5.3: add Service Provider

*Note: Since Laravel 5.5 the ServiceProviders are being registered automatically.*

If you are using an older version or have opted out of auto-discovery, add the following in config/app.php.

    'providers' => [
        Macgriog\Acl\AclServiceProvider::class,
    ],

## Database Migrations

This package works is meant to be used along a relational database like MariaDB. It expects 2 tables: `users` and `roles`. See the [migration files](./database/migrations) for Schema details.

In a fresh Laravel install you can publish and run the necessary migrations via:

    php artisan vendor:publish --provider="Macgriog\Acl\AclServiceProvider"
    php artisan migrate

*Please note, that this will require the `doctrine/dbal` package to be installed. It is not added as a composer dependency, because running the migrations is completely optional, depending on your use case.*

## Usage

### User Model

Add the Trait to your User model and define the Role relation:
    
    <?php
    
    namespace App;
    
    use Macgriog\Acl\Models\Role;
    use Macgriog\Acl\Traits\UserPermissions;
    
    class User extends Authenticatable
    {
        use UserPermissions;
    
        /**
         * @return mixed
         */
        public function roles()
        {
            return $this->belongsToMany(
                Role::class,
                'role_user',
                'user_id',
                'role_id'
            );
        }
    
        public function getRoles()
        {
            if ($this->roles) {
                return $this->roles;
            }
    
            return $this->roles = $this->roles();
        }
    
    }


Now you can check for permissions like so:

    $user->hasAccess('update'); // true|false if User has Permission
    $user->hasAccess(['update', 'create']) // true|false if ALL permissions are given
    $user->hasAnyAccess(['update', 'create']) // true|false if ANY permission is given

*Note: `hasAccess` and `hasAnyAccess` will check for a `is_root` attribute on the User. If a User is Root permissions are always considered as granted. You may adapt this behaviour to your needs using Eloquent's attribute accessors.*

And you can set permissions:

    $user->permissions = ['read' => true, 'update' => true];
    $user->save();

## Role Model

There is a sample [Role class](./src/Models/Role.php) ready for usage. Roles - like Users - have a permissions column. This makes it easy to define sets of permissions and re-use them between Users by assigning Roles to them.

## Route Middleware for Access Control

The package comes with a Middleware to be registered in your Route Middleware group:
    
Register in App\Http\Kernel.php:
    
    protected $routeMiddleware = [
        // ...
        'acl' => \Dreipc\Acl\Middleware\CheckPermission::class,
    ];

Example usage: 

    Route::get('backend')->middleware('acl:backend.access');

It takes the required permissions as arguments and aborts if not ALL of them are granted to the current User.

## Tests

Run tests via PHPUnit:

    vendor/bin/phpunit


## References

* OctoberCMS https://github.com/octobercms/october
