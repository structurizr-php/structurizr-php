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
use StructurizrPHP\StructurizrPHP\Core\Model\Location;
use StructurizrPHP\StructurizrPHP\Core\Model\Model;
use StructurizrPHP\StructurizrPHP\Core\Model\Properties;
use StructurizrPHP\StructurizrPHP\Core\Model\Property;
use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;

final class SoftwareSystemTest extends TestCase
{
    public function test_hydrating_software_system()
    {
        $softwareSystem = new SoftwareSystem('1', $model = new Model());

        $this->assertEquals($softwareSystem, SoftwareSystem::hydrate($softwareSystem->toArray(), $model));
    }

    public function test_hydrating_software_system_with_properties()
    {
        $softwareSystem = new SoftwareSystem('1', $model = new Model());
        $softwareSystem->setProperties(new Properties(new Property('key', 'value')));

        $this->assertEquals($softwareSystem, SoftwareSystem::hydrate($softwareSystem->toArray(), $model));
    }

    public function test_hydrating_software_system_with_relationship()
    {
        $model = new Model();
        $person = $model->addPerson('name', 'description', Location::unspecified());
        $softwareSystem = $model->addSoftwareSystem('name', 'description', Location::unspecified());

        $person->usesSoftwareSystem($softwareSystem, 'description', 'technology');

        $this->assertEquals($softwareSystem, SoftwareSystem::hydrate($softwareSystem->toArray(), $model));
    }
}
