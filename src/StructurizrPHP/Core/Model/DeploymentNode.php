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

/**
 * <p>
 *   Represents a deployment node, which is something like:
 * </p>
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
     * @var DeploymentNode|null
     */
    private $parent;

    /**
     * @var string|null
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

    public function setInstances(int $instances) : void
    {
        $this->instances = $instances;
    }

    public function setTechnology(?string $technology) : void
    {
        $this->technology = $technology;
    }

    public function setParent(DeploymentNode $parent) : void
    {
        $this->parent = $parent;
    }

    public function getParent() : ?DeploymentNode
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
        DeploymentNode $deploymentNode,
        string $description = "Uses",
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
                'children' => \array_map(function (DeploymentNode $child) {
                    return $child->toArray();
                }, $this->children),
                'containerInstances' => \array_map(function (ContainerInstance $containerInstance) {
                    return $containerInstance->toArray();
                }, $this->containerInstances),
                'instances' => $this->instances,
            ],
            parent::toArray()
        );

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
        } else {
            return self::CANONICAL_NAME_SEPARATOR . "Deployment" . self::CANONICAL_NAME_SEPARATOR . parent::formatForCanonicalName($this->getEnvironment()) . self::CANONICAL_NAME_SEPARATOR . parent::formatForCanonicalName($this->getName());
        }
    }

    public static function hydrate(array $deploymentNodeData, Model $model) : self
    {
        $deploymentNode = new self($deploymentNodeData['id'], $model);
        $deploymentNode->instances = $deploymentNodeData['instances'];
        $deploymentNode->setName($deploymentNodeData['name']);

        if (isset($deploymentNodeData['parent'])) {
            $deploymentNode->parent = $model->getElement($deploymentNodeData['parent']);
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

    public static function hydrateChildrenRelationships(DeploymentNode $deploymentNode, array $deploymentNodeData) : void
    {
        foreach ($deploymentNode->children as $child) {
            foreach ($deploymentNodeData['children'] as $childData) {
                if ($childData['id'] === $child->id()) {
                    parent::hydrateRelationships($child, $childData);

                    self::hydrateChildrenRelationships($child, $childData);
                }
            }
        }
    }
}
