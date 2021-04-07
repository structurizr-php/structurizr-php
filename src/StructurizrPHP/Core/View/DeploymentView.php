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

namespace StructurizrPHP\Core\View;

use StructurizrPHP\Core\Model\DeploymentNode;
use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\Model;
use StructurizrPHP\Core\Model\Relationship;
use StructurizrPHP\Core\Model\SoftwareSystem;

final class DeploymentView extends View
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var null|string
     */
    private $environment;

    public function __construct(
        SoftwareSystem $softwareSystem,
        string $key,
        string $description,
        ViewSet $viewSet
    ) {
        parent::__construct($softwareSystem, $key, $description, $viewSet);

        $this->model = $softwareSystem->getModel();
    }

    public static function hydrate(array $viewData, ViewSet $viewSet) : self
    {
        $view = new self(
            $viewSet->getModel()->getSoftwareSystem(
                $viewData['softwareSystemId']
            ),
            $viewData['key'],
            $viewData['description'],
            $viewSet
        );

        if (isset($viewData['environment'])) {
            $view->environment = $viewData['environment'];
        }

        parent::hydrateView($view, $viewData);

        return $view;
    }

    public function setEnvironment(?string $environment) : void
    {
        $this->environment = $environment;
    }

    public function addAllDeploymentNodes() : void
    {
        foreach ($this->getModel()->getDeploymentNodes() as $deploymentNode) {
            $this->add($deploymentNode);
        }
    }

    public function add(DeploymentNode $deploymentNode, bool $addRelationships = true) : void
    {
        if ($this->addContainerInstancesAndDeploymentNodesAndInfrastructureNodes($deploymentNode, $addRelationships)) {
            $parent = $deploymentNode->getParent();

            while ($parent !== null) {
                $this->addElement($parent, $addRelationships);
                $parent = $parent->getParent();
            }
        }
    }

    public function addRelationship(Relationship $relationship) : ?RelationshipView
    {
        return parent::addRelationship($relationship);
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'environment' => $this->environment,
                'softwareSystemId' => $this->softwareSystem->id(),
            ],
            parent::toArray()
        );
    }

    protected function canBeRemoved(Element $element) : bool
    {
        return true;
    }

    private function addContainerInstancesAndDeploymentNodesAndInfrastructureNodes(DeploymentNode $deploymentNode, bool $addRelationships) : bool
    {
        $hasContainersOrInfrastructureNodes = false;

        foreach ($deploymentNode->getContainerInstances() as $containerInstance) {
            $container = $containerInstance->getContainer();

            if ($this->softwareSystem === null || $container->getParent()->equals($this->softwareSystem)) {
                $this->addElement($containerInstance, $addRelationships);
                $hasContainersOrInfrastructureNodes = true;
            }
        }

        foreach ($deploymentNode->getInfrastructureNodes() as $infrastructureNode) {
            $this->addElement($infrastructureNode, $addRelationships);
            $hasContainersOrInfrastructureNodes = true;
        }

        foreach ($deploymentNode->getChildren() as $child) {
            $hasContainersOrInfrastructureNodes = (bool) ($hasContainersOrInfrastructureNodes | $this->addContainerInstancesAndDeploymentNodesAndInfrastructureNodes($child, $addRelationships));
        }

        if ($hasContainersOrInfrastructureNodes) {
            $this->addElement($deploymentNode, $addRelationships);
        }

        return $hasContainersOrInfrastructureNodes;
    }
}
