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
 *   Represents an infrastructure node, which is something like:
 * </p>.
 *
 * <ul>
 *     <li>Physical infrastructure (e.g. a physical server or device)</li>
 *     <li>Virtualised infrastructure (e.g. IaaS, PaaS, a virtual machine)</li>
 *     <li>Containerised infrastructure (e.g. a Docker container)</li>
 *     <li>etc</li>
 * </ul>
 */
final class InfrastructureNode extends DeploymentElement
{
    /**
     * @var DeploymentNode
     */
    private $parent;

    /**
     * @var null|string
     */
    private $technology;

    public static function hydrate(array $infrastructureNodeData, Model $model) : self
    {
        $infrastructureNode = new self($infrastructureNodeData['id'], $model);
        $infrastructureNode->setName($infrastructureNodeData['name']);

        if (isset($infrastructureNodeData['parent'])) {
            $element = $model->getElement($infrastructureNodeData['parent']);
            $infrastructureNode->parent = ($element instanceof DeploymentNode) ? $element : null;
        }

        if (isset($infrastructureNodeData['technology'])) {
            $infrastructureNode->technology = $infrastructureNodeData['technology'];
        }

        parent::hydrateDeploymentElement($infrastructureNode, $infrastructureNodeData);

        return $infrastructureNode;
    }

    public function setTechnology(?string $technology) : void
    {
        $this->technology = $technology;
    }

    public function setParent(DeploymentNode $parent) : void
    {
        $this->parent = $parent;
    }

    public function getParent() : ?Element
    {
        return $this->parent;
    }

    public function usesDeploymentElement(DeploymentElement $deploymentElement, string $description = 'Uses', string $technology = null, InteractionStyle $interactionStyle = null) : Relationship
    {
        return $this->getModel()->addRelationship(
            $this,
            $deploymentElement,
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

    public function getCanonicalName() : string
    {
        if ($this->parent !== null) {
            return $this->parent->getCanonicalName() . self::CANONICAL_NAME_SEPARATOR . parent::formatForCanonicalName($this->getName());
        }

        return self::CANONICAL_NAME_SEPARATOR . 'Infrastructure' . self::CANONICAL_NAME_SEPARATOR . parent::formatForCanonicalName($this->getEnvironment()) . self::CANONICAL_NAME_SEPARATOR . parent::formatForCanonicalName($this->getName());
    }
}