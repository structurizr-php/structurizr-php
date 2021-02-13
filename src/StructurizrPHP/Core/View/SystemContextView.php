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

use StructurizrPHP\Core\Exception\InvalidArgumentException;
use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\Person;
use StructurizrPHP\Core\Model\SoftwareSystem;

final class SystemContextView extends StaticView
{
    /**
     * @var bool
     */
    private $enterpriseBoundaryVisible;

    public function __construct(SoftwareSystem $softwareSystem, string $description, string $key, ViewSet $viewSet)
    {
        parent::__construct($softwareSystem, $description, $key, $viewSet);

        $this->enterpriseBoundaryVisible = true;
    }

    public static function hydrate(array $viewData, ViewSet $viewSet) : self
    {
        $view = new self(
            $viewSet->getModel()->getSoftwareSystem($viewData['softwareSystemId']),
            $viewData['description'],
            $viewData['key'],
            $viewSet
        );

        $view->enterpriseBoundaryVisible = $viewData['enterpriseBoundaryVisible'];

        parent::hydrateView($view, $viewData);

        return $view;
    }

    public function addAllElements() : void
    {
        $this->addAllSoftwareSystems();
        $this->addAllPeople();
    }

    public function addNearestNeighbours(Element $element) : void
    {
        if ($element instanceof Person || $element instanceof SoftwareSystem) {
            parent::addNearestTypeNeighbours($element, Person::class);
            parent::addNearestTypeNeighbours($element, SoftwareSystem::class);
        } else {
            throw new InvalidArgumentException('A person or software system must be specified.');
        }
    }

    public function setEnterpriseBoundaryVisible(bool $enterpriseBoundaryVisible) : void
    {
        $this->enterpriseBoundaryVisible = $enterpriseBoundaryVisible;
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'enterpriseBoundaryVisible' => $this->enterpriseBoundaryVisible,
                'softwareSystemId' => $this->softwareSystem->id(),
            ],
            parent::toArray()
        );
    }

    protected function canBeRemoved(Element $element) : bool
    {
        return !$this->softwareSystem->equals($element);
    }
}
