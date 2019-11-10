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

namespace StructurizrPHP\StructurizrPHP\Core\View;

use StructurizrPHP\StructurizrPHP\Core\Model\SoftwareSystem;

final class SystemContextView extends StaticView
{
    /**
     * @var bool
     */
    private $enterpriseBoundaryVisible = true;

    public function __construct(SoftwareSystem $softwareSystem, string $description, string $key, ViewSet $viewSet)
    {
        parent::__construct($softwareSystem, $description, $key, $viewSet);
    }

    public function addAllElements() : void
    {
        $this->addAllSoftwareSystems();
        $this->addAllPeople();
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'enterpriseBoundaryVisible' => $this->enterpriseBoundaryVisible,
            ],
            parent::toArray()
        );
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
}
