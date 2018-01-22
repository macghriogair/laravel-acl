<?php

use Macgriog\Acl\Traits\PermissionStrategy;

/**
 * @date    2017-01-04
 * @file    PermissionStrategyTest.php
 * @author  Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

class PermissionStrategyTest extends \PHPUnit_Framework_TestCase
{

    private $testable;

    public function setUp()
    {
        $this->testable = new Testable;
    }

    /** @test */
    public function it_sets_and_checks_allowed_permission_values()
    {
        $this->testable->setPermissionsAttribute(['a' => 1]);
        $this->assertEquals('{"a":1}', $this->testable->attributes['permissions']);

        $this->testable->setPermissionsAttribute(['a' => 0, 'b' => 1]);
        $this->assertEquals('{"b":1}', $this->testable->attributes['permissions']);

        $this->testable->setPermissionsAttribute(['a' => 1, 'b' => 1]);
        $this->assertEquals('{"a":1,"b":1}', $this->testable->attributes['permissions']);

        $this->setExpectedException(InvalidArgumentException::class);
        $this->testable->setPermissionsAttribute(['a' => 0, 'b' => 3]);
    }

    /** @test */
    public function it_returns_json_decoded_permissions_values()
    {
        $this->assertEmpty($this->testable->getPermissionsAttribute('a'));
        $this->testable->setPermissionsAttribute(['a' => 1, 'b' => 0]);
        $this->assertEquals(['a' => 1], $this->testable->getPermissionsAttribute('a'));
    }

    /** @test */
    public function it_denies_access_for_unmet_permission()
    {
        $this->assertFalse($this->testable->hasPermission('rule.them.all'));
    }

    /** @test */
    public function it_grants_for_a_permission()
    {
        $this->testable->permissions = [
            'do.this' => 1, 'do.that' => 0
        ];

        $this->assertFalse($this->testable->hasPermission('do.that'));
        $this->assertTrue($this->testable->hasPermission('do.this'));
    }

    /** @test */
    public function it_grants_for_all_permissions()
    {
        $this->testable->permissions = [
            'do.this' => 1, 'do.that' => 0, 'do.else' => 1
        ];

        $this->assertFalse($this->testable->hasPermission(['do.this', 'do.that']));
        $this->assertTrue($this->testable->hasPermission(['do.this', 'do.else'], false));
    }
}

class Testable
{
    use PermissionStrategy;
}
