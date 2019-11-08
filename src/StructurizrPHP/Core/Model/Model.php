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

use StructurizrPHP\StructurizrPHP\Assertion;
use StructurizrPHP\StructurizrPHP\Core\Model\Relationship\InteractionStyle;
use StructurizrPHP\StructurizrPHP\Exception\InvalidArgumentException;
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

    public function hasElement(string $id) : bool
    {
        try {
            $this->getElement($id);

            return true;
        } catch (RuntimeException $exception) {
            return false;
        }
    }

    public function getRelationship(string $id) : Relationship
    {
        Assertion::notEmpty($id);

        foreach ($this->softwareSystems as $softwareSystem) {
            foreach ($softwareSystem->relationships() as $relationship) {
                if ($relationship->id() === $id) {
                    return $relationship;
                }

                foreach ($softwareSystem->containers() as $container) {
                    foreach ($container->relationships() as $relationship) {
                        if ($relationship->id() === $id) {
                            return $relationship;
                        }
                    }
                }
            }
        }

        foreach ($this->people as $people) {
            foreach ($people->relationships() as $relationship) {
                if ($relationship->id() === $id) {
                    return $relationship;
                }
            }
        }

        throw new InvalidArgumentException(\sprintf("Relationship with id %s does not exists", $id));
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

            foreach ($softwareSystem->containers() as $container) {
                if ($container->id() === $id) {
                    return $container;
                }
            }
        }

        throw new RuntimeException(\sprintf("Element with id \"%s\" does not exists.", $id));
    }

    public function addRelationship(Element $source, Element $destination, string $description = "", string $technology = null, InteractionStyle $interactionStyle = null) : Relationship
    {
        $relationship = new Relationship(
            $this->idGenerator->generateId(),
            $source,
            $destination,
            $description
        );

        $relationship->setTechnology($technology);

        if ($interactionStyle) {
            $relationship->setInteractionStyle($interactionStyle);
        }

        $source->addRelationship($relationship);

        return $relationship;
    }

    public function addPerson(string $name = null, string $description = null, Location $location = null) : Person
    {
        $person = new Person(
            $this->idGenerator->generateId(),
            $this
        );

        $person->setName($name);
        $person->setDescription($description);

        if ($location) {
            $person->setLocation($location);
        }

        $this->people[] = $person;

        return $person;
    }

    public function addSoftwareSystem(string $name = null, string $description = null, Location $location = null) : SoftwareSystem
    {
        $softwareSystem = new SoftwareSystem(
            $this->idGenerator->generateId(),
            $this
        );

        $softwareSystem->setName($name);
        $softwareSystem->setDescription($description);

        if ($location) {
            $softwareSystem->setLocation($location);
        }

        $this->softwareSystems[] = $softwareSystem;

        return $softwareSystem;
    }

    public function addContainer(SoftwareSystem $parent, string $name, string $description, string $technology) : Container
    {
        if (!$parent->findContainerWithName($name)) {
            $container = new Container($this->idGenerator->generateId(), $parent, $this);

            $container->setName($name);
            $container->setDescription($description);
            $container->setTechnology($technology);

            $parent->add($container);

            return $container;
        }

        throw new RuntimeException(\sprintf("A container named \"name\" already exists for this software system.", $name));
    }

    public function toArray() : ?array
    {
        $data = [
            'enterprise' => ($this->enterprise) ? $this->enterprise->name() : null,
            'people' => [],
            'softwareSystems' => [],
            'deploymentNodes' => [],
        ];

        if (!\count($this->people) && !\count($this->softwareSystems)) {
            return $data;
        }

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

        $model->people = $modelDataObject->hydratePeopleByRelationships($withRelationships = false, $model);
        $model->softwareSystems = $modelDataObject->hydrateSoftwareSystemByRelationships($withRelationships = false, $model);

        // People with relationships
        $model->people = \array_merge(
            $modelDataObject->hydratePeopleByRelationships($withRelationships = true, $model),
            $model->people
        );

        // Software Systems with relationships
        $model->softwareSystems = \array_merge(
            $modelDataObject->hydrateSoftwareSystemByRelationships($withRelationships = true, $model),
            $model->softwareSystems
        );

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

    /**
     * @psalm-suppress MixedArgument
     */
    private function filterPeopleByRelationship(bool $withRelationships) : array
    {
        if (!isset($this->modelData['people']) || !\is_array($this->modelData['people'])) {
            return [];
        }

        return \array_filter(
            $this->modelData['people'],
            function (array $personData) use ($withRelationships) {
                if (!isset($personData['relationships']) || !\count($personData['relationships'])) {
                    return $withRelationships ? false : true;
                }

                return ($withRelationships && isset($personData['relationships']))
                    ? \is_array($personData['relationships'])
                    : !\is_array($personData['relationships']);
            }
        );
    }

    /**
     * @psalm-suppress MixedArgument
     */
    private function filterSoftwareSystemByRelationship(bool $withRelationships) : array
    {
        if (!isset($this->modelData['softwareSystems']) || !\is_array($this->modelData['softwareSystems'])) {
            return [];
        }

        return \array_filter(
            $this->modelData['softwareSystems'],
            function (array $softwareSystemData) use ($withRelationships) {
                if (!isset($softwareSystemData['relationships']) || !\count($softwareSystemData['relationships'])) {
                    return $withRelationships ? false : true;
                }

                return ($withRelationships)
                    ? \is_array($softwareSystemData['relationships'])
                    : !\is_array($softwareSystemData['relationships']);
            }
        );
    }
}
