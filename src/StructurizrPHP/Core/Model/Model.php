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

namespace StructurizrPHP\StructurizrPHP\Core\Model;

use StructurizrPHP\StructurizrPHP\Core\Model\Relationship\InteractionStyle;

final class Model
{
    /**
     * @var IdGenerator
     */
    private $idGenerator;

    /**
     * @var Enterprise|null
     */
    private $enterprise;

    /**
     * @var Person[]
     */
    private $people;

    /**
     * @var SoftwareSystem[]
     */
    private $softwareSystems;

    public function __construct()
    {
        $this->idGenerator = new SequentialIntegerIdGeneratorStrategy();
        $this->people = [];
        $this->softwareSystems = [];
    }

    /**
     * @param Enterprise $enterprise
     */
    public function setEnterprise(Enterprise $enterprise) : void
    {
        $this->enterprise = $enterprise;
    }

    /**
     * @return Person[]
     */
    public function people(): array
    {
        return $this->people;
    }

    /**
     * @return SoftwareSystem[]
     */
    public function softwareSystems(): array
    {
        return $this->softwareSystems;
    }

    public function addRelationship(Element $source, Element $destination, string $description, string $technology, InteractionStyle $interactionStyle) : Relationship
    {
        $relationship = new Relationship(
            $this->idGenerator->generateId(),
            $source,
            $destination,
            $description,
            $technology,
            $interactionStyle
        );

        $source->addRelationship($relationship);

        return $relationship;
    }

    public function addPerson(string $name, string $description, Location $location = null) : Person
    {
        $person = new Person(
            $this->idGenerator->generateId(),
            $name,
            $description,
            $location ? $location : Location::unspecified(),
            $this
        );

        $this->people[] = $person;

        return $person;
    }

    public function addSoftwareSystem(string $name, string $description, Location $location = null) : SoftwareSystem
    {
        $softwareSystem = new SoftwareSystem(
            $this->idGenerator->generateId(),
            $name,
            $description,
            $location ? $location : Location::unspecified(),
            $this
        );

        $this->softwareSystems[] = $softwareSystem;

        return $softwareSystem;
    }

    public function toArray() : ?array
    {
        if (!\count($this->people) && !\count($this->softwareSystems)) {
            return null;
        }

        $data = [
            'enterprise' => ($this->enterprise) ? $this->enterprise->name() : null,
            'people' => [],
            'softwareSystems' => [],
            'deploymentNodes' => [],
        ];

        if (\count($this->people)) {
            $data['people'] = \array_map(function (Person $person) {
                return $person->toArray();
            }, $this->people);
        }

        if (\count($this->softwareSystems)) {
            $data['softwareSystems'] = \array_map(function (SoftwareSystem $softwareSystem) {
                return $softwareSystem->toArray();
            }, $this->softwareSystems);
        }

        return $data;
    }
}
