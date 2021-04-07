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

use StructurizrPHP\Core\Model\Component;
use StructurizrPHP\Core\Model\Container;
use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\Person;
use StructurizrPHP\Core\Model\SoftwareSystem;

final class ComponentView extends StaticView
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var bool
     */
    private $externalContainerBoundariesVisible = true;

    public function __construct(Container $container, string $key, string $description, ViewSet $viewSet)
    {
        parent::__construct($container->getSoftwareSystem(), $description, $key, $viewSet);

        $this->container = $container;
    }

    public static function hydrate(array $viewData, ViewSet $viewSet) : self
    {
        $view = new self(
            $viewSet->getModel()->getContainer($viewData['containerId']),
            $viewData['description'],
            $viewData['key'],
            $viewSet
        );

        $view->externalContainerBoundariesVisible = $viewData['externalContainersBoundariesVisible'];

        parent::hydrateView($view, $viewData);

        return $view;
    }

    public function getContainer() : Container
    {
        return $this->container;
    }

    public function addAllElements() : void
    {
        $this->addAllSoftwareSystems();
        $this->addAllPeople();
        $this->addAllContainers();
        $this->addAllComponents();
    }

    public function addAllComponents() : void
    {
        foreach ($this->container->getComponents() as $component) {
            $this->addComponent($component);
        }
    }

    public function addAllContainers() : void
    {
        foreach ($this->softwareSystem->getContainers() as $container) {
            $this->addContainer($container);
        }
    }

    public function addContainer(Container $container, bool $addRelationships = true) : void
    {
        if (!$this->container->equals($container)) {
            $this->addElement($container, $addRelationships);
        }
    }

    public function addComponent(Component $component, bool $addRelationships = true) : void
    {
        $this->addElement($component, $addRelationships);
    }

    public function addAllNearestNeighbours() : void
    {
        foreach ($this->container->getComponents() as $component) {
            $this->addNearestTypeNeighbours($component, Person::class);
            $this->addNearestTypeNeighbours($component, SoftwareSystem::class);
            $this->addNearestTypeNeighbours($component, Container::class);
            $this->addNearestTypeNeighbours($component, Component::class);
        }
    }

    public function getContainerId() : string
    {
        return $this->container->id();
    }

    public function setExternalContainerBoundariesVisible(
        bool $externalContainerBoundariesVisible
    ) : void {
        $this->externalContainerBoundariesVisible = $externalContainerBoundariesVisible;
    }

    public function toArray() : array
    {
        return \array_merge(
            parent::toArray(),
            [
                'containerId' => $this->container->id(),
                'externalContainerBoundariesVisible' => $this->externalContainerBoundariesVisible,
            ]
        );
    }

    protected function canBeRemoved(Element $element) : bool
    {
        return true;
    }
}
