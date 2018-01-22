<?php

use Macgriog\Acl\Traits\RolePermissions;
use Macgriog\Acl\Traits\UserPermissions;

/**
 * @date    2017-01-04
 * @file    UserPermissionsTest.php
 * @author  Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

class UserPermissionsTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function it_merges_permissions_with_role_permissions()
    {
        $role = new TestRole;
        $role->permissions = [
            'do.this' => 0,
            'do.that' => 1,
            'do.else' => 1,
            'make.more' => 1
        ];

        $person = new TestPerson;
        $person->permissions = [
            'do.this' => 1,
            'do.that' => 0,
            'do.else' => -1
        ];
        $person->roles[] = $role;

        $this->assertEquals(
            [
                'do.this' => 1,
                'do.that' => 0,
                'do.else' => -1,
                'make.more' => 1
            ],
            $person->getMergedPermissions()
        );
    }

    /** @test */
    public function it_merges_permissions_with_multiple_roles()
    {
        $role1 = new TestRole;
        $role1->permissions = [
            'do.this' => 0,
            'do.that' => 1
        ];
        $role2 = new TestRole;
        $role2->permissions = [
            'do.else' => 1,
            'make.more' => 1
        ];

        $person = new TestPerson;
        $person->permissions = [
            'do.this' => 1,
            'do.that' => 0,
            'do.else' => -1
        ];
        $person->roles = [ $role1, $role2 ];

        $this->assertEquals(
            [
                'do.this' => 1,
                'do.that' => 0,
                'do.else' => -1,
                'make.more' => 1
            ],
            $person->getMergedPermissions()
        );
    }

    /** @test */
    public function it_checks_for_root_privilege()
    {
        $god = new TestPerson;
        $god->is_root = 1;

        $mortal = new TestPerson;
        $mortal->is_root = 0;

        $this->assertFalse($mortal->isRoot());
        $this->assertTrue($god->isRoot());
    }

    /** @test */
    public function it_gives_access_to_root_no_matter_permissions()
    {
        $mortal = new TestPerson;
        $mortal->permissions = [];
        $this->assertFalse($mortal->hasAccess('universe.create'));

        $god = new TestPerson;
        $god->is_root = 1;
        $god->permissions = ['universe.create' => -1];
        $this->assertTrue($god->hasAccess('universe.create'));
    }

    /** @test */
    public function it_provides_a_shorthand_for_any_access()
    {
        $person = new TestPerson;
        $person->permissions = [
            'foo' => 1,
            'bar' => 0
        ];

        $this->assertFalse($person->hasAccess(['foo', 'bar']));
        $this->assertTrue($person->hasAnyAccess(['foo', 'bar']));
    }
}

class TestPerson
{
    use UserPermissions;

    public $permissions = [];

    public $roles = [];

    public $is_root = 0;

    public function getRoles()
    {
        return $this->roles;
    }
}

class TestRole
{
    use RolePermissions;

    public $permissions = [];
}
