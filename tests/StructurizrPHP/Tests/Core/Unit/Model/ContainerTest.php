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

namespace StructurizrPHP\Tests\Core\Unit\Model;

use PHPUnit\Framework\TestCase;
use StructurizrPHP\Core\Model\Container;
use StructurizrPHP\Core\Model\Model;

final class ContainerTest extends TestCase
{
    public function test_hydrating_container() : void
    {
        $model = new Model();
        $software = $model->addSoftwareSystem('software', 'description');
        $container = new Container('1', $software, $model);
        $container->setParent($software);

        $this->assertEquals($container, Container::hydrate($container->toArray(), $software, $model));
    }

    public function test_get_component_by_name() : void
    {
        $model = new Model();
        $software = $model->addSoftwareSystem('software', 'description');
        $container = new Container('1', $software, $model);

        $component = $container->addComponent('test', 'test', 'test');

        $this->assertNull($container->getComponentWithName('nothing'));
        $this->assertEquals($component, $container->getComponentWithName('test'));
    }
}
