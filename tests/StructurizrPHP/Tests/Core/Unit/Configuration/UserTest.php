<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\Tests\Core\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\Core\Configuration\Role;
use StructurizrPHP\Core\Configuration\User;

final class UserTest extends TestCase
{
    public function test_equals_the_same_users() : void
    {
        $user = new User('test', Role::readOnly());
        $anotherUser = new User('test', Role::readOnly());

        $this->assertTrue($user->equals($anotherUser));
    }

    public function test_equals_different_role() : void
    {
        $user = new User('test', Role::readOnly());
        $anotherUser = new User('test', Role::readWrite());

        $this->assertFalse($user->equals($anotherUser));
    }

    public function test_equals_different_username() : void
    {
        $user = new User('test', Role::readOnly());
        $anotherUser = new User('testTest', Role::readOnly());

        $this->assertFalse($user->equals($anotherUser));
    }

    public function test_toString() : void
    {
        $user = new User('test', Role::readOnly());

        $expectedString = 'User {username=\'test\', role=' . (Role::readOnly())->toString() . '}';

        $this->assertEquals($expectedString, $user->toString());
    }
}
