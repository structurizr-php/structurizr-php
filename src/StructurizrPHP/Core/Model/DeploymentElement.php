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

abstract class DeploymentElement extends Element
{
    public const DEFAULT_DEPLOYMENT_ENVIRONMENT = 'Default';

    /**
     * @var null|DeploymentNode
     */
    protected $parent;

    /**
     * @var string
     */
    private $environment = self::DEFAULT_DEPLOYMENT_ENVIRONMENT;

    public static function hydrateDeploymentElement(self $element, array $elementData, Model $model) : void
    {
        $element->setEnvironment($elementData['environment']);

        if (isset($elementData['parent'])) {
            $parent = $model->getElement($elementData['parent']);
            $element->parent = ($parent instanceof DeploymentNode) ? $parent : null;
        }

        parent::hydrateElement($element, $elementData);
    }

    public function getEnvironment() : string
    {
        return $this->environment;
    }

    public function setEnvironment(string $environment) : void
    {
        $this->environment = $environment;
    }

    public function setParent(?DeploymentNode $parent) : void
    {
        $this->parent = $parent;
    }

    public function getParent() : ?DeploymentNode
    {
        return $this->parent;
    }

    public function usesDeploymentElement(self $deploymentElement, string $description = 'Uses', string $technology = null, InteractionStyle $interactionStyle = null) : Relationship
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
                'environment' => $this->environment,
            ],
            parent::toArray()
        );

        if ($this->parent !== null) {
            $data['parent'] = $this->parent->id();
        }

        return $data;
    }
}
