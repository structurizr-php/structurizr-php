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

use StructurizrPHP\Core\Model\Container;
use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\Person;
use StructurizrPHP\Core\Model\SoftwareSystem;

final class ContainerView extends StaticView
{
    public function __construct(
        ?SoftwareSystem $softwareSystem,
        string $key,
        string $description,
        ViewSet $viewSet
    ) {
        parent::__construct($softwareSystem, $key, $description, $viewSet);
    }

    public static function hydrate(array $viewData, ViewSet $viewSet) : self
    {
        $view = new self(
            $viewSet->getModel()->getSoftwareSystem(
                $viewData['softwareSystemId']
            ),
            $viewData['key'],
            $viewData['description'] ?? '',
            $viewSet
        );

        parent::hydrateView($view, $viewData);

        return $view;
    }

    public function addAllElements() : void
    {
        $this->addAllSoftwareSystems();
        $this->addAllPeople();
        $this->addAllContainers();
    }

    public function addAllContainers() : void
    {
        foreach ($this->softwareSystem->getContainers() as $container) {
            $this->addElement($container, true);
        }
    }

    public function addContainer(Container $container, bool $addRelationships = true) : void
    {
        $this->addElement($container, $addRelationships);
    }

    public function addAllNearestNeighbours() : void
    {
        foreach ($this->softwareSystem->getContainers() as $container) {
            $this->addNearestTypeNeighbours($container, Person::class);
            $this->addNearestTypeNeighbours($container, SoftwareSystem::class);
            $this->addNearestTypeNeighbours($container, Container::class);
        }
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'softwareSystemId' => $this->softwareSystem->id(),
            ],
            parent::toArray()
        );
    }

    protected function canBeRemoved(Element $element) : bool
    {
        return true;
    }
}
