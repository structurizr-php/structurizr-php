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

namespace StructurizrPHP\Tests\StructurizrPHP\Tests\Unit\Core\Model;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\StructurizrPHP\Core\Model\Container;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;

final class ContainerTest extends TestCase
{
    public function test_hydrating_container()
    {
        $model = new Model();
        $software = $model->addSoftwareSystem('software');
        $container = new Container("1", $software, $model);

        $container->setParent($software);

        $this->assertEquals($container, Container::hydrate($container->toArray(), $software, $model));
    }
}
