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
use StructurizrPHP\Core\Exception\RuntimeException;
use StructurizrPHP\Core\Model\Location;
use StructurizrPHP\Core\Model\Model;
use StructurizrPHP\Core\Model\Properties;
use StructurizrPHP\Core\Model\Property;
use StructurizrPHP\Core\Model\SoftwareSystem;

final class SoftwareSystemTest extends TestCase
{
    public function test_hydrating_software_system() : void
    {
        $softwareSystem = new SoftwareSystem('1', $model = new Model());

        $this->assertEquals($softwareSystem, SoftwareSystem::hydrate($softwareSystem->toArray(), $model));
    }

    public function test_hydrating_software_system_with_properties() : void
    {
        $softwareSystem = new SoftwareSystem('1', $model = new Model());
        $softwareSystem->setProperties(new Properties(new Property('key', 'value')));

        $this->assertEquals($softwareSystem, SoftwareSystem::hydrate($softwareSystem->toArray(), $model));
    }

    public function test_hydrating_software_system_with_relationship() : void
    {
        $model = new Model();
        $person = $model->addPerson('name', 'description', Location::unspecified());
        $softwareSystem = $model->addSoftwareSystem('name', 'description', Location::unspecified());

        $person->usesSoftwareSystem($softwareSystem, 'description', 'technology');

        $this->assertEquals($softwareSystem, SoftwareSystem::hydrate($softwareSystem->toArray(), $model));
    }

    public function test_hydrating_software_system_with_container() : void
    {
        $model = new Model();
        $softwareSystem = $model->addSoftwareSystem('name', 'description', Location::unspecified());

        $model->addContainer($softwareSystem, 'container', 'test', 'http');

        $this->assertEquals($softwareSystem, SoftwareSystem::hydrate($softwareSystem->toArray(), $model));
    }

    public function test_adding_container_twice() : void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('A container named "container" already exists for this software system.');

        $model = new Model();
        $softwareSystem = $model->addSoftwareSystem('name', 'description', Location::unspecified());

        $model->addContainer($softwareSystem, 'container', 'test', 'http');
        $model->addContainer($softwareSystem, 'container', 'test', 'http');
    }
}
