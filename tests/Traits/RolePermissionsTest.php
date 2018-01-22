<?php

use Macgriog\Acl\Traits\RolePermissions;

/**
 * @date    2017-01-04
 * @file    RolePermissionsTest.php
 * @author  Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

class RolePermissionsTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function it_provides_a_shorthand_for_any_access()
    {
        $role = new TestRole2;
        $role->permissions = [
            'foo' => 1,
            'bar' => 0
        ];

        $this->assertFalse($role->hasPermission(['foo', 'bar']));
        $this->assertTrue($role->hasAnyAccess(['foo', 'bar']));
    }
}

class TestRole2
{
    use RolePermissions;

    public $permissions = [];
}
