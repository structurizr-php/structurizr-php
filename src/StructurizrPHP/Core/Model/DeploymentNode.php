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

use StructurizrPHP\Core\Model\Relationship\InteractionStyle;

/**
 * <p>
 *   Represents a deployment node, which is something like:
 * </p>.
 *
 * <ul>
 *     <li>Physical infrastructure (e.g. a physical server or device)</li>
 *     <li>Virtualised infrastructure (e.g. IaaS, PaaS, a virtual machine)</li>
 *     <li>Containerised infrastructure (e.g. a Docker container)</li>
 *     <li>Database server</li>
 *     <li>Java EE web/application server</li>
 *     <li>Microsoft IIS</li>
 *     <li>etc</li>
 * </ul>
 */
final class DeploymentNode extends DeploymentElement
{
    /**
     * @var null|DeploymentNode
     */
    private $parent;

    /**
     * @var null|string
     */
    private $technology;

    /**
     * @var DeploymentNode[]
     */
    private $children;

    /**
     * @var int
     */
    private $instances;

    /**
     * @var ContainerInstance[]
     */
    private $containerInstances;

    public function __construct(string $id, Model $model)
    {
        parent::__construct($id, $model);
        $this->instances = 1;
        $this->children = [];
        $this->containerInstances = [];
    }

    public static function hydrate(array $deploymentNodeData, Model $model) : self
    {
        $deploymentNode = new self($deploymentNodeData['id'], $model);
        $deploymentNode->instances = $deploymentNodeData['instances'];
        $deploymentNode->setName($deploymentNodeData['name']);

        if (isset($deploymentNodeData['parent'])) {
            $element = $model->getElement($deploymentNodeData['parent']);
            $deploymentNode->parent = ($element instanceof self) ? $element : null;
        }

        if (isset($deploymentNodeData['technology'])) {
            $deploymentNode->technology = $deploymentNodeData['technology'];
        }

        if (isset($deploymentNodeData['children'])) {
            if (\is_array($deploymentNodeData['children'])) {
                foreach ($deploymentNodeData['children'] as $childData) {
                    $deploymentNode->children[] = self::hydrate($childData, $model);
                }
            }
        }

        if (isset($deploymentNodeData['containerInstances'])) {
            if (\is_array($deploymentNodeData['containerInstances'])) {
                foreach ($deploymentNodeData['containerInstances'] as $containerInstanceData) {
                    $deploymentNode->containerInstances[] = ContainerInstance::hydrate($containerInstanceData, $model);
                }
            }
        }

        parent::hydrateDeploymentElement($deploymentNode, $deploymentNodeData);

        return $deploymentNode;
    }

    public static function hydrateContainerInstancesRelationships(self $deploymentNode, array $deploymentNodeData) : void
    {
        if (isset($deploymentNodeData['containerInstances']) && \is_array($deploymentNodeData['containerInstances'])) {
            foreach ($deploymentNode->containerInstances as $containerInstance) {
                foreach ($deploymentNodeData['containerInstances'] as $containerInstanceData) {
                    if ($containerInstanceData['id'] === $containerInstance->id()) {
                        parent::hydrateRelationships($containerInstance, $containerInstanceData);
                    }
                }
            }
        }
    }

    public static function hydrateChildrenRelationships(self $deploymentNode, array $deploymentNodeData) : void
    {
        foreach ($deploymentNode->children as $child) {
            foreach ($deploymentNodeData['children'] as $childData) {
                if ($childData['id'] === $child->id()) {
                    parent::hydrateRelationships($child, $childData);
                    self::hydrateContainerInstancesRelationships($child, $childData);

                    self::hydrateChildrenRelationships($child, $childData);
                }
            }
        }
    }

    public function setInstances(int $instances) : void
    {
        $this->instances = $instances;
    }

    public function setTechnology(?string $technology) : void
    {
        $this->technology = $technology;
    }

    public function setParent(self $parent) : void
    {
        $this->parent = $parent;
    }

    public function getParent() : ?Element
    {
        return $this->parent;
    }

    /**
     * @return ContainerInstance[]
     */
    public function getContainerInstances() : array
    {
        return $this->containerInstances;
    }

    /**
     * @return self[]
     */
    public function getChildren() : array
    {
        return $this->children;
    }

    public function addDeploymentNode(string $name, ?string $environment = null, ?string $description = null, ?string $technology = null, ?int $instances = null, ?Properties $properties = null) : self
    {
        $deploymentNode = $this->getModel()->addDeploymentNodeWithParent($this, $name, $environment, $description, $technology, $instances, $properties);
        $this->children[] = $deploymentNode;

        return $deploymentNode;
    }

    public function add(Container $container, bool $replicateContainerRelationships = true) : ContainerInstance
    {
        $containerInstance = $this->getModel()->addContainerInstance($this, $container, $replicateContainerRelationships);

        $this->containerInstances[] = $containerInstance;

        return $containerInstance;
    }

    public function usesDeploymentNode(
        self $deploymentNode,
        string $description = 'Uses',
        string $technology = null,
        InteractionStyle $interactionStyle = null
    ) : Relationship {
        return $this->getModel()->addRelationship(
            $this,
            $deploymentNode,
            $description,
            $technology,
            $interactionStyle
        );
    }

    public function toArray() : array
    {
        $data = \array_merge(
            [
                'id' => $this->id(),
                'name' => $this->getName(),
                'environment' => $this->getEnvironment(),
                'instances' => $this->instances,
            ],
            parent::toArray()
        );

        if (\count($this->children)) {
            $data['children'] = \array_map(
                function (self $child) {
                    return $child->toArray();
                },
                $this->children
            );
        }

        if (\count($this->containerInstances)) {
            $data['containerInstances'] = \array_map(
                function (ContainerInstance $containerInstance) {
                    return $containerInstance->toArray();
                },
                $this->containerInstances
            );
        }

        if ($this->technology !== null) {
            $data['technology'] = $this->technology;
        }

        if ($this->parent) {
            $data['parent'] = $this->parent->id();
        }

        return $data;
    }

    public function tags() : Tags
    {
        // deployment nodes don't have any tags
        return new Tags();
    }

    public function getCanonicalName() : string
    {
        if ($this->parent !== null) {
            return $this->parent->getCanonicalName() . self::CANONICAL_NAME_SEPARATOR . parent::formatForCanonicalName($this->getName());
        }

        return self::CANONICAL_NAME_SEPARATOR . 'Deployment' . self::CANONICAL_NAME_SEPARATOR . parent::formatForCanonicalName($this->getEnvironment()) . self::CANONICAL_NAME_SEPARATOR . parent::formatForCanonicalName($this->getName());
    }
}
