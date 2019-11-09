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
use StructurizrPHP\StructurizrPHP\Core\Model\Person;
use StructurizrPHP\StructurizrPHP\Core\Model\Properties;
use StructurizrPHP\StructurizrPHP\Core\Model\Property;

final class PersonTest extends TestCase
{
    public function test_hydrating_person()
    {
        $person = new Person('1', $model = new Model());

        $this->assertEquals($person, Person::hydrate($person->toArray(), $model));
    }

    public function test_hydrating_person_with_properties()
    {
        $person = new Person('1', $model = new Model());
        $person->setProperties(new Properties(new Property('key', 'value')));

        $this->assertEquals($person, Person::hydrate($person->toArray(), $model));
    }

    public function test_hydrating_person_with_relationship()
    {
        $model = new Model();
        $person = $model->addPerson('name', 'description', Location::unspecified());
        $softwareSystem = $model->addSoftwareSystem('name', 'description', Location::unspecified());

        $person->usesSoftwareSystem($softwareSystem, 'description', 'technology');

        $newPerson = Person::hydrate($person->toArray(), $model);
        Person::hydrateRelationships($newPerson, $person->toArray());

        $this->assertEquals($person, $newPerson);
    }
}
