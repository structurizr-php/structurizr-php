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

namespace StructurizrPHP\Core\Model;

use StructurizrPHP\Core\Assertion;
use StructurizrPHP\Core\Exception\InvalidArgumentException;
use StructurizrPHP\Core\Exception\RuntimeException;
use StructurizrPHP\Core\Model\Relationship\InteractionStyle;

final class Model
{
    /**
     * @var IdGenerator
     */
    private $idGenerator;

    /**
     * @var Element[]
     */
    private $elementsById;

    /**
     * @var Relationship[]
     */
    private $relationshipsById;

    /**
     * @var null|Enterprise
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

    /**
     * @var DeploymentNode[]
     */
    private $deploymentNodes;

    public function __construct()
    {
        $this->idGenerator = new SequentialIntegerIdGeneratorStrategy();
        $this->elementsById = [];
        $this->relationshipsById = [];
        $this->people = [];
        $this->softwareSystems = [];
        $this->deploymentNodes = [];
    }

    public static function hydrate(?array $modelData) : self
    {
        $model = new self();

        if ($modelData === null) {
            return $model;
        }

        $modelDataObject = new ModelDataObject($modelData);

        $model->people = $modelDataObject->hydratePeople($model);
        $model->softwareSystems = $modelDataObject->hydrateSoftwareSystems($model);
        $model->deploymentNodes = $modelDataObject->hydrateDeploymentNodes($model);

        if (\count($model->people)) {
            foreach ($modelData['people'] as $personData) {
                Person::hydrateRelationships($model->getElement($personData['id']), $personData);
            }
        }

        if (\count($model->softwareSystems)) {
            foreach ($modelData['softwareSystems'] as $softwareSystemData) {
                SoftwareSystem::hydrateRelationships($model->getElement($softwareSystemData['id']), $softwareSystemData);
                SoftwareSystem::hydrateContainersRelationships($model->getSoftwareSystem($softwareSystemData['id']), $softwareSystemData);
            }
        }

        if (\count($model->deploymentNodes)) {
            foreach ($modelData['deploymentNodes'] as $deploymentNodeData) {
                DeploymentNode::hydrateRelationships($model->getDeploymentNode($deploymentNodeData['id']), $deploymentNodeData);
                DeploymentNode::hydrateContainerInstancesRelationships($model->getDeploymentNode($deploymentNodeData['id']), $deploymentNodeData);
                DeploymentNode::hydrateChildrenRelationships($model->getDeploymentNode($deploymentNodeData['id']), $deploymentNodeData);
            }
        }

        \usort(
            $model->people,
            function (Person $personA, Person $personB) {
                return (int) $personA->id() > (int) $personB->id()
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

        \usort(
            $model->deploymentNodes,
            function (DeploymentNode $deploymentNodeA, DeploymentNode $deploymentNodeB) {
                return (int) $deploymentNodeA->id() > (int) $deploymentNodeB->id()
                    ? 1
                    : 0;
            }
        );

        return $model;
    }

    public function idGenerator() : IdGenerator
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
    public function people() : array
    {
        return $this->people;
    }

    /**
     * @return SoftwareSystem[]
     */
    public function softwareSystems() : array
    {
        return $this->softwareSystems;
    }

    /**
     * @return DeploymentNode[]
     */
    public function getDeploymentNodes() : array
    {
        return $this->deploymentNodes;
    }

    public function hasElement(string $id) : bool
    {
        return \array_key_exists($id, $this->elementsById);
    }

    public function hasRelationship(string $id) : bool
    {
        return \array_key_exists($id, $this->relationshipsById);
    }

    public function contains(Element $element) : bool
    {
        return \in_array($element, $this->elementsById, true);
    }

    public function isEmpty() : bool
    {
        return !\count($this->people) && !\count($this->softwareSystems) && !\count($this->deploymentNodes);
    }

    public function getRelationship(string $id) : Relationship
    {
        if (\array_key_exists($id, $this->relationshipsById)) {
            return $this->relationshipsById[$id];
        }

        throw new InvalidArgumentException(\sprintf('Relationship with id %s does not exists', $id));
    }

    public function getElement(string $id) : Element
    {
        if (\array_key_exists($id, $this->elementsById)) {
            return $this->elementsById[$id];
        }

        throw new RuntimeException(\sprintf('Element with id "%s" does not exists.', $id));
    }

    public function getDeploymentNode(string $id) : DeploymentNode
    {
        $element = $this->getElement($id);

        if ($element instanceof DeploymentNode) {
            return $element;
        }

        throw new RuntimeException(\sprintf('Deployment Node with id "%s" does not exists.', $id));
    }

    public function getSoftwareSystem(string $id) : SoftwareSystem
    {
        $element = $this->getElement($id);

        if ($element instanceof SoftwareSystem) {
            return $element;
        }

        throw new RuntimeException(\sprintf('Software System with id "%s" does not exists.', $id));
    }

    public function getContainer(string $id) : Container
    {
        foreach ($this->softwareSystems as $softwareSystem) {
            foreach ($softwareSystem->getContainers() as $container) {
                if ($container->id() === $id) {
                    return $container;
                }
            }
        }

        throw new RuntimeException(\sprintf('Container with id "%s" does not exists.', $id));
    }

    public function addRelationship(Element $source, Element $destination, string $description = '', string $technology = null, InteractionStyle $interactionStyle = null) : Relationship
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

        $this->addRelationshipToInternalStructures($relationship);

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

        $this->addElementToInternalStructures($person);

        return $person;
    }

    public function addSoftwareSystem(string $name, string $description, Location $location = null) : SoftwareSystem
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

        $this->addElementToInternalStructures($softwareSystem);

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

            $this->addElementToInternalStructures($container);

            return $container;
        }

        throw new RuntimeException(\sprintf('A container named "%s" already exists for this software system.', $name));
    }

    /**
     * Adds a top-level deployment node to this model.
     */
    public function addDeploymentNode(string $name, ?string $environment = null, ?string $description = null, ?string $technology = null, ?int $instances = null, ?Properties $properties = null) : DeploymentNode
    {
        Assertion::notEmpty($name);

        if ($this->findDeploymentNodeWithName($name, $environment)) {
            throw new InvalidArgumentException(\sprintf('Deployment node "%s" already exists for "%s" environment', $name, $environment ? $environment : DeploymentNode::DEFAULT_DEPLOYMENT_ENVIRONMENT));
        }

        $deploymentNode = new DeploymentNode($this->idGenerator->generateId(), $this);
        $deploymentNode->setName($name);
        $deploymentNode->setEnvironment($environment ? $environment : DeploymentNode::DEFAULT_DEPLOYMENT_ENVIRONMENT);
        $deploymentNode->setInstances($instances ? $instances : 1);
        $deploymentNode->setTechnology($technology);
        $deploymentNode->setDescription($description);

        if ($properties !== null) {
            $deploymentNode->setProperties($properties);
        }

        $this->deploymentNodes[] = $deploymentNode;

        $this->addElementToInternalStructures($deploymentNode);

        return $deploymentNode;
    }

    public function addContainerInstance(DeploymentElement $deploymentElement, Container $container, bool $replicateContainerRelationships = true) : ContainerInstance
    {
        $instanceNumber = \count(\array_unique(\array_map(
            function (DeploymentNode $deploymentNode) {
                foreach ($deploymentNode->getContainerInstances() as $instance) {
                    return 1;
                }

                return null;
            },
            $this->deploymentNodes,
        )));

        $containerInstance = new ContainerInstance($container, $instanceNumber, $deploymentElement->getEnvironment(), $this->idGenerator->generateId(), $this);

        if ($replicateContainerRelationships) {
            // get all ContainerInstance objects
            /** @return ContainerInstance[] */
            $getContainerInstances = function (DeploymentNode $deploymentNode) use (&$getContainerInstances) : ?array {
                $containerInstances = [];
                $containerInstances = \array_merge($containerInstances, $deploymentNode->getContainerInstances());

                foreach ($deploymentNode->getChildren() as $child) {
                    $containerInstances = \array_merge($containerInstances, $getContainerInstances($child));
                }

                return $containerInstances;
            };

            // find all ContainerInstance objects in the same deployment environment
            /** @var ContainerInstance[] $containerInstances */
            $containerInstances = \array_filter(
                \array_merge(...\array_map(
                    function (DeploymentNode $deploymentNode) use (&$getContainerInstances) {
                        return $getContainerInstances($deploymentNode);
                    },
                    $this->deploymentNodes
                )),
                function (ContainerInstance $containerInstance) use ($deploymentElement) {
                    return $containerInstance->getEnvironment() === $deploymentElement->getEnvironment();
                }
            );

            // and replicate the container-container relationships within the same deployment environment
            foreach ($containerInstances as $ci) {
                $c = $ci->getContainer();

                foreach ($container->getRelationships() as $relationship) {
                    if ($relationship->getDestination()->equals($c)) {
                        $newRelationship = $this->addRelationship($containerInstance, $ci, $relationship->getDescription(), $relationship->getTechnology(), $relationship->getInteractionStyle());

                        $newRelationship->setTags(new Tags());
                        $newRelationship->setLinkedRelationshipId($relationship->id());
                    }
                }

                foreach ($c->getRelationships() as $relationship) {
                    if ($relationship->getDestination()->equals($container)) {
                        $newRelationship = $this->addRelationship($ci, $containerInstance, $relationship->getDescription(), $relationship->getTechnology(), $relationship->getInteractionStyle());
                        $newRelationship->setTags(new Tags());
                        $newRelationship->setLinkedRelationshipId($relationship->id());
                    }
                }
            }
        }

        $this->addElementToInternalStructures($containerInstance);

        return $containerInstance;
    }

    public function addDeploymentNodeWithParent(DeploymentNode $parent, string $name, ?string $environment = null, ?string $description = null, ?string $technology = null, ?int $instances = null, ?Properties $properties = null) : DeploymentNode
    {
        Assertion::notEmpty($name);

        if ($this->findDeploymentNodeWithName($name, $environment)) {
            throw new InvalidArgumentException(\sprintf('Deployment node "%s" already exists for "%s" environment', $name, $environment ? $environment : DeploymentNode::DEFAULT_DEPLOYMENT_ENVIRONMENT));
        }

        $deploymentNode = new DeploymentNode($this->idGenerator->generateId(), $this);
        $deploymentNode->setName($name);
        $deploymentNode->setEnvironment($environment ? $environment : DeploymentNode::DEFAULT_DEPLOYMENT_ENVIRONMENT);
        $deploymentNode->setInstances($instances ? $instances : 1);
        $deploymentNode->setTechnology($technology);
        $deploymentNode->setDescription($description);
        $deploymentNode->setParent($parent);

        if ($properties !== null) {
            $deploymentNode->setProperties($properties);
        }

        return $deploymentNode;
    }

    public function addElementToInternalStructures(Element $element) : void
    {
        if (\array_key_exists($element->id(), $this->elementsById) || \array_key_exists($element->id(), $this->relationshipsById)) {
            return;
        }

        $this->elementsById[$element->id()] = $element;
        $this->idGenerator->found($element->id());
    }

    public function addRelationshipToInternalStructures(Relationship $relationship) : void
    {
        if (\array_key_exists($relationship->id(), $this->elementsById) || \array_key_exists($relationship->id(), $this->relationshipsById)) {
            return;
        }

        $this->relationshipsById[$relationship->id()] = $relationship;
        $this->idGenerator->found($relationship->id());
    }

    public function findDeploymentNodeWithName(string $name, ?string $environment) : ?DeploymentNode
    {
        $environment = $environment ? $environment : DeploymentNode::DEFAULT_DEPLOYMENT_ENVIRONMENT;

        foreach ($this->deploymentNodes as $deploymentNode) {
            if ($deploymentNode->getName() === $name && $deploymentNode->getEnvironment() === $environment) {
                return $deploymentNode;
            }
        }

        return null;
    }

    /**
     * @return Relationship[]
     */
    public function addImplicitRelationships() : array
    {
        $implicitRelationships = [];
        $descriptionKey = 'D';
        $technologyKey = 'T';
        $candidateRelationships = [];
        $objMap = [];

        foreach ($this->relationshipsById as $relationship) {
            $source = $relationship->getSource();
            $destination = $relationship->getDestination();

            while ($source !== null) {
                while ($destination !== null) {
                    if (!$source->hasEfferentRelationshipWith($destination) && $this->propagatedRelationshipIsAllowed($source, $destination)) {
                        if (!\in_array($source, $objMap, true)) {
                            $objMap[] = $source;
                        }
                        $sourceKey = (int) \array_search($source, $objMap, true);

                        if (!\in_array($destination, $objMap, true)) {
                            $objMap[] = $destination;
                        }
                        $destinationKey = (int) \array_search($destination, $objMap, true);

                        if (!\array_key_exists($sourceKey, $candidateRelationships)) {
                            $candidateRelationships[$sourceKey] = [];
                        }

                        if (!\array_key_exists($destinationKey, $candidateRelationships[$sourceKey])) {
                            $candidateRelationships[$sourceKey][$destinationKey] = [
                                $descriptionKey => [],
                                $technologyKey => [],
                            ];
                        }

                        if ($relationship->getDescription() !== null) {
                            $candidateRelationships[$sourceKey][$destinationKey][$descriptionKey][] = $relationship->getDescription();
                        }

                        if ($relationship->getTechnology() !== null) {
                            $candidateRelationships[$sourceKey][$destinationKey][$technologyKey][] = $relationship->getTechnology();
                        }
                    }

                    $destination = $destination->getParent();
                }

                $destination = $relationship->getDestination();

                $source = $source->getParent();
            }
        }

        foreach ($candidateRelationships as $sourceKey => $source) {
            foreach ($candidateRelationships[$sourceKey] as $destinationKey => $destination) {
                $possibleDescriptions = $candidateRelationships[$sourceKey][$destinationKey][$descriptionKey] ?? [];
                $possibleTechnologies = $candidateRelationships[$sourceKey][$destinationKey][$technologyKey] ?? [];

                $description = '';

                if (\count($possibleDescriptions) === 1) {
                    $description = $possibleDescriptions[0];
                }

                $technology = '';

                if (\count($possibleTechnologies) === 1) {
                    $technology = $possibleTechnologies[0];
                }

                // todo ... this defaults to being a synchronous relationship
                $implicitRelationship = $this->addRelationship($objMap[$sourceKey], $objMap[$destinationKey], $description, $technology, InteractionStyle::synchronous());

                if ($implicitRelationship !== null) {
                    $implicitRelationships[] = $implicitRelationship;
                }
            }
        }

        return $implicitRelationships;
    }

    /**
     * @return Relationship[]
     */
    public function getRelationships() : array
    {
        return $this->relationshipsById;
    }

    public function addComponentOfType(Container $parent, string $name, string $type, string $description, ?string $technology = null) : Component
    {
        if ($parent->getComponentWithName($name) === null) {
            $component = new Component($this->idGenerator->generateId(), $this);
            $component->setName($name);
            $component->setDescription($description);

            if (\strlen($type) > 0) {
                $component->setType($type);
            }

            if ($technology) {
                $component->setTechnology($technology);
            }

            $component->setParent($parent);
            $parent->add($component);

            $component->setId($this->idGenerator->generateId());
            $this->addElementToInternalStructures($component);

            return $component;
        }

        throw new InvalidArgumentException("A component named '" . $name . "' already exists for this container.");
    }

    public function toArray() : array
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

        if (\count($this->deploymentNodes)) {
            $data['deploymentNodes'] = \array_map(function (DeploymentNode $deploymentNode) {
                return $deploymentNode->toArray();
            }, $this->deploymentNodes);
        }

        return $data;
    }

    private function propagatedRelationshipIsAllowed(Element $source, Element $destination) : bool
    {
        if ($source->equalTo($destination)) {
            return false;
        }

        return !($this->isChildOf($source, $destination) || $this->isChildOf($destination, $source));
    }

    private function isChildOf(Element $e1, Element $e2) : bool
    {
        if ($e1 instanceof Person || $e2 instanceof Person) {
            return false;
        }

        /** @var Element $parent */
        $parent = $e2->getParent();

        while ($parent !== null) {
            if ($parent->id() === $e1->id()) {
                return true;
            }

            $parent = $parent->getParent();
        }

        return false;
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
    public function hydrateSoftwareSystems(Model $model) : array
    {
        return \array_map(
            function (array $softwareSystemData) use ($model) {
                return SoftwareSystem::hydrate($softwareSystemData, $model);
            },
            $this->modelData['softwareSystems'] ?? []
        );
    }

    /**
     * @return Person[]
     */
    public function hydratePeople(Model $model) : array
    {
        return \array_map(
            function (array $personData) use ($model) {
                return Person::hydrate($personData, $model);
            },
            $this->modelData['people'] ?? []
        );
    }

    /**
     * @return DeploymentNode[]
     */
    public function hydrateDeploymentNodes(Model $model) : array
    {
        return \array_map(
            function (array $deploymentNodeData) use ($model) {
                return DeploymentNode::hydrate($deploymentNodeData, $model);
            },
            $this->modelData['deploymentNodes'] ?? []
        );
    }
}
