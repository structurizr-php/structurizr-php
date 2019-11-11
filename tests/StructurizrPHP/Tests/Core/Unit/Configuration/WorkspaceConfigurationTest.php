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

namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Core\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Configuration\Role;
use StructurizrPHP\StructurizrPHP\Core\Configuration\WorkspaceConfiguration;
use StructurizrPHP\StructurizrPHP\Core\Exception\AssertionException;

final class WorkspaceConfigurationTest extends TestCase
{
    public function test_addUser() : void
    {
        $workspaceConfiguration = new WorkspaceConfiguration();
        $workspaceConfiguration->addUser('test', Role::readOnly());
        $this->assertCount(1, $workspaceConfiguration->users());
    }

    public function test_addUser_empty_username() : void
    {
        $workspaceConfiguration = new WorkspaceConfiguration();
        $this->expectException(AssertionException::class);
        $workspaceConfiguration->addUser('', Role::readOnly());
    }
}
