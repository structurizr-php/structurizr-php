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

namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Workspace;

final class WorkspaceTest extends TestCase
{
    public function test_hydraing_workspace()
    {
        $workspace = new Workspace(
            "1",
            "name",
            "description"
        );

        $this->assertEquals($workspace, Workspace::hydrate($workspace->toArray("test")));
    }
}
