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
use StructurizrPHP\StructurizrPHP\Exception\RuntimeException;

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

    public function idGenerator(): IdGenerator
    {
        return $this->idGenerator;
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

    public function getElement(string $id) : Element
    {
        foreach ($this->people as $person) {
            if ($person->id() === $id) {
                return $person;
            }
        }

        foreach ($this->softwareSystems as $softwareSystem) {
            if ($softwareSystem->id() === $id) {
                return $softwareSystem;
            }
        }

        throw new RuntimeException(\sprintf("Element with id \"%s\" does not exists.", $id));
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

    public static function hydrate(?array $modelData) : Model
    {
        $model = new Model();

        if ($modelData === null) {
            return $model;
        }

        $modelDataObject = new ModelDataObject($modelData);

        $model->people = $modelDataObject->hydratePeopleByRelationships(false, $model);

        $model->softwareSystems = $modelDataObject->hydrateSoftwareSystemByRelationships(false, $model);

        // People with relationships
        $model->people = \array_merge(
            $modelDataObject->hydratePeopleByRelationships(true, $model),
            $model->people
        );

        // Software Systems with relationships
        $model->softwareSystems = \array_merge(
            $modelDataObject->hydrateSoftwareSystemByRelationships(true, $model),
            $model->softwareSystems
        );

        // sort things by ID
        \usort(
            $model->people,
            function (Person $personA, Person $personB) {
                return (int) $personA->id() > (int)$personB->id()
                    ? 1
                    : 0;
            }
        );

        \usort(
            $model->softwareSystems,
            function (SoftwareSystem $softwareSystemA, SoftwareSystem $softwareSystemB) {
                return (int) $softwareSystemA->id() > (int) $softwareSystemB->id()
                    ? 1
                    : 0;
            }
        );

        return $model;
    }
}

final class ModelDataObject
{
    /**
     * @var array
     */
    private $modelData;

    /**
     * @var array
     */
    public function __construct(array $modelData)
    {
        $this->modelData = $modelData;
    }

    /**
     * @return SoftwareSystem[]
     */
    public function hydrateSoftwareSystemByRelationships(bool $withRelationships, Model $model) : array
    {
        return \array_map(
            function (array $softwareSystemData) use ($model) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                return SoftwareSystem::hydrate($softwareSystemData, $model);
            },
            $this->filterSoftwareSystemByRelationship($withRelationships)
        );
    }

    /**
     * @return Person[]
     */
    public function hydratePeopleByRelationships(bool $withRelationships, Model $model) : array
    {
        return \array_map(
            function (array $personData) use ($model) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                return Person::hydrate($personData, $model);
            },
            $this->filterPeopleByRelationship($withRelationships)
        );
    }

    private function filterPeopleByRelationship(bool $withRelationships) : array
    {
        if (!isset($this->modelData['people']) || !\is_array($this->modelData['people'])) {
            return [];
        }

        return \array_filter(
            $this->modelData['people'],
            function (array $personData) use ($withRelationships) {
                return ($withRelationships) ? \is_array($personData['relationships']) : !\is_array($personData['relationships']) ;
            }
        );
    }

    private function filterSoftwareSystemByRelationship(bool $withRelationships) : array
    {
        if (!isset($this->modelData['softwareSystems']) || !\is_array($this->modelData['softwareSystems'])) {
            return [];
        }

        return \array_filter(
            $this->modelData['softwareSystems'],
            function (array $softwareSystemDAta) use ($withRelationships) {
                return ($withRelationships) ? \is_array($softwareSystemDAta['relationships']) : !\is_array($softwareSystemDAta['relationships']) ;
            }
        );
    }
}
