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

use StructurizrPHP\Core\Model\Element;
use StructurizrPHP\Core\Model\Model;

final class SystemLandscapeView extends StaticView
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var bool
     */
    private $enterpriseBoundaryVisible = true;

    public function __construct(Model $model, string $description, string $key, ViewSet $viewSet)
    {
        parent::__construct(null, $description, $key, $viewSet);
        $this->model = $model;
    }

    public static function hydrate(array $viewData, ViewSet $viewSet) : self
    {
        $view = new self(
            $viewSet->getModel(),
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

    public function toArray() : array
    {
        return \array_merge(
            [
                'enterpriseBoundaryVisible' => $this->enterpriseBoundaryVisible,
            ],
            parent::toArray()
        );
    }

    protected function getModel() : ?Model
    {
        return $this->model;
    }

    protected function canBeRemoved(Element $element) : bool
    {
        return true;
    }
}
